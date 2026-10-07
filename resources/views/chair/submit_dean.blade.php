<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — PBT Submission</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'PBT Submission',
                'badgeText' => 'Department Chair',
            ])

            <div class="flex-1 overflow-y-auto p-7">
                <div class="mb-5">
                    <h1 class="text-xl font-extrabold">Submit Faculty PBT to Dean</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Choose a faculty member to review and submit their Program by Teacher schedule.
                    </p>
                </div>

                @if (session('success'))
                    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                @if (!$semester)
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                        No active semester is set. Ask an administrator to set the active semester before submitting.
                    </div>
                @else
                    @php
                        $academicYearLabel =
                            $semester->academicYear->ay_year_label ??
                            ($semester->academicYear->ay_academic_year ?? 'Active academic year');
                    @endphp

                    <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-4">
                            <h2 class="font-bold">Select a Faculty PBT</h2>
                            <p class="mt-1 text-xs text-slate-500">
                                {{ $academicYearLabel }} · {{ $semester->sem_name }}
                            </p>
                        </div>

                        @if ($facultyOptions->isEmpty())
                            <div class="rounded-xl bg-slate-50 p-5 text-sm text-slate-500">
                                No faculty members with active scheduled classes were found in your department.
                            </div>
                        @else
                            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-3">
                                @foreach ($facultyOptions as $option)
                                    @php
                                        $isSelected = $selectedFaculty?->fac_id === $option['id'];
                                        $statusClass = match (strtolower($option['status'])) {
                                            'approved' => 'bg-emerald-100 text-emerald-700',
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'returned' => 'bg-red-100 text-red-700',
                                            default => 'bg-slate-100 text-slate-600',
                                        };
                                    @endphp

                                    <a href="{{ route('chair.submit_dean', ['faculty' => $option['id']]) }}"
                                        class="rounded-xl border p-4 transition hover:border-blue-400 hover:bg-blue-50
                                            {{ $isSelected ? 'border-blue-500 bg-blue-50 ring-2 ring-blue-100' : 'border-slate-200 bg-white' }}">
                                        <div class="flex items-start justify-between gap-3">
                                            <div>
                                                <div class="font-semibold">{{ $option['name'] }}</div>
                                                <div class="mt-1 text-sm text-slate-500">
                                                    {{ $option['schedule_count'] }}
                                                    {{ $option['schedule_count'] === 1 ? 'class' : 'classes' }}
                                                </div>
                                            </div>
                                            <span
                                                class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                                {{ $option['status'] }}
                                            </span>
                                        </div>

                                        @if ($option['submitted_at'])
                                            <div class="mt-3 text-xs text-slate-400">
                                                Submitted
                                                {{ \Carbon\Carbon::parse($option['submitted_at'])->format('M d, Y') }}
                                            </div>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </section>

                    @if ($selectedFaculty)
                        @php
                            $canSubmit =
                                $scheduleDetailsComplete &&
                                $conflicts === 0 &&
                                (!$submission || $submission->schsub_status === 'returned');
                        @endphp

                        <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4 flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <h2 class="font-bold">
                                        {{ $selectedFaculty->user->usr_name ?? 'Selected Faculty' }}’s PBT
                                    </h2>
                                    <p class="mt-1 text-xs text-slate-500">
                                        Review this faculty member’s schedule before submitting it to the Dean.
                                    </p>
                                </div>

                                <span
                                    class="rounded-full px-3 py-1 text-xs font-bold {{ $canSubmit ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $canSubmit ? 'Ready' : 'Not Ready' }}
                                </span>
                            </div>

                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between border-b border-slate-100 pb-3">
                                    <span>Scheduled classes included</span>
                                    <span class="font-semibold">{{ $scheduleCount }}</span>
                                </div>

                                <div class="flex justify-between border-b border-slate-100 pb-3">
                                    <span>Scheduling conflicts</span>
                                    <span
                                        class="font-semibold {{ $conflicts > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                        {{ $conflicts > 0 ? $conflicts . ' found' : 'None found' }}
                                    </span>
                                </div>

                                <div class="flex justify-between">
                                    <span>Faculty, course, section, and room assigned for every class</span>
                                    <span
                                        class="font-semibold {{ $scheduleDetailsComplete ? 'text-emerald-600' : 'text-amber-600' }}">
                                        {{ $scheduleDetailsComplete ? 'Complete' : 'Incomplete' }}
                                    </span>
                                </div>
                            </div>

                            <form method="POST" action="{{ route('chair.submit_dean.store') }}"
                                class="mt-5 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-4">
                                @csrf
                                <input type="hidden" name="faculty_id" value="{{ $selectedFaculty->fac_id }}">

                                @if ($submission && $submission->schsub_status !== 'returned')
                                    <span class="mr-auto text-sm text-slate-600">
                                        Submission status:
                                        <strong>{{ ucfirst($submission->schsub_status) }}</strong>
                                    </span>
                                @endif

                                <button type="submit" @disabled(!$canSubmit)
                                    class="rounded-xl px-4 py-2 text-sm font-semibold text-white {{ $canSubmit ? 'bg-red-600 hover:bg-red-700' : 'cursor-not-allowed bg-slate-400' }}">
                                    {{ $submission?->schsub_status === 'returned' ? 'Resubmit PBT to Dean' : 'Submit PBT to Dean' }}
                                </button>
                            </form>
                        </section>

                        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                            <div class="mb-4">
                                <h2 class="font-bold">PBT Schedule</h2>
                                <p class="mt-1 text-xs text-slate-500">
                                    These are the active classes included in this faculty member’s PBT.
                                </p>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200 text-xs uppercase text-slate-500">
                                            <th class="p-3">Faculty</th>
                                            <th class="p-3">Course</th>
                                            <th class="p-3">Section</th>
                                            <th class="p-3">Day &amp; Time</th>
                                            <th class="p-3">Room</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($schedules as $schedule)
                                            <tr class="border-b border-slate-100">
                                                <td class="p-3">
                                                    {{ optional($schedule->faculty?->user)->usr_name ?? 'Unknown' }}
                                                </td>
                                                <td class="p-3">
                                                    {{ $schedule->subject?->course_code ?? '—' }}
                                                    @if ($schedule->subject?->course_name)
                                                        — {{ $schedule->subject->course_name }}
                                                    @endif
                                                </td>
                                                <td class="p-3">
                                                    {{ $schedule->section?->sec_name ?? '—' }}
                                                </td>
                                                <td class="p-3">
                                                    {{ $schedule->sch_day }},
                                                    {{ \Carbon\Carbon::parse($schedule->sch_start_time)->format('g:i A') }}
                                                    –
                                                    {{ \Carbon\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}
                                                </td>
                                                <td class="p-3">
                                                    {{ $schedule->room?->room_name ?? '—' }}
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="p-8 text-center text-slate-500">
                                                    No active scheduled classes for this faculty member.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            @if ($schedules->total() > 0)
                                <div
                                    class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-4">
                                    <p class="text-sm text-slate-500">
                                        Showing {{ $schedules->firstItem() }}–{{ $schedules->lastItem() }}
                                        of {{ $schedules->total() }} classes
                                    </p>

                                    <div class="flex items-center gap-2">
                                        @if ($schedules->onFirstPage())
                                            <span
                                                class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400">
                                                Previous
                                            </span>
                                        @else
                                            <a href="{{ $schedules->previousPageUrl() }}"
                                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                                Previous
                                            </a>
                                        @endif

                                        @if ($schedules->hasMorePages())
                                            <a href="{{ $schedules->nextPageUrl() }}"
                                                class="rounded-lg border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                                Next
                                            </a>
                                        @else
                                            <span
                                                class="cursor-not-allowed rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-400">
                                                Next
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </section>
                    @endif
                @endif
            </div>
        </main>
    </div>
</body>

</html>
