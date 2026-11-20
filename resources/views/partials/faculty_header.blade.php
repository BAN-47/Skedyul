<div id="faculty-mobile-nav-backdrop" class="faculty-mobile-nav-backdrop" onclick="closeFacultyMobileNav()" aria-hidden="true"></div>
<div class="topbar">
    <button type="button" class="faculty-mobile-menu-button" onclick="toggleFacultyMobileNav()" aria-label="Open navigation menu" aria-expanded="false" aria-controls="faculty-sidebar">
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
    </button>
    <div class="topbar-title flex items-center gap-2.5">
        <span>{{ $title ?? 'My Subjects' }}</span>
        @if(!empty($activeSemester))
            <span class="faculty-term-active hidden sm:inline-flex"><span aria-hidden="true"></span> Term Active</span>
        @endif
    </div>
    <div class="flex items-center gap-2.5">
        @if(!empty($semesterLabel))
            <span class="faculty-term-pill" title="{{ $semesterLabel }}">
                <i class="ti ti-calendar-event" aria-hidden="true"></i>
                <span>{{ $semesterLabel }}</span>
            </span>
        @endif
        @if(!empty($badgeText))
            <span class="badge badge-blue text-[11px]">{{ $badgeText }}</span>
        @endif
        <div id="topbar-notif-bell" class="relative">
            <button type="button" onclick="facultyHeaderToggleNotifDropdown()"
                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
                <span class="faculty-notification-label">Notifications</span>
                <span id="notif-count" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                    0
                </span>
            </button>
            <div id="notif-dropdown" style="display:none;"
                class="absolute top-11 right-0 w-[340px] bg-white border border-slate-200 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] z-[100] overflow-hidden">
                <div class="px-4 py-3.5 border-b border-slate-200 text-sm font-bold text-slate-900">Notifications</div>
                <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
                <div class="px-3 py-2 border-t border-slate-200">
                    <button type="button" onclick="facultyHeaderOpenSuggestion()"
                        class="w-full rounded-lg bg-blue-600 px-3 py-2 text-xs font-bold text-white hover:bg-blue-700">
                        Suggest a course or schedule
                    </button>
                </div>
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

<div id="faculty-chair-suggestion-modal" class="fixed inset-0 z-[1200] hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="faculty-chair-suggestion-title">
    <form id="faculty-chair-suggestion-form" class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl" onsubmit="facultyHeaderSubmitSuggestion(event)">
        <div class="mb-4 flex items-center justify-between gap-3">
            <div>
                <h2 id="faculty-chair-suggestion-title" class="text-base font-extrabold text-slate-900">Send a suggestion to your chair</h2>
                <p class="mt-1 text-xs text-slate-500">Only the chair assigned to your department will receive it.</p>
            </div>
            <button type="button" onclick="facultyHeaderCloseSuggestion()" class="rounded-lg px-2 py-1 text-xl text-slate-400 hover:bg-slate-100" aria-label="Close">×</button>
        </div>
        <div class="space-y-3">
            <label class="block text-xs font-bold text-slate-600">Suggestion type
                <select name="suggestion_type" required class="mt-1.5 w-full rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-800">
                    <option value="course">Course I would like to teach</option>
                    <option value="schedule">Preferred schedule</option>
                </select>
            </label>
            <label class="block text-xs font-bold text-slate-600">Course or subject
                <input name="course" required maxlength="180" placeholder="e.g. IT 311 — Web Systems" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-normal text-slate-800">
            </label>
            <label class="block text-xs font-bold text-slate-600">Preferred days and times <span class="font-normal text-slate-400">(optional)</span>
                <input name="schedule_details" maxlength="600" placeholder="e.g. Tuesday/Thursday, morning" class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-normal text-slate-800">
            </label>
            <label class="block text-xs font-bold text-slate-600">Note to your chair
                <textarea name="message" required maxlength="2000" rows="4" placeholder="Add context for your suggestion..." class="mt-1.5 w-full rounded-lg border border-slate-200 px-3 py-2.5 text-sm font-normal text-slate-800"></textarea>
            </label>
            <p id="faculty-chair-suggestion-feedback" class="hidden rounded-lg px-3 py-2 text-xs" role="status" aria-live="polite"></p>
        </div>
        <div class="mt-5 flex justify-end gap-2">
            <button type="button" onclick="facultyHeaderCloseSuggestion()" class="rounded-lg bg-slate-100 px-4 py-2 text-xs font-semibold text-slate-700">Cancel</button>
            <button id="faculty-chair-suggestion-submit" type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700">Send to my chair</button>
        </div>
    </form>
</div>

<script>
function toggleFacultyMobileNav() {
    const sidebar = document.getElementById('faculty-sidebar');
    const backdrop = document.getElementById('faculty-mobile-nav-backdrop');
    const button = document.querySelector('.faculty-mobile-menu-button');
    if (!sidebar || !backdrop || !button) return;
    const isOpen = sidebar.classList.toggle('faculty-mobile-nav-open');
    backdrop.classList.toggle('faculty-mobile-nav-backdrop-open', isOpen);
    button.setAttribute('aria-expanded', String(isOpen));
    button.setAttribute('aria-label', isOpen ? 'Close navigation menu' : 'Open navigation menu');
}

function closeFacultyMobileNav() {
    const sidebar = document.getElementById('faculty-sidebar');
    const backdrop = document.getElementById('faculty-mobile-nav-backdrop');
    const button = document.querySelector('.faculty-mobile-menu-button');
    sidebar?.classList.remove('faculty-mobile-nav-open');
    backdrop?.classList.remove('faculty-mobile-nav-backdrop-open');
    button?.setAttribute('aria-expanded', 'false');
    button?.setAttribute('aria-label', 'Open navigation menu');
}

document.addEventListener('keydown', event => {
    if (event.key === 'Escape') closeFacultyMobileNav();
});
document.getElementById('faculty-sidebar')?.querySelectorAll('a').forEach(link => {
    link.addEventListener('click', closeFacultyMobileNav);
});
</script>

@php
    $facultyHeaderNotificationEndpoints = [
        'index' => route('notifications.index'),
        'unreadCount' => route('notifications.unread-count'),
        'read' => route('notifications.read', ['id' => '__notification_id__']),
        'readAll' => route('notifications.read-all'),
        'delete' => route('notifications.destroy', ['id' => '__notification_id__']),
        'sendSuggestion' => route('faculty.notifications.chair-suggestion'),
    ];
@endphp
<script>
const FACULTY_HEADER_NOTIFICATION_ENDPOINTS = @json($facultyHeaderNotificationEndpoints);
let facultyHeaderNotifOpen = false;

function facultyHeaderOpenSuggestion() {
    const modal = document.getElementById('faculty-chair-suggestion-modal');
    const dropdown = document.getElementById('notif-dropdown');
    if (dropdown) dropdown.style.display = 'none';
    facultyHeaderNotifOpen = false;
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    modal.querySelector('[name="course"]')?.focus();
}

function facultyHeaderCloseSuggestion() {
    const modal = document.getElementById('faculty-chair-suggestion-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    const feedback = document.getElementById('faculty-chair-suggestion-feedback');
    feedback?.classList.add('hidden');
}

async function facultyHeaderSubmitSuggestion(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const button = document.getElementById('faculty-chair-suggestion-submit');
    const feedback = document.getElementById('faculty-chair-suggestion-feedback');
    if (!form || !button || !feedback) return;

    button.disabled = true;
    button.textContent = 'Sending…';
    feedback.className = 'rounded-lg bg-blue-50 px-3 py-2 text-xs text-blue-700';
    feedback.textContent = 'Sending your suggestion to your department chair…';
    feedback.classList.remove('hidden');

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        if (!csrf) throw new Error('Could not verify this request. Refresh the page and try again.');
        const result = await facultyHeaderRequest(FACULTY_HEADER_NOTIFICATION_ENDPOINTS.sendSuggestion, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrf,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(new FormData(form).entries())),
        });
        feedback.className = 'rounded-lg bg-emerald-50 px-3 py-2 text-xs text-emerald-700';
        feedback.textContent = result.message || 'Your suggestion was sent to your department chair.';
        form.reset();
        window.setTimeout(facultyHeaderCloseSuggestion, 1200);
    } catch (error) {
        feedback.className = 'rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700';
        feedback.textContent = error.message || 'Unable to send your suggestion. Please try again.';
    } finally {
        button.disabled = false;
        button.textContent = 'Send to my chair';
    }
}

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
            facultyHeaderDeleteNotification(notification.notif_id, item, deleteButton);
        });

        item.append(dot, content, deleteButton);
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

async function facultyHeaderDeleteNotification(id, item, button) {
    button.disabled = true;
    try {
        const url = FACULTY_HEADER_NOTIFICATION_ENDPOINTS.delete.replace('__notification_id__', encodeURIComponent(id));
        await facultyHeaderRequest(url, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        });
        item.remove();
        if (!document.querySelector('#notif-list .notif-drop-item')) facultyHeaderShowNotifMessage('No notifications yet.');
        updateFacultyHeaderNotifCount();
    } catch (error) {
        button.disabled = false;
        facultyHeaderShowNotifMessage(error.message || 'Unable to delete this notification.');
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
