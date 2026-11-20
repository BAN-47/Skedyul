<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Dean Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    @php
        $colorMap = [
            'green' => ['text' => 'text-green-600', 'bar' => 'bg-green-600'],
            'amber' => ['text' => 'text-amber-600', 'bar' => 'bg-amber-600'],
            'cyan' => ['text' => 'text-cyan-600', 'bar' => 'bg-cyan-600'],
            'blue' => ['text' => 'text-blue-600', 'bar' => 'bg-blue-600'],
            'slate' => ['text' => 'text-slate-500', 'bar' => 'bg-slate-400'],
            'red' => ['text' => 'text-red-600', 'bar' => 'bg-red-600'],
        ];

        $statusBadge = [
            'Overload' => 'badge badge-red',
            'Near Max' => 'badge badge-amber',
            'Available' => 'badge badge-blue',
            'No Load' => 'badge badge-slate',
        ];
    @endphp

    <div class="app-shell">
        @include('partials.dean_sidebar')

        <div class="app-main">
            <div class="topbar">
                <div class="topbar-title flex items-center gap-2.5">
                    <span>Dean Dashboard</span>
                    <span class="hidden rounded-full border border-emerald-100 bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 sm:inline-flex">College overview</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="hidden rounded-full border border-blue-100 bg-blue-50 px-3 py-1.5 text-[11px] font-semibold text-blue-700 md:inline-flex">
                        {{ $semester?->sem_name ?? 'No active semester' }} · AY {{ $academicYear?->ay_academic_year ?? '—' }}
                    </span>
                    <a href="{{ route('dean.pending_approvals') }}" class="btn btn-secondary flex items-center gap-1.5">
                        Pending Approvals
                        @if (($pendingDeptCount ?? 0) > 0)
                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $pendingDeptCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <div class="page-content overflow-y-auto" style="max-height: calc(100vh - 64px); padding-bottom: 32px;">
                <div class="mb-6 overflow-hidden rounded-2xl bg-gradient-to-r from-slate-950 via-blue-950 to-blue-700 px-5 py-5 text-white shadow-lg sm:px-7 sm:py-6">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-[0.18em] text-blue-200">College scheduling overview</div>
                            <div class="mt-1 text-xl font-extrabold sm:text-2xl">Welcome, Dean {{ $greetingName ?? 'Dean' }}</div>
                            <div class="mt-1 text-xs text-blue-100 sm:text-sm">
                                {{ $college?->college_name ?? $college?->college_code ?? 'Your college' }} · {{ $semester?->sem_name ?? 'No active semester' }} · AY {{ $academicYear?->ay_academic_year ?? '—' }}
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3 py-2 text-xs font-semibold text-white">
                            <span class="h-2 w-2 rounded-full bg-emerald-300"></span> Scheduling active
                        </span>
                    </div>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-card-bar bg-cyan-600"></div>
                        <div class="stat-label">Total Faculty</div>
                        <div class="stat-value">{{ $totalFaculty ?? 0 }}</div>
                        <div class="stat-sub">Across {{ count($deptSummary ?? []) }} departments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-amber-600"></div>
                        <div class="stat-label">Subjects Plotted</div>
                        <div class="stat-value">{{ $subjectsPlotted ?? 0 }}</div>
                        <div class="stat-sub">of {{ $subjectsTotal ?? 0 }} current-semester subjects</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-violet-600"></div>
                        <div class="stat-label">Total Sections</div>
                        <div class="stat-value">{{ $totalSections ?? 0 }}</div>
                        <div class="stat-sub">Across all college departments</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-green-600"></div>
                        <div class="stat-label">Schedules Approved</div>
                        <div class="stat-value">{{ $scheduledApprovedCount ?? 0 }}</div>
                        <div class="stat-sub">
                            Pending: {{ $pendingDeptCount ?? 0 }}
                            dept{{ ($pendingDeptCount ?? 0) === 1 ? '' : 's' }}
                        </div>
                    </div>
                </div>

                <div class="card mt-4 overflow-hidden border border-blue-100">
                    <div class="card-header">
                        <div>
                            <div class="card-title"><span class="mr-1 text-amber-500" aria-hidden="true">🏆</span> Top 3 Most Plotted Faculty</div>
                            <div class="card-sub">Scheduled teaching hours across all departments in {{ $college?->college_code ?? 'your college' }} · {{ $semester?->sem_name ?? 'current semester' }}</div>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-blue-700">College-wide</span>
                    </div>
                    <div class="grid grid-cols-1 gap-3 pb-1 md:grid-cols-3">
                        @forelse($topFaculty ?? [] as $index => $leader)
                            @php
                                $leaderStyles = [
                                    ['border' => 'border-amber-400', 'badge' => 'bg-amber-50 text-amber-800', 'label' => '🥇 #1'],
                                    ['border' => 'border-slate-300', 'badge' => 'bg-slate-100 text-slate-700', 'label' => '🥈 #2'],
                                    ['border' => 'border-orange-300', 'badge' => 'bg-orange-50 text-orange-800', 'label' => '🥉 #3'],
                                ][$index] ?? ['border' => 'border-blue-200', 'badge' => 'bg-blue-50 text-blue-700', 'label' => '#' . ($index + 1)];
                                $leaderInitials = collect(preg_split('/\s+/', trim($leader['name'])))
                                    ->filter()->take(2)->map(fn($part) => strtoupper(substr($part, 0, 1)))->implode('');
                                $leaderMax = max(1, $topFaculty->max('total_plotted'));
                            @endphp
                            <div class="rounded-xl border-2 {{ $leaderStyles['border'] }} bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 text-sm font-extrabold text-blue-800">{{ $leaderInitials ?: 'F' }}</div>
                                    <div class="min-w-0 flex-1">
                                        <div class="truncate text-sm font-bold text-slate-900">{{ $leader['name'] }}</div>
                                        <div class="truncate text-[11px] text-slate-500">{{ $leader['rank'] }} · {{ $leader['department'] }}</div>
                                    </div>
                                    <span class="shrink-0 rounded-full px-2 py-1 text-[10px] font-extrabold {{ $leaderStyles['badge'] }}">{{ $leaderStyles['label'] }}</span>
                                </div>
                                <div class="mt-4 flex items-end justify-between gap-2">
                                    <div><div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Plotted load</div><div class="mt-0.5 text-xl font-extrabold text-slate-900">{{ $leader['total_plotted'] }}<span class="ml-1 text-xs font-semibold text-slate-400">hrs</span></div></div>
                                    <div class="text-right text-[10px] font-semibold text-slate-500">{{ $leader['class_count'] }} classes · {{ $leader['section_count'] }} sections</div>
                                </div>
                                <div class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-gradient-to-r from-blue-600 to-cyan-500" style="width:{{ min(100, round(($leader['total_plotted'] / $leaderMax) * 100)) }}%"></div></div>
                                <div class="mt-2 flex justify-between text-[10px] font-semibold"><span class="text-emerald-700">Day {{ $leader['day_hours'] }}h</span><span class="text-indigo-700">Evening {{ $leader['evening_hours'] }}h</span></div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-slate-400 md:col-span-3">No active plotted schedules in this college for the selected semester.</div>
                        @endforelse
                    </div>
                </div>

                <div class="two-col mt-5">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Department Summary</div>
                                <div class="card-sub">Faculty load status per program</div>
                            </div>
                        </div>

                        @forelse($deptSummary ?? [] as $dept)
                            @php
                                $c = $colorMap[$dept['color'] ?? 'slate'] ?? $colorMap['slate'];
                            @endphp
                            <div class="workload-item" data-dean-department-row>
                                <div class="workload-header">
                                    <div class="workload-name">
                                        {{ $dept['name'] }} — {{ $dept['count'] }} Faculty
                                    </div>
                                    <div class="workload-val {{ $c['text'] }}">{{ $dept['percent'] }}%</div>
                                </div>
                                <div class="workload-bar">
                                    <div class="workload-fill {{ $c['bar'] }}"
                                        style="width:{{ $dept['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="px-1 py-4 text-sm text-slate-400">No program data yet.</div>
                        @endforelse

                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">Overloaded Faculty</div>
                                <div class="workload-val text-red-600">{{ $overloadCount ?? 0 }}</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-red-600"
                                    style="width:{{ min(100, ($overloadCount ?? 0) * 10) }}%"></div>
                            </div>
                        </div>
                        <div id="dean-department-pagination" class="mt-3 flex items-center justify-between gap-2 border-t border-slate-100 pt-3 text-[11px] text-slate-500">
                            <span id="dean-department-page-info" aria-live="polite"></span>
                            <div class="flex gap-2">
                                <button type="button" id="dean-department-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Previous department page">Previous</button>
                                <button type="button" id="dean-department-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40" aria-label="Next department page">Next</button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Pending Approvals</div>
                                <div class="card-sub">
                                    @if (($pendingApprovals?->total() ?? 0) > 0)
                                        Showing {{ $pendingApprovals->firstItem() }}–{{ $pendingApprovals->lastItem() }} of {{ $pendingApprovals->total() }} pending
                                    @else
                                        No pending schedule submissions
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('dean.pending_approvals') }}"
                                class="topbar-btn btn-primary px-3 py-2 text-xs">
                                Go to Pending Approvals
                            </a>
                        </div>

                        @forelse($pendingApprovals ?? [] as $item)
                            <div class="approval-item">
                                <div class="approval-avatar" style="background:{{ $item['color'] ?? '#64748b' }};">
                                    {{ $item['initials'] ?? 'CH' }}
                                </div>
                                <div class="approval-content">
                                    <div class="approval-name">{{ $item['title'] ?? 'Schedule' }}</div>
                                    <div class="approval-detail">{{ $item['detail'] ?? '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="px-1 py-6 text-sm text-slate-400 text-center">
                                No pending schedule submissions.
                            </div>
                        @endforelse
                        @if (($pendingApprovals?->lastPage() ?? 1) > 1)
                            <div class="mt-3 flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3 text-xs">
                                <span class="text-slate-500">Page {{ $pendingApprovals->currentPage() }} of {{ $pendingApprovals->lastPage() }}</span>
                                <div class="flex items-center gap-2">
                                    @if ($pendingApprovals->previousPageUrl())
                                        <a href="{{ $pendingApprovals->previousPageUrl() }}" class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 font-semibold text-slate-600 hover:bg-slate-50">Previous</a>
                                    @else
                                        <span class="rounded-lg border border-slate-100 px-3 py-1.5 text-slate-300">Previous</span>
                                    @endif
                                    @if ($pendingApprovals->nextPageUrl())
                                        <a href="{{ $pendingApprovals->nextPageUrl() }}" class="rounded-lg border border-blue-100 bg-blue-50 px-3 py-1.5 font-semibold text-blue-700 hover:bg-blue-100">Next</a>
                                    @else
                                        <span class="rounded-lg border border-slate-100 px-3 py-1.5 text-slate-300">Next</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header flex-wrap">
                        <div>
                            <div class="card-title">College Schedule Rhythm</div>
                            <div class="card-sub">Active schedules across your college, grouped by weekday and shift</div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <div class="inline-flex rounded-lg bg-slate-100 p-1 text-[11px] font-semibold">
                                <button type="button" data-dean-rhythm-mode="hours" aria-pressed="true" class="rounded-md bg-white px-3 py-1.5 text-blue-700 shadow-sm">Schedule Load</button>
                                <button type="button" data-dean-rhythm-mode="rooms" aria-pressed="false" class="rounded-md px-3 py-1.5 text-slate-500">Room Util.</button>
                            </div>
                            <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                                <span class="inline-flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-sm bg-blue-600"></i>Day</span>
                                <span class="inline-flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-sm bg-cyan-500"></i>Evening</span>
                            </div>
                        </div>
                    </div>
                    @php $scheduleDayMax = max(1, $weeklyScheduleCounts->max(fn ($item) => $item['day_count'] + $item['evening_count'])); @endphp
                    <div id="dean-rhythm-hours" class="grid grid-cols-2 gap-3 pt-2 sm:grid-cols-4 xl:grid-cols-7" aria-label="College classes by weekday and shift">
                        @foreach($weeklyScheduleCounts as $item)
                            @php $dayHeight = $item['day_count'] ? max(7, round($item['day_count'] / $scheduleDayMax * 100)) : 0; $eveningHeight = $item['evening_count'] ? max(7, round($item['evening_count'] / $scheduleDayMax * 100)) : 0; @endphp
                            <div class="rounded-xl border border-slate-100 bg-gradient-to-b from-slate-50 to-white p-3">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-extrabold text-slate-700">{{ $item['day'] }}</span>
                                    <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-bold text-blue-700">{{ $item['day_count'] + $item['evening_count'] }} classes</span>
                                </div>
                                <div class="mt-3 flex h-20 items-end gap-2 rounded-lg bg-white px-3 pb-2 pt-1 shadow-inner">
                                    <div class="flex h-full flex-1 items-end"><div class="w-full rounded-t-md bg-gradient-to-t from-blue-700 to-blue-500" title="Day: {{ $item['day_count'] }} classes" style="height:{{ $dayHeight }}%"></div></div>
                                    <div class="flex h-full flex-1 items-end"><div class="w-full rounded-t-md bg-gradient-to-t from-cyan-600 to-cyan-400" title="Evening: {{ $item['evening_count'] }} classes" style="height:{{ $eveningHeight }}%"></div></div>
                                </div>
                                <div class="mt-2 flex justify-between text-[9px] font-semibold text-slate-500"><span>Day {{ $item['day_count'] }}</span><span>Evening {{ $item['evening_count'] }}</span></div>
                            </div>
                        @endforeach
                    </div>
                    <div id="dean-rhythm-rooms" class="hidden grid grid-cols-2 gap-3 pt-2 sm:grid-cols-4 xl:grid-cols-7" aria-label="Room use by weekday">
                        @foreach($weeklyScheduleCounts as $item)
                            <div class="rounded-xl border border-indigo-100 bg-gradient-to-b from-indigo-50/70 to-white p-3">
                                <div class="flex items-center justify-between gap-1">
                                    <span class="text-xs font-extrabold text-slate-700">{{ $item['day'] }}</span>
                                    <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-bold text-indigo-700">{{ $item['rooms_active'] }} rooms active</span>
                                </div>
                                @if ($item['peak_room'])
                                    <div class="mt-3 rounded-lg border border-indigo-100 bg-white p-3 shadow-sm">
                                        <div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Busiest room</div>
                                        <div class="mt-1 truncate text-sm font-extrabold text-indigo-800" title="{{ $item['peak_room'] }}">{{ $item['peak_room'] }}</div>
                                        <div class="mt-1 text-[10px] font-medium text-slate-500">{{ $item['peak_room_sections'] }} sections · {{ $item['peak_room_bookings'] }} scheduled blocks</div>
                                    </div>
                                @else
                                    <div class="mt-3 flex h-[74px] items-center justify-center rounded-lg bg-white text-[11px] text-slate-400">No room bookings</div>
                                @endif
                                <div class="mt-2 text-[9px] font-semibold text-slate-500">{{ $item['day_count'] + $item['evening_count'] }} classes this day</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <div>
                            <div class="card-title">My Recent Activity</div>
                            <div class="card-sub">Your latest sign-ins and account changes</div>
                        </div>
                        <div id="dean-activity-pagination" class="flex items-center gap-2 text-xs text-slate-500">
                            <span id="dean-activity-page-info" aria-live="polite"></span>
                            <button type="button" id="dean-activity-prev" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                            <button type="button" id="dean-activity-next" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                        </div>
                    </div>

                    <div class="space-y-0">
                        @forelse($recentActivity ?? [] as $activity)
                            @php
                                $activityDetail = preg_replace(
                                    '/^Role: [^;]+; /',
                                    '',
                                    (string) $activity->al_description,
                                );
                                $activityDetail = preg_replace('/; HTTP \d{3}$/', '', $activityDetail);
                                $activityTime = \Illuminate\Support\Carbon::parse($activity->al_created_at)->timezone(
                                    config('app.timezone'),
                                );
                                $activityIsLogin = in_array($activity->al_action, ['Logged in', 'Logged out'], true);
                            @endphp

                            <div data-dean-activity class="flex items-start gap-3.5 border-b border-slate-100 py-3.5 last:border-b-0">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $activityIsLogin ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
                                    <i
                                        class="ti {{ $activity->al_action === 'Logged in' ? 'ti-login' : ($activity->al_action === 'Logged out' ? 'ti-logout' : 'ti-edit') }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                        <div class="text-[13px] font-semibold text-slate-700">
                                            {{ $activity->al_action }}
                                        </div>
                                        <time class="whitespace-nowrap text-[11px] font-medium text-slate-400"
                                            datetime="{{ $activityTime->toIso8601String() }}">
                                            {{ $activityTime->format('M j, Y · g:i A') }}
                                        </time>
                                    </div>
                                    <div class="mt-1 break-words text-[12px] text-slate-500">
                                        {{ $activityDetail ?: $activity->al_target_table }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-slate-400">
                                No activity recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
                <footer class="mt-4 flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3 text-[11px] text-slate-500">
                    <span><strong class="text-slate-700">SKEDYUL</strong> · College scheduling and workload overview</span>
                    <span>{{ $college?->college_name ?? $college?->college_code ?? 'College' }} · {{ $semester?->sem_name ?? 'No active semester' }} · AY {{ $academicYear?->ay_academic_year ?? '—' }}</span>
                </footer>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        (() => {
            const rows = [...document.querySelectorAll('[data-dean-department-row]')];
            const pagination = document.getElementById('dean-department-pagination');
            const info = document.getElementById('dean-department-page-info');
            const previous = document.getElementById('dean-department-prev');
            const next = document.getElementById('dean-department-next');
            const pageSize = 5;
            let page = 0;
            if (!pagination || !info || !previous || !next) return;
            pagination.classList.toggle('hidden', rows.length <= pageSize);

            function renderDepartments() {
                const pageCount = Math.max(1, Math.ceil(rows.length / pageSize));
                page = Math.min(Math.max(page, 0), pageCount - 1);
                const start = page * pageSize;
                rows.forEach((row, index) => row.classList.toggle('hidden', index < start || index >= start + pageSize));
                info.textContent = rows.length ? `Showing ${start + 1}–${Math.min(start + pageSize, rows.length)} of ${rows.length} departments` : 'No departments';
                previous.disabled = page === 0;
                next.disabled = page >= pageCount - 1;
            }

            previous.addEventListener('click', () => { page--; renderDepartments(); });
            next.addEventListener('click', () => { page++; renderDepartments(); });
            renderDepartments();
        })();

        (() => {
            const buttons = [...document.querySelectorAll('[data-dean-rhythm-mode]')];
            const hours = document.getElementById('dean-rhythm-hours');
            const rooms = document.getElementById('dean-rhythm-rooms');
            if (!buttons.length || !hours || !rooms) return;
            buttons.forEach(button => button.addEventListener('click', () => {
                const showRooms = button.dataset.deanRhythmMode === 'rooms';
                hours.classList.toggle('hidden', showRooms);
                rooms.classList.toggle('hidden', !showRooms);
                buttons.forEach(toggle => {
                    const active = toggle === button;
                    toggle.setAttribute('aria-pressed', String(active));
                    toggle.classList.toggle('bg-white', active);
                    toggle.classList.toggle('text-blue-700', active);
                    toggle.classList.toggle('shadow-sm', active);
                    toggle.classList.toggle('text-slate-500', !active);
                });
            }));
        })();

        (() => {
            const rows = [...document.querySelectorAll('[data-dean-activity]')];
            const pagination = document.getElementById('dean-activity-pagination');
            const info = document.getElementById('dean-activity-page-info');
            const previous = document.getElementById('dean-activity-prev');
            const next = document.getElementById('dean-activity-next');
            const pageSize = 4;
            let page = 0;
            if (!pagination || !info || !previous || !next) return;
            pagination.classList.toggle('hidden', rows.length <= pageSize);

            function renderPage() {
                const pageCount = Math.max(1, Math.ceil(rows.length / pageSize));
                page = Math.min(Math.max(page, 0), pageCount - 1);
                const start = page * pageSize;
                rows.forEach((row, index) => row.classList.toggle('hidden', index < start || index >= start + pageSize));
                info.textContent = rows.length ? `Showing ${start + 1}–${Math.min(start + pageSize, rows.length)} of ${rows.length}` : 'No activity';
                previous.disabled = page === 0;
                next.disabled = page >= pageCount - 1;
            }

            previous.addEventListener('click', () => { page--; renderPage(); });
            next.addEventListener('click', () => { page++; renderPage(); });
            renderPage();
        })();

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        async function approveSubmission(id, button) {
            if (!confirm('Approve this schedule submission?')) return;

            button.disabled = true;

            try {
                const response = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showToast(data.message || 'Failed to approve.');
                    button.disabled = false;
                    return;
                }

                showToast(data.message || 'Schedule approved.');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                showToast('Failed to approve.');
                button.disabled = false;
            }
        }

        async function returnSubmission(id, button) {
            if (!confirm('Return this schedule to the chair?')) return;

            button.disabled = true;

            try {
                const response = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showToast(data.message || 'Failed to return.');
                    button.disabled = false;
                    return;
                }

                showToast(data.message || 'Returned to chair.');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                showToast('Failed to return.');
                button.disabled = false;
            }
        }
    </script>
</body>

</html>
