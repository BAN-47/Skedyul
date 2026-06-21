<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Dashboard</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty_dashboard.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.facultyMember_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Faculty Dashboard</div>
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

<div id="page-faculty-dashboard" class="page active">
      <!-- Welcome Banner with rotating quote -->
      <div style="width:100%;max-width:100%;box-sizing:border-box;background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#1a2d5a 100%);border-radius:16px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;min-height:148px;">
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:28px 28px;pointer-events:none;"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:24px;flex-wrap:wrap;min-width:0;">
          <div style="flex:1 1 auto;min-width:0;">
            <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">Welcome back, Jerome Bautista</div>
            <div style="font-size:20px;line-height:1.35;font-weight:700;color:#fff;margin-bottom:10px;font-style:italic;min-height:54px;" id="fac-quote-text">"The art of teaching is the art of assisting discovery."</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.4);font-weight:600;" id="fac-quote-author">— Mark Van Doren</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:14px;">
              <button onclick="prevFacQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:#fff;cursor:pointer;font-size:13px;flex-shrink:0;">&#8249;</button>
              <div id="fac-quote-dots" style="display:flex;gap:5px;"></div>
              <button onclick="nextFacQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:#fff;cursor:pointer;font-size:13px;flex-shrink:0;">&#8250;</button>
            </div>
          </div>
          <div style="text-align:right;flex:0 0 180px;width:180px;">
            <div style="font-size:44px;opacity:0.12;line-height:1;margin-bottom:10px;">"</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.3);white-space:nowrap;">Faculty · BSIS Dept</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:2px;white-space:nowrap;">AY 2025–2026 · 1st Sem</div>
          </div>
        </div>
      </div>

      <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Teaching Load</div><div class="stat-value">24h</div><div class="stat-sub">of 30h max</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">My Subjects</div><div class="stat-value">3</div><div class="stat-sub">This semester</div></div>
        <div class="stat-card" style="--accent:#d97706"><div class="stat-label">My Sections</div><div class="stat-value">4</div><div class="stat-sub">BSIS 1-A, 3-A, 3-B, 4-A</div></div>
      </div>

      <div class="row">
        <div style="flex:1;">
          <div class="card">
            <div class="card-header"><div><div class="card-title">Today's Schedule — Monday</div><div class="card-sub">AY 2025–2026 · 1st Semester</div></div></div>
            <div class="table-wrap"><table>
              <tr><th>Time</th><th>Subject</th><th>Room</th><th>Section</th><th>Type</th></tr>
              <tr><td style="font-family:var(--mono);font-size:12px;color:var(--text3);">7:00–8:30</td><td><b>CC 313 — Web Systems</b></td><td>Room 301</td><td>BSIS 3-A</td><td><span class="badge badge-blue">Lecture</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:12px;color:var(--text3);">10:00–11:30</td><td><b>CC 401 — Capstone 1</b></td><td>Room 302</td><td>BSIS 4-A</td><td><span class="badge badge-amber">Lecture</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:12px;color:var(--text3);">1:00–2:30</td><td><b>IT 302 — Networking</b></td><td>Lab 2</td><td>BSIS 3-B</td><td><span class="badge badge-green">Lab</span></td></tr>
            </table></div>
          </div>
        </div>
        <div style="width:280px;flex-shrink:0;">
          <div class="card">
            <div class="card-header"><div class="card-title">My Subjects</div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">CC 313 — Web Systems</div><div class="workload-val" style="color:var(--blue);">3u</div></div><div class="workload-bar"><div class="workload-fill" style="width:100%;background:var(--blue);"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">CC 401 — Capstone 1</div><div class="workload-val" style="color:var(--amber);">3u</div></div><div class="workload-bar"><div class="workload-fill" style="width:100%;background:var(--amber);"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">IT 302 — Networking</div><div class="workload-val" style="color:var(--green);">3u</div></div><div class="workload-bar"><div class="workload-fill" style="width:100%;background:var(--green);"></div></div></div>
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:12px;">
              <div><div style="color:var(--text3);">Total Load</div><div style="font-weight:800;font-size:18px;color:var(--text);">24h</div></div>
              <div style="text-align:right;"><div style="color:var(--text3);">Max Load</div><div style="font-weight:800;font-size:18px;color:var(--green);">30h</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ── FACULTY SUBJECTS (read-only, no add/edit) ── -->

  </div>
</div>

<script>
// ── NAV CLICK (placeholder — wire to your routing) ──────────────────────────
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', () => {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    item.classList.add('active');
    const titles = {
      'faculty-dashboard': 'Dashboard',
      'faculty-subjects': 'My Subjects',
      'faculty-schedule': 'My Schedule',
      'faculty-settings': 'Settings',
    };
    document.getElementById('topbar-title').textContent = titles[item.dataset.page] || 'SKEDYUL';
  });
});

function logout() {
  alert('Sign out clicked — wire this to your actual logout route.');
}

// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const FACULTY_NOTIFS = [
  { dot:'var(--blue)', text:'<b>Schedule Updated</b> — CC 313 time changed to 7:00 AM Mon & Wed.', time:'Today, 8:12 AM', unread:true },
  { dot:'var(--green)', text:'<b>New Assignment</b> — CC 401 added to your load for 1st Semester.', time:'Yesterday, 3:45 PM', unread:true },
  { dot:'var(--amber)', text:'<b>Reminder</b> — Faculty schedule submission deadline is Friday.', time:'Mar 16, 9:00 AM', unread:true },
  { dot:'var(--green)', text:'<b>System</b> — 1st Semester AY 2025–2026 schedule has been published.', time:'Mar 10, 10:00 AM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = FACULTY_NOTIFS.map((n) => `
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

// ── ROTATING QUOTE (Welcome Banner) ──────────────────────────────────────────
const FAC_QUOTES = [
  { text: '"The art of teaching is the art of assisting discovery."', author: '— Mark Van Doren' },
  { text: '"A good teacher can inspire hope, ignite the imagination, and instill a love of learning."', author: '— Brad Henry' },
  { text: '"Teaching is the one profession that creates all other professions."', author: '— Unknown' },
  { text: '"The mediocre teacher tells. The good teacher explains. The great teacher inspires."', author: '— William Arthur Ward' },
  { text: '"To teach is to touch a life forever."', author: '— Unknown' },
  { text: '"Education is not preparation for life; education is life itself."', author: '— John Dewey' },
];
let facQuoteIndex = Math.floor(Math.random() * FAC_QUOTES.length);
let facQuoteTimer = null;

function renderFacQuote() {
  const q = FAC_QUOTES[facQuoteIndex];
  const t = document.getElementById('fac-quote-text');
  const a = document.getElementById('fac-quote-author');
  const d = document.getElementById('fac-quote-dots');
  if (!t) return;
  t.style.opacity = '0'; a.style.opacity = '0';
  setTimeout(() => {
    t.textContent = q.text; a.textContent = q.author;
    t.style.transition = 'opacity 0.5s'; a.style.transition = 'opacity 0.5s';
    t.style.opacity = '1'; a.style.opacity = '1';
  }, 300);
  if (d) {
    d.innerHTML = '';
    FAC_QUOTES.forEach((_, i) => {
      const dot = document.createElement('div');
      dot.style.cssText = `width:6px;height:6px;border-radius:50%;background:${i===facQuoteIndex?'rgba(255,255,255,0.9)':'rgba(255,255,255,0.25)'};cursor:pointer;transition:background 0.3s;`;
      dot.onclick = () => { facQuoteIndex = i; renderFacQuote(); resetFacQuoteTimer(); };
      d.appendChild(dot);
    });
  }
}
function nextFacQuote() { facQuoteIndex = (facQuoteIndex + 1) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function prevFacQuote() { facQuoteIndex = (facQuoteIndex - 1 + FAC_QUOTES.length) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function resetFacQuoteTimer() { clearInterval(facQuoteTimer); facQuoteTimer = setInterval(nextFacQuote, 6000); }

renderFacQuote();
resetFacQuoteTimer();
</script>
</body>
</html>