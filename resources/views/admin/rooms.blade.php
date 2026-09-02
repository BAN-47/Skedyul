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
  @include('partials.admin_sidebar')

  <div class="app-main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Room Management</div>
      <div id="topbar-notif-bell" class="relative">
        <button onclick="toggleNotifDropdown()"
          class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
          Notifications <span id="notif-count" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;"
          class="absolute top-11 right-0 w-[340px] bg-white border border-slate-200 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] z-[100] overflow-hidden">
          <div class="px-4 py-3.5 border-b border-slate-200 text-sm font-bold text-slate-900">Notifications</div>
          <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
          <div class="px-4 py-2.5 border-t border-slate-200 text-center">
            <button onclick="markAllRead()" class="text-[12px] text-blue-600 font-semibold bg-transparent border-none cursor-pointer">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

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
        <div class="overflow-x-auto">
          <table class="data-table" id="rooms-table">
            <tr>
              @foreach(['Room','Type','Capacity','Status','Actions'] as $h)
              <th>{{ $h }}</th>
              @endforeach
            </tr>
            @foreach ($rooms as $room)
            <tr data-room-id="{{ $room->room_id }}">
              <td class="cell-name font-semibold">{{ $room->room_name }}</td>
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
                  <button onclick="deleteRoom('{{ $room->room_id }}', '{{ $room->room_name }}')" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
                </div>
              </td>
            </tr>
            @endforeach
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
    <div class="mb-4">
      <label class="field-label">Location / Floor</label>
      <input id="add-room-location" placeholder="e.g. 2nd Floor" class="field-input">
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
    <div class="bg-slate-50 rounded-lg p-3.5 mb-5">
      <div class="text-[10px] font-bold uppercase tracking-wide text-slate-400 mb-1">Location / Floor</div>
      <div id="vr-location" class="text-[13px] font-semibold text-slate-900">—</div>
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
      <div>
        <label class="field-label">Location / Floor</label>
        <input id="edit-room-location" placeholder="e.g. 2nd Floor" class="field-input">
      </div>
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

const ROOMS_DATA = {
  @foreach ($rooms as $room)
  "{{ $room->room_id }}": {
    name: @json($room->room_name),
    type: @json($room->room_type),
    capacity: {{ $room->room_capacity }},
    available: {{ $room->room_is_available ? 'true' : 'false' }},
    building: @json($room->room_building),
    location: @json($room->room_location)
  },
  @endforeach
};

// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const ADMIN_NOTIFS = [
  { dot:'#dc2626', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'#d97706', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'#2563eb', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'#16a34a', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'#2563eb', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = ADMIN_NOTIFS.map((n) => `
    <div class="notif-drop-item ${n.unread?'unread':''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  const dd = document.getElementById('notif-dropdown');
  if (dd) dd.style.display = notifOpen ? 'block' : 'none';
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = 'none';
  }
});
function markRead(el) {
  el.classList.remove('unread');
  updateNotifCount();
}
function markAllRead() {
  document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('.notif-drop-item.unread').length;
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline' : 'none'; }
}
renderNotifList();

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

// ── ROOMS: ADD ─────────────────────────────────────────────────────────────────
async function saveAddRoom() {
  const name = document.getElementById('add-room-name').value.trim();
  if (!name) { showErrorModal('Please enter a room name.'); return; }
  const type       = document.getElementById('add-room-type').value;
  const capacity   = document.getElementById('add-room-capacity').value;
  const building   = document.getElementById('add-room-building').value.trim();
  const location   = document.getElementById('add-room-location').value.trim();

  if (!capacity) { showErrorModal('Please enter a capacity.'); return; }

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
        room_type: type,
        room_capacity: capacity,
        room_is_available: true,
        room_building: building || null,
        room_location: location || null
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
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
      location: room.room_location
    };

    const tbody = document.getElementById('rooms-table');
    const tr = document.createElement('tr');
    tr.setAttribute('data-room-id', room.room_id);
    tr.style.opacity = '0';
    tr.innerHTML = `
      <td class="cell-name font-semibold">${room.room_name}</td>
      <td class="cell-type">${room.room_type}</td>
      <td class="cell-capacity">${room.room_capacity}</td>
      <td class="cell-status"><span class="badge badge-green">Available</span></td>
      <td>
        <div class="flex gap-1.5">
          <button onclick="openViewRoom('${room.room_id}')" class="btn btn-secondary text-[11px] px-3 py-1.5">View</button>
          <button onclick="openEditRoom('${room.room_id}')" class="btn btn-secondary text-[11px] px-3 py-1.5">Edit</button>
          <button onclick="deleteRoom('${room.room_id}', '${room.room_name}')" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
    requestAnimationFrame(() => { tr.style.transition = 'opacity 0.3s'; tr.style.opacity = '1'; });

    refreshStats();
    closeModal('modal-add-room');
    ['add-room-name','add-room-capacity','add-room-building','add-room-location'].forEach(id => {
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
  document.getElementById('vr-building').textContent = room.building || '—';
  document.getElementById('vr-location').textContent = room.location || '—';
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
  document.getElementById('edit-room-location').value   = room.location || '';
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
  const location = document.getElementById('edit-room-location').value.trim();

  if (!name || !capacity) { showErrorModal('Please fill in all required fields.'); return; }

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
        room_type: type,
        room_capacity: capacity,
        room_is_available: available,
        room_building: building || null,
        room_location: location || null
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
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
      location: room.room_location
    };

    const row = document.querySelector(`tr[data-room-id="${room.room_id}"]`);
    if (row) {
      row.querySelector('.cell-name').textContent = room.room_name;
      row.querySelector('.cell-type').textContent = room.room_type;
      row.querySelector('.cell-capacity').textContent = room.room_capacity;
      row.querySelector('.cell-status').innerHTML = room.room_is_available
        ? `<span class="badge badge-green">Available</span>`
        : `<span class="badge badge-amber">In Use</span>`;
      const deleteBtn = row.querySelector('button[onclick^="deleteRoom("]');
      if (deleteBtn) deleteBtn.setAttribute('onclick', `deleteRoom('${room.room_id}', '${room.room_name}')`);
    }

    refreshStats();
    closeModal('modal-edit-room');
    showToast(data.message || ('Room "' + name + '" updated successfully!'));
  } catch (err) {
    showErrorModal('Failed to update room. Please try again.');
    console.error(err);
  }
}

// ── ROOMS: DELETE ──────────────────────────────────────────────────────────────
async function deleteRoom(roomId, name) {
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
      setTimeout(() => { row.remove(); refreshStats(); }, 300);
    } else {
      refreshStats();
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