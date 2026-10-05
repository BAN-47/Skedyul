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
    // ── Today's schedule feed ───────────────────────────────────────────
    // Two ways to feed this header, so it works on pages with different
    // data shapes:
    //
    // 1) Pass $scheduleFeed directly — an already-flat array of
    //    ['code'=>, 'room'=>, 'day'=>, 'start'=>'H:i', 'end'=>'H:i']
    //    (e.g. the faculty dashboard, which already builds this shape
    //    from $todaySchedule for its own countdown table).
    //
    // 2) Pass $subjects — the same nested shape the faculty subjects
    //    page's grid is built from (['subject'=>, 'room'=>, 'schedules'=>
    //    [['day'=>,'start'=>,'end'=>], ...]]) — and this header will
    //    flatten it itself.
    //
    // If neither is passed, this header just shows chair/dean messages
    // with no "today's schedule" section.
    if (isset($scheduleFeed)) {
        $facultyHeaderSchedule = $scheduleFeed;
    } else {
        $facultyHeaderSchedule = [];
        foreach (($subjects ?? []) as $entry) {
            $subj = $entry['subject'] ?? null;
            $code = $subj->subj_code ?? ($entry['code'] ?? 'N/A');
            foreach (($entry['schedules'] ?? []) as $sched) {
                $facultyHeaderSchedule[] = [
                    'code'  => $code,
                    'room'  => $entry['room'] ?? '',
                    'day'   => $sched['day'],
                    'start' => \Carbon\Carbon::parse($sched['start'])->format('H:i'),
                    'end'   => \Carbon\Carbon::parse($sched['end'])->format('H:i'),
                ];
            }
        }
    }

    // ── Messages from the chair/dean ────────────────────────────────────
    // Wire this to a real query (e.g. $announcements = Announcement::forFaculty($faculty->id)->latest()->get())
    // once that table/relationship exists. Pass $announcements in from the
    // including page to override this placeholder.
    $facultyHeaderAnnouncements = $announcements ?? [
        ['from' => 'Chair Rodrigo Tan', 'role' => 'BSIS Chair', 'color' => '#d97706',
         'message' => 'Please submit your consultation hours schedule by Friday.', 'time' => '2h ago'],
        ['from' => 'Dean Villaceran', 'role' => 'Dean, CCICT', 'color' => '#0891b2',
         'message' => 'IS102 will be under maintenance next Monday — classes moved to IS201.', 'time' => 'Yesterday'],
    ];
@endphp

<script>
const FACULTY_HEADER_SCHEDULE = @json($facultyHeaderSchedule);
const FACULTY_HEADER_ANNOUNCEMENTS = @json($facultyHeaderAnnouncements);
const FACULTY_HEADER_DAY_NAMES = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

function facultyHeaderParseTimeToday(timeStr) {
    const [h, m] = timeStr.split(':').map(Number);
    const d = new Date();
    d.setHours(h, m, 0, 0);
    return d;
}

function renderFacultyHeaderNotifList() {
    const list = document.getElementById('notif-list');
    if (!list) return;

    const now = new Date();
    const todayName = FACULTY_HEADER_DAY_NAMES[now.getDay()];

    // Today's classes that are upcoming or currently in session, soonest first
    const todaysSched = FACULTY_HEADER_SCHEDULE
        .filter(s => s.day === todayName)
        .map(s => ({ ...s, start: facultyHeaderParseTimeToday(s.start), end: facultyHeaderParseTimeToday(s.end) }))
        .filter(s => s.end >= now)
        .sort((a, b) => a.start - b.start);

    const schedItems = todaysSched.map(s => {
        let label, dot;
        if (now < s.start) {
            const mins = Math.floor((s.start - now) / 60000);
            label = `Starts in ${mins < 60 ? mins + 'm' : Math.floor(mins / 60) + 'h ' + (mins % 60) + 'm'}`;
            dot = '#2563eb';
        } else {
            const mins = Math.floor((s.end - now) / 60000);
            label = mins < 5 ? `Ending in ${mins}m` : `Ongoing — ${mins}m left`;
            dot = mins < 5 ? '#dc2626' : '#16a34a';
        }
        return { dot, text: `<b>${s.code}</b> — ${s.room} · ${label}`, time: 'Today', unread: true };
    });

    const announceItems = FACULTY_HEADER_ANNOUNCEMENTS.map(a => ({
        dot: a.color,
        text: `<b>${a.from}</b> — ${a.message}`,
        time: a.time,
        unread: false,
    }));

    const items = [...schedItems, ...announceItems];

    list.innerHTML = items.length
        ? items.map((n) => `
            <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="facultyHeaderMarkRead(this)">
                <div class="notif-drop-dot" style="background:${n.dot};"></div>
                <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
            </div>`).join('')
        : `<div class="px-4 py-6 text-center text-xs text-slate-400">No notifications right now.</div>`;

    updateFacultyHeaderNotifCount();
}

function facultyHeaderToggleNotifDropdown() {
    const dd = document.getElementById('notif-dropdown');
    if (!dd) return;
    const isHidden = dd.style.display === 'none';
    dd.style.display = isHidden ? 'block' : 'none';
}

document.addEventListener('click', function (e) {
    const bell = document.getElementById('topbar-notif-bell');
    if (bell && !bell.contains(e.target)) {
        const dd = document.getElementById('notif-dropdown');
        if (dd) dd.style.display = 'none';
    }
});

function facultyHeaderMarkRead(el) {
    if (!el) return;
    el.classList.remove('unread');
    updateFacultyHeaderNotifCount();
}

function facultyHeaderMarkAllRead() {
    document.querySelectorAll('.notif-drop-item.unread').forEach((el) => el.classList.remove('unread'));
    updateFacultyHeaderNotifCount();
}

function updateFacultyHeaderNotifCount() {
    const unread = document.querySelectorAll('.notif-drop-item.unread').length;
    const badge = document.getElementById('notif-count');
    if (badge) {
        badge.textContent = unread;
        badge.style.display = unread > 0 ? 'inline' : 'none';
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderFacultyHeaderNotifList);
} else {
    renderFacultyHeaderNotifList();
}

// Keep "Starts in Xm" / "Ongoing" text accurate without a page refresh
setInterval(renderFacultyHeaderNotifList, 30000);
</script>