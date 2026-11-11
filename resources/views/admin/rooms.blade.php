<!DOCTYPE html>

<html lang="en">
<head>
  @include('partials.favicon')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Room Management</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">
  @include('partials.admin_sidebar')

  <div class="app-main">
        @include('partials.admin_header', ['title' => 'Technical Admin Rooms'])


    <div class="page-content" id="page-rooms">

      {{-- STAT CARDS --}}
      <div class="grid grid-cols-3 gap-3 mb-4">
        <div class="stat-card">
          <div class="stat-card-bar bg-blue-600"></div>
          <div class="stat-label">Total Rooms</div>
          <div class="stat-value" id="stat-total">{{ $rooms->count() }}</div>
          <div class="stat-sub">Lecture + Lab</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-green-600"></div>
          <div class="stat-label">Available</div>
          <div class="stat-value" id="stat-available">{{ $rooms->where('room_is_available', true)->count() }}</div>
          <div class="stat-sub">Ready to assign</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-red-600"></div>
          <div class="stat-label">In Use</div>
          <div class="stat-value" id="stat-inuse">{{ $rooms->where('room_is_available', false)->count() }}</div>
          <div class="stat-sub">Currently occupied</div>
        </div>
      </div>

      {{-- ROOMS TABLE CARD --}}
      <div class="card">
        <div class="card-header">
          <div class="card-title">Room Management</div>
          <button onclick="openModal('modal-add-room')" class="btn btn-primary">+ Add Room</button>
        </div>
        <div class="flex flex-col gap-2 px-4 pb-3 sm:flex-row sm:items-center sm:justify-between">
          <input id="room-search" type="search" placeholder="Search rooms, colleges, or room type..." aria-label="Search rooms"
            class="w-full max-w-sm rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
          <div class="flex items-center gap-2 text-xs text-slate-500">
            <label for="room-page-size">Rows</label>
            <select id="room-page-size" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5">
              <option value="10" selected>10</option>
              <option value="25">25</option>
              <option value="50">50</option>
            </select>
            <span id="room-page-info" aria-live="polite"></span>
            <button type="button" id="room-page-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
            <button type="button" id="room-page-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="data-table" id="rooms-table">
            <thead>
              <tr>
                @foreach(['Room','College','Type','Capacity','Status','Actions'] as $h)
                <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody id="rooms-table-body">
            @foreach ($rooms as $room)
            <tr data-room-id="{{ $room->room_id }}" class="room-row">
              <td class="cell-name font-semibold">{{ $room->room_name }}</td>
              <td class="cell-college">{{ $collegeNames[$room->room_college_id] ?? 'Unassigned' }}</td>
              <td class="cell-type">{{ $room->room_type }}</td>
              <td class="cell-capacity">{{ $room->room_capacity }}</td>
              <td class="cell-status">
                @if ($room->room_is_available)
                  <span class="badge badge-green">Available</span>
                @else
                  <span class="badge badge-amber">In Use</span>
                @endif
              </td>
              <td>
                <div class="flex gap-1.5">
                  <button onclick="openViewRoom('{{ $room->room_id }}')" class="btn btn-secondary text-[11px] px-3 py-1.5">View</button>
                  <button onclick="openEditRoom('{{ $room->room_id }}')" class="btn btn-secondary text-[11px] px-3 py-1.5">Edit</button>
                  <button onclick="deleteRoom('{{ $room->room_id }}')" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
                </div>
              </td>
            </tr>
            @endforeach
            <tr id="rooms-no-results" hidden><td colspan="6" class="text-center py-6 text-slate-400">No rooms match your search.</td></tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ADD ROOM MODAL --}}
<div class="modal-overlay" id="modal-add-room">
  <div class="modal-box w-[500px]">
    <div class="modal-header">
      <div class="modal-title">Add New Room</div>
      <button onclick="closeModal('modal-add-room')" class="modal-close">✕</button>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Room Name / Number</label>
        <input id="add-room-name" placeholder="e.g. Room 303" class="field-input">
      </div>
      <div>
        <label class="field-label">Room Type</label>
        <select id="add-room-type" class="field-input">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="mb-3">
      <label class="field-label">Assigned College <span class="text-red-500">*</span></label>
      <select id="add-room-college" class="field-input" required>
        <option value="">Select a college</option>
        @foreach($colleges as $college)
          <option value="{{ $college->college_id }}">{{ $college->college_name }} ({{ $college->college_code }})</option>
        @endforeach
      </select>
      <p class="text-[11px] text-slate-400 mt-1">This room will be assigned to the selected college.</p>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Capacity</label>
        <input id="add-room-capacity" type="number" min="1" placeholder="e.g. 40" class="field-input">
      </div>
      <div>
        <label class="field-label">Building</label>
        <input id="add-room-building" placeholder="e.g. ICT Building" class="field-input">
      </div>
    </div>
    <div class="modal-footer">
      <button onclick="closeModal('modal-add-room')" class="btn btn-secondary">Cancel</button>
      <button onclick="saveAddRoom()" class="btn btn-primary">Add Room</button>
    </div>
  </div>
</div>

{{-- VIEW ROOM MODAL --}}
<div class="modal-overlay" id="modal-view-room">
  <div class="modal-box w-[480px]">
    <div class="modal-header">
      <div class="modal-title">Room Details</div>
      <button onclick="closeModal('modal-view-room')" class="modal-close">✕</button>
    </div>
    <div class="flex items-center gap-4 p-4.5 rounded-2xl mb-5" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">
      <div class="w-14 h-14 rounded-2xl bg-blue-600 flex items-center justify-center text-2xl flex-shrink-0">🚪</div>
      <div>
        <div id="vr-name" class="text-xl font-extrabold text-white"></div>
        <div id="vr-type" class="text-[13px] text-white/60 mt-0.5"></div>
        <div id="vr-status-badge" class="mt-1.5"></div>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3.5">
      <div class="bg-slate-50 rounded-lg p-3.5">
        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">Capacity</div>
        <div id="vr-capacity" class="text-xl font-extrabold text-slate-900"></div>
        <div class="text-[11px] text-slate-400">students</div>
      </div>
      <div class="bg-slate-50 rounded-lg p-3.5">
        <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">Building</div>
        <div id="vr-building" class="text-[13px] font-semibold text-slate-900">—</div>
      </div>
    </div>
    <div class="bg-slate-50 rounded-lg p-3.5 mb-3.5">
      <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">Assigned College</div>
      <div id="vr-college" class="text-[13px] font-semibold text-slate-900">—</div>
    </div>
    <div class="modal-footer">
      <button onclick="closeModal('modal-view-room')" class="btn btn-secondary">Close</button>
      <button onclick="deleteFromView()" class="btn btn-danger">Delete</button>
      <button onclick="closeModal('modal-view-room'); openEditRoom(currentRoomId)" class="btn btn-primary">Edit Room</button>
    </div>
  </div>
</div>

{{-- EDIT ROOM MODAL --}}
<div class="modal-overlay" id="modal-edit-room">
  <div class="modal-box w-[500px]">
    <div class="modal-header">
      <div class="modal-title">Edit Room</div>
      <button onclick="closeModal('modal-edit-room')" class="modal-close">✕</button>
    </div>
    <div class="flex items-center gap-4 p-4 rounded-xl mb-5" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">
      <div class="w-[50px] h-[50px] rounded-xl bg-blue-600 flex items-center justify-center text-[22px] flex-shrink-0">🚪</div>
      <div id="edit-room-header" class="text-[17px] font-extrabold text-white"></div>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Room Name / Number</label>
        <input id="edit-room-name" placeholder="e.g. Room 301" class="field-input">
      </div>
      <div>
        <label class="field-label">Room Type</label>
        <select id="edit-room-type" class="field-input">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Capacity</label>
        <input id="edit-room-capacity" type="number" min="1" class="field-input">
      </div>
      <div>
        <label class="field-label">Status</label>
        <select id="edit-room-status" class="field-input">
          <option value="1">Available</option><option value="0">In Use</option>
        </select>
      </div>
    </div>
    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Building</label>
        <input id="edit-room-building" placeholder="e.g. ICT Building" class="field-input">
      </div>
    </div>
    <div class="mb-3">
      <label class="field-label">Assigned College <span class="text-red-500">*</span></label>
      <select id="edit-room-college" class="field-input" required>
        <option value="">Select a college</option>
        @foreach($colleges as $college)
          <option value="{{ $college->college_id }}">{{ $college->college_name }} ({{ $college->college_code }})</option>
        @endforeach
      </select>
    </div>
    <div class="bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12px] text-amber-800 mb-1">
      ⚠️ Changes will be reflected immediately in the room directory.
    </div>
    <div class="modal-footer">
      <button onclick="closeModal('modal-edit-room')" class="btn btn-secondary">Cancel</button>
      <button onclick="deleteFromEdit()" class="btn btn-danger">Delete</button>
      <button onclick="saveEditRoom()" class="btn btn-primary">Save Changes</button>
    </div>
  </div>
</div>

{{-- ERROR MODAL --}}
<div class="modal-overlay" id="modal-error">
  <div class="modal-box w-[400px]">
    <div class="flex items-center gap-3.5 pb-4">
      <div class="w-11 h-11 rounded-full bg-red-100 flex items-center justify-center text-[22px] flex-shrink-0">⚠️</div>
      <div class="modal-title">Action Failed</div>
    </div>
    <div id="error-modal-message" class="text-sm text-slate-600 leading-relaxed mb-5"></div>
    <div class="modal-footer">
      <button onclick="closeModal('modal-error')" class="btn btn-primary">OK</button>
    </div>
  </div>
</div>

<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let currentRoomId = null;
const roomSearch = document.getElementById('room-search');
const roomPageSizeSelect = document.getElementById('room-page-size');
const roomPageInfo = document.getElementById('room-page-info');
const roomPrevious = document.getElementById('room-page-prev');
const roomNext = document.getElementById('room-page-next');
const roomNoResults = document.getElementById('rooms-no-results');
let roomPage = 0;
let roomPageSize = Number(roomPageSizeSelect.value);

const ROOMS_DATA = {
  @foreach ($rooms as $room)
  "{{ $room->room_id }}": {
    name: @json($room->room_name),
    collegeId: @json($room->room_college_id),
    collegeName: @json($collegeNames[$room->room_college_id] ?? 'Unassigned'),
    type: @json($room->room_type),
    capacity: {{ $room->room_capacity }},
    available: {{ $room->room_is_available ? 'true' : 'false' }},
    building: @json($room->room_building),
  },
  @endforeach
};

// ── MODALS ─────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── ERROR MODAL (replaces browser alert()) ──────────────────────────────────
function showErrorModal(message) {
  document.getElementById('error-modal-message').textContent = message;
  openModal('modal-error');
}

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
}

// ── STATS ──────────────────────────────────────────────────────────────────
function refreshStats() {
  const rooms = Object.values(ROOMS_DATA);
  document.getElementById('stat-total').textContent = rooms.length;
  document.getElementById('stat-available').textContent = rooms.filter(r => r.available).length;
  document.getElementById('stat-inuse').textContent = rooms.filter(r => !r.available).length;
}

function renderRooms() {
  const rows = [...document.querySelectorAll('#rooms-table-body [data-room-id]')];
  const query = roomSearch.value.trim().toLowerCase();
  const matches = rows.filter(row => row.textContent.toLowerCase().includes(query));
  const pageCount = Math.max(1, Math.ceil(matches.length / roomPageSize));
  roomPage = Math.max(0, Math.min(roomPage, pageCount - 1));
  const start = roomPage * roomPageSize;

  rows.forEach(row => { row.hidden = true; });
  matches.slice(start, start + roomPageSize).forEach(row => { row.hidden = false; });
  roomNoResults.hidden = matches.length > 0;
  roomPageInfo.textContent = matches.length
    ? `Showing ${start + 1}–${Math.min(start + roomPageSize, matches.length)} of ${matches.length}`
    : 'Showing 0 of 0';
  roomPrevious.disabled = roomPage === 0;
  roomNext.disabled = roomPage >= pageCount - 1;
}

function createRoomRow(room, collegeName) {
  const row = document.createElement('tr');
  row.dataset.roomId = room.room_id;
  row.className = 'room-row';

  const addCell = (value, className = '') => {
    const cell = document.createElement('td');
    cell.className = className;
    cell.textContent = value;
    row.appendChild(cell);
    return cell;
  };

  addCell(room.room_name, 'cell-name font-semibold');
  addCell(collegeName, 'cell-college');
  addCell(room.room_type, 'cell-type');
  addCell(room.room_capacity, 'cell-capacity');

  const statusCell = addCell('', 'cell-status');
  const statusBadge = document.createElement('span');
  statusBadge.className = room.room_is_available ? 'badge badge-green' : 'badge badge-amber';
  statusBadge.textContent = room.room_is_available ? 'Available' : 'In Use';
  statusCell.appendChild(statusBadge);

  const actionsCell = document.createElement('td');
  const actions = document.createElement('div');
  actions.className = 'flex gap-1.5';
  [
    ['View', 'btn btn-secondary text-[11px] px-3 py-1.5', () => openViewRoom(room.room_id)],
    ['Edit', 'btn btn-secondary text-[11px] px-3 py-1.5', () => openEditRoom(room.room_id)],
    ['Delete', 'btn btn-danger text-[11px] px-3 py-1.5', () => deleteRoom(room.room_id, ROOMS_DATA[room.room_id]?.name || room.room_name)],
  ].forEach(([label, className, action]) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = className;
    button.textContent = label;
    button.addEventListener('click', action);
    actions.appendChild(button);
  });
  actionsCell.appendChild(actions);
  row.appendChild(actionsCell);

  return row;
}

roomSearch.addEventListener('input', () => { roomPage = 0; renderRooms(); });
roomPageSizeSelect.addEventListener('change', () => {
  roomPageSize = Number(roomPageSizeSelect.value);
  roomPage = 0;
  renderRooms();
});
roomPrevious.addEventListener('click', () => { roomPage--; renderRooms(); });
roomNext.addEventListener('click', () => { roomPage++; renderRooms(); });
renderRooms();

// ── ROOMS: ADD ─────────────────────────────────────────────────────────────────
async function saveAddRoom() {
  const name = document.getElementById('add-room-name').value.trim();
  if (!name) { showErrorModal('Please enter a room name.'); return; }
  const type       = document.getElementById('add-room-type').value;
  const capacity   = document.getElementById('add-room-capacity').value;
  const building   = document.getElementById('add-room-building').value.trim();
  const collegeId  = document.getElementById('add-room-college').value;

  if (!capacity) { showErrorModal('Please enter a capacity.'); return; }
  if (!collegeId) { showErrorModal('Please assign this room to a college.'); return; }

  try {
    const res = await fetch("{{ route('admin.rooms.store') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        room_name: name,
        room_college_id: collegeId,
        room_type: type,
        room_capacity: capacity,
        room_is_available: true,
        room_building: building || null
      })
    });

    const data = await res.json();

    if (!res.ok) {
      showErrorModal(data.message || 'Failed to add room. Please try again.');
      return;
    }

    const room = data.room;

    ROOMS_DATA[room.room_id] = {
      name: room.room_name,
      collegeId: room.room_college_id,
      collegeName: document.querySelector(`#add-room-college option[value="${room.room_college_id}"]`)?.textContent.trim() || 'Assigned',
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
    };

    const tbody = document.getElementById('rooms-table-body');
    const tr = createRoomRow(room, ROOMS_DATA[room.room_id].collegeName);
    tr.style.opacity = '0';
    tbody.appendChild(tr);
    requestAnimationFrame(() => { tr.style.transition = 'opacity 0.3s'; tr.style.opacity = '1'; });

    roomSearch.value = '';
    roomPage = Math.ceil(Object.keys(ROOMS_DATA).length / roomPageSize) - 1;
    renderRooms();
    refreshStats();
    closeModal('modal-add-room');
    ['add-room-name','add-room-capacity','add-room-building','add-room-college'].forEach(id => {
      const el = document.getElementById(id);
      if (el) el.value = '';
    });
    showToast(data.message || ('Room "' + name + '" added successfully!'));
  } catch (err) {
    showErrorModal('Failed to add room. Please try again.');
    console.error(err);
  }
}

// ── ROOMS: VIEW ────────────────────────────────────────────────────────────────
function openViewRoom(roomId) {
  currentRoomId = roomId;
  const room = ROOMS_DATA[roomId];
  if (!room) return;

  document.getElementById('vr-name').textContent     = room.name;
  document.getElementById('vr-type').textContent     = room.type;
  document.getElementById('vr-capacity').textContent = room.capacity;
  document.getElementById('vr-college').textContent = room.collegeName || 'Unassigned';
  document.getElementById('vr-building').textContent = room.building || '—';
  const status = room.available ? 'Available' : 'In Use';
  const badgeClass = room.available ? 'badge badge-green' : 'badge badge-amber';
  document.getElementById('vr-status-badge').innerHTML = `<span class="${badgeClass}">${status}</span>`;
  openModal('modal-view-room');
}

// ── ROOMS: EDIT ────────────────────────────────────────────────────────────────
function openEditRoom(roomId) {
  currentRoomId = roomId;
  const room = ROOMS_DATA[roomId];
  if (!room) return;

  document.getElementById('edit-room-header').textContent = room.name;
  document.getElementById('edit-room-name').value       = room.name;
  document.getElementById('edit-room-capacity').value   = room.capacity;
  document.getElementById('edit-room-building').value   = room.building || '';
  document.getElementById('edit-room-college').value    = room.collegeId || '';
  setSelectValue('edit-room-type', room.type);
  setSelectValue('edit-room-status', room.available ? '1' : '0');
  openModal('modal-edit-room');
}

async function saveEditRoom() {
  const name     = document.getElementById('edit-room-name').value.trim();
  const type     = document.getElementById('edit-room-type').value;
  const capacity = document.getElementById('edit-room-capacity').value;
  const available = document.getElementById('edit-room-status').value === '1';
  const building = document.getElementById('edit-room-building').value.trim();
  const collegeId = document.getElementById('edit-room-college').value;

  if (!name || !capacity) { showErrorModal('Please fill in all required fields.'); return; }
  if (!collegeId) { showErrorModal('Please assign this room to a college.'); return; }

  try {
    const res = await fetch(`/rooms/${currentRoomId}`, {
      method: 'PUT',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        room_name: name,
        room_college_id: collegeId,
        room_type: type,
        room_capacity: capacity,
        room_is_available: available,
        room_building: building || null
      })
    });

    const data = await res.json();

    if (!res.ok) {
      showErrorModal(data.message || 'Failed to update room. Please try again.');
      return;
    }

    const room = data.room;

    ROOMS_DATA[room.room_id] = {
      name: room.room_name,
      collegeId: room.room_college_id,
      collegeName: document.querySelector(`#edit-room-college option[value="${room.room_college_id}"]`)?.textContent.trim() || 'Assigned',
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
    };

    const row = document.querySelector(`tr[data-room-id="${room.room_id}"]`);
    if (row) {
      row.querySelector('.cell-name').textContent = room.room_name;
      row.querySelector('.cell-college').textContent = ROOMS_DATA[room.room_id].collegeName;
      row.querySelector('.cell-type').textContent = room.room_type;
      row.querySelector('.cell-capacity').textContent = room.room_capacity;
      row.querySelector('.cell-status').innerHTML = room.room_is_available
        ? `<span class="badge badge-green">Available</span>`
        : `<span class="badge badge-amber">In Use</span>`;
    }

    renderRooms();
    refreshStats();
    closeModal('modal-edit-room');
    showToast(data.message || ('Room "' + name + '" updated successfully!'));
  } catch (err) {
    showErrorModal('Failed to update room. Please try again.');
    console.error(err);
  }
}

// ── ROOMS: DELETE ──────────────────────────────────────────────────────────────
async function deleteRoom(roomId, name = ROOMS_DATA[roomId]?.name || 'Room') {
  if (!confirm('Delete room: ' + name + '?\nThis action cannot be undone.')) return;

  try {
    const res = await fetch(`/rooms/${roomId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      }
    });

    const data = await res.json();

    if (!res.ok) {
      showErrorModal(data.message || 'Failed to delete room. Please try again.');
      return;
    }

    showToast(data.message);
    delete ROOMS_DATA[roomId];
    const row = document.querySelector(`tr[data-room-id="${roomId}"]`);
    if (row) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => { row.remove(); refreshStats(); renderRooms(); }, 300);
    } else {
      refreshStats();
      renderRooms();
    }
  } catch (err) {
    showErrorModal('Failed to delete room. Please try again.');
    console.error(err);
  }
}

function deleteFromView() {
  const room = ROOMS_DATA[currentRoomId];
  closeModal('modal-view-room');
  deleteRoom(currentRoomId, room ? room.name : 'Room');
}

function deleteFromEdit() {
  const room = ROOMS_DATA[currentRoomId];
  closeModal('modal-edit-room');
  deleteRoom(currentRoomId, room ? room.name : 'Room');
}
</script>

</body>
</html>
