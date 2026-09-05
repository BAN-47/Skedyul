<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — {{ $title ?? 'Dean' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

    {{-- SIDEBAR IS A SEPARATE FILE --}}
    @include('partials.dean_sidebar')

    {{-- MAIN --}}
    <div class="app-main">

        {{-- TOPBAR --}}
        <div class="topbar">
            <div class="topbar-title">{{ $title ?? 'Dean Dashboard' }}</div>
            <div class="flex items-center gap-2">
                <button onclick="openModal('modal-export')" class="btn btn-primary">Export Report</button>
                <button onclick="openModal('modal-notify')"
                    class="btn btn-secondary flex items-center gap-1.5">
                    Notifications
                    <span id="notif-badge"
                        class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                        {{ $pendingApprovalsCount ?? 0 }}
                    </span>
                </button>
            </div>
        </div>

        {{-- ══ NOTIFICATION MODAL ══ --}}
        <div class="modal-overlay" id="modal-notify">
            <div class="modal-box" style="width:520px;">
                <div class="modal-header">
                    <div class="modal-title">Send Notification to Chairs</div>
                    <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
                </div>

                {{-- RECIPIENTS --}}
                <div class="mb-4">
                    <div class="field-label mb-2">Recipients</div>
                    <label class="flex items-center gap-3 px-3 py-2.5 border border-slate-200 rounded-xl mb-2 cursor-pointer hover:bg-slate-50 transition">
                        <input type="checkbox" id="check-all" onchange="toggleAll(this)" class="w-4 h-4 accent-blue-600" checked>
                        <span class="text-[13px] font-semibold text-slate-700">All Department Chairs</span>
                    </label>
                    <div id="chair-list" class="flex flex-col gap-2">
                        <div class="text-center py-4 text-slate-400 text-[13px]">Loading chairs...</div>
                    </div>
                </div>

                {{-- TYPE --}}
                <div class="mb-3">
                    <label class="field-label">Notification Type</label>
                    <select class="field-input" id="notif-type">
                        <option value="info">General Info</option>
                        <option value="reminder">Reminder</option>
                        <option value="urgent">Urgent</option>
                        <option value="deadline">Deadline Notice</option>
                    </select>
                </div>

                {{-- TITLE --}}
                <div class="mb-3">
                    <label class="field-label">Title</label>
                    <input class="field-input" id="notif-title"
                        placeholder="e.g. Schedule Submission Reminder">
                </div>

                {{-- MESSAGE --}}
                <div class="mb-4">
                    <label class="field-label">Message</label>
                    <textarea class="field-input" id="notif-message"
                        rows="4" style="resize:vertical;"
                        placeholder="Write your message to the chairs..."></textarea>
                </div>

                {{-- RECENTLY SENT --}}
                <div>
                    <div class="field-label mb-2">Recently Sent</div>
                    <div id="recently-sent" class="text-[12px] text-slate-400 text-center py-2">
                        No notifications sent yet this session.
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
                    <button class="btn btn-primary" onclick="sendNotification()">Send Notification</button>
                </div>
            </div>
        </div>

        {{-- ══ EXPORT MODAL ══ --}}
        <div class="modal-overlay" id="modal-export">
            <div class="modal-box" style="width:440px;">
                <div class="modal-header">
                    <div class="modal-title">Export Report</div>
                    <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
                </div>
                <div class="flex flex-col gap-3 mt-1">
                    <div>
                        <label class="field-label">Report Type</label>
                        <select class="field-input">
                            <option>Master Schedule</option>
                            <option>Faculty Workload Report</option>
                            <option>Faculty Deployment Report</option>
                            <option>Department Summary</option>
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Format</label>
                        <select class="field-input">
                            <option>PDF</option>
                            <option>Excel (.xlsx)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
                    <button class="btn btn-primary"
                        onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
                </div>
            </div>
        </div>

        <div class="toast" id="toast"><span id="toast-msg"></span></div>

        <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

        // ── MODALS ────────────────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.add('open');
            if (id === 'modal-notify') loadChairs();
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
        });

        // ── TOAST ─────────────────────────────────────────────────────────
        function showToast(msg, color = '#0f172a') {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.style.background = color;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3500);
        }

        // ── LOAD CHAIRS ───────────────────────────────────────────────────
        let chairsLoaded = false;
        const chairsUrl = "{{ route('dean.notifications') }}";

        async function loadChairs() {
            if (chairsLoaded) return;
            try {
            const res  = await fetch(chairsUrl, {
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (!res.ok) throw new Error(`Chair request failed (${res.status})`);
                const data = await res.json();
                renderChairs(data.chairs);
                chairsLoaded = true;
            } catch (err) {
                console.error('Unable to load department chairs:', err);
                document.getElementById('chair-list').innerHTML =
                    '<div class="text-red-500 text-[12px] text-center py-2">Failed to load chairs. Please try again.</div>';
            }
        }

        function renderChairs(chairs) {
            const list = document.getElementById('chair-list');
            if (!chairs || !chairs.length) {
                list.innerHTML = '<div class="text-slate-400 text-[13px] text-center py-2">No chairs found in database.</div>';
                return;
            }
            const colors = ['bg-orange-500','bg-violet-600','bg-cyan-600','bg-green-600','bg-red-500'];
            list.innerHTML = chairs.map(chair => {
                const name = chair.chair_name || 'Unnamed Chair';
                const color = colors[Math.abs(name.split('').reduce((a,c) => a + c.charCodeAt(0), 0)) % colors.length];
                return `
                <label class="flex items-center gap-3 px-3 py-2.5 border border-slate-200
                               rounded-xl cursor-pointer hover:bg-slate-50 transition">
                    <input type="checkbox" class="chair-checkbox w-4 h-4 accent-blue-600"
                           value="${chair.dc_usr_id}" checked onchange="syncSelectAll()">
                    <div class="w-9 h-9 rounded-full ${color} flex items-center justify-center
                                text-white text-[12px] font-bold flex-shrink-0">
                        ${chair.initials}
                    </div>
                    <div>
                        <div class="text-[13px] font-semibold text-slate-900">${name}</div>
                        <div class="text-[11px] text-slate-400">Chair · ${chair.dept_code}</div>
                    </div>
                </label>`;
            }).join('');
            syncSelectAll();
        }

        // ── SELECT ALL ────────────────────────────────────────────────────
        function toggleAll(master) {
            document.querySelectorAll('.chair-checkbox').forEach(cb => cb.checked = master.checked);
        }
        function syncSelectAll() {
            const all    = document.querySelectorAll('.chair-checkbox');
            const checked = document.querySelectorAll('.chair-checkbox:checked');
            const master  = document.getElementById('check-all');
            master.checked      = all.length > 0 && checked.length === all.length;
            master.indeterminate = checked.length > 0 && checked.length < all.length;
        }

        // ── SEND ──────────────────────────────────────────────────────────
        async function sendNotification() {
            const title   = document.getElementById('notif-title').value.trim();
            const message = document.getElementById('notif-message').value.trim();
            const type    = document.getElementById('notif-type').value;
            const selected = [...document.querySelectorAll('.chair-checkbox:checked')].map(cb => cb.value);

            if (!title)            { showToast('Please enter a title.', '#dc2626'); return; }
            if (!message)          { showToast('Please enter a message.', '#dc2626'); return; }
            if (!selected.length)  { showToast('Select at least one chair.', '#dc2626'); return; }

            try {
                const res  = await fetch('/dean/notifications/send', {
                    method:  'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ chair_ids: selected, title, message, type }),
                });
                const data = await res.json();

                if (data.success) {
                    addToRecentlySent({ title, type, count: data.sent });
                    document.getElementById('notif-title').value   = '';
                    document.getElementById('notif-message').value = '';
                    showToast('✅ Sent to ' + data.sent + ' chair(s)!', '#16a34a');
                    closeModal('modal-notify');
                } else {
                    showToast('Failed to send. Try again.', '#dc2626');
                }
            } catch {
                showToast('Network error. Try again.', '#dc2626');
            }
        }

        // ── RECENTLY SENT ─────────────────────────────────────────────────
        function addToRecentlySent({ title, type, count }) {
            const container = document.getElementById('recently-sent');
            const empty     = container.querySelector('.text-slate-400');
            if (empty) empty.remove();

            const colors = { info:'bg-blue-50 text-blue-600', reminder:'bg-amber-50 text-amber-600', urgent:'bg-red-50 text-red-600', deadline:'bg-slate-100 text-slate-500' };
            const labels = { info:'Info', reminder:'Reminder', urgent:'Urgent', deadline:'Deadline' };
            const time   = new Date().toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true });

            const item = document.createElement('div');
            item.style.cssText = 'padding:10px 12px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:6px;';
            item.innerHTML = `
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;" class="${colors[type]}">${labels[type]}</span>
                        <span style="font-size:13px;font-weight:600;color:#0f172a;">${title}</span>
                    </div>
                    <span style="font-size:11px;color:#94a3b8;">Sent ${time} · ${count} chair(s)</span>
                </div>`;
            container.prepend(item);
        }
        </script>