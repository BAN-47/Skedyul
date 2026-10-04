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

<script>
let chairHeaderNotifOpen = false;

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

function loadChairHeaderNotifications() {
    fetch('{{ route("chair.notifications.index") }}', {
        headers: { 'Accept': 'application/json' },
    })
        .then(res => res.json())
        .then(notifications => renderChairHeaderNotifList(notifications))
        .catch(() => {
            const list = document.getElementById('notif-list');
            if (list) list.innerHTML = '<div class="p-4 text-sm text-slate-400 text-center">Failed to load notifications.</div>';
        });
}

function renderChairHeaderNotifList(notifications) {
    const list = document.getElementById('notif-list');
    if (!list) return;

    if (!notifications.length) {
        list.innerHTML = '<div class="p-4 text-sm text-slate-400 text-center">No notifications yet.</div>';
        return;
    }

    const dotColors = {
        conflict: '#dc2626',
        overload: '#d97706',
        reminder: '#2563eb',
        info: '#2563eb',
    };

    list.innerHTML = notifications.map(n => `
        <div class="notif-drop-item ${!n.notif_is_read ? 'unread' : ''}" onclick="chairHeaderMarkRead('${n.notif_id}', this)">
            <div class="notif-drop-dot" style="background:${dotColors[n.notif_type] || '#94a3b8'};"></div>
            <div><div class="notif-drop-text"><b>${n.notif_title}</b> — ${n.notif_message}</div><div class="notif-drop-time">${chairHeaderFormatTime(n.notif_created_at)}</div></div>
        </div>`).join('');
}

function chairHeaderFormatTime(isoString) {
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

function chairHeaderMarkRead(id, el) {
    if (!el.classList.contains('unread')) return;

    fetch(`/chair/notifications/${id}/read`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                el.classList.remove('unread');
                updateChairHeaderNotifCount();
            }
        });
}

function chairHeaderMarkAllRead() {
    fetch('{{ route("chair.notifications.read-all") }}', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
                updateChairHeaderNotifCount();
            }
        });
}

function updateChairHeaderNotifCount() {
    fetch('{{ route("chair.notifications.index") }}', {
        headers: { 'Accept': 'application/json' },
    })
        .then(res => res.json())
        .then(notifications => {
            const unread = notifications.filter(n => !n.notif_is_read).length;
            const badge = document.getElementById('notif-count');
            if (badge) {
                badge.textContent = unread;
                badge.style.display = unread > 0 ? 'inline' : 'none';
            }
        });
}

updateChairHeaderNotifCount();
setInterval(updateChairHeaderNotifCount, 30000);
</script>
