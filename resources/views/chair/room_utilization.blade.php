<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Room Utilization</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
<div class="app-shell">
    @include('partials.chair_sidebar')
    <div class="app-main">
        @include('partials.chair_header', ['title' => 'Room Utilization', 'badgeText' => $selectedSemester ? (($selectedYear?->ay_academic_year ?? $selectedYear?->ay_year_label ?? 'Academic year').' · '.$selectedSemester->sem_name) : 'No semester selected'])

        <main class="page-content space-y-4">
            <section class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Program by Room</div>
                        <div class="card-sub">View the room’s weekly timetable from the same saved schedules used by PBS and PBT.</div>
                    </div>
                </div>

                <form method="GET" action="{{ route('chair.room_utilization') }}" class="grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2 xl:grid-cols-4">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Academic year
                        <select name="academic_year" class="field-input mt-1" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->ay_id }}" @selected((string) $selectedYearId === (string) $year->ay_id)>{{ $year->ay_academic_year ?? $year->ay_year_label }}{{ $year->ay_is_active ? ' · Current' : '' }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Semester
                        <select name="semester" class="field-input mt-1" onchange="this.form.submit()">
                            @forelse($semesters as $semester)
                                <option value="{{ $semester->sem_id }}" @selected((string) $selectedSemester?->sem_id === (string) $semester->sem_id)>{{ $semester->sem_name }}{{ $semester->sem_is_active ? ' · Current' : '' }}</option>
                            @empty
                                <option value="">No semester in this year</option>
                            @endforelse
                        </select>
                    </label>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Floor
                        <select name="floor" class="field-input mt-1" onchange="this.form.submit()">
                            <option value="all" @selected($floorFilter === 'all')>All floors</option>
                            @foreach(range(1, 10) as $floor)
                                <option value="{{ $floor }}" @selected($floorFilter === (string) $floor)>{{ $floor }}{{ match ($floor % 10) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' } }} floor</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">Room
                        <select name="room_id" class="field-input mt-1" onchange="this.form.submit()">
                            @forelse($visibleRooms as $room)
                                <option value="{{ $room->room_id }}" @selected((string) $selectedRoom?->room_id === (string) $room->room_id)>{{ $room->room_name }} · {{ $room->room_building }}{{ $room->utilization_floor ? ' · Floor '.$room->utilization_floor : '' }}</option>
                            @empty
                                <option value="">No rooms on this floor</option>
                            @endforelse
                        </select>
                    </label>
                    <div class="sm:col-span-2 xl:col-span-4 flex justify-end">
                        <button class="btn btn-primary" type="submit">Show timetable</button>
                    </div>
                </form>
            </section>

            @if($selectedRoom)
                <section class="card">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $selectedRoom->room_name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $selectedRoom->room_building }} · {{ $selectedRoom->room_type ?? 'Room' }}{{ $selectedRoom->room_capacity ? ' · Capacity '.$selectedRoom->room_capacity : '' }}</p>
                        </div>
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">{{ $selectedSemester?->sem_name ?? 'No semester' }} · {{ number_format($scheduledHours, 1) }} scheduled hours / week</span>
                    </div>

                    @if(!$selectedSemester)
                        <p class="mt-5 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">There is no semester registered for this academic year.</p>
                    @else
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4 2xl:grid-cols-7">
                            @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @php($daySchedules = $schedulesByDay->get($day, collect()))
                                <article class="min-h-48 rounded-xl border border-slate-200 bg-slate-50 p-3">
                                    <div class="mb-3 flex items-center justify-between border-b border-slate-200 pb-2">
                                        <h3 class="text-sm font-bold text-slate-700">{{ $day }}</h3>
                                        <span class="rounded-full bg-white px-2 py-0.5 text-[11px] font-semibold text-slate-500">{{ $daySchedules->count() }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        @forelse($daySchedules as $schedule)
                                            <div class="rounded-lg border border-blue-200 bg-white p-2.5 shadow-sm">
                                                <p class="text-[11px] font-bold text-blue-700">{{ \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') }} – {{ \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-800">{{ $schedule->subject?->course_code ?? 'Course' }}</p>
                                                <p class="text-xs text-slate-600">{{ $schedule->subject?->course_name ?? 'Course title unavailable' }}</p>
                                                <div class="mt-2 space-y-0.5 border-t border-slate-100 pt-2 text-[11px] text-slate-500">
                                                    <p>Section: {{ $schedule->section?->sec_name ?? '—' }}</p>
                                                    <p>Faculty: {{ $schedule->faculty?->full_name ?? $schedule->faculty?->user?->usr_name ?? 'Unassigned' }}</p>
                                                    <p>{{ ucfirst($schedule->sch_shift ?? 'Day') }}</p>
                                                </div>
                                            </div>
                                        @empty
                                            <p class="py-8 text-center text-xs text-slate-400">Room is free</p>
                                        @endforelse
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </section>
            @else
                <section class="card py-12 text-center text-sm text-slate-500">No rooms were found for this floor in your college.</section>
            @endif
        </main>
    </div>
</div>
</body>
</html>
