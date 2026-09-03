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
const ADMIN_NOTIFS = [
    { dot:'#dc2626', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
    { dot:'#d97706', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
    { dot:'#2563eb', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
    { dot:'#16a34a', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
    { dot:'#2563eb', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
];

function renderNotifList() {
    const list = document.getElementById('notif-list');
    if (!list) return;
    list.innerHTML = ADMIN_NOTIFS.map((n) => `
        <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="markRead(this)">
            <div class="notif-drop-dot" style="background:${n.dot};"></div>
            <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
        </div>`).join('');
    updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
    notifOpen = !notifOpen;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = notifOpen ? 'block' : 'none';
}
document.addEventListener('click', e => {
    const bell = document.getElementById('topbar-notif-bell');
    if (bell && !bell.contains(e.target)) {
        notifOpen = false;
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = 'none';
    }
});
function markRead(el) {
    el.classList.remove('unread');
    updateNotifCount();
}
function markAllRead() {
    document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
    updateNotifCount();
}
function updateNotifCount() {
    const unread = document.querySelectorAll('.notif-drop-item.unread').length;
    const badge = document.getElementById('notif-count');
    if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline' : 'none'; }
}
renderNotifList();
</script>