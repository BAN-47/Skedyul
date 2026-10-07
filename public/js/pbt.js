// SELECTED_DATE / SELECTED_TEACHER are set by a small inline <script> in
// pbt.blade.php — Blade's @json() can't run inside a plain .js file, so
// these are bridged in via window instead.
const SELECTED_DATE    = window.PBT_SELECTED_DATE;
const SELECTED_TEACHER = window.PBT_SELECTED_TEACHER;

/* ── modal helpers (same pattern as PBS / other chair pages) ── */
function openModal(id)  { document.getElementById(id).classList.add('active'); }
function closeModal(id) { document.getElementById(id).classList.remove('active'); }

document.querySelectorAll('.modal-overlay').forEach(overlay => {
  overlay.addEventListener('click', e => {
    if (e.target === overlay) overlay.classList.remove('active');
  });
});

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]').content;
}

let toastTimeout;
function showToast(msg) {
  const t = document.getElementById('toast');
  const m = document.getElementById('toast-msg');
  if (!t || !m) return;
  m.textContent = msg;
  t.classList.add('show');
  clearTimeout(toastTimeout);
  toastTimeout = setTimeout(() => t.classList.remove('show'), 9200);
}

function showInlineError(boxId, msg) {
  const box = document.getElementById(boxId);
  box.textContent = msg;
  box.classList.remove('hidden');
}
function clearInlineError(boxId) {
  document.getElementById(boxId).classList.add('hidden');
}

function setButtonLoading(button, loading, label = 'Saving...') {
  if (!button) return;
  if (!button.dataset.idleHtml) button.dataset.idleHtml = button.innerHTML;
  button.disabled = loading;
  button.setAttribute('aria-busy', String(loading));
  button.innerHTML = loading
    ? `<span class="inline-flex items-center gap-2"><span aria-hidden="true" class="h-3.5 w-3.5 animate-spin rounded-full border-2 border-current border-t-transparent"></span>${label}</span>`
    : button.dataset.idleHtml;
}

function setAddButtonsLoading(loading, keepOpen = false) {
  const confirm = document.getElementById('add-submit');
  const another = document.getElementById('add-another-submit');
  if (!loading) {
    setButtonLoading(confirm, false);
    setButtonLoading(another, false);
    return;
  }
  setButtonLoading(confirm, !keepOpen, 'Saving schedule...');
  setButtonLoading(another, keepOpen, 'Saving & preparing next...');
  (keepOpen ? confirm : another).disabled = true;
}

/*
  Same contract as pbs.js: { success, conflict, message }.
  conflict === true -> double-booking, nothing saved, shown as the
  bottom-right toast (modal stays open). Anything else stays inline.
*/
function handleScheduleError(data, inlineBoxId) {
  if (data.conflict) {
    showToast(data.message || 'Conflict: that slot is already taken.');
  } else {
    showInlineError(inlineBoxId, data.message || 'Something went wrong.');
  }
}

/* ── clock ── */
function tickClock() {
  const el = document.getElementById('ph-clock');
  if (!el) return;
  const now = new Date().toLocaleTimeString('en-US', {
    timeZone: 'Asia/Manila', hour: '2-digit', minute: '2-digit', hour12: true,
  });
  el.textContent = `${now} (Philippine Time)`;
}
tickClock();
setInterval(tickClock, 30000);

/* ── TEACHER picker popover ── */
function toggleTeacherPicker() {
  const panel = document.getElementById('teacher-picker-panel');
  const button = document.getElementById('teacher-picker-btn');
  panel.classList.toggle('hidden');
  button.setAttribute('aria-expanded', String(!panel.classList.contains('hidden')));
  if (!panel.classList.contains('hidden')) {
    const search = document.getElementById('teacher-search');
    search.value = '';
    filterTeacherList('');
    setTimeout(() => search.focus(), 30);
  }
}
document.addEventListener('click', e => {
  const panel = document.getElementById('teacher-picker-panel');
  const btn   = document.getElementById('teacher-picker-btn');
  if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
    panel.classList.add('hidden');
    btn.setAttribute('aria-expanded', 'false');
  }
});

function filterTeacherList(query) {
  const q = query.trim().toLowerCase();
  document.querySelectorAll('.teacher-option').forEach(opt => {
    opt.style.display = (opt.dataset.name.includes(q) || opt.dataset.role.toLowerCase().includes(q)) ? '' : 'none';
  });
  document.querySelectorAll('.teacher-group').forEach(group => {
    const visible = Array.from(group.querySelectorAll('.teacher-option'))
      .some(opt => opt.style.display !== 'none');
    group.style.display = visible ? '' : 'none';
  });
  const noResults = document.getElementById('teacher-no-results');
  if (noResults) noResults.classList.toggle('hidden', Array.from(document.querySelectorAll('.teacher-option')).some(opt => opt.style.display !== 'none'));
}

function selectTeacher(facultyId) {
  const params = new URLSearchParams(window.location.search);
  if (facultyId) {
    params.set('faculty', facultyId);
  } else {
    params.delete('faculty');
  }
  window.location.search = params.toString();
}

/* ── filters / date nav ── */
function applyFilters() {
  const params = new URLSearchParams(window.location.search);
  const map = { program: 'filter-program', semester: 'filter-semester' };
  Object.entries(map).forEach(([key, id]) => {
    const val = document.getElementById(id).value;
    val ? params.set(key, val) : params.delete(key);
  });
  window.location.search = params.toString();
}

function setShift(shift) {
  const params = new URLSearchParams(window.location.search);
  params.set('shift', shift);
  window.location.search = params.toString();
}

function paintShift() {
  const shift = new URLSearchParams(window.location.search).get('shift') || 'day';
  const on  = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-blue-600 text-white';
  const off = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-white text-slate-700';
  document.getElementById('shift-day').className   = (shift === 'day' ? on : off);
  document.getElementById('shift-night').className = (shift === 'night' ? on : off) + ' border-l border-slate-300';
}
paintShift();

function shiftDate(delta) {
  const d = new Date(SELECTED_DATE + 'T00:00:00');
  d.setDate(d.getDate() + delta);
  const params = new URLSearchParams(window.location.search);
  params.set('date', d.toISOString().slice(0, 10));
  window.location.search = params.toString();
}

/* ── ADD (opened by clicking an empty grid cell; teacher is implicit) ── */
function addMinutesToTime(hhmm, minutesToAdd) {
  const [h, m] = hhmm.split(':').map(Number);
  const total = (h * 60 + m + minutesToAdd) % (24 * 60);
  const hh = String(Math.floor(total / 60)).padStart(2, '0');
  const mm = String(total % 60).padStart(2, '0');
  return `${hh}:${mm}`;
}

/**
 * Filter subject dropdown options by the year level of the selected section.
 * e.g. Section "1-A" (year_level=1) → only show 1st-year subjects.
 */
function filterSubjectsBySection(sectionSelectId, subjectSelectId) {
  const secSel = document.getElementById(sectionSelectId);
  const subSel = document.getElementById(subjectSelectId);
  if (!secSel || !subSel) return;

  const selectedOpt = secSel.options[secSel.selectedIndex];
  const yearLevel = selectedOpt ? (selectedOpt.getAttribute('data-year-level') || '') : '';

  Array.from(subSel.options).forEach(opt => {
    if (!opt.value) {
      opt.hidden = false;
      opt.style.display = '';
      return;
    }
    const subYear = opt.getAttribute('data-year-level') || '';
    const match = !yearLevel || !subYear || String(subYear) === String(yearLevel);
    opt.hidden = !match;
    opt.style.display = match ? '' : 'none';
  });

  // Reset subject if current selection is no longer visible
  const current = subSel.options[subSel.selectedIndex];
  if (current && current.hidden) {
    subSel.value = '';
  }
}

// Wire up section → subject filter for Add + Edit modals
document.addEventListener('DOMContentLoaded', () => {
  const addSec = document.getElementById('add-section');
  if (addSec) {
    addSec.addEventListener('change', () => filterSubjectsBySection('add-section', 'add-subject'));
  }
  const editSec = document.getElementById('edit-section');
  if (editSec) {
    editSec.addEventListener('change', () => filterSubjectsBySection('edit-section', 'edit-subject'));
  }
});

function openAddModal(day, startTime) {
  if (!SELECTED_TEACHER) {
    showToast('Select a teacher first.');
    return;
  }
  clearInlineError('add-error');
  ['add-subject','add-section','add-room','add-description'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.value = '';
  });

  // Reset subject filter (show all until a section is chosen)
  filterSubjectsBySection('add-section', 'add-subject');

  document.querySelectorAll('.add-day-checkbox').forEach(input => {
    input.checked = input.value === (day || '');
  });
  document.getElementById('add-start').value = startTime || '';
  document.getElementById('add-end').value   = startTime ? addMinutesToTime(startTime, 60) : '';

  setAddButtonsLoading(false);
  openModal('modal-add-schedule');
}

function submitAdd(keepOpen = false) {
  clearInlineError('add-error');
  const payload = {
    subj_id:     document.getElementById('add-subject').value,
    fac_id:      SELECTED_TEACHER,
    sem_id:      document.getElementById('add-semester').value,
    prog_id:     document.getElementById('add-program').value,
    sec_id:      document.getElementById('add-section').value,
    room_id:     document.getElementById('add-room').value,
    days:        [...document.querySelectorAll('.add-day-checkbox:checked')].map(input => input.value),
    start_time:  document.getElementById('add-start').value,
    end_time:    document.getElementById('add-end').value,
    description: document.getElementById('add-description').value,
  };

  if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id || !payload.days.length || !payload.start_time || !payload.end_time) {
    showInlineError('add-error', 'Please fill in subject, section, room, at least one day, and both times.');
    return;
  }
  if (payload.end_time <= payload.start_time) {
    showInlineError('add-error', 'End time must be later than start time.');
    return;
  }

  setAddButtonsLoading(true, keepOpen);

  fetch('/chair/pbt', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    if (data.success) {
      showToast(data.message || 'Schedule added.');
      if (keepOpen) {
        document.querySelectorAll('.add-day-checkbox').forEach(input => { input.checked = false; });
        document.getElementById('add-start').value = '';
        document.getElementById('add-end').value = '';
        document.getElementById('add-description').value = '';
        setAddButtonsLoading(false);
      } else {
        closeModal('modal-add-schedule');
        setTimeout(() => location.reload(), 800);
      }
    } else {
      handleScheduleError(data, 'add-error');
      setAddButtonsLoading(false);
    }
  })
  .catch(() => {
    showInlineError('add-error', 'Something went wrong. Please try again.');
    setAddButtonsLoading(false);
  });
}

/* ── EDIT ── */
function openEditModal(block) {
  clearInlineError('edit-error');
  document.getElementById('edit-id').value      = block.dataset.scheduleId;
  document.getElementById('edit-section').value = block.dataset.section || '';
  // Filter subjects by the section's year level first, then set subject
  filterSubjectsBySection('edit-section', 'edit-subject');
  document.getElementById('edit-subject').value = block.dataset.subject || '';
  document.getElementById('edit-room').value    = block.dataset.room    || '';
  document.getElementById('edit-day').value     = block.dataset.day     || '';
  document.getElementById('edit-start').value   = block.dataset.start   || '';
  document.getElementById('edit-end').value     = block.dataset.end     || '';
  setButtonLoading(document.getElementById('edit-submit'), false);
  openModal('modal-edit-schedule');
}

function submitEdit() {
  clearInlineError('edit-error');
  const id = document.getElementById('edit-id').value;
  const payload = {
    subj_id:     document.getElementById('edit-subject').value,
    fac_id:      SELECTED_TEACHER,
    sem_id:      document.getElementById('edit-semester').value,
    prog_id:     document.getElementById('edit-program').value,
    sec_id:      document.getElementById('edit-section').value,
    room_id:     document.getElementById('edit-room').value,
    day:         document.getElementById('edit-day').value,
    start_time:  document.getElementById('edit-start').value,
    end_time:    document.getElementById('edit-end').value,
    description: document.getElementById('edit-description').value,
    _method:     'PUT',
  };

  if (payload.end_time <= payload.start_time) {
    showInlineError('edit-error', 'End time must be later than start time.');
    return;
  }

  const btn = document.getElementById('edit-submit');
  setButtonLoading(btn, true, 'Saving changes...');

  fetch(`/chair/pbt/${id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    if (data.success) {
      closeModal('modal-edit-schedule');
      showToast(data.message || 'Schedule updated.');
      setTimeout(() => location.reload(), 800);
    } else {
      handleScheduleError(data, 'edit-error');
      setButtonLoading(btn, false);
    }
  })
  .catch(() => {
    showInlineError('edit-error', 'Something went wrong. Please try again.');
    setButtonLoading(btn, false);
  });
}

/* ── DELETE (type-to-confirm) ── */
let deleteCode = '';

function openDeleteModal(block) {
  clearInlineError('delete-error');
  const id = block.dataset.scheduleId;
  document.getElementById('delete-id').value = id;

  deleteCode = String(id).replace(/-/g, '').slice(0, 8).toUpperCase();
  document.getElementById('delete-code').textContent = deleteCode;

  const input = document.getElementById('delete-confirm');
  input.value = '';
  validateDeleteCode();
  openModal('modal-delete-schedule');
  setTimeout(() => input.focus(), 50);
}

function validateDeleteCode() {
  const typed = document.getElementById('delete-confirm').value.trim().toUpperCase();
  const btn = document.getElementById('delete-submit');
  const ok = deleteCode !== '' && typed === deleteCode;
  btn.disabled = !ok;
  btn.classList.toggle('opacity-50', !ok);
  btn.classList.toggle('cursor-not-allowed', !ok);
}

function submitDelete() {
  clearInlineError('delete-error');
  const id = document.getElementById('delete-id').value;
  document.getElementById('delete-submit').disabled = true;

  fetch(`/chair/pbt/${id}`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify({ _method: 'DELETE' }),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    if (data.success) {
      closeModal('modal-delete-schedule');
      showToast(data.message || 'Schedule deleted.');
      setTimeout(() => location.reload(), 800);
    } else {
      showInlineError('delete-error', data.message || 'Could not delete this schedule.');
      validateDeleteCode();
    }
  })
  .catch(() => {
    showInlineError('delete-error', 'Something went wrong. Please try again.');
    validateDeleteCode();
  });
}

/* ── toolbar ── */
function saveDraft() {
  fetch('/chair/pbt/save-draft', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    showToast(data.message || 'Draft saved.');
  })
  .catch(() => showToast('Could not save the draft.'));
}

function clearAll() {
  if (!confirm('Clear all plotted schedules for this teacher? This cannot be undone.')) return;

  fetch('/chair/pbt/clear', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify(Object.fromEntries(new URLSearchParams(window.location.search))),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    if (data.success) {
      showToast(data.message || 'Cleared.');
      setTimeout(() => location.reload(), 800);
    } else {
      showToast(data.message || 'Could not clear schedules.');
    }
  })
  .catch(() => showToast('Something went wrong.'));
}
