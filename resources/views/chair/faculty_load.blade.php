<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Load Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/faculty_load.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Faculty Load Management</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">2</span>
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

    <div id="page-faculty" class="page active">

      <!-- Page header -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-size:20px;font-weight:800;color:var(--text);">Faculty Load Management</div>
          <div style="font-size:13px;color:var(--text3);">BSIS Department · Max 30 units/week</div>
        </div>
        <button class="topbar-btn btn-primary" onclick="openModal('modal-assign')">+ Assign Subject</button>
      </div>

      <!-- Stat cards -->
      <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Total Faculty</div><div class="stat-value">4</div><div class="stat-sub">BSIS Department</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">OK Load</div><div class="stat-value">2</div><div class="stat-sub">Within safe range</div></div>
        <div class="stat-card" style="--accent:#d97706"><div class="stat-label">Near Max</div><div class="stat-value">1</div><div class="stat-sub">3u or less remaining</div></div>
        <div class="stat-card" style="--accent:#dc2626"><div class="stat-label">Overloaded</div><div class="stat-value">0</div><div class="stat-sub">Exceeding 30u limit</div></div>
      </div>

      <div style="display:grid;grid-template-columns:1fr 280px;gap:20px;align-items:start;">

        <!-- Faculty table -->
        <div class="card">
          <div class="card-header">
            <div><div class="card-title">Faculty Load Overview</div><div class="card-sub">Click "Assign" to add subjects to a faculty member</div></div>
          </div>
          <div class="table-wrap"><table>
            <thead>
              <tr><th>Faculty</th><th>Employment</th><th>Subjects</th><th>Total Units</th><th>Units Left</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Jerome Bautista</b></td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text3);">CC 313, CC 401, IT 302</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--green);">24u</span></td>
                <td><span class="badge badge-green">6u left</span></td>
                <td><span class="badge badge-green">OK</span></td>
                <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-assign')">Assign More</button></td>
              </tr>
              <tr>
                <td><b>Felicitas Lagman</b></td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text3);">IT 302, IT 401, CC 202</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--amber);">27u</span></td>
                <td><span class="badge badge-amber">3u left</span></td>
                <td><span class="badge badge-amber">Near Max</span></td>
                <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-assign')">Assign More</button></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text3);">GE 102, IT 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--blue);">18u</span></td>
                <td><span class="badge badge-blue">12u left</span></td>
                <td><span class="badge badge-blue">Available</span></td>
                <td><button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-assign')">Assign</button></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td>
                <td>Part-time</td>
                <td style="font-size:12px;color:var(--text3);">IT 401, GE 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--teal);">18u</span></td>
                <td><span class="badge badge-grey">—</span></td>
                <td><span class="badge" style="background:#cffafe;color:#164e63;">Part-time</span></td>
                <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;opacity:.5;cursor:not-allowed;">Full</button></td>
              </tr>
            </tbody>
          </table></div>
        </div>

        <!-- Workload summary sidebar card -->
        <div class="card">
          <div class="card-header"><div class="card-title">Load Summary</div></div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Jerome Bautista</div>
              <div class="workload-val" style="color:var(--green);">24/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:80%;background:var(--green);"></div></div>
            <div style="font-size:11px;color:var(--green);margin-top:3px;">6 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Felicitas Lagman</div>
              <div class="workload-val" style="color:var(--amber);">27/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:90%;background:var(--amber);"></div></div>
            <div style="font-size:11px;color:var(--amber);margin-top:3px;">3 units remaining — near maximum</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Maria Santos</div>
              <div class="workload-val" style="color:var(--blue);">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--blue);"></div></div>
            <div style="font-size:11px;color:var(--blue);margin-top:3px;">12 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Ana Reyes (Part-time)</div>
              <div class="workload-val" style="color:var(--teal);">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--teal);"></div></div>
            <div style="font-size:11px;color:var(--text3);margin-top:3px;">Part-time — verify additional load with Dean</div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<!-- MODAL: ASSIGN SUBJECT -->
<div class="modal-overlay" id="modal-assign">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Assign Subject to Faculty</div>
      <button class="modal-close" onclick="closeModal('modal-assign')">×</button>
    </div>
    <div class="modal-body">
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Subject</label>
          <select class="field-select" id="assign-subject" onchange="updateUnitAlert()">
            <option value="3">CC 313 — Web Systems (3u)</option>
            <option value="3">CC 401 — Capstone 1 (3u)</option>
            <option value="3">IT 302 — Networking (3u)</option>
            <option value="3">CC 501 — System Integration (3u)</option>
            <option value="3">GE 102 — Mathematics (3u)</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Faculty Member</label>
          <select class="field-select" id="assign-faculty" onchange="updateUnitAlert()">
            <option value="bautista">Jerome Bautista</option>
            <option value="lagman">Felicitas Lagman</option>
            <option value="santos">Maria Santos</option>
            <option value="reyes">Ana Reyes (Part-time)</option>
          </select>
        </div>
      </div>
      <!-- Live unit alert -->
      <div id="unit-alert-box" class="unit-alert-box ua-ok">
        <span id="unit-alert-icon" style="flex-shrink:0;font-size:13px;font-weight:700;">OK</span>
        <span id="unit-alert-text">Jerome Bautista — currently 24u/30u. Adding 3u → 27u (3u remaining).</span>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Day(s)</label>
          <select class="field-select" id="assign-days" onchange="checkTimeConflict()">
            <option>Monday / Wednesday</option>
            <option>Tuesday / Thursday</option>
            <option>Monday / Wednesday / Friday</option>
            <option>Tuesday / Thursday / Saturday</option>
            <option>Friday</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Time Slot</label>
          <select class="field-select" id="assign-time" onchange="checkTimeConflict()">
            <option>7:00 – 8:30 AM</option>
            <option>8:30 – 10:00 AM</option>
            <option>10:00 – 11:30 AM</option>
            <option>1:00 – 2:30 PM</option>
            <option>2:30 – 4:00 PM</option>
          </select>
        </div>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Room</label>
          <select class="field-select">
            <option>Room 301</option><option>Room 302</option><option>Room 205</option><option>Lab 1</option><option>Lab 2</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">Section</label>
          <select class="field-select">
            <option>BSIS 1-A</option><option>BSIS 2-A</option><option>BSIS 3-A</option><option>BSIS 4-A</option>
          </select>
        </div>
      </div>
      <div id="time-conflict-warning" class="conflict-alert" style="display:none;margin-top:0;">
        <div class="conflict-alert-text"><strong>Schedule Conflict!</strong> This faculty is already assigned at this time slot. Choose a different day or time.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-assign')">Cancel</button>
      <button class="topbar-btn btn-primary" id="assign-save-btn" onclick="saveAssignment()">Save Assignment</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── FACULTY DATA ──────────────────────────────────────────────────────────────
const FACULTY = {
  bautista:{ name:'Jerome Bautista',  load:24, max:30, parttime:false,
    conflicts:[{days:'Monday / Wednesday',time:'7:00 – 8:30 AM'},{days:'Tuesday / Thursday',time:'8:30 – 10:00 AM'}] },
  lagman:  { name:'Felicitas Lagman', load:27, max:30, parttime:false,
    conflicts:[{days:'Monday / Wednesday',time:'10:00 – 11:30 AM'},{days:'Friday',time:'7:00 – 8:30 AM'}] },
  santos:  { name:'Maria Santos',     load:18, max:30, parttime:false,
    conflicts:[{days:'Tuesday / Thursday',time:'7:00 – 8:30 AM'},{days:'Monday / Wednesday',time:'8:30 – 10:00 AM'}] },
  reyes:   { name:'Ana Reyes',        load:18, max:30, parttime:true,
    conflicts:[{days:'Tuesday / Thursday',time:'1:00 – 2:30 PM'},{days:'Tuesday / Thursday',time:'2:30 – 4:00 PM'}] },
};

// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map((n) => `
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
function markRead(el) { el.classList.remove('unread'); updateNotifCount(); }
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

// ── MODALS ─────────────────────────────────────────────────────────────────────
function openModal(id) {
  document.getElementById(id).classList.add('open');
  if (id === 'modal-assign') { setTimeout(updateUnitAlert, 10); }
}
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

// ── LIVE UNIT ALERT ────────────────────────────────────────────────────────────
function updateUnitAlert() {
  const key = document.getElementById('assign-faculty').value;
  const subjectSel = document.getElementById('assign-subject');
  const addUnits = parseInt(subjectSel.options[subjectSel.selectedIndex].value) || 3;
  const f = FACULTY[key];
  if (!f) return;
  const newLoad   = f.load + addUnits;
  const remaining = f.max - newLoad;
  const box  = document.getElementById('unit-alert-box');
  const icon = document.getElementById('unit-alert-icon');
  const text = document.getElementById('unit-alert-text');
  const btn  = document.getElementById('assign-save-btn');

  if (f.parttime) {
    box.className = 'unit-alert-box ua-warn'; icon.textContent = '!';
    text.textContent = `${f.name} is Part-time — currently ${f.load}u/30u. Adding ${addUnits}u → ${newLoad}u. Verify with Dean before assigning additional load.`;
    btn.disabled = false; btn.className = 'topbar-btn btn-primary';
  } else if (newLoad > f.max) {
    box.className = 'unit-alert-box ua-over'; icon.textContent = 'X';
    text.textContent = `OVERLOAD: ${f.name} is at ${f.load}u/30u. Adding ${addUnits}u → ${newLoad}u — exceeds maximum by ${newLoad - f.max} unit(s). Cannot assign!`;
    btn.disabled = true; btn.className = 'topbar-btn btn-secondary';
  } else if (remaining <= 3) {
    box.className = 'unit-alert-box ua-warn'; icon.textContent = '!';
    text.textContent = `${f.name} — currently ${f.load}u/30u. Adding ${addUnits}u → ${newLoad}u. Only ${remaining} unit(s) left — near maximum!`;
    btn.disabled = false; btn.className = 'topbar-btn btn-primary';
  } else {
    box.className = 'unit-alert-box ua-ok'; icon.textContent = 'OK';
    text.textContent = `${f.name} — currently ${f.load}u/30u. Adding ${addUnits}u → ${newLoad}u. ${remaining} units remaining after this assignment.`;
    btn.disabled = false; btn.className = 'topbar-btn btn-primary';
  }
  checkTimeConflict();
}

// ── TIME CONFLICT CHECK ────────────────────────────────────────────────────────
function checkTimeConflict() {
  const key  = document.getElementById('assign-faculty').value;
  const days = document.getElementById('assign-days').value;
  const time = document.getElementById('assign-time').value;
  const f = FACULTY[key];
  if (!f) return;
  const clash = f.conflicts.some(c => c.days === days && c.time === time);
  document.getElementById('time-conflict-warning').style.display = clash ? 'flex' : 'none';
}

// ── SAVE ASSIGNMENT ────────────────────────────────────────────────────────────
function saveAssignment() {
  const key = document.getElementById('assign-faculty').value;
  const f = FACULTY[key];
  const subjectSel = document.getElementById('assign-subject');
  const addUnits = parseInt(subjectSel.options[subjectSel.selectedIndex].value) || 3;
  if (f.load + addUnits > f.max) { showToast('Cannot assign — would exceed 30-unit maximum!'); return; }
  const days = document.getElementById('assign-days').value;
  const time = document.getElementById('assign-time').value;
  const clash = f.conflicts.some(c => c.days === days && c.time === time);
  if (clash) { showToast('Cannot assign — schedule conflict at this time slot!'); return; }
  FACULTY[key].load += addUnits;
  FACULTY[key].conflicts.push({ days, time });
  closeModal('modal-assign');
  showToast(`Subject assigned to ${f.name}! New load: ${FACULTY[key].load}u/30u.`);
}
</script>
</body>
</html>