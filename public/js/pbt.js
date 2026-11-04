// SELECTED_DATE / SELECTED_TEACHER are set by a small inline <script> in
// pbt.blade.php — Blade's @json() can't run inside a plain .js file, so
// these are bridged in via window instead.
const SELECTED_DATE    = window.PBT_SELECTED_DATE;
const SELECTED_TEACHER = window.PBT_SELECTED_TEACHER;

function filterSubjectsByCategory() {
  const catSel = document.getElementById('add-category');
  const subSel = document.getElementById('add-subject');
  const secSel = document.getElementById('add-section');
  const deptSel = document.getElementById('add-department');
  if (!catSel || !subSel) return;

  const selectedCat = catSel.value;
  const deptId = deptSel ? deptSel.value : '';
  const selectedOpt = secSel ? secSel.options[secSel.selectedIndex] : null;
  const yearLevel = selectedOpt ? (selectedOpt.getAttribute('data-year-level') || '') : '';
  const activeSemester = String(window.ACTIVE_COURSE_SEMESTER || '');

  let subjectsToLoad = [];
  if (selectedCat) {
    const globalList = window.ALL_SUBJECTS_LIST || [];
    subjectsToLoad = globalList.filter(s => String(s.cat) === String(selectedCat));
  } else {
    Array.from(subSel.options).forEach(opt => {
      if (!opt.value) return;
      const subYear = opt.getAttribute('data-year-level') || '';
      const subSem = opt.getAttribute('data-semester') || '';
      const isRec = opt.getAttribute('data-course-code') === 'REC';
      const show = isRec || !yearLevel || !subYear || String(subYear) === String(yearLevel) || !subSem || String(subSem) === String(activeSemester);
      opt.hidden = !show;
      opt.style.display = show ? '' : 'none';
    });
    if (subSel.options[subSel.selectedIndex]?.hidden) subSel.value = '';
    return;
  }

  subSel.innerHTML = '<option value="">-- Select Subject --</option>';
  subjectsToLoad.forEach(sub => {
    const opt = document.createElement('option');
    opt.value = sub.id;
    opt.textContent = sub.code + ' — ' + sub.name;
    opt.dataset.yearLevel = sub.year || '';
    opt.dataset.semester = sub.sem || '';
    opt.dataset.category = sub.cat || '';
    opt.dataset.courseCode = sub.code || '';
    opt.dataset.lectureHours = sub.lecHours || 0;
    opt.dataset.labHours = sub.labHours || 0;
    
    if (!selectedCat) {
      if (!yearLevel) {
        // REC is always visible globally, other courses need section/year
        if (sub.code !== 'REC') {
          opt.hidden = true;
          opt.style.display = 'none';
        }
      } else if (sub.year && String(sub.year) !== String(yearLevel)) {
        opt.hidden = true;
        opt.style.display = 'none';
      }
    }
    subSel.appendChild(opt);
  });
}

function filterEditSubjectsByCategory() {
  const catSel = document.getElementById('edit-category');
  const subSel = document.getElementById('edit-subject');
  const secSel = document.getElementById('edit-section');
  const deptSel = document.getElementById('edit-department');
  if (!catSel || !subSel) return;

  const selectedCat = catSel.value;
  const deptId = deptSel ? deptSel.value : '';
  const selectedOpt = secSel ? secSel.options[secSel.selectedIndex] : null;
  const yearLevel = selectedOpt ? (selectedOpt.getAttribute('data-year-level') || '') : '';
  const activeSemester = String(window.ACTIVE_COURSE_SEMESTER || '');

  if (!selectedCat) {
    Array.from(subSel.options).forEach(opt => {
      if (!opt.value) return;
      const subYear = opt.getAttribute('data-year-level') || '';
      const subSem = opt.getAttribute('data-semester') || '';
      const isRec = opt.getAttribute('data-course-code') === 'REC';
      const yearMatch = isRec || !yearLevel || !subYear || String(subYear) === String(yearLevel);
      const semMatch = isRec || !activeSemester || !subSem || String(subSem) === String(activeSemester);
      const show = yearMatch && semMatch;
      opt.hidden = !show;
      opt.style.display = show ? '' : 'none';
    });
    if (subSel.options[subSel.selectedIndex]?.hidden) subSel.value = '';
    return;
  }

  // Filter by category across all departments
  const filtered = window.ALL_SUBJECTS_LIST || [];
  subSel.innerHTML = '<option value="">-- Select Subject --</option>';
  filtered.filter(s => String(s.cat) === String(selectedCat)).forEach(sub => {
    const opt = document.createElement('option');
    opt.value = sub.id;
    opt.textContent = sub.code + ' — ' + sub.name;
    opt.dataset.yearLevel = sub.year || '';
    opt.dataset.semester = sub.sem || '';
    opt.dataset.category = sub.cat || '';
    opt.dataset.lectureHours = sub.lecHours || 0;
    opt.dataset.labHours = sub.labHours || 0;
    
    const yearMatch = !yearLevel || (sub.year && String(sub.year) === String(yearLevel));
    const semMatch = !activeSemester || !sub.sem || String(sub.sem) === activeSemester;
    const show = yearMatch && semMatch;
    if (show) subSel.appendChild(opt);
  });
  if (subSel.options[subSel.selectedIndex]?.hidden) subSel.value = '';
}

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
  if (!box) return;
  const msgEl = box.querySelector('[id$="-msg"]') || box;
  msgEl.textContent = msg;
  box.classList.remove('hidden');
}
function clearInlineError(boxId) {
  const box = document.getElementById(boxId);
  if (box) box.classList.add('hidden');
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
    const message = data.message || 'Conflict: that slot is already taken.';
    showToast(message);
    showInlineError(inlineBoxId, message);
    return;
  }
  const firstValidationError = data.errors ? Object.values(data.errors).flat()[0] : null;
  const message = data.message || firstValidationError || 'Something went wrong.';
  showToast(message);
  showInlineError(inlineBoxId, message);
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

function getShiftValue() {
  return new URLSearchParams(window.location.search).get('shift') === 'night' ? 'evening' : 'day';
}

// Shift grid ranges in minutes since midnight.
// Day = 7:00 AM–4:00 PM, Evening = 4:00 PM–9:00 PM.
function getShiftGridRange() {
  return getShiftValue() === 'evening'
    ? { start: 16 * 60, end: 21 * 60, label: 'Evening (4:00 PM–9:00 PM)' }
    : { start: 7 * 60,  end: 16 * 60, label: 'Day (7:00 AM–4:00 PM)' };
}

// Returns an error message string if start/end fall outside the shift grid, otherwise null.
function validateTimeInRange(startTime, endTime) {
  const range = getShiftGridRange();
  const toMinutes = hhmm => {
    const [h, m] = hhmm.split(':').map(Number);
    return h * 60 + m;
  };
  const sMin = toMinutes(startTime);
  const eMin = toMinutes(endTime);
  if (sMin < range.start || eMin > range.end) {
    const fmt = mins => {
      const h = Math.floor(mins / 60);
      const m = mins % 60;
      const ampm = h >= 12 ? 'PM' : 'AM';
      const h12 = h % 12 === 0 ? 12 : h % 12;
      return `${h12}:${String(m).padStart(2, '0')} ${ampm}`;
    };
    return `This time is outside the ${range.label} window (${fmt(range.start)}–${fmt(range.end)}). Switch to the other shift or pick a time within range.`;
  }
  return null;
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
 * Filter subject dropdown options by the year level and semester of the selected section.
 * e.g. Section "1-A" (year_level=1) → only show 1st-year subjects.
 * Also respects active semester filter.
 */
function filterSubjectsBySection(sectionSelectId, subjectSelectId) {
  const secSel = document.getElementById(sectionSelectId);
  const subSel = document.getElementById(subjectSelectId);
  if (!secSel || !subSel) return;

  const selectedOpt = secSel.options[secSel.selectedIndex];
  const yearLevel = selectedOpt ? (selectedOpt.getAttribute('data-year-level') || '') : '';
  const activeSemester = String(window.ACTIVE_COURSE_SEMESTER || '');

  Array.from(subSel.options).forEach(opt => {
    if (!opt.value) {
      opt.hidden = false;
      opt.style.display = '';
      return;
    }
    const subYear = opt.getAttribute('data-year-level') || '';
    const subSem = opt.getAttribute('data-semester') || '';
    const courseCode = opt.getAttribute('data-course-code') || '';
    const isRec = courseCode === 'REC';
    
    if (!yearLevel) {
      // Always show REC globally, hide other courses until section selected
      opt.hidden = !isRec;
      opt.style.display = isRec ? '' : 'none';
    } else {
      const yearMatch = !subYear || String(subYear) === String(yearLevel);
      const semMatch = !opt.getAttribute('data-semester') || opt.getAttribute('data-semester') === activeSemester;
      const match = yearMatch && semMatch;
      opt.hidden = !match;
      opt.style.display = match ? '' : 'none';
    }
  });

  // Reset subject if current selection is no longer visible
  const current = subSel.options[subSel.selectedIndex];
  if (current && current.hidden) {
    subSel.value = '';
  }
}

// Wire up section → subject filter for Add + Edit modals
document.addEventListener('DOMContentLoaded', () => {
  const addDept = document.getElementById('add-department');
  if (addDept) {
    addDept.addEventListener('change', () => {
      const deptId = addDept.value;
      const sectionSelect = document.getElementById('add-section');
      const subjectSelect = document.getElementById('add-subject');
      const categorySelect = document.getElementById('add-category');
      const sections = (window.COLLEGE_SECTIONS && window.COLLEGE_SECTIONS[deptId]) || [];
      const subjects = (window.COLLEGE_SUBJECTS && window.COLLEGE_SUBJECTS[deptId]) || [];
      const allCategories = window.ALL_CATEGORIES || [];
      
      sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
      sections.forEach(sec => {
        const opt = document.createElement('option');
        opt.value = sec.id;
        opt.textContent = sec.name;
        opt.dataset.yearLevel = sec.year;
        sectionSelect.appendChild(opt);
      });
      
      subjectSelect.innerHTML = '<option value="">-- Select Subject --</option>';
      subjects.forEach(sub => {
        const opt = document.createElement('option');
        opt.value = sub.id;
        opt.textContent = sub.code + ' — ' + sub.name;
        opt.dataset.yearLevel = sub.year;
        opt.dataset.semester = sub.sem;
        opt.dataset.category = sub.cat;
        opt.dataset.courseCode = sub.code;
        opt.dataset.lectureHours = sub.lecHours;
        opt.dataset.labHours = sub.labHours;
        subjectSelect.appendChild(opt);
      });
      
      categorySelect.innerHTML = '<option value="">-- All --</option>';
      allCategories.forEach(cat => {
        const opt = document.createElement('option');
        opt.value = cat;
        opt.textContent = cat;
        categorySelect.appendChild(opt);
      });
      
      filterSubjectsBySection('add-section', 'add-subject');
    });
  }

  const addSec = document.getElementById('add-section');
  if (addSec) {
    addSec.addEventListener('change', () => {
      filterSubjectsBySection('add-section', 'add-subject');
    });
  }
  
const addCat = document.getElementById('add-category');
  if (addCat) {
    addCat.addEventListener('change', () => {
      filterSubjectsByCategory();
    });
  }
  
  const editDept = document.getElementById('edit-department');
  if (editDept) {
    editDept.addEventListener('change', () => {
      const deptId = editDept.value;
      const sectionSelect = document.getElementById('edit-section');
      const sections = (window.COLLEGE_SECTIONS && window.COLLEGE_SECTIONS[deptId]) || [];
      
      sectionSelect.innerHTML = '<option value="">-- Select Section --</option>';
      sections.forEach(sec => {
        const opt = document.createElement('option');
        opt.value = sec.id;
        opt.textContent = sec.name;
        opt.dataset.yearLevel = sec.year;
        sectionSelect.appendChild(opt);
      });
      filterSubjectsBySection('edit-section', 'edit-subject');
    });
  }
  
  const editCat = document.getElementById('edit-category');
  if (editCat) {
    editCat.addEventListener('change', () => {
      filterEditSubjectsByCategory();
    });
  }
  
  const editSec = document.getElementById('edit-section');
  if (editSec) {
    editSec.addEventListener('change', () => {
      filterSubjectsBySection('edit-section', 'edit-subject');
    });
  }
  
  // Trigger initial filter based on default department
  if (addDept) {
    addDept.dispatchEvent(new Event('change'));
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
  const subjId = document.getElementById('add-subject').value;
  const secId = document.getElementById('add-section').value;
  const roomId = document.getElementById('add-room').value;
  
  const subjSel = document.getElementById('add-subject');
  const selectedOpt = subjSel.options[subjSel.selectedIndex];
  const isRec = selectedOpt ? (selectedOpt.getAttribute('data-course-code') === 'REC') : false;

  const payload = {
    subj_id:     subjId,
    fac_id:      SELECTED_TEACHER,
    sem_id:      document.getElementById('add-semester').value,
    prog_id:     document.getElementById('add-department')?.value || document.getElementById('add-program')?.value || '',
    sec_id:      secId || null,
    room_id:     roomId || null,
    days:        [...document.querySelectorAll('.add-day-checkbox:checked')].map(input => input.value),
    start_time:  document.getElementById('add-start').value,
    end_time:    document.getElementById('add-end').value,
    shift:       getShiftValue(),
    description: document.getElementById('add-description').value,
  };

  if (!payload.subj_id || !payload.fac_id || !payload.days.length || !payload.start_time || !payload.end_time) {
    const m = 'Please select the course, at least one day, and both times.';
    showToast(m);
    showInlineError('add-error', m);
    return;
  }
  if (!isRec && (!payload.sec_id || !payload.room_id)) {
    const m = 'Please select section and room (not required for REC).';
    showToast(m);
    showInlineError('add-error', m);
    return;
  }
  if (payload.end_time <= payload.start_time) {
    const m = 'End time must be later than start time.';
    showToast(m);
    showInlineError('add-error', m);
    return;
  }
  const rangeError = validateTimeInRange(payload.start_time, payload.end_time);
  if (rangeError) {
    showToast(rangeError);
    showInlineError('add-error', rangeError);
    return;
  }

  setAddButtonsLoading(true, keepOpen);

  fetch('/chair/pbt', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    body: JSON.stringify(payload),
  })
  .then(async res => {
    const data = await res.json().catch(() => ({ message: `The server returned an unreadable response (${res.status}).` }));
    if (!res.ok && !data.message && !data.errors) data.message = `Schedule could not be saved (HTTP ${res.status}).`;
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
    showToast('Something went wrong. Please try again.');
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
    prog_id:     document.getElementById('edit-program')?.value || document.getElementById('edit-department')?.value || '',
    sec_id:      document.getElementById('edit-section').value,
    room_id:     document.getElementById('edit-room').value,
    day:         document.getElementById('edit-day').value,
    start_time:  document.getElementById('edit-start').value,
    end_time:    document.getElementById('edit-end').value,
    shift:       getShiftValue(),
    description: document.getElementById('edit-description').value,
    _method:     'PUT',
  };

  if (payload.end_time <= payload.start_time) {
    showInlineError('edit-error', 'End time must be later than start time.');
    return;
  }
  const editRangeError = validateTimeInRange(payload.start_time, payload.end_time);
  if (editRangeError) {
    showToast(editRangeError);
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
let scheduleSummaryCloseTimer;

function toggleScheduleSummary() {
    const modal = document.getElementById('schedule-summary-modal');
    if (!modal) return;
    modal.classList.contains('hidden') ? openScheduleSummary() : closeScheduleSummary();
}

function openScheduleSummary() {
    const modal = document.getElementById('schedule-summary-modal');
    const panel = document.getElementById('schedule-summary-panel');
    const toggle = document.getElementById('schedule-summary-toggle');
    if (!modal || !panel) return;
    clearTimeout(scheduleSummaryCloseTimer);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    requestAnimationFrame(() => {
        modal.classList.remove('opacity-0');
        modal.classList.add('opacity-100');
        panel.classList.remove('scale-95');
        panel.classList.add('scale-100');
    });
    toggle?.setAttribute('aria-expanded', 'true');
}

function closeScheduleSummary() {
    const modal = document.getElementById('schedule-summary-modal');
    const panel = document.getElementById('schedule-summary-panel');
    const toggle = document.getElementById('schedule-summary-toggle');
    if (!modal || modal.classList.contains('hidden')) return;
    modal.classList.remove('opacity-100');
    modal.classList.add('opacity-0');
    panel?.classList.remove('scale-100');
    panel?.classList.add('scale-95');
    toggle?.setAttribute('aria-expanded', 'false');
    scheduleSummaryCloseTimer = setTimeout(() => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }, 200);
}

document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeScheduleSummary();
});
