<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body data-active-tab="{{ request('reports_tab', '') }}" class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">
    @include('partials.admin_sidebar')
    <div class="app-main">

        {{-- TOPBAR --}}
        @include('partials.admin_header', ['title' => 'Technical Admin Reports'])


        {{-- FLASH --}}
        @if(session("success"))
            <script>document.addEventListener("DOMContentLoaded",()=>showToast("✅ {{ session("success") }}"));</script>
        @endif

        <div class="page-content">

            {{-- SEMESTER BADGE --}}
            @if($semester)
            <div class="flex items-center gap-2 mb-5">
                <span class="badge badge-blue">📅 {{ $semester->sem_name }}</span>
                <span class="text-[13px] text-slate-400">Current active semester</span>
            </div>
            @else
            <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-[13px]">
                ⚠️ No active semester found. Please activate a semester in Settings.
            </div>
            @endif

            {{-- TABS --}}
            <div class="flex gap-1 mb-5 bg-white border border-slate-200 rounded-xl p-1 w-fit">
                @foreach([
                    ['section', '🏫 By Section'],
                    ['teacher', '👨‍🏫 By Teacher'],
                    ['room',    '🚪 By Room'],
                ] as [$key, $label])
                <button
                    onclick="switchTab('{{ $key }}')"
                    id="tab-{{ $key }}"
                    class="px-5 py-2 rounded-lg text-[13px] font-semibold transition-all">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY SECTION
            ═════════════════════════════════════════════ --}}
            <div id="panel-section" class="tab-panel">
                <form method="GET" action="{{ route('admin.reports') }}" class="mb-4 flex flex-wrap items-center gap-2">
                    <input type="hidden" name="reports_tab" value="section">
                    <label class="sr-only" for="section-report-college">Filter by college</label>
                    <select id="section-report-college" name="section_college" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" onchange="document.getElementById('section-report-department').value=''; this.form.submit()">
                        <option value="">All colleges</option>
                        @foreach($colleges as $college)
                            <option value="{{ $college->college_id }}" @selected($sectionCollege === $college->college_id)>{{ $college->college_name }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="section-report-department">Filter by department</label>
                    <select id="section-report-department" name="section_department" class="w-full sm:w-48 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100" onchange="this.form.submit()" @disabled($departments->isEmpty())>
                        <option value="">All departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->dept_id }}" @selected($sectionDepartment === $department->dept_id)>{{ $department->dept_name }}</option>
                        @endforeach
                    </select>
                    <label class="sr-only" for="section-report-search">Search sections</label>
                    <input id="section-report-search" name="section_search" value="{{ request('section_search') }}" type="search" placeholder="Search section, department, or year…" class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <button class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Search</button>
                    @if(request('section_search') || $sectionCollege || $sectionDepartment)<a class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50" href="{{ route('admin.reports', ['reports_tab' => 'section']) }}">Clear</a>@endif
                </form>
                @if($bySection->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        {{ (request('section_search') || $sectionCollege || $sectionDepartment) ? 'No sections match these filters.' : 'No sections found.' }}
                    </div>
                @else
                <div class="mb-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Click a section to view its current-semester timetable.</span>
                    <span>{{ $bySection->total() }} sections</span>
                </div>
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach($bySection as $section)
                <article class="card border border-slate-200 shadow-sm">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="card-title">{{ $section->sec_name }}</div>
                            <div class="card-sub">{{ $section->program?->dept_code ?? 'N/A' }} · Year {{ $section->sec_year_level ?? '—' }}</div>
                        </div>
                        <span class="badge badge-blue">{{ $section->reportSchedules->pluck('subj_code')->filter()->unique()->count() }} subjects</span>
                    </div>
                    <div class="mt-3 text-sm text-slate-500">{{ $section->reportSchedules->count() }} scheduled class{{ $section->reportSchedules->count() === 1 ? '' : 'es' }} this semester</div>
                    <button type="button" data-report-schedule-url="{{ route('admin.reports.section-schedule', $section->sec_id) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                        <span aria-hidden="true">▦</span> View timetable
                    </button>
                </article>
                @endforeach
                </div>
                <div class="mt-4">{{ $bySection->appends(request()->except('section_page', 'room_page'))->links() }}</div>
                @endif
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY TEACHER
            ═════════════════════════════════════════════ --}}
            <div id="panel-teacher" class="tab-panel hidden">
                @php
                    $teacherGroups = collect([
                        ['title' => 'Full-time faculty', 'key' => 'full_time', 'faculty' => $byTeacher->filter(fn ($faculty) => $faculty->employment === 'full_time')],
                        ['title' => 'Part-time faculty', 'key' => 'part_time', 'faculty' => $byTeacher->filter(fn ($faculty) => $faculty->employment === 'part_time')],
                        ['title' => 'Other / unspecified employment', 'key' => 'other', 'faculty' => $byTeacher->filter(fn ($faculty) => !in_array($faculty->employment, ['full_time', 'part_time'], true))],
                    ])->filter(fn ($group) => $group['faculty']->isNotEmpty());
                @endphp
                <form method="GET" action="{{ route('admin.reports') }}" class="mb-4 flex flex-wrap items-center gap-2">
                    <input type="hidden" name="reports_tab" value="teacher">
                    @if(!$byTeacher->isEmpty())
                    <div class="flex flex-wrap gap-2 sm:mr-2" role="group" aria-label="Filter faculty by employment type">
                        <button type="button" data-teacher-filter="full_time" @disabled(!$teacherGroups->contains(fn ($group) => $group['key'] === 'full_time')) class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white disabled:opacity-50">Full-time</button>
                        <button type="button" data-teacher-filter="part_time" @disabled(!$teacherGroups->contains(fn ($group) => $group['key'] === 'part_time')) class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600 disabled:opacity-50">Part-time</button>
                        @if($teacherGroups->contains(fn ($group) => $group['key'] === 'other'))
                        <button type="button" data-teacher-filter="other" class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-600">Other</button>
                        @endif
                    </div>
                    @endif
                    <label class="sr-only" for="teacher-report-search">Search faculty</label>
                    <input id="teacher-report-search" name="teacher_search" value="{{ request('teacher_search') }}" type="search" placeholder="Search faculty, employee ID, college, or department…" class="w-full sm:w-64 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <button class="rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Search</button>
                    @if(request('teacher_search'))<a class="rounded-xl border border-slate-200 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50" href="{{ route('admin.reports', ['reports_tab' => 'teacher']) }}">Clear</a>@endif
                </form>
                @if($byTeacher->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        {{ request('teacher_search') ? 'No faculty match your search.' : 'No faculty records found.' }}
                    </div>
                @else
                <div class="mb-3 flex items-center justify-between text-xs text-slate-500">
                    <span>All active faculty records, including faculty with no plotted classes. {{ $workloadOverrideCount }} recorded workload override{{ $workloadOverrideCount === 1 ? '' : 's' }} in this result.</span>
                    <span>{{ $byTeacher->count() }} faculty</span>
                </div>
                @foreach($teacherGroups as $teacherGroup)
                <section class="mb-6" data-teacher-group="{{ $teacherGroup['key'] }}" aria-labelledby="teacher-group-{{ $teacherGroup['key'] }}">
                    <div class="mb-3 flex items-center gap-2">
                        <h3 id="teacher-group-{{ $teacherGroup['key'] }}" class="text-sm font-bold text-slate-800">{{ $teacherGroup['title'] }}</h3>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] font-semibold text-slate-500">{{ $teacherGroup['faculty']->count() }}</span>
                    </div>
                    <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach($teacherGroup['faculty'] as $facultyReport)
                    @php
                        $load = $facultyReport->load;
                        $over = $load['status'] === 'Over limit';
                        $badge = $over ? 'badge-red' : ($load['status'] === 'At limit' ? 'badge-amber' : 'badge-green');
                        $dayPct = $load['day_cap'] > 0 ? min(100, round($load['day_hours'] / $load['day_cap'] * 100)) : 0;
                        $eveningPct = $load['evening_cap'] > 0 ? min(100, round($load['evening_hours'] / $load['evening_cap'] * 100)) : 0;
                        $combinedPct = ($load['part_time_cap'] ?? 0) > 0 ? min(100, round($load['total_hours'] / $load['part_time_cap'] * 100)) : 0;
                    @endphp
                    <article data-teacher-card class="card border border-slate-200 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">
                                    {{ strtoupper(substr($facultyReport->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate font-bold text-slate-900">{{ $facultyReport->name }}</div>
                                    <div class="mt-0.5 text-xs text-slate-500">{{ $facultyReport->college }} · {{ $facultyReport->department }}</div>
                                    <div class="mt-1 text-[11px] text-slate-400">{{ $facultyReport->designation }} · {{ str_replace('_', ' ', ucfirst($facultyReport->employment)) }}</div>
                                </div>
                            </div>
                            <span class="badge {{ $badge }}">{{ $load['status'] }}</span>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Day workload</div>
                                <div class="mt-1 font-bold text-slate-800">{{ $load['day_hours'] }} <span class="font-normal text-slate-400">/ {{ $load['day_cap'] }} hrs</span></div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full {{ $load['day_hours'] > $load['day_cap'] ? 'bg-rose-500' : 'bg-emerald-500' }}" style="width:{{ $dayPct }}%"></div></div>
                            </div>
                            <div class="rounded-xl bg-slate-50 p-3">
                                <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Evening workload</div>
                                <div class="mt-1 font-bold text-slate-800">{{ $load['evening_hours'] }} <span class="font-normal text-slate-400">/ {{ $load['evening_cap'] }} hrs</span></div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full {{ $load['evening_hours'] > $load['evening_cap'] ? 'bg-rose-500' : 'bg-indigo-500' }}" style="width:{{ $eveningPct }}%"></div></div>
                            </div>
                        </div>
                        @if($load['is_part_time'])
                            <div class="mt-3 rounded-xl border border-cyan-100 bg-cyan-50/60 px-3 py-2 text-xs text-cyan-900">
                                Part-time shared cap: <strong>{{ $load['total_hours'] }} / {{ $load['part_time_cap'] }} hrs</strong>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-white"><div class="h-full rounded-full {{ $load['total_hours'] > $load['part_time_cap'] ? 'bg-rose-500' : 'bg-cyan-500' }}" style="width:{{ $combinedPct }}%"></div></div>
                            </div>
                        @endif
                        @if($facultyReport->workload_overrides->isNotEmpty())
                            <details class="mt-3 rounded-xl border border-rose-100 bg-rose-50/60 p-3">
                                <summary class="cursor-pointer text-xs font-bold text-rose-800">
                                    {{ $facultyReport->workload_overrides->count() }} recorded workload override{{ $facultyReport->workload_overrides->count() === 1 ? '' : 's' }} · View analytics
                                </summary>
                                <div class="mt-3 space-y-2">
                                    @foreach($facultyReport->workload_overrides->take(5) as $override)
                                        <div class="rounded-lg border border-rose-100 bg-white p-2.5 text-xs">
                                            <div class="font-bold text-rose-800">
                                                {{ $override->wlo_hours_after + 0 }} / {{ $override->wlo_cap_hours + 0 }} hours · {{ $override->wlo_designation ?: ucfirst($override->wlo_shift) }}
                                            </div>
                                            <div class="mt-1 text-slate-500">
                                                {{ $override->course?->course_code ?? 'Course' }} · {{ $override->section?->sec_name ?? 'Section not set' }} · {{ $override->created_at?->format('M j, Y') }}
                                            </div>
                                            <p class="mt-1 text-slate-700">{{ $override->wlo_reason }}</p>
                                            <div class="mt-1 text-slate-500">Approved by {{ $override->approvedBy?->usr_name ?? 'Unknown' }}</div>
                                        </div>
                                    @endforeach
                                    @if($facultyReport->workload_overrides->count() > 5)
                                        <div class="text-[11px] text-slate-500">Showing the latest 5 of {{ $facultyReport->workload_overrides->count() }} records.</div>
                                    @endif
                                </div>
                            </details>
                        @endif

                        <button type="button" data-faculty-schedule-url="{{ route('admin.reports.faculty-schedule', $facultyReport->fac_id) }}" data-faculty-name="{{ $facultyReport->name }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                            <span aria-hidden="true">▦</span> View timetable
                        </button>
                    </article>
                @endforeach
                    </div>
                    <div data-teacher-pagination class="mt-4 flex items-center justify-between text-sm text-slate-600">
                        <span data-teacher-page-label></span>
                        <div class="flex gap-2">
                            <button type="button" data-teacher-prev class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">Previous</button>
                            <button type="button" data-teacher-next class="rounded-lg border border-slate-200 px-3 py-1.5 hover:bg-slate-50">Next</button>
                        </div>
                    </div>
                </section>
                @endforeach
                @endif
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY ROOM
            ═════════════════════════════════════════════ --}}
            <div id="panel-room" class="tab-panel hidden">
                <form method="GET" action="{{ route('admin.reports') }}" class="mb-4 flex flex-wrap items-center gap-2">
                    <input type="hidden" name="reports_tab" value="room">
                    <label class="sr-only" for="room-report-search">Search rooms</label>
                    <input id="room-report-search" name="room_search" value="{{ request('room_search') }}" type="search" placeholder="Search room, building, or type…" class="min-w-[240px] flex-1 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <button class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700" type="submit">Search</button>
                    @if(request('room_search'))<a class="rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-600 hover:bg-slate-50" href="{{ route('admin.reports', ['reports_tab' => 'room']) }}">Clear</a>@endif
                </form>
                @if($byRoom->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        {{ request('room_search') ? 'No rooms match your search.' : 'No rooms found in the system.' }}
                    </div>
                @else
                <div class="mb-3 flex items-center justify-between text-xs text-slate-500">
                    <span>Click a room to view its current-semester timetable.</span>
                    <span>{{ $byRoom->total() }} rooms</span>
                </div>
                <div class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                @foreach($byRoom as $room)
                    <article class="card border border-slate-200 shadow-sm">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <div class="card-title">{{ $room->room_name }}</div>
                                <div class="card-sub mt-1 flex items-center gap-2">
                                    <span class="badge badge-grey">{{ ucfirst($room->room_type ?? 'N/A') }}</span>
                                    <span class="text-slate-400">Capacity: {{ $room->room_capacity ?? '—' }}</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="badge {{ $room->room_is_available ? 'badge-green' : 'badge-red' }}">{{ $room->room_is_available ? 'Available' : 'Unavailable' }}</span>
                                <span class="badge badge-blue">{{ $room->reportSchedules->count() }} booking{{ $room->reportSchedules->count() !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        <button type="button" data-report-schedule-url="{{ route('admin.reports.room-schedule', $room->room_id) }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">
                            <span aria-hidden="true">▦</span> View timetable
                        </button>
                    </article>
                @endforeach
                </div>
                <div class="mt-4">{{ $byRoom->appends(request()->except('section_page', 'room_page'))->links() }}</div>
                @endif
            </div>

        </div>{{-- end page-content --}}
    </div>{{-- end app-main --}}
</div>{{-- end app-shell --}}

<div id="faculty-timetable-modal" class="fixed inset-0 z-[1500] hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="faculty-timetable-title" onclick="if(event.target===this) closeFacultyTimetable()">
    <section class="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <header class="flex items-start justify-between gap-4 border-b border-slate-100 px-5 py-4">
            <div class="min-w-0">
                <h2 id="faculty-timetable-title" class="truncate text-lg font-bold text-slate-900">Timetable</h2>
                <p id="faculty-timetable-term" class="mt-1 text-xs text-slate-500">Loading schedule…</p>
            </div>
            <button type="button" onclick="closeFacultyTimetable()" class="rounded-lg px-3 py-1 text-xl text-slate-400 hover:bg-slate-100" aria-label="Close timetable">×</button>
        </header>
        <div class="grid grid-cols-1 gap-3 border-b border-slate-100 bg-slate-50 p-4 sm:grid-cols-3">
            <div class="rounded-xl bg-white p-3"><div id="timetable-stat-1-label" class="text-[10px] font-bold uppercase text-slate-400">Summary</div><div id="timetable-stat-1-value" class="mt-1 font-bold text-slate-800">—</div></div>
            <div class="rounded-xl bg-white p-3"><div id="timetable-stat-2-label" class="text-[10px] font-bold uppercase text-slate-400">Summary</div><div id="timetable-stat-2-value" class="mt-1 font-bold text-slate-800">—</div></div>
            <div class="rounded-xl bg-white p-3"><div id="timetable-stat-3-label" class="text-[10px] font-bold uppercase text-slate-400">Summary</div><div id="timetable-stat-3-value" class="mt-1 font-bold text-slate-800">—</div></div>
        </div>
        <p id="faculty-timetable-error" class="hidden m-4 rounded-lg bg-rose-50 px-3 py-2 text-sm text-rose-700"></p>
        <div class="min-h-0 flex-1 overflow-auto p-4">
            <table class="data-table min-w-[760px]">
                <thead><tr><th>Day</th><th>Time</th><th>Course</th><th>Section</th><th>Room</th><th>Faculty</th></tr></thead>
                <tbody id="faculty-timetable-rows"><tr><td colspan="6" class="py-8 text-center text-slate-400">Loading timetable…</td></tr></tbody>
            </table>
        </div>
    </section>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── TABS ──────────────────────────────────────────────────────────────────
const TABS = ['section','teacher','room'];

function switchTab(active) {
    TABS.forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== active);
        const btn = document.getElementById('tab-' + t);
        if (t === active) {
            btn.classList.add('bg-blue-600','text-white','shadow-sm');
            btn.classList.remove('text-slate-500','hover:bg-slate-100');
        } else {
            btn.classList.remove('bg-blue-600','text-white','shadow-sm');
            btn.classList.add('text-slate-500','hover:bg-slate-100');
        }
    });
    localStorage.setItem('reports_tab', active);
}

// Restore last active tab
const requestedTab = document.body.dataset.activeTab;
const savedTab = localStorage.getItem('reports_tab') || 'section';
switchTab(TABS.includes(requestedTab) ? requestedTab : savedTab);

// ── FACULTY EMPLOYMENT FILTER AND PAGINATION ─────────────────────────────
const teacherFilterButtons = [...document.querySelectorAll('[data-teacher-filter]')];
const teacherGroups = [...document.querySelectorAll('[data-teacher-group]')];
let activeTeacherFilter = teacherFilterButtons.find(button => button.dataset.teacherFilter === 'full_time' && !button.disabled)?.dataset.teacherFilter
    || teacherFilterButtons.find(button => !button.disabled)?.dataset.teacherFilter;

function renderTeacherGroups() {
    teacherGroups.forEach(group => {
        const visible = group.dataset.teacherGroup === activeTeacherFilter;
        group.classList.toggle('hidden', !visible);
        if (!visible) return;

        const cards = [...group.querySelectorAll('[data-teacher-card]')];
        const pageSize = 6;
        const pageCount = Math.max(1, Math.ceil(cards.length / pageSize));
        group.dataset.page = Math.min(Number(group.dataset.page || 1), pageCount);
        const page = Number(group.dataset.page);
        cards.forEach((card, index) => card.classList.toggle('hidden', index < (page - 1) * pageSize || index >= page * pageSize));
        group.querySelector('[data-teacher-page-label]').textContent = `Page ${page} of ${pageCount} · ${cards.length} faculty`;
        group.querySelector('[data-teacher-prev]').disabled = page <= 1;
        group.querySelector('[data-teacher-next]').disabled = page >= pageCount;
        group.querySelector('[data-teacher-pagination]').classList.toggle('hidden', cards.length <= pageSize);
    });

    teacherFilterButtons.forEach(button => {
        const selected = button.dataset.teacherFilter === activeTeacherFilter;
        button.classList.toggle('bg-blue-600', selected);
        button.classList.toggle('text-white', selected);
        button.classList.toggle('border', !selected);
        button.classList.toggle('border-slate-200', !selected);
        button.classList.toggle('bg-white', !selected);
        button.classList.toggle('text-slate-600', !selected);
    });
}

teacherFilterButtons.forEach(button => button.addEventListener('click', () => {
    activeTeacherFilter = button.dataset.teacherFilter;
    renderTeacherGroups();
}));
teacherGroups.forEach(group => {
    group.querySelector('[data-teacher-prev]').addEventListener('click', () => {
        group.dataset.page = Math.max(1, Number(group.dataset.page || 1) - 1);
        renderTeacherGroups();
    });
    group.querySelector('[data-teacher-next]').addEventListener('click', () => {
        group.dataset.page = Number(group.dataset.page || 1) + 1;
        renderTeacherGroups();
    });
});
if (teacherGroups.length) renderTeacherGroups();

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ── PRINT ──────────────────────────────────────────────────────────────────
function printReport() { window.print(); }

// Load one faculty member's schedule only when the admin opens it.
const facultyTimetableModal = document.getElementById('faculty-timetable-modal');
const facultyTimetableRows = document.getElementById('faculty-timetable-rows');
const facultyTimetableError = document.getElementById('faculty-timetable-error');

function closeFacultyTimetable() {
    facultyTimetableModal.classList.add('hidden');
    facultyTimetableModal.classList.remove('flex');
}

function addTimetableCell(row, value, className = '') {
    const cell = document.createElement('td');
    cell.textContent = value || '—';
    if (className) cell.className = className;
    row.appendChild(cell);
}

async function openReportTimetable(url, fallbackTitle = 'Timetable', facultyName = null) {
    facultyTimetableModal.classList.remove('hidden');
    facultyTimetableModal.classList.add('flex');
    document.getElementById('faculty-timetable-title').textContent = fallbackTitle;
    document.getElementById('faculty-timetable-term').textContent = 'Loading current semester…';
    facultyTimetableError.classList.add('hidden');
    facultyTimetableRows.replaceChildren();
    const loadingRow = document.createElement('tr');
    const loadingCell = document.createElement('td');
    loadingCell.colSpan = 6;
    loadingCell.className = 'py-8 text-center text-slate-400';
    loadingCell.textContent = 'Loading timetable…';
    loadingRow.appendChild(loadingCell);
    facultyTimetableRows.appendChild(loadingRow);

    try {
        const response = await fetch(url, { headers: { Accept: 'application/json' } });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Could not load this timetable.');

        document.getElementById('faculty-timetable-title').textContent = result.title || (facultyName ? facultyName + ' — Timetable' : fallbackTitle);
        document.getElementById('faculty-timetable-term').textContent = (result.department ? result.department + ' · ' : '') + (result.subtitle || result.semester || 'Current semester');
        let stats = result.stats;
        if (!stats && result.load) {
            stats = [
                { label: 'Day workload', value: `${result.load.day_hours} / ${result.load.day_cap} hrs` },
                { label: 'Evening workload', value: `${result.load.evening_hours} / ${result.load.evening_cap} hrs` },
                { label: 'Total workload', value: result.load.is_part_time ? `${result.load.total_hours} / ${result.load.part_time_cap} shared hrs` : `${result.load.total_hours} hrs total` },
            ];
        }
        (stats || []).forEach((stat, index) => {
            const n = index + 1;
            document.getElementById(`timetable-stat-${n}-label`).textContent = stat.label;
            document.getElementById(`timetable-stat-${n}-value`).textContent = stat.value;
        });
        facultyTimetableRows.replaceChildren();

        if (!result.schedules?.length) {
            const row = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = 6;
            cell.className = 'py-8 text-center text-slate-400';
            cell.textContent = 'No active classes for this item in the current semester.';
            row.appendChild(cell);
            facultyTimetableRows.appendChild(row);
            return;
        }

        result.schedules.forEach(schedule => {
            const row = document.createElement('tr');
            addTimetableCell(row, schedule.day, 'font-semibold');
            addTimetableCell(row, schedule.time, 'whitespace-nowrap font-mono text-xs');
            const courseCell = document.createElement('td');
            const code = document.createElement('div');
            code.className = 'font-mono text-xs text-slate-500';
            code.textContent = schedule.course_code;
            const name = document.createElement('div');
            name.className = 'font-semibold';
            name.textContent = schedule.course_name;
            courseCell.append(code, name);
            row.appendChild(courseCell);
            addTimetableCell(row, schedule.section);
            addTimetableCell(row, schedule.room);
            addTimetableCell(row, schedule.faculty || facultyName || '—');
            facultyTimetableRows.appendChild(row);
        });
    } catch (error) {
        facultyTimetableRows.replaceChildren();
        facultyTimetableError.textContent = error.message;
        facultyTimetableError.classList.remove('hidden');
        document.getElementById('faculty-timetable-term').textContent = 'Unable to load timetable';
    }
}

document.querySelectorAll('[data-faculty-schedule-url]').forEach(button => {
    button.addEventListener('click', async () => {
        await openReportTimetable(button.dataset.facultyScheduleUrl, 'Faculty timetable', button.dataset.facultyName);
    });
});

document.querySelectorAll('[data-report-schedule-url]').forEach(button => {
    button.addEventListener('click', () => openReportTimetable(button.dataset.reportScheduleUrl));
});

// ── EXPORT CSV ────────────────────────────────────────────────────────────
function exportCSV() {
    const activeTab = localStorage.getItem('reports_tab') || 'section';
    const panel = document.getElementById('panel-' + activeTab);
    const tables = panel.querySelectorAll('table');
    if (!tables.length) { showToast('No data to export.'); return; }

    let csv = '';
    tables.forEach(table => {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cols = [...row.querySelectorAll('th,td')].map(c => `"${c.innerText.trim()}"`);
            csv += cols.join(',') + '\n';
        });
        csv += '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `skedyul-report-by-${activeTab}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    showToast('Report exported as CSV!');
}
</script>
</body>
</html>
