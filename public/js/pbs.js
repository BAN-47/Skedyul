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
    setTimeout(() => t.classList.remove('show'), 3200);
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

/* ── SHIFT TOGGLE ────────────────────────────────────────────────────────────
   The grid rows are rendered server-side in the blade based on ?shift= param.
   setShift() just updates the URL and reloads — the blade does the rendering.
   paintShift() only updates button styles to match the current URL param.
────────────────────────────────────────────────────────────────────────────── */
function getCurrentShift() {
    return new URLSearchParams(window.location.search).get('shift') || 'day';
}

function setShift(shift) {
    // Only reload if actually changing
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

    const activeClass   = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-blue-600 text-white rounded-l';
    const inactiveClass = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-white text-slate-700 border-l border-slate-300 rounded-r';

    if (shift === 'day') {
        dayBtn.className   = activeClass + ' rounded-l';
        nightBtn.className = inactiveClass;
    } else {
        dayBtn.className   = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-white text-slate-700 rounded-l';
        nightBtn.className = 'px-2.5 py-1.5 text-[11px] font-bold uppercase bg-blue-600 text-white border-l border-slate-300 rounded-r';
    }
}

// Run on page load
paintShift();

/* ── filters / date nav ── */
function applyFilters() {
    const params = new URLSearchParams(window.location.search);
    const map = {
        program:  'filter-program',
        year:     'filter-year',
        section:  'filter-section',
        semester: 'filter-semester',
    };
    Object.entries(map).forEach(([key, id]) => {
        const el  = document.getElementById(id);
        const val = el ? el.value : '';
        val ? params.set(key, val) : params.delete(key);
    });
    // Keep the current shift when applying filters
    if (!params.has('shift')) params.set('shift', getCurrentShift());
    window.location.search = params.toString();
}

function shiftDate(delta) {
    const d = new Date(SELECTED_DATE + 'T00:00:00');
    d.setDate(d.getDate() + delta);
    const params = new URLSearchParams(window.location.search);
    params.set('date', d.toISOString().slice(0, 10));
    // Keep the current shift when navigating dates
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
    ['add-subject','add-faculty','add-semester','add-program',
     'add-year','add-section','add-description'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

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
        fac_id:      document.getElementById('add-faculty').value,
        sem_id:      document.getElementById('add-semester').value,
        prog_id:     document.getElementById('add-program').value,
        year_level:  document.getElementById('add-year').value,
        sec_id:      document.getElementById('add-section').value,
        day:         document.getElementById('add-day').value,
        start_time:  document.getElementById('add-start').value,
        end_time:    document.getElementById('add-end').value,
        description: document.getElementById('add-description').value,
    };

    if (!payload.subj_id || !payload.fac_id || !payload.sec_id ||
        !payload.day || !payload.start_time || !payload.end_time) {
        showInlineError('add-error', 'Please fill in subject, professor, section, day, and both times.');
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
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
            'Accept':        'application/json',
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
    document.getElementById('edit-id').value       = block.dataset.scheduleId;
    document.getElementById('edit-subject').value  = block.dataset.subject  || '';
    document.getElementById('edit-faculty').value  = block.dataset.faculty  || '';
    document.getElementById('edit-section').value  = block.dataset.section  || '';
    document.getElementById('edit-semester').value = block.dataset.semester || '';
    document.getElementById('edit-day').value      = block.dataset.day      || '';
    document.getElementById('edit-start').value    = block.dataset.start    || '';
    document.getElementById('edit-end').value      = block.dataset.end      || '';
    document.getElementById('edit-submit').disabled = false;
    openModal('modal-edit-schedule');
}

function submitEdit() {
    clearInlineError('edit-error');
    const id      = document.getElementById('edit-id').value;
    const payload = {
        subj_id:     document.getElementById('edit-subject').value,
        fac_id:      document.getElementById('edit-faculty').value,
        sem_id:      document.getElementById('edit-semester').value,
        sec_id:      document.getElementById('edit-section').value,
        day:         document.getElementById('edit-day').value,
        start_time:  document.getElementById('edit-start').value,
        end_time:    document.getElementById('edit-end').value,
        _method:     'PUT',
    };

    if (payload.end_time <= payload.start_time) {
        showInlineError('edit-error', 'End time must be later than start time.');
        return;
    }

    const btn = document.getElementById('edit-submit');
    btn.disabled = true;

    fetch(`/chair/pbs/${id}`, {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
            'Accept':        'application/json',
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
            showInlineError('edit-error', data.message || 'Could not update this schedule.');
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
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
            'Accept':        'application/json',
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

    fetch('/chair/pbs/clear', {
        method:  'POST',
        headers: {
            'Content-Type':  'application/json',
            'X-CSRF-TOKEN':  csrfToken(),
            'Accept':        'application/json',
        },
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