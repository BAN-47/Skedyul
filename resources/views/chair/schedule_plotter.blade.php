<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Schedule Plotter</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/schedule_plotter.css') }}">
</head>
<style>
  html, body { overflow: hidden; }

  .sidebar-nav {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .sidebar-nav::-webkit-scrollbar { display: none; }

  .page {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .page::-webkit-scrollbar { display: none; }

  .schedule-grid-wrap {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .schedule-grid-wrap::-webkit-scrollbar { display: none; }
</style>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Schedule Plotter</div>
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

    <div class="page-content" style="margin-left: 30px; margin-top: 30px; margin-right: 30px; display: block;animation: fadeIn .3s ease;">
      <!-- Page header row -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-size:20px;font-weight:800;color:var(--text);">Schedule Plotter</div>
          <div style="font-size:13px;color:var(--text3);">BSIS Department · Click any event to view details</div>
        </div>
        <div style="display:flex;gap:10px;">
          <select class="field-select" style="width:170px;" onchange="filterPlotter(this.value)">
            <option value="all">All Faculty</option>
            <option value="bautista">Jerome Bautista</option>
            <option value="santos">Maria Santos</option>
            <option value="reyes">Ana Reyes</option>
            <option value="lagman">Felicitas Lagman</option>
          </select>
          <button class="topbar-btn btn-primary" onclick="openModal('modal-assign')">+ Assign Subject</button>
        </div>
      </div>

      <!-- Conflict banner -->
      <div id="plotter-conflict-banner" class="conflict-alert">
        <div class="conflict-alert-text"><strong>2 Conflicts</strong> — Maria Santos: GE 102 &amp; IT 101 overlap Tue 7:00–8:30 AM · Jerome Bautista: Room conflict Wed 10:00–11:30 AM.</div>
        <button class="topbar-btn btn-danger" style="margin-left:auto;padding:5px 10px;font-size:11px;" onclick="openModal('modal-resolve')">Fix</button>
      </div>

      <!-- Clean banner (hidden until conflict resolved) -->
      <div id="plotter-clean-banner" class="success-alert" style="display:none;">
        <div class="success-alert-text"><strong>Schedule is conflict-free!</strong> Ready to submit to Dean.</div>
      </div>

      <!-- Schedule grid -->
      <div class="card" style="padding:0;overflow:hidden;">
        <div class="schedule-grid-wrap" style="padding:16px;">
          <div class="schedule-grid">
            <!-- Headers -->
            <div class="sg-header">Time</div>
            <div class="sg-header">Monday</div>
            <div class="sg-header">Tuesday</div>
            <div class="sg-header">Wednesday</div>
            <div class="sg-header">Thursday</div>
            <div class="sg-header">Friday</div>
            <div class="sg-header">Saturday</div>

            <!-- 7:00–8:30 -->
            <div class="sg-time">7:00–8:30</div>
            <div class="sg-cell"><div class="sg-event blue" onclick="openEventDetail('e1')">CC 313<br><span style="font-size:10px;opacity:.8">Bautista·R301</span></div></div>
            <div class="sg-cell" id="conflict-cell"><div class="sg-event red" onclick="openModal('modal-resolve')">GE 102/IT 101<br><span style="font-size:10px">Santos—CONFLICT</span></div></div>
            <div class="sg-cell"><div class="sg-event blue" onclick="openEventDetail('e1')">CC 313<br><span style="font-size:10px;opacity:.8">Bautista·R301</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event teal" onclick="openEventDetail('e5')">IT 302<br><span style="font-size:10px;opacity:.8">Lagman·Lab2</span></div></div>
            <div class="sg-cell"></div>

            <!-- 8:30–10:00 -->
            <div class="sg-time">8:30–10:00</div>
            <div class="sg-cell"><div class="sg-event green" onclick="openEventDetail('e2')">IT 101<br><span style="font-size:10px;opacity:.8">Santos·R205</span></div></div>
            <div class="sg-cell"><div class="sg-event amber" onclick="openEventDetail('e3')">CC 401<br><span style="font-size:10px;opacity:.8">Bautista·R302</span></div></div>
            <div class="sg-cell"><div class="sg-event green" onclick="openEventDetail('e2')">IT 101<br><span style="font-size:10px;opacity:.8">Santos·R205</span></div></div>
            <div class="sg-cell"><div class="sg-event amber" onclick="openEventDetail('e3')">CC 401<br><span style="font-size:10px;opacity:.8">Bautista·R302</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"></div>

            <!-- 10:00–11:30 -->
            <div class="sg-time">10:00–11:30</div>
            <div class="sg-cell"><div class="sg-event purple" onclick="openEventDetail('e4')">CC 202<br><span style="font-size:10px;opacity:.8">Lagman·R206</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event purple" onclick="openEventDetail('e4')">CC 202<br><span style="font-size:10px;opacity:.8">Lagman·R206</span></div></div>
            <div class="sg-cell"><div class="sg-event teal" onclick="openEventDetail('e5')">IT 302<br><span style="font-size:10px;opacity:.8">Lagman·Lab2</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"></div>

            <!-- 1:00–2:30 -->
            <div class="sg-time">1:00–2:30</div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event blue" onclick="openEventDetail('e6')">IT 401<br><span style="font-size:10px;opacity:.8">Reyes·R207</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event blue" onclick="openEventDetail('e6')">IT 401<br><span style="font-size:10px;opacity:.8">Reyes·R207</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"></div>

            <!-- 2:30–4:00 -->
            <div class="sg-time">2:30–4:00</div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event green" onclick="openEventDetail('e7')">GE 101<br><span style="font-size:10px;opacity:.8">Reyes·R101</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"><div class="sg-event green" onclick="openEventDetail('e7')">GE 101<br><span style="font-size:10px;opacity:.8">Reyes·R101</span></div></div>
            <div class="sg-cell"></div>
            <div class="sg-cell"></div>
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

<!-- MODAL: RESOLVE CONFLICT -->
<div class="modal-overlay" id="modal-resolve">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Resolve Conflict — Maria Santos</div>
      <button class="modal-close" onclick="closeModal('modal-resolve')">×</button>
    </div>
    <div class="modal-body">
      <div class="conflict-alert" style="margin-bottom:16px;">
        <div class="conflict-alert-text"><strong>Conflict:</strong> GE 102 and IT 101 are both on Tuesday 7:00–8:30 AM. Move one subject to a free slot to resolve.</div>
      </div>
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Subject to Move</label>
        <select class="field-select">
          <option>GE 102 — BSIS 1-A (currently Tue 7:00 AM)</option>
          <option>IT 101 — BSIS 1-B (currently Tue 7:00 AM)</option>
        </select>
      </div>
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">New Day</label>
          <select class="field-select">
            <option>Monday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option>
          </select>
        </div>
        <div class="field-group">
          <label class="field-label">New Time Slot</label>
          <select class="field-select">
            <option>8:30 – 10:00 AM — Free</option>
            <option>10:00 – 11:30 AM — Free</option>
            <option>1:00 – 2:30 PM — Free</option>
            <option>2:30 – 4:00 PM — Free</option>
          </select>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-resolve')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="resolveConflict()">Apply Fix &amp; Clear Conflict</button>
    </div>
  </div>
</div>

<!-- MODAL: EVENT DETAIL -->
<div class="modal-overlay" id="modal-event-detail">
  <div class="modal" style="width:420px;">
    <div class="modal-header">
      <div class="modal-title">Schedule Entry</div>
      <button class="modal-close" onclick="closeModal('modal-event-detail')">×</button>
    </div>
    <div class="modal-body">
      <div style="background:linear-gradient(135deg,var(--navy),#1e3a8a);border-radius:12px;padding:16px;margin-bottom:16px;">
        <div style="font-size:18px;font-weight:800;color:#fff;" id="ed-subject">—</div>
        <div style="font-size:12px;color:rgba(255,255,255,.5);margin-top:4px;" id="ed-section">—</div>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div style="background:var(--grey);border-radius:8px;padding:12px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:3px;">Faculty</div><div style="font-size:13px;font-weight:700;" id="ed-faculty">—</div></div>
        <div style="background:var(--grey);border-radius:8px;padding:12px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:3px;">Room</div><div style="font-size:13px;font-weight:700;" id="ed-room">—</div></div>
        <div style="background:var(--grey);border-radius:8px;padding:12px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:3px;">Day</div><div style="font-size:13px;font-weight:700;" id="ed-day">—</div></div>
        <div style="background:var(--grey);border-radius:8px;padding:12px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:3px;">Time</div><div style="font-size:13px;font-weight:700;" id="ed-time">—</div></div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-event-detail')">Close</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-event-detail');openModal('modal-assign')">Edit Assignment</button>
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

const EVENTS = {
  e1:{ subject:'CC 313 — Web Systems',     section:'BSIS 3-A · 3 units', faculty:'Jerome Bautista', room:'Room 301', day:'Mon/Wed', time:'7:00–8:30 AM' },
  e2:{ subject:'IT 101 — Intro to IT',     section:'BSIS 1-A · 3 units', faculty:'Maria Santos',    room:'Room 205', day:'Mon/Wed', time:'8:30–10:00 AM' },
  e3:{ subject:'CC 401 — Capstone 1',      section:'BSIS 4-A · 3 units', faculty:'Jerome Bautista', room:'Room 302', day:'Tue/Thu', time:'8:30–10:00 AM' },
  e4:{ subject:'CC 202 — Data Structures', section:'BSIS 2-A · 3 units', faculty:'F. Lagman',       room:'Room 206', day:'Mon/Wed', time:'10:00–11:30 AM' },
  e5:{ subject:'IT 302 — Networking',      section:'BSIS 3-B · 3 units', faculty:'F. Lagman',       room:'Lab 2',    day:'Fri/Thu', time:'7:00–8:30 AM' },
  e6:{ subject:'IT 401 — Info Assurance',  section:'BSIS 4-A · 3 units', faculty:'Ana Reyes',       room:'Room 207', day:'Tue/Thu', time:'1:00–2:30 PM' },
  e7:{ subject:'GE 101 — Purpose. Comm',   section:'BSIS 1-A · 3 units', faculty:'Ana Reyes',       room:'Room 101', day:'Tue/Thu', time:'2:30–4:00 PM' },
};

// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — Maria Santos: GE 102 &amp; IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
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
  const newLoad = f.load + addUnits;
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
  showToast(`Subject assigned to ${f.name}! New load: ${FACULTY[key].load}u/30u (${30 - FACULTY[key].load}u remaining).`);
}

// ── RESOLVE CONFLICT ───────────────────────────────────────────────────────────
function resolveConflict() {
  closeModal('modal-resolve');
  document.getElementById('plotter-conflict-banner').style.display = 'none';
  document.getElementById('plotter-clean-banner').style.display = 'flex';
  const cell = document.getElementById('conflict-cell');
  if (cell) {
    cell.innerHTML = '<div class="sg-event green" onclick="openEventDetail(\'e2\')">GE 102<br><span style="font-size:10px;opacity:.8">Santos·R205</span></div>';
  }
  showToast('Conflict resolved! Schedule is now clean and ready to submit.');
}

// ── PLOTTER FILTER ─────────────────────────────────────────────────────────────
function filterPlotter(val) {
  document.querySelectorAll('.sg-event').forEach(ev => {
    if (val === 'all') { ev.style.opacity = '1'; return; }
    const txt = ev.textContent.toLowerCase();
    const frags = { bautista:'bautista', santos:'santos', reyes:'reyes', lagman:'lagman' };
    ev.style.opacity = txt.includes(frags[val] || val) ? '1' : '0.15';
  });
}

// ── EVENT DETAIL ───────────────────────────────────────────────────────────────
function openEventDetail(id) {
  const ev = EVENTS[id]; if (!ev) return;
  document.getElementById('ed-subject').textContent = ev.subject;
  document.getElementById('ed-section').textContent = ev.section;
  document.getElementById('ed-faculty').textContent = ev.faculty;
  document.getElementById('ed-room').textContent    = ev.room;
  document.getElementById('ed-day').textContent     = ev.day;
  document.getElementById('ed-time').textContent    = ev.time;
  openModal('modal-event-detail');
}
</script>
</body>
</html>