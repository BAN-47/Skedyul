<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Department Chair Dashboard</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

@include('partials.chair_sidebar')

  <!-- ══════════ MAIN ══════════ -->
<div class="app-main relative">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Department Chairperson Dashboard</div>
      <div class="flex items-center gap-2.5">
        <span class="badge badge-blue text-[11px]">BSIS · AY 2025–26 · 1st Sem</span>
        <div class="relative flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-slate-100 text-slate-600 text-[13px] font-semibold cursor-pointer hover:bg-slate-200 transition"
             id="notif-btn" onclick="toggleNotifPanel()">
          <div class="w-2 h-2 rounded-full bg-red-500" id="notif-dot"></div>
          Notifications
        </div>
      </div>
    </div>

    <!-- NOTIFICATION PANEL -->
    <div class="hidden absolute top-[60px] right-7 w-[340px] max-h-[420px] overflow-y-auto bg-white rounded-2xl border border-slate-200 shadow-[0_20px_60px_rgba(0,0,0,.15)] z-50"
         id="notif-panel">
      <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
        <span class="text-[14px] font-bold text-slate-900">Notifications</span>
        <span class="badge badge-red" id="notif-count-badge">2 new</span>
      </div>

      <div class="flex gap-2.5 px-4 py-3 border-b border-slate-100 hover:bg-slate-50">
        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-red-500"></div>
        <div>
          <div class="text-[12.5px] text-slate-600 leading-relaxed"><strong>Conflict — Maria Santos</strong><br>GE 102 & IT 101 overlap Tue 7:00–8:30 AM. Fix before submitting.</div>
          <div class="text-[11px] text-slate-400 mt-1">Today, 08:30 AM</div>
        </div>
      </div>

      <div class="flex gap-2.5 px-4 py-3 border-b border-slate-100 hover:bg-slate-50">
        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-red-500"></div>
        <div>
          <div class="text-[12.5px] text-slate-600 leading-relaxed"><strong>Conflict — Jerome Bautista</strong><br>CC 313 & CC 401 assigned same room Wed 10:00–11:30 AM.</div>
          <div class="text-[11px] text-slate-400 mt-1">Today, 08:35 AM</div>
        </div>
      </div>

      <div class="flex gap-2.5 px-4 py-3 border-b border-slate-100 hover:bg-slate-50">
        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-amber-500"></div>
        <div>
          <div class="text-[12.5px] text-slate-600 leading-relaxed"><strong>Near-Max Load — Felicitas Lagman</strong><br>Currently at 27u / 30u max. Only 3 units remaining.</div>
          <div class="text-[11px] text-slate-400 mt-1">Today, 08:00 AM</div>
        </div>
      </div>

      <div class="flex gap-2.5 px-4 py-3 border-b border-slate-100 hover:bg-slate-50">
        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-blue-500"></div>
        <div>
          <div class="text-[12.5px] text-slate-600 leading-relaxed"><strong>Submission Deadline Reminder</strong><br>Schedule due to Dean Villaceran by Friday, July 25.</div>
          <div class="text-[11px] text-slate-400 mt-1">Yesterday, 9:00 AM</div>
        </div>
      </div>

      <div class="flex gap-2.5 px-4 py-3 hover:bg-slate-50">
        <div class="w-2 h-2 rounded-full mt-1.5 flex-shrink-0 bg-green-500"></div>
        <div>
          <div class="text-[12.5px] text-slate-600 leading-relaxed"><strong>System Backup Complete</strong><br>Automatic backup successful at 06:00 AM.</div>
          <div class="text-[11px] text-slate-400 mt-1">Today, 06:00 AM</div>
        </div>
      </div>
    </div>

    <!-- ══════════ DASHBOARD PAGE ══════════ -->
<div class="page-content">
      <!-- Page heading -->
      <div class="flex items-center gap-3 mb-6">
        <div class="flex-1">
          <div class="text-[22px] font-extrabold">Good morning, Chair Tan</div>
          <div class="text-[13px] text-slate-400 mt-0.5">BSIS Department · AY 2025–2026 · 1st Semester</div>
        </div>
        <span id="dash-status-badge" class="badge badge-red">2 Conflicts</span>
      </div>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-card-bar bg-blue-600"></div>
          <div class="stat-label">Faculty (BSIS)</div>
          <div class="stat-value">4</div>
          <div class="stat-sub">Under your department</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-green-600"></div>
          <div class="stat-label">Subjects Plotted</div>
          <div class="stat-value">5</div>
          <div class="stat-sub">of 6 total</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-red-600"></div>
          <div class="stat-label">Conflicts</div>
          <div class="stat-value" id="stat-conflicts">2</div>
          <div class="stat-sub" id="stat-conflicts-sub">Requires resolution</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-cyan-600"></div>
          <div class="stat-label">Sections</div>
          <div class="stat-value">23</div>
          <div class="stat-sub">All year levels</div>
        </div>
      </div>

      <!-- Conflict Alert -->
      <div id="dash-conflict-alert" class="flex items-center gap-4 bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
        <div class="text-[13px] text-red-700 leading-relaxed">
          <strong class="block text-red-800 font-bold mb-0.5">2 Conflicts Detected</strong>
          Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM. Jerome Bautista: CC 313 & CC 401 room conflict Wed 10:00–11:30 AM. Resolve before submitting to the Dean.
        </div>
        <a href="{{ route('chair.schedule_plotter') }}"
           class="ml-auto whitespace-nowrap px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-red-100 text-red-600 hover:bg-red-200 no-underline">Fix Now</a>
      </div>

      <!-- Success Alert (hidden by default) -->
      <div id="dash-ok-alert" class="hidden items-center gap-4 bg-green-50 border border-green-200 rounded-2xl p-4 mb-6">
        <div class="text-[13px] text-green-700">
          <strong class="block text-green-800 font-bold mb-0.5">No Conflicts — Schedule is clean!</strong> All faculty schedules are conflict-free. Ready to submit to the Dean.
        </div>
        <button class="ml-auto whitespace-nowrap px-3 py-1.5 rounded-lg text-[12px] font-semibold bg-green-100 text-green-600 hover:bg-green-200">Submit</button>
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
                <td><span class="font-mono font-bold text-green-600">24u</span></td>
                <td><span class="badge badge-green">6u left</span></td>
                <td><span class="badge badge-green">OK</span></td>
              </tr>
              <tr>
                <td><b>Felicitas Lagman</b></td>
                <td><span class="font-mono font-bold text-amber-600">27u</span></td>
                <td><span class="badge badge-amber">3u left</span></td>
                <td><span class="badge badge-amber">Near Max</span></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td>
                <td><span class="font-mono font-bold text-blue-600">18u</span></td>
                <td><span class="badge badge-blue">12u left</span></td>
                <td><span class="badge badge-blue">Available</span></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td>
                <td><span class="font-mono font-bold text-cyan-600">18u</span></td>
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
              <div class="workload-val text-green-600">24/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill bg-green-600" style="width:80%"></div></div>
            <div class="text-[11px] text-green-600 mt-1">6 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Felicitas Lagman</div>
              <div class="workload-val text-amber-600">27/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill bg-amber-600" style="width:90%"></div></div>
            <div class="text-[11px] text-amber-600 mt-1">3 units remaining — near maximum</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Maria Santos</div>
              <div class="workload-val text-blue-600">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill bg-blue-600" style="width:60%"></div></div>
            <div class="text-[11px] text-blue-600 mt-1">12 units remaining</div>
          </div>
          <div class="workload-item">
            <div class="workload-header">
              <div class="workload-name">Ana Reyes (Part-time)</div>
              <div class="workload-val text-cyan-600">18/30u</div>
            </div>
            <div class="workload-bar"><div class="workload-fill bg-cyan-600" style="width:60%"></div></div>
            <div class="text-[11px] text-slate-400 mt-1">Part-time — verify additional load with Dean</div>
          </div>
        </div>
      </div>

    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end app-main -->
</div><!-- end app-shell -->

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
  document.getElementById('notif-panel').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
  const panel = document.getElementById('notif-panel');
  const btn   = document.getElementById('notif-btn');
  if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
    panel.classList.add('hidden');
  }
});

/* ── RESOLVE CONFLICTS (demo) ── */
function resolveConflicts() {
  document.getElementById('dash-conflict-alert').classList.add('hidden');
  document.getElementById('dash-ok-alert').classList.remove('hidden');
  document.getElementById('dash-ok-alert').classList.add('flex');
  document.getElementById('dash-status-badge').textContent = 'All Clear';
  document.getElementById('dash-status-badge').className = 'badge badge-green';
  document.getElementById('stat-conflicts').textContent = '0';
  document.getElementById('stat-conflicts-sub').textContent = 'All clear';
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