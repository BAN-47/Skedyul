<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Total Rooms</div><div class="stat-value">12</div><div class="stat-sub">Lecture + Lab</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">Available</div><div class="stat-value">9</div><div class="stat-sub">Ready to assign</div></div>
        <div class="stat-card" style="--accent:#dc2626"><div class="stat-label">In Use</div><div class="stat-value">3</div><div class="stat-sub">Currently occupied</div></div>
      </div>
      <div class="card">
        <div class="card-header"><div class="card-title">Room Management</div><button class="topbar-btn btn-primary" onclick="openModal('modal-add-room')">+ Add Room</button></div>
        <div class="table-wrap"><table id="rooms-table">
          <tr><th>Room</th><th>Type</th><th>Capacity</th><th>Status</th><th>Actions</th></tr>
          <tr><td><b>Room 301</b></td><td>Lecture</td><td>40</td><td><span class="badge badge-amber">In Use</span></td><td style="display:flex;gap:6px;"><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('Room 301','Lecture','40','In Use','CC 313 — Web Systems · Mon/Wed 7:00–8:30 AM','Jerome Bautista','Ground Floor, ICT Building','Air-conditioned, projector, whiteboard, 40 movable chairs.')">View</button><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('Room 301','Lecture','40','In Use','Ground Floor, ICT Building','Air-conditioned, projector, whiteboard, 40 movable chairs.')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'Room 301')">Delete</button></td></tr>
          <tr><td><b>Room 302</b></td><td>Lecture</td><td>40</td><td><span class="badge badge-green">Available</span></td><td style="display:flex;gap:6px;"><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('Room 302','Lecture','40','Available','—','—','Ground Floor, ICT Building','Air-conditioned, projector, whiteboard, 40 movable chairs.')">View</button><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('Room 302','Lecture','40','Available','Ground Floor, ICT Building','Air-conditioned, projector, whiteboard, 40 movable chairs.')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'Room 302')">Delete</button></td></tr>
          <tr><td><b>Lab 1</b></td><td>Laboratory</td><td>35</td><td><span class="badge badge-amber">In Use</span></td><td style="display:flex;gap:6px;"><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('Lab 1','Laboratory','35','In Use','IT 401 — System Admin · Tue/Thu 8:30–10:00 AM','Ana Reyes','2nd Floor, ICT Building','35 desktop computers, air-conditioned, network switches, projector.')">View</button><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('Lab 1','Laboratory','35','In Use','2nd Floor, ICT Building','35 desktop computers, air-conditioned, network switches, projector.')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'Lab 1')">Delete</button></td></tr>
          <tr><td><b>Lab 2</b></td><td>Laboratory</td><td>35</td><td><span class="badge badge-amber">In Use</span></td><td style="display:flex;gap:6px;"><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('Lab 2','Laboratory','35','In Use','IT 302 — Networking · Mon/Wed 1:00–2:30 PM','Carlo Mendoza','2nd Floor, ICT Building','35 desktop computers, network lab equipment, patch panels.')">View</button><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('Lab 2','Laboratory','35','In Use','2nd Floor, ICT Building','35 desktop computers, network lab equipment, patch panels.')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'Lab 2')">Delete</button></td></tr>
          <tr><td><b>Room 201</b></td><td>Lecture</td><td>45</td><td><span class="badge badge-green">Available</span></td><td style="display:flex;gap:6px;"><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openViewRoom('Room 201','Lecture','45','Available','—','—','2nd Floor, Main Building','Air-conditioned, smart TV, whiteboard, 45 fixed chairs.')">View</button><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditRoom('Room 201','Lecture','45','Available','2nd Floor, Main Building','Air-conditioned, smart TV, whiteboard, 45 fixed chairs.')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'Room 201')">Delete</button></td></tr>
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
      <div class="field-group"><label class="field-label">Building / Location</label><input class="field-input" id="add-room-location" placeholder="e.g. 2nd Floor, ICT Building"></div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Facilities / Equipment</label><textarea class="field-input" id="add-room-facilities" rows="2" placeholder="e.g. Air-conditioned, projector, whiteboard..." style="resize:vertical;"></textarea></div>
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
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Location</div>
        <div id="vr-location" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:12px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Current Assignment</div>
      <div id="vr-assignment" style="font-size:13px;font-weight:600;color:var(--text);"></div>
      <div id="vr-faculty" style="font-size:12px;color:var(--text3);margin-top:3px;"></div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:20px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:6px;">Facilities</div>
      <div id="vr-facilities" style="font-size:13px;color:var(--text2);line-height:1.6;"></div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-view-room')">Close</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteCurrentRoom('modal-view-room')">Delete</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-view-room');showToast('Room details saved!')">Edit Room</button>
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
          <option>Available</option><option>In Use</option><option>Under Maintenance</option>
        </select>
      </div>
    </div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Building / Location</label><input class="field-input" id="edit-room-location" placeholder="e.g. 2nd Floor, ICT Building"></div>
    <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Facilities / Equipment</label><textarea class="field-input" id="edit-room-facilities" rows="2" style="resize:vertical;" placeholder="e.g. Air-conditioned, projector..."></textarea></div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
      ⚠️ Changes will be reflected immediately in the room directory.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-room')">Cancel</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);padding:8px 16px;" onclick="deleteCurrentRoom('modal-edit-room')">Delete</button>
      <button class="topbar-btn btn-primary" id="edit-room-save-btn">Save Changes</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
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

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
}

// ── ROOMS: ADD ─────────────────────────────────────────────────────────────────
function saveAddRoom() {
  const name = document.getElementById('add-room-name').value.trim();
  if (!name) { alert('Please enter a room name.'); return; }
  const type       = document.getElementById('add-room-type').value;
  const capacity   = document.getElementById('add-room-capacity').value || '—';
  const location   = document.getElementById('add-room-location').value || '—';
  const facilities = document.getElementById('add-room-facilities').value || '—';
  const tbody = document.querySelector('#rooms-table');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><b>${name}</b></td><td>${type}</td><td>${capacity}</td>
    <td><span class="badge badge-green">Available</span></td>
    <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;"
      onclick="openViewRoom('${name}','${type}','${capacity}','Available','—','—','${location}','${facilities}')">View</button></td>`;
  tbody.appendChild(tr);
  ['add-room-name','add-room-capacity','add-room-location','add-room-facilities'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-room');
  showToast('Room "' + name + '" added successfully!');
}

// ── ROOMS: VIEW ────────────────────────────────────────────────────────────────
function openViewRoom(name, type, capacity, status, assignment, faculty, location, facilities) {
  document.getElementById('vr-name').textContent     = name;
  document.getElementById('vr-type').textContent     = type;
  document.getElementById('vr-capacity').textContent = capacity;
  document.getElementById('vr-location').textContent = location;
  document.getElementById('vr-assignment').textContent = assignment;
  document.getElementById('vr-faculty').textContent  = faculty !== '—' ? 'Faculty: ' + faculty : '';
  document.getElementById('vr-facilities').textContent = facilities;
  const colors = { 'Available':'badge-green', 'In Use':'badge-amber', 'Under Maintenance':'badge-red' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="badge ${colors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-view-room');
}

// ── ROOMS: EDIT ────────────────────────────────────────────────────────────────
function openEditRoom(name, type, capacity, status, location, facilities) {
  document.getElementById('edit-room-header').textContent = name;
  document.getElementById('edit-room-name').value       = name;
  document.getElementById('edit-room-capacity').value   = capacity;
  document.getElementById('edit-room-location').value   = location;
  document.getElementById('edit-room-facilities').value = facilities;
  setSelectValue('edit-room-type', type);
  setSelectValue('edit-room-status', status);
  document.getElementById('edit-room-save-btn').onclick = () => {
    closeModal('modal-edit-room');
    showToast('Room "' + document.getElementById('edit-room-name').value + '" updated successfully!');
  };
  openModal('modal-edit-room');
}

// ── ROOMS: DELETE ──────────────────────────────────────────────────────────────
function deleteCurrentRoom(modalId) {
  const name = document.getElementById('vr-name') ? document.getElementById('vr-name').textContent
    : (document.getElementById('edit-room-name') ? document.getElementById('edit-room-name').value : 'Room');
  if (!confirm('Delete room: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  const rows = document.querySelectorAll('#rooms-table tr');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => row.remove(), 300);
    }
  });
  showToast(name + ' deleted successfully.');
}

function deleteTableRow(btn, name) {
  if (!confirm('Delete: ' + name + '?\nThis action cannot be undone.')) return;
  const row = btn.closest('tr');
  if (row) {
    row.style.transition = 'opacity 0.3s';
    row.style.opacity = '0';
    setTimeout(() => { row.remove(); }, 300);
  }
  showToast(name + ' deleted successfully.');
}
</script>
</body>
</html>