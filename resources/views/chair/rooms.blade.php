<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Room Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
<div class="app-shell">
    @include('partials.chair_sidebar')
    <div class="app-main">
        @include('partials.chair_header', [
            'title' => 'Room Management',
            'badgeText' => trim(collect([$academicYear->ay_academic_year ?? 'No AY', $semester->sem_name ?? 'No Semester'])->filter()->implode(' · '))
        ])

        <div class="page-content" id="page-chair-rooms">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="stat-card"><div class="stat-card-bar bg-blue-600"></div><div class="stat-label">Assigned Rooms</div><div class="stat-value">{{ $totalRooms }}</div><div class="stat-sub">{{ $building }}</div></div>
                <div class="stat-card"><div class="stat-card-bar bg-green-600"></div><div class="stat-label">Available</div><div class="stat-value">{{ $availableCount }}</div><div class="stat-sub">No active schedule this semester</div></div>
                <div class="stat-card"><div class="stat-card-bar bg-amber-500"></div><div class="stat-label">In Use</div><div class="stat-value">{{ $inUseCount }}</div><div class="stat-sub">Has an active schedule</div></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Rooms for Your College</div>
                        <div class="card-sub">Room availability and scheduled classes for {{ $semester->sem_name ?? 'the active semester' }}</div>
                    </div>
                    <button type="button" onclick="openChairAddRoom()" class="btn btn-primary">+ Add Room</button>
                </div>

                @if(session('success'))
                    <div class="mb-3 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ session('error') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
                @endif

                <div class="flex flex-col gap-2 mb-3 sm:flex-row sm:items-center sm:justify-between">
                    <input id="chair-room-search" type="search" placeholder="Search rooms or subjects..." aria-label="Search rooms"
                        class="field-input w-full max-w-sm">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <span id="chair-room-page-info" aria-live="polite"></span>
                        <nav class="flex items-center gap-1.5" aria-label="Room pagination">
                            <button type="button" id="chair-room-prev" class="min-w-[76px] rounded-xl bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">← Prev</button>
                            <div id="chair-room-page-numbers" class="flex items-center gap-1.5"></div>
                            <button type="button" id="chair-room-next" class="min-w-[76px] rounded-xl bg-slate-50 px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">Next →</button>
                        </nav>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table" id="chair-rooms-table">
                        <thead><tr>
                            @foreach(['Room','Type','Capacity','Status','Scheduled Classes','Action'] as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                        @forelse($roomData as $item)
                            @php
                                $room = $item['room'];
                                $roomSchedules = $item['schedules'];
                                $searchText = strtolower($room->room_name.' '.$room->room_type.' '.$room->room_building.' '.$roomSchedules->map(fn($s) => ($s->subject->course_code ?? $s->subject->subj_code ?? '').' '.($s->subject->course_name ?? $s->subject->subj_name ?? '').' '.($s->section->sec_name ?? '').' '.$s->sch_day)->implode(' '));
                            @endphp
                            <tr data-chair-room-row data-search="{{ $searchText }}">
                                <td class="font-semibold">{{ $room->room_name }}<div class="text-[11px] text-slate-400">{{ $room->room_building ?: 'Building not set' }}</div></td>
                                <td>{{ $room->room_type }}</td>
                                <td>{{ $room->room_capacity }}</td>
                                <td>@if($item['is_booked'])<span class="badge badge-amber">In Use</span>@else<span class="badge badge-green">Available</span>@endif</td>
                                <td>
                                    @forelse($roomSchedules->take(2) as $schedule)
                                        <div class="text-[12px] font-semibold text-slate-700">{{ $schedule->subject->course_code ?? $schedule->subject->subj_code ?? 'Class' }} · {{ substr($schedule->sch_day, 0, 3) }}</div>
                                    @empty
                                        <span class="text-xs text-slate-400">No active classes</span>
                                    @endforelse
                                    @if($roomSchedules->count() > 2)<div class="text-[11px] text-slate-400">+{{ $roomSchedules->count() - 2 }} more</div>@endif
                                </td>
                                <td><button type="button" class="btn btn-secondary text-[11px] px-3 py-1.5" onclick="viewChairRoom('{{ $room->room_id }}')">View</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-6 text-slate-400">No rooms assigned to your college yet. Use Add Room to create one.</td></tr>
                        @endforelse
                        <tr id="chair-room-no-results" hidden><td colspan="6" class="text-center py-6 text-slate-400">No rooms match your search.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="chair-room-add-modal">
    <div class="modal-box w-[520px] max-w-[94vw]">
        <form method="POST" action="{{ route('chair.rooms.store') }}">
            @csrf
            <div class="modal-header">
                <div><div class="modal-title">Add Room</div><div class="text-xs text-slate-400">This room will be assigned to your college.</div></div>
                <button type="button" onclick="closeChairAddRoom()" class="modal-close">✕</button>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="field-label">Room Name / Number</label>
                    <input name="room_name" value="{{ old('room_name') }}" placeholder="e.g. Room 303" required maxlength="100" class="field-input">
                </div>
                <div>
                    <label class="field-label">Room Type</label>
                    <select name="room_type" required class="field-input">
                        <option value="">Select type</option>
                        @foreach(['Lecture','Laboratory','AVR / Function Hall','Conference Room'] as $type)
                            <option value="{{ $type }}" @selected(old('room_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div>
                    <label class="field-label">Capacity</label>
                    <input type="number" name="room_capacity" value="{{ old('room_capacity') }}" min="1" required placeholder="e.g. 40" class="field-input">
                </div>
                <div>
                    <label class="field-label">Building</label>
                    <input name="room_building" value="{{ old('room_building') }}" required maxlength="100" placeholder="e.g. CCICT Building" class="field-input">
                </div>
            </div>
            <div class="mb-4">
                <label class="field-label">Location / Floor <span class="font-normal normal-case text-slate-400">(optional)</span></label>
                <input name="room_location" value="{{ old('room_location') }}" maxlength="150" placeholder="e.g. 2nd Floor" class="field-input">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="closeChairAddRoom()" class="btn btn-secondary">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Room</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="chair-room-view-modal">
    <div class="modal-box w-[560px] max-w-[94vw]">
        <div class="modal-header">
            <div><div class="modal-title" id="chair-room-modal-name">Room details</div><div class="text-xs text-slate-400" id="chair-room-modal-building"></div></div>
            <button type="button" onclick="closeChairRoomModal()" class="modal-close">✕</button>
        </div>
        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="rounded-xl bg-slate-50 p-3"><div class="field-label">Room type</div><div id="chair-room-modal-type" class="font-semibold"></div></div>
            <div class="rounded-xl bg-slate-50 p-3"><div class="field-label">Capacity</div><div id="chair-room-modal-capacity" class="font-semibold"></div></div>
        </div>
        <div class="field-label mb-2">Active scheduled classes</div>
        <div id="chair-room-modal-schedules" class="max-h-64 overflow-y-auto space-y-2"></div>
        <div class="modal-footer"><button type="button" onclick="closeChairRoomModal()" class="btn btn-secondary">Close</button></div>
    </div>
</div>

<script>
function openChairAddRoom() { document.getElementById('chair-room-add-modal').classList.add('active'); }
function closeChairAddRoom() { document.getElementById('chair-room-add-modal').classList.remove('active'); }
const chairRooms = {
    @foreach($roomData as $item)
    @php
        $room = $item['room'];
        $scheduledClasses = $item['schedules']->map(function ($schedule) {
            return [
                'course' => $schedule->subject->course_code ?? $schedule->subject->subj_code ?? 'Class',
                'title' => $schedule->subject->course_name ?? $schedule->subject->subj_name ?? '',
                'section' => $schedule->section->sec_name ?? 'Section not set',
                'day' => $schedule->sch_day,
                'time' => \Carbon\Carbon::parse($schedule->sch_start_time)->format('g:i A') . ' – ' . \Carbon\Carbon::parse($schedule->sch_end_time)->format('g:i A'),
            ];
        })->values();
    @endphp
    @json($room->room_id): {
        name: @json($room->room_name), building: @json($room->room_building), type: @json($room->room_type), capacity: @json($room->room_capacity),
        schedules: @json($scheduledClasses),
    },
    @endforeach
};

const roomRows = [...document.querySelectorAll('[data-chair-room-row]')];
const roomSearch = document.getElementById('chair-room-search');
const roomPageInfo = document.getElementById('chair-room-page-info');
const roomPageNumbers = document.getElementById('chair-room-page-numbers');
const roomNoResults = document.getElementById('chair-room-no-results');
const roomPageSize = 10;
let roomPage = 1;

function renderChairRoomRows() {
    const query = roomSearch.value.trim().toLowerCase();
    const matches = roomRows.filter(row => row.dataset.search.includes(query));
    const pages = Math.max(1, Math.ceil(matches.length / roomPageSize));
    roomPage = Math.min(Math.max(roomPage, 1), pages);
    const start = (roomPage - 1) * roomPageSize;
    roomRows.forEach(row => { row.hidden = true; });
    matches.slice(start, start + roomPageSize).forEach(row => { row.hidden = false; });
    roomNoResults.hidden = matches.length > 0 || roomRows.length === 0;
    roomPageInfo.textContent = matches.length ? `Showing ${start + 1}–${Math.min(start + roomPageSize, matches.length)} of ${matches.length}` : 'Showing 0 of 0';
    document.getElementById('chair-room-prev').disabled = roomPage <= 1;
    document.getElementById('chair-room-next').disabled = roomPage >= pages || matches.length === 0;
    roomPageNumbers.replaceChildren();
    for (let page = 1; page <= pages; page++) {
        const button = document.createElement('button');
        button.type = 'button';
        button.textContent = String(page);
        button.setAttribute('aria-label', `Page ${page}`);
        button.setAttribute('aria-current', page === roomPage ? 'page' : 'false');
        button.className = `min-w-[44px] rounded-xl px-3.5 py-2.5 text-sm font-semibold transition ${page === roomPage ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
        button.addEventListener('click', () => { roomPage = page; renderChairRoomRows(); });
        roomPageNumbers.appendChild(button);
    }
}
roomSearch.addEventListener('input', () => { roomPage = 1; renderChairRoomRows(); });
document.getElementById('chair-room-prev').addEventListener('click', () => { roomPage--; renderChairRoomRows(); });
document.getElementById('chair-room-next').addEventListener('click', () => { roomPage++; renderChairRoomRows(); });
renderChairRoomRows();

function viewChairRoom(id) {
    const room = chairRooms[id];
    if (!room) return;
    document.getElementById('chair-room-modal-name').textContent = room.name;
    document.getElementById('chair-room-modal-building').textContent = room.building || 'Building not set';
    document.getElementById('chair-room-modal-type').textContent = room.type;
    document.getElementById('chair-room-modal-capacity').textContent = `${room.capacity} students`;
    const list = document.getElementById('chair-room-modal-schedules');
    list.replaceChildren();
    if (!room.schedules.length) {
        const empty = document.createElement('div');
        empty.className = 'rounded-lg bg-slate-50 p-3 text-sm text-slate-400';
        empty.textContent = 'No active classes scheduled in this room.';
        list.appendChild(empty);
    } else {
        room.schedules.forEach(item => {
            const card = document.createElement('div');
            card.className = 'rounded-lg border border-slate-100 p-3';
            const title = document.createElement('div');
            title.className = 'font-semibold text-slate-800';
            title.textContent = `${item.course}${item.title ? ` — ${item.title}` : ''}`;
            const detail = document.createElement('div');
            detail.className = 'mt-1 text-xs text-slate-500';
            detail.textContent = `${item.section} · ${item.day} · ${item.time}`;
            card.append(title, detail);
            list.appendChild(card);
        });
    }
    document.getElementById('chair-room-view-modal').classList.add('open');
}
function closeChairRoomModal() { document.getElementById('chair-room-view-modal').classList.remove('open'); }
document.getElementById('chair-room-view-modal').addEventListener('click', event => {
    if (event.target.id === 'chair-room-view-modal') closeChairRoomModal();
});
document.getElementById('chair-room-add-modal').addEventListener('click', event => {
    if (event.target.id === 'chair-room-add-modal') closeChairAddRoom();
});
@if($errors->any())
openChairAddRoom();
@endif
</script>
</body>
</html>
