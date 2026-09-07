<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

{{-- ══ APP WRAPPER ══ --}}
<div class="app-shell">

    {{-- ══ SIDEBAR ══ --}}
    @include('partials.admin_sidebar')

    {{-- ══ MAIN ══ --}}
    <div class="app-main">

        {{-- ══ TOPBAR ══ --}}
        @include('partials.admin_header', ['title' => 'Technical Admin Dashboard'])

        {{-- ══ PAGE CONTENT ══ --}}
        <div class="page-content">

            {{-- ══ QUOTE BANNER ══ --}}
            <div class="quote-banner">
                <div class="quote-banner-grid"></div>
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="text-[11px] font-bold uppercase tracking-[1.5px] text-white/40 mb-1.5">
                            Welcome back, Tech Admin
                        </div>
                        <div id="quote-text" class="text-[22px] leading-snug font-bold text-white mb-2.5 italic">
                            "Education is the most powerful weapon which you can use to change the world."
                        </div>
                        <div id="quote-author" class="text-[12px] font-semibold text-white/40">
                            — Nelson Mandela
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <button type="button" id="quote-prev"
                                class="w-7 h-7 rounded-full bg-white/10 border-none text-white text-[13px] cursor-pointer flex items-center justify-center hover:bg-white/20 transition">‹</button>
                            <div class="flex gap-2" id="quote-dots"></div>
                            <button type="button" id="quote-next"
                                class="w-7 h-7 rounded-full bg-white/10 border-none text-white text-[13px] cursor-pointer flex items-center justify-center hover:bg-white/20 transition">›</button>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-[44px] opacity-[.12] leading-none mb-2.5">"</div>
                        <div class="text-[11px] text-white/30">AY 2025–2026 · 1st Sem</div>
                        <div class="text-[11px] text-white/30 mt-1">Last backup</div>
                        <div class="text-[12px] font-bold text-emerald-400">Today 06:00 AM ✓</div>
                    </div>
                </div>
            </div>

            {{-- ══ STAT CARDS ROW 1 ══ --}}
            <div class="grid grid-cols-4 gap-3 mb-3">
                <div class="stat-card">
                    <div class="stat-card-bar bg-blue-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Total Users</div>
                    <div class="stat-value">{{ $totalUsers }}</div>
                    <div class="stat-sub">4 roles registered</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-green-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">System Status</div>
                    <div class="text-[20px] font-extrabold text-slate-900 leading-none mb-1">{{ $dbStatus }}</div>
                    <div class="stat-sub">Vercel · Supabase active</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-amber-500"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Total Faculty</div>
                    <div class="stat-value">{{ $totalFaculty }}</div>
                    <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-cyan-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">DB Records</div>
                    <div class="stat-value">{{ $dbRecords }}</div>
                    <div class="stat-sub">Supabase PostgreSQL</div>
                </div>
            </div>

            {{-- ══ STAT CARDS ROW 2 ══ --}}
            <div class="grid grid-cols-4 gap-3 mb-4">
                <div class="stat-card">
                    <div class="stat-card-bar bg-violet-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Total Sections</div>
                    <div class="stat-value">{{ $totalSections }}</div>
                    <div class="stat-sub">Across 3 programs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-green-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Subjects Offered</div>
                    <div class="stat-value">{{ $subjectsOffered }}</div>
                    <div class="stat-sub">This semester</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-red-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Schedule Conflicts</div>
                    <div class="stat-value">{{ $scheduleConflicts }}</div>
                    <div class="stat-sub">Needs resolution</div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-bar bg-cyan-600"></div>
                    <div class="stat-icon"></div>
                    <div class="stat-label">Rooms Available</div>
                    <div class="stat-value">{{ $roomsAvailable }}</div>
                    <div class="stat-sub">{{ $roomsOccupied }} currently occupied</div>
                </div>
            </div>

            {{-- ══ USER ACCOUNTS + SYSTEM INFO ══ --}}
            <div class="grid grid-cols-12 gap-3 mb-4">

                {{-- User Accounts Table --}}
                <div class="col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">User Accounts</div>
                            <div class="card-sub">All registered system users</div>
                        </div>
                        <span class="badge badge-blue">{{ $totalUsers }} Total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $roleLabels = [
                                        'faculty'          => ['Faculty',     'badge-grey'],
                                        'department_chair' => ['Dept. Chair', 'badge-amber'],
                                        'dean'             => ['Dean',        'badge-blue'],
                                        'system_admin'     => ['System Admin','badge-navy'],
                                    ];
                                @endphp
                                @forelse($users as $user)
                                    @php
                                        [$roleLabel, $roleBadgeClass] = $roleLabels[$user->usr_role] ?? [ucfirst($user->usr_role), 'badge-grey'];
                                    @endphp
                                    <tr>
                                        <td class="font-semibold">{{ $user->usr_name }}</td>
                                        <td><span class="badge {{ $roleBadgeClass }}">{{ $roleLabel }}</span></td>
                                        <td>
                                            <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                                                {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users') }}"
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-semibold no-underline hover:bg-slate-200 transition">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-slate-400">No user accounts found yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- System Info + Role Distribution --}}
                <div class="col-span-4 flex flex-col gap-3">

                    <div class="card">
                        <div class="card-title mb-3">System Info</div>
                        <div class="text-[12px] text-slate-500 leading-[1.8]">
                            <div>🌐 <b class="text-slate-700">Host:</b> Vercel (Production)</div>
                            <div>🗄️ <b class="text-slate-700">DB:</b> Supabase PostgreSQL</div>
                            <div>🔐 <b class="text-slate-700">Auth:</b> JWT + Laravel</div>
                            <div>📱 <b class="text-slate-700">Mobile:</b> React Native</div>
                            <div>🎨 <b class="text-slate-700">Web UI:</b> Tailwind CSS</div>
                            <div class="mt-2 pt-2 border-t border-slate-100">
                                <div class="text-[11px] text-slate-400">Last backup</div>
                                <div class="font-bold text-green-600">Today, 06:00 AM ✓</div>
                            </div>
                        </div>
                    </div>

                    <div class="card flex-1">
                        <div class="card-title mb-3">Role Distribution</div>
                        @php $pct = fn($count) => $totalUsers > 0 ? round(($count / $totalUsers) * 100) : 0; @endphp

                        @foreach([
                            ['Faculty Members',  'faculty',          'bg-blue-600'],
                            ['Dept. Chairs',     'department_chair', 'bg-amber-500'],
                            ['Dean',             'dean',             'bg-cyan-600'],
                            ['Tech Admin',       'system_admin',     'bg-slate-800'],
                        ] as [$label, $key, $color])
                        <div class="workload-item">
                            <div class="workload-header">
                                <span class="workload-name">{{ $label }}</span>
                                <span class="workload-val text-slate-500">{{ $roleCounts[$key] ?? 0 }}</span>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill {{ $color }}" style="width:{{ $pct($roleCounts[$key] ?? 0) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ══ SECTIONS + SCHEDULE COMPLETION ══ --}}
            <div class="grid grid-cols-12 gap-3 mb-4">

                <div class="col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Current Sections</div>
                            <div class="card-sub">
                                AY {{ $academicYear->ay_year_label ?? 'N/A' }} · {{ $semester->sem_name ?? 'N/A' }}
                            </div>
                        </div>
                        <span class="badge badge-blue">{{ $section->count() }} Total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach(['Section','Program','Year','Students','Subjects','Status'] as $h)
                                    <th>{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section as $sec)
                                <tr>
                                    <td class="font-semibold">{{ $sec->sec_name }}</td>
                                    <td>{{ $sec->program->prog_code ?? 'N/A' }}</td>
                                    <td>{{ $sec->sec_year_level }}</td>
                                    <td>{{ $sec->sec_no_of_student }}</td>
                                    <td>—</td>
                                    <td>
                                        <span class="badge {{ $sec->sec_status === 'active' ? 'badge-green' : 'badge-amber' }}">
                                            {{ ucfirst($sec->sec_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-6 text-slate-400">No sections found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Schedule Completion --}}
                <div class="col-span-4 card">
                    <div class="card-title mb-0.5">Schedule Completion</div>
                    <div class="card-sub mb-4">By program</div>

                    @foreach($program as $programs)
                    @php
                        $pcolor = match($programs['color']) {
                            'blue'  => 'bg-blue-600',
                            'amber' => 'bg-amber-500',
                            'teal'  => 'bg-cyan-600',
                            'red'   => 'bg-red-600',
                            'green' => 'bg-green-600',
                            default => 'bg-slate-500',
                        };
                        $tcolor = match($programs['color']) {
                            'blue'  => 'text-blue-600',
                            'amber' => 'text-amber-500',
                            'teal'  => 'text-cyan-600',
                            'red'   => 'text-red-600',
                            'green' => 'text-green-600',
                            default => 'text-slate-500',
                        };
                    @endphp
                    <div class="workload-item">
                        <div class="workload-header">
                            <span class="workload-name">{{ $programs['name'] }}</span>
                            <span class="workload-val {{ $tcolor }}">{{ $programs['percent'] }}%</span>
                        </div>
                        <div class="workload-bar">
                            <div class="workload-fill {{ $pcolor }}" style="width:{{ $programs['percent'] }}%"></div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="text-[12px] font-bold text-slate-500 mb-2">Sections Summary</div>
                        <div class="flex gap-2">
                            <div class="flex-1 text-center p-2 rounded-xl bg-green-50">
                                <div class="text-[20px] font-extrabold text-green-600">{{ $scheduledCount }}</div>
                                <div class="text-[10px] font-semibold text-green-600">Fully Scheduled</div>
                            </div>
                            <div class="flex-1 text-center p-2 rounded-xl bg-amber-50">
                                <div class="text-[20px] font-extrabold text-amber-500">{{ $inProgressCount }}</div>
                                <div class="text-[10px] font-semibold text-amber-500">In Progress</div>
                            </div>
                            <div class="flex-1 text-center p-2 rounded-xl bg-red-50">
                                <div class="text-[20px] font-extrabold text-red-600">{{ $unscheduledCount }}</div>
                                <div class="text-[10px] font-semibold text-red-600">Unscheduled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SUBJECTS + ROOM UTILIZATION ══ --}}
            <div class="grid grid-cols-12 gap-3 mb-4">

                <div class="col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Subjects Offered</div>
                            <div class="card-sub">Current semester — all programs</div>
                        </div>
                        <span class="badge badge-blue">{{ $subject->count() }} Subjects</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach(['Code','Subject','Units','Program','Assigned Faculty','Status'] as $h)
                                    <th>{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subject as $subjects)
                                <tr>
                                    <td><span class="font-mono text-[12px] text-slate-600">{{ $subjects->subj_code }}</span></td>
                                    <td class="font-semibold">{{ $subjects->subj_name }}</td>
                                    <td>{{ $subjects->subj_lecture_hours + $subjects->subj_lab_hours }}</td>
                                    <td>{{ $subjects->program->prog_name ?? 'N/A' }}</td>
                                    <td class="text-red-500">Unassigned</td>
                                    <td>
                                        <span class="badge {{ $subjects->subj_is_active ? 'badge-green' : 'badge-red' }}">
                                            {{ $subjects->subj_is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-6 text-slate-400">No subjects found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Room Utilization --}}
                <div class="col-span-4 card">
                    <div class="card-title mb-0.5">Room Utilization</div>
                    <div class="card-sub mb-3">This week</div>

                    <div id="room-util-track" class="overflow-hidden relative">
                        <div id="room-util-list" class="flex flex-col transition-transform duration-400">
                            @foreach($room as $rooms)
                            @php
                                $rc = match($rooms['color']) {
                                    'blue'  => ['bg-blue-600',  'text-blue-600'],
                                    'amber' => ['bg-amber-500', 'text-amber-500'],
                                    'green' => ['bg-green-600', 'text-green-600'],
                                    'red'   => ['bg-red-600',   'text-red-600'],
                                    default => ['bg-slate-500', 'text-slate-500'],
                                };
                            @endphp
                            <div class="workload-item">
                                <div class="workload-header">
                                    <span class="workload-name">{{ $rooms['name'] }}</span>
                                    <span class="workload-val {{ $rc[1] }}">
                                        {{ $rooms['count'] }} {{ Str::plural('class', $rooms['count']) }}
                                    </span>
                                </div>
                                <div class="workload-bar">
                                    <div class="workload-fill {{ $rc[0] }}" style="width:{{ $rooms['percent'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-center items-center gap-2.5 mt-2.5">
                        <button id="room-util-prev"
                            class="px-3 py-1 text-[12px] border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
                            onclick="roomUtilPage(-1)">‹ Prev</button>
                        <span id="room-util-indicator" class="text-[12px] text-slate-400"></span>
                        <button id="room-util-next"
                            class="px-3 py-1 text-[12px] border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
                            onclick="roomUtilPage(1)">Next ›</button>
                    </div>

                    <div class="flex justify-between mt-4 pt-4 border-t border-slate-100 text-[12px]">
                        <div>
                            <div class="text-slate-400">Total Rooms</div>
                            <div class="text-[18px] font-extrabold text-slate-900">{{ $totalRooms }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400">In Use</div>
                            <div class="text-[18px] font-extrabold text-blue-600">{{ $roomsInUse }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Available</div>
                            <div class="text-[18px] font-extrabold text-green-600">{{ $roomsAvailable }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ RECENT ACTIVITY ══ --}}
            <div class="card mb-4">
                <div class="card-header">
                    <div>
                        <div class="card-title">Recent System Activity</div>
                        <div class="card-sub">Latest actions across all users</div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead>
                            <tr>
                                @foreach(['Time','User','Action','Details','Status'] as $h)
                                <th>{{ $h }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($audit_log as $log)
                            <tr>
                                <td class="font-mono text-[11px] text-slate-400">{{ $log->created_at->format('h:i A') }}</td>
                                <td class="font-semibold">{{ $log->user_name }}</td>
                                <td>{{ $log->action }}</td>
                                <td class="text-slate-500">{{ $log->details }}</td>
                                <td>
                                    @php
                                        $logBadgeClass = match($log->status) {
                                            'Success' => 'badge-green',
                                            'Info'    => 'badge-blue',
                                            'Warning' => 'badge-red',
                                            default   => 'badge-grey',
                                        };
                                    @endphp
                                    <span class="badge {{ $logBadgeClass }}">{{ $log->status }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-6 text-slate-400">No recent activity.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>{{-- end page-content --}}
    </div>{{-- end app-main --}}
</div>{{-- end app-shell --}}

<script>
(function() {
    const perPage = 5;
    const list = document.getElementById('room-util-list');
    if (!list) return;
    const items = Array.from(list.children);
    const total = items.length;
    if (total === 0) return;
    const pages = Math.ceil(total / perPage);
    let current = 0;
    function getItemHeight() {
        const first = items[0];
        const style = window.getComputedStyle(first);
        return first.offsetHeight + (parseFloat(style.marginBottom) || 0);
    }
    function render() {
        const itemHeight = getItemHeight();
        const track = document.getElementById('room-util-track');
        track.style.height = (perPage * itemHeight) + 'px';
        list.style.transform = `translateY(-${current * perPage * itemHeight}px)`;
        document.getElementById('room-util-indicator').textContent = `Page ${current + 1} of ${pages}`;
        document.getElementById('room-util-prev').disabled = current === 0;
        document.getElementById('room-util-next').disabled = current === pages - 1;
    }
    window.roomUtilPage = function(dir) {
        const next = current + dir;
        if (next < 0 || next >= pages) return;
        current = next;
        render();
    };
    requestAnimationFrame(render);
    window.addEventListener('resize', render);
})();

(function() {
    const quotes = [
        { text: "Education is the most powerful weapon which you can use to change the world.", author: "Nelson Mandela" },
        { text: "The beautiful thing about learning is that no one can take it away from you.", author: "B.B. King" },
        { text: "An investment in knowledge pays the best interest.", author: "Benjamin Franklin" }
    ];
    const textEl   = document.getElementById('quote-text');
    const authorEl = document.getElementById('quote-author');
    const dotsEl   = document.getElementById('quote-dots');
    const prevBtn  = document.getElementById('quote-prev');
    const nextBtn  = document.getElementById('quote-next');
    if (!textEl || !dotsEl) return;
    let current = 0;
    function renderDots() {
        dotsEl.innerHTML = quotes.map((_, i) =>
            `<span style="width:6px;height:6px;border-radius:50%;display:inline-block;background:rgba(255,255,255,${i === current ? '.9' : '.25'});"></span>`
        ).join('');
    }
    function renderQuote() {
        textEl.textContent = `"${quotes[current].text}"`;
        authorEl.textContent = `— ${quotes[current].author}`;
        renderDots();
    }
    prevBtn.addEventListener('click', () => { current = (current - 1 + quotes.length) % quotes.length; renderQuote(); });
    nextBtn.addEventListener('click', () => { current = (current + 1) % quotes.length; renderQuote(); });
    renderQuote();
})();
</script>

</body>
</html>