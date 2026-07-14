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
                <thead><tr><th>Name</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                  @php
                    $roleLabels  = [
                      'faculty'          => ['Faculty', 'badge-grey'],
                      'department_chair' => ['Dept. Chair', 'badge-amber'],
                      'dean'             => ['Dean', 'badge-blue'],
                      'system_admin'     => ['System Admin', 'badge-navy'],
                    ];
                  @endphp
                  @forelse($users as $user)
                    @php
                      [$roleLabel, $roleBadge] = $roleLabels[$user->usr_role] ?? [ucfirst($user->usr_role), 'badge-grey'];
                    @endphp
                    <tr>
                      <td><b>{{ $user->usr_name }}</b></td>
                      <td><span class="badge {{ $roleBadge }}">{{ $roleLabel }}</span></td>
                      <td>
                        <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                          {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                      <td>
                        <a href="{{ route('admin.users') }}" class="topbar-btn btn-secondary-custom" style="padding:4px 10px;font-size:11px;text-decoration:none;">
                          Edit
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="4" style="text-align:center;padding:24px;color:var(--text3);font-size:13px;">
                        No user accounts found yet.
                      </td>
                    </tr>
                  @endforelse
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
            @php
              $pct = fn($count) => $totalUsers > 0 ? round(($count / $totalUsers) * 100) : 0;
            @endphp
            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Faculty Members</div>
                <div class="workload-val" style="color:var(--blue)">{{ $roleCounts['faculty'] }}</div>
              </div>
              <div class="workload-bar"><div class="workload-fill" style="width:{{ $pct($roleCounts['faculty']) }}%;background:var(--blue)"></div></div>
            </div>
            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Dept. Chairs</div>
                <div class="workload-val" style="color:var(--amber)">{{ $roleCounts['department_chair'] }}</div>
              </div>
              <div class="workload-bar"><div class="workload-fill" style="width:{{ $pct($roleCounts['department_chair']) }}%;background:var(--amber)"></div></div>
            </div>
            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Dean</div>
                <div class="workload-val" style="color:var(--teal)">{{ $roleCounts['dean'] }}</div>
              </div>
              <div class="workload-bar"><div class="workload-fill" style="width:{{ $pct($roleCounts['dean']) }}%;background:var(--teal)"></div></div>
            </div>
            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Tech Admin</div>
                <div class="workload-val" style="color:var(--navy)">{{ $roleCounts['system_admin'] }}</div>
              </div>
              <div class="workload-bar"><div class="workload-fill" style="width:{{ $pct($roleCounts['system_admin']) }}%;background:var(--navy)"></div></div>
            </div>
          </div>
        </div>
      </div>

    </div><!-- end page-content -->
  </div><!-- end main -->
</div><!-- end app-wrapper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
