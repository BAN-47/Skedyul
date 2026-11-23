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
@php
    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $gridStart = $shift === 'night' ? 16 * 60 : 7 * 60;
    $gridEnd = $shift === 'night' ? 22 * 60 : 16 * 60;
    $slotMinutes = 30;
    $slotCount = intdiv($gridEnd - $gridStart, $slotMinutes);
    $gridSchedules = [];
    foreach ($schedulesByDay as $dayName => $dayItems) {
        $dayIndex = array_search($dayName, $days, true);
        if ($dayIndex === false) continue;
        foreach ($dayItems as $schedule) {
            $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
            $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
            $startMinute = max($start->hour * 60 + $start->minute, $gridStart);
            $endMinute = min($end->hour * 60 + $end->minute, $gridEnd);
            if ($endMinute <= $startMinute) continue;
            $startRow = intdiv($startMinute - $gridStart, $slotMinutes);
            $span = max(1, (int) ceil(($endMinute - $startMinute) / $slotMinutes));
            $gridSchedules[] = ['day' => $dayIndex, 'row' => $startRow, 'span' => min($span, $slotCount - $startRow), 'schedule' => $schedule];
        }
    }
@endphp
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

                <form id="room-utilization-filters" method="GET" action="{{ route('chair.room_utilization') }}" class="grid gap-3 border-t border-slate-100 pt-4 sm:grid-cols-2 xl:grid-cols-4">
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
                        <input type="hidden" name="room_id" id="room-id" value="{{ $selectedRoom?->room_id }}">
                        <div class="relative mt-1" id="room-picker">
                            <button type="button" id="room-picker-toggle" class="field-input flex w-full items-center justify-between text-left">
                                <span id="room-picker-label" class="truncate">{{ $selectedRoom ? $selectedRoom->room_name.' · '.$selectedRoom->room_building : 'No rooms on this floor' }}</span>
                                <span class="ml-2 text-slate-400">⌄</span>
                            </button>
                            <div id="room-picker-panel" class="absolute z-30 mt-1 hidden w-full rounded-xl border border-slate-200 bg-white p-2 shadow-xl">
                                <input id="room-picker-search" type="search" class="field-input mb-2 w-full" placeholder="Search room or building…" autocomplete="off">
                                <div id="room-picker-options" class="max-h-60 space-y-1 overflow-y-auto">
                                    @forelse($visibleRooms as $room)
                                        <button type="button" class="room-picker-option block w-full rounded-lg px-3 py-2 text-left text-sm hover:bg-blue-50" data-room-id="{{ $room->room_id }}" data-search="{{ strtolower($room->room_name.' '.$room->room_building.' '.($room->utilization_floor ? $room->utilization_floor.' floor' : '')) }}" data-label="{{ $room->room_name }} · {{ $room->room_building }}">
                                            <span class="font-semibold text-slate-800">{{ $room->room_name }}</span><span class="text-slate-500"> · {{ $room->room_building }}{{ $room->utilization_floor ? ' · Floor '.$room->utilization_floor : '' }}</span>
                                        </button>
                                    @empty
                                        <p class="px-3 py-2 text-sm text-slate-400">No rooms on this floor.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </label>
                    <input type="hidden" name="shift" value="{{ $shift }}">
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
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">{{ $selectedSemester?->sem_name ?? 'No semester' }} · {{ number_format($scheduledHours, 1) }} scheduled hours / week</span>
                            <div class="inline-flex rounded-xl bg-slate-100 p-1" aria-label="Select shift">
                                <a class="rounded-lg px-4 py-2 text-sm font-bold {{ $shift === 'day' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white' }}" href="{{ route('chair.room_utilization', request()->except('shift') + ['shift' => 'day']) }}">Day</a>
                                <a class="rounded-lg px-4 py-2 text-sm font-bold {{ $shift === 'night' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white' }}" href="{{ route('chair.room_utilization', request()->except('shift') + ['shift' => 'night']) }}">Evening</a>
                            </div>
                        </div>
                    </div>

                    @if(!$selectedSemester)
                        <p class="mt-5 rounded-lg bg-amber-50 p-4 text-sm text-amber-800">There is no semester registered for this academic year.</p>
                    @else
                        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-200">
                            <div class="min-w-[1050px]">
                                <div class="grid border-b border-slate-200 bg-slate-50" style="grid-template-columns: 90px repeat(7,minmax(0,1fr));">
                                    <div class="px-3 py-3 text-right text-[11px] font-bold uppercase text-slate-500">Time</div>
                                    @foreach($days as $day)<div class="border-l border-slate-200 px-2 py-3 text-center text-xs font-bold text-slate-700">{{ $day }}</div>@endforeach
                                </div>
                                <div class="grid relative" style="grid-template-columns:90px repeat(7,minmax(0,1fr));grid-template-rows:repeat({{ $slotCount }},30px);">
                                    @for($i=0;$i<$slotCount;$i+=2)
                                        @php $mins=$gridStart+$i*$slotMinutes; $from=\Illuminate\Support\Carbon::createFromTime(intdiv($mins,60),0); $to=(clone $from)->addHour(); @endphp
                                        <div class="flex items-center justify-end border-b border-slate-200 bg-slate-50 px-2 text-[11px] font-semibold text-slate-500" style="grid-column:1;grid-row:{{ $i+1 }}/span 2">{{ $from->format('g') }}–{{ $to->format('g A') }}</div>
                                    @endfor
                                    @for($row=0;$row<$slotCount;$row++)
                                        @php $mins=$gridStart+$row*$slotMinutes; @endphp
                                        @foreach($days as $dayIndex=>$day)
                                            <div class="border-b border-l border-slate-100 {{ $mins % 60 === 0 ? '!border-b-slate-200' : '' }} {{ $shift === 'day' && $dayIndex === 6 ? 'bg-slate-100/80' : '' }}" style="grid-column:{{ $dayIndex+2 }};grid-row:{{ $row+1 }}"></div>
                                        @endforeach
                                    @endfor
                                    @if($shift === 'day')
                                        <div class="z-10 flex items-center justify-center border border-slate-300 bg-slate-200/90 px-2 text-center text-[11px] font-bold text-slate-500" style="grid-column:8;grid-row:1/span {{ $slotCount }}">No Day Classes Sunday</div>
                                        @php $noonRow = intdiv(12 * 60 - $gridStart, $slotMinutes) + 1; @endphp
                                        <div class="z-20 flex items-center justify-center bg-slate-900 text-xs font-bold tracking-[3px] text-white" style="grid-column:2/span 6;grid-row:{{ $noonRow }}/span 2">NOON BREAK</div>
                                    @endif
                                    @foreach($gridSchedules as $block)
                                        @php $schedule=$block['schedule']; @endphp
                                        <article class="z-10 m-0.5 overflow-hidden rounded-lg border border-blue-200 bg-blue-50 p-2 shadow-sm" style="grid-column:{{ $block['day']+2 }};grid-row:{{ $block['row']+1 }}/span {{ $block['span'] }}">
                                            <p class="text-[10px] font-bold text-blue-700">{{ \Illuminate\Support\Carbon::parse($schedule->sch_start_time)->format('g:i A') }}–{{ \Illuminate\Support\Carbon::parse($schedule->sch_end_time)->format('g:i A') }}</p>
                                            <p class="truncate text-xs font-bold text-slate-800">{{ $schedule->subject?->course_code ?? 'Course' }}</p>
                                            <p class="truncate text-[11px] text-slate-600">{{ $schedule->subject?->course_name ?? 'Course title unavailable' }}</p>
                                            <p class="mt-1 truncate text-[10px] text-slate-500">{{ $schedule->section?->sec_name ?? '—' }} · {{ $schedule->faculty?->full_name ?? $schedule->faculty?->user?->usr_name ?? 'Unassigned' }}</p>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </section>
            @else
                <section class="card py-12 text-center text-sm text-slate-500">No rooms were found for this floor in your college.</section>
            @endif
        </main>
    </div>
</div>
<script>
(() => {
    const form = document.getElementById('room-utilization-filters');
    const toggle = document.getElementById('room-picker-toggle');
    const panel = document.getElementById('room-picker-panel');
    const search = document.getElementById('room-picker-search');
    const roomId = document.getElementById('room-id');
    const label = document.getElementById('room-picker-label');
    if (!form || !toggle || !panel) return;
    toggle.addEventListener('click', () => { panel.classList.toggle('hidden'); if (!panel.classList.contains('hidden')) search?.focus(); });
    search?.addEventListener('input', () => {
        const term = search.value.trim().toLowerCase();
        document.querySelectorAll('.room-picker-option').forEach(option => { option.classList.toggle('hidden', !option.dataset.search.includes(term)); });
    });
    document.querySelectorAll('.room-picker-option').forEach(option => option.addEventListener('click', () => {
        roomId.value = option.dataset.roomId;
        label.textContent = option.dataset.label;
        form.submit();
    }));
    document.addEventListener('click', event => { if (!event.target.closest('#room-picker')) panel.classList.add('hidden'); });
})();
</script>
</body>
</html>
