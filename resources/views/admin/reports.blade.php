<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Reports & Export</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/reports.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.admin_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Reports & Export</div>
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

    <div id="page-reports" class="page active">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Schedule Reports</div>
          <div style="display:flex;gap:8px;">
            <button class="topbar-btn btn-secondary" onclick="showToast('Excel file exported successfully!')">⬇ Export Excel</button>
            <button class="topbar-btn btn-primary" onclick="showToast('PDF exported successfully!')">⬇ Export PDF</button>
          </div>
        </div>
        <div class="tab-bar" style="margin-bottom:16px;">
          <button class="tab-btn active">By Section</button>
          <button class="tab-btn">By Teacher</button>
          <button class="tab-btn">By Room</button>
        </div>
        <div class="table-wrap"><table>
          <tr><th>Section</th><th>Subject</th><th>Faculty</th><th>Room</th><th>Day & Time</th><th>Units</th></tr>
          <tr><td>BSIS 3-A</td><td>CC 313 — Web Systems</td><td>Jerome Bautista</td><td>Room 301</td><td>Mon/Wed 7:00–8:30</td><td>3</td></tr>
          <tr><td>BSIS 3-A</td><td>IT 401 — System Admin</td><td>Ana Reyes</td><td>Lab 1</td><td>Tue/Thu 8:30–10:00</td><td>3</td></tr>
          <tr><td>BSIS 3-A</td><td>GE 101 — Ethics</td><td>Maria Santos</td><td>Room 201</td><td>Mon/Wed 10:00–11:30</td><td>3</td></tr>
          <tr><td>BSIS 3-A</td><td>CC 401 — Capstone 1</td><td>Jerome Bautista</td><td>Room 302</td><td>Wed/Fri 11:30–1:00</td><td>3</td></tr>
          <tr><td>BSIS 3-A</td><td>IT 302 — Networking</td><td>Carlo Mendoza</td><td>Lab 2</td><td>Mon/Wed 1:00–2:30</td><td>3</td></tr>
          <tr><td>BSIS 3-B</td><td>CC 313 — Web Systems</td><td>Jerome Bautista</td><td>Room 301</td><td>Tue/Thu 7:00–8:30</td><td>3</td></tr>
          <tr><td>BSIS 3-B</td><td>CC 101 — Programming 1</td><td>Carlo Mendoza</td><td>Room 201</td><td>Tue/Thu 2:30–4:00</td><td>3</td></tr>
        </table></div>
      </div>
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

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── TAB SWITCHING (visual only — does not filter table data yet) ───────────
document.querySelectorAll('.tab-bar').forEach(bar => {
  bar.querySelectorAll('.tab-btn').forEach(btn => {
    btn.onclick = () => {
      bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
    };
  });
});
</script>
</body>
</html>