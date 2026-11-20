<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
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
                    <button onclick="openModal('modal-notify')" class="btn btn-secondary flex items-center gap-1.5">
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
                        <div class="modal-title" id="notification-modal-title">Send Notification</div>
                        <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
                    </div>

                    <div class="mb-4">
                        <label class="field-label">Send to</label>
                        <select class="field-input" id="notif-audience" onchange="changeNotificationAudience()">
                            <option value="chairs">Department chairs</option>
                            <option value="system_admin">System administrators — report an issue or bug</option>
                        </select>
                    </div>

                    {{-- RECIPIENTS --}}
                    <div class="mb-4" id="chair-recipient-options">
                        <div class="field-label mb-2">Recipients</div>
                        <label
                            class="flex items-center gap-3 px-3 py-2.5 border border-slate-200 rounded-xl mb-2 cursor-pointer hover:bg-slate-50 transition">
                            <input type="checkbox" id="check-all" onchange="toggleAll(this)"
                                class="w-4 h-4 accent-blue-600" checked>
                            <span class="text-[13px] font-semibold text-slate-700">All Department Chairs</span>
                        </label>
                        <div id="chair-list" class="flex flex-col gap-2">
                            @forelse(($notificationChairs ?? collect()) as $index => $chair)
                                @php
                                    $chairColors = ['#f97316', '#7c3aed', '#0891b2', '#16a34a', '#ef4444'];
                                    $chairColor = $chairColors[$index % count($chairColors)];
                                @endphp
                                <label class="flex items-center gap-3 px-3 py-2.5 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition">
                                    <input type="checkbox" class="chair-checkbox w-4 h-4 accent-blue-600"
                                        value="{{ $chair['dc_usr_id'] }}" checked onchange="syncSelectAll()">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-[12px] font-bold flex-shrink-0"
                                        style="background-color: {{ $chairColor }}">
                                        {{ $chair['initials'] }}
                                    </div>
                                    <div>
                                        <div class="text-[13px] font-semibold text-slate-900">{{ $chair['chair_name'] }}</div>
                                        <div class="text-[11px] text-slate-400">Chair · {{ $chair['dept_code'] }}</div>
                                    </div>
                                </label>
                            @empty
                                <div class="text-slate-400 text-[13px] text-center py-2">No active department chairs found in your college.</div>
                            @endforelse
                        </div>
                    </div>
                    <div id="issue-recipient-note" class="mb-4 hidden rounded-xl border border-blue-100 bg-blue-50 px-3 py-3 text-[13px] text-blue-800">
                        Your report will be sent to all active system administrators.
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
                        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
                    </div>

                    {{-- MESSAGE --}}
                    <div class="mb-4">
                        <label class="field-label">Message</label>
                        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;"
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
                        <button id="send-notification-button" class="btn btn-primary" onclick="sendNotification()">Send
                            Notification</button>
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
                const pendingCountUrl = "{{ route('dean.notifications.pending-count') }}";
                // The badge rendered by the server may use a different pending
                // count than this endpoint. Establish a live baseline first so
                // opening a Dean page never announces existing approvals as new.
                let lastPendingCount = null;

                async function pollPendingCount() {
                    try {
                        const res = await fetch(pendingCountUrl, {
                            cache: 'no-store',
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        if (!res.ok) throw new Error(`Pending count request failed (${res.status})`);
                        const data = await res.json();
                        const badge = document.getElementById('notif-badge');
                        const count = Number(data.count);
                        if (!Number.isFinite(count)) return;
                        if (badge) badge.textContent = String(count);

                        if (lastPendingCount !== null && count > lastPendingCount) {
                            showToast('New pending approval received', '#2563eb');
                        }
                        lastPendingCount = count;
                    } catch (err) {
                        console.error('Poll failed:', err);
                    }
                }
                pollPendingCount();
                setInterval(pollPendingCount, 15000);

                // ── MODALS ────────────────────────────────────────────────────────
                function openModal(id) {
                    document.getElementById(id).classList.add('open');
                }

                function closeModal(id) {
                    document.getElementById(id).classList.remove('open');
                }
                document.querySelectorAll('.modal-overlay').forEach(m => {
                    m.addEventListener('click', e => {
                        if (e.target === m) m.classList.remove('open');
                    });
                });

                // ── TOAST ─────────────────────────────────────────────────────────
                function showToast(msg, color = '#0f172a') {
                    const t = document.getElementById('toast');
                    document.getElementById('toast-msg').textContent = msg;
                    t.style.background = color;
                    t.classList.add('show');
                    setTimeout(() => t.classList.remove('show'), 3500);
                }

                function escapeNotificationText(value) {
                    return String(value).replace(/[&<>"']/g, character => ({
                        '&': '&amp;',
                        '<': '&lt;',
                        '>': '&gt;',
                        '"': '&quot;',
                        "'": '&#39;'
                    })[character]);
                }

                const sendNotificationUrl = "{{ route('dean.notifications.send') }}";
                const reportIssueUrl = "{{ route('dean.notifications.report-issue') }}";

                function changeNotificationAudience() {
                    const reportMode = document.getElementById('notif-audience').value === 'system_admin';
                    const typeSelect = document.getElementById('notif-type');
                    document.getElementById('chair-recipient-options').classList.toggle('hidden', reportMode);
                    document.getElementById('issue-recipient-note').classList.toggle('hidden', !reportMode);
                    document.getElementById('notification-modal-title').textContent = reportMode
                        ? 'Report a System Issue'
                        : 'Send Notification to Chairs';
                    document.getElementById('notif-title').placeholder = reportMode
                        ? 'e.g. Schedule page returns a server error'
                        : 'e.g. Schedule Submission Reminder';
                    document.getElementById('send-notification-button').textContent = reportMode
                        ? 'Send Issue Report'
                        : 'Send Notification';
                    typeSelect.innerHTML = reportMode
                        ? '<option value="bug">Bug or broken feature</option><option value="system_error">System outage or error</option><option value="access">Access or account issue</option><option value="other">Other system issue</option>'
                        : '<option value="info">General Info</option><option value="reminder">Reminder</option><option value="urgent">Urgent</option><option value="deadline">Deadline Notice</option>';
                }
                // ── SELECT ALL ────────────────────────────────────────────────────
                function toggleAll(master) {
                    document.querySelectorAll('.chair-checkbox').forEach(cb => cb.checked = master.checked);
                }

                function syncSelectAll() {
                    const all = document.querySelectorAll('.chair-checkbox');
                    const checked = document.querySelectorAll('.chair-checkbox:checked');
                    const master = document.getElementById('check-all');
                    master.checked = all.length > 0 && checked.length === all.length;
                    master.indeterminate = checked.length > 0 && checked.length < all.length;
                }

                // ── SEND ──────────────────────────────────────────────────────────
                async function sendNotification() {
                    const title = document.getElementById('notif-title').value.trim();
                    const message = document.getElementById('notif-message').value.trim();
                    const type = document.getElementById('notif-type').value;
                    const reportMode = document.getElementById('notif-audience').value === 'system_admin';
                    const selected = [...document.querySelectorAll('.chair-checkbox:checked')].map(cb => cb.value);

                    if (!title) {
                        showToast('Please enter a title.', '#dc2626');
                        return;
                    }
                    if (!message) {
                        showToast('Please enter a message.', '#dc2626');
                        return;
                    }
                    if (!reportMode && !selected.length) {
                        showToast('Select at least one chair.', '#dc2626');
                        return;
                    }

                    const sendButton = document.getElementById('send-notification-button');
                    const buttonLabel = sendButton.textContent;
                    sendButton.disabled = true;
                    sendButton.textContent = 'Sending…';
                    sendButton.classList.add('opacity-70', 'cursor-wait');

                    try {
                        const res = await fetch(reportMode ? reportIssueUrl : sendNotificationUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': CSRF,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify(reportMode
                                ? { title, message, type }
                                : { chair_ids: selected, title, message, type }),
                        });
                        const data = await res.json();

                        if (!res.ok || !data.success) {
                            throw new Error(data.message || 'Failed to send. Please try again.');
                        }

                        addToRecentlySent({
                            title,
                            type,
                            count: data.sent,
                            target: reportMode ? 'system admin(s)' : 'chair(s)',
                        });
                        document.getElementById('notif-title').value = '';
                        document.getElementById('notif-message').value = '';
                        showToast(reportMode
                            ? 'Issue report sent to system administrators.'
                            : '✅ Sent to ' + data.sent + ' chair(s)!', '#16a34a');
                        closeModal('modal-notify');
                    } catch (error) {
                        showToast(error.message || 'Network error. Try again.', '#dc2626');
                    } finally {
                        sendButton.disabled = false;
                        sendButton.textContent = buttonLabel;
                        sendButton.classList.remove('opacity-70', 'cursor-wait');
                    }
                }

                // ── RECENTLY SENT ─────────────────────────────────────────────────
                function addToRecentlySent({
                    title,
                    type,
                    count,
                    target = 'chair(s)'
                }) {
                    const container = document.getElementById('recently-sent');
                    const empty = container.querySelector('.text-slate-400');
                    if (empty) empty.remove();

                    const colors = {
                        info: 'bg-blue-50 text-blue-600',
                        reminder: 'bg-amber-50 text-amber-600',
                        urgent: 'bg-red-50 text-red-600',
                        deadline: 'bg-slate-100 text-slate-500'
                    };
                    const labels = {
                        info: 'Info',
                        reminder: 'Reminder',
                        urgent: 'Urgent',
                        deadline: 'Deadline'
                    };
                    const time = new Date().toLocaleTimeString('en-US', {
                        hour: 'numeric',
                        minute: '2-digit',
                        hour12: true
                    });

                    const item = document.createElement('div');
                    item.style.cssText = 'padding:10px 12px;border-radius:10px;border:1px solid #e2e8f0;margin-bottom:6px;';
                    item.innerHTML = `
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:3px;">
                    <div style="display:flex;align-items:center;gap:6px;">
                        <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;" class="${colors[type]}">${labels[type]}</span>
                        <span style="font-size:13px;font-weight:600;color:#0f172a;">${escapeNotificationText(title)}</span>
                    </div>
                    <span style="font-size:11px;color:#94a3b8;">Sent ${time} · ${count} ${target}</span>
                </div>`;
                    container.prepend(item);
                }
            </script>
