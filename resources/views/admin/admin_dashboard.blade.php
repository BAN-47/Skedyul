<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Admin Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
<style>
</style>
</head>
<body>

<div class="app-wrapper">

  @include('partials.admin_sidebar')

  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Technical Admin Dashboard</div>
      <div class="notif-wrap">
        <button class="btn-notif" type="button">
          Notifications <span class="notif-badge" id="notif-count">3</span>
        </button>
      </div>
    </div>
    <!-- END TOPBAR -->

    <!-- PAGE CONTENT -->
    <div class="page-content">

      <!-- WELCOME BANNER -->
      <div class="quote-banner">
        <div class="quote-banner-grid"></div>
        <div class="position-relative" style="z-index:1;">
          <div class="d-flex align-items-start justify-content-between gap-4">
            <div class="flex-grow-1">
              <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:6px;">
                Welcome back, Tech Admin
              </div>
              <div id="quote-text" style="font-size:22px;line-height:1.3;font-weight:700;color:#fff;margin-bottom:10px;font-style:italic;">
                "Education is the most powerful weapon which you can use to change the world."
              </div>
              <div id="quote-author" style="font-size:12px;color:rgba(255,255,255,.4);font-weight:600;">— Nelson Mandela</div>
              <div class="d-flex align-items-center gap-2 mt-3">
                <span style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;">‹</span>
                <div class="d-flex gap-2">
                  <span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.9);"></span>
                  <span style="width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.25);"></span>
                </div>
                <span style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;">›</span>
              </div>
            </div>
            <div class="text-end flex-shrink-0">
              <div style="font-size:44px;opacity:.12;line-height:1;margin-bottom:10px;">"</div>
              <div style="font-size:11px;color:rgba(255,255,255,.3);">AY 2025–2026 · 1st Sem</div>
              <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:4px;">Last backup</div>
              <div style="font-size:12px;font-weight:700;color:#4ade80;">Today 06:00 AM ✓</div>
            </div>
          </div>
        </div>
      </div>

      <!-- STAT CARDS ROW 1 -->
      <div class="row g-3 mb-3">
        <div class="col-md-3"><div class="stat-card" style="--accent:#2563eb"><div class="stat-icon">👥</div><div class="stat-label">Total Users</div><div class="stat-value"></div><div class="stat-sub">4 roles registered</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#16a34a"><div class="stat-icon">✅</div><div class="stat-label">System Status</div><div class="stat-value"></div><div class="stat-sub">Vercel · Supabase active</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#d97706"><div class="stat-icon">👨‍🏫</div><div class="stat-label">Total Faculty</div><div class="stat-value"></div><div class="stat-sub">BSIS · BSIT · BIT-CT</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#0891b2"><div class="stat-icon">🗄️</div><div class="stat-label">DB Records</div><div class="stat-value"></div><div class="stat-sub">Supabase PostgreSQL</div></div></div>
      </div>

      <!-- STAT CARDS ROW 2 -->
      <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="stat-card" style="--accent:#7c3aed"><div class="stat-icon">🏫</div><div class="stat-label">Total Sections</div><div class="stat-value"></div><div class="stat-sub">Across 3 programs</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#16a34a"><div class="stat-icon">📚</div><div class="stat-label">Subjects Offered</div><div class="stat-value"></div><div class="stat-sub">This semester</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#dc2626"><div class="stat-icon">⚡</div><div class="stat-label">Schedule Conflicts</div><div class="stat-value"></div><div class="stat-sub">Needs resolution</div></div></div>
        <div class="col-md-3"><div class="stat-card" style="--accent:#0891b2"><div class="stat-icon">🚪</div><div class="stat-label">Rooms Available</div><div class="stat-value"></div><div class="stat-sub">9 currently occupied</div></div></div>
      </div>

      <!-- USER ACCOUNTS + SYSTEM INFO -->
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="dash-card h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div><div class="card-title">User Accounts</div><div class="card-sub">All registered system users</div></div>
              <a href="{{ route('admin.users') }}" class="topbar-btn btn-primary-custom">+ Add User</a>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Name</th><th>Role</th><th>Department</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                  <tr><td><b>Ma. Emie Villaceran</b></td><td><span class="badge badge-blue">Dean</span></td><td>CCICT</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;" onclick="showToast('Editing Ma. Emie Villaceran...')">Edit</button></td></tr>
                  <tr><td><b>Rodrigo Tan</b></td><td><span class="badge badge-amber">Dept. Chair</span></td><td>BSIS</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;" onclick="showToast('Editing Rodrigo Tan...')">Edit</button></td></tr>
                  <tr><td><b>Lourdes Delos Santos</b></td><td><span class="badge badge-amber">Dept. Chair</span></td><td>BSIT</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;" onclick="showToast('Editing Lourdes Delos Santos...')">Edit</button></td></tr>
                  <tr><td><b>Jerome Bautista</b></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIS</td><td><span class="badge badge-green">Active</span></td><td><button class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;" onclick="showToast('Editing Jerome Bautista...')">Edit</button></td></tr>
                  <tr><td><b>Ana Reyes</b></td><td><span class="badge badge-grey">Faculty</span></td><td>BSIT</td><td><span class="badge badge-amber">Pending</span></td><td><button class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;" onclick="showToast('Editing Ana Reyes...')">Edit</button></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4 d-flex flex-column gap-3">
          <div class="dash-card">
            <div class="card-title mb-3">System Info</div>
            <div style="font-size:12px;color:var(--text2);line-height:1.8;">
              <div>🌐 <b>Host:</b> Vercel (Production)</div>
              <div>🗄️ <b>DB:</b> Supabase PostgreSQL</div>
              <div>🔐 <b>Auth:</b> JWT + Laravel</div>
              <div>📱 <b>Mobile:</b> React Native</div>
              <div>🎨 <b>Web UI:</b> Bootstrap 5</div>
              <div class="mt-2 pt-2" style="border-top:1px solid var(--border);">
                <div style="font-size:11px;color:var(--text3);">Last backup</div>
                <div style="font-weight:700;color:var(--green);">Today, 06:00 AM ✓</div>
              </div>
            </div>
          </div>
          <div class="dash-card flex-grow-1">
            <div class="card-title mb-3">Role Distribution</div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Faculty Members</div><div class="workload-val" style="color:var(--blue)">19</div></div><div class="workload-bar"><div class="workload-fill" style="width:70%;background:var(--blue)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Dept. Chairs</div><div class="workload-val" style="color:var(--amber)">3</div></div><div class="workload-bar"><div class="workload-fill" style="width:11%;background:var(--amber)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Dean</div><div class="workload-val" style="color:var(--teal)">1</div></div><div class="workload-bar"><div class="workload-fill" style="width:4%;background:var(--teal)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Tech Admin</div><div class="workload-val" style="color:var(--navy)">1</div></div><div class="workload-bar"><div class="workload-fill" style="width:4%;background:var(--navy)"></div></div></div>
          </div>
        </div>
      </div>

      <!-- SECTIONS + SCHEDULE COMPLETION -->
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="dash-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div><div class="card-title">Current Sections</div><div class="card-sub">AY 2025–2026 · 1st Semester</div></div>
              <span class="badge badge-blue">28 Total</span>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Section</th><th>Program</th><th>Year</th><th>Students</th><th>Subjects</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td><b>BSIS 1-A</b></td><td>BSIS</td><td>1st Year</td><td>42</td><td>8</td><td><span class="badge badge-green">Scheduled</span></td></tr>
                  <tr><td><b>BSIS 1-B</b></td><td>BSIS</td><td>1st Year</td><td>40</td><td>8</td><td><span class="badge badge-green">Scheduled</span></td></tr>
                  <tr><td><b>BSIS 2-A</b></td><td>BSIS</td><td>2nd Year</td><td>38</td><td>7</td><td><span class="badge badge-green">Scheduled</span></td></tr>
                  <tr><td><b>BSIT 1-A</b></td><td>BSIT</td><td>1st Year</td><td>44</td><td>8</td><td><span class="badge badge-green">Scheduled</span></td></tr>
                  <tr><td><b>BSIT 2-A</b></td><td>BSIT</td><td>2nd Year</td><td>39</td><td>7</td><td><span class="badge badge-amber">In Progress</span></td></tr>
                  <tr><td><b>BIT-CT 1-A</b></td><td>BIT-CT</td><td>1st Year</td><td>35</td><td>6</td><td><span class="badge badge-amber">In Progress</span></td></tr>
                  <tr><td><b>BIT-CT 2-A</b></td><td>BIT-CT</td><td>2nd Year</td><td>33</td><td>6</td><td><span class="badge badge-red">Unscheduled</span></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="dash-card h-100">
            <div class="card-title mb-1">Schedule Completion</div>
            <div class="card-sub mb-3">By program</div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">BSIS</div><div class="workload-val" style="color:var(--green)">100%</div></div><div class="workload-bar"><div class="workload-fill" style="width:100%;background:var(--green)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">BSIT</div><div class="workload-val" style="color:var(--amber)">72%</div></div><div class="workload-bar"><div class="workload-fill" style="width:72%;background:var(--amber)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">BIT-CT</div><div class="workload-val" style="color:var(--red)">45%</div></div><div class="workload-bar"><div class="workload-fill" style="width:45%;background:var(--red)"></div></div></div>
            <div class="mt-3 pt-3" style="border-top:1px solid var(--border);">
              <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:8px;">Sections Summary</div>
              <div class="d-flex gap-2">
                <div class="flex-fill text-center p-2 rounded-3" style="background:var(--green-light);"><div style="font-size:20px;font-weight:800;color:var(--green);">20</div><div style="font-size:10px;color:var(--green);font-weight:600;">Fully Scheduled</div></div>
                <div class="flex-fill text-center p-2 rounded-3" style="background:var(--amber-light);"><div style="font-size:20px;font-weight:800;color:var(--amber);">6</div><div style="font-size:10px;color:var(--amber);font-weight:600;">In Progress</div></div>
                <div class="flex-fill text-center p-2 rounded-3" style="background:var(--red-light);"><div style="font-size:20px;font-weight:800;color:var(--red);">2</div><div style="font-size:10px;color:var(--red);font-weight:600;">Unscheduled</div></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- SUBJECTS + ROOM UTILIZATION -->
      <div class="row g-3 mb-4">
        <div class="col-lg-8">
          <div class="dash-card">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div><div class="card-title">Subjects Offered</div><div class="card-sub">Current semester — all programs</div></div>
              <span class="badge badge-blue">42 Subjects</span>
            </div>
            <div class="table-wrap">
              <table>
                <thead><tr><th>Code</th><th>Subject</th><th>Units</th><th>Program</th><th>Assigned Faculty</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">CC101</span></td><td><b>Intro to Computing</b></td><td>3</td><td>BSIS / BSIT</td><td>Jerome Bautista</td><td><span class="badge badge-green">Active</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">CC102</span></td><td><b>Computer Programming 1</b></td><td>3</td><td>BSIS</td><td>Ana Reyes</td><td><span class="badge badge-green">Active</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">IT201</span></td><td><b>Data Structures &amp; Algo</b></td><td>3</td><td>BSIT</td><td>Carlo Mendoza</td><td><span class="badge badge-green">Active</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">IS301</span></td><td><b>Systems Analysis &amp; Design</b></td><td>3</td><td>BSIS</td><td>Maria Santos</td><td><span class="badge badge-green">Active</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">CT101</span></td><td><b>Technical Drawing</b></td><td>3</td><td>BIT-CT</td><td><span style="color:var(--red);">Unassigned</span></td><td><span class="badge badge-red">No Faculty</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">GE001</span></td><td><b>Understanding the Self</b></td><td>3</td><td>All</td><td>Noel Garcia</td><td><span class="badge badge-green">Active</span></td></tr>
                  <tr><td><span style="font-family:var(--mono);font-size:12px;">GE002</span></td><td><b>Purposive Communication</b></td><td>3</td><td>All</td><td>Liza Cruz</td><td><span class="badge badge-amber">Conflict</span></td></tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="dash-card h-100">
            <div class="card-title mb-1">Room Utilization</div>
            <div class="card-sub mb-3">This week</div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Room 301 (Lab)</div><div class="workload-val" style="color:var(--red)">95%</div></div><div class="workload-bar"><div class="workload-fill" style="width:95%;background:var(--red)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Room 205 (Lecture)</div><div class="workload-val" style="color:var(--amber)">80%</div></div><div class="workload-bar"><div class="workload-fill" style="width:80%;background:var(--amber)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Room 101 (Lecture)</div><div class="workload-val" style="color:var(--green)">65%</div></div><div class="workload-bar"><div class="workload-fill" style="width:65%;background:var(--green)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">Room 302 (Lab)</div><div class="workload-val" style="color:var(--green)">60%</div></div><div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--green)"></div></div></div>
            <div class="workload-item"><div class="workload-header"><div class="workload-name">AVR / Function Hall</div><div class="workload-val" style="color:var(--blue)">30%</div></div><div class="workload-bar"><div class="workload-fill" style="width:30%;background:var(--blue)"></div></div></div>
            <div class="d-flex justify-content-between mt-3 pt-3" style="border-top:1px solid var(--border);font-size:12px;">
              <div><div style="color:var(--text3);">Total Rooms</div><div style="font-weight:800;font-size:18px;color:var(--text);">12</div></div>
              <div><div style="color:var(--text3);">In Use</div><div style="font-weight:800;font-size:18px;color:var(--blue);">9</div></div>
              <div><div style="color:var(--text3);">Available</div><div style="font-weight:800;font-size:18px;color:var(--green);">3</div></div>
            </div>
          </div>
        </div>
      </div>

      <!-- RECENT ACTIVITY -->
      <div class="dash-card mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
          <div><div class="card-title">Recent System Activity</div><div class="card-sub">Latest actions across all users</div></div>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Time</th><th>User</th><th>Action</th><th>Details</th><th>Status</th></tr></thead>
            <tbody>
              <tr><td style="font-family:var(--mono);font-size:11px;color:var(--text3);">09:42 AM</td><td><b>Rodrigo Tan</b></td><td>Schedule Updated</td><td>BSIS 2-A · Monday slot reassigned</td><td><span class="badge badge-green">Success</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:11px;color:var(--text3);">09:15 AM</td><td><b>Ana Reyes</b></td><td>Login</td><td>Web portal · Chrome / Windows</td><td><span class="badge badge-blue">Info</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:11px;color:var(--text3);">08:50 AM</td><td><b>Lourdes Delos Santos</b></td><td>Subject Added</td><td>CT102 — Technical Drawing 2</td><td><span class="badge badge-green">Success</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:11px;color:var(--text3);">08:30 AM</td><td><b>System</b></td><td>Conflict Detected</td><td>GE002 · Room 205 double-booked Wed 1PM</td><td><span class="badge badge-red">Warning</span></td></tr>
              <tr><td style="font-family:var(--mono);font-size:11px;color:var(--text3);">07:55 AM</td><td><b>Tech Admin</b></td><td>User Created</td><td>New faculty account — Liza Cruz (BSIT)</td><td><span class="badge badge-green">Success</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

    </div><!-- end page-content -->
  </div><!-- end main -->
</div><!-- end app-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
