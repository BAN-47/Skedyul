<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Program by Teacher (PBT)</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    @php
        // ── Defaults so this page renders before PbtController exists ──
        $schedules = $schedules ?? collect(); // schedules already filtered to $selectedFaculty, if one is chosen
        $summarySchedules = $summarySchedules ?? $schedules;
        $subjects = $subjects ?? collect();
        $facultyFullTime = $facultyFullTime ?? collect();
        $facultyPartTime = $facultyPartTime ?? collect();
        $deans = $deans ?? collect();
        $facultyChairs = $facultyChairs ?? collect();
        $placeholderFaculty = $placeholderFaculty ?? null;
        $editableFaculty = collect($facultyFullTime)
            ->concat($facultyPartTime)
            ->concat($facultyChairs)
            ->concat($deans)
            ->concat($placeholderFaculty ? collect([$placeholderFaculty]) : collect())
            ->unique('fac_id')
            ->sortBy('fac_last_name')
            ->values();
        $sections = $sections ?? collect();
        $programs = $programs ?? collect();
        $semesters = $semesters ?? collect();
        $filters = $filters ?? [];
        $activeSemester = $activeSemester ?? null;
        $departments = $departments ?? ($programs ?? collect());
        $programs = $programs ?? ($departments ?? collect());
        $chairDepartment = $chairDepartment ?? null;
        $chairProgram = $chairProgram ?? ($chairDepartment ?? null);
        $selectedFaculty = $selectedFaculty ?? null; // the Faculty (or Dean) model currently being viewed, or null
        $loadStats = $loadStats ?? [
            'preparations' => null,
            'units' => null,
            'hours_week' => null,
            'designation' => null,
            'production' => null,
            'extension' => null,
            'research' => null,
        ];

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        // The schedule is drawn as a CSS Grid: one time column, followed by one column per day.
        // Each half-hour is one row, so a one-hour class takes two rows.
        // Same Day/Evening shift mechanics as PBS.
        $shift = request()->query('shift', 'day') === 'night' ? 'night' : 'day';

        if ($shift === 'night') {
            $gridStart = 16 * 60; // 4:00 PM
            $gridEnd = 22 * 60; // 10:00 PM
        } else {
            $gridStart = 7 * 60; // 7:00 AM
            $gridEnd = 16 * 60; // 4:00 PM
        }

        $slotMinutes = 30;
        $slotCount = intdiv($gridEnd - $gridStart, $slotMinutes);

        $noonStart = 12 * 60;
        $noonEnd = 13 * 60;
        $showNoonBreak = $shift === 'day' && $noonStart >= $gridStart && $noonEnd <= $gridEnd;

        $selectedDate = $selectedDate ?? now();
        if (is_string($selectedDate)) {
            $selectedDate = \Illuminate\Support\Carbon::parse($selectedDate);
        }

        // Occupied-cell lookup — same purpose as PBS: don't render a click
        // target under an existing schedule block.
        $occupied = [];
        foreach ($schedules as $s) {
            $dIdx = array_search($s->sch_day, $days, true);
            if ($dIdx === false) {
                continue;
            }
            $start = \Illuminate\Support\Carbon::parse($s->sch_start_time);
            $end = \Illuminate\Support\Carbon::parse($s->sch_end_time);
            $sMin = max($start->hour * 60 + $start->minute, $gridStart);
            $eMin = min($end->hour * 60 + $end->minute, $gridEnd);
            for ($m = $sMin; $m < $eMin; $m += $slotMinutes) {
                $rowIdx = intdiv($m - $gridStart, $slotMinutes);
                $occupied[$dIdx . '-' . $rowIdx] = true;
            }
        }
    @endphp

    <div class="app-shell">

        @include('partials.chair_sidebar')

        {{-- ══════════ MAIN ══════════ --}}
        <div class="app-main">

            @include('partials.chair_header', [
                'title' => 'Program by Teacher (PBT)',
                'collegeContext' => $chairDepartment?->department?->college_name,
                'contextTag' => collect([
                    $chairDepartment?->dept_code,
                    $activeSemester?->ay_academic_year,
                    $activeSemester?->sem_name,
                ])->filter()->implode(' · '),
            ])

            <div class="page-content" id="page-pbt">

                {{-- HEADING + ACTIONS --}}
                <div class="flex items-center justify-between gap-3 mb-4">
                    <span class="text-xs text-slate-500">Schedules save as drafts when confirmed</span>
                    <div class="ml-auto flex flex-wrap items-center justify-end gap-2">
                        @if ($selectedFaculty)
                            @if ($activeSemester)
                                <form method="POST" action="{{ route('chair.submit_dean.store') }}">
                                    @csrf
                                    <input type="hidden" name="faculty_id" value="{{ $selectedFaculty->fac_id }}">
                                    <input type="hidden" name="return_to_pbt" value="1">
                                    <input type="hidden" name="shift" value="{{ $shift }}">
                                    <button type="submit" class="btn btn-primary">
                                        Submit to Dean
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('chair.export_reports.pbt.preview', ['faculty_id' => $selectedFaculty->fac_id, 'semester' => $activeSemester?->sem_id]) }}"
                                data-export-preview class="btn btn-secondary">Preview PBT Export</a>
                        @endif
                    </div>
                </div>

                <div class="flex justify-start -mt-2 mb-3">
                    <button type="button" id="schedule-summary-toggle" aria-expanded="false"
                        aria-controls="schedule-summary-modal" onclick="toggleScheduleSummary()"
                        class="btn btn-secondary inline-flex items-center gap-2">
                        <span>Summary</span><span aria-hidden="true">▦</span>
                    </button>
                </div>

                <div class="w-full">

                    {{-- ══════════ SUMMARY OF COURSES (per teacher) ══════════ --}}
                    <div id="schedule-summary-modal"
                        class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/40 p-4 opacity-0 transition-opacity duration-200"
                        role="dialog" aria-modal="true" aria-labelledby="schedule-summary-title"
                        onclick="if(event.target === this) closeScheduleSummary()">
                        <aside id="schedule-summary-panel"
                            class="card !p-0 w-[min(1100px,calc(100vw-2rem))] max-h-[calc(100dvh-2rem)] overflow-y-auto self-center shadow-2xl scale-95 transition-transform duration-200">
                            <div
                                class="sticky top-0 z-10 flex items-center justify-between bg-blue-600 text-white px-4 py-3">
                                <span id="schedule-summary-title" class="text-[12px] font-extrabold tracking-wide">
                                    Summary of Courses — {{ strtoupper($selectedShift ?? 'day') }} Workload
                                </span>
                                <button type="button" onclick="closeScheduleSummary()"
                                    class="rounded-lg px-2 py-1 text-lg leading-none hover:bg-white/20"
                                    aria-label="Close summary">✕</button>
                            </div>

                            @php
                                // OJT is one shared course row; other courses stay split by section.
                                $pbtCourseRows = $summarySchedules
                                    ->filter(function ($s) {
                                        $course = $s->course ?? $s->subject;

                                        return $course &&
                                            ($course->course_code ?? ($course->subj_code ?? null)) !== 'REC' &&
                                            ($course->course_name ?? ($course->subj_name ?? null)) !==
                                                'Research, Extension and Consultation Time';
                                    })
                                    ->unique(function ($s) {
                                        $course = $s->course ?? $s->subject;
                                        $isOjt = \App\Services\ScheduleAssignmentService::isOjt($course);
                                        return ($s->sch_course_id ?? ($s->sch_subj_id ?? '')) .
                                            ($isOjt ? '' : '|' . ($s->sch_sec_id ?? ''));
                                    })
                                    ->values();
                            @endphp

                            <div class="overflow-x-auto">
                                <table class="w-full text-[10px] border-collapse">
                                    <thead>
                                        <tr class="bg-slate-100 text-slate-600 uppercase font-bold">
                                            <th class="px-1.5 py-1.5 text-left border-b border-slate-200">Course Code
                                            </th>
                                            <th class="px-1.5 py-1.5 text-left border-b border-l border-slate-200">
                                                Descriptive Title</th>
                                            <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">
                                                Units</th>
                                            <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">
                                                Scheduled<br>Hours</th>
                                            <th class="px-1.5 py-1.5 text-left border-b border-l border-slate-200">
                                                Degree<br>Yr. &amp; Sec.</th>
                                            <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">
                                                Total<br>No. of Students</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pbtCourseRows as $s)
                                            @php
                                                $course = $s->course ?? $s->subject;
                                                $code = $course->course_code ?? ($course->subj_code ?? '—');
                                                $title = $course->course_name ?? ($course->subj_name ?? '—');
                                                $isOjt = \App\Services\ScheduleAssignmentService::isOjt($course);
                                                $courseUnits = \App\Services\ScheduleAssignmentService::courseUnits(
                                                    $course,
                                                );
                                                $courseHours = \App\Services\ScheduleAssignmentService::courseHours(
                                                    $course,
                                                );
                                                $courseMeetings = $summarySchedules->where(
                                                    'sch_course_id',
                                                    $s->sch_course_id,
                                                );
                                                if (!$isOjt) {
                                                    $courseMeetings = $courseMeetings->where(
                                                        'sch_sec_id',
                                                        $s->sch_sec_id,
                                                    );
                                                }
                                                $meetingHours = fn($meeting) => \Illuminate\Support\Carbon::parse(
                                                    $meeting->sch_start_time,
                                                )->diffInMinutes(
                                                    \Illuminate\Support\Carbon::parse($meeting->sch_end_time),
                                                ) / 60;
                                                $scheduledHours = $isOjt
                                                    ? $courseMeetings
                                                        ->unique(
                                                            fn($meeting) => $meeting->sch_day .
                                                                '|' .
                                                                $meeting->sch_start_time .
                                                                '|' .
                                                                $meeting->sch_end_time,
                                                        )
                                                        ->sum($meetingHours)
                                                    : $courseMeetings->sum($meetingHours);
                                                $secName = $isOjt
                                                    ? $courseMeetings
                                                        ->map(fn($meeting) => $meeting->section?->sec_name)
                                                        ->filter()
                                                        ->unique()
                                                        ->implode(', ')
                                                    : optional($s->section)->sec_name ?? '—';
                                                $studs = $isOjt
                                                    ? $courseMeetings
                                                        ->map(fn($meeting) => $meeting->section)
                                                        ->filter()
                                                        ->unique('sec_id')
                                                        ->sum(fn($section) => (int) $section->sec_no_of_student)
                                                    : optional($s->section)->sec_no_of_student ??
                                                        (optional($s->section)->sec_max_capacity ?? '—');
                                            @endphp
                                            <tr class="border-b border-slate-100 text-slate-700">
                                                <td class="px-1.5 py-1.5 font-bold text-slate-800 whitespace-nowrap">
                                                    {{ $code }}</td>
                                                <td class="px-1.5 py-1.5 border-l border-slate-100 leading-snug">
                                                    {{ $title }}</td>
                                                <td class="px-1.5 py-1.5 border-l border-slate-100 text-center">
                                                    {{ $courseUnits }}</td>
                                                <td class="px-1.5 py-1.5 border-l border-slate-100 text-center"
                                                    title="{{ $scheduledHours }} of {{ $courseHours }} total hours scheduled">
                                                    {{ $scheduledHours }}/{{ $courseHours }}</td>
                                                <td class="px-1.5 py-1.5 border-l border-slate-100 whitespace-nowrap">
                                                    {{ $secName }}</td>
                                                <td
                                                    class="px-1.5 py-1.5 border-l border-slate-100 text-center font-semibold">
                                                    {{ $studs }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-2 py-3 text-center text-slate-400 italic">
                                                    @if (empty($selectedFaculty))
                                                        Select a teacher to view their courses.
                                                    @else
                                                        No courses assigned yet.
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <div class="border-t border-slate-200 p-3 text-[11px] text-slate-700 space-y-1">
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">No. of Preparations:</span>
                                    <span>{{ $loadStats['preparations'] ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">No. of Units:</span>
                                    <span>{{ $loadStats['units'] ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Scheduled Hours/Week
                                        ({{ ucfirst($selectedShift ?? 'day') }}):</span>
                                    <span>{{ $loadStats['hours_week'] ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Assigned Workload Hours/Week
                                        ({{ ucfirst($selectedShift ?? 'day') }}):</span>
                                    <span>{{ $loadStats['workload_hours'] ?? '—' }}</span>
                                </div>
                                @if (!empty($loadStats['is_part_time']))
                                    <div class="flex justify-between gap-2">
                                        <span class="font-bold">Scheduled Hours/Week (All Shifts):</span>
                                        <span>{{ $loadStats['total_hours_week'] ?? '—' }}</span>
                                    </div>
                                    <div class="flex justify-between gap-2">
                                        <span class="font-bold">Assigned Workload Hours/Week (All Shifts):</span>
                                        <span>{{ $loadStats['total_workload_hours'] ?? '—' }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Teaching Load Limit:</span>
                                    <span>{{ isset($loadStats['load_limit']) ? rtrim(rtrim(number_format((float) $loadStats['load_limit'], 2), '0'), '.') . 'h' : '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Administrative Designation:</span>
                                    <span class="text-right">{{ $loadStats['designation'] ?? '—' }}</span>
                                </div>
                                <div class="border-t border-slate-100 my-1.5"></div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Production:</span>
                                    <span>{{ $loadStats['production'] ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Extension:</span>
                                    <span>{{ $loadStats['extension'] ?? '—' }}</span>
                                </div>
                                <div class="flex justify-between gap-2">
                                    <span class="font-bold">Research:</span>
                                    <span>{{ $loadStats['research'] ?? '—' }}</span>
                                </div>
                            </div>
                        </aside>
                    </div>

                    {{-- ══════════ PLOTTER ══════════ --}}
                    <section class="card !p-0 overflow-hidden">

                        {{-- FILTER TOOLBAR --}}
                        <div
                            class="flex flex-wrap items-center gap-1.5 px-3 py-2.5 border-b border-slate-200 bg-slate-50 relative">

                            {{-- TEACHER picker: button opens a searchable, grouped popover --}}
                            <div class="relative">
                                <button type="button" id="teacher-picker-btn" onclick="toggleTeacherPicker()"
                                    aria-haspopup="listbox" aria-expanded="false"
                                    class="inline-flex min-w-[230px] items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 text-left shadow-sm transition hover:border-indigo-300 hover:shadow focus:outline-none focus:ring-2 focus:ring-indigo-200">
                                    <span
                                        class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-indigo-50 text-sm font-bold text-indigo-700">
                                        @if ($selectedFaculty?->fac_profile_image)
                                            <img src="{{ \App\Services\ProfileImageStorage::url($selectedFaculty->fac_profile_image, 'faculty_profile') }}"
                                                class="h-full w-full object-cover"
                                                alt="{{ $selectedFaculty->fac_first_name }} profile photo">
                                        @else
                                            {{ $selectedFaculty ? strtoupper(substr($selectedFaculty->fac_first_name, 0, 1) . substr($selectedFaculty->fac_last_name, 0, 1)) : '👤' }}
                                        @endif
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span
                                            class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Viewing
                                            schedule for</span>
                                        <span
                                            class="block truncate text-[13px] font-semibold text-slate-800">{{ $selectedFaculty ? \App\Services\FacultyNameFormatter::format($selectedFaculty) : 'Choose a teacher' }}</span>
                                    </span>
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20"
                                        fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd"
                                            d="M5.22 7.47a.75.75 0 0 1 1.06 0L10 11.19l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 8.53a.75.75 0 0 1 0-1.06Z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div id="teacher-picker-panel"
                                    class="hidden absolute left-0 top-[calc(100%+8px)] z-50 w-[min(340px,calc(100vw-32px))] overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_20px_60px_rgba(15,23,42,0.18)]">
                                    <div class="border-b border-slate-100 bg-slate-50 p-3">
                                        <div class="mb-2 text-sm font-bold text-slate-800">Select a teacher</div>
                                        <input type="text" id="teacher-search"
                                            oninput="filterTeacherList(this.value)" placeholder="Search by name..."
                                            autocomplete="off" aria-label="Search teachers"
                                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-[13px] outline-none transition placeholder:text-slate-400 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    </div>
                                    <div class="max-h-[300px] overflow-y-auto p-2" role="listbox"
                                        aria-label="Teachers">

                                        @foreach ([
        'FULL-TIME' => $facultyFullTime,
        'PART-TIME' => $facultyPartTime,
        'DEAN' => $deans,
        'DEPT CHAIR' => $facultyChairs,
    ] as $groupLabel => $groupList)
                                            @if ($groupList->isEmpty() && $groupLabel === 'DEPT CHAIR')
                                                @continue
                                            @endif
                                            <div class="teacher-group mb-2 last:mb-0"
                                                data-group="{{ $groupLabel }}">
                                                <div
                                                    class="px-2 pb-1 pt-2 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                                    {{ $groupLabel }}
                                                </div>
                                                @forelse($groupList as $person)
                                                    @php
                                                        $pid = $person->fac_id ?? ($person->dean_id ?? '');
                                                        $pname = \App\Services\FacultyNameFormatter::format($person);
                                                    @endphp
                                                    <button type="button"
                                                        class="teacher-option flex w-full items-center gap-3 rounded-xl px-2.5 py-2 text-left transition hover:bg-indigo-50 focus:bg-indigo-50 focus:outline-none {{ ($selectedFaculty->fac_id ?? null) === $pid ? 'bg-indigo-50 ring-1 ring-inset ring-indigo-100' : '' }}"
                                                        role="option"
                                                        aria-selected="{{ ($selectedFaculty->fac_id ?? null) === $pid ? 'true' : 'false' }}"
                                                        data-name="{{ strtolower($pname) }}"
                                                        data-role="{{ $groupLabel }}"
                                                        onclick="selectTeacher('{{ $pid }}')">
                                                        <span
                                                            class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full bg-slate-100 text-xs font-bold text-slate-600">
                                                            @if ($person->fac_profile_image)
                                                                <img src="{{ \App\Services\ProfileImageStorage::url($person->fac_profile_image, 'faculty_profile') }}"
                                                                    class="h-full w-full object-cover"
                                                                    alt="{{ $pname }} profile photo">
                                                            @else
                                                                {{ strtoupper(substr($pname, 0, 1)) }}
                                                            @endif
                                                        </span>
                                                        <span class="min-w-0 flex-1"><span
                                                                class="block truncate text-[13px] font-semibold text-slate-800">{{ $pname }}</span><span
                                                                class="block text-[11px] text-slate-500">{{ ucwords(strtolower($groupLabel)) }}</span></span>
                                                        @if (($selectedFaculty->fac_id ?? null) === $pid)
                                                            <span class="text-sm font-bold text-indigo-600"
                                                                aria-label="Selected">✓</span>
                                                        @endif
                                                    </button>
                                                @empty
                                                    <div class="px-3 py-2 text-[11.5px] text-slate-400 italic">None
                                                    </div>
                                                @endforelse
                                            </div>
                                        @endforeach
                                        <div id="teacher-no-results"
                                            class="hidden rounded-xl px-3 py-6 text-center text-[13px] text-slate-500">
                                            No teachers match that search.</div>
                                    </div>
                                </div>
                            </div>


                            <div class="flex min-w-[150px] flex-col gap-1">
                                <select id="filter-academic-year" onchange="applyAcademicYear()"
                                    aria-label="Select academic year" title="Select academic year"
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-[11px] font-medium text-slate-700 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    @foreach ($academicYears as $yearOption)
                                        <option value="{{ $yearOption->ay_id }}" @selected((string) $selectedAcademicYearId === (string) $yearOption->ay_id)>
                                            {{ $yearOption->ay_academic_year ?? $yearOption->ay_year_label }}{{ $yearOption->ay_is_active ? ' · Current' : ' · Archived' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex min-w-[190px] flex-col gap-1">
                                <select id="filter-semester" onchange="applyFilters()"
                                    aria-label="Select semester" title="Select semester"
                                    class="h-9 w-full rounded-lg border border-slate-200 bg-white px-3 text-[11px] font-medium text-slate-700 shadow-sm outline-none transition focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                                    <option value="">Select semester</option>
                                    @foreach ($semesters as $semester)
                                        <option value="{{ $semester->sem_id }}"
                                            data-course-semester="{{ str_contains(strtolower($semester->sem_name ?? ''), '2nd') || str_contains(strtolower($semester->sem_name ?? ''), 'second') ? 2 : 1 }}"
                                            @selected(($filters['semester'] ?? '') === $semester->sem_id)>
                                            {{ $semester->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" id="filter-department" value="{{ $selectedDeptId }}">

                            <div class="flex self-end overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                                <button type="button" id="shift-day" onclick="setShift('day')"
                                    class="px-2.5 py-1.5 text-[11px] font-bold uppercase">Day</button>
                                <button type="button" id="shift-night" onclick="setShift('night')"
                                    class="px-2.5 py-1.5 text-[11px] font-bold uppercase border-l border-slate-300">Evening</button>
                            </div>

                            <div class="ml-auto text-[11px] text-slate-400 italic">
                                Click an empty time slot below to add a schedule
                            </div>
                        </div>

                        {{-- ══ WEEK GRID (identical mechanics to PBS) ══ --}}
                        {{-- The first column labels time; each remaining column represents one day. --}}
                        <div class="overflow-x-auto">
                            <div class="min-w-[840px]">

                                <div class="grid bg-slate-100 border-b border-slate-200"
                                    style="grid-template-columns: 90px repeat({{ count($days) }}, minmax(0,1fr));">
                                    <div
                                        class="px-2 py-2 text-center text-[10.5px] font-bold uppercase tracking-wide text-slate-500">
                                        Time</div>
                                    @foreach ($days as $d)
                                        <div
                                            class="px-2 py-2 text-center text-[11.5px] font-bold text-slate-700 border-l border-slate-200">
                                            {{ $d }}</div>
                                    @endforeach
                                </div>

                                <div class="grid relative"
                                    style="grid-template-columns: 90px repeat({{ count($days) }}, minmax(0,1fr));
                          grid-template-rows: repeat({{ $slotCount }}, 26px);">

                                    @for ($i = 0; $i < $slotCount; $i += 2)
                                        @php
                                            $mins = $gridStart + $i * $slotMinutes;
                                            $bandStart = \Illuminate\Support\Carbon::createFromTime(
                                                intdiv($mins, 60),
                                                0,
                                            );
                                            $bandEnd = (clone $bandStart)->addHour();
                                            $label =
                                                $bandStart->format('g') .
                                                '-' .
                                                $bandEnd->format('g') .
                                                ' ' .
                                                $bandEnd->format('A');
                                        @endphp
                                        <div class="bg-slate-50 border-b border-slate-200 px-2 flex items-center justify-end text-[10.5px] font-semibold text-slate-500"
                                            style="grid-column: 1; grid-row: {{ $i + 1 }} / span 2;">
                                            {{ $label }}
                                        </div>
                                    @endfor

                                    {{-- Build each half-hour cell. Empty cells open Add Schedule; occupied cells cannot be booked again. --}}
                                    @for ($i = 0; $i < $slotCount; $i++)
                                        @php $mins = $gridStart + ($i * $slotMinutes); @endphp
                                        @for ($c = 0; $c < count($days); $c++)
                                            @php
                                                $cellDay = $days[$c];
                                                $cellTime = \Illuminate\Support\Carbon::createFromTime(
                                                    intdiv($mins, 60),
                                                    $mins % 60,
                                                )->format('H:i');
                                                $isTaken = isset($occupied[$c . '-' . $i]);
                                            @endphp
                                            @if ($isTaken || !$selectedFaculty)
                                                <div class="border-l border-b border-slate-100 {{ $mins % 60 === 0 ? '!border-b-slate-200' : '' }}"
                                                    style="grid-column: {{ $c + 2 }}; grid-row: {{ $i + 1 }};">
                                                </div>
                                            @elseif($canPlotSelectedSemester ?? false)
                                                <button type="button"
                                                    onclick="openAddModal('{{ $cellDay }}', '{{ $cellTime }}')"
                                                    class="pbs-cell border-l border-b border-slate-100 {{ $mins % 60 === 0 ? '!border-b-slate-200' : '' }} hover:bg-blue-50 cursor-pointer transition"
                                                    style="grid-column: {{ $c + 2 }}; grid-row: {{ $i + 1 }};"
                                                    title="Add schedule — {{ $cellDay }} {{ \Illuminate\Support\Carbon::createFromTime(intdiv($mins, 60), $mins % 60)->format('g:i A') }}">
                                                </button>
                                            @else
                                                <div class="pbs-cell border-l border-b border-slate-100 bg-slate-50/40 cursor-not-allowed"
                                                    style="grid-column: {{ $c + 2 }}; grid-row: {{ $i + 1 }};"
                                                    title="Archived semester — view only"></div>
                                            @endif
                                        @endfor
                                    @endfor

                                    @if ($showNoonBreak)
                                        @php
                                            $noonRow = intdiv($noonStart - $gridStart, $slotMinutes) + 1;
                                            $noonSpan = intdiv($noonEnd - $noonStart, $slotMinutes);
                                        @endphp
                                        <div class="z-10 flex items-center justify-center bg-slate-900 text-white text-[12px] font-bold tracking-[3px] pointer-events-none"
                                            style="grid-column: 2 / span {{ min(6, count($days)) }}; grid-row: {{ $noonRow }} / span {{ $noonSpan }};">
                                            NOON BREAK
                                        </div>
                                    @endif

                                    {{-- SCHEDULE BLOCKS — Subject / Section / Room (professor is implied: it's the selected teacher) --}}
                                    {{-- Place each saved class over the rows and day column that match its time and day. --}}
                                    @foreach ($schedules as $s)
                                        @php
                                            $dayIndex = array_search($s->sch_day, $days, true);
                                            if ($dayIndex === false) {
                                                continue;
                                            }

                                            $start = \Illuminate\Support\Carbon::parse($s->sch_start_time);
                                            $end = \Illuminate\Support\Carbon::parse($s->sch_end_time);

                                            $startMins = max($start->hour * 60 + $start->minute, $gridStart);
                                            $endMins = min($end->hour * 60 + $end->minute, $gridEnd);
                                            if ($endMins <= $startMins) {
                                                continue;
                                            }

                                            $row = intdiv($startMins - $gridStart, $slotMinutes) + 1;
                                            $span = max(1, (int) ceil(($endMins - $startMins) / $slotMinutes));
                                            $course = $s->course ?? $s->subject;
                                            $courseCode = $course?->course_code ?? $course?->subj_code;
                                            $courseName = $course?->course_name ?? $course?->subj_name;
                                            $isOjt = \App\Services\ScheduleAssignmentService::isOjt($course);
                                            $sharedSectionNames = $isOjt
                                                ? $summarySchedules
                                                    ->filter(
                                                        fn($meeting) => $meeting->sch_course_id === $s->sch_course_id &&
                                                            $meeting->sch_day === $s->sch_day &&
                                                            substr((string) $meeting->sch_start_time, 0, 5) ===
                                                                substr((string) $s->sch_start_time, 0, 5) &&
                                                            substr((string) $meeting->sch_end_time, 0, 5) ===
                                                                substr((string) $s->sch_end_time, 0, 5) &&
                                                            $meeting->sch_room_id === $s->sch_room_id,
                                                    )
                                                    ->map(fn($meeting) => $meeting->section?->sec_name)
                                                    ->filter()
                                                    ->unique()
                                                    ->implode(', ')
                                                : $s->section->sec_name ?? '';
                                            $isRec =
                                                $courseCode === 'REC' ||
                                                $courseName === 'Research, Extension and Consultation Time';
                                        @endphp

                                        <div class="group relative z-20 m-[3px] rounded-md overflow-hidden bg-emerald-500 border border-emerald-600 text-white shadow-sm flex flex-col"
                                            style="grid-column: {{ $dayIndex + 2 }}; grid-row: {{ $row }} / span {{ $span }};"
                                            data-schedule-id="{{ $s->sch_id }}"
                                            data-subject="{{ $s->sch_subj_id }}" data-section="{{ $s->sch_sec_id }}"
                                            data-department="{{ $s->section?->sec_dept_id ?? ($course?->course_dept_id ?? '') }}"
                                            data-faculty="{{ $s->sch_fac_id }}"
                                            data-room="{{ $s->sch_room_id }}" data-semester="{{ $s->sch_sem_id }}"
                                            data-day="{{ $s->sch_day }}"
                                            data-start="{{ substr($s->sch_start_time, 0, 5) }}"
                                            data-end="{{ substr($s->sch_end_time, 0, 5) }}">

                                            @if ($canPlotSelectedSemester ?? false)
                                            <div
                                                class="absolute inset-x-0 top-0 z-10 hidden group-hover:flex gap-0.5 p-0.5">
                                                <button type="button"
                                                    onclick="event.stopPropagation(); openEditModal(this.closest('[data-schedule-id]'))"
                                                    class="flex-1 rounded-sm bg-white/90 hover:bg-white py-[3px] text-[9px] font-bold text-slate-800">Edit</button>
                                                <button type="button"
                                                    onclick="event.stopPropagation(); openDeleteModal(this.closest('[data-schedule-id]'))"
                                                    class="flex-1 rounded-sm bg-white/90 hover:bg-white py-[3px] text-[9px] font-bold text-red-700">Delete</button>
                                            </div>
                                            @endif

                                            <div
                                                class="flex-1 flex flex-col items-center justify-center text-center px-2 py-2 leading-snug">
                                                <div
                                                    class="{{ $isRec ? 'text-[10px] leading-tight break-words' : 'text-[13px] tracking-wide' }} font-extrabold">
                                                    {{ $isRec ? $courseName ?? 'Research, Extension and Consultation Time' : $courseCode ?? '—' }}
                                                </div>
                                                <a href="{{ route('chair.pbs', ['section' => $s->sch_sec_id, 'department' => $s->section?->sec_dept_id, 'semester' => $s->sch_sem_id, 'shift' => $shift === 'night' ? 'night' : 'day']) }}"
                                                    onclick="event.stopPropagation()"
                                                    title="Open this section's matching PBS schedule"
                                                    class="text-[11.5px] font-semibold mt-1 hover:underline">
                                                    {{ $sharedSectionNames }}
                                                </a>
                                                @if ($s->room)
                                                    <div class="text-[11.5px] font-medium mt-0.5 opacity-95">
                                                        {{ $s->room->room_name }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        {{-- DATE NAV --}}
                        <div class="flex flex-col items-center gap-1 py-3 border-t border-slate-200 bg-slate-50">
                            <div class="flex items-center gap-3">
                                <button type="button" onclick="shiftDate(-1)"
                                    class="w-7 h-7 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center">‹</button>
                                <div class="text-[13px] font-bold text-slate-700">
                                    {{ $selectedDate->format('F j, Y (l)') }}</div>
                                <button type="button" onclick="shiftDate(1)"
                                    class="w-7 h-7 rounded-full border border-slate-300 bg-white text-slate-600 hover:bg-slate-100 flex items-center justify-center">›</button>
                            </div>
                            <div class="text-[10.5px] text-slate-400" id="ph-clock"></div>
                        </div>

                    </section>
                </div>
                <div class="mt-8 rounded-xl border border-slate-200 bg-white p-5 shadow-sm print:break-inside-avoid">
                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div class="flex min-h-28 flex-col justify-between text-center">
                            <div class="text-left text-xs font-bold text-slate-700">Prepared by:</div>
                            <div class="border-b border-slate-700 pb-1 text-sm font-bold uppercase">
                                {{ $signoff['prepared_by'] ?? 'Chair name unavailable' }}</div>
                            <div class="text-xs text-slate-600">Program Coordinator / Chair</div>
                        </div>
                        <div class="flex min-h-28 flex-col justify-between text-center">
                            <div class="text-left text-xs font-bold text-slate-700">Reviewed, Certified True and
                                Correct:</div>
                            <div class="border-b border-slate-700 pb-1 text-sm font-bold uppercase">
                                {{ $signoff['reviewed_by'] ?? 'Dean not assigned' }}</div>
                            <div class="text-xs text-slate-600">{{ $signoff['reviewed_title'] ?? 'Director / Dean' }}
                            </div>
                        </div>
                        <div class="flex min-h-28 flex-col justify-between text-center">
                            <div class="text-left text-xs font-bold text-slate-700">Approved by:</div>
                            <div
                                class="w-full border-b border-slate-700 px-1 pb-1 text-center text-sm font-bold uppercase">
                                {{ $signoff['campus_director'] ?? '' }}
                            </div>
                            <div class="text-xs text-slate-600">Campus Director</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- ══════════════ MODAL: ADD ══════════════ --}}
    <div class="modal-overlay" id="modal-add-schedule">
        <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]">
            <div class="modal-header">
                <div class="modal-title text-indigo-700">Add Schedule</div>
                <button type="button" onclick="closeModal('modal-add-schedule')" class="modal-close">✕</button>
            </div>

            <div class="max-h-[62vh] overflow-y-auto pr-1">

                {{-- ── FIXED FIELDS (top) ── --}}
                <div class="mb-3">
                    <label class="field-label">Semester</label>
                    <div id="add-semester-label" class="field-input bg-slate-100 font-semibold text-slate-700">
                        {{ optional($activeSemester)->label ?? 'No semester selected' }}
                    </div>
                    <input type="hidden" id="add-semester" value="{{ optional($activeSemester)->sem_id ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="field-label">Professor</label>
                    @if ($selectedFaculty)
                        <div class="field-input bg-slate-100 font-semibold text-slate-700">
                            {{ \App\Services\FacultyNameFormatter::format($selectedFaculty) }}
                        </div>
                        <input type="hidden" id="add-faculty" value="{{ $selectedFaculty->fac_id }}">
                    @else
                        <select id="add-faculty" class="field-input w-full">
                            <option value="">-- Select Professor --</option>
                            @foreach ($editableFaculty as $person)
                                @php $professorName = $person->fac_is_placeholder ? \App\Services\UnassignedFacultyService::LABEL : \App\Services\FacultyNameFormatter::format($person); @endphp
                                <option value="{{ $person->fac_id }}">{{ $professorName }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>

                <div class="mb-3">
                    <label class="field-label">Department</label>
                    <select id="add-department" class="field-input w-full">
                        <option value="">-- Select Department --</option>
                        @foreach ($allDepartments as $dept)
                            <option value="{{ $dept->dept_id }}" @selected(($selectedDeptId ?? '') === $dept->dept_id)>
                                {{ $dept->dept_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- ── SECTION (triggers subject filter) ── --}}
                <div class="relative mb-3" id="add-single-section-wrap" data-pbt-section-picker>
                    <label class="field-label">Section</label>
                    <select id="add-section" class="sr-only"
                        onchange="filterSubjectsBySection('add-section', 'add-subject')">
                        <option value="">-- Select Section --</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->sec_id }}" data-dept-id="{{ $sec->sec_dept_id }}"
                                data-year-level="{{ $sec->sec_year_level ?? '' }}"
                                data-is-evening="{{ $sec->sec_is_evening ? '1' : '0' }}">{{ $sec->sec_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" data-pbt-section-trigger aria-expanded="false"
                        class="field-input flex w-full items-center justify-between text-left">
                        <span data-pbt-section-label>-- Select Section --</span><span aria-hidden="true">⌄</span>
                    </button>
                    <div data-pbt-section-panel
                        class="absolute z-50 mt-1 hidden w-[min(100%,420px)] rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                        <input type="search" data-pbt-section-search placeholder="Search sections..."
                            class="mb-2 w-full rounded-md border border-slate-200 px-2.5 py-2 text-xs outline-none focus:border-indigo-400">
                        <div class="mb-2 flex gap-1" role="group" aria-label="Filter sections by shift">
                            <button type="button" data-pbt-shift="all"
                                class="rounded border px-2 py-1 text-[10px]">All <span
                                    data-pbt-shift-count="all"></span></button>
                            <button type="button" data-pbt-shift="day"
                                class="rounded border px-2 py-1 text-[10px]">Day <span
                                    data-pbt-shift-count="day"></span></button>
                            <button type="button" data-pbt-shift="evening"
                                class="rounded border px-2 py-1 text-[10px]">Evening <span
                                    data-pbt-shift-count="evening"></span></button>
                        </div>
                        <div class="mb-2 flex flex-wrap gap-1" role="group" aria-label="Filter sections by year">
                            @foreach ([['all', 'All years'], ['1', '1st'], ['2', '2nd'], ['3', '3rd'], ['4', '4th'], ['5', '5th']] as [$yearFilter, $yearText])
                                <button type="button" data-pbt-year="{{ $yearFilter }}"
                                    class="rounded-full border px-2 py-1 text-[9px]">{{ $yearText }} <span
                                        data-pbt-year-count="{{ $yearFilter }}"></span></button>
                            @endforeach
                        </div>
                        <div data-pbt-section-options class="max-h-48 overflow-y-auto"></div>
                    </div>
                </div>
                <div class="mb-3 hidden" id="add-ojt-sections-wrap">
                    <span class="field-label">Year 4 sections <span
                            class="font-normal normal-case text-slate-400">(select one or more)</span></span>
                    <div id="add-ojt-sections"
                        class="mt-1.5 max-h-40 space-y-1 overflow-y-auto rounded-lg border border-slate-200 p-2"></div>
                </div>
                <div class="mb-3 hidden" id="add-ojt-summary-wrap">
                    <label
                        class="flex cursor-pointer items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700">
                        <input type="checkbox" id="add-ojt-is-evening"
                            class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Is Evening?</span>
                    </label>
                    <p id="add-ojt-summary-notice"
                        class="mt-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-semibold text-amber-800"
                        role="status" aria-live="polite">
                        The 5 OJT units will count in the Day summary.
                    </p>
                </div>

                {{-- ── SUBJECT + CATEGORY (side by side) ── --}}
                <div class="flex items-end gap-2 mb-3">
                    <div class="flex-1">
                        <label class="field-label">Category</label>
                        <select id="add-category" class="sr-only" onchange="filterSubjectsByCategory()">
                            <option value="">-- All --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <div class="relative" data-category-picker="add">
                            <button type="button" class="field-input !py-[9px] flex w-full items-center justify-between gap-2 text-left" aria-haspopup="listbox" aria-expanded="false" onclick="toggleCategoryPicker('add')"><span data-category-label>All categories</span><span aria-hidden="true">⌄</span></button>
                            <div class="absolute left-0 top-[calc(100%+6px)] z-[90] hidden w-full min-w-[220px] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl" data-category-panel>
                                <div class="border-b border-slate-100 p-2"><input type="search" data-category-search placeholder="Search categories..." oninput="renderCategoryChoices('add')" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                                <div class="max-h-48 overflow-y-auto p-1" role="listbox" data-category-options></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-[3]">
                        <label class="field-label">Subject</label>
                        <select id="add-subject" class="field-input">
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->course_id ?? $sub->subj_id }}"
                                    data-dept-id="{{ $sub->course_dept_id ?? '' }}"
                                    data-course-code="{{ $sub->course_code ?? ($sub->subj_code ?? '') }}"
                                    data-course-name="{{ $sub->course_name ?? ($sub->subj_name ?? '') }}"
                                    data-year-level="{{ $sub->course_year_level ?? '' }}"
                                    data-semester="{{ $sub->course_semester ?? '' }}"
                                    data-category="{{ $sub->course_category ?? '' }}"
                                    data-lecture-hours="{{ $sub->course_lecture_hours ?? 0 }}"
                                    data-lab-hours="{{ $sub->course_lab_hours ?? 0 }}">
                                    {{ $sub->course_code ?? $sub->subj_code }} —
                                    {{ $sub->course_name ?? $sub->subj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Room</label>
                    <select id="add-room" class="field-input">
                        <option value="">-- Select Room --</option>
                        @foreach ($rooms ?? collect() as $r)
                            <option value="{{ $r->room_id }}">{{ $r->room_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <span class="field-label">Days <span class="font-normal normal-case text-slate-400">(select one or
                            more)</span></span>
                    <div class="mt-1.5 grid grid-cols-2 gap-2 sm:grid-cols-3">
                        @foreach ($days as $d)
                            <label
                                class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-slate-200 px-2 py-2 text-xs text-slate-700 hover:border-blue-300 hover:bg-blue-50">
                                <input type="checkbox" name="add-days[]" value="{{ $d }}"
                                    class="add-day-checkbox rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                <span>{{ $d }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Start Time</label>
                        <input type="time" id="add-start" step="1800" class="field-input">
                    </div>
                    <div>
                        <label class="field-label">End Time</label>
                        <input type="time" id="add-end" step="1800" class="field-input">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Description (optional)</label>
                    <textarea id="add-description" rows="3" class="field-input resize-y"></textarea>
                </div>

            </div>

            <div id="add-error" class="hidden mx-4 mb-3 rounded-lg border-2 border-red-400 bg-red-50 px-4 py-3">
                <div class="flex items-start gap-2">
                    <span class="mt-0.5 text-red-500 font-bold text-lg leading-none">⚠</span>
                    <span id="add-error-msg" class="text-sm font-medium text-red-700 leading-snug"></span>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-add-schedule')"
                    class="btn btn-secondary">Cancel</button>
                <button type="button" id="add-another-submit" onclick="submitAdd(true)"
                    class="btn btn-secondary">Save &amp; Add Another</button>
                <button type="button" id="add-submit" onclick="submitAdd()" class="btn btn-primary">Confirm
                    Schedule</button>
            </div>
        </div>
    </div>


    {{-- ══════════════ MODAL: EDIT ══════════════ --}}
    <div class="modal-overlay" id="modal-edit-schedule">
        <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]">
            <div class="modal-header">
                <div class="modal-title text-indigo-700">Edit Schedule</div>
                <button type="button" onclick="closeModal('modal-edit-schedule')" class="modal-close">✕</button>
            </div>

            <div class="max-h-[62vh] overflow-y-auto pr-1">
                <div id="edit-error"
                    class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[12.5px] text-red-700 mb-3">
                </div>
                <div class="mb-3 rounded-lg border border-blue-100 bg-blue-50 px-3 py-2 text-[12px] text-blue-800">
                    Changes apply to this schedule only. All other schedule rows stay unchanged.
                </div>
                {{-- FIXED FIELDS --}}
                <div class="mb-3">
                    <label class="field-label">Semester</label>
                    <div id="edit-semester-label" class="field-input bg-slate-100 font-semibold text-slate-700">
                        {{ optional($activeSemester)->label ?? 'No active semester' }}
                    </div>
                    <input type="hidden" id="edit-semester" value="{{ optional($activeSemester)->sem_id ?? '' }}">
                </div>

                <div class="mb-3">
                    <label class="field-label">Department</label>
                    <select id="edit-department" class="field-input w-full">
                        <option value="">-- Select Department --</option>
                        @foreach ($allDepartments as $dept)
                            <option value="{{ $dept->dept_id }}" @selected(($selectedDeptId ?? '') === $dept->dept_id)>
                                {{ $dept->dept_code }} — {{ $dept->dept_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="field-label">Professor</label>
                    <div class="relative" id="pbt-edit-teacher-picker">
                        <input type="hidden" id="edit-faculty" value="">
                        <button type="button" id="pbt-edit-teacher-trigger" onclick="togglePbtEditTeacherPicker()"
                            aria-haspopup="listbox" aria-expanded="false"
                            class="field-input flex w-full items-center justify-between text-left">
                            <span id="pbt-edit-teacher-label" class="block truncate">Choose a professor</span>
                            <span aria-hidden="true" class="text-slate-400">⌄</span>
                        </button>
                        <div id="pbt-edit-teacher-panel"
                            class="absolute left-0 top-[calc(100%+6px)] z-[70] hidden w-full min-w-[260px] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                            <div class="border-b border-slate-100 bg-slate-50 p-3">
                                <input id="pbt-edit-teacher-search" type="search" oninput="filterPbtEditTeachers(this.value)"
                                    placeholder="Search professors..." autocomplete="off" aria-label="Search professors"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-400 focus:ring-2 focus:ring-indigo-100">
                            </div>
                            <div id="pbt-edit-teacher-options" role="listbox" aria-label="Professors" class="max-h-52 overflow-y-auto p-2">
                                @foreach ($editableFaculty as $person)
                                    @php $professorName = \App\Services\FacultyNameFormatter::format($person); @endphp
                                    <button type="button" role="option" aria-selected="false"
                                        data-id="{{ $person->fac_id }}" data-name="{{ strtolower($person->fac_is_placeholder ? \App\Services\UnassignedFacultyService::LABEL : $professorName) }}"
                                        data-label="{{ $person->fac_is_placeholder ? \App\Services\UnassignedFacultyService::LABEL : $professorName }}" onclick="selectPbtEditTeacher(this)"
                                        class="pbt-edit-teacher-option flex w-full items-center rounded-lg px-3 py-2 text-left text-sm text-slate-700 hover:bg-indigo-50">
                                        <span class="min-w-0 flex-1 truncate">{{ $person->fac_is_placeholder ? \App\Services\UnassignedFacultyService::LABEL : $professorName }}</span>
                                        <span class="pbt-edit-teacher-check hidden font-bold text-indigo-600" aria-hidden="true">✓</span>
                                    </button>
                                @endforeach
                                <div id="pbt-edit-teacher-empty" class="hidden px-3 py-5 text-center text-sm text-slate-500">No professors match that search.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" id="edit-id">

                {{-- SECTION (triggers subject filter) --}}
                <div class="relative mb-3" data-pbt-section-picker>
                    <label class="field-label">Section</label>
                    <select id="edit-section" class="sr-only">
                        <option value="">-- Select Section --</option>
                        @foreach ($sections as $sec)
                            <option value="{{ $sec->sec_id }}" data-dept-id="{{ $sec->sec_dept_id }}"
                                data-year-level="{{ $sec->sec_year_level ?? '' }}"
                                data-is-evening="{{ $sec->sec_is_evening ? '1' : '0' }}">{{ $sec->sec_name }}
                            </option>
                        @endforeach
                    </select>
                    <button type="button" data-pbt-section-trigger aria-expanded="false"
                        class="field-input flex w-full items-center justify-between text-left">
                        <span data-pbt-section-label>-- Select Section --</span><span aria-hidden="true">⌄</span>
                    </button>
                    <div data-pbt-section-panel
                        class="absolute z-50 mt-1 hidden w-[min(100%,420px)] rounded-lg border border-slate-200 bg-white p-2 shadow-xl">
                        <input type="search" data-pbt-section-search placeholder="Search sections..."
                            class="mb-2 w-full rounded-md border border-slate-200 px-2.5 py-2 text-xs outline-none focus:border-indigo-400">
                        <div class="mb-2 flex gap-1" role="group" aria-label="Filter sections by shift">
                            <button type="button" data-pbt-shift="all"
                                class="rounded border px-2 py-1 text-[10px]">All <span
                                    data-pbt-shift-count="all"></span></button>
                            <button type="button" data-pbt-shift="day"
                                class="rounded border px-2 py-1 text-[10px]">Day <span
                                    data-pbt-shift-count="day"></span></button>
                            <button type="button" data-pbt-shift="evening"
                                class="rounded border px-2 py-1 text-[10px]">Evening <span
                                    data-pbt-shift-count="evening"></span></button>
                        </div>
                        <div class="mb-2 flex flex-wrap gap-1" role="group" aria-label="Filter sections by year">
                            @foreach ([['all', 'All years'], ['1', '1st'], ['2', '2nd'], ['3', '3rd'], ['4', '4th'], ['5', '5th']] as [$yearFilter, $yearText])
                                <button type="button" data-pbt-year="{{ $yearFilter }}"
                                    class="rounded-full border px-2 py-1 text-[9px]">{{ $yearText }} <span
                                        data-pbt-year-count="{{ $yearFilter }}"></span></button>
                            @endforeach
                        </div>
                        <div data-pbt-section-options class="max-h-48 overflow-y-auto"></div>
                    </div>
                </div>

                {{-- SUBJECT + CATEGORY --}}
                <div class="flex items-end gap-2 mb-3">
                    <div class="flex-1">
                        <label class="field-label">Category</label>
                        <select id="edit-category" class="sr-only" onchange="filterEditSubjectsByCategory()">
                            <option value="">-- All --</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                        <div class="relative" data-category-picker="edit">
                            <button type="button" class="field-input !py-[9px] flex w-full items-center justify-between gap-2 text-left" aria-haspopup="listbox" aria-expanded="false" onclick="toggleCategoryPicker('edit')"><span data-category-label>All categories</span><span aria-hidden="true">⌄</span></button>
                            <div class="absolute left-0 top-[calc(100%+6px)] z-[90] hidden w-full min-w-[220px] overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl" data-category-panel>
                                <div class="border-b border-slate-100 p-2"><input type="search" data-category-search placeholder="Search categories..." oninput="renderCategoryChoices('edit')" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-xs focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100"></div>
                                <div class="max-h-48 overflow-y-auto p-1" role="listbox" data-category-options></div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-[3]">
                        <label class="field-label">Subject</label>
                        <select id="edit-subject" class="field-input">
                            <option value="">-- Select Subject --</option>
                            @foreach ($subjects as $sub)
                                <option value="{{ $sub->course_id ?? $sub->subj_id }}"
                                    data-dept-id="{{ $sub->course_dept_id ?? '' }}"
                                    data-course-code="{{ $sub->course_code ?? ($sub->subj_code ?? '') }}"
                                    data-year-level="{{ $sub->course_year_level ?? '' }}"
                                    data-category="{{ $sub->course_category ?? '' }}"
                                    data-semester="{{ $sub->course_semester ?? '' }}"
                                    data-lecture-hours="{{ $sub->course_lecture_hours ?? 0 }}"
                                    data-lab-hours="{{ $sub->course_lab_hours ?? 0 }}">
                                    {{ $sub->course_code ?? $sub->subj_code }} —
                                    {{ $sub->course_name ?? $sub->subj_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="field-label">Room</label>
                    <select id="edit-room" class="field-input">
                        <option value="">-- Select Room --</option>
                        @foreach ($rooms ?? collect() as $r)
                            <option value="{{ $r->room_id }}">{{ $r->room_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="field-label">Day</label>
                    <select id="edit-day" class="field-input">
                        <option value="">-- Select Day --</option>
                        @foreach ($days as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Start Time</label>
                        <input type="time" id="edit-start" step="1800" class="field-input">
                    </div>
                    <div>
                        <label class="field-label">End Time</label>
                        <input type="time" id="edit-end" step="1800" class="field-input">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Description (optional)</label>
                    <textarea id="edit-description" rows="3" class="field-input resize-y"></textarea>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-edit-schedule')"
                    class="btn btn-secondary">Cancel</button>
                <button type="button" id="edit-submit" onclick="submitEdit()" class="btn btn-primary">Edit
                    Schedule</button>
            </div>
        </div>
    </div>



    {{-- ══════════════ MODAL: DELETE ══════════════ --}}
    <div class="modal-overlay" id="modal-delete-schedule">
        <div class="modal-box w-[420px] max-w-[calc(100vw-32px)]">
            <div class="modal-header">
                <div class="modal-title text-red-600">Delete Schedule</div>
                <button type="button" onclick="closeModal('modal-delete-schedule')" class="modal-close">✕</button>
            </div>

            <div class="text-center">
                <input type="hidden" id="delete-id">

                <p class="text-[13px] font-semibold text-slate-700 mb-3">
                    Are you sure you want to delete this specific schedule?
                </p>

                <p class="text-[12.5px] text-slate-600 mb-2">
                    To confirm, please type
                    <span class="font-mono font-bold text-slate-900" id="delete-code"></span>
                    in the box below:
                </p>

                <input type="text" id="delete-confirm" autocomplete="off" oninput="validateDeleteCode()"
                    class="field-input text-center font-mono mb-3">

                <p class="text-[11.5px] text-slate-500 mb-1">
                    Note: When confirmed, this schedule won't be retrieved in any way.
                </p>

                <div id="delete-error"
                    class="hidden rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-[12.5px] text-red-700 text-left">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeModal('modal-delete-schedule')"
                    class="btn btn-secondary">Cancel</button>
                <button type="button" id="delete-submit" disabled onclick="submitDelete()"
                    class="btn btn-danger opacity-50 cursor-not-allowed">Delete Schedule</button>
            </div>
        </div>
        {{-- ══════════════ MODAL: SUBMIT PBT TO DEAN ══════════════ --}}
        @if ($selectedFaculty && $activeSemester)
            <div class="modal-overlay" id="modal-submit-dean">
                <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]" role="dialog" aria-modal="true"
                    aria-labelledby="submit-dean-modal-title">
                    <div class="modal-header">
                        <div class="modal-title text-indigo-700" id="submit-dean-modal-title">Submit PBT to Dean</div>
                        <button type="button" onclick="closeModal('modal-submit-dean')" class="modal-close"
                            aria-label="Close submit confirmation">✕</button>
                    </div>

                    <div class="max-h-[62vh] overflow-y-auto pr-1">
                        <div class="mb-3">
                            <label class="field-label">Semester</label>
                            <div class="field-input bg-slate-100 font-semibold text-slate-700">
                                {{ $activeSemester->label ?? ($activeSemester->sem_name ?? 'Selected semester') }}
                            </div>
                        </div>

                        <div
                            class="mb-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-[12.5px] text-slate-600">
                            Faculty:
                            <span class="font-bold text-slate-900">
                                {{ \App\Services\FacultyNameFormatter::format($selectedFaculty) }}
                            </span>
                        </div>

                        <div
                            class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-3 text-sm leading-relaxed text-amber-800">
                            This will send this faculty member’s active-semester PBT schedule to the Dean for review.
                            The system will block submission if required schedule details are missing or schedule
                            conflicts
                            are found.
                        </div>
                    </div>

                    <form method="POST" action="{{ route('chair.submit_dean.store') }}"
                        class="mt-5 flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 pt-4">
                        @csrf
                        <input type="hidden" name="faculty_id" value="{{ $selectedFaculty->fac_id }}">
                        <input type="hidden" name="return_to_pbt" value="1">
                        <input type="hidden" name="shift" value="{{ $shift }}">

                        <button type="button" onclick="closeModal('modal-submit-dean')" class="btn btn-secondary">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Confirm &amp; Submit to Dean
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- TOAST --}}
        <div class="toast" id="toast" role="alert" aria-live="assertive"><span id="toast-msg"></span></div>

        <script>
            window.PBT_SELECTED_DATE = @json($selectedDate->toDateString());
            window.PBT_SELECTED_TEACHER = @json($selectedFaculty->fac_id ?? null);
            window.PBT_PLACEHOLDER_FACULTY = @json($placeholderFaculty?->fac_id);
            window.COLLEGE_SECTIONS = {
                @foreach ($allDepartments as $dept)
                    "{{ $dept->dept_id }}": [
                        @php $deptSections = $sections->where('sec_dept_id', $dept->dept_id); @endphp
                        @foreach ($deptSections as $sec)
                            {
                                id: "{{ $sec->sec_id }}",
                                name: "{{ $sec->sec_name }}",
                                dept: "{{ $sec->sec_dept_id }}",
                                isEvening: "{{ $sec->sec_is_evening ? '1' : '0' }}",
                                year: "{{ $sec->sec_year_level }}"
                            },
                        @endforeach
                    ],
                @endforeach
            };
            window.COLLEGE_SUBJECTS = {
                @foreach ($allDepartments as $dept)
                    "{{ $dept->dept_id }}": [
                        @foreach ($subjects->where('course_dept_id', $dept->dept_id) as $sub)
                            {
                                id: "{{ $sub->course_id ?? $sub->subj_id }}",
                                code: "{{ $sub->course_code ?? $sub->subj_code }}",
                                name: "{{ $sub->course_name ?? $sub->subj_name }}",
                                dept: "{{ $sub->course_dept_id ?? '' }}",
                                year: "{{ $sub->course_year_level ?? '' }}",
                                sem: "{{ $sub->course_semester ?? '' }}",
                                cat: "{{ $sub->course_category ?? '' }}",
                                lecHours: "{{ $sub->course_lecture_hours ?? 0 }}",
                                labHours: "{{ $sub->course_lab_hours ?? 0 }}"
                            },
                        @endforeach
                    ],
                @endforeach
            };
            window.ALL_SUBJECTS_LIST = [
                @foreach ($allSubjects as $sub)
                    {
                        id: "{{ $sub->course_id ?? $sub->subj_id }}",
                        code: "{{ $sub->course_code ?? $sub->subj_code }}",
                        name: "{{ $sub->course_name ?? $sub->subj_name }}",
                        deptCode: "{{ $sub->program?->dept_code ?? '' }}",
                        year: "{{ $sub->course_year_level ?? '' }}",
                        sem: "{{ $sub->course_semester ?? '' }}",
                        cat: "{{ $sub->course_category ?? '' }}",
                        dept: "{{ $sub->course_dept_id ?? '' }}",
                        lecHours: "{{ $sub->course_lecture_hours ?? 0 }}",
                        labHours: "{{ $sub->course_lab_hours ?? 0 }}"
                    },
                @endforeach
            ];
            window.CAN_PLOT_SELECTED_SEMESTER = @json($canPlotSelectedSemester ?? false);
            window.ACTIVE_COURSE_SEMESTER = @json(str_contains(strtolower($activeSemester->sem_name ?? ''), '2nd') ||
                str_contains(strtolower($activeSemester->sem_name ?? ''), 'second')
                    ? 2
                    : 1);
            window.ALL_CATEGORIES = @json($categories ?? []);
        </script>
        <script src="{{ asset('js/pbt.js') }}?v={{ filemtime(public_path('js/pbt.js')) }}" defer></script>
        <script src="{{ asset('js/schedule-signoff.js') }}" defer></script>

        @if (session('error') || session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    showToast(@json(session('error') ?? session('success')));
                });
            </script>
        @endif

        @include('chair.partials.export_preview_dialog')
</body>

</html>
