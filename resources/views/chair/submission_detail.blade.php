<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Submission Details</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'Submission Details',
                'badgeText' => 'Department Chair',
            ])
            <div class="flex-1 overflow-y-auto p-5 md:p-7">
                @php
                    $isPbs = $submission->schsub_type === 'pbs';
                    $kind = $isPbs ? 'Program by Section (PBS)' : 'Program by Teacher (PBT)';
                    $target = $isPbs
                        ? $submission->section?->sec_name ?? 'Section unavailable'
                        : ($submission->faculty?->user?->usr_name ??
                        trim(
                            implode(
                                ' ',
                                array_filter([
                                    $submission->faculty?->fac_first_name,
                                    $submission->faculty?->fac_middle_name,
                                    $submission->faculty?->fac_last_name,
                                    $submission->faculty?->fac_suffix,
                                ]),
                            ),
                        ) ?:
                        'Faculty unavailable');
                    [$statusLabel, $statusClass] = match ($submission->schsub_status) {
                        'approved' => ['Approved', 'bg-emerald-100 text-emerald-700'],
                        'pending' => ['Pending review', 'bg-amber-100 text-amber-700'],
                        'returned' => ['Returned for revision', 'bg-rose-100 text-rose-700'],
                        default => [ucfirst($submission->schsub_status ?? 'Unknown'), 'bg-slate-100 text-slate-600'],
                    };
                @endphp

                <div class="mb-5 flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <a href="{{ route('chair.submit_dean') }}"
                            class="text-sm font-semibold text-blue-600 hover:text-blue-700">← All submissions</a>
                        <h1 class="mt-3 text-xl font-bold">{{ $kind }}</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ $target }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span
                            class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                        @if ($submission->schsub_status === 'returned')
                            <a href="{{ route('chair.submit_dean.edit', $submission->schsub_id) }}"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Edit
                                and resubmit</a>
                        @endif
                    </div>
                </div>

                <section
                    class="mb-5 grid grid-cols-1 gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:grid-cols-2 xl:grid-cols-4">
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Department</div>
                        <div class="mt-1 font-semibold">
                            {{ $submission->department?->dept_code ?? ($submission->department?->dept_name ?? '—') }}
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Semester</div>
                        <div class="mt-1 font-semibold">{{ $submission->semester?->sem_name ?? '—' }} @if ($submission->semester?->academicYear)
                                · AY
                                {{ $submission->semester->academicYear->ay_year_label ?? $submission->semester->academicYear->ay_academic_year }}
                            @endif
                        </div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Submitted</div>
                        <div class="mt-1 font-semibold">
                            {{ $submission->schsub_submitted_at?->format('M d, Y g:i A') ?? '—' }}</div>
                    </div>
                    <div>
                        <div class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Classes in
                            submission</div>
                        <div class="mt-1 font-semibold">{{ $rows->count() }}</div>
                    </div>
                </section>

                @if ($submission->schsub_remarks)
                    <section
                        class="mb-5 rounded-2xl border {{ $submission->schsub_status === 'returned' ? 'border-rose-200 bg-rose-50' : 'border-slate-200 bg-white' }} p-5">
                        <div
                            class="text-sm font-bold {{ $submission->schsub_status === 'returned' ? 'text-rose-800' : 'text-slate-800' }}">
                            Dean’s remarks
                            @if ($submission->reviewedBy?->usr_name)
                                <span class="font-normal text-slate-500">·
                                    {{ $submission->reviewedBy->usr_name }}</span>
                            @endif
                        </div>
                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700">
                            {{ $submission->schsub_remarks }}</p>
                    </section>
                @elseif ($submission->schsub_status === 'returned')
                    <section class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-5 text-sm text-rose-800">
                        The Dean returned this submission, but no reason was recorded.
                    </section>
                @endif

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <h2 class="font-semibold">Submitted schedule</h2>
                        <p class="mt-1 text-xs text-slate-500">This is the schedule snapshot that was sent to the Dean.
                        </p>
                    </div>
                    @if ($rows->isEmpty())
                        <div class="p-8 text-center text-sm text-slate-500">No schedule rows were saved with this
                            submission.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[850px] w-full text-left text-sm">
                                <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">Day &amp; time</th>
                                        <th class="px-5 py-3 font-semibold">Course</th>
                                        <th class="px-5 py-3 font-semibold">Descriptive title</th>
                                        <th class="px-5 py-3 font-semibold">Faculty</th>
                                        <th class="px-5 py-3 font-semibold">Section</th>
                                        <th class="px-5 py-3 font-semibold">Room</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($rows as $row)
                                        @php
                                            $startTime = $row['start_time'] ?? null;
                                            $endTime = $row['end_time'] ?? null;
                                            try {
                                                $timeLabel =
                                                    ($startTime
                                                        ? \Illuminate\Support\Carbon::parse($startTime)->format('g:i A')
                                                        : '—') .
                                                    ' – ' .
                                                    ($endTime
                                                        ? \Illuminate\Support\Carbon::parse($endTime)->format('g:i A')
                                                        : '—');
                                            } catch (\Throwable $exception) {
                                                $timeLabel = ($startTime ?? '—') . ' – ' . ($endTime ?? '—');
                                            }
                                        @endphp
                                        <tr class="align-top">
                                            <td class="whitespace-nowrap px-5 py-4 font-medium">
                                                {{ $row['day'] ?? '—' }}<div class="mt-1 text-xs text-slate-500">
                                                    {{ $timeLabel }}</div>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 font-semibold">
                                                {{ $row['subject_code'] ?? '—' }}</td>
                                            <td class="min-w-[200px] px-5 py-4">{{ $row['subject_name'] ?? '—' }}</td>
                                            <td class="px-5 py-4">{{ $row['faculty'] ?? '—' }}</td>
                                            <td class="whitespace-nowrap px-5 py-4">{{ $row['section'] ?? '—' }}</td>
                                            <td class="whitespace-nowrap px-5 py-4">{{ $row['room'] ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
</body>

</html>
