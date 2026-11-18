<div class="space-y-5">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <div class="text-xs font-bold uppercase tracking-widest text-blue-600">{{ $kind }} export preview
            </div>
            <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $title }}</h3>
            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" data-export-preview-close
                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                Back to edit
            </button>
            @if ($kind === 'PBS')
                <button type="button" data-export-pbs-confirm
                    data-student-url="{{ route('chair.pbs.section.students') }}"
                    data-section-id="{{ request()->query('section') }}" data-download-url="{{ $downloadUrl }}"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Confirm and Download Excel
                </button>
            @else
                <a href="{{ $downloadUrl }}"
                    class="rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">
                    Confirm and Download {{ $kind === 'MIS' ? 'DOCX' : 'Excel' }}
                </a>
            @endif
        </div>
    </div>

    @if ($kind === 'PBS')
        <section class="rounded-2xl border border-blue-100 bg-blue-50 p-4">
            <label for="preview-student-count" class="block text-sm font-semibold text-slate-800">Number of
                students</label>
            <input id="preview-student-count" type="number" min="0" max="500" step="1"
                value="{{ $studentCount }}"
                class="mt-2 w-32 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
            <p class="mt-1 text-xs text-slate-500">This count will be saved to the section when you confirm the
                download.</p>
        </section>
    @endif

    @if ($rows->isEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500">
            No schedule entries match these export filters.
        </section>
    @else
        <div class="space-y-5">
            @foreach ($displayShifts as $shiftKey => $shiftTitle)
                @php
                    $shiftRows = $rowsByShift[$shiftKey] ?? collect();
                    $shiftCourses = $coursesByShift[$shiftKey] ?? collect();
                @endphp
                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                        <div>
                            <h4 class="font-semibold">{{ $shiftTitle }}</h4>
                            <p class="mt-1 text-xs text-slate-500">{{ $shiftRows->count() }} plotted class entries ·
                                {{ $semesterLabel }}</p>
                        </div>
                        <span
                            class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $kind }}</span>
                    </div>
                    <div
                        class="grid gap-4 p-4 {{ in_array($kind, ['PBS', 'PBT'], true) ? 'lg:grid-cols-2' : 'grid-cols-1' }}">
                        @if (in_array($kind, ['PBS', 'PBT'], true))
                            <div class="min-w-0 overflow-hidden rounded-xl border border-slate-200">
                                <h5 class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold">
                                    Summary of Courses</h5>
                                @if ($shiftCourses->isEmpty())
                                    <p class="px-4 py-5 text-center text-sm text-slate-500">No course assignments for
                                        this shift.</p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="min-w-[540px] w-full text-left text-sm">
                                            <thead class="bg-white text-[11px] uppercase tracking-wide text-slate-500">
                                                <tr>
                                                    <th class="px-3 py-3">Course Code</th>
                                                    <th class="px-3 py-3">Descriptive Title</th>
                                                    <th class="px-3 py-3">Degree Yr. &amp; Sec.</th>
                                                    <th class="px-3 py-3">Total No. of Students</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach ($shiftCourses as $course)
                                                    <tr>
                                                        <td class="px-3 py-3 font-semibold">
                                                            {{ $course['course_code'] }}</td>
                                                        <td class="px-3 py-3">{{ $course['course_name'] }}</td>
                                                        <td class="px-3 py-3">{{ $course['degree_section'] }}</td>
                                                        <td class="px-3 py-3">{{ $course['student_count'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                                @if ($kind === 'PBT')
                                    @php $workload = $workloadByShift[$shiftKey] ?? ['preparations' => 0, 'units' => 0, 'hours' => 0]; @endphp
                                    <div class="border-t border-slate-200 bg-slate-50 px-4 py-3">
                                        <h6 class="mb-2 text-xs font-semibold text-slate-700">PBT Workload Summary</h6>
                                        <dl class="space-y-1.5 text-sm">
                                            <div class="flex justify-between gap-4">
                                                <dt class="text-slate-600">No. of Preparations:</dt>
                                                <dd class="font-medium text-slate-900">{{ $workload['preparations'] }}
                                                </dd>
                                            </div>
                                            <div class="flex justify-between gap-4">
                                                <dt class="text-slate-600">No. of Units:</dt>
                                                <dd class="font-medium text-slate-900">{{ $workload['units'] }}</dd>
                                            </div>
                                            <div class="flex justify-between gap-4">
                                                <dt class="text-slate-600">No. of Hours/Week:</dt>
                                                <dd class="font-medium text-slate-900">{{ $workload['hours'] }}</dd>
                                            </div>
                                        </dl>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="min-w-0 overflow-hidden rounded-xl border border-slate-200">
                            <h5 class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold">Schedule
                                Details</h5>
                            @if ($shiftRows->isEmpty())
                                <p class="px-4 py-5 text-center text-sm text-slate-500">No schedule entries for this
                                    shift.</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-[780px] w-full text-left text-sm">
                                        <thead class="bg-white text-[11px] uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="px-3 py-3">Section</th>
                                                <th class="px-3 py-3">Faculty</th>
                                                <th class="px-3 py-3">Course</th>
                                                <th class="px-3 py-3">Descriptive Title</th>
                                                <th class="px-3 py-3">Day &amp; Time</th>
                                                <th class="px-3 py-3">Room</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100">
                                            @foreach ($shiftRows as $row)
                                                @php
                                                    try {
                                                        $timeText =
                                                            \Illuminate\Support\Carbon::parse(
                                                                $row['start_time'],
                                                            )->format('g:i A') .
                                                            ' – ' .
                                                            \Illuminate\Support\Carbon::parse($row['end_time'])->format(
                                                                'g:i A',
                                                            );
                                                    } catch (\Throwable $exception) {
                                                        $timeText =
                                                            ($row['start_time'] ?? '—') .
                                                            ' – ' .
                                                            ($row['end_time'] ?? '—');
                                                    }
                                                @endphp
                                                <tr class="align-top">
                                                    <td class="whitespace-nowrap px-3 py-3">
                                                        {{ $row['section'] ?? '—' }}</td>
                                                    <td class="px-3 py-3">{{ $row['faculty'] ?? '—' }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3 font-semibold">
                                                        {{ $row['course_code'] ?? '—' }}</td>
                                                    <td class="min-w-[180px] px-3 py-3">
                                                        {{ $row['course_name'] ?? '—' }}</td>
                                                    <td class="whitespace-nowrap px-3 py-3">{{ $row['day'] ?? '—' }}
                                                        <div class="mt-1 text-xs text-slate-500">{{ $timeText }}
                                                        </div>
                                                    </td>
                                                    <td class="whitespace-nowrap px-3 py-3">{{ $row['room'] ?? '—' }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            @endforeach
        </div>
    @endif
</div>
