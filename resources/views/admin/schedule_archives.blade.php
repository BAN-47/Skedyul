<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Schedule Archives</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            body { background: #fff !important; overflow: visible !important; height: auto !important; }
            .no-print, .app-shell > aside { display: none !important; }
            .app-main, .page-content { padding: 0 !important; margin: 0 !important; overflow: visible !important; }
            .archive-grid-scroll { overflow: visible !important; }
            .archive-grid-min { min-width: 0 !important; }
        }
    </style>
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
    <div class="app-shell">
        @include('partials.admin_sidebar')

        <div class="app-main">
            @include('partials.admin_header', ['title' => 'Schedule Archives'])

            <main class="page-content">
                @if (!$archiveTableAvailable)
                    <div class="card border border-amber-200 bg-amber-50 text-sm text-amber-800">
                        Schedule Archives are not available yet. Apply the required database migration first.
                    </div>
                @else
                    <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                        <div>
                            <h1 class="text-xl font-extrabold text-slate-900">Archived Faculty Schedule</h1>
                            <p class="mt-1 text-sm text-slate-500">View past schedules and their course summaries. Archived records are read-only.</p>
                        </div>
                        <button type="button" onclick="window.print()" class="no-print btn btn-secondary">Print</button>
                    </div>

                    <form method="GET" action="{{ route('admin.schedule-archives') }}"
                        class="no-print card mb-4 grid grid-cols-1 gap-3 p-4 sm:grid-cols-2 xl:grid-cols-5">
                        <div>
                            <label for="archive-year" class="field-label">Academic year</label>
                            <select class="field-input" name="year" id="archive-year" onchange="this.form.submit()">
                                @forelse ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->ay_id }}" @selected($selectedYear === $academicYear->ay_id)>
                                        {{ $academicYear->year_label }}
                                    </option>
                                @empty
                                    <option value="">No archived years</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label for="archive-semester" class="field-label">Semester</label>
                            <select class="field-input" name="semester" id="archive-semester" onchange="this.form.submit()">
                                @forelse ($semesters as $semester)
                                    <option value="{{ $semester->sem_id }}" @selected($selectedSemester === $semester->sem_id)>
                                        {{ $semester->semester_name }}
                                    </option>
                                @empty
                                    <option value="">No archived semesters</option>
                                @endforelse
                            </select>
                        </div>
                        <div>
                            <label for="archive-faculty" class="field-label">Faculty</label>
                            <select class="field-input" name="faculty" id="archive-faculty">
                                <option value="">Choose a faculty member</option>
                                @foreach ($facultyMembers as $facultyMember)
                                    <option value="{{ $facultyMember->fac_id }}" @selected($selectedFaculty === $facultyMember->fac_id)>
                                        {{ $facultyMember->faculty_name ?: 'Faculty name unavailable' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="archive-shift" class="field-label">Schedule</label>
                            <select class="field-input" name="shift" id="archive-shift">
                                <option value="day" @selected($selectedShift === 'day')>Day</option>
                                <option value="evening" @selected($selectedShift === 'evening')>Evening</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn btn-primary">View Schedule</button>
                            <a href="{{ route('admin.schedule-archives', ['year' => $selectedYear, 'semester' => $selectedSemester]) }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    @if (!$selectedYear || !$selectedSemester)
                        <section class="card px-4 py-12 text-center">
                            <div class="text-base font-bold text-slate-700">No archived semesters found</div>
                            <p class="mt-1 text-sm text-slate-500">Schedules from completed academic years will appear here.</p>
                        </section>
                    @elseif (!$selectedFaculty)
                        <section class="card px-4 py-12 text-center">
                            <div class="text-base font-bold text-slate-700">Choose a faculty member</div>
                            <p class="mt-1 text-sm text-slate-500">Select a faculty member to view their archived timetable and course summary.</p>
                        </section>
                    @else
                        <section class="card !p-0 overflow-hidden">
                            <div class="no-print flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 py-2.5">
                                <div class="inline-flex min-w-[220px] items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-700">
                                        {{ strtoupper(substr($facultyMembers->firstWhere('fac_id', $selectedFaculty)->faculty_name ?? '', 0, 2)) }}
                                    </span>
                                    <span class="min-w-0">
                                        <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Archived schedule</span>
                                        <span class="block truncate text-[13px] font-semibold text-slate-800">{{ $facultyMembers->firstWhere('fac_id', $selectedFaculty)->faculty_name ?? 'Faculty' }}</span>
                                    </span>
                                </div>
                                <div class="flex overflow-hidden rounded-lg border border-slate-300" aria-label="Schedule shift">
                                    <a href="{{ route('admin.schedule-archives', ['year' => $selectedYear, 'semester' => $selectedSemester, 'faculty' => $selectedFaculty, 'shift' => 'day']) }}"
                                        class="px-3 py-2 text-[11px] font-bold uppercase {{ $selectedShift === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">Day</a>
                                    <a href="{{ route('admin.schedule-archives', ['year' => $selectedYear, 'semester' => $selectedSemester, 'faculty' => $selectedFaculty, 'shift' => 'evening']) }}"
                                        class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase {{ $selectedShift === 'evening' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">Evening</a>
                                </div>
                                <details class="relative ml-auto">
                                    <summary class="btn btn-secondary cursor-pointer list-none">Summary ▦</summary>
                                    <div class="absolute right-0 top-full z-50 mt-2 w-[min(900px,calc(100vw-2rem))] max-h-[70vh] overflow-auto rounded-xl border border-slate-200 bg-white shadow-2xl">
                                        <div class="sticky top-0 bg-blue-600 px-4 py-3 text-sm font-extrabold text-white">Course Summary — {{ ucfirst($selectedShift) }} Archive</div>
                                        <div class="overflow-x-auto">
                                            <table class="w-full text-[11px]">
                                                <thead class="bg-slate-100 text-left uppercase text-slate-600">
                                                    <tr><th class="p-2">Course</th><th class="p-2">Title</th><th class="p-2 text-center">Units</th><th class="p-2 text-center">Scheduled Hours</th><th class="p-2">Section</th><th class="p-2 text-center">Students</th></tr>
                                                </thead>
                                                <tbody>
                                                    @forelse ($courseRows as $row)
                                                        <tr class="border-t border-slate-100 text-slate-700">
                                                            <td class="p-2 font-bold">{{ $row->course_code ?? '—' }}</td>
                                                            <td class="p-2">{{ $row->course_name ?? 'Course unavailable' }}</td>
                                                            <td class="p-2 text-center">{{ $row->course_units }}</td>
                                                            <td class="p-2 text-center">{{ rtrim(rtrim(number_format($row->scheduled_hours, 2), '0'), '.') }}</td>
                                                            <td class="p-2">{{ $row->section_name ?? '—' }}</td>
                                                            <td class="p-2 text-center">{{ $row->student_count }}</td>
                                                        </tr>
                                                    @empty
                                                        <tr><td colspan="6" class="p-6 text-center text-slate-400">No archived courses in this shift.</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="space-y-1 border-t border-slate-200 p-3 text-xs text-slate-700">
                                            <div class="flex justify-between"><strong>Preparations:</strong><span>{{ $summaryPreparations }}</span></div>
                                            <div class="flex justify-between"><strong>Total Units:</strong><span>{{ rtrim(rtrim(number_format($summaryUnits, 2), '0'), '.') }}</span></div>
                                            <div class="flex justify-between"><strong>Scheduled Hours:</strong><span>{{ rtrim(rtrim(number_format($summaryHours, 2), '0'), '.') }}</span></div>
                                        </div>
                                    </div>
                                </details>
                                <span class="text-[11px] italic text-slate-400">View only</span>
                            </div>

                            <div class="archive-grid-scroll overflow-x-auto">
                                <div class="archive-grid-min min-w-[840px]">
                                    <div class="grid border-b border-slate-200 bg-slate-100" style="grid-template-columns:90px repeat({{ count($days) }},minmax(0,1fr));">
                                        <div class="px-2 py-2 text-center text-[10px] font-bold uppercase text-slate-500">Time</div>
                                        @foreach ($days as $day)
                                            <div class="border-l border-slate-200 px-2 py-2 text-center text-[11px] font-bold text-slate-700">{{ $day }}</div>
                                        @endforeach
                                    </div>
                                    <div class="relative grid bg-white" style="grid-template-columns:90px repeat({{ count($days) }},minmax(0,1fr));grid-template-rows:repeat({{ $slotCount }},26px);">
                                        @for ($slot = 0; $slot < $slotCount; $slot += 2)
                                            @php
                                                $minutes = $gridStart + $slot * $slotMinutes;
                                                $hour = \Illuminate\Support\Carbon::createFromTime(intdiv($minutes, 60), 0);
                                                $hourEnd = (clone $hour)->addHour();
                                            @endphp
                                            <div class="flex items-center justify-end border-b border-slate-200 bg-slate-50 px-2 text-[10px] font-semibold text-slate-500" style="grid-column:1;grid-row:{{ $slot + 1 }}/span 2;">{{ $hour->format('g') }}–{{ $hourEnd->format('g A') }}</div>
                                        @endfor
                                        @for ($slot = 0; $slot < $slotCount; $slot++)
                                            @php $minutes = $gridStart + ($slot * $slotMinutes); @endphp
                                            @foreach ($days as $dayIndex => $day)
                                                <div class="border-b border-l border-slate-100 {{ $minutes % 60 === 0 ? '!border-b-slate-200' : '' }}" style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $slot + 1 }};"></div>
                                            @endforeach
                                        @endfor
                                        @if ($selectedShift === 'day')
                                            <div class="pointer-events-none z-10 flex items-center justify-center bg-slate-900 text-[11px] font-bold tracking-[3px] text-white" style="grid-column:2/span {{ count($days) }};grid-row:11/span 2;">NOON BREAK</div>
                                        @endif
                                        @foreach ($archives as $archive)
                                            @php
                                                $dayIndex = array_search($archive->day, $days, true);
                                                if ($dayIndex === false) continue;
                                                $start = \Illuminate\Support\Carbon::parse($archive->start_time);
                                                $end = \Illuminate\Support\Carbon::parse($archive->end_time);
                                                $startMinutes = max($start->hour * 60 + $start->minute, $gridStart);
                                                $endMinutes = min($end->hour * 60 + $end->minute, $gridEnd);
                                                if ($endMinutes <= $startMinutes) continue;
                                                $row = intdiv($startMinutes - $gridStart, $slotMinutes) + 1;
                                                $span = max(1, (int) ceil(($endMinutes - $startMinutes) / $slotMinutes));
                                            @endphp
                                            <article class="z-20 m-[3px] flex flex-col items-center justify-center overflow-hidden rounded-md border border-emerald-600 bg-emerald-500 px-1.5 py-1 text-center leading-tight text-white shadow-sm" style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $row }}/span {{ $span }};">
                                                <strong class="text-[12px] font-extrabold tracking-wide">{{ $archive->course_code ?? '—' }}</strong>
                                                <span class="mt-1 text-[10px] font-semibold">{{ $archive->section_name ?? 'Section' }}</span>
                                                <span class="text-[10px]">{{ $archive->room_name ?? 'Room not set' }}</span>
                                                <span class="mt-1 text-[9px] font-medium opacity-90">{{ $archive->course_name ?? '' }}</span>
                                            </article>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @if ($archives->isEmpty())
                                <div class="border-t border-slate-100 px-4 py-3 text-center text-[12px] text-slate-500">No archived {{ $selectedShift }}-shift classes for this faculty member.</div>
                            @endif
                        </section>
                    @endif
                @endif
            </main>
        </div>
    </div>
</body>

</html>
