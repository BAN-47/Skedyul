<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Export Reports</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/export_reports.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Export Reports</div>
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

    <div id="page-reports" class="page active">

      <!-- Page header -->
      <div style="margin-bottom:20px;">
        <div style="font-size:20px;font-weight:800;color:var(--text);">Export Reports</div>
        <div style="font-size:13px;color:var(--text3);">BSIS Department · AY 2025–2026 · 1st Semester</div>
      </div>

      <!-- Report cards -->
      <div class="three-col" style="margin-bottom:24px;">

        <div class="card" style="cursor:pointer;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(37,99,235,.15)'" onmouseout="this.style.boxShadow=''">
          <div style="width:40px;height:40px;border-radius:10px;background:var(--blue-pale);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px;">📅</div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">Faculty Schedule</div>
          <div style="font-size:12px;color:var(--text3);margin-bottom:16px;">Individual timetables per faculty member</div>
          <div style="display:flex;gap:8px;">
            <button class="topbar-btn btn-primary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Faculty Schedule','PDF')">PDF</button>
            <button class="topbar-btn btn-secondary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Faculty Schedule','Excel')">Excel</button>
          </div>
        </div>

        <div class="card" style="cursor:pointer;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(37,99,235,.15)'" onmouseout="this.style.boxShadow=''">
          <div style="width:40px;height:40px;border-radius:10px;background:var(--green-light);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px;">📋</div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">Section Master List</div>
          <div style="font-size:12px;color:var(--text3);margin-bottom:16px;">Complete schedule per section/class</div>
          <div style="display:flex;gap:8px;">
            <button class="topbar-btn btn-primary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Section Master List','PDF')">PDF</button>
            <button class="topbar-btn btn-secondary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Section Master List','Excel')">Excel</button>
          </div>
        </div>

        <div class="card" style="cursor:pointer;transition:box-shadow .2s;" onmouseover="this.style.boxShadow='0 4px 20px rgba(37,99,235,.15)'" onmouseout="this.style.boxShadow=''">
          <div style="width:40px;height:40px;border-radius:10px;background:var(--amber-light);display:flex;align-items:center;justify-content:center;font-size:20px;margin-bottom:12px;">📊</div>
          <div style="font-size:14px;font-weight:700;color:var(--text);margin-bottom:4px;">Workload Summary</div>
          <div style="font-size:12px;color:var(--text3);margin-bottom:16px;">Units per faculty with load breakdown</div>
          <div style="display:flex;gap:8px;">
            <button class="topbar-btn btn-primary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Workload Summary','PDF')">PDF</button>
            <button class="topbar-btn btn-secondary" style="font-size:12px;padding:6px 14px;flex:1;" onclick="exportReport('Workload Summary','Excel')">Excel</button>
          </div>
        </div>

      </div>

      <!-- Recent exports -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Recent Exports</div>
            <div class="card-sub">Last generated reports this semester</div>
          </div>
        </div>
        <div id="recent-exports-list">
          <div class="check-item">
            <div class="check-dot" style="background:var(--blue);"></div>
            <div class="check-label">Faculty Schedule — PDF</div>
            <div class="check-status" style="color:var(--text3);font-weight:400;font-size:12px;">Jun 20, 2026 · 10:34 AM</div>
          </div>
          <div class="check-item">
            <div class="check-dot" style="background:var(--green);"></div>
            <div class="check-label">Section Master List — Excel</div>
            <div class="check-status" style="color:var(--text3);font-weight:400;font-size:12px;">Jun 18, 2026 · 3:12 PM</div>
          </div>
          <div class="check-item">
            <div class="check-dot" style="background:var(--amber);"></div>
            <div class="check-label">Workload Summary — PDF</div>
            <div class="check-status" style="color:var(--text3);font-weight:400;font-size:12px;">Jun 15, 2026 · 9:00 AM</div>
          </div>
        </div>
      </div>

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

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

// ── EXPORT REPORT ──────────────────────────────────────────────────────────────
const dotColors = { 'Faculty Schedule':'var(--blue)', 'Section Master List':'var(--green)', 'Workload Summary':'var(--amber)' };

function exportReport(name, format) {
  showToast(name + ' — ' + format + ' generated successfully!');

  // Add to recent exports list
  const list = document.getElementById('recent-exports-list');
  const now = new Date();
  const time = now.toLocaleString('en-US', { month:'short', day:'numeric', year:'numeric', hour:'numeric', minute:'2-digit', hour12:true });
  const item = document.createElement('div');
  item.className = 'check-item';
  item.style.animation = 'fadeIn .3s ease';
  item.innerHTML = `
    <div class="check-dot" style="background:${dotColors[name]||'var(--blue)'};"></div>
    <div class="check-label">${name} — ${format}</div>
    <div class="check-status" style="color:var(--text3);font-weight:400;font-size:12px;">${time}</div>`;
  list.insertBefore(item, list.firstChild);
}
</script>
</body>
</html>