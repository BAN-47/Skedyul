<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Dean Courses</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.dean_sidebar')
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            <header class="topbar">
                <div class="topbar-title">Course Offerings</div>
                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">Dean · View only</span>
            </header>

            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-5">
                        <p class="mb-1 text-sm font-semibold uppercase tracking-wide text-blue-600">Academic catalog</p>
                        <h1 class="text-2xl font-extrabold tracking-tight">Courses</h1>
                        <p class="mt-1 text-sm text-slate-500">View course offerings for your college. This page is read only.</p>
                    </div>

                    @if (!$collegeId)
                        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                            No college is assigned to your dean account. Ask the system administrator to review your account assignment.
                        </div>
                    @else
                        <form method="GET" action="{{ route('dean.courses') }}"
                            class="mb-5 grid grid-cols-1 gap-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm md:grid-cols-3">
                            <label class="block">
                                <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Program</span>
                                <select name="program_id" onchange="this.form.submit()"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select program</option>
                                    @foreach ($programs as $program)
                                        <option value="{{ $program->dept_id }}" @selected($selectedProgramId === (string) $program->dept_id)>
                                            {{ $program->dept_name }}{{ $program->dept_code ? ' (' . $program->dept_code . ')' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Year level</span>
                                <select name="year_level" onchange="this.form.submit()"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select year</option>
                                    @foreach ($yearLevels as $value => $label)
                                        <option value="{{ $value }}" @selected($selectedYearLevel === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="block">
                                <span class="mb-1.5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Semester</span>
                                <select name="semester" onchange="this.form.submit()"
                                    class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                    <option value="">Select semester</option>
                                    @foreach ($semesters as $value => $label)
                                        <option value="{{ $value }}" @selected($selectedSemester === (string) $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </form>

                        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                            @if (!$selectedProgramId || !$selectedYearLevel || !$selectedSemester)
                                <div class="p-10 text-center text-sm text-slate-500">Select a program, year level, and semester to view course offerings.</div>
                            @elseif ($courses->isEmpty())
                                <div class="p-10 text-center text-sm text-slate-500">No active courses match the selected program and term.</div>
                            @else
                                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 px-5 py-4">
                                    <div>
                                        <h2 class="font-semibold text-slate-900">{{ $programs->firstWhere('dept_id', $selectedProgramId)?->dept_name }}</h2>
                                        <p class="mt-0.5 text-sm text-slate-500">{{ $yearLevels[(int) $selectedYearLevel] }} · {{ $semesters[(int) $selectedSemester] }}</p>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">{{ $courses->count() }} {{ \Illuminate\Support\Str::plural('course', $courses->count()) }}</span>
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                                        <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                            <tr>
                                                <th class="px-5 py-3">Course code</th>
                                                <th class="px-5 py-3">Descriptive title</th>
                                                <th class="px-4 py-3 text-center">Units</th>
                                                <th class="px-4 py-3 text-center">Lecture units</th>
                                                <th class="px-4 py-3 text-center">Lab units</th>
                                                <th class="px-4 py-3 text-center">Total (lec + lab)</th>
                                                <th class="px-5 py-3">Program</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 text-slate-700">
                                            @foreach ($courses as $course)
                                                @php
                                                    // Lab teaching hours are stored at 3 per lab unit; show the entered units here.
                                                    $lectureUnits = (float) ($course->course_lecture_hours ?? 0);
                                                    $labUnits = (float) ($course->course_lab_hours ?? 0) / 3;
                                                    $totalUnits = $lectureUnits + $labUnits;
                                                @endphp
                                                <tr class="hover:bg-slate-50/70">
                                                    <td class="whitespace-nowrap px-5 py-3 font-semibold text-slate-900">{{ $course->course_code }}</td>
                                                    <td class="min-w-[16rem] px-5 py-3">{{ $course->course_name }}</td>
                                                    <td class="px-4 py-3 text-center">{{ $course->course_units ?? '—' }}</td>
                                                    <td class="px-4 py-3 text-center">{{ $lectureUnits }}</td>
                                                    <td class="px-4 py-3 text-center">{{ $labUnits }}</td>
                                                    <td class="px-4 py-3 text-center font-medium">{{ $totalUnits }}</td>
                                                    <td class="whitespace-nowrap px-5 py-3">{{ $course->program?->dept_name ?? '—' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </section>
                    @endif
                </div>
            </div>
        </main>
    </div>
</body>
</html>

