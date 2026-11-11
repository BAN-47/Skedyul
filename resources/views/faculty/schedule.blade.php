<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — My Schedule</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            body {
                background: #fff !important;
                color: #111827 !important;
                overflow: visible !important;
            }

            .sidebar,
            .topbar,
            .no-print {
                display: none !important;
            }

            #screen-app,
            .main,
            .page,
            .page-content {
                display: block !important;
                width: 100% !important;
                height: auto !important;
                max-height: none !important;
                overflow: visible !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .schedule-layout {
                grid-template-columns: 260px minmax(0, 1fr) !important;
                gap: 8px !important;
            }

            .schedule-scroll {
                overflow: visible !important;
            }

            .schedule-min-width {
                min-width: 0 !important;
            }

            .print-card {
                break-inside: avoid;
                box-shadow: none !important;
            }

            .signature-card {
                break-inside: avoid;
                margin-top: 12px !important;
            }

            a {
                color: inherit !important;
                text-decoration: none !important;
            }
        }
    </style>
</head>

<body class="bg-slate-50 font-sans text-slate-900">
    @php
        $days = $days ?? ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $shift = $shift ?? 'day';
        $gridStart = $shift === 'night' ? 16 * 60 : 7 * 60;
        $gridEnd = $shift === 'night' ? 21 * 60 + 30 : 16 * 60;
        $slotMinutes = 30;
        $slotCount = intdiv($gridEnd - $gridStart, $slotMinutes);
        $showNoonBreak = $shift === 'day';
        $signatories = $signatories ?? [];
    @endphp

    <div id="screen-app" class="screen active flex-row">
        @include('partials.facultyMember_sidebar')

        <div class="main">
            @php
                $scheduleFeed = $allSchedules
                    ->map(
                        fn($schedule) => [
                            'code' => $schedule->subject->course_code ?? 'N/A',
                            'room' => $schedule->room->room_name ?? 'N/A',
                            'day' => $schedule->sch_day ?? '',
                            'start' => \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('H:i'),
                            'end' => \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('H:i'),
                        ],
                    )
                    ->values()
                    ->all();
            @endphp
            @include('partials.faculty_header', [
                'title' => 'My Schedule',
                'scheduleFeed' => $scheduleFeed,
                'announcements' => $announcements ?? null,
            ])

            <main class="page-content" id="page-faculty-schedule">
                <div class="no-print mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-extrabold text-slate-900">My Teaching Schedule</h1>
                        <p class="mt-1 text-sm text-slate-500">View-only schedule and course summary for
                            {{ $activeSemester->label ?? 'the active semester' }}.</p>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button type="button" id="schedule-summary-toggle" aria-expanded="false"
                            aria-controls="schedule-summary-modal" onclick="toggleScheduleSummary()"
                            class="btn btn-secondary inline-flex items-center gap-2">
                            <span>Summary</span><span aria-hidden="true">▦</span>
                        </button>
                        <a href="{{ route('faculty.pbt_export', ['semester' => $activeSemester?->sem_id]) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            Download XLSX
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-secondary">
                            Print Schedule
                        </button>
                    </div>
                </div>

                {{-- SUMMARY MODAL (PBT STYLE) --}}
                <div id="schedule-summary-modal"
                    class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/40 p-4 opacity-0 transition-opacity duration-200"
                    role="dialog" aria-modal="true" aria-labelledby="schedule-summary-title"
                    onclick="if(event.target === this) closeScheduleSummary()">
                    <aside id="schedule-summary-panel"
                        class="card !p-0 w-[min(1100px,calc(100vw-2rem))] max-h-[calc(100dvh-2rem)] overflow-y-auto self-center shadow-2xl scale-95 transition-transform duration-200">
                        <div
                            class="sticky top-0 z-10 flex items-center justify-between bg-blue-600 text-white px-4 py-3">
                            <span id="schedule-summary-title" class="text-[12px] font-extrabold tracking-wide">
                                Summary of Courses
                            </span>
                            <button type="button" onclick="closeScheduleSummary()"
                                class="rounded-lg px-2 py-1 text-lg leading-none hover:bg-white/20"
                                aria-label="Close summary">✕</button>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-[10px] border-collapse">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-600 uppercase font-bold">
                                        <th class="px-1.5 py-1.5 text-left border-b border-slate-200">Course Code</th>
                                        <th class="px-1.5 py-1.5 text-left border-b border-l border-slate-200">
                                            Descriptive Title</th>
                                        <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">Units
                                        </th>
                                        <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">
                                            Scheduled<br>Hours</th>
                                        <th class="px-1.5 py-1.5 text-left border-b border-l border-slate-200">
                                            Degree<br>Yr. &amp; Sec.</th>
                                        <th class="px-1.5 py-1.5 text-center border-b border-l border-slate-200">
                                            Total<br>No. of Students</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($courseRows as $schedule)
                                        @php
                                            $course = $schedule->subject;
                                            $section = $schedule->section;
                                            $courseHours = \App\Services\ScheduleAssignmentService::courseHours(
                                                $course,
                                            );
                                            $courseMeetings = $allSchedules
                                                ->where('sch_course_id', $schedule->sch_course_id)
                                                ->where('sch_sec_id', $schedule->sch_sec_id);
                                            $meetingHours = fn($m) => \Illuminate\Support\Carbon::parse(
                                                $m->sch_start_time,
                                            )->diffInMinutes(\Illuminate\Support\Carbon::parse($m->sch_end_time)) / 60;
                                            $scheduledHours = $courseMeetings->sum($meetingHours);
                                        @endphp
                                        <tr class="border-b border-slate-100 text-slate-700">
                                            <td class="px-1.5 py-1.5 font-bold text-slate-800 whitespace-nowrap">
                                                {{ $course->course_code ?? '—' }}</td>
                                            <td class="px-1.5 py-1.5 border-l border-slate-100 leading-snug">
                                                {{ $course->course_name ?? '—' }}</td>
                                            <td class="px-1.5 py-1.5 border-l border-slate-100 text-center">
                                                {{ $course->course_units ?? 0 }}</td>
                                            <td class="px-1.5 py-1.5 border-l border-slate-100 text-center"
                                                title="{{ $scheduledHours }} of {{ $courseHours }} total hours scheduled">
                                                {{ $scheduledHours }}/{{ $courseHours }}</td>
                                            <td class="px-1.5 py-1.5 border-l border-slate-100 whitespace-nowrap">
                                                {{ $section->sec_name ?? '—' }}</td>
                                            <td
                                                class="px-1.5 py-1.5 border-l border-slate-100 text-center font-semibold">
                                                {{ $section->sec_no_of_student ?? ($section->sec_max_capacity ?? '—') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="px-2 py-4 text-center italic text-slate-400">No
                                                courses assigned this semester.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="border-t border-slate-200 p-3 text-[11px] text-slate-700 space-y-1">
                            <div class="flex justify-between gap-2"><span class="font-bold">No. of
                                    Preparations:</span><span>{{ $loadStats['preparations'] ?? 0 }}</span></div>
                            <div class="flex justify-between gap-2"><span class="font-bold">No. of
                                    Units:</span><span>{{ $loadStats['units'] ?? 0 }}</span></div>
                            <div class="flex justify-between gap-2"><span class="font-bold">No. of
                                    Hours/Week:</span><span>{{ $loadStats['hours_week'] ?? 0 }}</span></div>
                            <div class="flex justify-between gap-2"><span class="font-bold">Teaching Load
                                    Limit:</span><span>{{ isset($loadStats['load_limit']) ? rtrim(rtrim(number_format((float) $loadStats['load_limit'], 2), '0'), '.') . 'h' : '—' }}</span>
                            </div>
                            <div class="flex justify-between gap-2"><span class="font-bold">Administrative
                                    Designation:</span><span
                                    class="text-right">{{ $loadStats['designation'] ?: '—' }}</span></div>
                            <div class="border-t border-slate-100 my-1.5"></div>
                            <div class="flex justify-between gap-2"><span
                                    class="font-bold">Production:</span><span>{{ $loadStats['production'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between gap-2"><span
                                    class="font-bold">Extension:</span><span>{{ $loadStats['extension'] ?? '—' }}</span>
                            </div>
                            <div class="flex justify-between gap-2"><span
                                    class="font-bold">Research:</span><span>{{ $loadStats['research'] ?? '—' }}</span>
                            </div>
                        </div>
                    </aside>
                </div>

                <section class="print-card card !p-0 overflow-hidden w-full">
                    <div
                        class="no-print flex flex-wrap items-center gap-2 border-b border-slate-200 bg-slate-50 px-3 py-2.5">
                        <div
                            class="inline-flex min-w-[220px] items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <span
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-sm font-bold text-indigo-700">
                                {{ strtoupper(substr($faculty->fac_first_name ?? '', 0, 1) . substr($faculty->fac_last_name ?? '', 0, 1)) }}
                            </span>
                            <span class="min-w-0">
                                <span
                                    class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Viewing
                                    my schedule</span>
                                <span
                                    class="block truncate text-[13px] font-semibold text-slate-800">{{ $faculty->full_name }}</span>
                            </span>
                        </div>

                        <form method="GET" action="{{ route('faculty.schedule') }}">
                            <input type="hidden" name="shift" value="{{ $shift }}">
                            <label for="faculty-semester" class="sr-only">Academic year and semester</label>
                            <select id="faculty-semester" name="semester" onchange="this.form.submit()"
                                class="rounded-lg border border-slate-300 bg-white px-3 py-2 text-[11px] font-semibold text-slate-700">
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->sem_id }}"
                                        @selected(($activeSemester?->sem_id ?? '') === $semester->sem_id)>
                                        {{ $semester->label }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        <div class="flex overflow-hidden rounded-lg border border-slate-300"
                            aria-label="Schedule shift">
                            <a href="{{ route('faculty.schedule', ['shift' => 'day', 'semester' => $activeSemester?->sem_id]) }}"
                                class="px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'day' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">Day</a>
                            <a href="{{ route('faculty.schedule', ['shift' => 'night', 'semester' => $activeSemester?->sem_id]) }}"
                                class="border-l border-slate-300 px-3 py-2 text-[11px] font-bold uppercase {{ $shift === 'night' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600' }}">Evening</a>
                        </div>

                        <span class="ml-auto text-[11px] italic text-slate-400">View only</span>
                    </div>

                    <div class="schedule-scroll overflow-x-auto">
                        <div class="schedule-min-width min-w-[840px]">
                            <div class="grid border-b border-slate-200 bg-slate-100"
                                style="grid-template-columns:90px repeat({{ count($days) }},minmax(0,1fr));">
                                <div
                                    class="px-2 py-2 text-center text-[10px] font-bold uppercase tracking-wide text-slate-500">
                                    Time</div>
                                @foreach ($days as $day)
                                    <div
                                        class="border-l border-slate-200 px-2 py-2 text-center text-[11px] font-bold text-slate-700">
                                        {{ $day }}</div>
                                @endforeach
                            </div>

                            <div id="faculty-week-grid" class="relative grid bg-white"
                                style="grid-template-columns:90px repeat({{ count($days) }},minmax(0,1fr)); grid-template-rows:repeat({{ $slotCount }},26px);">
                                @for ($slot = 0; $slot < $slotCount; $slot += 2)
                                    @php
                                        $minutes = $gridStart + $slot * $slotMinutes;
                                        $hour = \Illuminate\Support\Carbon::createFromTime(intdiv($minutes, 60), 0);
                                        $hourEnd = (clone $hour)->addHour();
                                    @endphp
                                    <div class="flex items-center justify-end border-b border-slate-200 bg-slate-50 px-2 text-[10px] font-semibold text-slate-500"
                                        style="grid-column:1;grid-row:{{ $slot + 1 }} / span 2;">
                                        {{ $hour->format('g') }}–{{ $hourEnd->format('g A') }}
                                    </div>
                                @endfor

                                @for ($slot = 0; $slot < $slotCount; $slot++)
                                    @php $minutes = $gridStart + ($slot * $slotMinutes); @endphp
                                    @foreach ($days as $dayIndex => $day)
                                        <div class="border-b border-l border-slate-100 {{ $minutes % 60 === 0 ? '!border-b-slate-200' : '' }}"
                                            style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $slot + 1 }};">
                                        </div>
                                    @endforeach
                                @endfor

                                @if ($showNoonBreak)
                                    <div class="pointer-events-none z-10 flex items-center justify-center bg-slate-900 text-[11px] font-bold tracking-[3px] text-white"
                                        style="grid-column:2 / span {{ count($days) }};grid-row:11 / span 2;">NOON
                                        BREAK</div>
                                @endif

                                @foreach ($schedules as $schedule)
                                    @php
                                        $dayIndex = array_search($schedule->sch_day, $days, true);
                                        if ($dayIndex === false) {
                                            continue;
                                        }
                                        $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                                        $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                                        $startMinutes = max($start->hour * 60 + $start->minute, $gridStart);
                                        $endMinutes = min($end->hour * 60 + $end->minute, $gridEnd);
                                        if ($endMinutes <= $startMinutes) {
                                            continue;
                                        }
                                        $row = intdiv($startMinutes - $gridStart, $slotMinutes) + 1;
                                        $span = max(1, (int) ceil(($endMinutes - $startMinutes) / $slotMinutes));
                                        $course = $schedule->subject;
                                    @endphp
                                    <article
                                        class="z-20 m-[3px] flex flex-col items-center justify-center overflow-hidden rounded-md border border-emerald-600 bg-emerald-500 px-1.5 py-1 text-center leading-tight text-white shadow-sm"
                                        style="grid-column:{{ $dayIndex + 2 }};grid-row:{{ $row }} / span {{ $span }};">
                                        <strong
                                            class="text-[12px] font-extrabold tracking-wide">{{ $course->course_code ?? '—' }}</strong>
                                        <span
                                            class="mt-1 text-[10px] font-semibold">{{ $schedule->section->sec_name ?? 'Section' }}</span>
                                        <span
                                            class="text-[10px]">{{ $schedule->room->room_name ?? 'Room not set' }}</span>
                                        <span
                                            class="mt-1 text-[9px] font-medium opacity-90">{{ $course->course_name ?? '' }}</span>
                                    </article>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if ($schedules->isEmpty())
                        <div class="border-t border-slate-100 px-4 py-3 text-center text-[12px] text-slate-500">
                            {{ $activeSemester ? 'No ' . $shift . '-shift classes are assigned to you this semester.' : 'There is no active semester.' }}
                        </div>
                    @endif
                </section>

                <section class="signature-card card mt-4 overflow-x-auto print-card">
                    <table class="w-full min-w-[720px] border-collapse text-center text-[12px]">
                        <thead>
                            <tr class="text-left text-[10px] font-bold uppercase tracking-wide text-slate-600">
                                <th class="w-1/3 border-b border-slate-300 px-4 py-2">Prepared by:</th>
                                <th class="w-1/3 border-b border-l border-slate-300 px-4 py-2">Reviewed, Certified True
                                    and Correct:</th>
                                <th class="w-1/3 border-b border-l border-slate-300 px-4 py-2">Approved by:</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 pb-3 pt-8 align-bottom">
                                    <div class="border-b border-slate-800 pb-1 font-bold uppercase">
                                        {{ $signatories['chair_name'] ?: '—' }}</div>
                                    <div class="pt-1 text-slate-600">
                                        {{ $signatories['chair_title'] ?? 'Department Chair' }}</div>
                                </td>
                                <td class="border-l border-slate-200 px-4 pb-3 pt-8 align-bottom">
                                    <div class="border-b border-slate-800 pb-1 font-bold uppercase">
                                        {{ $signatories['dean_name'] ?: '—' }}</div>
                                    <div class="pt-1 text-slate-600">{{ $signatories['dean_title'] ?? 'Dean, CCICT' }}
                                    </div>
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
            </main>
        </div>
    </div>
    <script>
        function toggleScheduleSummary() {
            const modal = document.getElementById('schedule-summary-modal');
            const panel = document.getElementById('schedule-summary-panel');
            const button = document.getElementById('schedule-summary-toggle');
            const open = modal.classList.contains('hidden');
            modal.classList.toggle('hidden', !open);
            modal.classList.toggle('flex', open);
            requestAnimationFrame(() => {
                modal.classList.toggle('opacity-0', !open);
                panel.classList.toggle('scale-95', !open);
                panel.classList.toggle('scale-100', open);
            });
            button.setAttribute('aria-expanded', String(open));
        }

        function closeScheduleSummary() {
            const modal = document.getElementById('schedule-summary-modal');
            const panel = document.getElementById('schedule-summary-panel');
            const button = document.getElementById('schedule-summary-toggle');
            modal.classList.add('opacity-0');
            panel.classList.add('scale-95');
            panel.classList.remove('scale-100');
            button.setAttribute('aria-expanded', 'false');
            setTimeout(() => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }, 200);
        }

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeScheduleSummary();
        });
    </script>
</body>

</html>
