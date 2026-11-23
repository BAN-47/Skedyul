<!DOCTYPE html>
<html lang="en">
<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
    $gridBusy = [];
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
            $span = min($span, $slotCount - $startRow);
            $gridSchedules[] = ['day' => $dayIndex, 'row' => $startRow, 'span' => $span, 'schedule' => $schedule];
            for ($row = $startRow; $row < $startRow + $span; $row++) $gridBusy[$dayIndex][$row] = true;
        }
    }
@endphp
<div class="app-shell">
    @include('partials.chair_sidebar')
    <div class="app-main">
        @include('partials.chair_header', ['title' => 'Room Utilization', 'badgeText' => $selectedSemester ? (($selectedYear?->ay_academic_year ?? $selectedYear?->ay_year_label ?? 'Academic year').' · '.$selectedSemester->sem_name) : 'No semester selected'])

        <main class="page-content space-y-4">
            <section class="card">
                <form id="room-utilization-filters" method="GET" action="{{ route('chair.room_utilization') }}" class="flex flex-wrap items-center gap-2">
                        <select aria-label="Academic year" name="academic_year" class="field-input min-w-[180px] flex-1" onchange="this.form.submit()">
                            @foreach($academicYears as $year)
                                <option value="{{ $year->ay_id }}" @selected((string) $selectedYearId === (string) $year->ay_id)>{{ $year->ay_academic_year ?? $year->ay_year_label }}{{ $year->ay_is_active ? ' · Current' : '' }}</option>
                            @endforeach
                        </select>
                        <select aria-label="Semester" name="semester" class="field-input min-w-[180px] flex-1" onchange="this.form.submit()">
                            @forelse($semesters as $semester)
                                <option value="{{ $semester->sem_id }}" @selected((string) $selectedSemester?->sem_id === (string) $semester->sem_id)>{{ $semester->sem_name }}{{ $semester->sem_is_active ? ' · Current' : '' }}</option>
                            @empty
                                <option value="">No semester in this year</option>
                            @endforelse
                        </select>
                        <select aria-label="Floor" name="floor" class="field-input min-w-[135px] flex-1" onchange="this.form.submit()">
                            <option value="all" @selected($floorFilter === 'all')>All floors</option>
                            @foreach(range(1, 10) as $floor)
                                <option value="{{ $floor }}" @selected($floorFilter === (string) $floor)>{{ $floor }}{{ match ($floor % 10) { 1 => 'st', 2 => 'nd', 3 => 'rd', default => 'th' } }} floor</option>
                            @endforeach
                        </select>
                    <div class="relative min-w-[240px] flex-[1.4]" id="room-picker">
                        <input type="hidden" name="room_id" id="room-id" value="{{ $selectedRoom?->room_id }}">
                        <button type="button" id="room-picker-toggle" class="field-input flex w-full items-center justify-between text-left">
                            <span id="room-picker-label" class="truncate">{{ $selectedRoom ? $selectedRoom->room_name.' · '.$selectedRoom->room_building : 'Select room' }}</span>
                            <span class="ml-2 rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Search⌄</span>
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
                    <div class="inline-flex rounded-xl bg-slate-100 p-1" aria-label="Select shift">
                        <a class="rounded-lg px-4 py-2 text-sm font-bold {{ $shift === 'day' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white' }}" href="{{ route('chair.room_utilization', request()->except('shift') + ['shift' => 'day']) }}">Day</a>
                        <a class="rounded-lg px-4 py-2 text-sm font-bold {{ $shift === 'night' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:bg-white' }}" href="{{ route('chair.room_utilization', request()->except('shift') + ['shift' => 'night']) }}">Evening</a>
                    </div>
                    <input type="hidden" name="shift" value="{{ $shift }}">
                </form>

            @if($selectedRoom)
                <div class="mt-4 border-t border-slate-100 pt-4">
                    <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="text-lg font-bold text-slate-900">{{ $selectedRoom->room_name }}</h2>
                            <p class="mt-1 text-sm text-slate-500">{{ $selectedRoom->room_building }} · {{ $selectedRoom->room_type ?? 'Room' }}{{ $selectedRoom->room_capacity ? ' · Capacity '.$selectedRoom->room_capacity : '' }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">{{ $selectedSemester?->sem_name ?? 'No semester' }} · {{ number_format($scheduledHours, 1) }} scheduled hours / week</span>
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
                                <div class="grid relative" style="grid-template-columns:90px repeat(7,minmax(0,1fr));grid-template-rows:repeat({{ $slotCount }},26px);">
                                    @for($i=0;$i<$slotCount;$i+=2)
                                        @php $mins=$gridStart+$i*$slotMinutes; $from=\Illuminate\Support\Carbon::createFromTime(intdiv($mins,60),0); $to=(clone $from)->addHour(); @endphp
                                        <div class="flex items-center justify-end border-b border-slate-200 bg-slate-50 px-2 text-[11px] font-semibold text-slate-500" style="grid-column:1;grid-row:{{ $i+1 }}/span 2">{{ $from->format('g') }}–{{ $to->format('g A') }}</div>
                                    @endfor
                                    @for($row=0;$row<$slotCount;$row++)
                                        @php $mins=$gridStart+$row*$slotMinutes; @endphp
                                        @foreach($days as $dayIndex=>$day)
                                            @php
                                                $isNoonBreak = $shift === 'day' && $dayIndex < 6 && $mins >= 12 * 60 && $mins < 13 * 60;
                                                $isBusy = isset($gridBusy[$dayIndex][$row]);
                                                $cellClass = 'border-b border-l border-slate-100 '.($mins % 60 === 0 ? '!border-b-slate-200' : '');
                                            @endphp
                                            @if($canPlotSelectedSemester && !$isBusy && !$isNoonBreak)
                                                <button type="button" onclick="openRoomAddModal('{{ $day }}','{{ \Illuminate\Support\Carbon::createFromTime(intdiv($mins,60),$mins%60)->format('H:i') }}')" class="{{ $cellClass }} bg-white hover:bg-blue-50" style="grid-column:{{ $dayIndex+2 }};grid-row:{{ $row+1 }}" title="Add schedule — {{ $day }} {{ \Illuminate\Support\Carbon::createFromTime(intdiv($mins,60),$mins%60)->format('g:i A') }}"></button>
                                            @else
                                                <div class="{{ $cellClass }} {{ $isNoonBreak ? 'bg-slate-100' : '' }}" style="grid-column:{{ $dayIndex+2 }};grid-row:{{ $row+1 }}"></div>
                                            @endif
                                        @endforeach
                                    @endfor
                                    @if($shift === 'day')
                                        @php $noonRow = intdiv(12 * 60 - $gridStart, $slotMinutes) + 1; @endphp
                                        <div class="z-20 flex items-center justify-center bg-slate-900 text-xs font-bold tracking-[3px] text-white pointer-events-none" style="grid-column:2/span 6;grid-row:{{ $noonRow }}/span 2">NOON BREAK</div>
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
                </div>
            @else
                <div class="mt-4 border-t border-slate-100 py-12 text-center text-sm text-slate-500">No rooms were found for this floor in your college.</div>
            @endif
            </section>
        </main>
    </div>
</div>
@if($selectedRoom && $selectedSemester && $canPlotSelectedSemester)
<div id="room-add-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="room-add-title">
    <div class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-2xl">
        <div class="flex items-start justify-between gap-4"><div><h2 id="room-add-title" class="text-lg font-bold">Add schedule to room</h2><p class="mt-1 text-sm text-slate-500">{{ $selectedRoom->room_name }} · {{ $selectedSemester->sem_name }}</p></div><button type="button" class="btn btn-secondary" onclick="closeRoomAddModal()">Close</button></div>
        <div class="mt-4 grid gap-3 sm:grid-cols-2">
            <label class="text-xs font-semibold uppercase text-slate-500">Section
                <select id="room-add-section" class="field-input mt-1"><option value="">Select section</option>@foreach($sections as $section)<option value="{{ $section->sec_id }}" data-department="{{ $section->sec_dept_id }}" data-year="{{ $section->sec_year_level }}" data-evening="{{ $section->sec_is_evening ? '1' : '0' }}">{{ $section->sec_name }} · {{ $section->program?->dept_code }}{{ $section->sec_is_evening ? ' · Evening' : ' · Day' }}</option>@endforeach</select>
            </label>
            <label class="text-xs font-semibold uppercase text-slate-500">Category
                <select id="room-add-category" class="field-input mt-1"><option value="">All categories</option>@foreach($categories as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select>
            </label>
            <label class="text-xs font-semibold uppercase text-slate-500 sm:col-span-2">Subject
                <select id="room-add-subject" class="field-input mt-1"><option value="">Select a section first</option>@foreach($courses as $course)<option value="{{ $course->course_id }}" data-category="{{ $course->course_category }}" data-department="{{ $course->course_dept_id }}" data-year="{{ $course->course_year_level }}" data-semester="{{ $course->course_semester }}">{{ $course->course_code }} · {{ $course->course_name }}</option>@endforeach</select>
            </label>
            <label class="text-xs font-semibold uppercase text-slate-500 sm:col-span-2">Professor
                <select id="room-add-faculty" class="field-input mt-1"><option value="">Select professor</option>@foreach($faculty as $member)<option value="{{ $member->fac_id }}">{{ $member->full_name }}{{ $member->fac_is_placeholder ? ' · Unassigned' : '' }}</option>@endforeach</select>
            </label>
            <label class="text-xs font-semibold uppercase text-slate-500">Start time<input id="room-add-start" type="time" step="1800" class="field-input mt-1"></label>
            <label class="text-xs font-semibold uppercase text-slate-500">End time<input id="room-add-end" type="time" step="1800" class="field-input mt-1"></label>
            <div class="sm:col-span-2"><p class="text-xs font-semibold uppercase text-slate-500">Day</p><div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-4">@foreach($days as $day)<label class="flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm"><input class="room-add-day" type="checkbox" value="{{ $day }}">{{ $day }}</label>@endforeach</div><p id="room-add-notice" class="mt-2 text-xs text-amber-700"></p></div>
        </div>
        <p id="room-add-error" class="mt-3 hidden rounded-lg bg-rose-50 p-3 text-sm text-rose-700"></p>
        <div class="mt-5 flex justify-end gap-2"><button type="button" class="btn btn-secondary" onclick="closeRoomAddModal()">Cancel</button><button id="room-add-save" type="button" class="btn btn-primary" onclick="submitRoomSchedule()">Save schedule</button></div>
    </div>
</div>
@endif
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

const roomAddModal = document.getElementById('room-add-modal');
function openRoomAddModal(day, time) {
    if (!roomAddModal) return;
    document.getElementById('room-add-error').classList.add('hidden');
    document.getElementById('room-add-start').value = time;
    const [hour, minute] = time.split(':').map(Number);
    const endMinute = hour * 60 + minute + 60;
    document.getElementById('room-add-end').value = `${String(Math.floor(endMinute / 60)).padStart(2,'0')}:${String(endMinute % 60).padStart(2,'0')}`;
    document.querySelectorAll('.room-add-day').forEach(input => { input.checked = input.value === day; });
    roomAddModal.classList.remove('hidden'); roomAddModal.classList.add('flex');
}
function closeRoomAddModal() { roomAddModal?.classList.add('hidden'); roomAddModal?.classList.remove('flex'); }
function updateRoomAddSubjectOptions() {
    const sectionSelect = document.getElementById('room-add-section');
    const subjectSelect = document.getElementById('room-add-subject');
    const category = document.getElementById('room-add-category').value;
    const section = sectionSelect.selectedOptions[0];
    const isEvening = section?.dataset.evening === '1';
    document.querySelectorAll('.room-add-day').forEach(input => {
        const blocked = !isEvening && input.value === 'Sunday';
        input.disabled = blocked;
        if (blocked) input.checked = false;
    });
    document.getElementById('room-add-notice').textContent = !section ? '' : (!isEvening ? 'Day section: Sunday is unavailable. Monday–Saturday noon break is blocked.' : 'Monday–Saturday noon break is blocked.');
    [...subjectSelect.options].forEach((option, index) => {
        if (index === 0) { option.hidden = false; return; }
        const categoryMatch = !category || option.dataset.category === category;
        const scopedMatch = category || !section || (option.dataset.department === section.dataset.department && option.dataset.year === section.dataset.year && option.dataset.semester === '{{ str_contains(strtolower((string) $selectedSemester?->sem_name), '2nd') ? '2' : '1' }}');
        option.hidden = !(categoryMatch && scopedMatch);
    });
    if (subjectSelect.selectedOptions[0]?.hidden) subjectSelect.value = '';
}
document.getElementById('room-add-section')?.addEventListener('change', updateRoomAddSubjectOptions);
document.getElementById('room-add-category')?.addEventListener('change', updateRoomAddSubjectOptions);
async function submitRoomSchedule() {
    const error = document.getElementById('room-add-error');
    const days = [...document.querySelectorAll('.room-add-day:checked')].map(input => input.value);
    const section = document.getElementById('room-add-section');
    const payload = {
        subj_id: document.getElementById('room-add-subject').value,
        fac_id: document.getElementById('room-add-faculty').value,
        room_id: document.getElementById('room-id').value,
        sem_id: '{{ $selectedSemester?->sem_id }}',
        sec_id: section.value,
        days,
        start_time: document.getElementById('room-add-start').value,
        end_time: document.getElementById('room-add-end').value,
        shift: section.selectedOptions[0]?.dataset.evening === '1' ? 'evening' : 'day'
    };
    if (!payload.subj_id || !payload.fac_id || !payload.sec_id || !payload.room_id || !days.length || !payload.start_time || !payload.end_time) {
        error.textContent = 'Choose a section, subject, professor, day, and start and end times.'; error.classList.remove('hidden'); return;
    }
    if (payload.end_time <= payload.start_time) { error.textContent = 'End time must be later than start time.'; error.classList.remove('hidden'); return; }
    const button = document.getElementById('room-add-save'); button.disabled = true; button.textContent = 'Saving…';
    try {
        const response = await fetch('{{ route('chair.pbs.store') }}', {method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify(payload)});
        const result = await response.json();
        if (!response.ok || !result.success) throw new Error(result.message || Object.values(result.errors || {}).flat()[0] || 'Schedule could not be saved.');
        window.location.reload();
    } catch (exception) {
        error.textContent = exception.message; error.classList.remove('hidden');
    } finally { button.disabled = false; button.textContent = 'Save schedule'; }
}
</script>
</body>
</html>
