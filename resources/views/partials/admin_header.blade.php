@php
    $adminAppHost = (string) (parse_url((string) config('app.url'), PHP_URL_HOST) ?? '');
    $adminOnLaravelCloud = str_contains(strtolower($adminAppHost), 'laravel.cloud');
@endphp
<div class="topbar admin-topbar">
    <div class="topbar-title admin-topbar-heading">
        <div class="admin-topbar-title-row">
            <span>{{ $title ?? 'SKEDYUL' }}</span>
            @if($adminOnLaravelCloud)
                <span class="admin-cloud-pill"><span class="admin-cloud-dot"></span> Laravel Cloud</span>
            @endif
        </div>
        <span class="admin-topbar-subtitle">{{ request()->routeIs('admin.dashboard') ? 'Academic Scheduling & Resource Analytics' : 'SKEDYUL Academic Core' }}</span>
    </div>
    <div class="admin-topbar-actions">
        <button type="button" onclick="openAdminAnnouncementModal()"
            class="admin-notification-button flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-[13px] font-semibold">
            Send announcement
        </button>
        @if(isset($semester) || isset($academicYear))
            <div class="admin-term-pill">
                <span class="admin-term-dot"></span>
                <span>{{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label ?? 'Academic year not set' }} · {{ $semester->sem_name ?? 'Semester not set' }}</span>
            </div>
        @endif
    <div id="topbar-notif-bell" class="relative">
        <button type="button" onclick="toggleNotifDropdown()"
            class="admin-notification-button flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
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
</div>

<div class="modal-overlay" id="admin-announcement-modal" role="dialog" aria-modal="true" aria-labelledby="admin-announcement-title">
    <div class="modal-box" style="width:560px;">
        <div class="modal-header">
            <div class="modal-title" id="admin-announcement-title">Send system announcement</div>
            <button type="button" class="modal-close" onclick="closeAdminAnnouncementModal()" aria-label="Close">✕</button>
        </div>
        <div class="mb-3">
            <label class="field-label" for="admin-announcement-audience">Recipients</label>
            <select id="admin-announcement-audience" class="field-input">
                <option value="all">All active users</option>
                <option value="deans">All Deans</option>
                <option value="chairs">All Department Chairs</option>
                <option value="faculty">All Faculty</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="field-label" for="admin-announcement-type">Announcement type</label>
            <select id="admin-announcement-type" class="field-input">
                <option value="info">System update / announcement</option>
                <option value="urgent">Warning / urgent issue</option>
                <option value="deadline">Maintenance notice</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="field-label" for="admin-announcement-title-input">Title</label>
            <input id="admin-announcement-title-input" class="field-input" maxlength="200" placeholder="e.g. Scheduled system maintenance">
        </div>
        <div class="mb-2">
            <label class="field-label" for="admin-announcement-message">Message</label>
            <textarea id="admin-announcement-message" class="field-input" rows="4" maxlength="2000" style="resize:vertical;" placeholder="Write the update or warning for users..."></textarea>
        </div>
        <div id="admin-announcement-feedback" class="min-h-5 text-[12px]" aria-live="polite"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeAdminAnnouncementModal()">Cancel</button>
            <button type="button" id="admin-announcement-send" class="btn btn-primary" onclick="sendAdminAnnouncement()">Send announcement</button>
        </div>
    </div>
</div>

<script>
let notifOpen = false;
const adminAnnouncementUrl = @json(route('admin.notifications.announcement'));
const adminCsrfToken = @json(csrf_token());

function openAdminAnnouncementModal() {
    document.getElementById('notif-dropdown').style.display = 'none';
    notifOpen = false;
    document.getElementById('admin-announcement-feedback').textContent = '';
    document.getElementById('admin-announcement-modal').classList.add('open');
}

function closeAdminAnnouncementModal() {
    document.getElementById('admin-announcement-modal').classList.remove('open');
}

document.getElementById('admin-announcement-modal').addEventListener('click', event => {
    if (event.target.id === 'admin-announcement-modal') closeAdminAnnouncementModal();
});

async function sendAdminAnnouncement() {
    const button = document.getElementById('admin-announcement-send');
    const feedback = document.getElementById('admin-announcement-feedback');
    const title = document.getElementById('admin-announcement-title-input').value.trim();
    const message = document.getElementById('admin-announcement-message').value.trim();
    if (!title || !message) {
        feedback.className = 'min-h-5 text-[12px] text-red-600';
        feedback.textContent = 'Enter both a title and a message.';
        return;
    }

    button.disabled = true;
    button.textContent = 'Sending…';
    feedback.textContent = '';
    try {
        const response = await fetch(adminAnnouncementUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': adminCsrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                audience: document.getElementById('admin-announcement-audience').value,
                type: document.getElementById('admin-announcement-type').value,
                title,
                message,
            }),
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || `Announcement failed (${response.status}).`);

        feedback.className = 'min-h-5 text-[12px] font-semibold text-emerald-700';
        feedback.textContent = `Sent to ${data.sent} active user(s).`;
        document.getElementById('admin-announcement-title-input').value = '';
        document.getElementById('admin-announcement-message').value = '';
        updateNotifCount();
    } catch (error) {
        feedback.className = 'min-h-5 text-[12px] text-red-600';
        feedback.textContent = error.message || 'Could not send the announcement. Try again.';
    } finally {
        button.disabled = false;
        button.textContent = 'Send announcement';
    }
}

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
        <div class="notif-drop-item ${!n.notif_is_read ? 'unread' : ''}" role="button" tabindex="0" onclick="openAdminNotification('${escapeNotificationHtml(n.notif_id)}', '${escapeNotificationHtml(n.notif_type)}')" onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();openAdminNotification('${escapeNotificationHtml(n.notif_id)}', '${escapeNotificationHtml(n.notif_type)}')}" style="cursor:pointer">
            <div class="notif-drop-dot" style="background:${dotColors[n.notif_type] || '#94a3b8'};"></div>
            <div><div class="notif-drop-text"><b>${escapeNotificationHtml(n.notif_title)}</b> — ${escapeNotificationHtml(n.notif_message)}</div><div class="notif-drop-time">${formatNotifTime(n.notif_created_at)}</div></div>
        </div>`).join('');
}

function escapeNotificationHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);
}

function openAdminNotification(id, type) {
    const request = fetch(`/notifications/${id}/read`, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': adminCsrfToken,
        },
    }).catch(() => null);

    if (type === 'new_user') {
        request.finally(() => window.location.assign('{{ route("admin.pending-accounts") }}'));
        return;
    }

    request.finally(() => {
        loadNotifications();
        updateNotifCount();
    });
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

async function markRead(id, el) {
    if (!el.classList.contains('unread')) return;

    try {
        const response = await fetch(`/notifications/${encodeURIComponent(id)}/read`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': adminCsrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Could not mark this notification as read.');
        el.classList.remove('unread');
        updateNotifCount();
    } catch (error) {
        console.error('Unable to mark notification as read:', error);
    }
}

async function markAllRead() {
    try {
        const response = await fetch('{{ route("notifications.read-all") }}', {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': adminCsrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Could not mark notifications as read.');

        document.querySelectorAll('#notif-list .notif-drop-item.unread').forEach(item => item.classList.remove('unread'));
        updateNotifCount();
    } catch (error) {
        console.error('Unable to mark all notifications as read:', error);
        const list = document.getElementById('notif-list');
        if (list) {
            const notice = document.createElement('div');
            notice.className = 'px-4 py-2 text-xs text-red-600';
            notice.textContent = error.message || 'Could not mark notifications as read. Try again.';
            list.prepend(notice);
        }
    }
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
