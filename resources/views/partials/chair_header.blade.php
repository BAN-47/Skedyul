<div class="topbar">
    <div class="topbar-title">{{ $title ?? 'Department Chair Dashboard' }}</div>
    <div class="flex items-center gap-2.5">
        @if(!empty($badgeText))
            <span class="badge badge-blue text-[11px]">{{ $badgeText }}</span>
        @endif
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

@php
    $chairHeaderNotificationEndpoints = [
        'index' => route('chair.notifications.index'),
        'unreadCount' => route('chair.notifications.unread-count'),
        'read' => route('chair.notifications.read', ['notification' => '__notification_id__']),
        'readAll' => route('chair.notifications.read-all'),
    ];
@endphp
<script>
let chairHeaderNotifOpen = false;
const CHAIR_HEADER_NOTIFICATION_ENDPOINTS = @json($chairHeaderNotificationEndpoints);

function chairHeaderToggleNotifDropdown() {
    chairHeaderNotifOpen = !chairHeaderNotifOpen;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = chairHeaderNotifOpen ? 'block' : 'none';
    if (chairHeaderNotifOpen) loadChairHeaderNotifications();
}

document.addEventListener('click', function (e) {
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
        headers: { 'Accept': 'application/json', ...(options.headers || {}) },
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
        info: '#2563eb',
    };

    const fragment = document.createDocumentFragment();
    notifications.forEach(notification => {
        const item = document.createElement('div');
        item.className = `notif-drop-item${notification.notif_is_read ? '' : ' unread'}`;
        if (!notification.notif_is_read) {
            item.setAttribute('role', 'button');
            item.tabIndex = 0;
            item.addEventListener('click', () => chairHeaderMarkRead(notification.notif_id, item));
            item.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    chairHeaderMarkRead(notification.notif_id, item);
                }
            });
        }

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
        content.append(text, time);
        item.append(dot, content);
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
    const timeStr = date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit' });
    if (isToday) return `Today, ${timeStr}`;
    if (isYesterday) return `Yesterday, ${timeStr}`;
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ', ' + timeStr;
}

async function chairHeaderMarkRead(id, el) {
    if (!el) return;
    if (!el.classList.contains('unread')) return;

    try {
        const url = CHAIR_HEADER_NOTIFICATION_ENDPOINTS.read.replace('__notification_id__', encodeURIComponent(id));
        await chairHeaderRequest(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        el.classList.remove('unread');
        el.removeAttribute('role');
        el.removeAttribute('tabindex');
        updateChairHeaderNotifCount();
    } catch (error) {
        chairHeaderShowMessage(error.message || 'Unable to mark this notification as read.');
    }
}

async function chairHeaderMarkAllRead() {
    try {
        await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.readAll, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        document.querySelectorAll('#notif-list .notif-drop-item.unread').forEach(el => {
            el.classList.remove('unread');
            el.removeAttribute('role');
            el.removeAttribute('tabindex');
        });
        updateChairHeaderNotifCount();
    } catch (error) {
        chairHeaderShowMessage(error.message || 'Unable to mark notifications as read.');
    }
}

async function updateChairHeaderNotifCount() {
    try {
        const result = await chairHeaderRequest(CHAIR_HEADER_NOTIFICATION_ENDPOINTS.unreadCount);
        const unread = Number(result.count);
        if (!Number.isInteger(unread) || unread < 0) throw new Error('The server returned an invalid notification count.');
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
