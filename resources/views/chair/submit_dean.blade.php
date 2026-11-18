<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — PBS / PBT Submissions</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => '',
                'badgeText' => 'Department Chair',
            ])

            <div class="flex-1 overflow-y-auto p-5 md:p-7">
                <div class="mb-5">
                    <h1 class="text-xl font-bold">Schedule Submissions</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Track Program by Section and Program by Teacher submissions sent to the Dean.
                    </p>
                </div>

                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        {{ session('error') }}
                    </div>
                @endif

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-5 py-4">
                        <div>
                            <h2 class="font-semibold">PBS and PBT submissions</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                @if ($semester)
                                    {{ $semester->sem_name }}
                                    @if ($semester->academicYear)
                                        · AY
                                        {{ $semester->academicYear->ay_year_label ?? $semester->academicYear->ay_academic_year }}
                                    @endif
                                @else
                                    No active semester
                                @endif
                            </p>
                        </div>
                        <span class="text-sm text-slate-500">{{ $submissions->total() }} total</span>
                    </div>

                    @if (!$semester)
                        <div class="p-8 text-center text-sm text-slate-500">
                            There is no active semester. Set the active semester before reviewing submissions.
                        </div>
                    @elseif ($submissions->isEmpty())
                        <div class="p-10 text-center">
                            <div class="font-semibold text-slate-700">No submissions yet</div>
                            <p class="mt-1 text-sm text-slate-500">Submitted PBS and PBT schedules will appear here.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-[1050px] w-full text-left text-sm">
                                <thead class="bg-slate-50 text-[11px] uppercase tracking-wide text-slate-500">
                                    <tr>
                                        <th class="px-5 py-3 font-semibold">Type</th>
                                        <th class="px-5 py-3 font-semibold">Faculty / Section</th>
                                        <th class="px-5 py-3 font-semibold">Classes</th>
                                        <th class="px-5 py-3 font-semibold">Submitted</th>
                                        <th class="px-5 py-3 font-semibold">Status</th>
                                        <th class="min-w-[230px] px-5 py-3 font-semibold">Dean’s remarks</th>
                                        <th class="px-5 py-3 text-right font-semibold">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach ($submissions as $submission)
                                        @php
                                            $isPbs = $submission->schsub_type === 'pbs';
                                            $label = $isPbs ? 'PBS' : 'PBT';
                                            $subjectOfSubmission = $isPbs
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
                                                default => [
                                                    ucfirst($submission->schsub_status ?? 'Unknown'),
                                                    'bg-slate-100 text-slate-600',
                                                ],
                                            };
                                        @endphp
                                        <tr class="align-top hover:bg-slate-50/70">
                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span
                                                    class="rounded-md bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">{{ $label }}</span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="font-semibold text-slate-800">{{ $subjectOfSubmission }}
                                                </div>
                                                <div class="mt-1 text-xs text-slate-500">
                                                    {{ $submission->semester?->sem_name }}
                                                    @if ($submission->semester?->academicYear)
                                                        · AY
                                                        {{ $submission->semester->academicYear->ay_year_label ?? $submission->semester->academicYear->ay_academic_year }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                                {{ $submission->class_count }}
                                                {{ $submission->class_count === 1 ? 'class' : 'classes' }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-slate-600">
                                                {{ $submission->schsub_submitted_at?->format('M d, Y g:i A') ?? '—' }}
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4">
                                                <span
                                                    class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">{{ $statusLabel }}</span>
                                            </td>
                                            <td class="max-w-sm px-5 py-4 text-xs leading-5 text-slate-600">
                                                @if ($submission->schsub_remarks)
                                                    <span class="block">{{ $submission->schsub_remarks }}</span>
                                                    @if ($submission->reviewedBy?->usr_name)
                                                        <span class="mt-1 block text-[11px] text-slate-400">Dean:
                                                            {{ $submission->reviewedBy->usr_name }}</span>
                                                    @endif
                                                @else
                                                    <span class="text-slate-400">No remarks</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-right">
                                                <div class="inline-flex items-center gap-2">
                                                    <a href="{{ route('chair.submit_dean.show', $submission->schsub_id) }}"
                                                        class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                                        View
                                                    </a>
                                                    @if ($submission->schsub_status === 'returned')
                                                        <a href="{{ route('chair.submit_dean.edit', $submission->schsub_id) }}"
                                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs font-semibold text-white hover:bg-blue-700">
                                                            Edit
                                                        </a>
                                                    @else
                                                        <button type="button" disabled
                                                            title="Editing is available after the Dean returns this submission"
                                                            class="cursor-not-allowed rounded-lg bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-400">
                                                            Edit
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 px-5 py-4">
                            <p class="text-sm text-slate-500">
                                Showing {{ $submissions->firstItem() }}–{{ $submissions->lastItem() }} of
                                {{ $submissions->total() }} submissions
                            </p>
                            @if ($submissions->hasPages())
                                <div class="flex items-center gap-2">
                                    @if ($submissions->onFirstPage())
                                        <span
                                            class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400">Previous</span>
                                    @else
                                        <a href="{{ $submissions->previousPageUrl() }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Previous</a>
                                    @endif
                                    <span class="px-2 text-sm text-slate-500">Page {{ $submissions->currentPage() }} of
                                        {{ $submissions->lastPage() }}</span>
                                    @if ($submissions->hasMorePages())
                                        <a href="{{ $submissions->nextPageUrl() }}"
                                            class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Next</a>
                                    @else
                                        <span
                                            class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400">Next</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endif
                </section>
            </div>
        </main>
    </div>
</body>

</html>
