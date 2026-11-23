<div id="dean-export-preview-modal" class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-4"
    role="dialog" aria-modal="true" aria-labelledby="dean-export-preview-title" aria-hidden="true">
    <div class="flex max-h-[90vh] w-full max-w-5xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
        <div class="flex items-start justify-between gap-4 border-b border-slate-200 px-5 py-4 sm:px-6">
            @php
                $previewSchedules = collect($previewSchedules ?? []);
                $previewShiftData = collect();

                $isEveningSchedule = static function ($schedule): bool {
                    $storedShift = strtolower(trim((string) ($schedule->sch_shift ?? '')));
                    if (in_array($storedShift, ['evening', 'night'], true)) {
                        return true;
                    }

                    if ((bool) ($schedule->section?->sec_is_evening ?? false)) {
                        return true;
                    }

                    try {
                        $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                        return $start->hour * 60 + $start->minute >= 16 * 60;
                    } catch (\Throwable $exception) {
                        return false;
                    }
                };

                foreach (['day' => 'Day Program', 'evening' => 'Evening Program'] as $shiftKey => $shiftTitle) {
                    $shiftSchedules = $previewSchedules
                        ->filter(
                            fn($schedule) => $shiftKey === 'evening'
                                ? $isEveningSchedule($schedule)
                                : !$isEveningSchedule($schedule),
                        )
                        ->values();

                    $courseRows = $shiftSchedules
                        ->filter(fn($schedule) => $schedule->subject !== null)
                        ->groupBy(
                            fn($schedule) => (string) ($schedule->subject->course_id ??
                                ($schedule->sch_course_id ?? '')) .
                                '|' .
                                (string) ($schedule->section?->sec_id ?? ($schedule->sch_sec_id ?? '')),
                        )
                        ->map(function ($meetings) {
                            $first = $meetings->first();
                            $subject = $first->subject;
                            $section = $first->section;

                            return [
                                'course_code' => $subject->course_code ?? '—',
                                'course_name' => $subject->course_name ?? '—',
                                'section' => $section?->sec_name ?? '—',
                                'students' => (int) ($section?->sec_no_of_student ?? 0),
                            ];
                        })
                        ->sortBy(fn($row) => $row['course_code'] . '|' . $row['section'])
                        ->values();

                    $previewShiftData->push([
                        'key' => $shiftKey,
                        'title' => $shiftTitle,
                        'schedules' => $shiftSchedules,
                        'courses' => $courseRows,
                    ]);
                }
            @endphp

            <div id="dean-export-preview-modal"
                class="fixed inset-0 z-[100] hidden items-center justify-center bg-slate-950/60 p-3 sm:p-5"
                role="dialog" aria-modal="true" aria-labelledby="dean-export-preview-title" aria-hidden="true">
                <div
                    class="flex max-h-[94vh] w-full max-w-7xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <header class="flex items-start justify-between gap-4 border-b border-slate-200 px-4 py-3 sm:px-6">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-blue-600">Export preview ·
                                {{ $previewKind }}</p>
                            <h2 id="dean-export-preview-title" class="mt-1 text-xl font-extrabold text-slate-900">
                                {{ $previewTitle }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $previewSubtitle }} · {{ $previewSemesterLabel }}
                            </p>
                            <p class="text-[10px] font-bold uppercase tracking-widest text-blue-600">Export Preview</p>
                            <h2 id="dean-export-preview-title" class="text-lg font-bold text-slate-900">Check the export
                                details</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $previewTitle }} · {{ $previewSubtitle }} · {{ $previewSemesterLabel }}
                            </p>
                        </div>
                        <button type="button" data-dean-preview-close aria-label="Close export preview"
                            class="rounded-lg p-2 text-xl leading-none text-slate-500 hover:bg-slate-100 hover:text-slate-800">&times;</button>
                </div>
                <button type="button" data-dean-preview-close aria-label="Close export preview"
                    class="rounded-lg px-2 py-1 text-xl leading-none text-slate-500 hover:bg-slate-100">&times;</button>
                </header>

                <div class="flex flex-wrap items-center justify-between gap-2 bg-slate-50 px-5 py-3 text-sm sm:px-6">
                    <span class="font-semibold text-slate-700">{{ $previewSchedules->count() }}
                        {{ \Illuminate\Support\Str::plural('class', $previewSchedules->count()) }} ·
                        {{ $previewOwnerName }}</span>
                    @if ($previewKind === 'PBT')
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">Workbook
                            includes Day and Evening schedules</span>
                    @else
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800">All
                            active-semester classes</span>
                    @endif
                    <div
                        class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm sm:px-6">
                        <span class="font-semibold text-slate-700">{{ $previewOwnerName }}</span>
                        <span class="text-xs text-slate-500">{{ $previewSchedules->count() }} schedule entries</span>
                    </div>

                    <div class="min-h-0 flex-1 overflow-auto">
                        <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                            <thead
                                class="sticky top-0 bg-slate-100 text-xs font-bold uppercase tracking-wide text-slate-600">
                                <tr>
                                    <th class="border-b border-slate-200 px-4 py-3">Course</th>
                                    <th class="border-b border-slate-200 px-4 py-3">Subject</th>
                                    <th class="border-b border-slate-200 px-4 py-3">
                                        {{ $previewKind === 'PBT' ? 'Section' : 'Faculty' }}</th>
                                    <th class="border-b border-slate-200 px-4 py-3">Day</th>
                                    <th class="border-b border-slate-200 px-4 py-3">Time</th>
                                    <th class="border-b border-slate-200 px-4 py-3">Shift</th>
                                    <th class="border-b border-slate-200 px-4 py-3">Room</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 text-slate-700">
                                @foreach ($previewSchedules as $schedule)
                                    <tr class="hover:bg-slate-50">
                                        <td class="whitespace-nowrap px-4 py-3 font-semibold">
                                            {{ $schedule->subject?->course_code ?? '—' }}</td>
                                        <td class="px-4 py-3">{{ $schedule->subject?->course_name ?? '—' }}</td>
                                        <td class="px-4 py-3">
                                            @if ($previewKind === 'PBT')
                                                {{ $schedule->section?->sec_name ?? '—' }}
                                            @else
                                                {{ \App\Services\FacultyNameFormatter::format($schedule->faculty) ?: 'Unassigned' }}
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            {{ ucfirst(strtolower((string) $schedule->sch_day)) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            {{ \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') }}–{{ \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}
                                        </td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            {{ ucfirst(strtolower((string) ($schedule->sch_shift ?? 'day'))) }}</td>
                                        <td class="whitespace-nowrap px-4 py-3">
                                            {{ $schedule->room?->room_name ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <main class="min-h-0 flex-1 space-y-4 overflow-y-auto bg-slate-50 p-3 sm:p-5">
                        @foreach ($previewShiftData as $shiftData)
                            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                                <div
                                    class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-4 py-3">
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-800">{{ $shiftData['title'] }}</h3>
                                        <p class="text-[10px] text-slate-500">{{ $shiftData['schedules']->count() }}
                                            schedule entries · {{ $previewSemesterLabel }}</p>
                                    </div>
                                    <span
                                        class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-600">{{ $previewKind }}</span>
                                </div>

                                <div
                                    class="flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 bg-white px-5 py-4 sm:px-6">
                                    <button type="button" data-dean-preview-close
                                        class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back
                                        to schedule</button>
                                    <a href="{{ $previewDownloadUrl }}"
                                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Confirm
                                        and Download XLSX</a>
                                </div>
                                @if ($shiftData['schedules']->isEmpty())
                                    <p class="p-6 text-center text-xs text-slate-500">No schedule entries for this
                                        shift.</p>
                                @else
                                    <div class="grid gap-3 p-3 lg:grid-cols-2">
                                        <div class="min-w-0 overflow-hidden rounded-lg border border-slate-200">
                                            <h4
                                                class="border-b border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold">
                                                Summary of Courses</h4>
                                            <div class="overflow-x-auto">
                                                <table class="w-full min-w-[480px] text-left text-[10px]">
                                                    <thead
                                                        class="bg-white text-[9px] uppercase tracking-wide text-slate-500">
                                                        <tr>
                                                            <th class="px-2 py-2">Course Code</th>
                                                            <th class="px-2 py-2">Descriptive Title</th>
                                                            <th class="px-2 py-2">Degree Yr. &amp; Sec.</th>
                                                            <th class="px-2 py-2 text-right">Students</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                                        @forelse ($shiftData['courses'] as $course)
                                                            <tr>
                                                                <td class="px-2 py-2 font-semibold">
                                                                    {{ $course['course_code'] }}</td>
                                                                <td class="px-2 py-2">{{ $course['course_name'] }}</td>
                                                                <td class="px-2 py-2">{{ $course['section'] }}</td>
                                                                <td class="px-2 py-2 text-right">
                                                                    {{ $course['students'] }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4"
                                                                    class="px-3 py-5 text-center text-slate-500">No
                                                                    course assignments for this shift.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <div class="min-w-0 overflow-hidden rounded-lg border border-slate-200">
                                            <h4
                                                class="border-b border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold">
                                                Schedule Details</h4>
                                            <div class="max-h-72 overflow-auto">
                                                <table class="w-full min-w-[620px] text-left text-[10px]">
                                                    <thead
                                                        class="sticky top-0 bg-white text-[9px] uppercase tracking-wide text-slate-500">
                                                        <tr>
                                                            @if ($previewKind === 'PBT')
                                                                <th class="px-2 py-2">Section</th>
                                                            @else
                                                                <th class="px-2 py-2">Faculty</th>
                                                            @endif
                                                            <th class="px-2 py-2">Course</th>
                                                            <th class="px-2 py-2">Descriptive Title</th>
                                                            <th class="px-2 py-2">Day &amp; Time</th>
                                                            <th class="px-2 py-2">Room</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="divide-y divide-slate-100 text-slate-700">
                                                        @foreach ($shiftData['schedules'] as $schedule)
                                                            <tr class="align-top">
                                                                @if ($previewKind === 'PBT')
                                                                    <td class="whitespace-nowrap px-2 py-2">
                                                                        {{ $schedule->section?->sec_name ?? '—' }}</td>
                                                                @else
                                                                    <td class="whitespace-nowrap px-2 py-2">
                                                                        {{ \App\Services\FacultyNameFormatter::format($schedule->faculty) ?: 'Unassigned' }}
                                                                    </td>
                                                                @endif
                                                                <td class="whitespace-nowrap px-2 py-2 font-semibold">
                                                                    {{ $schedule->subject?->course_code ?? '—' }}</td>
                                                                <td class="min-w-[140px] px-2 py-2">
                                                                    {{ $schedule->subject?->course_name ?? '—' }}</td>
                                                                <td class="whitespace-nowrap px-2 py-2">
                                                                    {{ ucfirst(strtolower((string) $schedule->sch_day)) }}
                                                                    {{ \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') }}–{{ \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}
                                                                </td>
                                                                <td class="whitespace-nowrap px-2 py-2">
                                                                    {{ $schedule->room?->room_name ?? '—' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    </main>

                    <footer
                        class="flex flex-wrap items-center justify-end gap-2 border-t border-slate-200 bg-white px-4 py-3 sm:px-6">
                        <button type="button" data-dean-preview-close
                            class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">Back
                            to schedule</button>
                        <a href="{{ $previewDownloadUrl }}"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-xs font-semibold text-white hover:bg-blue-700">Confirm
                            and Download Excel</a>
                    </footer>
                </div>
            </div>

            <script>
                (() => {
                    const modal = document.getElementById('dean-export-preview-modal');
                    if (!modal || modal.dataset.initialized === 'true') return;
                    modal.dataset.initialized = 'true';

                    const open = () => {
                        modal.classList.remove('hidden');
                        modal.classList.add('flex');
                        modal.setAttribute('aria-hidden', 'false');
                        document.body.classList.add('overflow-hidden');
                        modal.querySelector('[data-dean-preview-close]')?.focus();
                    };
                    const close = () => {
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        modal.setAttribute('aria-hidden', 'true');
                        document.body.classList.remove('overflow-hidden');
                    };

                    document.querySelectorAll('[data-dean-preview-open]').forEach((button) => button.addEventListener('click',
                        open));
                    modal.querySelectorAll('[data-dean-preview-close]').forEach((button) => button.addEventListener('click',
                        close));
                    modal.addEventListener('click', (event) => {
                        if (event.target === modal) close();
                    });
                    document.addEventListener('keydown', (event) => {
                        if (event.key === 'Escape' && !modal.classList.contains('hidden')) close();
                    });
                })();
            </script>
            </script>
