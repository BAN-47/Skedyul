<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Schedule Plotter</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/chair_dashboard.css') }}">
<style>
  :root {
    --sp-blue:#4f5bff; --sp-blue-light:#eef0ff;
    --sp-border:#e5e7eb; --sp-text:#1f2937; --sp-text2:#6b7280; --sp-text3:#9ca3af;
  }

  .sp-toolbar { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; margin-bottom:20px; }
  .sp-toolbar-left { display:flex; gap:10px; flex-wrap:wrap; }
  .sp-btn { display:inline-flex; align-items:center; gap:6px; padding:10px 16px; border-radius:10px; font-size:13px; font-weight:700; border:1px solid var(--sp-border); background:#fff; color:var(--sp-text); cursor:pointer; }
  .sp-btn.primary { background:var(--sp-blue); color:#fff; border:none; }
  .sp-hours { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--sp-text2); font-weight:600; }
  .sp-hours select { padding:6px 10px; border-radius:8px; border:1px solid var(--sp-border); font-size:13px; font-family:inherit; }

  .sp-card { background:#fff; border:1px solid var(--sp-border); border-radius:14px; padding:18px; margin-bottom:18px; }
  .sp-card-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; cursor:pointer; }
  .sp-card-title { font-size:14px; font-weight:800; color:var(--sp-text); }
  .sp-collapse-icon { font-size:12px; color:var(--sp-text3); transition:transform .15s; }
  .sp-collapsed .sp-collapse-icon { transform:rotate(-90deg); }
  .sp-collapsible-body { display:block; }
  .sp-collapsed .sp-collapsible-body { display:none; }

  .sp-chip-row { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:14px; }
  .sp-subj-chip { display:inline-flex; align-items:center; gap:6px; padding:6px 14px; border-radius:20px; font-size:13px; font-weight:700; color:#fff; }

  .sp-class-row { display:flex; align-items:center; gap:8px; padding:12px; border-radius:10px; background:var(--grey,#f8f9fb); margin-bottom:8px; flex-wrap:wrap; }
  .sp-class-row.editing { background:var(--sp-blue-light); border:1px solid var(--sp-blue); }
  .sp-mini-select, .sp-mini-input { padding:7px 10px; border-radius:8px; border:1px solid var(--sp-border); font-size:12px; font-family:inherit; background:#fff; }
  .sp-mini-select { min-width:110px; }
  .sp-mini-input[type="time"] { min-width:110px; }
  .sp-badge { display:inline-flex; align-items:center; padding:6px 12px; border-radius:8px; font-size:12px; font-weight:700; color:#fff; min-width:70px; justify-content:center; }
  .sp-class-meta { font-size:12px; color:var(--sp-text2); display:flex; gap:10px; flex-wrap:wrap; }
  .sp-day-pills { display:flex; gap:4px; }
  .sp-day-pill { width:26px; height:26px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; background:#e5e7eb; color:var(--sp-text3); border:none; cursor:default; }
  .sp-day-pill.active { background:var(--sp-blue); color:#fff; }
  .sp-day-pill.clickable { cursor:pointer; }
  .sp-day-pill.clickable:hover { background:#d1d5db; }
  .sp-day-pill.clickable.active:hover { background:var(--sp-blue); }
  .sp-remove-btn, .sp-save-btn { background:none; border:none; cursor:pointer; font-size:15px; padding:4px 8px; }
  .sp-remove-btn { color:var(--sp-text3); margin-left:auto; }
  .sp-remove-btn:hover { color:#dc2626; }
  .sp-save-btn { color:#16a34a; font-weight:700; font-size:13px; }

  .sp-grid { width:100%; border-collapse:collapse; }
  .sp-grid th, .sp-grid td { border:1px solid #eef0f4; padding:0; }
  .sp-grid th { background:#f7f9fc; font-size:13px; font-weight:700; padding:12px 14px; text-align:center; color:#3b4a66; }
  .sp-grid th:first-child { text-align:left; }
  .sp-grid td.time-col { font-size:13px; font-weight:600; color:#5b6b8c; padding:10px 14px; white-space:nowrap; }
  .sp-cell-inner { min-height:44px; padding:4px; display:flex; flex-direction:column; gap:4px; }
  .sp-chip {
    border-radius:6px; padding:8px 12px; font-size:12.5px; font-weight:700; color:#fff;
    position:relative; line-height:1.2; display:flex; align-items:center; justify-content:space-between; gap:6px;
  }
  .sp-chip .sc-name { font-weight:700; }
  .sp-chip .sc-meta { display:none; } /* meta hidden for a cleaner look, matching reference */
  .sp-chip form { display:flex; align-items:center; opacity:0; transition:opacity .12s; }
  .sp-chip:hover form { opacity:1; }
  .sp-chip button.remove { background:rgba(255,255,255,.25); border:none; color:#fff; border-radius:5px; width:16px; height:16px; font-size:11px; line-height:1; cursor:pointer; flex-shrink:0; }

  .alert-banner { padding:12px 16px; border-radius:10px; margin-bottom:16px; font-size:13px; }
  .alert-banner.error { background:#fef2f2; color:#991b1b; border:1px solid #fecaca; }
  .alert-banner.success { background:#f0fdf4; color:#166534; border:1px solid #bbf7d0; }
</style>
</head>
<body>

<div class="app-wrapper">
@include('partials.chair_sidebar')

<div class="main">
  <div class="topbar">
    <div class="topbar-title">Schedule Plotter</div>
    <div style="font-size:12px;color:var(--text3);">
      AY {{ $academicYear->ay_year_label ?? 'N/A' }} · {{ $semester->sem_name ?? 'No active semester' }}
    </div>
  </div>

  <div class="page-content">

    @if (session('success'))
      <div class="alert-banner success">{{ session('success') }}</div>
    @endif

    @error('conflict')
      <div class="alert-banner error"><strong>Cannot save:</strong> {{ $message }}</div>
    @enderror

    <!-- TOOLBAR -->
    <div class="sp-toolbar">
      <div class="sp-toolbar-left">
        <button class="sp-btn primary" type="button" onclick="addNewClassRow()">+ Add Class</button>
        <button class="sp-btn" type="button" onclick="toggleCard('subjects-card')">Subjects</button>
        <button class="sp-btn" type="button" onclick="window.print()">⬇ Download</button>
        <button class="sp-btn" type="button" onclick="clearHourFilter()">↺ Clear</button>
      </div>
      <div class="sp-hours">
        Hours:
        <select id="hour-start" onchange="applyHourFilter()"></select>
        to
        <select id="hour-end" onchange="applyHourFilter()"></select>
      </div>
    </div>

    @if (!$semester)
      <div class="alert-banner error">No active semester is set — ask the admin to activate one before plotting a schedule.</div>
    @else

    <!-- MANAGE SUBJECTS — collapsible, no modal. Read-only reference; use your Subjects admin page to add/remove. -->
    <div class="sp-card" id="subjects-card">
      <div class="sp-card-header" onclick="toggleCard('subjects-card')">
        <div class="sp-card-title">Department Subjects</div>
        <span class="sp-collapse-icon">▾</span>
      </div>
      <div class="sp-collapsible-body">
        <div class="sp-chip-row">
          @forelse ($subjects as $s)
            @php
              $palette = ['#4f5bff','#16a34a','#d97706','#dc2626','#0d9488','#7c3aed','#0891b2','#be185d'];
              $color = $palette[crc32($s->subj_code) % count($palette)];
            @endphp
            <span class="sp-subj-chip" style="background:{{ $color }};">{{ $s->subj_code }}</span>
          @empty
            <span style="font-size:13px;color:var(--sp-text3);">No subjects found for your department.</span>
          @endforelse
        </div>
      </div>
    </div>

    <!-- SCHEDULED CLASSES — new rows inserted here inline, no modal -->
    <div class="sp-card">
      <div class="sp-card-title">Scheduled Classes</div>

      <div id="new-class-row-container"></div>

      @forelse ($schedules as $sch)
        @php
          $palette = ['#4f5bff','#16a34a','#d97706','#dc2626','#0d9488','#7c3aed','#0891b2','#be185d'];
          $color = $palette[crc32($sch->subject->subj_code ?? 'x') % count($palette)];
        @endphp
        <div class="sp-class-row">
          <span class="sp-badge" style="background:{{ $color }};">{{ $sch->subject->subj_code ?? '—' }}</span>

          <div class="sp-class-meta">
            <span>🕒 {{ \Carbon\Carbon::parse($sch->sch_start_time)->format('g:i A') }} – {{ \Carbon\Carbon::parse($sch->sch_end_time)->format('g:i A') }}</span>
            <span>👤 {{ $sch->faculty->user->usr_name ?? '—' }}</span>
            <span>🚪 {{ $sch->room->room_name ?? '—' }}</span>
            <span>🏫 {{ $sch->section->sec_name ?? '—' }}</span>
          </div>

          <div class="sp-day-pills">
            @foreach (['M' => 'Monday','T' => 'Tuesday','W' => 'Wednesday','Th' => 'Thursday','F' => 'Friday','S' => 'Saturday'] as $short => $full)
              <span class="sp-day-pill {{ $sch->sch_day === $full ? 'active' : '' }}">{{ $short }}</span>
            @endforeach
          </div>

          <form method="POST" action="{{ route('chair.schedule_plotter.destroy', $sch->sch_id) }}"
                onsubmit="return confirm('Remove this class from the schedule?');" style="margin-left:auto;">
            @csrf @method('DELETE')
            <button type="submit" class="sp-remove-btn" title="Remove">🗑</button>
          </form>
        </div>
      @empty
        <div id="empty-classes-msg" style="font-size:13px;color:var(--sp-text3);">No classes scheduled yet. Click <strong>+ Add Class</strong> to get started.</div>
      @endforelse
    </div>

    <!-- CALENDAR GRID — hourly rows, class lands in the hour bucket its start time falls under -->
    <div class="sp-card" style="padding:0;overflow-x:auto;">
      <table class="sp-grid" id="plotter-grid">
        <thead>
          <tr>
            <th style="width:90px;">Time</th>
            @foreach ($days as $d)
              <th>{{ $d }}</th>
            @endforeach
          </tr>
        </thead>
        <tbody>
          @foreach ($slots as $slot)
            @php $slotHour = \Carbon\Carbon::parse($slot['start'])->format('H'); @endphp
            <tr data-start="{{ $slot['start'] }}">
              <td class="time-col">{{ \Carbon\Carbon::parse($slot['start'])->format('g:i A') }}</td>
              @foreach ($days as $d)
                <td>
                  <div class="sp-cell-inner">
                    @foreach ($schedules->where('sch_day', $d)->filter(fn ($s) => \Carbon\Carbon::parse($s->sch_start_time)->format('H') === $slotHour) as $sch)
                      @php
                        $palette = ['#4f5bff','#16a34a','#d97706','#dc2626','#0d9488','#7c3aed','#0891b2','#be185d'];
                        $color = $palette[crc32($sch->subject->subj_code ?? 'x') % count($palette)];
                      @endphp
                      <div class="sp-chip" style="background:{{ $color }};" title="{{ $sch->faculty->user->usr_name ?? '—' }} · {{ $sch->room->room_name ?? '—' }} · {{ $sch->section->sec_name ?? '—' }}">
                        <span class="sc-name">{{ $sch->subject->subj_name ?? $sch->subject->subj_code ?? '—' }}</span>
                        <form method="POST" action="{{ route('chair.schedule_plotter.destroy', $sch->sch_id) }}"
                              onsubmit="return confirm('Remove this class from the schedule?');">
                          @csrf @method('DELETE')
                          <button type="submit" class="remove" title="Remove">×</button>
                        </form>
                      </div>
                    @endforeach
                  </div>
                </td>
              @endforeach
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @endif
  </div>
</div>
</div>

<!-- HIDDEN TEMPLATE — cloned into #new-class-row-container each time "+ Add Class" is clicked -->
<template id="new-class-template">
  <form method="POST" action="{{ route('chair.schedule_plotter.store') }}" class="sp-class-row editing">
    @csrf
    <select class="sp-mini-select" name="subj_id" required>
      <option value="">Subject</option>
      @foreach ($subjects as $s)
        <option value="{{ $s->subj_id }}">{{ $s->subj_code }}</option>
      @endforeach
    </select>

    <select class="sp-mini-select" name="fac_id" required>
      <option value="">Faculty</option>
      @foreach ($faculty as $f)
        <option value="{{ $f->fac_id }}">{{ $f->user->usr_name ?? 'Unknown' }}</option>
      @endforeach
    </select>

    <select class="sp-mini-select" name="room_id" required>
      <option value="">Room</option>
      @foreach ($rooms as $r)
        <option value="{{ $r->room_id }}">{{ $r->room_name }}</option>
      @endforeach
    </select>

    <select class="sp-mini-select" name="sec_id" required>
      <option value="">Section</option>
      @foreach ($sections as $s)
        <option value="{{ $s->sec_id }}">{{ $s->sec_name }}</option>
      @endforeach
    </select>

    <input class="sp-mini-input" type="time" name="start_time" required>
    <span style="font-size:12px;color:var(--sp-text2);">to</span>
    <input class="sp-mini-input" type="time" name="end_time" required>

    <input type="hidden" name="day" class="new-row-day-input" required>
    <div class="sp-day-pills new-row-day-pills">
      @foreach (['M' => 'Monday','T' => 'Tuesday','W' => 'Wednesday','Th' => 'Thursday','F' => 'Friday','S' => 'Saturday'] as $short => $full)
        <button type="button" class="sp-day-pill clickable" data-day="{{ $full }}" onclick="selectDay(this)">{{ $short }}</button>
      @endforeach
    </div>

    <button type="submit" class="sp-save-btn" title="Save">✓ Save</button>
    <button type="button" class="sp-remove-btn" title="Cancel" onclick="this.closest('form').remove()">×</button>
  </form>
</template>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── COLLAPSIBLE PANELS (Subjects) ────────────────────────────────────────────
function toggleCard(id) {
  document.getElementById(id).classList.toggle('sp-collapsed');
}

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

// ── ADD CLASS — inline row, no modal ─────────────────────────────────────────
function addNewClassRow() {
  const container = document.getElementById('new-class-row-container');
  const tpl = document.getElementById('new-class-template');
  const clone = tpl.content.cloneNode(true);
  container.prepend(clone);

  const emptyMsg = document.getElementById('empty-classes-msg');
  if (emptyMsg) emptyMsg.style.display = 'none';

  const firstSelect = container.querySelector('form select');
  if (firstSelect) firstSelect.focus();
}

// Single-select day toggle within a "new class" row
function selectDay(btn) {
  const row = btn.closest('.new-row-day-pills');
  row.querySelectorAll('.sp-day-pill').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const form = btn.closest('form');
  form.querySelector('.new-row-day-input').value = btn.dataset.day;
}

// ── HOURS RANGE FILTER (client-side only — shows/hides grid rows) ───────────
const GRID_HOURS = [
  @foreach ($slots as $slot)
    { start: "{{ $slot['start'] }}", label: "{{ \Carbon\Carbon::parse($slot['start'])->format('g:i A') }}" },
  @endforeach
];

function populateHourSelectors() {
  const startSel = document.getElementById('hour-start');
  const endSel = document.getElementById('hour-end');
  if (!startSel || !endSel) return;

  GRID_HOURS.forEach((h) => {
    const o1 = document.createElement('option');
    o1.value = h.start; o1.textContent = h.label;
    startSel.appendChild(o1);

    const o2 = document.createElement('option');
    o2.value = h.start; o2.textContent = h.label;
    endSel.appendChild(o2);
  });

  startSel.value = GRID_HOURS[0]?.start ?? '';
  endSel.value = GRID_HOURS[GRID_HOURS.length - 1]?.start ?? '';
}

function applyHourFilter() {
  const start = document.getElementById('hour-start').value;
  const end = document.getElementById('hour-end').value;
  document.querySelectorAll('#plotter-grid tbody tr').forEach(row => {
    const rowStart = row.dataset.start;
    const visible = rowStart >= start && rowStart <= end;
    row.style.display = visible ? '' : 'none';
  });
}

function clearHourFilter() {
  const startSel = document.getElementById('hour-start');
  const endSel = document.getElementById('hour-end');
  if (!startSel || !endSel || GRID_HOURS.length === 0) return;
  startSel.value = GRID_HOURS[0].start;
  endSel.value = GRID_HOURS[GRID_HOURS.length - 1].start;
  applyHourFilter();
}

populateHourSelectors();
applyHourFilter();
</script>
</body>
</html>