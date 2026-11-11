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
                        <form method="GET" action="{{ route('dean.pbt') }}"
                            class="flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 py-2.5">
                            <select id="faculty_id" name="faculty_id" required
                                onchange="if (this.value) this.form.requestSubmit()"
                                class="min-w-[260px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100"
                                aria-label="Select a faculty member">
                                <option value="">Choose a faculty member</option>
                                @foreach ($facultyMembers as $faculty)
                                    @php
                                        $name = $faculty->user?->usr_name ?? $faculty->full_name;
                                        $departmentCode = $faculty->program?->dept_code ?? 'Department';
                                    @endphp
                                    <option value="{{ $faculty->fac_id }}" @selected(($selectedFaculty?->fac_id ?? request('faculty_id')) === $faculty->fac_id)>
                                        {{ $departmentCode }} — {{ $name }}</option>
                                @endforeach
                            </select>
                            <input type="hidden" name="shift" value="{{ $shift }}">
                            <span
                                class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600">
                                {{ $semester?->sem_name ?? 'No active semester' }}
                                @if ($semester?->academicYear?->ay_academic_year)
                                    , AY {{ $semester->academicYear->ay_academic_year }}
                                @elseif ($semester?->academicYear?->ay_year_label)
                                    , AY {{ $semester->academicYear->ay_year_label }}
                                @endif
                            </span>
                            <div class="ml-auto flex items-center gap-2">
                                <div class="flex overflow-hidden rounded-lg border border-slate-300 bg-white">
                                    @if ($selectedFaculty)
                                        <a href="{{ route('dean.pbt', ['faculty_id' => $selectedFaculty->fac_id, 'shift' => 'day']) }}"
                                            class="px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'day' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Day</a>
                                        <a href="{{ route('dean.pbt', ['faculty_id' => $selectedFaculty->fac_id, 'shift' => 'night']) }}"
                                            class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'night' ? 'bg-blue-600 text-white' : 'text-slate-600 hover:bg-slate-50' }}">Evening</a>
                                    @else
                                        <span
                                            class="bg-blue-600 px-3 py-2 text-[11px] font-bold uppercase text-white">Day</span>
                                        <span
                                            class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase text-slate-400">Evening</span>
                                    @endif
                                </div>
                            </div>
                        </form>

                        @if (!$semester)
                            <div class="p-8 text-center text-sm text-amber-700">No active semester is set.</div>
                        @elseif (!$selectedFaculty)
                            <div class="p-8 text-center text-sm text-slate-500">Choose a faculty member to view their
                                weekly timetable.</div>
                        @else
                            <div
                                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h2 class="text-base font-extrabold">
                                            {{ $selectedFaculty->user?->usr_name ?? $selectedFaculty->full_name }}</h2>
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
                                                $startTime = \Illuminate\Support\Carbon::parse(
                                                    $schedule->sch_start_time,
                                                );
                                                $endTime = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                                                $startMinutes = max(
                                                    $startTime->hour * 60 + $startTime->minute,
                                                    $gridStart,
                                                );
                                                $endMinutes = min($endTime->hour * 60 + $endTime->minute, $gridEnd);
                                                $row = intdiv($startMinutes - $gridStart, $slotMinutes) + 1;
                                                $span = max(
                                                    1,
                                                    (int) ceil(($endMinutes - $startMinutes) / $slotMinutes),
                                                );
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
                        @endif
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
