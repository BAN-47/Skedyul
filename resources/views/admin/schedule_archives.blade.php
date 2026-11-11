<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Schedule Archives</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
    <div class="app-shell">
        @include('partials.admin_sidebar')

        <div class="app-main">
            @include('partials.admin_header', ['title' => 'Schedule Archives'])

            <main class="page-content">
                <div class="mb-4">
                    <h1 class="text-xl font-extrabold text-slate-900">Schedule Archives</h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Read-only schedules from previous academic years. Viewing archives never changes schedules.
                    </p>
                </div>

                @if (!$archiveTableAvailable)
                    <div class="card border border-amber-200 bg-amber-50 text-sm text-amber-800">
                        Schedule Archives are not available yet. The required database migration has not been applied.
                    </div>
                @else
                    <form method="GET" action="{{ route('admin.schedule-archives') }}"
                        class="card mb-4 grid grid-cols-1 gap-3 p-4 sm:grid-cols-3">
                        <div>
                            <label for="archive-year" class="field-label">Academic year</label>
                            <select class="field-input" name="year" id="archive-year">
                                <option value="">All past years</option>
                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->ay_id }}" @selected($selectedYear === $academicYear->ay_id)>
                                        {{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="archive-semester" class="field-label">Semester</label>
                            <select class="field-input" name="semester" id="archive-semester">
                                <option value="">All semesters</option>
                                @foreach ($semesters as $semester)
                                    <option value="{{ $semester->sem_id }}" @selected($selectedSemester === $semester->sem_id)>
                                        {{ $semester->sem_name }} — {{ $semester->academicYear?->ay_academic_year ?? $semester->academicYear?->ay_year_label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="btn btn-primary">Filter</button>
                            <a href="{{ route('admin.schedule-archives') }}" class="btn btn-secondary">Clear</a>
                        </div>
                    </form>

                    <section class="card overflow-hidden">
                        @if ($archives->isEmpty())
                            <div class="px-4 py-12 text-center">
                                <div class="text-base font-bold text-slate-700">No past schedules found</div>
                                <p class="mt-1 text-sm text-slate-500">
                                    Past-year schedules will appear here. Current-year schedules are not changed by this page.
                                </p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="data-table min-w-[1100px]">
                                    <thead>
                                        <tr>
                                            <th>Academic Year / Semester</th>
                                            <th>Course</th>
                                            <th>Section</th>
                                            <th>Faculty</th>
                                            <th>Day &amp; Time</th>
                                            <th>Room</th>
                                            <th>Shift</th>
                                            <th>Record</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($archives as $archive)
                                            <tr>
                                                <td>
                                                    <div class="font-semibold">{{ $archive->year_label }}</div>
                                                    <div class="text-xs text-slate-500">{{ $archive->semester_name }}</div>
                                                </td>
                                                <td>
                                                    <div class="font-semibold">{{ $archive->course_code ?? '—' }}</div>
                                                    <div class="text-xs text-slate-500">{{ $archive->course_name ?? 'Course unavailable' }}</div>
                                                </td>
                                                <td>{{ $archive->section_name ?? '—' }}</td>
                                                <td>{{ trim($archive->faculty_name) !== '' ? $archive->faculty_name : '—' }}</td>
                                                <td>
                                                    <div>{{ $archive->day ?? '—' }}</div>
                                                    <div class="text-xs text-slate-500">
                                                        {{ $archive->start_time ?? '—' }}–{{ $archive->end_time ?? '—' }}
                                                    </div>
                                                </td>
                                                <td>{{ $archive->room_name ?? '—' }}</td>
                                                <td>{{ ucfirst($archive->shift ?? '—') }}</td>
                                                <td>
                                                    <span class="badge {{ $archive->source === 'Archived snapshot' ? 'badge-blue' : 'badge-amber' }}">
                                                        {{ $archive->source }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
                                <p class="text-sm text-slate-500">
                                    Showing {{ $archives->firstItem() ?? 0 }}–{{ $archives->lastItem() ?? 0 }}
                                    of {{ $archives->total() }} schedules
                                </p>
                                {{ $archives->links() }}
                            </div>
                        @endif
                    </section>
                @endif
            </main>
        </div>
    </div>
</body>

</html>
