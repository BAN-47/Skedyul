// SELECTED_DATE is set by a small inline <script> in pbs.blade.php
const SELECTED_DATE = window.PBS_SELECTED_DATE;

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
        // Global mode: fetch all courses everywhere matching this category
        const globalList = window.ALL_SUBJECTS_LIST || [];
        subjectsToLoad = globalList.filter(s => String(s.cat) === String(selectedCat));
    } else {
        // Standard mode: fetch department subjects matching department & year
        const deptList = (window.COLLEGE_SUBJECTS && window.COLLEGE_SUBJECTS[deptId]) || [];
        subjectsToLoad = deptList;
    }

    subSel.innerHTML = '<option value="">-- Select Subject --</option>';
    subjectsToLoad.forEach(sub => {
        const opt = document.createElement('option');
        opt.value = sub.id;
        opt.textContent = sub.code + ' — ' + sub.name;
        opt.dataset.yearLevel = sub.year || '';
        opt.dataset.semester = sub.sem || '';
        opt.dataset.category = sub.cat || '';
        opt.dataset.lectureHours = sub.lecHours || 0;
        opt.dataset.labHours = sub.labHours || 0;
        
        // REC is always visible globally, other courses follow year/semester filters
        const isRec = sub.code === 'REC';
        const yearMatch = selectedCat || !yearLevel || !sub.year || String(sub.year) === String(yearLevel);
        const semesterMatch = selectedCat || !activeSemester || !sub.sem || String(sub.sem) === activeSemester;
        if (!yearMatch || !semesterMatch) {
            opt.hidden = true;
            opt.style.display = 'none';
        }
        // Always show REC regardless of filters
        if (isRec) {
            opt.hidden = false;
            opt.style.display = '';
        }
        subSel.appendChild(opt);
    });
}

/* ── Edit modal category filter ── */
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
        // reset to department mode: filter by section year level and semester
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

/* ── modal helpers ── */
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
    box.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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

function togglePbsTeacherPicker() {
    const panel = document.getElementById('pbs-teacher-panel');
    const trigger = document.getElementById('pbs-teacher-trigger');
    if (!panel || !trigger) return;
    panel.classList.toggle('hidden');
    trigger.setAttribute('aria-expanded', String(!panel.classList.contains('hidden')));
    if (!panel.classList.contains('hidden')) {
        const search = document.getElementById('pbs-teacher-search');
        search.value = '';
        filterPbsTeachers('');
        setTimeout(() => search.focus(), 30);
    }
}

function filterPbsTeachers(query) {
    const q = query.trim().toLowerCase();
    const options = [...document.querySelectorAll('.pbs-teacher-option')];
    options.forEach(option => { option.style.display = option.dataset.name.includes(q) ? '' : 'none'; });
    const empty = document.getElementById('pbs-teacher-empty');
    if (empty) empty.classList.toggle('hidden', options.some(option => option.style.display !== 'none'));
}

function selectPbsTeacher(option) {
    document.getElementById('add-faculty').value = option.dataset.id;
    document.getElementById('pbs-teacher-label').textContent = option.dataset.label;
    document.getElementById('pbs-teacher-trigger').classList.remove('text-slate-600');
    document.getElementById('pbs-teacher-trigger').classList.add('text-slate-800');
    document.querySelectorAll('.pbs-teacher-option').forEach(item => {
        const selected = item === option;
        item.setAttribute('aria-selected', String(selected));
        item.classList.toggle('bg-indigo-50', selected);
        item.querySelector('.pbs-teacher-check').classList.toggle('hidden', !selected);
    });
    document.getElementById('pbs-teacher-panel').classList.add('hidden');
    document.getElementById('pbs-teacher-trigger').setAttribute('aria-expanded', 'false');
}

document.addEventListener('click', event => {
    const picker = document.getElementById('pbs-teacher-picker');
    if (!picker || picker.contains(event.target)) return;
    document.getElementById('pbs-teacher-panel')?.classList.add('hidden');
    document.getElementById('pbs-teacher-trigger')?.setAttribute('aria-expanded', 'false');
});

/*
  Backend responses come back as { success, conflict, message }.
  - conflict === true  -> a double-booking (teacher/room/section already
    busy at an overlapping time). Nothing was saved. Per spec, this is
    surfaced as the bottom-right toast, not inline in the modal, and the
    modal stays open with the form untouched so you can pick another slot.
  - conflict === false (or absent) -> a plain validation/server error,
    shown inline in the modal as before.
*/
function handleScheduleError(data, inlineBoxId) {
    let message;
    if (data.conflict) {
        message = data.message || 'Conflict: that slot is already taken.';
    } else if (data.message) {
        message = data.message;
    } else if (data.errors) {
        message = Object.values(data.errors).flat()[0] || 'Validation failed.';
    } else {
        message = 'Something went wrong.';
    }
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

/* ── SHIFT TOGGLE ── */
function getCurrentShift() {
    return new URLSearchParams(window.location.search).get('shift') || 'day';
}

function getShiftValue() {
    return getCurrentShift() === 'night' ? 'evening' : 'day';
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

function setShift(shift) {
    if (shift === getCurrentShift()) return;
    const params = new URLSearchParams(window.location.search);
    params.set('shift', shift);
    window.location.search = params.toString();
}

function paintShift() {
    const shift = getCurrentShift();
    const dayBtn   = document.getElementById('shift-day');
    const nightBtn = document.getElementById('shift-night');
    if (!dayBtn || !nightBtn) return;

    if (shift === 'day') {
        dayBtn.className   = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-blue-600 text-white rounded-l';
        nightBtn.className = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-white text-slate-700 border-l border-slate-300 rounded-r';
    } else {
        dayBtn.className   = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-white text-slate-700 rounded-l';
        nightBtn.className = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-blue-600 text-white border-l border-slate-300 rounded-r';
    }
}
paintShift();

/* ── filters / date nav ── */
function applyFilters() {
    const params = new URLSearchParams(window.location.search);
    const department = document.getElementById('filter-department')?.value || '';
    const section = document.getElementById('filter-section')?.value || '';

    department ? params.set('department', department) : params.delete('department');
    section ? params.set('section', section) : params.delete('section');
    params.set('shift', getCurrentShift());

    window.location.search = params.toString();
}



function shiftDate(delta) {
    const d = new Date(SELECTED_DATE + 'T00:00:00');
    d.setDate(d.getDate() + delta);
    const params = new URLSearchParams(window.location.search);
    params.set('date', d.toISOString().slice(0, 10));
    if (!params.has('shift')) params.set('shift', getCurrentShift());
    window.location.search = params.toString();
}

/* ── ADD (opened by clicking an empty grid cell) ── */
function addMinutesToTime(hhmm, minutesToAdd) {
    const [h, m] = hhmm.split(':').map(Number);
    const total  = (h * 60 + m + minutesToAdd) % (24 * 60);
    const hh     = String(Math.floor(total / 60)).padStart(2, '0');
    const mm     = String(total % 60).padStart(2, '0');
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
        const yearMatch = !yearLevel || !subYear || String(subYear) === String(yearLevel);
        const semMatch = !activeSemester || !opt.getAttribute('data-semester') || opt.getAttribute('data-semester') === activeSemester;
        const match = yearMatch && semMatch;
        opt.hidden = !match;
        opt.style.display = match ? '' : 'none';
    });

    // Reset subject if current selection is no longer visible
    const current = subSel.options[subSel.selectedIndex];
    if (current && current.hidden) {
        subSel.value = '';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const headerDept = document.getElementById('filter-department');
    const headerSection = document.getElementById('filter-section');
    if (headerDept && headerSection) {
        const filterSections = () => {
            const deptId = headerDept.value;
            Array.from(headerSection.options).forEach(option => {
                if (!option.value) return;
                const show = !deptId || option.dataset.deptId === deptId;
                option.hidden = !show;
                option.style.display = show ? '' : 'none';
            });
            if (headerSection.selectedOptions[0]?.hidden) headerSection.value = '';
        };
        headerDept.addEventListener('change', filterSections);
        filterSections();
    }
});

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
            filterSubjectsBySection();
        });
    }

    const addSec = document.getElementById('add-section');
    if (addSec) {
        addSec.addEventListener('change', () => {
            filterSubjectsBySection('add-section', 'add-subject');
            filterSubjectsBySection();
        });
    }
    
    const addCat = document.getElementById('add-category');
    if (addCat) {
        addCat.addEventListener('change', () => {
            filterSubjectsByCategory();
        });
    }
    
    const editSec = document.getElementById('edit-section');
    if (editSec) {
        editSec.addEventListener('change', () => {
            filterSubjectsBySection('edit-section', 'edit-subject');
        });
    }
    
    const editCat = document.getElementById('edit-category');
    if (editCat) {
        editCat.addEventListener('change', () => {
            filterSubjectsByCategory();
        });
    }
    
    // Trigger initial filter based on default department
    if (addDept) {
        addDept.dispatchEvent(new Event('change'));
    }
});

function openAddModal(day, startTime) {
    clearInlineError('add-error');

    // Reset form fields (semester + department are fixed / hidden)
    ['add-subject', 'add-faculty', 'add-room', 'add-section', 'add-description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });
    document.getElementById('pbs-teacher-label').textContent = 'Choose a professor';
    document.getElementById('pbs-teacher-trigger').classList.add('text-slate-600');
    document.getElementById('pbs-teacher-trigger').classList.remove('text-slate-800');
    document.getElementById('pbs-teacher-panel').classList.add('hidden');
    document.getElementById('pbs-teacher-trigger').setAttribute('aria-expanded', 'false');
    document.querySelectorAll('.pbs-teacher-option').forEach(option => {
        option.setAttribute('aria-selected', 'false');
        option.classList.remove('bg-indigo-50');
        option.querySelector('.pbs-teacher-check').classList.add('hidden');
    });

    // Reset subject filter (show all until a section is chosen)
    filterSubjectsBySection('add-section', 'add-subject');

    // Pre-fill day + times from the clicked cell
    document.querySelectorAll('.add-day-checkbox').forEach(input => {
        input.checked = input.value === (day || '');
    });
    document.getElementById('add-start').value = startTime || '';
    document.getElementById('add-end').value   = startTime ? addMinutesToTime(startTime, 60) : '';

    // Pre-fill department and section from current page filters when available
    const params = new URLSearchParams(window.location.search);
    const filterDept = params.get('department');
    const filterSection = params.get('section');
    
    // Pre-fill department
    if (filterDept) {
        const deptEl = document.getElementById('add-department');
        if (deptEl) {
            deptEl.value = filterDept;
            // Trigger department change to load sections for that department
            deptEl.dispatchEvent(new Event('change'));
        }
    }
    
    // Pre-fill section (after department change has loaded sections)
    if (filterSection) {
        const sectionEl = document.getElementById('add-section');
        if (sectionEl) {
            // Small delay to allow department change to populate sections
            setTimeout(() => {
                sectionEl.value = filterSection;
                filterSubjectsBySection('add-section', 'add-subject');
            }, 100);
        }
    }

    setAddButtonsLoading(false);
    openModal('modal-add-schedule');
}

function submitAdd(keepOpen = false) {
    clearInlineError('add-error');

    const payload = {
        subj_id:     document.getElementById('add-subject').value,
        fac_id:      document.getElementById('add-faculty').value,
        room_id:     document.getElementById('add-room').value,
        sem_id:      document.getElementById('add-semester').value,
        sec_id:      document.getElementById('add-section').value,
        days:        [...document.querySelectorAll('.add-day-checkbox:checked')].map(input => input.value),
        start_time:  document.getElementById('add-start').value,
        end_time:    document.getElementById('add-end').value,
        shift:       getShiftValue(),
    };

    if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id ||
        !payload.sem_id || !payload.days.length || !payload.start_time || !payload.end_time) {
        const m = 'Please select the professor, room, section, course, at least one day, and both times.';
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

    fetch('/chair/pbs', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
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
            // 422 conflict → toast (bottom-right), modal stays open
            // other errors → inline in modal
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

    document.getElementById('edit-id').value       = block.dataset.scheduleId || '';
    document.getElementById('edit-section').value  = block.dataset.section  || '';
    // Filter subjects by section year level first, then set the subject
    filterSubjectsBySection('edit-section', 'edit-subject');
    document.getElementById('edit-subject').value  = block.dataset.subject  || '';
    document.getElementById('edit-faculty').value  = block.dataset.faculty  || '';
    document.getElementById('edit-day').value      = block.dataset.day      || '';
    document.getElementById('edit-start').value    = block.dataset.start    || '';
    document.getElementById('edit-end').value      = block.dataset.end      || '';

    // Room from data-room attribute on the schedule block
    const roomEl = document.getElementById('edit-room');
    if (roomEl) roomEl.value = block.dataset.room || '';

    setButtonLoading(document.getElementById('edit-submit'), false);
    openModal('modal-edit-schedule');
}

function submitEdit() {
    clearInlineError('edit-error');

    const id = document.getElementById('edit-id').value;
    const payload = {
        subj_id:     document.getElementById('edit-subject').value,
        fac_id:      document.getElementById('edit-faculty').value,
        room_id:     document.getElementById('edit-room')?.value || '',
        sem_id:      document.getElementById('edit-semester').value,
        sec_id:      document.getElementById('edit-section').value,
        day:         document.getElementById('edit-day').value,
        start_time:  document.getElementById('edit-start').value,
        end_time:    document.getElementById('edit-end').value,
        shift:       getShiftValue(),
        _method:     'PUT',
    };

    if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id ||
        !payload.sem_id || !payload.day || !payload.start_time || !payload.end_time) {
        showInlineError('edit-error', 'Please fill in professor, room, semester, section, course, day, and both times.');
        return;
    }
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

    fetch(`/chair/pbs/${id}`, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
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

/* ── DELETE ── */
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
    const btn   = document.getElementById('delete-submit');
    const ok    = deleteCode !== '' && typed === deleteCode;
    btn.disabled = !ok;
    btn.classList.toggle('opacity-50',        !ok);
    btn.classList.toggle('cursor-not-allowed', !ok);
}

function submitDelete() {
    clearInlineError('delete-error');
    const id = document.getElementById('delete-id').value;
    document.getElementById('delete-submit').disabled = true;

    fetch(`/chair/pbs/${id}`, {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
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
async function saveDraft() {
    const sectionId = document.getElementById('filter-section')?.value || '';
    const input = document.getElementById('section-student-count');
    const countRaw = input ? String(input.value).trim() : '';
    const count = countRaw === '' ? null : parseInt(countRaw, 10);

    // Save section student count (summary) when a section is selected
    if (sectionId && count !== null && !Number.isNaN(count) && count >= 0) {
        try {
            const res = await fetch('/chair/pbs/section/students', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken(),
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    sec_id: sectionId,
                    sec_no_of_student: count,
                }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok || !data.success) {
                showToast(data.message || 'Could not save student count.');
                return;
            }
            if (input && data.sec_no_of_student != null) {
                input.value = data.sec_no_of_student;
            }
        } catch (e) {
            showToast('Network error saving student count.');
            return;
        }
    }

    // Save draft marker for schedules already on the grid (they save on Confirm)
    try {
        const res = await fetch('/chair/pbs/save-draft', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
            },
            credentials: 'same-origin',
            body: JSON.stringify({
                section: sectionId,
                semester: document.getElementById('filter-semester')?.value || '',
            }),
        });
        const data = await res.json().catch(() => ({}));
        showToast(data.message || 'Draft saved (summary + schedules).');
    } catch (e) {
        showToast('Could not save the draft.');
    }
}

function clearAll() {
    if (!confirm('Clear all plotted schedules for the current filter? This cannot be undone.')) return;

    const params = new URLSearchParams(window.location.search);
    const body = {
        section:  params.get('section')  || '',
        semester: params.get('semester') || '',
    };

    fetch('/chair/pbs/clear', {
        method:  'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken(),
            'Accept': 'application/json',
        },
        body: JSON.stringify(body),
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
