<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Dean PBS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
@php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $shift = request()->query('shift', 'day') === 'night' ? 'night' : 'day';
    $gridStart = $shift === 'night' ? 16 * 60 : 7 * 60;
    $gridEnd = $shift === 'night' ? 21 * 60 + 30 : 16 * 60;
    $slotMinutes = 30;
    $slotCount = intdiv($gridEnd - $gridStart, $slotMinutes);
    $noonStart = 12 * 60;
    $noonEnd = 13 * 60;
    $showNoonBreak = $shift === 'day';
    $visibleSchedules = $schedules
        ->filter(function ($schedule) use ($shift) {
            $storedShift = strtolower((string) ($schedule->sch_shift ?? 'day'));
            return $shift === 'night'
                ? in_array($storedShift, ['evening', 'night'], true)
                : !in_array($storedShift, ['evening', 'night'], true);
        })
        ->values();
    $occupied = [];
    foreach ($visibleSchedules as $schedule) {
        $dayIndex = array_search(ucfirst(strtolower((string) $schedule->sch_day)), $days, true);
        if ($dayIndex === false) {
            continue;
        }
        $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
        $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
        $startMinutes = max($start->hour * 60 + $start->minute, $gridStart);
        $endMinutes = min($end->hour * 60 + $end->minute, $gridEnd);
        for ($minute = $startMinutes; $minute < $endMinutes; $minute += $slotMinutes) {
            $occupied[$dayIndex . '-' . intdiv($minute - $gridStart, $slotMinutes)] = true;
        }
    }
    $programsForPicker = $departments
        ->filter(fn($department) => $sections->contains('sec_dept_id', $department->dept_id))
        ->values();
    $selectedProgramId = $selectedSection?->sec_dept_id ?? request()->query('program_id');
    if (!$programsForPicker->contains('dept_id', $selectedProgramId)) {
        $selectedProgramId = null;
    }
    $programSections = $selectedProgramId
        ? $sections->where('sec_dept_id', $selectedProgramId)->sortBy('sec_name')->values()
        : collect();
@endphp

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.dean_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="topbar">
                <div class="topbar-title">Program by Section (PBS)</div>
                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Dean · View
                    only</span>
            </header>
            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="mb-1 text-sm font-semibold uppercase tracking-wide text-blue-600">Schedule viewer
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight">Program by Section</h1>
                            <p class="mt-1 text-sm text-slate-500">View the department’s weekly section timetable. This
                                page is read only.</p>
                        </div>
                        <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                            <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">Active semester
                            </div>
                            <div class="mt-1 text-sm font-bold text-slate-800">
                                @if ($semester)
                                    {{ $semester->sem_name }}
                                    @if ($semester->academicYear?->ay_academic_year)
                                        · AY {{ $semester->academicYear->ay_academic_year }}
                                    @elseif ($semester->academicYear?->ay_year_label)
                                        · AY {{ $semester->academicYear->ay_year_label }}
                                    @endif
                                @else
                                    No active semester
                                @endif
                            </div>
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                            role="alert">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <section class="mb-4 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">
                            @foreach ($programsForPicker as $program)
                                @php
                                    $programCode = strtoupper(
                                        preg_replace('/[^A-Z0-9]/', '', (string) $program->dept_code),
                                    );
                                    $programLabel = match ($programCode) {
                                        'IT' => 'BSIT',
                                        'IS' => 'BSIS',
                                        'BITCT' => 'BIT CT',
                                        default => strtoupper((string) $program->dept_code),
                                    };
                                    $programSectionCount = $sections->where('sec_dept_id', $program->dept_id)->count();
                                @endphp
                                <a href="{{ route('dean.pbs', ['program_id' => $program->dept_id, 'shift' => $shift]) }}"
                                    @if ((string) $selectedProgramId === (string) $program->dept_id) aria-current="true" @endif
                                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-bold transition {{ (string) $selectedProgramId === (string) $program->dept_id ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:text-blue-700' }}">
                                    {{ $programLabel }}
                                    <span
                                        class="rounded-full px-1.5 py-0.5 text-[10px] {{ (string) $selectedProgramId === (string) $program->dept_id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $programSectionCount }}</span>
                                </a>
                            @endforeach
                        </div>
                        <form method="GET" action="{{ route('dean.pbs') }}"
                            class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">
                            <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                            <input type="hidden" name="shift" value="{{ $shift }}">
                            <div id="dean-pbs-section-picker" class="relative min-w-[210px]">
                                <input type="hidden" id="section_id" name="section_id"
                                    value="{{ $selectedSection?->sec_id ?? '' }}">
                                <button type="button" id="dean-pbs-section-trigger" aria-haspopup="dialog"
                                    aria-expanded="false" aria-controls="dean-pbs-section-menu"
                                    @disabled(!$selectedProgramId)
                                    class="flex w-full items-center gap-2 rounded-lg border border-blue-200 bg-white px-3 py-2 text-left text-xs shadow-sm transition hover:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-full {{ $selectedSection ? ($selectedSection->sec_is_evening ? 'bg-violet-500' : 'bg-amber-500') : 'bg-slate-300' }}"></span>
                                    <span id="dean-pbs-section-label"
                                        class="max-w-[150px] truncate font-bold text-slate-800">
                                        {{ $selectedSection?->sec_name ?? ($selectedProgramId ? 'Select section' : 'Choose a program first') }}
                                    </span>
                                    <span
                                        class="ml-auto rounded bg-slate-100 px-1.5 py-0.5 text-[9px] font-bold text-slate-500">
                                        {{ $selectedSection ? ($selectedSection->sec_is_evening ? 'Evening' : 'Day') : 'Browse' }}
                                    </span>
                                    <svg class="h-3.5 w-3.5 shrink-0 text-slate-400" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.22 7.22a.75.75 0 0 1 1.06 0L10 10.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.28a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="dean-pbs-section-menu" role="dialog" aria-label="Choose a section"
                                    class="absolute left-0 top-full z-[120] mt-2 hidden max-h-[min(80vh,620px)] w-[min(92vw,440px)] max-w-[calc(100vw-1rem)] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                                    <div class="border-b border-slate-100 p-3">
                                        <label for="dean-pbs-section-search" class="sr-only">Search section name</label>
                                        <input id="dean-pbs-section-search" type="search"
                                            placeholder="Search section name..." autocomplete="off"
                                            class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                                    </div>
                                    <div class="border-b border-slate-100 px-3 py-2">
                                        <div class="flex rounded-lg bg-slate-100 p-1" role="group"
                                            aria-label="Filter sections by schedule">
                                            <button type="button" data-dean-section-shift="all"
                                                class="flex-1 rounded-md bg-white px-2 py-1.5 text-[10px] font-semibold text-slate-700 shadow-sm">All
                                                Sections <span data-dean-shift-count="all"
                                                    class="ml-0.5 text-[9px] opacity-70"></span></button>
                                            <button type="button" data-dean-section-shift="day"
                                                class="flex-1 rounded-md px-2 py-1.5 text-[10px] font-semibold text-slate-500">☀
                                                Day <span data-dean-shift-count="day"
                                                    class="ml-0.5 text-[9px] opacity-70"></span></button>
                                            <button type="button" data-dean-section-shift="evening"
                                                class="flex-1 rounded-md px-2 py-1.5 text-[10px] font-semibold text-slate-500">☾
                                                Evening <span data-dean-shift-count="evening"
                                                    class="ml-0.5 text-[9px] opacity-70"></span></button>
                                        </div>
                                        <div class="mt-2 flex flex-wrap gap-1" role="group"
                                            aria-label="Filter sections by year">
                                            @foreach ([['all', 'All Years'], ['1', '1st Year'], ['2', '2nd Year'], ['3', '3rd Year'], ['4', '4th Year'], ['5', '5th Year']] as [$yearValue, $yearText])
                                                <button type="button" data-dean-section-year="{{ $yearValue }}"
                                                    class="rounded-full border border-slate-200 px-2 py-1 text-[9px] font-semibold text-slate-500 transition {{ $yearValue === 'all' ? 'border-blue-200 bg-blue-50 text-blue-700' : '' }}">{{ $yearText }}
                                                    <span data-dean-year-count="{{ $yearValue }}"
                                                        class="opacity-70"></span></button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div id="dean-pbs-section-list" class="max-h-[min(42vh,340px)] overflow-y-auto p-2">
                                        @foreach ([['day', 'Day Sections'], ['evening', 'Evening Sections']] as [$groupShift, $groupTitle])
                                            @php
                                                $groupSections = $programSections->filter(
                                                    fn($section) => ($section->sec_is_evening ? 'evening' : 'day') ===
                                                        $groupShift,
                                                );
                                            @endphp
                                            <section data-dean-section-group="{{ $groupShift }}"
                                                class="mb-2 last:mb-0">
                                                <div
                                                    class="sticky top-0 z-10 flex items-center justify-between bg-white px-2 py-1.5 text-[9px] font-extrabold uppercase tracking-wide {{ $groupShift === 'day' ? 'text-amber-700' : 'text-violet-700' }}">
                                                    <span>{{ $groupShift === 'day' ? '☀' : '☾' }}
                                                        {{ $groupTitle }}</span>
                                                    <span data-dean-group-count="{{ $groupShift }}"
                                                        class="rounded bg-slate-100 px-1.5 py-0.5 text-slate-500">{{ $groupSections->count() }}</span>
                                                </div>
                                                @foreach ($groupSections as $section)
                                                    @php
                                                        $yearNumber = (int) ($section->sec_year_level ?? 0);
                                                        $yearLabel = match ($yearNumber) {
                                                            1 => '1st Year',
                                                            2 => '2nd Year',
                                                            3 => '3rd Year',
                                                            4 => '4th Year',
                                                            5 => '5th Year',
                                                            default => 'Year not set',
                                                        };
                                                    @endphp
                                                    <button type="button" data-dean-section-option
                                                        data-section-id="{{ $section->sec_id }}"
                                                        data-shift="{{ $groupShift }}"
                                                        data-year="{{ $yearNumber }}"
                                                        data-search="{{ strtolower($section->sec_name . ' ' . $yearLabel) }}"
                                                        class="mb-1 flex w-full items-center gap-2 rounded-lg border border-transparent px-2.5 py-2 text-left text-slate-700 transition hover:border-blue-100 hover:bg-blue-50">
                                                        <span
                                                            class="h-2 w-2 shrink-0 rounded-full {{ $groupShift === 'day' ? 'bg-amber-500' : 'bg-violet-500' }}"></span>
                                                        <span class="min-w-0 flex-1">
                                                            <span
                                                                class="block truncate text-[11px] font-bold">{{ $section->sec_name }}</span>
                                                            <span
                                                                class="mt-0.5 block text-[9px] text-slate-400">{{ $yearLabel }}
                                                                ·
                                                                {{ $section->sec_no_of_student !== null ? $section->sec_no_of_student . ' students' : 'Student count not set' }}</span>
                                                        </span>
                                                        <span
                                                            class="shrink-0 text-[9px] font-semibold text-slate-400">{{ $groupShift === 'day' ? 'Day' : 'Evening' }}</span>
                                                    </button>
                                                @endforeach
                                            </section>
                                        @endforeach
                                        <div id="dean-pbs-section-empty"
                                            class="hidden px-3 py-8 text-center text-xs text-slate-400">No sections
                                            match that search.</div>
                                    </div>
                                    <div
                                        class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-3 py-2 text-[9px] text-slate-500">
                                        <span>Choose a section to open its timetable</span><span>Day / Evening ·
                                            Year</span>
                                    </div>
                                </div>
                            </div>
                            <span
                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600">
                                {{ $semester?->sem_name ?? 'No active semester' }}
                                @if ($semester?->academicYear?->ay_academic_year)
                                    , AY {{ $semester->academicYear->ay_academic_year }}
                                @elseif ($semester?->academicYear?->ay_year_label)
                                    , AY {{ $semester->academicYear->ay_year_label }}
                                @endif
                            </span>
                            <div class="ml-auto flex overflow-hidden rounded-lg border border-slate-300 bg-white">
                                <a href="{{ route('dean.pbs', $selectedSection ? ['section_id' => $selectedSection->sec_id, 'program_id' => $selectedProgramId, 'shift' => 'day'] : ['program_id' => $selectedProgramId, 'shift' => 'day']) }}"
                                    class="px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'day' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Day</a>
                                <a href="{{ route('dean.pbs', $selectedSection ? ['section_id' => $selectedSection->sec_id, 'program_id' => $selectedProgramId, 'shift' => 'night'] : ['program_id' => $selectedProgramId, 'shift' => 'night']) }}"
                                    class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'night' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Evening</a>
                            </div>
                        </form>

                        @if (!$semester)
                            <div class="border-b border-amber-100 bg-amber-50 px-4 py-2.5 text-sm text-amber-800">No
                                active semester is set.</div>
                        @elseif (!$selectedSection)
                            <div class="border-b border-slate-200 bg-blue-50/50 px-4 py-2.5 text-sm text-slate-600">
                                Choose a program and section above to load its timetable preview.</div>
                        @endif
                        @if ($selectedSection)
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-extrabold">{{ $selectedSection->sec_name }}</h2>
                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700">{{ $selectedSection->program?->dept_code ?? 'Department' }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ $visibleSchedules->count() }} scheduled
                                        {{ $visibleSchedules->count() === 1 ? 'class' : 'classes' }} · View only</p>
                                </div>
                                @if ($schedules->isNotEmpty())
                                    <a data-export-preview
                                        href="{{ route('dean.pbs.export.preview', ['section' => $selectedSection->sec_id, 'semester' => $semester->sem_id, 'shift' => $shift]) }}"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Preview
                                        Export</a>
                                @endif
                            </div>
                        @endif
                        <div class="overflow-x-auto">
                            <div class="min-w-[920px]">
                                <div class="grid border-b border-slate-200 bg-slate-100"
                                    style="grid-template-columns:90px repeat(7,minmax(0,1fr));">
                                    <div
                                        class="px-2 py-2 text-center text-[10.5px] font-bold uppercase tracking-wide text-slate-500">
                                        Time</div>
                                    @foreach ($days as $day)
                                        <div
                                            class="border-l border-slate-200 px-2 py-2 text-center text-[11.5px] font-bold text-slate-700">
                                            {{ $day }}</div>
                                    @endforeach
                                </div>
                                <div class="relative grid"
                                    style="grid-template-columns:90px repeat(7,minmax(0,1fr));grid-template-rows:repeat({{ $slotCount }},26px);">
                                    @for ($i = 0; $i < $slotCount; $i += 2)
                                        @php
                                            $minutes = $gridStart + $i * $slotMinutes;
                                            $hourStart = \Illuminate\Support\Carbon::createFromTime(
                                                intdiv($minutes, 60),
                                                0,
                                            );
                                            $hourEnd = (clone $hourStart)->addHour();
                                            $timeLabel =
                                                $hourStart->format('g') .
                                                '-' .
                                                $hourEnd->format('g') .
                                                ' ' .
                                                $hourEnd->format('A');
                                        @endphp
                                        <div class="flex items-center justify-end border-b border-slate-200 bg-slate-50 px-2 text-[10.5px] font-semibold text-slate-500"
                                            style="grid-column:1;grid-row:{{ $i + 1 }} / span 2;">
                                            {{ $timeLabel }}</div>
                                    @endfor

                                    @for ($i = 0; $i < $slotCount; $i++)
                                        @php
                                            $minutes = $gridStart + $i * $slotMinutes;
                                        @endphp
                                        @for ($dayIndex = 0; $dayIndex < count($days); $dayIndex++)
                                            <div class="border-b border-l border-slate-100 {{ $minutes % 60 === 0 ? '!border-b-slate-200' : '' }}"
                                                style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $i + 1 }};">
                                            </div>
                                        @endfor
                                    @endfor

                                    @if ($showNoonBreak)
                                        @php
                                            $noonRow = intdiv($noonStart - $gridStart, $slotMinutes) + 1;
                                            $noonSpan = intdiv($noonEnd - $noonStart, $slotMinutes);
                                        @endphp
                                        <div class="z-10 flex items-center justify-center bg-slate-900 text-[12px] font-bold tracking-[3px] text-white"
                                            style="grid-column:2 / span 7;grid-row:{{ $noonRow }} / span {{ $noonSpan }};">
                                            NOON BREAK</div>
                                    @endif

                                    @foreach ($visibleSchedules as $schedule)
                                        @php
                                            $dayIndex = array_search(
                                                ucfirst(strtolower((string) $schedule->sch_day)),
                                                $days,
                                                true,
                                            );
                                            $startTime = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                                            $endTime = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                                            $startMinutes = max($startTime->hour * 60 + $startTime->minute, $gridStart);
                                            $endMinutes = min($endTime->hour * 60 + $endTime->minute, $gridEnd);
                                            $row = intdiv($startMinutes - $gridStart, $slotMinutes) + 1;
                                            $span = max(1, (int) ceil(($endMinutes - $startMinutes) / $slotMinutes));
                                            $course = $schedule->subject?->course_code ?? '—';
                                            $teacherLastName = $schedule->faculty?->fac_last_name;
                                            $teacherPrefix = filled($schedule->faculty?->fac_doctorate_degree)
                                                ? 'Dr.'
                                                : 'Prof.';
                                        @endphp
                                        @if ($dayIndex !== false && $endMinutes > $startMinutes)
                                            <div class="relative z-20 m-[3px] flex flex-col overflow-hidden rounded-md border border-emerald-600 bg-emerald-500 text-white shadow-sm"
                                                style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $row }} / span {{ $span }};">
                                                <div
                                                    class="flex flex-1 flex-col items-center justify-center px-2 py-1 text-center leading-snug">
                                                    <div class="text-[13px] font-extrabold tracking-wide">
                                                        {{ $course }}</div>
                                                    <div class="mt-1 text-[11.5px] font-semibold">
                                                        {{ $schedule->faculty ? $teacherPrefix . ' ' . $teacherLastName : 'Faculty not assigned' }}
                                                    </div>
                                                    <div class="mt-0.5 text-[11.5px] font-medium">
                                                        {{ $schedule->room?->room_name ?? 'Room not assigned' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </section>
                    <section
                        class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm print-card">
                        <table class="w-full min-w-[720px] border-collapse text-center text-[12px]">
                            <thead>
                                <tr class="text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                    <th class="w-1/3 border-b border-slate-300 px-4 py-2">Prepared by:</th>
                                    <th class="w-1/3 border-b border-l border-slate-300 px-4 py-2">Reviewed, Certified
                                        True and Correct:</th>
                                    <th class="w-1/3 border-b border-l border-slate-300 px-4 py-2">Approved by:</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 pb-3 pt-8 align-bottom">
                                        <div class="border-b border-slate-800 pb-1 font-bold uppercase">
                                            {{ $signatories['chair_name'] ?: '—' }}</div>
                                        <div class="pt-1 text-slate-600">{{ $signatories['chair_title'] }}</div>
                                    </td>
                                    <td class="border-l border-slate-200 px-4 pb-3 pt-8 align-bottom">
                                        <div class="border-b border-slate-800 pb-1 font-bold uppercase">
                                            {{ $signatories['dean_name'] ?: '—' }}</div>
                                        <div class="pt-1 text-slate-600">{{ $signatories['dean_title'] }}</div>
                                    </td>
                                    <td class="border-l border-slate-200 px-4 pb-3 pt-8 align-bottom">
                                        <div class="border-b border-slate-800 pb-1 font-bold uppercase">
                                            {{ $signatories['campus_director'] ?: '—' }}</div>
                                        <div class="pt-1 text-slate-600">Campus Director</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </section>
                </div>
            </div>
        </main>
    </div>
    @include('chair.partials.export_preview_dialog')
    <script>
        (() => {
            const root = document.getElementById('dean-pbs-section-picker');
            const trigger = document.getElementById('dean-pbs-section-trigger');
            const menu = document.getElementById('dean-pbs-section-menu');
            const search = document.getElementById('dean-pbs-section-search');
            const list = document.getElementById('dean-pbs-section-list');
            const sectionInput = document.getElementById('section_id');
            const form = sectionInput?.form;
            if (!root || !trigger || !menu || !search || !list || !sectionInput || !form) return;

            let shiftFilter = 'all';
            let yearFilter = 'all';
            const options = () => Array.from(list.querySelectorAll('[data-dean-section-option]'));

            const render = () => {
                const query = search.value.trim().toLocaleLowerCase();
                const allOptions = options();
                let visibleCount = 0;
                allOptions.forEach((option) => {
                    const matches = option.dataset.search.includes(query) &&
                        (shiftFilter === 'all' || option.dataset.shift === shiftFilter) &&
                        (yearFilter === 'all' || option.dataset.year === yearFilter);
                    option.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });

                list.querySelectorAll('[data-dean-section-group]').forEach((group) => {
                    const visible = group.querySelectorAll('[data-dean-section-option]:not(.hidden)')
                    .length;
                    group.classList.toggle('hidden', visible === 0);
                    group.querySelector('[data-dean-group-count]')?.replaceChildren(String(visible));
                });
                document.getElementById('dean-pbs-section-empty')?.classList.toggle('hidden', visibleCount > 0);

                ['all', 'day', 'evening'].forEach((filter) => {
                    const count = allOptions.filter((option) => filter === 'all' || option.dataset.shift ===
                        filter).length;
                    document.querySelector('[data-dean-shift-count="' + filter + '"]')?.replaceChildren(
                        String(count));
                });
                ['all', '1', '2', '3', '4', '5'].forEach((filter) => {
                    const count = allOptions.filter((option) => filter === 'all' || option.dataset.year ===
                        filter).length;
                    document.querySelector('[data-dean-year-count="' + filter + '"]')?.replaceChildren(
                        String(count));
                });
            };

            const setOpen = (open) => {
                menu.classList.toggle('hidden', !open);
                trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                if (open) {
                    render();
                    search.focus();
                }
            };

            trigger.addEventListener('click', () => setOpen(menu.classList.contains('hidden')));
            search.addEventListener('input', render);
            menu.querySelectorAll('[data-dean-section-shift]').forEach((button) => {
                button.addEventListener('click', () => {
                    shiftFilter = button.dataset.deanSectionShift;
                    menu.querySelectorAll('[data-dean-section-shift]').forEach((item) => {
                        const active = item === button;
                        item.classList.toggle('bg-white', active);
                        item.classList.toggle('shadow-sm', active);
                        item.classList.toggle('text-slate-700', active);
                        item.classList.toggle('text-slate-500', !active);
                    });
                    render();
                });
            });
            menu.querySelectorAll('[data-dean-section-year]').forEach((button) => {
                button.addEventListener('click', () => {
                    yearFilter = button.dataset.deanSectionYear;
                    menu.querySelectorAll('[data-dean-section-year]').forEach((item) => {
                        const active = item === button;
                        item.classList.toggle('border-blue-200', active);
                        item.classList.toggle('bg-blue-50', active);
                        item.classList.toggle('text-blue-700', active);
                        item.classList.toggle('border-slate-200', !active);
                        item.classList.toggle('text-slate-500', !active);
                    });
                    render();
                });
            });
            list.addEventListener('click', (event) => {
                const option = event.target.closest('[data-dean-section-option]');
                if (!option) return;
                sectionInput.value = option.dataset.sectionId;
                form.requestSubmit();
            });
            document.addEventListener('click', (event) => {
                if (!root.contains(event.target)) setOpen(false);
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !menu.classList.contains('hidden')) setOpen(false);
            });
            render();
        })();
    </script>
</body>

</html>
