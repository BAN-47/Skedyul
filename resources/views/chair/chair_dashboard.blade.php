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
      <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <input id="dashboard-faculty-search" type="search" placeholder="Search faculty..." aria-label="Search dashboard faculty"
          class="w-full max-w-sm rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
        <div class="flex items-center gap-3 text-xs text-slate-500">
          <span id="dashboard-faculty-page-info" aria-live="polite"></span>
          <button type="button" id="dashboard-faculty-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
          <button type="button" id="dashboard-faculty-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
        </div>
      </div>
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
                <tr data-dashboard-faculty data-person-key="{{ $fl['fac_id'] }}" data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.$fl['status'].' '.$fl['hours']) }}">
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
              <tr id="dashboard-table-no-results" class="hidden"><td colspan="4" class="text-center text-slate-400 py-4">No faculty match your search.</td></tr>
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
            <div class="workload-item" data-dashboard-faculty data-person-key="{{ $fl['fac_id'] }}" data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.$fl['status'].' '.$fl['hours']) }}">
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
          <div id="dashboard-list-no-results" class="hidden text-[13px] text-slate-400 py-4 text-center">No faculty match your search.</div>
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
  const pageSize = 10;
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
