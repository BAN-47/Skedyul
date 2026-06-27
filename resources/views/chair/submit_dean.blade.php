<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Submit to Dean</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/submit_dean.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Submit to Dean</div>
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

    <div id="page-submit" class="page active">

      <!-- Page header -->
      <div style="margin-bottom:20px;">
        <div style="font-size:20px;font-weight:800;color:var(--text);">Submit Schedule to Dean</div>
        <div style="font-size:13px;color:var(--text3);">Final review before sending to Dean Villaceran</div>
      </div>

      <!-- Blocked banner (shown when conflicts exist) -->
      <div id="submit-blocked" class="conflict-alert">
        <div class="conflict-alert-text">
          <strong>Submission Blocked</strong> — 1 unresolved conflict exists. The schedule cannot be published or sent to the Dean until all conflicts are fixed.
        </div>
        <a href="{{ route('chair.conflict_checker') }}" class="topbar-btn btn-danger" style="margin-left:auto;padding:6px 12px;font-size:12px;flex-shrink:0;text-decoration:none;">Go Fix</a>
      </div>

      <!-- Ready banner (shown when all clear) -->
      <div id="submit-ready" class="success-alert" style="display:none;">
        <div class="success-alert-text"><strong>Ready to submit!</strong> All conflicts resolved. Click Submit below to send to Dean Villaceran.</div>
      </div>

      <!-- Submission checklist -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <div>
            <div class="card-title">Submission Checklist</div>
            <div class="card-sub">All items must pass before submission</div>
          </div>
          <span id="submit-overall-badge" class="badge badge-red">Not Ready</span>
        </div>

        <div class="check-item">
          <div class="check-dot" style="background:var(--amber);"></div>
          <div class="check-label">All subjects assigned to faculty</div>
          <div class="check-status" style="color:var(--amber);">1 unassigned (CC 501)</div>
        </div>
        <div class="check-item">
          <div class="check-dot" id="chk-conflict-dot" style="background:var(--red);"></div>
          <div class="check-label">No scheduling conflicts</div>
          <div class="check-status" id="chk-conflict-val" style="color:var(--red);">1 conflict — cannot publish</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">All faculty within 30-unit load</div>
          <div class="check-status" style="color:var(--green);">Pass</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">All sections have complete subjects</div>
          <div class="check-status" style="color:var(--green);">Pass</div>
        </div>
        <div class="check-item">
          <div class="check-dot" style="background:var(--green);"></div>
          <div class="check-label">All rooms assigned</div>
          <div class="check-status" style="color:var(--green);">Pass</div>
        </div>

        <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;">
          <button class="topbar-btn btn-secondary" onclick="showToast('Draft PDF exported!')">Export Draft</button>
          <button class="topbar-btn btn-danger" id="btn-submit-dean" onclick="attemptSubmit()">Submit to Dean</button>
        </div>
      </div>

      <!-- Schedule summary card -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Schedule Summary</div>
            <div class="card-sub">BSIS Department · AY 2025–2026 · 1st Semester</div>
          </div>
          <span class="badge badge-blue">Draft</span>
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px;">
          <div style="background:var(--grey);border-radius:10px;padding:14px;">
            <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Faculty</div>
            <div style="font-size:22px;font-weight:800;color:var(--text);">4</div>
            <div style="font-size:11px;color:var(--text3);">assigned</div>
          </div>
          <div style="background:var(--grey);border-radius:10px;padding:14px;">
            <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Subjects</div>
            <div style="font-size:22px;font-weight:800;color:var(--text);">6</div>
            <div style="font-size:11px;color:var(--text3);">total (1 unassigned)</div>
          </div>
          <div style="background:var(--grey);border-radius:10px;padding:14px;">
            <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;margin-bottom:4px;">Sections</div>
            <div style="font-size:22px;font-weight:800;color:var(--text);">4</div>
            <div style="font-size:11px;color:var(--text3);">BSIS 1-A to 4-A</div>
          </div>
        </div>
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Faculty</th><th>Subjects</th><th>Total Load</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Jerome Bautista</b></td>
                <td style="font-size:12px;color:var(--text2);">CC 313, CC 401</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--green);">24u</span></td>
                <td><span class="badge badge-green">OK</span></td>
              </tr>
              <tr>
                <td><b>Felicitas Lagman</b></td>
                <td style="font-size:12px;color:var(--text2);">IT 302, CC 202</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--amber);">27u</span></td>
                <td><span class="badge badge-amber">Near Max</span></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td>
                <td style="font-size:12px;color:var(--text2);">GE 102, IT 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--blue);">18u</span></td>
                <td><span class="badge badge-red">Has Conflict</span></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td>
                <td style="font-size:12px;color:var(--text2);">IT 401, GE 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--teal);">18u</span></td>
                <td><span class="badge badge-grey">Part-time</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── STATE ─────────────────────────────────────────────────────────────────────
let hasConflict = true; // set to false once conflicts are resolved

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

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

// ── ATTEMPT SUBMIT ─────────────────────────────────────────────────────────────
function attemptSubmit() {
  if (hasConflict) {
    showToast('Cannot submit — fix the conflict first!');
    return;
  }
  const btn = document.getElementById('btn-submit-dean');
  btn.textContent = 'Submitted ✓';
  btn.disabled = true;
  btn.className = 'topbar-btn btn-secondary';
  const badge = document.getElementById('submit-overall-badge');
  if (badge) { badge.textContent = 'Submitted'; badge.className = 'badge badge-green'; }
  document.getElementById('submit-ready').innerHTML = '<div class="success-alert-text"><strong>Schedule submitted!</strong> Sent to Dean Villaceran. Awaiting approval.</div>';
  showToast('Schedule submitted to Dean Villaceran! Awaiting approval.');
}

// ── SIMULATE CONFLICT RESOLVED (for demo — wire to real state later) ──────────
// Call clearConflict() from console or a button to test the "ready" state
function clearConflict() {
  hasConflict = false;
  document.getElementById('submit-blocked').style.display = 'none';
  document.getElementById('submit-ready').style.display = 'flex';
  document.getElementById('chk-conflict-dot').style.background = 'var(--green)';
  document.getElementById('chk-conflict-val').style.color = 'var(--green)';
  document.getElementById('chk-conflict-val').textContent = 'Pass — no conflicts';
  const btn = document.getElementById('btn-submit-dean');
  btn.className = 'topbar-btn btn-primary';
  btn.textContent = 'Submit to Dean';
  const badge = document.getElementById('submit-overall-badge');
  if (badge) { badge.textContent = 'Ready'; badge.className = 'badge badge-green'; }
}
</script>
</body>
</html>