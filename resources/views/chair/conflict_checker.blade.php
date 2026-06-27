<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Conflict Checker</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/conflict_checker.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Conflict Checker</div>
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

    <div id="page-conflicts" class="page active">

      <!-- Page header -->
      <div style="margin-bottom:20px;">
        <div style="font-size:20px;font-weight:800;color:var(--text);">Conflict Checker</div>
        <div style="font-size:13px;color:var(--text3);">All conflicts must be resolved before Dean submission</div>
      </div>

      <!-- Conflict banner (shown when conflicts exist) -->
      <div id="conflict-main-alert" class="conflict-alert">
        <div class="conflict-alert-text">
          <strong>1 Active Conflict — Maria Santos</strong>
          GE 102 (BSIS 1-A) and IT 101 (BSIS 1-B) both scheduled on Tuesday 7:00–8:30 AM. Schedule cannot be published until this is resolved.
        </div>
        <button class="topbar-btn btn-danger" style="margin-left:auto;padding:6px 12px;font-size:12px;flex-shrink:0;" onclick="openModal('modal-resolve')">Resolve Now</button>
      </div>

      <!-- Clean banner (shown after all conflicts resolved) -->
      <div id="conflict-clear-alert" class="success-alert" style="display:none;">
        <div class="success-alert-text"><strong>All conflicts resolved!</strong> Schedule is clean and ready for Dean submission.</div>
      </div>

      <!-- Active conflicts table -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <div class="card-title">Active Conflicts</div>
          <span id="conflict-count-badge" class="badge badge-red">2 conflicts</span>
        </div>
        <div id="conflict-row-wrap">
          <div class="table-wrap">
            <table>
              <thead>
                <tr><th>Faculty</th><th>Subject A</th><th>Subject B</th><th>Day</th><th>Time</th><th>Type</th><th>Action</th></tr>
              </thead>
              <tbody>
                <tr id="conflict-row-1">
                  <td><b>Maria Santos</b></td>
                  <td>GE 102 · BSIS 1-A</td>
                  <td>IT 101 · BSIS 1-B</td>
                  <td>Tuesday</td>
                  <td style="font-family:var(--mono);font-size:12px;">7:00–8:30 AM</td>
                  <td><span class="badge badge-red">Faculty Overlap</span></td>
                  <td><button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-resolve')">Resolve</button></td>
                </tr>
                <tr id="conflict-row-2">
                  <td><b>Jerome Bautista</b></td>
                  <td>CC 313 · BSIS 2-A</td>
                  <td>CC 401 · BSIS 3-A</td>
                  <td>Wednesday</td>
                  <td style="font-family:var(--mono);font-size:12px;">10:00–11:30 AM</td>
                  <td><span class="badge badge-red">Room Conflict</span></td>
                  <td><button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-resolve')">Resolve</button></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div id="no-conflicts-msg" style="display:none;text-align:center;padding:28px;color:var(--green);font-weight:700;font-size:15px;">
          ✅ No active conflicts
        </div>
      </div>

      <!-- Validation checklist -->
      <div class="card">
        <div class="card-header">
          <div class="card-title">Validation Checklist</div>
          <div class="card-sub">All items must pass before schedule can be submitted</div>
        </div>
        <div class="check-item">
          <div class="check-dot" id="vd-overlap" style="background:var(--red);"></div>
          <div class="check-label">Faculty double-booking</div>
          <div class="check-status" id="vs-overlap" style="color:var(--red);">1 found</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">Room double-booking</div>
          <div class="check-status" style="color:var(--green);">Clear</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">Faculty overload (&gt;30 units)</div>
          <div class="check-status" style="color:var(--green);">Clear</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--amber);"></div>
          <div class="check-label">Unassigned subjects</div>
          <div class="check-status" style="color:var(--amber);">1 remaining (CC 501)</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">Missing room assignments</div>
          <div class="check-status" style="color:var(--green);">Clear</div>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- RESOLVE CONFLICT MODAL -->
<div class="modal-overlay" id="modal-resolve">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Resolve Conflict — Maria Santos</div>
      <button class="modal-close" onclick="closeModal('modal-resolve')">×</button>
    </div>
    <div class="modal-body">
      <div class="conflict-alert" style="margin-bottom:16px;">
        <div class="conflict-alert-text">
          <strong>Conflict:</strong> GE 102 and IT 101 are both on Tuesday 7:00–8:30 AM. Move one subject to a free slot to resolve.
        </div>
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
            <option>Monday</option>
            <option>Wednesday</option>
            <option>Thursday</option>
            <option>Friday</option>
            <option>Saturday</option>
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

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
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

// ── RESOLVE CONFLICT ───────────────────────────────────────────────────────────
let conflictsRemaining = 2;

function resolveConflict() {
  closeModal('modal-resolve');
  conflictsRemaining--;

  if (conflictsRemaining > 0) {
    // Still has conflicts — hide row 1, update badge
    const row1 = document.getElementById('conflict-row-1');
    if (row1) {
      row1.style.transition = 'opacity 0.3s';
      row1.style.opacity = '0';
      setTimeout(() => row1.remove(), 300);
    }
    const badge = document.getElementById('conflict-count-badge');
    if (badge) badge.textContent = conflictsRemaining + ' conflict' + (conflictsRemaining > 1 ? 's' : '');
    // Update validation checklist
    document.getElementById('vd-overlap').style.background = 'var(--amber)';
    document.getElementById('vs-overlap').style.color = 'var(--amber)';
    document.getElementById('vs-overlap').textContent = conflictsRemaining + ' remaining';
    showToast('Conflict resolved! ' + conflictsRemaining + ' conflict remaining.');
  } else {
    // All resolved
    document.getElementById('conflict-main-alert').style.display = 'none';
    document.getElementById('conflict-clear-alert').style.display = 'flex';
    document.getElementById('conflict-row-wrap').style.display = 'none';
    document.getElementById('no-conflicts-msg').style.display = 'block';

    const badge = document.getElementById('conflict-count-badge');
    if (badge) { badge.textContent = 'All clear'; badge.className = 'badge badge-green'; }

    // Update validation checklist
    document.getElementById('vd-overlap').style.background = 'var(--green)';
    document.getElementById('vs-overlap').style.color = 'var(--green)';
    document.getElementById('vs-overlap').textContent = 'Clear';

    showToast('All conflicts resolved! Schedule is ready to submit to Dean.');
  }
}
</script>
</body>
</html>