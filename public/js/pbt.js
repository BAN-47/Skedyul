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

function showToast(msg) {
  const t = document.getElementById('toast');
  const m = document.getElementById('toast-msg');
  if (!t || !m) return;
  m.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

function showInlineError(boxId, msg) {
  const box = document.getElementById(boxId);
  box.textContent = msg;
  box.classList.remove('hidden');
}
function clearInlineError(boxId) {
  document.getElementById(boxId).classList.add('hidden');
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
  panel.classList.toggle('hidden');
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
  }
});

function filterTeacherList(query) {
  const q = query.trim().toLowerCase();
  document.querySelectorAll('.teacher-option').forEach(opt => {
    opt.style.display = opt.dataset.name.includes(q) ? '' : 'none';
  });
  // Hide a group header entirely if every option inside it is filtered out
  document.querySelectorAll('.teacher-group').forEach(group => {
    const visible = Array.from(group.querySelectorAll('.teacher-option'))
      .some(opt => opt.style.display !== 'none');
    group.style.display = visible ? '' : 'none';
  });
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

function openAddModal(day, startTime) {
  if (!SELECTED_TEACHER) {
    showToast('Select a teacher first.');
    return;
  }
  clearInlineError('add-error');
  ['add-subject','add-semester','add-program','add-section','add-room',
   'add-description'].forEach(id => document.getElementById(id).value = '');

  document.getElementById('add-day').value   = day || '';
  document.getElementById('add-start').value = startTime || '';
  document.getElementById('add-end').value   = startTime ? addMinutesToTime(startTime, 60) : '';

  document.getElementById('add-submit').disabled = false;
  openModal('modal-add-schedule');
}

function submitAdd() {
  clearInlineError('add-error');
  const payload = {
    subj_id:     document.getElementById('add-subject').value,
    fac_id:      SELECTED_TEACHER,
    sem_id:      document.getElementById('add-semester').value,
    prog_id:     document.getElementById('add-program').value,
    sec_id:      document.getElementById('add-section').value,
    room_id:     document.getElementById('add-room').value,
    day:         document.getElementById('add-day').value,
    start_time:  document.getElementById('add-start').value,
    end_time:    document.getElementById('add-end').value,
    description: document.getElementById('add-description').value,
  };

  if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.day || !payload.start_time || !payload.end_time) {
    showInlineError('add-error', 'Please fill in subject, section, day, and both times.');
    return;
  }
  if (payload.end_time <= payload.start_time) {
    showInlineError('add-error', 'End time must be later than start time.');
    return;
  }

  const btn = document.getElementById('add-submit');
  btn.disabled = true;

  fetch('/chair/pbt', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({}));
    if (data.success) {
      closeModal('modal-add-schedule');
      showToast(data.message || 'Schedule added.');
      setTimeout(() => location.reload(), 800);
    } else {
      // Conflict stays in the modal so nothing is lost and nothing is inserted
      showInlineError('add-error', data.message || 'Could not save this schedule.');
      btn.disabled = false;
    }
  })
  .catch(() => {
    showInlineError('add-error', 'Something went wrong. Please try again.');
    btn.disabled = false;
  });
}

/* ── EDIT ── */
function openEditModal(block) {
  clearInlineError('edit-error');
  document.getElementById('edit-id').value      = block.dataset.scheduleId;
  document.getElementById('edit-subject').value = block.dataset.subject || '';
  document.getElementById('edit-section').value = block.dataset.section || '';
  document.getElementById('edit-room').value    = block.dataset.room    || '';
  document.getElementById('edit-day').value     = block.dataset.day     || '';
  document.getElementById('edit-start').value   = block.dataset.start   || '';
  document.getElementById('edit-end').value     = block.dataset.end     || '';
  document.getElementById('edit-submit').disabled = false;
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
  btn.disabled = true;

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
      showInlineError('edit-error', data.message || 'Could not update this schedule.');
      btn.disabled = false;
    }
  })
  .catch(() => {
    showInlineError('edit-error', 'Something went wrong. Please try again.');
    btn.disabled = false;
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