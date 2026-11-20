<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unassigned Faculty — SKEDYUL</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', ['title' => 'Unassigned Faculty', 'badgeText' => 'Department Chair'])
            <div class="flex-1 overflow-y-auto p-5 md:p-7">
                <div class="mx-auto max-w-6xl space-y-5">
                    <header>
                        <h1 class="text-2xl font-bold text-slate-900">Unassigned Faculty</h1>
                        <p class="mt-1 text-sm text-slate-500">
                            Classes using Professor X are waiting for your department to choose a real teacher.
                            Their workload starts after you assign them.
                        </p>
                    </header>

                    @if (session('success'))
                        <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('success') }}</div>
                    @endif
                    @if (session('error'))
                        <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
                    @endif

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
                            <div>
                                <h2 class="font-semibold">Classes to assign</h2>
                                <p class="mt-1 text-xs text-slate-500">
                                    {{ $chair->program?->dept_name ?? 'Your department' }}
                                    @if ($semester)
                                        · {{ $semester->sem_name }}
                                        @if ($semester->academicYear)
                                            · AY {{ $semester->academicYear->ay_year_label ?? $semester->academicYear->ay_academic_year }}
                                        @endif
                                    @endif
                                </p>
                            </div>
                            <span class="rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">{{ $schedules->count() }} waiting</span>
                        </div>

                        @if (!$semester)
                            <div class="p-8 text-center text-sm text-slate-500">There is no current semester.</div>
                        @elseif ($schedules->isEmpty())
                            <div class="p-10 text-center">
                                <div class="font-semibold text-slate-700">Nothing to assign</div>
                                <p class="mt-1 text-sm text-slate-500">No unassigned classes for your department in the current semester.</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-100">
                                @foreach ($schedules as $schedule)
                                    <article class="grid gap-4 px-5 py-4 lg:grid-cols-[minmax(0,1fr)_minmax(280px,360px)] lg:items-center">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-semibold text-slate-900">{{ $schedule->subject?->course_code ?? 'Course' }} — {{ $schedule->subject?->course_name ?? '' }}</h3>
                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[11px] font-semibold text-amber-800">Unassigned</span>
                                            </div>
                                            <p class="mt-1 text-sm text-slate-600">
                                                {{ $schedule->section?->sec_name ?? 'No section' }}
                                                · {{ $schedule->sch_day }}
                                                · {{ \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') }}–{{ \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">Room: {{ $schedule->room?->room_name ?? 'Not set' }}</p>
                                        </div>
                                        @if ($faculty->isEmpty())
                                            <p class="rounded-lg bg-slate-50 px-3 py-2 text-sm text-slate-500">No active faculty accounts are available in your department.</p>
                                        @else
                                            <form method="POST" action="{{ route('chair.unassigned_faculty.assign', $schedule->sch_id) }}" class="flex flex-col gap-2 sm:flex-row">
                                                @csrf
                                                @method('PUT')
                                                <label class="sr-only" for="faculty-{{ $schedule->sch_id }}">Choose faculty</label>
                                                <select id="faculty-{{ $schedule->sch_id }}" name="fac_id" required class="min-w-0 flex-1 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-sm focus:border-blue-400 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                                    <option value="">Choose a faculty member</option>
                                                    @foreach ($faculty as $member)
                                                        <option value="{{ $member->fac_id }}">{{ \App\Services\FacultyNameFormatter::format($member) }}</option>
                                                    @endforeach
                                                </select>
                                                <button type="submit" class="shrink-0 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">Assign</button>
                                            </form>
                                        @endif
                                    </article>
                                @endforeach
                            </div>
                        @endif
                    </section>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
