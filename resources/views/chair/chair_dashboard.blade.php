<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
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
          <div class="stat-label">Faculty at Max Load</div>
          <div class="stat-value">{{ $facultyAtMaxLoadCount }}</div>
          <div class="stat-sub">{{ $facultyAtMaxLoadCount === 1 ? 'faculty member' : 'faculty members' }} at their weekly teaching-hour cap</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-cyan-600"></div>
          <div class="stat-label">Sections</div>
          <div class="stat-value">{{ $totalSections }}</div>
          <div class="stat-sub">All year levels</div>
        </div>
      </div>

      <!-- Faculty Load + Workload Distribution -->
      <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <input id="dashboard-faculty-search" type="search" placeholder="Search faculty..." aria-label="Search dashboard faculty"
          class="w-full max-w-sm rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        <div class="flex items-center gap-3 text-xs text-slate-500">
          <span id="dashboard-faculty-page-info" aria-live="polite"></span>
          <button type="button" id="dashboard-faculty-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
          <button type="button" id="dashboard-faculty-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
        </div>
      </div>
      <div class="two-col items-start">
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Faculty Load Summary</div>
              <div class="card-sub">{{ $deptChair->program->prog_code ?? '' }} &middot; Day caps follow designation; Evening is 30h; part-time is 30h per shift</div>
            </div>
          </div>
          <table>
            <thead>
              <tr><th>Faculty</th><th>Special Position</th><th>Day Load</th><th>Evening Load</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse($facultyLoad as $fl)
                @php
                  $dayColor = match($fl['day']['status']) {
                      'Near Max'   => 'text-amber-600',
                      'Full'       => 'text-red-600',
                      'Part-time'  => 'text-cyan-600',
                      default      => 'text-green-600',
                  };
                  $eveningColor = match($fl['evening']['status']) {
                      'Near Max'   => 'text-amber-600',
                      'Full'       => 'text-red-600',
                      'Part-time'  => 'text-cyan-600',
                      default      => 'text-green-600',
                  };
                  $dayBadge = match($fl['day']['status']) {
                      'Near Max'  => 'badge-amber',
                      'Full'      => 'badge-red',
                      'Part-time' => 'badge-teal',
                      default     => 'badge-green',
                  };
                  $eveningBadge = match($fl['evening']['status']) {
                      'Near Max'  => 'badge-amber',
                      'Full'      => 'badge-red',
                      'Part-time' => 'badge-teal',
                      default     => 'badge-green',
                  };
                @endphp
                <tr data-dashboard-faculty data-person-key="{{ $fl['fac_id'] }}" data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.($fl['special_position'] ?? '').' '.$fl['day']['status'].' '.$fl['evening']['status'].' '.$fl['day']['hours'].' '.$fl['evening']['hours']) }}">
                  <td><b>{{ $fl['name'] }}</b></td>
                  <td>{{ $fl['special_position'] ?: '—' }}</td>
                  @foreach (['day', 'evening'] as $shift)
                    @php $color = $shift === 'day' ? $dayColor : $eveningColor; @endphp
                    <td><span class="font-mono font-bold {{ $color }}">{{ $fl[$shift]['hours'] }}h</span><span class="text-xs text-slate-400"> / {{ $fl[$shift]['max_hours'] }}h</span><div class="text-xs text-slate-500">{{ $fl[$shift]['remaining'] }}h left</div></td>
                  @endforeach
                  <td><div><span class="badge {{ $dayBadge }}">Day: {{ $fl['day']['status'] }}</span></div><div class="mt-1"><span class="badge {{ $eveningBadge }}">Evening: {{ $fl['evening']['status'] }}</span></div></td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-slate-400 py-4">No faculty in this department yet.</td></tr>
              @endforelse
              <tr id="dashboard-table-no-results" class="hidden"><td colspan="5" class="text-center text-slate-400 py-4">No faculty match your search.</td></tr>
            </tbody>
          </table>
        </div>

        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Workload Distribution</div>
          <div class="card-sub">Day and Evening teaching hours per week</div>
            </div>
          </div>
          @forelse($facultyLoad as $fl)
            <div class="workload-item" data-dashboard-faculty data-person-key="{{ $fl['fac_id'] }}" data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.($fl['special_position'] ?? '').' '.$fl['day']['status'].' '.$fl['evening']['status']) }}">
              <div class="workload-name mb-2">{{ $fl['name'] }}{{ $fl['employment'] === 'part_time' ? ' (Part-time)' : '' }}{{ $fl['special_position'] ? ' · '.$fl['special_position'] : '' }}</div>
              @if ($fl['employment'] === 'part_time')
                @php
                  $hours = $fl['day']['hours'] + $fl['evening']['hours'];
                  $remaining = max(0, 30 - $hours);
                  $percent = min(100, round($hours / 30 * 100));
                  $state = $hours >= 30 ? 'Full' : ($hours >= 27 ? 'Near Max' : 'Part-time');
                  $barColor = match($state) { 'Near Max' => 'bg-amber-600', 'Full' => 'bg-red-600', default => 'bg-cyan-600' };
                  $textColor = match($state) { 'Near Max' => 'text-amber-600', 'Full' => 'text-red-600', default => 'text-cyan-600' };
                @endphp
                <div class="workload-header mt-1">
                  <div class="text-xs font-semibold text-slate-600">Total</div>
                  <div class="workload-val {{ $textColor }}">{{ $hours }}/30h</div>
                </div>
                <div class="workload-bar"><div class="workload-fill {{ $barColor }}" style="width:{{ $percent }}%"></div></div>
                <div class="text-[11px] {{ $textColor }} mt-1">{{ $remaining }} teaching hours remaining</div>
              @else
                @foreach (['day' => 'Day', 'evening' => 'Evening'] as $shift => $label)
                  @php
                    $state = $fl[$shift]['status'];
                    $barColor = match($state) { 'Near Max' => 'bg-amber-600', 'Full' => 'bg-red-600', 'Part-time' => 'bg-cyan-600', default => 'bg-green-600' };
                    $textColor = match($state) { 'Near Max' => 'text-amber-600', 'Full' => 'text-red-600', 'Part-time' => 'text-cyan-600', default => 'text-green-600' };
                  @endphp
                  <div class="workload-header mt-1">
                    <div class="text-xs font-semibold text-slate-600">{{ $label }}</div>
                    <div class="workload-val {{ $textColor }}">{{ $fl[$shift]['hours'] }}/{{ $fl[$shift]['max_hours'] }}h</div>
                  </div>
                  <div class="workload-bar"><div class="workload-fill {{ $barColor }}" style="width:{{ $fl[$shift]['percent'] }}%"></div></div>
                  <div class="text-[11px] {{ $textColor }} mt-1">{{ $fl[$shift]['remaining'] }} teaching hours remaining</div>
                  @if ($shift === 'day')<hr class="my-2 border-slate-100">@endif
                @endforeach
              @endif
            </div>
          @empty
            <div class="text-[13px] text-slate-400 py-4 text-center">No workload data yet.</div>
          @endforelse
          <div id="dashboard-list-no-results" class="hidden text-[13px] text-slate-400 py-4 text-center">No faculty match your search.</div>
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <div>
            <div class="card-title">Weekly Class Pattern</div>
            <div class="card-sub">Active scheduled classes across {{ $totalFaculty }} faculty members</div>
          </div>
          <span class="badge badge-blue">{{ $weeklyScheduleCounts->sum('count') }} classes</span>
        </div>
        @php $weeklyMax = max(1, $weeklyScheduleCounts->max('count')); @endphp
        <div class="grid grid-cols-7 gap-2 pt-3" role="img" aria-label="Weekly classes by day">
          @foreach($weeklyScheduleCounts as $item)
            <div class="flex h-32 flex-col items-center justify-end gap-2 rounded-xl bg-slate-50 px-2 pt-2">
              <span class="text-xs font-bold text-slate-600">{{ $item['count'] }}</span>
              <div class="flex h-20 w-full items-end justify-center">
                <div class="w-3/5 rounded-t-lg bg-blue-600 transition-all" style="height:{{ $item['count'] ? max(8, round($item['count'] / $weeklyMax * 100)) : 0 }}%"></div>
              </div>
              <span class="text-[10px] font-semibold text-slate-500">{{ $item['day'] }}</span>
            </div>
          @endforeach
        </div>
      </div>

      <div class="card mt-4">
        <div class="card-header">
          <div>
            <div class="card-title">My Recent Activity</div>
            <div class="card-sub">Your sign-ins and recent changes</div>
          </div>
          <div id="dashboard-activity-pagination" class="flex items-center gap-2 text-xs text-slate-500">
            <span id="dashboard-activity-page-info" aria-live="polite"></span>
            <button type="button" id="dashboard-activity-prev" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
            <button type="button" id="dashboard-activity-next" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
          </div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead><tr><th>Time</th><th>Action</th><th>Details</th></tr></thead>
            <tbody>
              @forelse($recentActivity as $activity)
                @php
                  $activityDetail = preg_replace('/^Role: [^;]+; /', '', (string) $activity->al_description);
                  $activityDetail = preg_replace('/; HTTP \d{3}$/', '', $activityDetail);
                @endphp
                <tr data-dashboard-activity>
                  <td class="whitespace-nowrap text-xs text-slate-400">{{ \Illuminate\Support\Carbon::parse($activity->al_created_at)->format('M j, Y g:i A') }}</td>
                  <td class="font-semibold">{{ $activity->al_action }}</td>
                  <td class="text-xs text-slate-500">{{ $activityDetail ?: $activity->al_target_table }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="py-5 text-center text-sm text-slate-400">No activity recorded yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end app-main -->
</div><!-- end app-shell -->

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
(() => {
  const search = document.getElementById('dashboard-faculty-search');
  const items = [...document.querySelectorAll('[data-dashboard-faculty]')];
  const facultyGroups = [...items.reduce((groups, item) => {
    const key = item.dataset.personKey;
    if (!groups.has(key)) groups.set(key, []);
    groups.get(key).push(item);
    return groups;
  }, new Map()).values()];
  const tableEmpty = document.getElementById('dashboard-table-no-results');
  const listEmpty = document.getElementById('dashboard-list-no-results');
  const info = document.getElementById('dashboard-faculty-page-info');
  const previous = document.getElementById('dashboard-faculty-prev');
  const next = document.getElementById('dashboard-faculty-next');
  const pageSize = 4;
  let page = 0;

  function renderFacultyPage() {
    const query = search.value.trim().toLowerCase();
    const matches = facultyGroups.filter(group => group[0].dataset.search.includes(query));
    const pages = Math.max(1, Math.ceil(matches.length / pageSize));
    page = Math.min(page, pages - 1);
    const first = page * pageSize;
    items.forEach(item => { item.style.display = 'none'; });
    matches.slice(first, first + pageSize).flat().forEach(item => { item.style.display = ''; });
    const hasMatches = matches.length > 0;
    tableEmpty.classList.toggle('hidden', hasMatches || facultyGroups.length === 0);
    listEmpty.classList.toggle('hidden', hasMatches || facultyGroups.length === 0);
    info.textContent = hasMatches ? `Showing ${first + 1}–${Math.min(first + pageSize, matches.length)} of ${matches.length}` : 'Showing 0 of 0';
    previous.disabled = page === 0;
    next.disabled = page >= pages - 1;
  }

  search.addEventListener('input', () => { page = 0; renderFacultyPage(); });
  previous.addEventListener('click', () => { page--; renderFacultyPage(); });
  next.addEventListener('click', () => { page++; renderFacultyPage(); });
  renderFacultyPage();
})();

(() => {
  const rows = [...document.querySelectorAll('[data-dashboard-activity]')];
  const pagination = document.getElementById('dashboard-activity-pagination');
  const info = document.getElementById('dashboard-activity-page-info');
  const previous = document.getElementById('dashboard-activity-prev');
  const next = document.getElementById('dashboard-activity-next');
  const pageSize = 4;
  let page = 0;
  if (!pagination || !info || !previous || !next) return;
  pagination.classList.toggle('hidden', rows.length <= pageSize);

  function renderActivityPage() {
    const pages = Math.max(1, Math.ceil(rows.length / pageSize));
    page = Math.min(Math.max(page, 0), pages - 1);
    const start = page * pageSize;
    rows.forEach((row, index) => { row.classList.toggle('hidden', index < start || index >= start + pageSize); });
    info.textContent = rows.length ? `Showing ${start + 1}–${Math.min(start + pageSize, rows.length)} of ${rows.length}` : 'No activity';
    previous.disabled = page === 0;
    next.disabled = page >= pages - 1;
  }

  previous.addEventListener('click', () => { page--; renderActivityPage(); });
  next.addEventListener('click', () => { page++; renderActivityPage(); });
  renderActivityPage();
})();

function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
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
