<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Department Chair Dashboard</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

@include('partials.chair_sidebar')

  <!-- ══════════ MAIN ══════════ -->
<div class="app-main relative">

    @include('partials.chair_header', [
        'title' => 'Department Chair Dashboard',
        'badgeText' => trim(collect([
            $deptChair->program->prog_code ?? $deptChair->department->dept_code ?? 'DEPT',
            $academicYear?->ay_academic_year,
            $semester?->sem_name,
        ])->filter()->implode(' · '))
    ])

    <!-- ══════════ DASHBOARD PAGE ══════════ -->
<div class="page-content">
      <!-- Page heading -->
      <div class="flex items-center gap-3 mb-6">
        <div class="flex-1">
          <div class="text-[22px] font-extrabold">Good morning, Chair {{ $deptChair->dc_last_name }}</div>
          <div class="text-[13px] text-slate-400 mt-0.5">
            {{ $deptChair->department->dept_name ?? 'Department' }}
            @if($academicYear) &middot; AY {{ $academicYear->ay_academic_year }} @endif
            @if($semester) &middot; {{ $semester->sem_name }} @endif
          </div>
        </div>
      </div>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card">
          <div class="stat-card-bar bg-blue-600"></div>
          <div class="stat-label">Faculty ({{ $deptChair->program->prog_code ?? '' }})</div>
          <div class="stat-value">{{ $totalFaculty }}</div>
          <div class="stat-sub">Under your department</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-green-600"></div>
          <div class="stat-label">Subjects Plotted</div>
          <div class="stat-value">{{ $subjectsPlotted }}</div>
          <div class="stat-sub">of {{ $totalSubjects }} total</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-red-600"></div>
          <div class="stat-label">Conflicts</div>
          <div class="stat-value" id="stat-conflicts">{{ $conflictsCount }}</div>
          <div class="stat-sub" id="stat-conflicts-sub">
            {{ $conflictsCount > 0 ? 'Requires resolution' : 'All clear' }}
          </div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-cyan-600"></div>
          <div class="stat-label">Sections</div>
          <div class="stat-value">{{ $totalSections }}</div>
          <div class="stat-sub">All year levels</div>
        </div>
      </div>

      <!-- Faculty Load + Workload Distribution -->
      <div class="two-col">
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Faculty Load Summary</div>
              <div class="card-sub">{{ $deptChair->program->prog_code ?? '' }} &middot; Max 30 units/week</div>
            </div>
          </div>
          <table>
            <thead>
              <tr><th>Faculty</th><th>Load</th><th>Remaining</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse($facultyLoad as $fl)
                @php
                  $loadColor = match($fl['status']) {
                      'Near Max'   => 'text-amber-600',
                      'Full'       => 'text-red-600',
                      'Part-time'  => 'text-cyan-600',
                      default      => 'text-green-600',
                  };
                  $statusBadge = match($fl['status']) {
                      'Near Max'  => 'badge-amber',
                      'Full'      => 'badge-red',
                      'Part-time' => 'badge-teal',
                      default     => 'badge-green',
                  };
                @endphp
                <tr>
                  <td><b>{{ $fl['name'] }}</b></td>
                  <td><span class="font-mono font-bold {{ $loadColor }}">{{ $fl['hours'] }}u</span></td>
                  <td>
                    @if($fl['employment'] === 'part_time')
                      <span class="badge badge-grey">Part-time</span>
                    @else
                      <span class="badge {{ $statusBadge === 'badge-red' ? 'badge-red' : ($statusBadge === 'badge-amber' ? 'badge-amber' : 'badge-blue') }}">
                        {{ $fl['remaining'] }}u left
                      </span>
                    @endif
                  </td>
                  <td><span class="badge {{ $statusBadge }}">{{ $fl['status'] }}</span></td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-slate-400 py-4">No faculty in this department yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Workload Distribution</div>
              <div class="card-sub">Units per week &middot; Max 30</div>
            </div>
          </div>
          @forelse($facultyLoad as $fl)
            @php
              $barColor = match($fl['status']) {
                  'Near Max'  => 'bg-amber-600',
                  'Full'      => 'bg-red-600',
                  'Part-time' => 'bg-cyan-600',
                  default     => 'bg-green-600',
              };
              $textColor = match($fl['status']) {
                  'Near Max'  => 'text-amber-600',
                  'Full'      => 'text-red-600',
                  'Part-time' => 'text-cyan-600',
                  default     => 'text-green-600',
              };
            @endphp
            <div class="workload-item">
              <div class="workload-header">
                <div class="workload-name">{{ $fl['name'] }}{{ $fl['employment'] === 'part_time' ? ' (Part-time)' : '' }}</div>
                <div class="workload-val {{ $textColor }}">{{ $fl['hours'] }}/30u</div>
              </div>
              <div class="workload-bar"><div class="workload-fill {{ $barColor }}" style="width:{{ $fl['percent'] }}%"></div></div>
              <div class="text-[11px] {{ $fl['employment'] === 'part_time' ? 'text-slate-400' : $textColor }} mt-1">
                @if($fl['employment'] === 'part_time')
                  Part-time &mdash; verify additional load with Dean
                @else
                  {{ $fl['remaining'] }} units remaining{{ $fl['status'] === 'Near Max' ? ' — near maximum' : '' }}
                @endif
              </div>
            </div>
          @empty
            <div class="text-[13px] text-slate-400 py-4 text-center">No workload data yet.</div>
          @endforelse
        </div>
      </div>

    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end app-main -->
</div><!-- end app-shell -->

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}

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

function markAllNotificationsRead() {
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  fetch('{{ route("chair.notifications.readAll") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': csrf,
      'Accept': 'application/json',
    },
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.querySelectorAll('.notif-item').forEach(el => el.classList.add('opacity-50'));
      document.getElementById('notif-dot').classList.add('hidden');
      document.getElementById('notif-count-badge').textContent = '0 new';
      document.getElementById('notif-count-badge').className = 'badge badge-grey';
      showToast('All notifications marked as read.');
    }
  })
  .catch(() => showToast('Could not update notifications. Try again.'));
}

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>
</body>
</html>