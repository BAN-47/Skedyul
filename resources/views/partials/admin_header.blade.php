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
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                <span class="text-sm font-bold text-slate-900">Notifications</span>
                <button type="button" onclick="openAdminAnnouncementModal()" class="rounded-lg bg-blue-600 px-3 py-2 text-[11px] font-bold text-white transition hover:bg-blue-700">Send announcement</button>
            </div>
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

<div id="admin-notification-viewer" class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="admin-notification-viewer-title" onclick="if(event.target===this) closeAdminNotificationViewer()">
    <div class="w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div class="min-w-0">
                <p id="admin-notification-viewer-type" class="mb-1 text-[10px] font-bold uppercase tracking-wide text-blue-600"></p>
                <h2 id="admin-notification-viewer-title" class="text-base font-bold text-slate-900">Notification</h2>
                <p id="admin-notification-viewer-time" class="mt-1 text-xs text-slate-400"></p>
            </div>
            <button type="button" onclick="closeAdminNotificationViewer()" class="rounded-lg px-2 py-1 text-xl leading-none text-slate-400 hover:bg-slate-100" aria-label="Close notification">×</button>
        </div>
        <div id="admin-notification-viewer-message" class="max-h-[45vh] overflow-y-auto whitespace-pre-wrap break-words px-5 py-5 text-sm leading-6 text-slate-700"></div>
        <div id="admin-notification-attachment-wrap" class="hidden border-t border-slate-100 px-5 py-4">
            <p class="mb-2 text-xs font-semibold text-slate-500">Attached screenshot <span class="font-normal">· click to zoom</span></p>
            <button type="button" onclick="openAdminNotificationImage()" class="block max-h-72 max-w-full overflow-hidden rounded-lg border border-slate-200 bg-slate-50">
                <img id="admin-notification-attachment" alt="Screenshot attached to this notification" class="max-h-72 max-w-full object-contain">
            </button>
        </div>
        <div class="flex justify-end border-t border-slate-100 px-5 py-3">
            <button type="button" onclick="closeAdminNotificationViewer()" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Done</button>
        </div>
    </div>
</div>

<div id="admin-notification-image-zoom" class="fixed inset-0 z-[1210] hidden items-center justify-center bg-slate-950/90 p-4" role="dialog" aria-modal="true" aria-label="Zoomed notification screenshot" onclick="if(event.target===this) closeAdminNotificationImage()">
    <button type="button" onclick="closeAdminNotificationImage()" class="absolute right-5 top-4 rounded-full bg-white/15 px-3 py-1 text-2xl text-white hover:bg-white/25" aria-label="Close image">×</button>
    <img id="admin-notification-zoom-image" alt="Zoomed screenshot attached to notification" class="max-h-[92vh] max-w-[96vw] rounded-lg object-contain shadow-2xl">
</div>

<script>
let notifOpen = false;
const adminNotificationRecords = new Map();
const adminAnnouncementUrl = @json(route('admin.notifications.announcement'));
const adminCsrfToken = @json(csrf_token());
const adminNotificationDeleteUrl = @json(route('notifications.destroy', ['id' => '__notification_id__']));
const adminNotificationReadUrl = @json(route('notifications.read', ['id' => '__notification_id__']));

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
    adminNotificationRecords.clear();
    notifications.forEach(notification => adminNotificationRecords.set(String(notification.notif_id), notification));

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
            <div class="min-w-0 flex-1"><div class="notif-drop-text"><b>${escapeNotificationHtml(n.notif_title)}</b> — ${formatAdminNotificationMessage(n.notif_message)}</div><div class="notif-drop-time">${formatNotifTime(n.notif_created_at)}</div></div>
            <button type="button" class="ml-auto shrink-0 rounded-md px-2 py-1 text-slate-400 hover:bg-red-50 hover:text-red-600" aria-label="Delete notification" title="Delete notification" onclick="event.stopPropagation();deleteAdminNotification('${escapeNotificationHtml(n.notif_id)}', this)">×</button>
        </div>`).join('');
}

function escapeNotificationHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
    })[character]);
}

function formatAdminNotificationMessage(value) {
    const message = String(value ?? '');
    const match = message.match(/(?:\r?\n)?Screenshot:\s*(https?:\/\/[^\s]+|\/admin\/notifications\/attachments\/[^\s]+)/i);
    if (!match) return escapeNotificationHtml(message);

    let attachmentPath = '';
    try {
        const parsed = new URL(match[1], window.location.origin);
        if (parsed.origin === window.location.origin && /^\/admin\/notifications\/attachments\/[0-9a-f-]{36}\.(?:jpe?g|png|webp)$/i.test(parsed.pathname)) {
            attachmentPath = parsed.pathname;
        }
    } catch {}
    const messageText = message.replace(match[0], '').trimEnd();
    const link = attachmentPath
        ? `<a href="${escapeNotificationHtml(attachmentPath)}" target="_blank" rel="noopener" onclick="event.stopPropagation()" class="mt-1 inline-block font-semibold text-blue-600 underline">View attached screenshot</a>`
        : '';
    return `${escapeNotificationHtml(messageText).replace(/\n/g, '<br>')}${link ? `<br>${link}` : ''}`;
}

function openAdminNotification(id, type) {
    const notification = adminNotificationRecords.get(String(id));
    const isApproval = ['new_user', 'approval'].includes(type) || /pending.*approv|approv.*pending/i.test(notification?.notif_title || '');
    if (isApproval) {
        const readUrl = adminNotificationReadUrl.replace('__notification_id__', encodeURIComponent(id));
        fetch(readUrl, {
            method: 'PUT',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': adminCsrfToken },
        }).finally(() => window.location.assign(@json(route('admin.pending-accounts'))));
        return;
    }

    if (notification) showAdminNotificationViewer(notification);
    const readUrl = adminNotificationReadUrl.replace('__notification_id__', encodeURIComponent(id));
    const request = fetch(readUrl, {
        method: 'PUT',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': adminCsrfToken,
        },
    }).catch(() => null);

    request.finally(() => {
        loadNotifications();
        updateNotifCount();
    });
}

function adminNotificationAttachment(message) {
    const match = String(message ?? '').match(/(?:\r?\n)?Screenshot:\s*(https?:\/\/[^\s]+|\/admin\/notifications\/attachments\/[^\s]+)/i);
    if (!match) return { message: String(message ?? ''), url: '' };
    try {
        const parsed = new URL(match[1], window.location.origin);
        if (parsed.origin !== window.location.origin || !/^\/admin\/notifications\/attachments\/[0-9a-f-]{36}\.(?:jpe?g|png|webp)$/i.test(parsed.pathname)) {
            return { message: String(message ?? '').replace(match[0], '').trimEnd(), url: '' };
        }
        return { message: String(message ?? '').replace(match[0], '').trimEnd(), url: parsed.pathname };
    } catch {
        return { message: String(message ?? '').replace(match[0], '').trimEnd(), url: '' };
    }
}

function showAdminNotificationViewer(notification) {
    const modal = document.getElementById('admin-notification-viewer');
    const attachment = adminNotificationAttachment(notification.notif_message);
    document.getElementById('admin-notification-viewer-title').textContent = notification.notif_title || 'Notification';
    document.getElementById('admin-notification-viewer-type').textContent = String(notification.notif_type || 'info').replaceAll('_', ' ');
    document.getElementById('admin-notification-viewer-time').textContent = formatNotifTime(notification.notif_created_at);
    document.getElementById('admin-notification-viewer-message').textContent = attachment.message || 'No message was included.';
    const wrap = document.getElementById('admin-notification-attachment-wrap');
    const image = document.getElementById('admin-notification-attachment');
    if (attachment.url) {
        image.src = attachment.url;
        wrap.classList.remove('hidden');
    } else {
        image.removeAttribute('src');
        wrap.classList.add('hidden');
    }
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeAdminNotificationViewer() {
    const modal = document.getElementById('admin-notification-viewer');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function openAdminNotificationImage() {
    const source = document.getElementById('admin-notification-attachment').src;
    if (!source) return;
    document.getElementById('admin-notification-zoom-image').src = source;
    const zoom = document.getElementById('admin-notification-image-zoom');
    zoom.classList.remove('hidden');
    zoom.classList.add('flex');
}

function closeAdminNotificationImage() {
    const zoom = document.getElementById('admin-notification-image-zoom');
    zoom.classList.add('hidden');
    zoom.classList.remove('flex');
}

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') {
        closeAdminNotificationImage();
        closeAdminNotificationViewer();
    }
});

async function deleteAdminNotification(id, button) {
    button.disabled = true;
    const url = adminNotificationDeleteUrl.replace('__notification_id__', encodeURIComponent(id));
    try {
        const response = await fetch(url, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': adminCsrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
        });
        const data = await response.json().catch(() => ({}));
        if (!response.ok || !data.success) throw new Error(data.message || 'Could not delete this notification.');
        button.closest('.notif-drop-item')?.remove();
        if (!document.querySelector('#notif-list .notif-drop-item')) {
            document.getElementById('notif-list').innerHTML = '<div class="p-4 text-sm text-slate-400 text-center">No notifications yet.</div>';
        }
        updateNotifCount();
    } catch (error) {
        button.disabled = false;
        console.error('Unable to delete admin notification:', error);
    }
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
