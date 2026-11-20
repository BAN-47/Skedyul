<div class="topbar">
    <div class="topbar-title min-w-0">
        @if (!empty($collegeContext))
            <div class="flex min-w-0 items-center gap-2">
                <a href="{{ route('chair.dashboard') }}"
                    class="shrink-0 text-[13px] font-bold text-slate-800 no-underline hover:text-blue-700">Dashboard</a>
                <span class="shrink-0 text-slate-300" aria-hidden="true">/</span>
                <span class="truncate text-[11px] font-medium leading-tight text-slate-600">{{ $collegeContext }}</span>
            </div>
            @if (!empty($title) && strtolower(trim($title)) !== 'dashboard')
                <div class="truncate text-[10px] font-medium text-slate-400">{{ $title }}</div>
            @endif
        @else
            {{ $title ?? 'Department Chair Dashboard' }}
        @endif
    </div>
    <div class="flex items-center gap-2.5">
        @if (!empty($contextTag))
            <span
                class="hidden max-w-[19rem] truncate rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-[10px] font-bold text-blue-700 sm:inline-flex sm:items-center"
                title="{{ $contextTag }}">{{ $contextTag }}</span>
        @elseif(!empty($badgeText))
            <span class="badge badge-blue text-[11px]">{{ $badgeText }}</span>
        @endif
        <button type="button" onclick="chairHeaderOpenAdminReport()"
            class="inline-flex shrink-0 rounded-lg border border-blue-200 bg-blue-50 px-2 py-2 text-[10px] font-semibold text-blue-700 transition hover:bg-blue-100 sm:px-3 sm:text-[12px]">
            Contact Admin
        </button>
        <div id="topbar-notif-bell" class="relative">
            <button type="button" onclick="chairHeaderToggleNotifDropdown()"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
                Notifications
                <span id="notif-count" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    {{ $notifCount ?? 0 }}
                </span>
            </button>
            <div id="notif-dropdown" style="display:none;"
                class="absolute top-11 right-0 w-[340px] bg-white border border-slate-200 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] z-[100] overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-200 text-sm font-bold text-slate-900">Notifications</div>
                <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
                <div class="px-4 py-2.5 border-t border-slate-200 text-center">
                    <button type="button" onclick="chairHeaderMarkAllRead()"
                        class="text-[12px] text-blue-600 font-semibold bg-transparent border-none cursor-pointer">
                        Mark all as read
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="chair-notification-viewer" class="fixed inset-0 z-[1300] hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="chair-notification-viewer-title">
    <div class="w-full max-w-lg overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div class="min-w-0">
                <p id="chair-notification-viewer-type" class="mb-1 text-[10px] font-bold uppercase tracking-wide text-blue-600"></p>
                <h2 id="chair-notification-viewer-title" class="text-base font-bold text-slate-900">Notification</h2>
                <p id="chair-notification-viewer-time" class="mt-1 text-xs text-slate-400"></p>
            </div>
            <button type="button" onclick="chairHeaderCloseNotification()" class="rounded-lg px-2 py-1 text-xl leading-none text-slate-400 hover:bg-slate-100" aria-label="Close notification">×</button>
        </div>
        <div id="chair-notification-viewer-message" class="max-h-[60vh] overflow-y-auto whitespace-pre-wrap break-words px-5 py-5 text-sm leading-6 text-slate-700"></div>
        <p id="chair-notification-viewer-error" class="hidden mx-5 mb-4 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700" role="status"></p>
        <div class="flex justify-end border-t border-slate-100 px-5 py-3">
            <button type="button" onclick="chairHeaderCloseNotification()" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Done</button>
        </div>
    </div>
</div>

<div id="chair-admin-report-modal" class="fixed inset-0 z-[1350] hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="chair-admin-report-title" onclick="if(event.target===this) chairHeaderCloseAdminReport()">
    <form id="chair-admin-report-form" class="w-full max-w-lg overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl" onsubmit="chairHeaderSubmitAdminReport(event)">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 id="chair-admin-report-title" class="text-base font-bold text-slate-900">Contact System Admin</h2>
                <p class="mt-1 text-xs text-slate-500">Report a bug, system issue, or feature idea.</p>
            </div>
            <button type="button" onclick="chairHeaderCloseAdminReport()" class="rounded-lg px-2 py-1 text-xl leading-none text-slate-400 hover:bg-slate-100" aria-label="Close">×</button>
        </div>
        <div class="space-y-4 px-5 py-5">
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Message type</span>
                <select name="category" required class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm text-slate-700 focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                    <option value="bug">Bug report</option>
                    <option value="system_issue">System issue</option>
                    <option value="feature_request">Feature request</option>
                    <option value="other">Other feedback</option>
                </select>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Title</span>
                <input name="title" required maxlength="175" placeholder="Briefly describe what you need" class="w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
            </label>
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Details</span>
                <textarea name="message" required maxlength="1700" rows="5" placeholder="Include what happened, where you saw it, or what feature would help." class="w-full resize-y rounded-lg border border-slate-200 px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"></textarea>
            </label>
            <label class="block">
                <span class="mb-1.5 block text-[11px] font-bold uppercase tracking-wide text-slate-500">Screenshot (optional)</span>
                <input name="attachment" type="file" accept="image/jpeg,image/png,image/webp" class="block w-full rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-600 file:mr-3 file:rounded-md file:border-0 file:bg-blue-50 file:px-3 file:py-1.5 file:font-semibold file:text-blue-700">
                <span class="mt-1 block text-[10px] text-slate-400">PNG, JPG, or WEBP · up to 5 MB</span>
            </label>
            <p id="chair-admin-report-feedback" class="hidden rounded-lg px-3 py-2 text-xs" role="status" aria-live="polite"></p>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-3">
            <button type="button" onclick="chairHeaderCloseAdminReport()" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-200">Cancel</button>
            <button id="chair-admin-report-submit" type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Send to Admin</button>
        </div>
    </form>
</div>

@php
    $chairHeaderNotificationEndpoints = [
        'index' => route('chair.notifications.index'),
        'unreadCount' => route('chair.notifications.unread-count'),
        'read' => route('chair.notifications.read', ['notification' => '__notification_id__']),
        'readAll' => route('chair.notifications.read-all'),
        'delete' => route('notifications.destroy', ['id' => '__notification_id__']),
        'reportAdmin' => route('chair.notifications.report-admin'),
    ];
@endphp
<script>
    let chairHeaderNotifOpen = false;
    const CHAIR_HEADER_NOTIFICATION_ENDPOINTS = @json($chairHeaderNotificationEndpoints);

    function chairHeaderOpenAdminReport() {
        const modal = document.getElementById('chair-admin-report-modal');
        if (!modal) return;
        document.getElementById('chair-admin-report-feedback')?.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.querySelector('select')?.focus();
    }

    function chairHeaderCloseAdminReport() {
        const modal = document.getElementById('chair-admin-report-modal');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    async function chairHeaderSubmitAdminReport(event) {
        event.preventDefault();
        const form = event.currentTarget;
        const button = document.getElementById('chair-admin-report-submit');
        const feedback = document.getElementById('chair-admin-report-feedback');
        const payload = new FormData(form);
        button.disabled = true;
        button.textContent = 'Sending…';
        feedback.classList.add('hidden');
        try {
            const result = await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.reportAdmin, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: payload,
            });
            feedback.textContent = result.message || 'Message sent to the system administrators.';
            feedback.className = 'rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-700';
            form.reset();
            window.setTimeout(chairHeaderCloseAdminReport, 1000);
        } catch (error) {
            feedback.textContent = error.message || 'Could not send your message. Please try again.';
            feedback.className = 'rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700';
        } finally {
            button.disabled = false;
            button.textContent = 'Send to Admin';
        }
    }

    function chairHeaderToggleNotifDropdown() {
        chairHeaderNotifOpen = !chairHeaderNotifOpen;
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = chairHeaderNotifOpen ? 'block' : 'none';
        if (chairHeaderNotifOpen) loadChairHeaderNotifications();
    }

    function chairHeaderOpenNotification(notification, item) {
        const modal = document.getElementById('chair-notification-viewer');
        if (!modal) return;
        document.getElementById('chair-notification-viewer-title').textContent = notification.notif_title || 'Notification';
        document.getElementById('chair-notification-viewer-type').textContent = (notification.notif_type || 'info').replaceAll('_', ' ');
        document.getElementById('chair-notification-viewer-time').textContent = chairHeaderFormatTime(notification.notif_created_at);
        document.getElementById('chair-notification-viewer-message').textContent = notification.notif_message || 'No message was included.';
        document.getElementById('chair-notification-viewer-error').classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        modal.querySelector('button')?.focus();
        if (!notification.notif_is_read) chairHeaderMarkRead(notification.notif_id, item);
    }

    function chairHeaderCloseNotification() {
        const modal = document.getElementById('chair-notification-viewer');
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    document.getElementById('chair-notification-viewer')?.addEventListener('click', event => {
        if (event.target.id === 'chair-notification-viewer') chairHeaderCloseNotification();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape') chairHeaderCloseNotification();
    });

    document.addEventListener('click', function(e) {
        const bell = document.getElementById('topbar-notif-bell');
        if (bell && !bell.contains(e.target)) {
            chairHeaderNotifOpen = false;
            const dd = document.getElementById('notif-dropdown');
            if (dd) dd.style.display = 'none';
        }
    });

    async function chairHeaderRequest(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Accept': 'application/json',
                ...(options.headers || {})
            },
        });
        let result;
        try {
            result = await response.json();
        } catch {
            throw new Error('The server returned an unexpected response.');
        }
        if (!response.ok) throw new Error(result.message || 'Unable to update notifications.');
        return result;
    }

    function chairHeaderShowMessage(message) {
        const list = document.getElementById('notif-list');
        if (!list) return;
        const item = document.createElement('div');
        item.className = 'p-4 text-sm text-slate-400 text-center';
        item.textContent = message;
        list.replaceChildren(item);
    }

    async function loadChairHeaderNotifications() {
        chairHeaderShowMessage('Loading notifications…');
        try {
            const notifications = await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.index);
            if (!Array.isArray(notifications)) throw new Error('The server returned invalid notifications.');
            renderChairHeaderNotifList(notifications);
        } catch (error) {
            chairHeaderShowMessage(error.message || 'Failed to load notifications.');
        }
    }

    function renderChairHeaderNotifList(notifications) {
        const list = document.getElementById('notif-list');
        if (!list) return;

        if (!notifications.length) {
            chairHeaderShowMessage('No notifications yet.');
            return;
        }

        const dotColors = {
            conflict: '#dc2626',
            overload: '#d97706',
            reminder: '#2563eb',
            approval: '#16a34a',
            returned: '#d97706',
            info: '#2563eb',
        };

        const fragment = document.createDocumentFragment();
        notifications.forEach(notification => {
            const item = document.createElement('div');
            item.className = `notif-drop-item${notification.notif_is_read ? '' : ' unread'}`;
            item.setAttribute('role', 'button');
            item.tabIndex = 0;
            item.setAttribute('aria-label', `Open notification: ${notification.notif_title || 'Notification'}`);
            item.addEventListener('click', () => chairHeaderOpenNotification(notification, item));
            item.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    chairHeaderOpenNotification(notification, item);
                }
            });

            const dot = document.createElement('div');
            dot.className = 'notif-drop-dot';
            dot.style.backgroundColor = dotColors[notification.notif_type] || '#94a3b8';

            const content = document.createElement('div');
            const text = document.createElement('div');
            text.className = 'notif-drop-text';
            const title = document.createElement('b');
            title.textContent = notification.notif_title || 'Notification';
            text.append(title, document.createTextNode(` — ${notification.notif_message || ''}`));
            const time = document.createElement('div');
            time.className = 'notif-drop-time';
            time.textContent = chairHeaderFormatTime(notification.notif_created_at);
            content.classList.add('min-w-0', 'flex-1');
            content.append(text, time);

            const deleteButton = document.createElement('button');
            deleteButton.type = 'button';
            deleteButton.className = 'ml-auto shrink-0 rounded-md px-2 py-1 text-slate-400 hover:bg-red-50 hover:text-red-600';
            deleteButton.setAttribute('aria-label', 'Delete notification');
            deleteButton.title = 'Delete notification';
            deleteButton.textContent = '×';
            deleteButton.addEventListener('click', event => {
                event.stopPropagation();
                chairHeaderDeleteNotification(notification.notif_id, item, deleteButton);
            });

            item.append(dot, content, deleteButton);
            fragment.appendChild(item);
        });
        list.replaceChildren(fragment);
    }

    function chairHeaderFormatTime(isoString) {
        const date = new Date(isoString);
        if (Number.isNaN(date.getTime())) return '';
        const now = new Date();
        const isToday = date.toDateString() === now.toDateString();
        const yesterday = new Date(now);
        yesterday.setDate(now.getDate() - 1);
        const isYesterday = date.toDateString() === yesterday.toDateString();
        const timeStr = date.toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit'
        });
        if (isToday) return `Today, ${timeStr}`;
        if (isYesterday) return `Yesterday, ${timeStr}`;
        return date.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        }) + ', ' + timeStr;
    }

    async function chairHeaderMarkRead(id, el) {
        if (!el) return;
        if (!el.classList.contains('unread')) return;

        try {
            const url = CHAIR_HEADER_NOTIFICATION_ENDPOINTS.read.replace('__notification_id__', encodeURIComponent(
                id));
            await chairHeaderRequest(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
            });
            el.classList.remove('unread');
            updateChairHeaderNotifCount();
        } catch (error) {
            const feedback = document.getElementById('chair-notification-viewer-error');
            if (feedback) {
                feedback.textContent = error.message || 'Unable to mark this notification as read.';
                feedback.classList.remove('hidden');
            }
        }
    }

    async function chairHeaderMarkAllRead() {
        try {
            await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.readAll, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
            });
            document.querySelectorAll('#notif-list .notif-drop-item.unread').forEach(el => {
                el.classList.remove('unread');
            });
            updateChairHeaderNotifCount();
        } catch (error) {
            chairHeaderShowMessage(error.message || 'Unable to mark notifications as read.');
        }
    }

    async function chairHeaderDeleteNotification(id, item, button) {
        button.disabled = true;
        try {
            const url = CHAIR_HEADER_NOTIFICATION_ENDPOINTS.delete.replace('__notification_id__', encodeURIComponent(id));
            await chairHeaderRequest(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
            });
            item.remove();
            if (!document.querySelector('#notif-list .notif-drop-item')) chairHeaderShowMessage('No notifications yet.');
            updateChairHeaderNotifCount();
        } catch (error) {
            button.disabled = false;
            console.error('Unable to delete chair notification:', error);
        }
    }

    async function updateChairHeaderNotifCount() {
        try {
            const result = await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.unreadCount);
            const unread = Number(result.count);
            if (!Number.isInteger(unread) || unread < 0) throw new Error(
                'The server returned an invalid notification count.');
            const badge = document.getElementById('notif-count');
            if (badge) {
                badge.textContent = unread;
                badge.style.display = unread > 0 ? 'inline' : 'none';
            }
        } catch (error) {
            console.error('Unable to refresh chair notification count:', error);
        }
    }

    updateChairHeaderNotifCount();
    setInterval(updateChairHeaderNotifCount, 30000);
</script>
