<div class="topbar">
    <div class="topbar-title">{{ $title ?? 'SKEDYUL' }}</div>
    <div id="topbar-notif-bell" class="relative">
        <button type="button" onclick="toggleNotifDropdown()"
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
                <button type="button" onclick="markAllRead()"
                    class="text-[12px] text-blue-600 font-semibold bg-transparent border-none cursor-pointer">
                    Mark all as read
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let notifOpen = false;

function toggleNotifDropdown() {
    notifOpen = !notifOpen;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = notifOpen ? 'block' : 'none';
    if (notifOpen) loadNotifications();
}

document.addEventListener('click', e => {
    const bell = document.getElementById('topbar-notif-bell');
    if (bell && !bell.contains(e.target)) {
        notifOpen = false;
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = 'none';
    }
});

function loadNotifications() {
    fetch('{{ route("notifications.index") }}', {
        headers: { 'Accept': 'application/json' },
    })
        .then(res => res.json())
        .then(notifications => renderNotifList(notifications))
        .catch(() => {
            const list = document.getElementById('notif-list');
            if (list) list.innerHTML = '<div class="p-4 text-sm text-slate-400 text-center">Failed to load notifications.</div>';
        });
}

function renderNotifList(notifications) {
    const list = document.getElementById('notif-list');
    if (!list) return;

    if (!notifications.length) {
        list.innerHTML = '<div class="p-4 text-sm text-slate-400 text-center">No notifications yet.</div>';
        return;
    }

    const dotColors = {
        conflict: '#dc2626',
        overload: '#d97706',
        new_user: '#2563eb',
        backup: '#16a34a',
        info: '#2563eb',
    };

    list.innerHTML = notifications.map(n => `
        <div class="notif-drop-item ${!n.notif_is_read ? 'unread' : ''}" onclick="markRead('${n.notif_id}', this)">
            <div class="notif-drop-dot" style="background:${dotColors[n.notif_type] || '#94a3b8'};"></div>
            <div><div class="notif-drop-text"><b>${n.notif_title}</b> — ${n.notif_message}</div><div class="notif-drop-time">${formatNotifTime(n.notif_created_at)}</div></div>
        </div>`).join('');
}

function formatNotifTime(isoString) {
    const date = new Date(isoString);
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

function markRead(id, el) {
    if (!el.classList.contains('unread')) return;

    fetch(`/notifications/${id}/read`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                el.classList.remove('unread');
                updateNotifCount();
            }
        });
}

function markAllRead() {
    fetch('{{ route("notifications.read-all") }}', {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
                updateNotifCount();
            }
        });
}

function updateNotifCount() {
    fetch('{{ route("notifications.unread-count") }}', {
        headers: { 'Accept': 'application/json' },
    })
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notif-count');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline' : 'none';
            }
        });
}

updateNotifCount();
setInterval(updateNotifCount, 30000);
</script>