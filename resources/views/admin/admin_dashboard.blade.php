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
            Notifications <span class="notif-badge" id="notif-count">{{ $notifCount }}</span>
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
            <div id="quote-author" style="font-size:12px;color:rgba(255,255,255,.4);font-weight:600;">— Nelson Mandela</div>
            <div class="d-flex align-items-center gap-2 mt-3">
              <span id="quote-prev" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;cursor:pointer;">‹</span>
              <div class="d-flex gap-2" id="quote-dots"></div>
              <span id="quote-next" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;cursor:pointer;">›</span>
            </div>
          </div>
          <div class="text-end flex-shrink-0">
            <div style="font-size:44px;opacity:.12;line-height:1;margin-bottom:10px;">"</div>
            <div style="font-size:11px;color:rgba(255,255,255,.3);">AY 2025–2026 · 1st Sem</div>
            <div style="font-size:11px;color:rgba(255,255,255,.3);margin-top:4px;">Last backup</div>
            <div style="font-size:12px;font-weight:700;color:#4ade80;">Today 06:00 AM ✓</div>
          </div>
        </div>

        <!-- STAT CARDS ROW 1 -->
        <div class="row g-3 mb-3">
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#2563eb">
              <div class="stat-icon">👥</div>
              <div class="stat-label">Total Users</div>
              <div class="stat-value">{{ $totalUsers }}</div>
              <div class="stat-sub">4 roles registered</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#16a34a">
              <div class="stat-icon">✅</div>
              <div class="stat-label">System Status</div>
              <div class="stat-value" style="font-size:20px;">{{ $dbStatus }}</div>
              <div class="stat-sub">Vercel · Supabase active</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#d97706">
              <div class="stat-icon">👨‍🏫</div>
              <div class="stat-label">Total Faculty</div>
              <div class="stat-value">{{ $totalFaculty }}</div>
              <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#0891b2">
              <div class="stat-icon">🗄️</div>
              <div class="stat-label">DB Records</div>
              <div class="stat-value">{{ $dbRecords }}</div>
              <div class="stat-sub">Supabase PostgreSQL</div>
            </div>
          </div>
        </div>

        <!-- STAT CARDS ROW 2 -->
        <div class="row g-3 mb-4">
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#7c3aed">
              <div class="stat-icon">🏫</div>
              <div class="stat-label">Total Sections</div>
              <div class="stat-value">{{ $totalSections }}</div>
              <div class="stat-sub">Across 3 programs</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#16a34a">
              <div class="stat-icon">📚</div>
              <div class="stat-label">Subjects Offered</div>
               <div class="stat-value">{{ $subjectsOffered }}</div>
              <div class="stat-sub">This semester</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#dc2626">
              <div class="stat-icon">⚡</div>
              <div class="stat-label">Schedule Conflicts</div>
              <div class="stat-value">{{ $scheduleConflicts }}</div>
              <div class="stat-sub">Needs resolution</div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="stat-card" style="--accent:#0891b2">
              <div class="stat-icon">🚪</div>
              <div class="stat-label">Rooms Available</div>
              <div class="stat-value">{{ $roomsAvailable }}</div>
              <div class="stat-sub">{{ $roomsOccupied }} currently occupied</div>
            </div>
          </div>
        </div>

        <!-- USER ACCOUNTS + SYSTEM INFO -->
        <div class="row g-3 mb-4">
          <div class="col-lg-8">
            <div class="dash-card h-100">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                  <div class="card-title">User Accounts</div>
                  <div class="card-sub">All registered system users</div>
                </div>
                <span class="badge badge-blue">{{ $totalUsers }} Total</span>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php
                    $roleLabels = [
                    'faculty' => ['Faculty', 'badge-grey'],
                    'department_chair' => ['Dept. Chair', 'badge-amber'],
                    'dean' => ['Dean', 'badge-blue'],
                    'system_admin' => ['System Admin', 'badge-navy'],
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
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $pct($roleCounts['faculty']) }}%;background:var(--blue)"></div>
                </div>
              </div>
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">Dept. Chairs</div>
                  <div class="workload-val" style="color:var(--amber)">{{ $roleCounts['department_chair'] }}</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $pct($roleCounts['department_chair']) }}%;background:var(--amber)"></div>
                </div>
              </div>
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">Dean</div>
                  <div class="workload-val" style="color:var(--teal)">{{ $roleCounts['dean'] }}</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $pct($roleCounts['dean']) }}%;background:var(--teal)"></div>
                </div>
              </div>
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">Tech Admin</div>
                  <div class="workload-val" style="color:var(--navy)">{{ $roleCounts['system_admin'] }}</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $pct($roleCounts['system_admin']) }}%;background:var(--navy)"></div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- SECTIONS + SCHEDULE COMPLETION -->
        <div class="row g-3 mb-4">
          <div class="col-lg-8">
            <div class="dash-card">
              <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                  <div class="card-title">Current Sections</div>
                  <div class="card-sub">
                    AY {{ $academicYear->ay_year_label ?? 'N/A' }} · {{ $semester->sem_name ?? 'N/A' }}
                  </div>
                </div>
                <span class="badge badge-blue">{{ $section->count() }} Total</span>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Section</th>
                      <th>Program</th>
                      <th>Year</th>
                      <th>Students</th>
                      <th>Subjects</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($subject as $subjects)
                    <tr>
                      <td><span style="font-family:var(--mono);font-size:12px;">{{ $subjects->subj_code }}</span></td>
                      <td><b>{{ $subjects->subj_name }}</b></td>
                      <td>{{ $subjects->subj_lecture_hours + $subjects->subj_lab_hours }}</td>
                      <td>{{ $subjects->program->prog_name ?? 'N/A' }}</td>
                      <td><span style="color:var(--red);">Unassigned</span></td>
                      <td>
                        <span class="badge {{ $subjects->subj_is_active ? 'badge-green' : 'badge-red' }}">
                          {{ $subjects->subj_is_active ? 'Active' : 'Inactive' }}
                        </span>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" style="text-align:center;">No subjects found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="dash-card h-100">
              <div class="card-title mb-1">Schedule Completion</div>
              <div class="card-sub mb-3">By program</div>

              @foreach($program as $programs)
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">{{ $programs['name'] }}</div>
                  <div class="workload-val" style="color:var(--{{ $programs['color'] }})">{{ $programs['percent'] }}%</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $programs['percent'] }}%;background:var(--{{ $programs['color'] }})"></div>
                </div>
              </div>
              @endforeach

              <div class="mt-3 pt-3" style="border-top:1px solid var(--border);">
                <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:8px;">Sections Summary</div>
                <div class="d-flex gap-2">
                  <div class="flex-fill text-center p-2 rounded-3" style="background:var(--green-light);">
                    <div style="font-size:20px;font-weight:800;color:var(--green);">{{ $scheduledCount }}</div>
                    <div style="font-size:10px;color:var(--green);font-weight:600;">Fully Scheduled</div>
                  </div>
                  <div class="flex-fill text-center p-2 rounded-3" style="background:var(--amber-light);">
                    <div style="font-size:20px;font-weight:800;color:var(--amber);">{{ $inProgressCount }}</div>
                    <div style="font-size:10px;color:var(--amber);font-weight:600;">In Progress</div>
                  </div>
                  <div class="flex-fill text-center p-2 rounded-3" style="background:var(--red-light);">
                    <div style="font-size:20px;font-weight:800;color:var(--red);">{{ $unscheduledCount }}</div>
                    <div style="font-size:10px;color:var(--red);font-weight:600;">Unscheduled</div>
                  </div>
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
                <div>
                  <div class="card-title">Subjects Offered</div>
                  <div class="card-sub">Current semester — all programs</div>
                </div>
                <span class="badge badge-blue">{{ $subject->count() }} Subjects</span>
              </div>
              <div class="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Code</th>
                      <th>Subject</th>
                      <th>Units</th>
                      <th>Program</th>
                      <th>Assigned Faculty</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($subject as $subjects)
                    <tr>
                      <td><span style="font-family:var(--mono);font-size:12px;">{{ $subjects->code }}</span></td>
                      <td><b>{{ $subjects->title }}</b></td>
                      <td>{{ $subjects->units }}</td>
                      <td>{{ $subjects->program }}</td>
                      <td>
                        @if($subjects->faculty_name)
                        {{ $subjects->faculty_name }}
                        @else
                        <span style="color:var(--red);">Unassigned</span>
                        @endif
                      </td>
                      <td>
                        <span class="badge badge-{{ match($subjects->status) {
                          'Active'     => 'green',
                          'Conflict'   => 'amber',
                          'No Faculty' => 'red',
                          default      => 'grey',
                      } }}">
                          {{ $subjects->status }}
                        </span>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="6" style="text-align:center;">No subjects found.</td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
          <!-- room for odc -->
          <div class="col-lg-4">
            <div class="dash-card h-100">
              <div class="card-title mb-1">Room Utilization</div>
              <div class="card-sub mb-3">This week</div>

              @foreach($room as $rooms)
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">{{ $rooms['name'] }}</div>
                  <div class="workload-val" style="color:var(--{{ $rooms['color'] }})">{{ $rooms['percent'] }}%</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $rooms['percent'] }}%;background:var(--{{ $rooms['color'] }})"></div>
                </div>
              </div>
              @endforeach

              <div class="d-flex justify-content-between mt-3 pt-3" style="border-top:1px solid var(--border);font-size:12px;">
                <div>
                  <div style="color:var(--text3);">Total Rooms</div>
                  <div style="font-weight:800;font-size:18px;color:var(--text);">{{ $totalRooms }}</div>
                </div>
                <div>
                  <div style="color:var(--text3);">In Use</div>
                  <div style="font-weight:800;font-size:18px;color:var(--blue);">{{ $roomsInUse }}</div>
                </div>
                <div>
                  <div style="color:var(--text3);">Available</div>
                  <div style="font-weight:800;font-size:18px;color:var(--green);">{{ $roomsAvailable }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- RECENT ACTIVITY -->
        <div class="dash-card mb-4">
          <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
              <div class="card-title">Recent System Activity</div>
              <div class="card-sub">Latest actions across all users</div>
            </div>
          </div>
          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Time</th>
                  <th>User</th>
                  <th>Action</th>
                  <th>Details</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($audit_log as $log)
                <tr>
                  <td style="font-family:var(--mono);font-size:11px;color:var(--text3);">
                    {{ $log->created_at->format('h:i A') }}
                  </td>
                  <td><b>{{ $log->user_name }}</b></td>
                  <td>{{ $log->action }}</td>
                  <td>{{ $log->details }}</td>
                  <td>
                    <span class="badge badge-{{ match($log->status) {
                      'Success' => 'green',
                      'Info'    => 'blue',
                      'Warning' => 'red',
                      default   => 'grey',
                  } }}">
                      {{ $log->status }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" style="text-align:center;">No recent activity.</td>
                </tr>
                @endforelse
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
