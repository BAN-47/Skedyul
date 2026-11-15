<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Dean PBT</title>
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
    $programOrder = ['BSIT' => 1, 'IT' => 1, 'BSIS' => 2, 'IS' => 2, 'BITCT' => 3];
    $programsForPicker = $facultyMembers
        ->map(fn($faculty) => $faculty->program)
        ->filter()
        ->unique('dept_id')
        ->sortBy(function ($program) use ($programOrder) {
            $code = strtoupper(preg_replace('/[^A-Z0-9]/', '', (string) $program->dept_code));
            return $programOrder[$code] ?? 99;
        })
        ->values();
    $selectedProgramId = $selectedFaculty?->fac_dept_id ?? request()->query('program_id');
    if (!$programsForPicker->contains('dept_id', $selectedProgramId)) {
        $selectedProgramId = null;
    }
    $programFacultyMembers = $selectedProgramId
        ? $facultyMembers->where('fac_dept_id', $selectedProgramId)->values()
        : collect();
@endphp

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.dean_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="topbar">
                <div class="topbar-title">Program by Teacher (PBT)</div>
                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Dean · View
                    only</span>
            </header>
            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="mb-1 text-sm font-semibold uppercase tracking-wide text-blue-600">Schedule viewer
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight">Program by Teacher</h1>
                            <p class="mt-1 text-sm text-slate-500">View the faculty member’s weekly timetable. This page
                                is read only.</p>
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
                                    $programFacultyCount = $facultyMembers
                                        ->where('fac_dept_id', $program->dept_id)
                                        ->count();
                                @endphp
                                <a href="{{ route('dean.pbt', ['program_id' => $program->dept_id, 'shift' => $shift]) }}"
                                    @if ((string) $selectedProgramId === (string) $program->dept_id) aria-current="true" @endif
                                    class="inline-flex items-center gap-2 rounded-lg border px-3 py-2 text-xs font-bold transition {{ (string) $selectedProgramId === (string) $program->dept_id ? 'border-blue-600 bg-blue-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-blue-300 hover:text-blue-700' }}">
                                    {{ $programLabel }}
                                    <span
                                        class="rounded-full px-1.5 py-0.5 text-[10px] {{ (string) $selectedProgramId === (string) $program->dept_id ? 'bg-white/20 text-white' : 'bg-slate-100 text-slate-500' }}">{{ $programFacultyCount }}</span>
                                </a>
                            @endforeach
                        </div>
                        <form method="GET" action="{{ route('dean.pbt') }}"
                            class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">
                            <input type="hidden" name="program_id" value="{{ $selectedProgramId }}">
                            <input type="hidden" name="shift" value="{{ $shift }}">
                            <label class="sr-only" for="faculty_id">Choose a faculty member</label>
                            <select id="faculty_id" name="faculty_id" required @disabled(!$selectedProgramId)
                                onchange="if (this.value) this.form.requestSubmit()"
                                class="min-w-[260px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400"
                                aria-label="Select a faculty member">
                                <option value="">
                                    {{ $selectedProgramId ? 'Choose a faculty member' : 'Choose a program first' }}
                                </option>
                                @foreach ($programFacultyMembers as $faculty)
                                    <option value="{{ $faculty->fac_id }}" @selected(($selectedFaculty?->fac_id ?? request('faculty_id')) === $faculty->fac_id)>
                                        {{ \App\Services\FacultyNameFormatter::format($faculty) }}
                                    </option>
                                @endforeach
                            </select>
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
                                <a href="{{ route('dean.pbt', $selectedFaculty ? ['faculty_id' => $selectedFaculty->fac_id, 'program_id' => $selectedProgramId, 'shift' => 'day'] : ['program_id' => $selectedProgramId, 'shift' => 'day']) }}"
                                    class="px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'day' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Day</a>
                                <a href="{{ route('dean.pbt', $selectedFaculty ? ['faculty_id' => $selectedFaculty->fac_id, 'program_id' => $selectedProgramId, 'shift' => 'night'] : ['program_id' => $selectedProgramId, 'shift' => 'night']) }}"
                                    class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'night' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Evening</a>
                            </div>
                        </form>

                        @if (!$semester)
                            <div class="border-b border-amber-100 bg-amber-50 px-4 py-2.5 text-sm text-amber-800">No
                                active semester is set.</div>
                        @elseif (!$selectedFaculty)
                            <div class="border-b border-slate-200 bg-blue-50/50 px-4 py-2.5 text-sm text-slate-600">
                                Choose a program and faculty member above to load their timetable preview.</div>
                        @endif
                        @if ($selectedFaculty)
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-extrabold">
                                            {{ \App\Services\FacultyNameFormatter::format($selectedFaculty) }}</h2>
                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-[11px] font-semibold text-blue-700">{{ $selectedFaculty->program?->dept_code ?? 'Department' }}</span>
                                    </div>
                                    <p class="mt-1 text-xs text-slate-500">{{ $visibleSchedules->count() }} scheduled
                                        {{ $visibleSchedules->count() === 1 ? 'class' : 'classes' }} · View only</p>
                                </div>
                                @if ($schedules->isNotEmpty())
                                    <a href="{{ route('dean.pbt_export.download', ['faculty_id' => $selectedFaculty->fac_id]) }}"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Download
                                        XLSX</a>
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
                                        @endphp
                                        @if ($dayIndex !== false && $endMinutes > $startMinutes)
                                            <div class="relative z-20 m-[3px] flex flex-col overflow-hidden rounded-md border border-emerald-600 bg-emerald-500 text-white shadow-sm"
                                                style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $row }} / span {{ $span }};">
                                                <div
                                                    class="flex flex-1 flex-col items-center justify-center px-2 py-1 text-center leading-snug">
                                                    <div class="text-[13px] font-extrabold tracking-wide">
                                                        {{ $schedule->subject?->course_code ?? '—' }}</div>
                                                    <div class="mt-1 text-[11.5px] font-semibold">
                                                        {{ $schedule->section?->sec_name ?? 'Section not assigned' }}
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
</body>

</html>
