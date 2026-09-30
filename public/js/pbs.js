// SELECTED_DATE is set by a small inline <script> in pbs.blade.php
const SELECTED_DATE = window.PBS_SELECTED_DATE;

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

function showToast(msg) {
    const t = document.getElementById('toast');
    const m = document.getElementById('toast-msg');
    if (!t || !m) return;
    m.textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 4200);
}

function showInlineError(boxId, msg) {
    const box = document.getElementById(boxId);
    if (!box) return;
    box.textContent = msg;
    box.classList.remove('hidden');
}
function clearInlineError(boxId) {
    const box = document.getElementById(boxId);
    if (box) box.classList.add('hidden');
}

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
    if (data.conflict) {
        showToast(data.message || 'Conflict: that slot is already taken.');
    } else if (data.message) {
        showInlineError(inlineBoxId, data.message);
    } else if (data.errors) {
        // Laravel validation errors: { errors: { field: ['msg'] } }
        const first = Object.values(data.errors).flat()[0];
        showInlineError(inlineBoxId, first || 'Validation failed.');
    } else {
        showInlineError(inlineBoxId, 'Something went wrong.');
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

/* ── SHIFT TOGGLE ── */
function getCurrentShift() {
    return new URLSearchParams(window.location.search).get('shift') || 'day';
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

    // Program locked server-side; semester label includes academic year.
    // Semester is fixed by admin settings — do not put it in the URL
    const map = {
        program:       'filter-program',
        section:       'filter-section',
    };

    Object.entries(map).forEach(([key, id]) => {
        const el  = document.getElementById(id);
        const val = el ? el.value : '';
        val ? params.set(key, val) : params.delete(key);
    });

    if (!params.has('shift')) params.set('shift', getCurrentShift());
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

function openAddModal(day, startTime) {
    clearInlineError('add-error');

    // Reset all form fields
    ['add-subject', 'add-faculty', 'add-room', 'add-semester', 'add-program',
     'add-year', 'add-section', 'add-description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    // Pre-fill day + times from the clicked cell
    document.getElementById('add-day').value   = day || '';
    document.getElementById('add-start').value = startTime || '';
    document.getElementById('add-end').value   = startTime ? addMinutesToTime(startTime, 60) : '';

    // Pre-fill from current page filters when available
    const params = new URLSearchParams(window.location.search);
    const filterSemester = params.get('semester');
    const filterSection  = params.get('section');
    const filterProgram  = params.get('program');
    const filterYear     = params.get('year');

    if (filterSemester) {
        const el = document.getElementById('add-semester');
        if (el) el.value = filterSemester;
    }
    if (filterSection) {
        const el = document.getElementById('add-section');
        if (el) el.value = filterSection;
    }
    if (filterProgram) {
        const el = document.getElementById('add-program');
        if (el) el.value = filterProgram;
    }
    if (filterYear) {
        const el = document.getElementById('add-year');
        if (el) el.value = filterYear;
    }

    document.getElementById('add-submit').disabled = false;
    openModal('modal-add-schedule');
}

function submitAdd() {
    clearInlineError('add-error');

    const payload = {
        subj_id:     document.getElementById('add-subject').value,
        fac_id:      document.getElementById('add-faculty').value,
        room_id:     document.getElementById('add-room').value,
        sem_id:      document.getElementById('add-semester').value,
        sec_id:      document.getElementById('add-section').value,
        day:         document.getElementById('add-day').value,
        start_time:  document.getElementById('add-start').value,
        end_time:    document.getElementById('add-end').value,
    };

    if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id ||
        !payload.sem_id || !payload.day || !payload.start_time || !payload.end_time) {
        showInlineError('add-error', 'Please fill in subject, professor, room, semester, section, day, and both times.');
        return;
    }
    if (payload.end_time <= payload.start_time) {
        showInlineError('add-error', 'End time must be later than start time.');
        return;
    }

    const btn = document.getElementById('add-submit');
    btn.disabled = true;

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
        const data = await res.json().catch(() => ({}));

        if (data.success) {
            closeModal('modal-add-schedule');
            showToast(data.message || 'Schedule added.');
            setTimeout(() => location.reload(), 800);
        } else {
            // 422 conflict → toast (bottom-right), modal stays open
            // other errors → inline in modal
            handleScheduleError(data, 'add-error');
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

    document.getElementById('edit-id').value       = block.dataset.scheduleId || '';
    document.getElementById('edit-subject').value  = block.dataset.subject  || '';
    document.getElementById('edit-faculty').value  = block.dataset.faculty  || '';
    document.getElementById('edit-section').value  = block.dataset.section  || '';
    document.getElementById('edit-semester').value = block.dataset.semester || '';
    document.getElementById('edit-day').value      = block.dataset.day      || '';
    document.getElementById('edit-start').value    = block.dataset.start    || '';
    document.getElementById('edit-end').value      = block.dataset.end      || '';

    // Room from data-room attribute on the schedule block
    const roomEl = document.getElementById('edit-room');
    if (roomEl) roomEl.value = block.dataset.room || '';

    document.getElementById('edit-submit').disabled = false;
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
        _method:     'PUT',
    };

    if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id ||
        !payload.sem_id || !payload.day || !payload.start_time || !payload.end_time) {
        showInlineError('edit-error', 'Please fill in subject, professor, room, semester, section, day, and both times.');
        return;
    }
    if (payload.end_time <= payload.start_time) {
        showInlineError('edit-error', 'End time must be later than start time.');
        return;
    }

    const btn = document.getElementById('edit-submit');
    btn.disabled = true;

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
            btn.disabled = false;
        }
    })
    .catch(() => {
        showInlineError('edit-error', 'Something went wrong. Please try again.');
        btn.disabled = false;
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
function saveDraft() {
    fetch('/chair/pbs/save-draft', {
        method:  'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken(), 'Accept': 'application/json' },
    })
    .then(async res => {
        const data = await res.json().catch(() => ({}));
        showToast(data.message || 'Draft saved.');
    })
    .catch(() => showToast('Could not save the draft.'));
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
