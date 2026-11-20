<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Department Chair Dashboard</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
  .chair-dashboard { background: #f4f7fc; }
  .chair-dashboard .page-content { padding: 1.75rem; }
  .chair-dashboard-hero {
    position: relative;
    overflow: hidden;
    margin: -.15rem 0 1.5rem;
    padding: 1.45rem 1.6rem;
    border: 1px solid #dbeafe;
    border-radius: 1.25rem;
    background: linear-gradient(110deg, #fff 0%, #f1f7ff 60%, #eefcf8 100%);
    box-shadow: 0 8px 24px rgba(30, 64, 175, .045);
  }
  .chair-dashboard-hero::after {
    content: '';
    position: absolute;
    top: -3.5rem;
    right: -1.5rem;
    width: 12rem;
    height: 12rem;
    border-radius: 9999px;
    background: radial-gradient(circle, rgba(59, 130, 246, .12), rgba(16, 185, 129, .05) 68%, transparent 70%);
    pointer-events: none;
  }
  .chair-dashboard .stat-card {
    min-height: 132px;
    padding: 1.25rem 1.35rem;
    box-shadow: 0 5px 18px rgba(15, 23, 42, .035);
    transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
  }
  .chair-dashboard .stat-card:hover {
    transform: translateY(-3px);
    border-color: #bfdbfe;
    box-shadow: 0 14px 26px rgba(30, 64, 175, .09);
  }
  .chair-dashboard .stat-card-bar { height: 4px; }
  .chair-dashboard .stat-icon {
    top: auto;
    right: 1rem;
    bottom: 1rem;
    display: grid;
    width: 2.65rem;
    height: 2.65rem;
    place-items: center;
    border-radius: .9rem;
    background: #eff6ff;
    color: #2563eb;
    font-size: 1.25rem;
    opacity: 1;
  }
  .chair-dashboard .stat-icon.icon-green { background: #ecfdf5; color: #059669; }
  .chair-dashboard .stat-icon.icon-red { background: #fff1f2; color: #e11d48; }
  .chair-dashboard .stat-icon.icon-cyan { background: #ecfeff; color: #0891b2; }
  .chair-dashboard .stat-label { padding-right: 3rem; color: #64748b; }
  .chair-dashboard .stat-value { font-size: 2rem; }
  .chair-dashboard .card {
    min-width: 0;
    box-shadow: 0 6px 22px rgba(15, 23, 42, .035);
    transition: box-shadow .2s ease, border-color .2s ease;
  }
  .chair-dashboard .card:hover { border-color: #cbdcf4; box-shadow: 0 12px 26px rgba(15, 23, 42, .065); }
  .chair-dashboard .rank-card { transition: transform .2s ease, box-shadow .2s ease; }
  .chair-dashboard .rank-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(15, 23, 42, .10); }
  .chair-dashboard .faculty-summary-table { border-collapse: separate; border-spacing: 0; }
  .chair-dashboard .faculty-summary-table { min-width: 720px; }
  .chair-dashboard .faculty-summary-scroll { max-width: 100%; overflow-x: auto; overscroll-behavior-inline: contain; }
  .chair-dashboard .faculty-summary-table thead th { background: #f1f5fb; color: #64748b; font-size: .65rem; letter-spacing: .045em; }
  .chair-dashboard .faculty-summary-table tbody tr { transition: background-color .16s ease; }
  .chair-dashboard .faculty-summary-table tbody tr:nth-child(even) { background: #fbfdff; }
  .chair-dashboard .faculty-summary-table tbody tr:hover { background: #eff6ff; }
  .chair-dashboard .load-avatar { display: grid; width: 2rem; height: 2rem; flex: 0 0 auto; place-items: center; border-radius: .75rem; font-size: .68rem; font-weight: 800; }
  .chair-dashboard .load-value-chip { display: inline-flex; min-width: 3.1rem; justify-content: center; border-radius: .6rem; padding: .3rem .45rem; font-size: .72rem; font-weight: 800; }
  .chair-dashboard .timetable-day { min-height: 10rem; border: 1px solid #e2e8f0; border-radius: 1rem; background: #fff; overflow: hidden; }
  .chair-dashboard .timetable-day-title { border-bottom: 1px solid #e2e8f0; background: #f8fafc; padding: .65rem .8rem; color: #475569; font-size: .72rem; font-weight: 800; text-transform: uppercase; }
  .chair-dashboard .timetable-event { border: 1px solid #a7f3d0; border-left: 3px solid #10b981; border-radius: .75rem; background: linear-gradient(135deg, #ecfdf5, #f0fdfa); padding: .65rem; }
  .chair-dashboard .timetable-event.evening { border-color: #c4b5fd; border-left-color: #7c3aed; background: linear-gradient(135deg, #f5f3ff, #faf5ff); }
  .chair-dashboard .timetable-event a { color: #2563eb; font-size: .65rem; font-weight: 800; text-decoration: none; }
  .chair-dashboard .timetable-event a:hover { text-decoration: underline; }
  .chair-dashboard .weekly-day-card { min-width: 5.7rem; border: 1px solid #e5ebf5; background: linear-gradient(180deg, #fff, #f8faff); transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease; }
  .chair-dashboard .weekly-day-card:hover { transform: translateY(-2px); border-color: #bfdbfe; box-shadow: 0 8px 18px rgba(37, 99, 235, .08); }
  .chair-dashboard .weekly-day-card.is-peak { border-color: #bfdbfe; background: linear-gradient(180deg, #eff6ff, #fff); }
  .chair-dashboard-footer { border-top: 1px solid #dbe4f0; background: rgba(255,255,255,.85); }
  @media (max-width: 640px) {
    .chair-dashboard .page-content { padding: 1rem; }
    .chair-dashboard-hero { padding: 1.15rem; }
    .chair-dashboard-footer { margin-left: -1rem; margin-right: -1rem; padding-left: 1rem; padding-right: 1rem; }
  }
  @media (prefers-reduced-motion: reduce) {
    .chair-dashboard .stat-card, .chair-dashboard .card, .chair-dashboard .rank-card, .chair-dashboard .nav-item { transition: none; }
  }
</style>
</head>
<body class="chair-dashboard font-sans text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

@include('partials.chair_sidebar')

  <!-- ══════════ MAIN ══════════ -->
<div class="app-main relative">

    @include('partials.chair_header', [
        'title' => 'Dashboard',
        'collegeContext' => $deptChair->program?->department?->college_name ?? $deptChair->department?->college_name,
        'contextTag' => collect([$deptChair->program?->dept_code, $academicYear?->ay_academic_year, $semester?->sem_name])->filter()->implode(' · '),
    ])

    <!-- ══════════ DASHBOARD PAGE ══════════ -->
<div class="page-content">
      <!-- Page heading -->
      <div class="chair-dashboard-hero flex items-center gap-3">
        <div class="flex-1">
          <div class="relative z-10 text-[22px] font-extrabold tracking-tight text-slate-900">Good morning, Chair {{ $deptChair->dc_last_name }}</div>
          <div class="relative z-10 mt-1 text-[13px] text-slate-500">
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
          <div class="stat-icon" aria-hidden="true"><i class="ti ti-users"></i></div>
          <div class="stat-label">Faculty ({{ $deptChair->program->dept_code ?? '' }})</div>
          <div class="stat-value">{{ $totalFaculty }}</div>
          <div class="stat-sub">Under your department</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-green-600"></div>
          <div class="stat-icon icon-green" aria-hidden="true"><i class="ti ti-calendar-check"></i></div>
          <div class="stat-label">Sections Plotted</div>
          <div class="stat-value">{{ $sectionsPlotted }}</div>
          <div class="stat-sub">of {{ $totalSections }} department sections</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-red-600"></div>
          <div class="stat-icon icon-red" aria-hidden="true"><i class="ti ti-alert-triangle"></i></div>
          <div class="stat-label">Faculty at Max Load</div>
          <div class="stat-value">{{ $facultyAtMaxLoadCount }}</div>
          <div class="stat-sub">{{ $facultyAtMaxLoadCount === 1 ? 'faculty member' : 'faculty members' }} at their weekly teaching-hour cap</div>
        </div>
        <div class="stat-card">
          <div class="stat-card-bar bg-cyan-600"></div>
          <div class="stat-icon icon-cyan" aria-hidden="true"><i class="ti ti-building-community"></i></div>
          <div class="stat-label">Sections</div>
          <div class="stat-value">{{ $totalSections }}</div>
          <div class="stat-sub">All year levels</div>
        </div>
      </div>

      <!-- LOAD LEADERS (Top 3) -->
      @if($topFaculty->isNotEmpty())
      <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h2 class="text-base font-bold text-slate-800"><span aria-hidden="true">🏅</span> Top 3 Most Plotted Faculty</h2>
            <p class="text-xs text-slate-500">In {{ $deptChair->department->dept_name ?? 'your department' }} · By scheduled teaching hours this semester</p>
          </div>

        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          @foreach($topFaculty as $index => $tf)
            @php
              $rankInfo = [
              0 => ['label' => '#1 Gold', 'border' => 'border-amber-400', 'bg' => 'bg-amber-50', 'icon' => '🥇'],
              1 => ['label' => '#2 Silver', 'border' => 'border-slate-300', 'bg' => 'bg-slate-50', 'icon' => '🥈'],
              2 => ['label' => '#3 Bronze', 'border' => 'border-orange-300', 'bg' => 'bg-orange-50', 'icon' => '🥉'],
              ][$index];
              $totalMax = $tf['employment'] === 'part_time' ? 30 : ($tf['day']['max_hours'] + $tf['evening']['max_hours']);
              $rem = max(0, $totalMax - $tf['total_plotted']);
              $pct = min(100, round($tf['total_plotted'] / max(1, $totalMax) * 100));
              $dayCap = $tf['day']['max_hours'];
              $eveningCap = $tf['evening']['max_hours'];
              $dayPct = min(100, round($tf['day']['hours'] / max(1, $dayCap) * 100));
              $eveningPct = min(100, round($tf['evening']['hours'] / max(1, $eveningCap) * 100));
            @endphp
            <div class="rank-card relative overflow-hidden rounded-2xl border-2 {{ $rankInfo['border'] }} bg-white p-5 shadow-sm">
              <div class="absolute top-0 right-0 px-3 py-1 text-[10px] font-bold uppercase {{ $rankInfo['bg'] }} {{ str_replace('border-', 'text-', $rankInfo['border']) }} rounded-bl-xl border-b border-l {{ $rankInfo['border'] }}">
                <span aria-hidden="true">{{ $rankInfo['icon'] }}</span> {{ $rankInfo['label'] }}
              </div>
              <div class="flex items-center gap-4 mb-4">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xl font-bold text-slate-700 shadow-inner">
                  {{ strtoupper(substr($tf['name'], 0, 1)) }}{{ strtoupper(substr(strrchr($tf['name'], " "), 1, 1)) }}
                </div>
                <div>
                  <div class="font-bold text-slate-900">{{ $tf['name'] }}</div>
                  <div class="text-[11px] text-slate-500">{{ $tf['rank'] ?: 'Faculty' }} · {{ $deptChair->program->prog_code ?? '' }}</div>
                  @if($rem <= 3 && $tf['total_plotted'] > 0)
                    <div class="mt-1 inline-flex items-center rounded-md bg-red-50 px-1.5 py-0.5 text-[9px] font-bold text-red-600 border border-red-100">
                      ⚠️ Near Cap ({{ $rem }}h Left)
                    </div>
                  @endif
                </div>
              </div>

              <div class="mb-4 space-y-3">
                <div class="flex items-center justify-between gap-2">
                  <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Plotted</div>
                  <div class="text-right text-sm font-extrabold text-slate-900">{{ $tf['total_plotted'] }}h <span class="text-slate-400 font-medium">/ {{ $totalMax }}h total cap</span>@if($tf['employment'] === 'part_time')<div class="text-[9px] font-semibold text-slate-400">{{ $rem }}h shared capacity left</div>@endif</div>
                </div>
                @if($tf['employment'] === 'part_time')
                  @php
                    $sharedBarTone = $pct > 90 ? 'bg-red-500' : ($pct > 70 ? 'bg-amber-500' : 'bg-emerald-500');
                  @endphp
                  <div>
                    <div class="mb-1 flex items-center justify-between gap-2 text-[10px]">
                      <span class="font-bold uppercase tracking-wide text-emerald-700">Shared Day + Evening</span>
                      <span class="font-extrabold text-slate-700">{{ $tf['total_plotted'] }}h <span class="font-medium text-slate-400">/ {{ $totalMax }}h max</span></span>
                    </div>
                    <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                      <div class="h-full rounded-full transition-all duration-700 {{ $sharedBarTone }}" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="mt-1 flex justify-between text-[9px] font-semibold text-slate-400">
                      <span>{{ $pct }}% of shared cap</span>
                      <span>{{ $rem }}h left</span>
                    </div>
                  </div>
                @else
                  @foreach ([['label' => 'Day', 'load' => $tf['day'], 'percent' => $dayPct, 'tone' => 'emerald'], ['label' => 'Evening', 'load' => $tf['evening'], 'percent' => $eveningPct, 'tone' => 'indigo']] as $shiftLoad)
                    @php
                      $shiftRemaining = max(0, $shiftLoad['load']['max_hours'] - $shiftLoad['load']['hours']);
                      $barTone = $shiftLoad['percent'] > 90 ? 'bg-red-500' : ($shiftLoad['percent'] > 70 ? 'bg-amber-500' : ($shiftLoad['tone'] === 'emerald' ? 'bg-emerald-500' : 'bg-indigo-500'));
                      $textTone = $shiftLoad['percent'] > 90 ? 'text-red-600' : ($shiftLoad['tone'] === 'emerald' ? 'text-emerald-700' : 'text-indigo-700');
                    @endphp
                    <div>
                      <div class="mb-1 flex items-center justify-between gap-2 text-[10px]">
                        <span class="font-bold uppercase tracking-wide {{ $textTone }}">{{ $shiftLoad['label'] }}</span>
                        <span class="font-extrabold text-slate-700">{{ $shiftLoad['load']['hours'] }}h <span class="font-medium text-slate-400">/ {{ $shiftLoad['load']['max_hours'] }}h max</span></span>
                      </div>
                      <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full transition-all duration-700 {{ $barTone }}" style="width: {{ $shiftLoad['percent'] }}%"></div>
                      </div>
                      <div class="mt-1 flex justify-between text-[9px] font-semibold text-slate-400">
                        <span>{{ $shiftLoad['percent'] }}% of shift cap</span>
                        <span>{{ $shiftRemaining }}h left</span>
                      </div>
                    </div>
                  @endforeach
                @endif
              </div>

              <div class="grid grid-cols-3 gap-2 bg-slate-50 rounded-xl p-3 mb-4">
                <div class="text-center">
                  <div class="text-[9px] font-bold text-slate-400 uppercase">Day</div>
                  <div class="text-xs font-bold text-emerald-600">{{ $tf['day']['hours'] }}h</div>
                </div>
                <div class="text-center border-l border-slate-200">
                  <div class="text-[9px] font-bold text-slate-400 uppercase">Evening</div>
                  <div class="text-xs font-bold text-blue-600">{{ $tf['evening']['hours'] }}h</div>
                </div>
                <div class="text-center border-l border-slate-200">
                  <div class="text-[9px] font-bold text-slate-400 uppercase"><span aria-hidden="true">📚</span> Classes</div>
                  <div class="text-xs font-bold text-slate-700">{{ $tf['classes'] }} Subj</div>
                </div>
              </div>

              <button type="button" data-faculty-id="{{ $tf['fac_id'] }}" data-faculty-name="{{ $tf['name'] }}" data-faculty-role="{{ $tf['rank'] ?: 'Faculty' }} · {{ $deptChair->program->dept_code ?? '' }}" data-rank-label="{{ $rankInfo['label'] }}" data-total-hours="{{ $tf['total_plotted'] }}" data-day-hours="{{ $tf['day']['hours'] }}" data-evening-hours="{{ $tf['evening']['hours'] }}" data-day-cap="{{ $tf['day']['max_hours'] }}" data-evening-cap="{{ $tf['evening']['max_hours'] }}" data-cap-hours="{{ $totalMax }}" data-cap-percent="{{ $pct }}" data-cap-left="{{ $rem }}" onclick="openChairTimetable(this)" class="flex w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50/60 py-2.5 text-xs font-bold text-blue-700 transition hover:border-blue-300 hover:bg-blue-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"/></svg>
                View Timetable
              </button>
            </div>
          @endforeach
        </div>
      </div>
      @endif

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
              <div class="card-sub">{{ $deptChair->program->prog_code ?? '' }} &middot; Day caps follow designation; Evening is 30h; part-time Day + Evening share one 30h cap</div>
            </div>
          </div>
          <div class="faculty-summary-scroll">
          <table class="faculty-summary-table w-full">
            <thead>
              <tr><th>Faculty</th><th>Special Position</th><th>Day Load</th><th>Evening Load</th><th>Status</th></tr>
            </thead>
            <tbody>
              @forelse($facultyLoad as $fl)
                @php
                  $dayColor = match($fl['day']['status']) {
                      'Overload'   => 'text-red-600',
                      'Near Max'   => 'text-amber-600',
                      'Full'       => 'text-red-600',
                      'Part-time'  => 'text-cyan-600',
                      default      => 'text-green-600',
                  };
                  $eveningColor = match($fl['evening']['status']) {
                      'Overload'   => 'text-red-600',
                      'Near Max'   => 'text-amber-600',
                      'Full'       => 'text-red-600',
                      'Part-time'  => 'text-cyan-600',
                      default      => 'text-green-600',
                  };
                  $dayBadge = match($fl['day']['status']) {
                      'Overload'  => 'badge-red',
                      'Near Max'  => 'badge-amber',
                      'Full'      => 'badge-red',
                      'Part-time' => 'badge-teal',
                      default     => 'badge-green',
                  };
                  $eveningBadge = match($fl['evening']['status']) {
                      'Overload'  => 'badge-red',
                      'Near Max'  => 'badge-amber',
                      'Full'      => 'badge-red',
                      'Part-time' => 'badge-teal',
                      default     => 'badge-green',
                  };
                @endphp
                <tr data-dashboard-faculty data-person-key="{{ $fl['fac_id'] }}" data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.($fl['special_position'] ?? '').' '.$fl['day']['status'].' '.$fl['evening']['status'].' '.$fl['day']['hours'].' '.$fl['evening']['hours']) }}">
                  <td>
                    <div class="flex items-center gap-2.5">
                      <span class="load-avatar {{ ['bg-blue-100 text-blue-700','bg-emerald-100 text-emerald-700','bg-violet-100 text-violet-700','bg-amber-100 text-amber-700','bg-cyan-100 text-cyan-700','bg-rose-100 text-rose-700'][$loop->index % 6] }}">
                        {{ strtoupper(substr($fl['name'], 0, 1)) }}{{ strtoupper(substr(strrchr($fl['name'], ' ') ?: $fl['name'], 1, 1)) }}
                      </span>
                      <span class="font-bold text-slate-800">{{ $fl['name'] }}</span>
                    </div>
                  </td>
                  <td><span class="text-xs font-semibold {{ $fl['special_position'] ? 'text-indigo-700' : 'text-slate-400' }}">{{ $fl['special_position'] ?: '—' }}</span></td>
                  @foreach (['day', 'evening'] as $shift)
                    @php
                      $color = $shift === 'day' ? $dayColor : $eveningColor;
                      $valueTone = $shift === 'day' ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700';
                      if (in_array($fl[$shift]['status'], ['Overload', 'Full'], true)) $valueTone = 'bg-rose-50 text-rose-700';
                      elseif ($fl[$shift]['status'] === 'Near Max') $valueTone = 'bg-amber-50 text-amber-700';
                      elseif ($fl[$shift]['status'] === 'Part-time') $valueTone = 'bg-cyan-50 text-cyan-700';
                    @endphp
                    <td><span class="load-value-chip {{ $valueTone }}">{{ $fl[$shift]['hours'] }}h</span>
                      @if ($fl['employment'] === 'part_time')
                        <div class="text-xs text-slate-500">Counts toward shared cap</div>
                      @else
                        <span class="text-xs text-slate-400"> / {{ $fl[$shift]['max_hours'] }}h</span>
                        <div class="text-xs text-slate-500">{{ $fl[$shift]['remaining'] }}h left</div>
                      @endif
                    </td>
                  @endforeach
                  <td>
                    @if ($fl['employment'] === 'part_time')
                      <span class="badge {{ $dayBadge }}">Shared: {{ $fl['day']['status'] }}</span>
                    @else
                      <div><span class="badge {{ $dayBadge }}">Day: {{ $fl['day']['status'] }}</span></div><div class="mt-1"><span class="badge {{ $eveningBadge }}">Evening: {{ $fl['evening']['status'] }}</span></div>
                    @endif
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-slate-400 py-4">No faculty in this department yet.</td></tr>
              @endforelse
              <tr id="dashboard-table-no-results" class="hidden"><td colspan="5" class="text-center text-slate-400 py-4">No faculty match your search.</td></tr>
            </tbody>
          </table>
          </div>
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
                  $hours = $fl['day']['limit_hours'];
                  $remaining = max(0, 30 - $hours);
                  $percent = min(100, round($hours / 30 * 100));
                  $state = $hours > 30 ? 'Overload' : ($hours >= 30 ? 'Full' : ($hours >= 27 ? 'Near Max' : 'Part-time'));
                  $barColor = match($state) { 'Overload', 'Full' => 'bg-red-600', 'Near Max' => 'bg-amber-600', default => 'bg-cyan-600' };
                  $textColor = match($state) { 'Overload', 'Full' => 'text-red-600', 'Near Max' => 'text-amber-600', default => 'text-cyan-600' };
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
                    $barColor = match($state) { 'Overload', 'Full' => 'bg-red-600', 'Near Max' => 'bg-amber-600', 'Part-time' => 'bg-cyan-600', default => 'bg-green-600' };
                    $textColor = match($state) { 'Overload', 'Full' => 'text-red-600', 'Near Max' => 'text-amber-600', 'Part-time' => 'text-cyan-600', default => 'text-green-600' };
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

      <div class="card mt-4 overflow-hidden">
        <div class="card-header">
          <div>
            <div class="flex flex-wrap items-center gap-2">
              <div class="card-title">Weekly Class Pattern</div>
              <span class="rounded-md bg-blue-100 px-2 py-1 text-[9px] font-extrabold text-blue-700">{{ number_format($weeklyScheduleCounts->sum('hours'), 0) }} Total Plotted Hours</span>
            </div>
            <div class="card-sub">Active schedules across {{ $totalFaculty }} faculty members · Monday to Saturday</div>
          </div>
          <div class="flex items-center gap-1 rounded-lg bg-slate-100 p-1 text-[10px]">
            <span class="px-1.5 font-semibold text-slate-500">View By:</span>
            <button type="button" data-weekly-mode="hours" aria-pressed="true" class="weekly-mode rounded-md bg-white px-2 py-1 font-bold text-slate-800 shadow-sm">Hour Load</button>
            <button type="button" data-weekly-mode="rooms" aria-pressed="false" class="weekly-mode rounded-md px-2 py-1 font-semibold text-slate-500">Room Util.</button>
          </div>
        </div>
        @php $weeklyMaxHours = max(1, $weeklyScheduleCounts->max('hours')); @endphp
        <div id="weekly-room-summary" class="hidden grid grid-cols-1 gap-2 px-4 pt-3 sm:grid-cols-2">
          <div class="flex items-center justify-between rounded-xl border border-blue-100 bg-blue-50/60 px-3 py-2.5">
            <div><div class="text-[9px] font-bold uppercase tracking-wide text-slate-500">Sections in Rooms</div><div class="text-[10px] text-slate-500">This department’s active schedules</div></div>
            <strong class="text-lg font-black text-blue-700">{{ $roomUtilSummary['occupied_sections'] }}</strong>
          </div>
          <div class="flex items-center justify-between rounded-xl border border-violet-100 bg-violet-50/60 px-3 py-2.5">
            <div><div class="text-[9px] font-bold uppercase tracking-wide text-slate-500">Most Used Room</div><div class="text-[10px] text-slate-500">{{ $roomUtilSummary['top_room_name'] }}</div></div>
            <strong class="text-lg font-black text-violet-700">{{ $roomUtilSummary['top_room_sections'] }} <span class="text-[9px]">sections</span></strong>
          </div>
        </div>
        <div class="overflow-x-auto px-4 pb-4 pt-2">
        <div class="grid min-w-[700px] grid-cols-7 gap-2" aria-label="Weekly classes by day">
          @foreach($weeklyScheduleCounts as $item)
            <div class="weekly-day-card {{ $item['hours'] === $weeklyMaxHours && $item['hours'] > 0 ? 'is-peak' : '' }} relative flex min-h-[104px] flex-col items-center rounded-xl px-2 py-3 text-center" data-weekly-card data-hours="{{ $item['hours'] }}" data-classes="{{ $item['count'] }}" data-rooms="{{ $item['rooms'] }}" data-occupied-sections="{{ $item['occupied_sections'] }}" data-room-percent="{{ $item['room_percent'] }}" data-room-breakdown="{{ json_encode($item['room_breakdown'], JSON_HEX_APOS | JSON_HEX_QUOT) }}">
              @if($item['hours'] === $weeklyMaxHours && $item['hours'] > 0)<span class="absolute -top-2 right-1 rounded-full bg-blue-600 px-1.5 py-0.5 text-[8px] font-extrabold text-white">PEAK</span>@endif
              <span class="text-[9px] font-extrabold text-slate-500">{{ strtoupper($item['day']) }}</span>
              <span data-weekly-primary class="mt-2 text-xl font-black text-slate-900">{{ number_format($item['hours'], 0) }}h</span>
              <span data-weekly-secondary class="mt-1 text-[9px] text-slate-500">{{ $item['count'] }} {{ $item['count'] === 1 ? 'Class' : 'Classes' }} Plotted</span>
              <span data-weekly-tertiary class="mt-1 line-clamp-2 text-[8px] text-slate-400">{{ $item['rooms'] }} {{ $item['rooms'] === 1 ? 'Room' : 'Rooms' }} Scheduled</span>
              <div class="mt-2 h-1 w-4/5 overflow-hidden rounded-full bg-slate-100"><div data-weekly-bar class="h-full rounded-full bg-blue-600" style="width:{{ $item['hours'] ? max(8, round($item['hours'] / $weeklyMaxHours * 100)) : 0 }}%"></div></div>
            </div>
          @endforeach
        </div>
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

      <footer class="chair-dashboard-footer -mx-7 mt-8 flex flex-wrap items-center justify-between gap-2 px-7 py-3 text-[10px] text-slate-500">
        <span><strong class="text-slate-700">SKEDYUL Enterprise</strong> · Academic Timetabling &amp; Faculty Workload Suite</span>
        <span>{{ $deptChair->department->dept_name ?? 'College of Computer Information and Communication Technology' }} · Session Active</span>
      </footer>
    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end app-main -->
</div><!-- end app-shell -->

<div id="chair-timetable-modal" class="fixed inset-0 z-[160] hidden items-center justify-center bg-slate-950/55 p-3 backdrop-blur-[2px]" role="presentation" onclick="if(event.target===this) closeChairTimetable()">
  <section class="w-full max-w-6xl overflow-hidden rounded-2xl border border-white/70 bg-white shadow-2xl" style="max-height: min(92vh, 900px);" role="dialog" aria-modal="true" aria-labelledby="chair-timetable-title">
    <div class="h-1 bg-gradient-to-r from-blue-600 via-cyan-500 to-emerald-400"></div>
    <header class="flex flex-wrap items-start justify-between gap-4 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-blue-50/70 px-5 py-4 sm:px-7">
      <div class="flex min-w-0 flex-1 items-center gap-3">
        <div id="chair-timetable-avatar" class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-blue-100 text-lg font-black text-blue-700">--</div>
        <div class="min-w-0 flex-1">
          <div class="flex flex-wrap items-center gap-2">
            <h2 id="chair-timetable-title" class="truncate text-lg font-extrabold text-slate-900 sm:text-xl">Faculty timetable</h2>
            <span id="chair-timetable-leader" class="hidden rounded-full bg-amber-100 px-2 py-1 text-[9px] font-extrabold text-amber-800"></span>
            <span id="chair-timetable-cap" class="rounded-full bg-orange-100 px-2 py-1 text-[9px] font-extrabold text-orange-800"></span>
          </div>
          <div class="mt-1 flex flex-wrap items-center gap-2 text-[10px] text-slate-500">
            <span id="chair-timetable-role"></span>
            <span id="chair-timetable-day" class="rounded-full bg-emerald-100 px-2 py-1 font-bold text-emerald-700"></span>
            <span id="chair-timetable-evening" class="rounded-full bg-indigo-100 px-2 py-1 font-bold text-indigo-700"></span>
            <span id="chair-timetable-term" class="text-slate-400"></span>
          </div>
        </div>
      </div>
      <div class="flex shrink-0 items-center gap-2">
        <button type="button" onclick="window.print()" class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-[10px] font-bold text-slate-600">Print</button>
        <button type="button" onclick="closeChairTimetable()" aria-label="Close timetable" class="grid h-9 w-9 place-items-center rounded-xl border border-slate-200 bg-white text-lg text-slate-500 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600">&times;</button>
      </div>
    </header>
    <div class="max-h-[calc(92vh-150px)] overflow-auto p-4 sm:p-6">
      <div id="chair-timetable-stats" class="mb-4 flex flex-wrap gap-2"></div>
      <div id="chair-timetable-grid" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4"></div>
      <div id="chair-timetable-empty" class="hidden rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
        <div class="text-3xl" aria-hidden="true">🗓️</div>
        <p class="mt-2 font-bold text-slate-700">No active timetable found</p>
        <p class="mt-1 text-xs text-slate-500">No classes are plotted for this faculty in the selected semester.</p>
      </div>
      <div class="mt-5 grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Faculty load summary">
        <div class="rounded-xl border border-slate-200 bg-white p-3"><div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Total Plotted</div><div id="chair-timetable-total" class="mt-1 text-lg font-black text-slate-900"></div><div id="chair-timetable-total-cap" class="text-[9px] text-slate-400">Weekly hours</div></div>
        <div class="rounded-xl border border-emerald-100 bg-emerald-50/60 p-3"><div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Day Class</div><div id="chair-timetable-day-total" class="mt-1 text-lg font-black text-emerald-600"></div><div id="chair-timetable-day-cap" class="text-[9px] text-slate-400">Teaching hours</div></div>
        <div class="rounded-xl border border-indigo-100 bg-indigo-50/60 p-3"><div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Evening Class</div><div id="chair-timetable-evening-total" class="mt-1 text-lg font-black text-indigo-600"></div><div id="chair-timetable-evening-cap" class="text-[9px] text-slate-400">Teaching hours</div></div>
        <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-3"><div class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Sections &amp; Rooms</div><div id="chair-timetable-sections-rooms" class="mt-1 text-lg font-black text-blue-700"></div><div class="text-[9px] text-slate-400">In this timetable</div></div>
      </div>
    </div>
    <footer class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 bg-slate-50 px-5 py-3.5 sm:px-7">
      <p class="text-[11px] text-slate-500">Select a section on a class card to open its matching PBS schedule.</p>
      <a id="chair-timetable-open-pbt" href="#" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-xs font-bold text-white transition hover:bg-blue-700">
        Open full PBT <span aria-hidden="true">↗</span>
      </a>
    </footer>
  </section>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CHAIR_DASHBOARD_TIMETABLES = @js($topFacultySchedules);
const CHAIR_DASHBOARD_SEMESTER_ID = @js($semester?->sem_id);
const CHAIR_DASHBOARD_PBT_URL = @js(route('chair.pbt'));
const CHAIR_DASHBOARD_PBS_URL = @js(route('chair.pbs'));
let chairTimetableReturnFocus = null;

function openChairTimetable(button) {
  const modal = document.getElementById('chair-timetable-modal');
  const facultyId = button.dataset.facultyId;
  const facultyName = button.dataset.facultyName || 'Faculty timetable';
  const schedules = CHAIR_DASHBOARD_TIMETABLES[facultyId] || [];
  const weekDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
  chairTimetableReturnFocus = button;

  document.getElementById('chair-timetable-title').textContent = facultyName;
  document.getElementById('chair-timetable-avatar').textContent = facultyName.split(/\s+/).filter(Boolean).slice(0, 2).map(part => part[0]).join('').toUpperCase() || 'FC';
  document.getElementById('chair-timetable-role').textContent = button.dataset.facultyRole || 'Faculty';
  document.getElementById('chair-timetable-day').textContent = `Day Class (${Number(button.dataset.dayHours || 0)}h / ${Number(button.dataset.dayCap || 0)}h max)`;
  document.getElementById('chair-timetable-evening').textContent = `Evening Class (${Number(button.dataset.eveningHours || 0)}h / ${Number(button.dataset.eveningCap || 0)}h max)`;
  document.getElementById('chair-timetable-cap').textContent = `${button.dataset.capHours || 0}h cap · ${button.dataset.capPercent || 0}% reached · ${button.dataset.capLeft || 0}h left`;
  const leader = document.getElementById('chair-timetable-leader');
  leader.textContent = button.dataset.rankLabel ? `🏆 ${button.dataset.rankLabel} Teaching Load Leader` : '';
  leader.classList.toggle('hidden', !button.dataset.rankLabel);
  document.getElementById('chair-timetable-term').textContent = @js($timetableTerm);
  document.getElementById('chair-timetable-total').textContent = `${Number(button.dataset.totalHours || 0)}h`;
  document.getElementById('chair-timetable-day-total').textContent = `${Number(button.dataset.dayHours || 0)}h`;
  document.getElementById('chair-timetable-evening-total').textContent = `${Number(button.dataset.eveningHours || 0)}h`;
  document.getElementById('chair-timetable-total-cap').textContent = `of ${Number(button.dataset.capHours || 0)}h total cap`;
  document.getElementById('chair-timetable-day-cap').textContent = `${Number(button.dataset.dayHours || 0)}h of ${Number(button.dataset.dayCap || 0)}h max`;
  document.getElementById('chair-timetable-evening-cap').textContent = `${Number(button.dataset.eveningHours || 0)}h of ${Number(button.dataset.eveningCap || 0)}h max`;
  const sectionCount = new Set(schedules.map(item => item.section_id).filter(Boolean)).size;
  const roomCount = new Set(schedules.map(item => item.room_id || item.room).filter(value => value && value !== 'Room not set')).size;
  document.getElementById('chair-timetable-sections-rooms').textContent = `${sectionCount} Sec · ${roomCount} Rooms`;
  const stats = document.getElementById('chair-timetable-stats');
  stats.replaceChildren();
  [
    [`${schedules.length} scheduled classes`, 'bg-blue-50 text-blue-700'],
    [`${schedules.filter(item => String(item.shift).toLowerCase().includes('even')).length} evening`, 'bg-violet-50 text-violet-700'],
    [`${schedules.filter(item => !String(item.shift).toLowerCase().includes('even')).length} day`, 'bg-emerald-50 text-emerald-700'],
  ].forEach(([label, tone]) => {
    const chip = document.createElement('span');
    chip.className = `rounded-full px-3 py-1.5 text-[11px] font-extrabold ${tone}`;
    chip.textContent = label;
    stats.appendChild(chip);
  });

  const grid = document.getElementById('chair-timetable-grid');
  grid.replaceChildren();
  const hasSchedules = schedules.length > 0;
  document.getElementById('chair-timetable-empty').classList.toggle('hidden', hasSchedules);
  grid.classList.toggle('hidden', !hasSchedules);

  if (hasSchedules) {
    weekDays.forEach(day => {
      const daySchedules = schedules.filter(item => String(item.day).toLowerCase() === day.toLowerCase());
      const column = document.createElement('section');
      column.className = 'timetable-day';
      const heading = document.createElement('h3');
      heading.className = 'timetable-day-title flex items-center justify-between';
      const dayName = document.createElement('span');
      dayName.textContent = day;
      const count = document.createElement('span');
      count.className = 'rounded-full bg-white px-2 py-0.5 text-[10px] text-slate-500';
      count.textContent = String(daySchedules.length);
      heading.append(dayName, count);
      column.appendChild(heading);

      const events = document.createElement('div');
      events.className = 'space-y-2 p-2';
      if (!daySchedules.length) {
        const free = document.createElement('p');
        free.className = 'py-6 text-center text-[11px] italic text-slate-400';
        free.textContent = 'No classes';
        events.appendChild(free);
      }

      daySchedules.forEach(schedule => {
        const isEvening = String(schedule.shift).toLowerCase().includes('even');
        const event = document.createElement('article');
        event.className = `timetable-event ${isEvening ? 'evening' : ''}`;
        const time = document.createElement('div');
        time.className = `mb-1 text-[10px] font-extrabold ${isEvening ? 'text-violet-700' : 'text-emerald-700'}`;
        time.textContent = `${schedule.start || 'Time not set'}${schedule.end ? ` – ${schedule.end}` : ''}`;
        const course = document.createElement('div');
        course.className = 'text-sm font-extrabold text-slate-800';
        course.textContent = schedule.course_code || 'Course';
        const courseName = document.createElement('div');
        courseName.className = 'mt-0.5 line-clamp-2 text-[11px] text-slate-600';
        courseName.textContent = schedule.course_name || '';
        const meta = document.createElement('div');
        meta.className = 'mt-2 flex flex-wrap gap-x-2 gap-y-1 text-[10px] font-semibold text-slate-500';
        const section = document.createElement('span');
        section.textContent = `▦ ${schedule.section || 'Section not set'}`;
        const room = document.createElement('span');
        room.textContent = `⌂ ${schedule.room || 'Room not set'}`;
        meta.append(section, room);
        event.append(time, course, courseName, meta);

        if (schedule.section_id && schedule.department_id) {
          const pbsUrl = new URL(CHAIR_DASHBOARD_PBS_URL, window.location.origin);
          pbsUrl.searchParams.set('department', schedule.department_id);
          pbsUrl.searchParams.set('section', schedule.section_id);
          if (CHAIR_DASHBOARD_SEMESTER_ID) pbsUrl.searchParams.set('semester', CHAIR_DASHBOARD_SEMESTER_ID);
          pbsUrl.searchParams.set('shift', isEvening ? 'night' : 'day');
          const pbsLink = document.createElement('a');
          pbsLink.className = 'mt-2 inline-block';
          pbsLink.href = pbsUrl.toString();
          pbsLink.textContent = 'Open matching PBS →';
          event.appendChild(pbsLink);
        }
        events.appendChild(event);
      });
      column.appendChild(events);
      grid.appendChild(column);
    });
  }

  const pbtUrl = new URL(CHAIR_DASHBOARD_PBT_URL, window.location.origin);
  pbtUrl.searchParams.set('faculty', facultyId);
  if (CHAIR_DASHBOARD_SEMESTER_ID) pbtUrl.searchParams.set('semester', CHAIR_DASHBOARD_SEMESTER_ID);
  document.getElementById('chair-timetable-open-pbt').href = pbtUrl.toString();
  modal.classList.remove('hidden');
  modal.classList.add('flex');
  document.body.classList.add('overflow-hidden');
  modal.querySelector('[aria-label="Close timetable"]').focus();
}

function closeChairTimetable() {
  const modal = document.getElementById('chair-timetable-modal');
  if (!modal) return;
  modal.classList.add('hidden');
  modal.classList.remove('flex');
  document.body.classList.remove('overflow-hidden');
  chairTimetableReturnFocus?.focus();
}

document.addEventListener('keydown', event => {
  if (event.key === 'Escape' && !document.getElementById('chair-timetable-modal')?.classList.contains('hidden')) {
    closeChairTimetable();
  }
});

document.querySelectorAll('[data-weekly-mode]').forEach(button => {
  button.addEventListener('click', () => {
    const mode = button.dataset.weeklyMode;
    document.getElementById('weekly-room-summary')?.classList.toggle('hidden', mode !== 'rooms');
    document.querySelectorAll('[data-weekly-mode]').forEach(toggle => {
      const active = toggle === button;
      toggle.setAttribute('aria-pressed', String(active));
      toggle.classList.toggle('bg-white', active);
      toggle.classList.toggle('shadow-sm', active);
      toggle.classList.toggle('font-bold', active);
      toggle.classList.toggle('text-slate-800', active);
      toggle.classList.toggle('font-semibold', !active);
      toggle.classList.toggle('text-slate-500', !active);
    });

    document.querySelectorAll('[data-weekly-card]').forEach(card => {
      const primary = card.querySelector('[data-weekly-primary]');
      const secondary = card.querySelector('[data-weekly-secondary]');
      const tertiary = card.querySelector('[data-weekly-tertiary]');
      const bar = card.querySelector('[data-weekly-bar]');
      if (mode === 'rooms') {
        primary.textContent = `${card.dataset.occupiedSections} ${Number(card.dataset.occupiedSections) === 1 ? 'Section' : 'Sections'}`;
        let roomBreakdown = [];
        try { roomBreakdown = JSON.parse(card.dataset.roomBreakdown || '[]'); } catch { roomBreakdown = []; }
        secondary.textContent = roomBreakdown.length
          ? roomBreakdown.slice(0, 2).map(room => `${room.room}: ${room.sections} ${room.sections === 1 ? 'section' : 'sections'}`).join(' · ')
          : 'No sections in rooms';
        tertiary.textContent = `${card.dataset.rooms} ${Number(card.dataset.rooms) === 1 ? 'room' : 'rooms'} occupied · ${card.dataset.roomPercent}% assigned`;
        bar.style.width = `${card.dataset.roomPercent}%`;
        bar.classList.remove('bg-blue-600');
        bar.classList.add('bg-emerald-500');
      } else {
        primary.textContent = `${Number(card.dataset.hours).toFixed(0)}h`;
        secondary.textContent = `${card.dataset.classes} ${Number(card.dataset.classes) === 1 ? 'Class' : 'Classes'} Plotted`;
        const total = Math.max(1, ...[...document.querySelectorAll('[data-weekly-card]')].map(item => Number(item.dataset.hours)));
        bar.style.width = `${Number(card.dataset.hours) ? Math.max(8, Math.round(Number(card.dataset.hours) / total * 100)) : 0}%`;
        bar.classList.remove('bg-emerald-500');
        bar.classList.add('bg-blue-600');
      }
    });
  });
});

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
