<div class="topbar">
    <div class="topbar-title">{{ $title ?? 'My Subjects' }}</div>
    <div class="flex items-center gap-2.5">
        @if(!empty($badgeText))
            <span class="badge badge-blue text-[11px]">{{ $badgeText }}</span>
        @endif
        <div id="topbar-notif-bell" class="relative">
            <button type="button" onclick="facultyHeaderToggleNotifDropdown()"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
                Notifications
                <span id="notif-count" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    0
                </span>
            </button>
            <div id="notif-dropdown" style="display:none;"
                class="absolute top-11 right-0 w-[340px] bg-white border border-slate-200 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] z-[100] overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-200 text-sm font-bold text-slate-900">Notifications</div>
                <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
                <div class="px-4 py-2.5 border-t border-slate-200 text-center">
                    <button type="button" onclick="facultyHeaderMarkAllRead()"
                        class="text-[12px] text-blue-600 font-semibold bg-transparent border-none cursor-pointer">
                        Mark all as read
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@php
    $facultyHeaderNotificationEndpoints = [
        'index' => route('notifications.index'),
        'unreadCount' => route('notifications.unread-count'),
        'read' => route('notifications.read', ['id' => '__notification_id__']),
        'readAll' => route('notifications.read-all'),
    ];
@endphp
<script>
const FACULTY_HEADER_NOTIFICATION_ENDPOINTS = @json($facultyHeaderNotificationEndpoints);
let facultyHeaderNotifOpen = false;

async function facultyHeaderRequest(url, options = {}) {
    const response = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            ...(options.headers || {}),
        },
    });
    let result;
    try {
        result = await response.json();
    } catch {
        throw new Error('The server returned an unexpected response.');
    }
    if (!response.ok) {
        throw new Error(result.message || 'Unable to update notifications.');
    }
    return result;
}

function facultyHeaderShowNotifMessage(message) {
    const list = document.getElementById('notif-list');
    if (!list) return;
    const item = document.createElement('div');
    item.className = 'px-4 py-6 text-center text-xs text-slate-400';
    item.textContent = message;
    list.replaceChildren(item);
}

function facultyHeaderFormatNotifTime(value) {
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return '';

    const now = new Date();
    const today = date.toDateString() === now.toDateString();
    const yesterday = new Date(now);
    yesterday.setDate(now.getDate() - 1);
    const time = date.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
    if (today) return `Today, ${time}`;
    if (date.toDateString() === yesterday.toDateString()) return `Yesterday, ${time}`;
    return `${date.toLocaleDateString([], { month: 'short', day: 'numeric' })}, ${time}`;
}

function renderFacultyHeaderNotifList(notifications) {
    const list = document.getElementById('notif-list');
    if (!list) return;
    if (!notifications.length) {
        facultyHeaderShowNotifMessage('No notifications yet.');
        return;
    }

    const dotColors = {
        conflict: '#dc2626',
        overload: '#d97706',
        reminder: '#2563eb',
        info: '#2563eb',
        assignment: '#2563eb',
        approval: '#16a34a',
    };
    const fragment = document.createDocumentFragment();

    notifications.forEach(notification => {
        const item = document.createElement('div');
        item.className = `notif-drop-item${notification.notif_is_read ? '' : ' unread'}`;
        if (!notification.notif_is_read) {
            item.setAttribute('role', 'button');
            item.tabIndex = 0;
            item.addEventListener('click', () => facultyHeaderMarkRead(notification.notif_id, item));
            item.addEventListener('keydown', event => {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    facultyHeaderMarkRead(notification.notif_id, item);
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
        time.textContent = facultyHeaderFormatNotifTime(notification.notif_created_at);

        content.append(text, time);
        item.append(dot, content);
        fragment.appendChild(item);
    });
    list.replaceChildren(fragment);
}

async function loadFacultyHeaderNotifications() {
    facultyHeaderShowNotifMessage('Loading notifications…');
    try {
        const notifications = await facultyHeaderRequest(FACULTY_HEADER_NOTIFICATION_ENDPOINTS.index);
        if (!Array.isArray(notifications)) throw new Error('The server returned invalid notifications.');
        renderFacultyHeaderNotifList(notifications);
    } catch (error) {
        facultyHeaderShowNotifMessage(error.message || 'Failed to load notifications.');
    }
}

async function facultyHeaderToggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    if (!dd) return;
    facultyHeaderNotifOpen = !facultyHeaderNotifOpen;
    dd.style.display = facultyHeaderNotifOpen ? 'block' : 'none';
    if (facultyHeaderNotifOpen) await loadFacultyHeaderNotifications();
}

document.addEventListener('click', function (e) {
    const bell = document.getElementById('topbar-notif-bell');
    if (bell && !bell.contains(e.target)) {
        facultyHeaderNotifOpen = false;
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = 'none';
    }
});

async function facultyHeaderMarkRead(id, el) {
    if (!id || !el || !el.classList.contains('unread')) return;
    try {
        const url = FACULTY_HEADER_NOTIFICATION_ENDPOINTS.read.replace('__notification_id__', encodeURIComponent(id));
        await facultyHeaderRequest(url, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        el.classList.remove('unread');
        el.removeAttribute('role');
        el.removeAttribute('tabindex');
        updateFacultyHeaderNotifCount();
    } catch (error) {
        facultyHeaderShowNotifMessage(error.message || 'Unable to mark this notification as read.');
    }
}

async function facultyHeaderMarkAllRead() {
    try {
        await facultyHeaderRequest(FACULTY_HEADER_NOTIFICATION_ENDPOINTS.readAll, {
            method: 'PUT',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        document.querySelectorAll('#notif-list .notif-drop-item.unread').forEach(el => {
            el.classList.remove('unread');
            el.removeAttribute('role');
            el.removeAttribute('tabindex');
        });
        updateFacultyHeaderNotifCount();
    } catch (error) {
        facultyHeaderShowNotifMessage(error.message || 'Unable to mark notifications as read.');
    }
}

async function updateFacultyHeaderNotifCount() {
    try {
        const result = await facultyHeaderRequest(FACULTY_HEADER_NOTIFICATION_ENDPOINTS.unreadCount);
        const count = Number(result.count);
        if (!Number.isInteger(count) || count < 0) throw new Error('The server returned an invalid notification count.');
        const badge = document.getElementById('notif-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'inline' : 'none';
        }
    } catch (error) {
        console.error('Unable to refresh faculty notification count:', error);
    }
}

updateFacultyHeaderNotifCount();
setInterval(updateFacultyHeaderNotifCount, 30000);
</script>