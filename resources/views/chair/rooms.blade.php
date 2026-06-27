<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Room Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/rooms.css') }}">
</head>
<style>
  html, body { overflow: hidden; }

  .sidebar-nav {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .sidebar-nav::-webkit-scrollbar { display: none; }

  .page-content {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .page-content::-webkit-scrollbar { display: none; }
</style>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Room Management</div>
      <div class="topbar-semester-badge">BSIS · AY 2025–26 · 1st Sem</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" class="topbar-notif-btn">
          Notifications
          <span id="notif-dot" class="topbar-notif-dot"></span>
        </button>
        <div id="notif-dropdown" class="notif-dropdown">
          <div class="notif-dropdown-header">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div class="notif-dropdown-footer">
            <button onclick="markAllRead()" class="notif-mark-all-btn">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    <div class="page-content" style="display: block;animation: fadeIn .3s ease;">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <div class="page-heading">Room Management</div>
          <div class="page-subheading">ICT Building · AY 2025–26 · 1st Semester</div>
        </div>
        <button class="topbar-btn btn-primary" onclick="openModal('modal-add-room')">+ Add Room</button>
      </div>

      <!-- Stat cards -->
      <div class="stat-grid" style="margin-bottom:24px;">
        <div class="stat-card" style="--accent:#2563eb">
          <div class="stat-label">Total Rooms</div>
          <div class="stat-value">5</div>
          <div class="stat-sub">ICT Building</div>
        </div>
        <div class="stat-card" style="--accent:#16a34a">
          <div class="stat-label">Available</div>
          <div class="stat-value">2</div>
          <div class="stat-sub">Ready to assign</div>
        </div>
        <div class="stat-card" style="--accent:#d97706">
          <div class="stat-label">In Use</div>
          <div class="stat-value">3</div>
          <div class="stat-sub">Currently assigned</div>
        </div>
        <div class="stat-card" style="--accent:#0891b2">
          <div class="stat-label">Laboratories</div>
          <div class="stat-value">2</div>
          <div class="stat-sub">Lab 1 &amp; Lab 2</div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;">

        <!-- Room table -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Room Overview</div>
              <div class="card-sub">Manage room assignments and availability</div>
            </div>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Room</th>
                  <th>Type</th>
                  <th>Capacity</th>
                  <th>Status</th>
                  <th>Assigned Subject</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><b>Room 301</b></td>
                  <td>Lecture</td>
                  <td>45</td>
                  <td><span class="badge badge-amber">In Use</span></td>
                  <td style="font-size:12px;color:var(--text3);">CC 313 — Mon/Wed 7:00</td>
                  <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-edit-room')">Edit</button></td>
                </tr>
                <tr>
                  <td><b>Room 302</b></td>
                  <td>Lecture</td>
                  <td>45</td>
                  <td><span class="badge badge-amber">In Use</span></td>
                  <td style="font-size:12px;color:var(--text3);">CC 401 — Tue/Thu 8:30</td>
                  <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-edit-room')">Edit</button></td>
                </tr>
                <tr>
                  <td><b>Room 205</b></td>
                  <td>Lecture</td>
                  <td>40</td>
                  <td><span class="badge badge-green">Available</span></td>
                  <td style="font-size:12px;color:var(--text3);">—</td>
                  <td><button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-edit-room')">Assign</button></td>
                </tr>
                <tr>
                  <td><b>Lab 1</b></td>
                  <td>Laboratory</td>
                  <td>40</td>
                  <td><span class="badge badge-amber">In Use</span></td>
                  <td style="font-size:12px;color:var(--text3);">IT 401 — Fri 10:00</td>
                  <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-edit-room')">Edit</button></td>
                </tr>
                <tr>
                  <td><b>Lab 2</b></td>
                  <td>Laboratory</td>
                  <td>40</td>
                  <td><span class="badge badge-green">Available</span></td>
                  <td style="font-size:12px;color:var(--text3);">—</td>
                  <td><button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-edit-room')">Assign</button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Room availability sidebar -->
        <div class="card">
          <div class="card-header"><div class="card-title">Room Availability</div></div>

          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Room 301</div>
              <div class="workload-val" style="color:var(--amber);">In Use</div>
            </div>
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">Lecture · Cap. 45</div>
            <div class="room-usage-bar"><div class="room-usage-fill" style="width:100%;background:var(--amber);"></div></div>
            <div style="font-size:11px;color:var(--amber);margin-top:3px;">CC 313 — Mon/Wed 7:00</div>
          </div>

          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Room 302</div>
              <div class="workload-val" style="color:var(--amber);">In Use</div>
            </div>
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">Lecture · Cap. 45</div>
            <div class="room-usage-bar"><div class="room-usage-fill" style="width:100%;background:var(--amber);"></div></div>
            <div style="font-size:11px;color:var(--amber);margin-top:3px;">CC 401 — Tue/Thu 8:30</div>
          </div>

          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Room 205</div>
              <div class="workload-val" style="color:var(--green);">Free</div>
            </div>
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">Lecture · Cap. 40</div>
            <div class="room-usage-bar"><div class="room-usage-fill" style="width:0%;background:var(--green);"></div></div>
            <div style="font-size:11px;color:var(--green);margin-top:3px;">No subject assigned</div>
          </div>

          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Lab 1</div>
              <div class="workload-val" style="color:var(--amber);">In Use</div>
            </div>
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">Laboratory · Cap. 40</div>
            <div class="room-usage-bar"><div class="room-usage-fill" style="width:100%;background:var(--amber);"></div></div>
            <div style="font-size:11px;color:var(--amber);margin-top:3px;">IT 401 — Fri 10:00</div>
          </div>

          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Lab 2</div>
              <div class="workload-val" style="color:var(--green);">Free</div>
            </div>
            <div style="font-size:11px;color:var(--text3);margin-bottom:4px;">Laboratory · Cap. 40</div>
            <div class="room-usage-bar"><div class="room-usage-fill" style="width:0%;background:var(--green);"></div></div>
            <div style="font-size:11px;color:var(--green);margin-top:3px;">No subject assigned</div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- MODAL: ADD ROOM -->
<div class="modal-overlay" id="modal-add-room">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Add New Room</div>
      <button class="modal-close" onclick="closeModal('modal-add-room')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Room Name / Number</label>
          <input class="field-input" type="text" placeholder="e.g. Room 303">
        </div>
        <div class="field-group">
          <label class="field-label">Type</label>
          <select class="field-select">
            <option>Lecture</option>
            <option>Laboratory</option>
            <option>Seminar Room</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Capacity</label>
          <input class="field-input" type="number" placeholder="e.g. 40" min="1">
        </div>
        <div class="field-group">
          <label class="field-label">Building / Floor</label>
          <input class="field-input" type="text" placeholder="e.g. ICT Building, 3rd Floor">
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-add-room')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="saveRoom()">Save Room</button>
    </div>
  </div>
</div>

<!-- MODAL: EDIT / ASSIGN ROOM -->
<div class="modal-overlay" id="modal-edit-room">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Edit Room Assignment</div>
      <button class="modal-close" onclick="closeModal('modal-edit-room')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Room</label>
          <select class="field-select">
            <option>Room 301</option>
            <option>Room 302</option>
            <option>Room 205</option>
            <option>Lab 1</option>
            <option>Lab 2</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Subject</label>
          <select class="field-select">
            <option>CC 313 — Web Systems</option>
            <option>CC 401 — Capstone 1</option>
            <option>IT 302 — Networking</option>
            <option>IT 401 — Info Assurance</option>
            <option>GE 101 — Purposive Comm.</option>
            <option>CC 501 — System Integration</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Day(s)</label>
          <select class="field-select">
            <option>Monday / Wednesday</option>
            <option>Tuesday / Thursday</option>
            <option>Monday / Wednesday / Friday</option>
            <option>Tuesday / Thursday / Saturday</option>
            <option>Friday</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Time Slot</label>
          <select class="field-select">
            <option>7:00 – 8:30 AM</option>
            <option>8:30 – 10:00 AM</option>
            <option>10:00 – 11:30 AM</option>
            <option>1:00 – 2:30 PM</option>
            <option>2:30 – 4:00 PM</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-room')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="saveRoomAssignment()">Save Assignment</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── NOTIFICATIONS ─────────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)',   text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)',  text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
    <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div>
        <div class="notif-drop-text">${n.text}</div>
        <div class="notif-drop-time">${n.time}</div>
      </div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  const dd = document.getElementById('notif-dropdown');
  if (dd) dd.classList.toggle('open', notifOpen);
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.classList.remove('open');
  }
});
function markRead(el) { el.classList.remove('unread'); updateNotifCount(); }
function markAllRead() {
  document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('.notif-drop-item.unread').length;
  const dot = document.getElementById('notif-dot');
  if (dot) dot.style.display = unread > 0 ? 'block' : 'none';
}
renderNotifList();

// ── MODALS ─────────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

// ── SAVE HANDLERS ──────────────────────────────────────────────────────────────
function saveRoom() {
  closeModal('modal-add-room');
  showToast('Room added successfully!');
}
function saveRoomAssignment() {
  closeModal('modal-edit-room');
  showToast('Room assignment saved!');
}
</script>
</body>
</html>