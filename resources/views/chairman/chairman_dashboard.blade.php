<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Department Chair Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair_dashboard.css') }}">
</head>
<body>

<div class="app-wrapper">

@include('partials.chair_sidebar')

  <!-- ══════════ MAIN ══════════ -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Department Chairperson Dashboard</div>
      <div style="display:flex;align-items:center;gap:10px;">
        <span class="badge badge-blue" style="font-size:11px;">BSIS · AY 2025–26 · 1st Sem</span>
        <div class="notif-btn" id="notif-btn" onclick="toggleNotifPanel()"
             style="width:auto;padding:0 14px;border-radius:8px;font-size:13px;font-weight:600;color:var(--text2);gap:7px;">
          <div class="notif-dot" id="notif-dot"></div>
          Notifications
        </div>
      </div>
    </div>

    <!-- NOTIFICATION PANEL -->
    <div class="notif-panel" id="notif-panel">
      <div class="notif-panel-header">
        <span class="notif-panel-title">Notifications</span>
        <span class="badge badge-red" id="notif-count-badge">2 new</span>
      </div>
      <div class="notif-panel-item">
        <div class="notif-dot-sm" style="background:var(--red);"></div>
        <div>
          <div class="notif-panel-text"><strong>Conflict — Maria Santos</strong><br>GE 102 & IT 101 overlap Tue 7:00–8:30 AM. Fix before submitting.</div>
          <div class="notif-panel-time">Today, 08:30 AM</div>
        </div>
      </div>
      <div class="notif-panel-item">
        <div class="notif-dot-sm" style="background:var(--red);"></div>
        <div>
          <div class="notif-panel-text"><strong>Conflict — Jerome Bautista</strong><br>CC 313 & CC 401 assigned same room Wed 10:00–11:30 AM.</div>
          <div class="notif-panel-time">Today, 08:35 AM</div>
        </div>
      </div>
      <div class="notif-panel-item">
        <div class="notif-dot-sm" style="background:var(--amber);"></div>
        <div>
          <div class="notif-panel-text"><strong>Near-Max Load — Felicitas Lagman</strong><br>Currently at 27u / 30u max. Only 3 units remaining.</div>
          <div class="notif-panel-time">Today, 08:00 AM</div>
        </div>
      </div>
      <div class="notif-panel-item">
        <div class="notif-dot-sm" style="background:var(--blue);"></div>
        <div>
          <div class="notif-panel-text"><strong>Submission Deadline Reminder</strong><br>Schedule due to Dean Villaceran by Friday, July 25.</div>
          <div class="notif-panel-time">Yesterday, 9:00 AM</div>
        </div>
      </div>
      <div class="notif-panel-item">
        <div class="notif-dot-sm" style="background:var(--green);"></div>
        <div>
          <div class="notif-panel-text"><strong>System Backup Complete</strong><br>Automatic backup successful at 06:00 AM.</div>
          <div class="notif-panel-time">Today, 06:00 AM</div>
        </div>
      </div>
    </div>

    <!-- ══════════ DASHBOARD PAGE ══════════ -->
    <div class="page-content" style="animation:fadeIn .3s ease;">

      <!-- Page heading -->
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <div style="flex:1;">
          <div style="font-size:22px;font-weight:800;">Good morning, Chair Tan</div>
          <div style="font-size:13px;color:var(--text3);margin-top:3px;">BSIS Department · AY 2025–2026 · 1st Semester</div>
        </div>
        <span id="dash-status-badge" class="badge badge-red">2 Conflicts</span>
      </div>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card" style="--accent:#2563eb">
          <div class="stat-label">Faculty (BSIS)</div>
          <div class="stat-value">4</div>
          <div class="stat-sub">Under your department</div>
        </div>
        <div class="stat-card" style="--accent:#16a34a">
          <div class="stat-label">Subjects Plotted</div>
          <div class="stat-value">5</div>
          <div class="stat-sub">of 6 total</div>
        </div>
        <div class="stat-card" style="--accent:#dc2626">
          <div class="stat-label">Conflicts</div>
          <div class="stat-value" id="stat-conflicts">2</div>
          <div class="stat-sub" id="stat-conflicts-sub">Requires resolution</div>
        </div>
        <div class="stat-card" style="--accent:#0891b2">
          <div class="stat-label">Sections</div>
          <div class="stat-value">23</div>
          <div class="stat-sub">All year levels</div>
        </div>
      </div>

      <!-- Conflict Alert -->
      <div id="dash-conflict-alert" class="conflict-alert">
        <div class="conflict-alert-text">
          <strong>2 Conflicts Detected</strong>
          Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM. Jerome Bautista: CC 313 & CC 401 room conflict Wed 10:00–11:30 AM. Resolve before submitting to the Dean.
        </div>
        <button class="topbar-btn btn-danger" style="margin-left:auto;white-space:nowrap;padding:6px 12px;font-size:12px;" onclick="resolveConflicts()">Fix Now</button>
      </div>

      <!-- Success Alert (hidden by default) -->
      <div id="dash-ok-alert" class="success-alert" style="display:none;">
        <div class="success-alert-text">
          <strong>No Conflicts — Schedule is clean!</strong> All faculty schedules are conflict-free. Ready to submit to the Dean.
        </div>
        <button class="topbar-btn btn-success" style="margin-left:auto;white-space:nowrap;padding:6px 12px;font-size:12px;">Submit</button>
      </div>

      <!-- Faculty Load + Workload Distribution -->
      <div class="two-col">
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Faculty Load Summary</div>
              <div class="card-sub">BSIS · Max 30 units/week</div>
            </div>
          </div>
          <table>
            <thead>
              <tr><th>Faculty</th><th>Load</th><th>Remaining</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Jerome Bautista</b></td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--green)">24u</span></td>
                <td><span class="badge badge-green">6u left</span></td>
                <td><span class="badge badge-green">OK</span></td>
              </tr>
              <tr>
                <td><b>Felicitas Lagman</b></td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--amber)">27u</span></td>
                <td><span class="badge badge-amber">3u left</span></td>
                <td><span class="badge badge-amber">Near Max</span></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--blue)">18u</span></td>
                <td><span class="badge badge-blue">12u left</span></td>
                <td><span class="badge badge-blue">Available</span></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--teal)">18u</span></td>
                <td><span class="badge badge-grey">Part-time</span></td>
                <td><span class="badge badge-teal">Part-time</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Workload Distribution</div>
              <div class="card-sub">Units per week · Max 30</div>
            </div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Jerome Bautista</div>
              <div class="workload-val" style="color:var(--green)">24/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:80%;background:var(--green)"></div></div>
            <div style="font-size:11px;color:var(--green);margin-top:3px;">6 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Felicitas Lagman</div>
              <div class="workload-val" style="color:var(--amber)">27/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:90%;background:var(--amber)"></div></div>
            <div style="font-size:11px;color:var(--amber);margin-top:3px;">3 units remaining — near maximum</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Maria Santos</div>
              <div class="workload-val" style="color:var(--blue)">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--blue)"></div></div>
            <div style="font-size:11px;color:var(--blue);margin-top:3px;">12 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Ana Reyes (Part-time)</div>
              <div class="workload-val" style="color:var(--teal)">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--teal)"></div></div>
            <div style="font-size:11px;color:var(--text3);margin-top:3px;">Part-time — verify additional load with Dean</div>
          </div>
        </div>
      </div>

    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end main -->
</div><!-- end app-wrapper -->

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
/* ── SIDEBAR NAV ── */
function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

/* ── NOTIFICATION PANEL ── */
function toggleNotifPanel() {
  document.getElementById('notif-panel').classList.toggle('open');
}
document.addEventListener('click', function(e) {
  const panel = document.getElementById('notif-panel');
  const btn   = document.getElementById('notif-btn');
  if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
    panel.classList.remove('open');
  }
});

/* ── RESOLVE CONFLICTS (demo) ── */
function resolveConflicts() {
  document.getElementById('dash-conflict-alert').style.display = 'none';
  document.getElementById('dash-ok-alert').style.display = 'flex';
  document.getElementById('dash-status-badge').textContent = 'All Clear';
  document.getElementById('dash-status-badge').className = 'badge badge-green';
  document.getElementById('stat-conflicts').textContent = '0';
  document.getElementById('stat-conflicts-sub').textContent = 'All clear';
  document.getElementById('conflict-badge').textContent = '0';
  document.getElementById('conflict-badge').style.background = 'var(--green)';
  document.getElementById('notif-dot').style.display = 'none';
  document.getElementById('notif-count-badge').textContent = '0 new';
  document.getElementById('notif-count-badge').className = 'badge badge-grey';
  showToast('Conflicts resolved! Schedule is now clean and ready to submit.');
}

/* ── TOAST ── */
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>
</body>
</html>
