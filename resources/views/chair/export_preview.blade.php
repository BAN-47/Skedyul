<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — {{ $kind }} Export Preview</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'Export Preview',
                'badgeText' => 'Department Chair',
            ])
            <div class="flex-1 overflow-y-auto p-5 md:p-7">
                <div class="mx-auto max-w-7xl">
                    <a href="{{ $backUrl }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700">←
                        Back</a>
                    <div class="mt-4 mb-5 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest text-blue-600">{{ $kind }}
                                EXPORT PREVIEW</div>
                            <h1 class="mt-1 text-2xl font-bold">{{ $title }}</h1>
                            <p class="mt-1 text-sm text-slate-500">{{ $subtitle }}</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ $backUrl }}"
                                class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Back
                                to edit</a>
                            @if ($kind === 'PBS')
                                <button type="button" onclick="confirmPbsDownload()"
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
                        <section class="mb-5 rounded-2xl border border-blue-100 bg-blue-50 p-4">
                            <label for="preview-student-count" class="block text-sm font-semibold text-slate-800">Number
                                of students</label>
                            <input id="preview-student-count" type="number" min="0" max="500"
                                step="1" value="{{ $studentCount }}"
                                class="mt-2 w-32 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm">
                            <p class="mt-1 text-xs text-slate-500">This count will be saved to the section when you
                                confirm the download.</p>
                        </section>

                    @endif

                    @if (in_array($kind, ['PBS', 'PBT'], true))
                        <section class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-5 py-4">
                                <h2 class="font-semibold">Summary of Courses</h2>
                                <p class="mt-1 text-xs text-slate-500">Course assignments and section enrollment,
                                    separated by program shift.</p>
                            </div>
                            <div class="space-y-5 p-4">
                                @foreach (['day' => 'Day Program', 'evening' => 'Evening Program'] as $shiftKey => $shiftTitle)
                                    @php $shiftCourses = $coursesByShift[$shiftKey] ?? collect(); @endphp
                                    <div class="overflow-hidden rounded-xl border border-slate-200">
                                        <div
                                            class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                                            <h3 class="text-sm font-bold text-slate-800">{{ $shiftTitle }}</h3>
                                            <span class="text-xs text-slate-500">{{ $shiftCourses->count() }}
                                                course-section rows</span>
                                            @if ($rows->isEmpty())
                                                <section
                                                    class="overflow-hidden rounded-2xl border border-slate-200 bg-white p-8 text-center text-sm text-slate-500 shadow-sm">
                                                    No schedule entries match these export filters.
                                                </section>
                                            @else
                                                <div class="space-y-5">
                                                    @foreach ($displayShifts as $shiftKey => $shiftTitle)
                                                        @php
                                                            $shiftRows = $rowsByShift[$shiftKey] ?? collect();
                                                            $shiftCourses = $coursesByShift[$shiftKey] ?? collect();
                                                        @endphp
                                                        <section
                                                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                                                            <div
                                                                class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                                                                <div>
                                                                    <h2 class="font-semibold">{{ $shiftTitle }}</h2>
                                                                    <p class="mt-1 text-xs text-slate-500">
                                                                        {{ $shiftRows->count() }} plotted class entries
                                                                        · {{ $semesterLabel }}</p>
                                                                </div>
                                                                <span
                                                                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $kind }}</span>
                                                            </div>
                                                            <div
                                                                class="grid gap-4 p-4 {{ in_array($kind, ['PBS', 'PBT'], true) ? 'lg:grid-cols-2' : 'grid-cols-1' }}">
                                                                @if (in_array($kind, ['PBS', 'PBT'], true))
                                                                    <div
                                                                        class="min-w-0 overflow-hidden rounded-xl border border-slate-200">
                                                                        <h3
                                                                            class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold">
                                                                            Summary of Courses</h3>
                                                                        @if ($shiftCourses->isEmpty())
                                                                            <p
                                                                                class="px-4 py-5 text-center text-sm text-slate-500">
                                                                                No course assignments for this shift.
                                                                            </p>
                                                                        @else
                                                                            <div class="overflow-x-auto">
                                                                                <table
                                                                                    class="min-w-[650px] w-full text-left text-sm">
                                                                                    <thead
                                                                                        class="bg-white text-[11px] uppercase tracking-wide text-slate-500">
                                                                                        <tr>
                                                                                            <th class="px-4 py-3">Course
                                                                                                Code</th>
                                                                                            <th class="px-4 py-3">
                                                                                                Descriptive Title</th>
                                                                                            <th class="px-4 py-3">Degree
                                                                                                Yr. &amp; Sec.</th>
                                                                                            <th class="px-4 py-3">Total
                                                                                                No. of Students</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody
                                                                                        class="divide-y divide-slate-100">
                                                                                        @foreach ($shiftCourses as $course)
                                                                                            <tr>
                                                                                                <td
                                                                                                    class="px-4 py-3 font-semibold">
                                                                                                    {{ $course['course_code'] }}
                                                                                                </td>
                                                                                                <td class="px-4 py-3">
                                                                                                    {{ $course['course_name'] }}
                                                                                                </td>
                                                                                                <td class="px-4 py-3">
                                                                                                    {{ $course['degree_section'] }}
                                                                                                </td>
                                                                                                <td class="px-4 py-3">
                                                                                                    {{ $course['student_count'] }}
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                                @if ($shiftCourses->isEmpty())
                                                                                    <p
                                                                                        class="px-4 py-5 text-center text-sm text-slate-500">
                                                                                        No course assignments for this
                                                                                        shift.</p>
                                                                                @else
                                                                                    <div class="overflow-x-auto">
                                                                                        <table
                                                                                            class="min-w-[540px] w-full text-left text-sm">
                                                                                            <thead
                                                                                                class="bg-white text-[11px] uppercase tracking-wide text-slate-500">
                                                                                                <tr>
                                                                                                    <th
                                                                                                        class="px-3 py-3">
                                                                                                        Course Code</th>
                                                                                                    <th
                                                                                                        class="px-3 py-3">
                                                                                                        Descriptive
                                                                                                        Title</th>
                                                                                                    <th
                                                                                                        class="px-3 py-3">
                                                                                                        Degree Yr. &amp;
                                                                                                        Sec.</th>
                                                                                                    <th
                                                                                                        class="px-3 py-3">
                                                                                                        Total No. of
                                                                                                        Students</th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody
                                                                                                class="divide-y divide-slate-100">
                                                                                                @foreach ($shiftCourses as $course)
                                                                                                    <tr>
                                                                                                        <td
                                                                                                            class="px-3 py-3 font-semibold">
                                                                                                            {{ $course['course_code'] }}
                                                                                                        </td>
                                                                                                        <td
                                                                                                            class="px-3 py-3">
                                                                                                            {{ $course['course_name'] }}
                                                                                                        </td>
                                                                                                        <td
                                                                                                            class="px-3 py-3">
                                                                                                            {{ $course['degree_section'] }}
                                                                                                        </td>
                                                                                                        <td
                                                                                                            class="px-3 py-3">
                                                                                                            {{ $course['student_count'] }}
                                                                                                        </td>
                                                                                                    </tr>
                                                                                                @endforeach
                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                @endif
                                                                                @if ($kind === 'PBT')
                                                                                    @php $workload = $workloadByShift[$shiftKey] ?? ['preparations' => 0, 'units' => 0, 'hours' => 0]; @endphp
                                                                                    <div
                                                                                        class="border-t border-slate-200 bg-slate-50 px-4 py-3">
                                                                                        <h4
                                                                                            class="mb-2 text-xs font-semibold text-slate-700">
                                                                                            PBT Workload Summary</h4>
                                                                                        <dl class="space-y-1.5 text-sm">
                                                                                            <div
                                                                                                class="flex justify-between gap-4">
                                                                                                <dt
                                                                                                    class="text-slate-600">
                                                                                                    No. of Preparations:
                                                                                                </dt>
                                                                                                <dd
                                                                                                    class="font-medium text-slate-900">
                                                                                                    {{ $workload['preparations'] }}
                                                                                                </dd>
                                                                                            </div>
                                                                                            <div
                                                                                                class="flex justify-between gap-4">
                                                                                                <dt
                                                                                                    class="text-slate-600">
                                                                                                    No. of Units:</dt>
                                                                                                <dd
                                                                                                    class="font-medium text-slate-900">
                                                                                                    {{ $workload['units'] }}
                                                                                                </dd>
                                                                                            </div>
                                                                                            <div
                                                                                                class="flex justify-between gap-4">
                                                                                                <dt
                                                                                                    class="text-slate-600">
                                                                                                    No. of Hours/Week:
                                                                                                </dt>
                                                                                                <dd
                                                                                                    class="font-medium text-slate-900">
                                                                                                    {{ $workload['hours'] }}
                                                                                                </dd>
                                                                                            </div>
                                                                                        </dl>
                                                                                    </div>
                                                                                @endif
                                                                            </div>
                                                                        @endif

                                                                        <div
                                                                            class="min-w-0 overflow-hidden rounded-xl border border-slate-200">
                                                                            <h3
                                                                                class="border-b border-slate-200 bg-slate-50 px-4 py-3 text-sm font-semibold">
                                                                                Schedule Details</h3>
                                                                            @if ($shiftRows->isEmpty())
                                                                                <p
                                                                                    class="px-4 py-5 text-center text-sm text-slate-500">
                                                                                    No schedule entries for this shift.
                                                                                </p>
                                                                            @else
                                                                                <div class="overflow-x-auto">
                                                                                    <table
                                                                                        class="min-w-[780px] w-full text-left text-sm">
                                                                                        <thead
                                                                                            class="bg-white text-[11px] uppercase tracking-wide text-slate-500">
                                                                                            <tr>
                                                                                                <th class="px-3 py-3">
                                                                                                    Section</th>
                                                                                                <th class="px-3 py-3">
                                                                                                    Faculty</th>
                                                                                                <th class="px-3 py-3">
                                                                                                    Course</th>
                                                                                                <th class="px-3 py-3">
                                                                                                    Descriptive Title
                                                                                                </th>
                                                                                                <th class="px-3 py-3">
                                                                                                    Day &amp; Time</th>
                                                                                                <th class="px-3 py-3">
                                                                                                    Room</th>
                                                                                            </tr>
                                                                                        </thead>
                                                                                        <tbody
                                                                                            class="divide-y divide-slate-100">
                                                                                            @foreach ($shiftRows as $row)
                                                                                                @php
                                                                                                    try {
                                                                                                        $timeText =
                                                                                                            \Illuminate\Support\Carbon::parse(
                                                                                                                $row[
                                                                                                                    'start_time'
                                                                                                                ],
                                                                                                            )->format(
                                                                                                                'g:i A',
                                                                                                            ) .
                                                                                                            ' – ' .
                                                                                                            \Illuminate\Support\Carbon::parse(
                                                                                                                $row[
                                                                                                                    'end_time'
                                                                                                                ],
                                                                                                            )->format(
                                                                                                                'g:i A',
                                                                                                            );
                                                                                                    } catch (\Throwable $exception) {
                                                                                                        $timeText =
                                                                                                            ($row[
                                                                                                                'start_time'
                                                                                                            ] ??
                                                                                                                '—') .
                                                                                                            ' – ' .
                                                                                                            ($row[
                                                                                                                'end_time'
                                                                                                            ] ??
                                                                                                                '—');
                                                                                                    }
                                                                                                @endphp
                                                                                                <tr class="align-top">
                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3">
                                                                                                        {{ $row['section'] ?? '—' }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="px-3 py-3">
                                                                                                        {{ $row['faculty'] ?? '—' }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3 font-semibold">
                                                                                                        {{ $row['course_code'] ?? '—' }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="min-w-[180px] px-3 py-3">
                                                                                                        {{ $row['course_name'] ?? '—' }}
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3">
                                                                                                        {{ $row['day'] ?? '—' }}
                                                                                                        <div
                                                                                                            class="mt-1 text-xs text-slate-500">
                                                                                                            {{ $timeText }}
                                                                                                        </div>
                                                                                                    </td>
                                                                                                    <td
                                                                                                        class="whitespace-nowrap px-3 py-3">
                                                                                                        {{ $row['room'] ?? '—' }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        </tbody>
                                                                                    </table>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </section>
                                                    @endif
                        </section>
                    @endforeach
                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div
                            class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                            <div>
                                <h2 class="font-semibold">Schedule details</h2>
                                <p class="mt-1 text-xs text-slate-500">{{ $rows->count() }} plotted class entries ·
                                    {{ $semesterLabel }}</p>
                            </div>
                            <span
                                class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $kind }}</span>
                        </div>
                        @if ($rows->isEmpty())
                            <div class="p-8 text-center text-sm text-slate-500">No schedule entries match these export
                                filters.</div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-[950px] w-full text-left text-sm">
                                    <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                                        <tr>
                                            <th class="px-5 py-3">Section</th>
                                            <th class="px-5 py-3">Faculty</th>
                                            <th class="px-5 py-3">Course</th>
                                            <th class="px-5 py-3">Descriptive title</th>
                                            <th class="px-5 py-3">MIS code</th>
                                            <th class="px-5 py-3">Day &amp; time</th>
                                            <th class="px-5 py-3">Room</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach ($rows as $row)
                                            @php
                                                try {
                                                    $timeText =
                                                        \Illuminate\Support\Carbon::parse($row['start_time'])->format(
                                                            'g:i A',
                                                        ) .
                                                        ' – ' .
                                                        \Illuminate\Support\Carbon::parse($row['end_time'])->format(
                                                            'g:i A',
                                                        );
                                                } catch (\Throwable $exception) {
                                                    $timeText =
                                                        ($row['start_time'] ?? '—') . ' – ' . ($row['end_time'] ?? '—');
                                                }
                                            @endphp
                                            <tr class="align-top">
                                                <td class="whitespace-nowrap px-5 py-3">{{ $row['section'] ?? '—' }}
                                                </td>
                                                <td class="px-5 py-3">{{ $row['faculty'] ?? '—' }}</td>
                                                <td class="whitespace-nowrap px-5 py-3 font-semibold">
                                                    {{ $row['course_code'] ?? '—' }}</td>
                                                <td class="min-w-[220px] px-5 py-3">{{ $row['course_name'] ?? '—' }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-3">{{ $row['mis_code'] ?: '—' }}
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-3">{{ $row['day'] ?? '—' }}<div
                                                        class="mt-1 text-xs text-slate-500">{{ $timeText }}</div>
                                                </td>
                                                <td class="whitespace-nowrap px-5 py-3">{{ $row['room'] ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @endif
                    </section>
                </div>
            </div>
        </main>
    </div>
    @if ($kind === 'PBS')
        <script>
            async function confirmPbsDownload() {
                const countInput = document.getElementById('preview-student-count');
                const count = Number(String(countInput?.value ?? '').trim());
                if (!Number.isInteger(count) || count < 0 || count > 500) {
                    alert('Enter a whole student count from 0 to 500.');
                    countInput?.focus();
                    return;
                }

                const button = document.querySelector('button[onclick="confirmPbsDownload()"]');
                if (button) {
                    button.disabled = true;
                    button.textContent = 'Preparing download…';
                }

                try {
                    const response = await fetch(@json(route('chair.pbs.section.students')), {
                        method: 'PUT',
                        credentials: 'same-origin',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            sec_id: @json(request()->query('section')),
                            sec_no_of_student: count,
                        }),
                    });
                    const result = await response.json().catch(() => ({}));
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Could not save the section student count.');
                    }
                    window.location.assign(@json($downloadUrl));
                } catch (error) {
                    alert(error instanceof Error ? error.message : 'Could not prepare the PBS export.');
                    if (button) {
                        button.disabled = false;
                        button.textContent = 'Confirm and Download Excel';
                    }
                }
            }
        </script>
    @endif
</body>

</html>
