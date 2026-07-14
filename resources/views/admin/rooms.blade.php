<!DOCTYPE html>

<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Room Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/rooms.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.admin_sidebar')

  <!-- MAIN -->

  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Room Management</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

<div id="page-rooms" class="page active">
  <div class="stat-grid" style="grid-template-columns:repeat(3,1fr)">
    <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Total Rooms</div><div class="stat-value" id="stat-total">{{ $rooms->count() }}</div><div class="stat-sub">Lecture + Lab</div></div>
    <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">Available</div><div class="stat-value" id="stat-available">{{ $rooms->where('room_is_available', true)->count() }}</div><div class="stat-sub">Ready to assign</div></div>
    <div class="stat-card" style="--accent:#dc2626"><div class="stat-label">In Use</div><div class="stat-value" id="stat-inuse">{{ $rooms->where('room_is_available', false)->count() }}</div><div class="stat-sub">Currently occupied</div></div>
  </div>
  <div class="card">
    <div class="card-header"><div class="card-title">Room Management</div><button class="topbar-btn btn-primary" onclick="openModal('modal-add-room')">+ Add Room</button></div>
    <div class="table-wrap"><table id="rooms-table">
      <tr><th>Room</th><th>Type</th><th>Capacity</th><th>Status</th><th>Actions</th></tr>
      @foreach ($rooms as $room)
      <tr data-room-id="{{ $room->room_id }}">
        <td class="cell-name"><b>{{ $room->room_name }}</b></td>
        <td class="cell-type">{{ $room->room_type }}</td>
        <td class="cell-capacity">{{ $room->room_capacity }}</td>
        <td class="cell-status">
          @if ($room->room_is_available)
            <span class="badge badge-green">Available</span>
          @else
            <span class="badge badge-amber">In Use</span>
          @endif
        </td>
        <td style="display:flex;gap:6px;">
          <button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('{{ $room->room_id }}')">View</button>
          <button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('{{ $room->room_id }}')">Edit</button>
          <button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteRoom('{{ $room->room_id }}', '{{ $room->room_name }}')">Delete</button>
        </td>
      </tr>
      @endforeach
    </table></div>
  </div>
</div>

  </div>
</div>

<!-- ADD ROOM MODAL -->

<div class="modal-overlay" id="modal-add-room">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Add New Room</div>
      <button class="modal-close" onclick="closeModal('modal-add-room')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Room Name / Number</label><input class="field-input" id="add-room-name" placeholder="e.g. Room 303"></div>
      <div class="field-group"><label class="field-label">Room Type</label>
        <select class="field-select" id="add-room-type">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Capacity</label><input class="field-input" id="add-room-capacity" type="number" min="1" placeholder="e.g. 40"></div>
      <div class="field-group"><label class="field-label">Building</label><input class="field-input" id="add-room-building" placeholder="e.g. ICT Building"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Location / Floor</label><input class="field-input" id="add-room-location" placeholder="e.g. 2nd Floor"></div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-room')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="saveAddRoom()">Add Room</button>
    </div>
  </div>
</div>

<!-- VIEW ROOM MODAL -->

<div class="modal-overlay" id="modal-view-room">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title">Room Details</div>
      <button class="modal-close" onclick="closeModal('modal-view-room')">✕</button>
    </div>
    <!-- Room Header -->
    <div style="display:flex;align-items:center;gap:16px;padding:18px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:14px;margin-bottom:20px;">
      <div style="width:56px;height:56px;border-radius:14px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;">🚪</div>
      <div>
        <div id="vr-name" style="font-size:20px;font-weight:800;color:#fff;"></div>
        <div id="vr-type" style="font-size:13px;color:rgba(255,255,255,0.6);margin-top:2px;"></div>
        <div id="vr-status-badge" style="margin-top:6px;"></div>
      </div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Capacity</div>
        <div id="vr-capacity" style="font-size:22px;font-weight:800;color:var(--text);"></div>
        <div style="font-size:11px;color:var(--text3);">students</div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Building</div>
        <div id="vr-building" style="font-size:13px;font-weight:600;color:var(--text);">—</div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:20px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Location / Floor</div>
      <div id="vr-location" style="font-size:13px;font-weight:600;color:var(--text);">—</div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-view-room')">Close</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteFromView()">Delete</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-view-room'); openEditRoom(currentRoomId)">Edit Room</button>
    </div>
  </div>
</div>

<!-- EDIT ROOM MODAL -->

<div class="modal-overlay" id="modal-edit-room">
  <div class="modal" style="width:500px;">
    <div class="modal-header">
      <div class="modal-title">Edit Room</div>
      <button class="modal-close" onclick="closeModal('modal-edit-room')">✕</button>
    </div>
    <div style="display:flex;align-items:center;gap:16px;padding:16px;background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:12px;margin-bottom:20px;">
      <div style="width:50px;height:50px;border-radius:12px;background:var(--blue);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">🚪</div>
      <div id="edit-room-header" style="font-size:17px;font-weight:800;color:#fff;"></div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Room Name / Number</label><input class="field-input" id="edit-room-name" placeholder="e.g. Room 301"></div>
      <div class="field-group"><label class="field-label">Room Type</label>
        <select class="field-select" id="edit-room-type">
          <option>Lecture</option><option>Laboratory</option><option>AVR / Function Hall</option><option>Conference Room</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Capacity</label><input class="field-input" id="edit-room-capacity" type="number" min="1"></div>
      <div class="field-group"><label class="field-label">Status</label>
        <select class="field-select" id="edit-room-status">
          <option value="1">Available</option><option value="0">In Use</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Building</label><input class="field-input" id="edit-room-building" placeholder="e.g. ICT Building"></div>
      <div class="field-group"><label class="field-label">Location / Floor</label><input class="field-input" id="edit-room-location" placeholder="e.g. 2nd Floor"></div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
      ⚠️ Changes will be reflected immediately in the room directory.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-room')">Cancel</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteFromEdit()">Delete</button>
      <button class="topbar-btn btn-primary" onclick="saveEditRoom()">Save Changes</button>
    </div>
  </div>
</div>

<!-- ERROR MODAL -->

<div class="modal-overlay" id="modal-error">
  <div class="modal" style="width:400px;">
    <div style="display:flex;align-items:center;gap:14px;padding:8px 0 16px;">
      <div style="width:44px;height:44px;border-radius:50%;background:var(--red-light);display:flex;align-items:center;justify-content:center;font-size:22px;flex-shrink:0;">⚠️</div>
      <div class="modal-title" style="margin:0;">Action Failed</div>
    </div>
    <div id="error-modal-message" style="font-size:14px;color:var(--text2);line-height:1.6;margin-bottom:20px;"></div>
    <div class="modal-footer">
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-error')">OK</button>
    </div>
  </div>
</div>

<!-- TOAST -->

<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
let currentRoomId = null;

// Room data passed from backend, keyed by room_id, for instant modal fill (no extra fetch)
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
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'var(--blue)', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
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

    // Add to local data store
    ROOMS_DATA[room.room_id] = {
      name: room.room_name,
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
      location: room.room_location
    };

    // Insert new row into table
    const tbody = document.getElementById('rooms-table');
    const tr = document.createElement('tr');
    tr.setAttribute('data-room-id', room.room_id);
    tr.style.opacity = '0';
    tr.innerHTML = `
      <td class="cell-name"><b>${room.room_name}</b></td>
      <td class="cell-type">${room.room_type}</td>
      <td class="cell-capacity">${room.room_capacity}</td>
      <td class="cell-status"><span class="badge badge-green">Available</span></td>
      <td style="display:flex;gap:6px;">
        <button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('${room.room_id}')">View</button>
        <button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('${room.room_id}')">Edit</button>
        <button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteRoom('${room.room_id}', '${room.room_name}')">Delete</button>
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
  const colors = { 'Available':'badge-green', 'In Use':'badge-amber' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="badge ${colors[status]}">${status}</span>`;
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

    // Update local data store
    ROOMS_DATA[room.room_id] = {
      name: room.room_name,
      type: room.room_type,
      capacity: room.room_capacity,
      available: room.room_is_available,
      building: room.room_building,
      location: room.room_location
    };

    // Update the table row in place
    const row = document.querySelector(`tr[data-room-id="${room.room_id}"]`);
    if (row) {
      row.querySelector('.cell-name').innerHTML = `<b>${room.room_name}</b>`;
      row.querySelector('.cell-type').textContent = room.room_type;
      row.querySelector('.cell-capacity').textContent = room.room_capacity;
      row.querySelector('.cell-status').innerHTML = room.room_is_available
        ? `<span class="badge badge-green">Available</span>`
        : `<span class="badge badge-amber">In Use</span>`;
      // Update delete button's captured name argument
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