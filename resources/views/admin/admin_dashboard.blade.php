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
        <button class="btn-notif" onclick="toggleNotif()">
          Notifications <span class="notif-badge" id="notif-count">3</span>
        </button>
        <div class="notif-dropdown" id="notif-dropdown">
          <div class="p-3 fw-bold" style="font-size:14px;border-bottom:1px solid var(--border);">Notifications</div>
          <div id="notif-list"></div>
          <div class="p-2 text-center" style="border-top:1px solid var(--border);">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
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
                <button onclick="prevQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:#fff;cursor:pointer;font-size:13px;">‹</button>
                <div id="quote-dots" class="d-flex gap-2"></div>
                <button onclick="nextQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,.1);border:none;color:#fff;cursor:pointer;font-size:13px;">›</button>
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
          <div class="card h-100">
            <div class="card-header">
              <div>
                <div class="card-title">User Accounts</div>
                <div class="card-sub">All registered system users</div>
              </div>
              <span class="badge badge-blue">{{ $users->count() }} Total</span>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Department</th>
                    <th>Employment</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($users as $user)
                    <tr>
                      <td><b>{{ $user->name }}</b></td>
                      <td>
                        @if ($user->role === 'Dean')
                          <span class="badge badge-blue">Dean</span>
                        @elseif ($user->role === 'Dept. Chair')
                          <span class="badge badge-amber">Dept. Chair</span>
                        @elseif ($user->role === 'Faculty')
                          <span class="badge badge-grey">Faculty</span>
                        @else
                          <span class="badge badge-teal">{{ $user->role }}</span>
                        @endif
                      </td>
                      <td>{{ $user->department }}</td>
                      <td>{{ $user->employment }}</td>
                      <td>
                        @if ($user->status === 'Active')
                          <span class="badge badge-green">Active</span>
                        @elseif ($user->status === 'Pending')
                          <span class="badge badge-amber">Pending</span>
                        @else
                          <span class="badge badge-red">Inactive</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" style="text-align:center;color:var(--text3);padding:24px;">
                        No users found.
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
        </div>

      </div>
          <div class="card flex-grow-1">
            <div class="card-title mb-3">Role Distribution</div>

            @php
              $total = $roleCounts->sum();
            @endphp

            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Faculty Members</div>
                <div class="workload-val" style="color:var(--blue)">{{ $roleCounts['Faculty'] ?? 0 }}</div>
              </div>
              <div class="workload-bar">
                <div class="workload-fill" style="width:{{ $total > 0 ? round(($roleCounts['Faculty'] ?? 0) / $total * 100) : 0 }}%;background:var(--blue)"></div>
              </div>
            </div>

            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Dept. Chairs</div>
                <div class="workload-val" style="color:var(--amber)">{{ $roleCounts['Dept. Chair'] ?? 0 }}</div>
              </div>
              <div class="workload-bar">
                <div class="workload-fill" style="width:{{ $total > 0 ? round(($roleCounts['Dept. Chair'] ?? 0) / $total * 100) : 0 }}%;background:var(--amber)"></div>
              </div>
            </div>

            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Dean</div>
                <div class="workload-val" style="color:var(--teal)">{{ $roleCounts['Dean'] ?? 0 }}</div>
              </div>
              <div class="workload-bar">
                <div class="workload-fill" style="width:{{ $total > 0 ? round(($roleCounts['Dean'] ?? 0) / $total * 100) : 0 }}%;background:var(--teal)"></div>
              </div>
            </div>

            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">Tech Admin</div>
                <div class="workload-val" style="color:var(--navy)">{{ $roleCounts['Admin'] ?? 0 }}</div>
              </div>
              <div class="workload-bar">
                <div class="workload-fill" style="width:{{ $total > 0 ? round(($roleCounts['Admin'] ?? 0) / $total * 100) : 0 }}%;background:var(--navy)"></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-3 mb-4">

        {{-- Sections Table --}}
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Current Sections</div>
                <div class="card-sub">AY 2025–2026 · 1st Semester</div>
              </div>
              <span class="badge badge-blue">{{ $sections->count() }} Total</span>
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
                  @forelse ($sections as $section)
                    <tr>
                      <td><b>{{ $section->name }}</b></td>
                      <td>{{ $section->program }}</td>
                      <td>{{ $section->year_level }}</td>
                      <td>{{ $section->student_count }}</td>
                      <td>{{ $section->subject_count }}</td>
                      <td>
                        @if ($section->status === 'Scheduled')
                          <span class="badge badge-green">Scheduled</span>
                        @elseif ($section->status === 'In Progress')
                          <span class="badge badge-amber">In Progress</span>
                        @else
                          <span class="badge badge-red">Unscheduled</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" style="text-align:center;color:var(--text3);padding:24px;">
                        No sections found.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-title" style="margin-bottom:4px;">Schedule Completion</div>
            <div class="card-sub" style="margin-bottom:16px;">By program</div>

            @foreach ($completionByProgram as $program => $pct)
              @php
                $color = $pct >= 100 ? 'var(--green)' : ($pct >= 60 ? 'var(--amber)' : 'var(--red)');
              @endphp
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">{{ $program }}</div>
                  <div class="workload-val" style="color:{{ $color }}">{{ $pct }}%</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $pct }}%;background:{{ $color }}"></div>
                </div>
              </div>
            @endforeach

            <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border);">
              <div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:8px;">Sections Summary</div>
              <div style="display:flex;gap:8px;">
                <div style="flex:1;text-align:center;padding:8px;border-radius:10px;background:var(--green-light);">
                  <div style="font-size:20px;font-weight:800;color:var(--green);">{{ $scheduledCount }}</div>
                  <div style="font-size:10px;color:var(--green);font-weight:600;">Fully Scheduled</div>
                </div>
                <div style="flex:1;text-align:center;padding:8px;border-radius:10px;background:var(--amber-light);">
                  <div style="font-size:20px;font-weight:800;color:var(--amber);">{{ $inProgressCount }}</div>
                  <div style="font-size:10px;color:var(--amber);font-weight:600;">In Progress</div>
                </div>
                <div style="flex:1;text-align:center;padding:8px;border-radius:10px;background:var(--red-light);">
                  <div style="font-size:20px;font-weight:800;color:var(--red);">{{ $unscheduledCount }}</div>
                  <div style="font-size:10px;color:var(--red);font-weight:600;">Unscheduled</div>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>

      <div class="row g-3 mb-4">

        {{-- Subjects Table --}}
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <div>
                <div class="card-title">Subjects Offered</div>
                <div class="card-sub">Current semester — all programs</div>
              </div>
              <span class="badge badge-blue">{{ $subjects->count() }} Subjects</span>
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
                  @forelse ($subjects as $subject)
                    <tr>
                      <td><span style="font-family:var(--mono);font-size:12px;">{{ $subject->code }}</span></td>
                      <td><b>{{ $subject->name }}</b></td>
                      <td>{{ $subject->units }}</td>
                      <td>{{ $subject->program }}</td>
                      <td>
                        @if ($subject->faculty)
                          {{ $subject->faculty->name }}
                        @else
                          <span style="color:var(--red);">Unassigned</span>
                        @endif
                      </td>
                      <td>
                        @if ($subject->status === 'Active')
                          <span class="badge badge-green">Active</span>
                        @elseif ($subject->status === 'Conflict')
                          <span class="badge badge-amber">Conflict</span>
                        @elseif ($subject->status === 'No Faculty')
                          <span class="badge badge-red">No Faculty</span>
                        @else
                          <span class="badge badge-grey">{{ $subject->status }}</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="6" style="text-align:center;color:var(--text3);padding:24px;">
                        No subjects found.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card h-100">
            <div class="card-title" style="margin-bottom:4px;">Room Utilization</div>
            <div class="card-sub" style="margin-bottom:16px;">This week</div>

            @foreach ($roomUtilization as $room)
              @php
                $color = $room['pct'] >= 90
                  ? 'var(--red)'
                  : ($room['pct'] >= 70 ? 'var(--amber)' : ($room['pct'] >= 50 ? 'var(--green)' : 'var(--blue)'));
              @endphp
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">{{ $room['name'] }}</div>
                  <div class="workload-val" style="color:{{ $color }}">{{ $room['pct'] }}%</div>
                </div>
                <div class="workload-bar">
                  <div class="workload-fill" style="width:{{ $room['pct'] }}%;background:{{ $color }}"></div>
                </div>
              </div>
            @endforeach

            <div style="display:flex;justify-content:space-between;margin-top:16px;padding-top:16px;border-top:1px solid var(--border);font-size:12px;">
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

      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
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
              @forelse ($activityLogs as $log)
                <tr>
                  <td style="font-family:var(--mono);font-size:11px;color:var(--text3);">
                    {{ $log->created_at->format('h:i A') }}
                  </td>
                  <td><b>{{ $log->user?->name ?? 'System' }}</b></td>
                  <td>{{ $log->action }}</td>
                  <td>{{ $log->details }}</td>
                  <td>
                    @if ($log->status === 'Success')
                      <span class="badge badge-green">Success</span>
                    @elseif ($log->status === 'Warning')
                      <span class="badge badge-red">Warning</span>
                    @elseif ($log->status === 'Info')
                      <span class="badge badge-blue">Info</span>
                    @else
                      <span class="badge badge-grey">{{ $log->status }}</span>
                    @endif
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" style="text-align:center;color:var(--text3);padding:24px;">
                    No recent activity.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast-custom" id="toast">✅ <span id="toast-msg"></span></div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
/* ──  NOTIFICATIONS ── */
const NOTIFS = [
  { dot:'var(--red)',   text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.',      time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)',  text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.',     time:'Today, 06:00 AM', unread:false },
  { dot:'var(--blue)',  text:'<b>User Created</b> — New faculty account created for Liza Cruz.',   time:'Yesterday, 7:55 AM', unread:false },
];

(function renderNotifs() {
  const list = document.getElementById('notif-list');
  list.innerHTML = NOTIFS.map(n => `
    <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div>
        <div class="notif-drop-text">${n.text}</div>
        <div class="notif-drop-time">${n.time}</div>
      </div>
    </div>`).join('');
  updateBadge();
})();

let notifOpen = false;
function toggleNotif() {
  notifOpen = !notifOpen;
  document.getElementById('notif-dropdown').classList.toggle('open', notifOpen);
}
document.addEventListener('click', e => {
  if (!e.target.closest('.notif-wrap')) {
    notifOpen = false;
    document.getElementById('notif-dropdown').classList.remove('open');
  }
});
function markRead(el)  { el.classList.remove('unread'); updateBadge(); }
function markAllRead() { document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread')); updateBadge(); }
function updateBadge() {
  const n = document.querySelectorAll('.notif-drop-item.unread').length;
  const b = document.getElementById('notif-count');
  b.textContent = n; b.style.display = n > 0 ? 'inline' : 'none';
}

/* ── QUOTES ── */
const QUOTES = [
  { text:'"Education is the most powerful weapon which you can use to change the world."', author:'— Nelson Mandela' },
  { text:'"The beautiful thing about learning is that no one can take it away from you."', author:'— B.B. King' },
  { text:'"An investment in knowledge pays the best interest."', author:'— Benjamin Franklin' },
  { text:'"Education is not the filling of a pail, but the lighting of a fire."', author:'— W.B. Yeats' },
  { text:'"Live as if you were to die tomorrow. Learn as if you were to live forever."', author:'— Mahatma Gandhi' },
  { text:'"It does not matter how slowly you go as long as you do not stop."', author:'— Confucius' },
  { text:'"The capacity to learn is a gift; the ability to learn is a skill; the willingness to learn is a choice."', author:'— Brian Herbert' },
];
let qi = Math.floor(Math.random() * QUOTES.length), qt = null;
function renderQuote() {
  const q = QUOTES[qi];
  const t = document.getElementById('quote-text'), a = document.getElementById('quote-author'), d = document.getElementById('quote-dots');
  t.style.opacity='0'; a.style.opacity='0';
  setTimeout(() => { t.textContent=q.text; a.textContent=q.author; t.style.transition=a.style.transition='opacity .5s'; t.style.opacity=a.style.opacity='1'; }, 300);
  d.innerHTML='';
  QUOTES.forEach((_,i) => {
    const dot = document.createElement('div');
    dot.className='quote-dot';
    dot.style.cssText=`width:6px;height:6px;background:${i===qi?'rgba(255,255,255,.9)':'rgba(255,255,255,.25)'};`;
    dot.onclick=()=>{ qi=i; renderQuote(); resetQT(); };
    d.appendChild(dot);
  });
}
function nextQuote(){ qi=(qi+1)%QUOTES.length; renderQuote(); resetQT(); }
function prevQuote(){ qi=(qi-1+QUOTES.length)%QUOTES.length; renderQuote(); resetQT(); }
function resetQT()  { clearInterval(qt); qt=setInterval(nextQuote,6000); }
renderQuote(); resetQT();

/* ── TOAST ── */
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

/* ── SIDEBAR NAV active state ── */
document.querySelectorAll('.nav-item').forEach(item => {
  item.addEventListener('click', function() {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    this.classList.add('active');
  });
});
</script>
</body>
</html>
