<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<title>SKEDYUL — My Schedule</title>
</head>
<body class="bg-slate-50 font-sans text-slate-900 dark:bg-slate-950 dark:text-slate-100">

<div id="screen-app" class="screen active flex-row">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">My Schedule</div>
    </div>

    <!-- FACULTY SCHEDULE PAGE -->
    <div id="page-faculty-schedule" class="page active">

      <!-- Currently Using Room -->
      <div class="card mb-5">
        <div class="card-header">
          <div><div class="card-title">Currently Using Room</div><div class="card-sub">Live session status</div></div>
          <span class="badge {{ $currentSchedule ? 'badge-green' : 'badge-grey' }}">
            {{ $currentSchedule ? 'Now in Session' : 'No Active Class' }}
          </span>
        </div>

        @if ($currentSchedule)
          <div class="flex items-start justify-between gap-4 rounded-xl bg-gradient-to-br from-slate-900 to-blue-900 p-5"
               data-start="{{ $currentSchedule->sch_start_time }}" data-end="{{ $currentSchedule->sch_end_time }}" id="live-room-block">
            <div>
              <div class="mb-1.5 text-[10px] font-bold uppercase tracking-[1.2px] text-white/40">Now in Session</div>
              <div class="text-[22px] font-extrabold text-white">{{ $currentSchedule->room->room_name ?? 'N/A' }}</div>
              <div class="mt-0.5 text-sm text-white/60">{{ $currentSchedule->subject->subj_code ?? '' }} — {{ $currentSchedule->subject->subj_name ?? 'N/A' }}</div>
              <div class="mt-0.5 text-xs text-white/40">{{ $currentSchedule->section->sec_name ?? 'N/A' }} · {{ $currentSchedule->sch_start_time }}–{{ $currentSchedule->sch_end_time }}</div>
              <div class="mt-3.5">
                <div class="mb-1 flex justify-between text-[11px] text-white/40">
                  <span>{{ $currentSchedule->sch_start_time }}</span><span>{{ $currentSchedule->sch_end_time }}</span>
                </div>
                <div class="h-1.5 w-[280px] max-w-full overflow-hidden rounded-full bg-white/10">
                  <div id="web-room-progress" class="h-full w-0 rounded-full bg-green-400 transition-[width] duration-1000"></div>
                </div>
              </div>
            </div>
            <div class="shrink-0 text-right">
              <div class="mb-1.5 text-[11px] text-white/40">Time Remaining</div>
              <div id="web-room-countdown" class="font-mono text-4xl font-extrabold tracking-[2px] text-green-400">--:--</div>
              <div class="mt-0.5 text-[11px] text-white/30">min : sec</div>
            </div>
          </div>
          <div class="mt-3.5 flex items-center gap-3 border-t border-slate-200 pt-3.5 dark:border-slate-800">
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-green-600 text-[13px] font-extrabold text-white">
              {{ strtoupper(substr($faculty->fac_first_name,0,1) . substr($faculty->fac_last_name,0,1)) }}
            </div>
            <div><div class="text-[13px] font-bold text-slate-900 dark:text-slate-100">{{ $faculty->full_name }}</div><div class="text-[11px] text-slate-400">Assigned Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div></div>
            <span class="badge badge-green ml-auto">Active</span>
          </div>
          @if ($nextInRoom)
            <div class="mt-3 rounded-xl bg-slate-50 p-3 dark:bg-slate-800">
              <div class="mb-1 text-[11px] font-bold uppercase tracking-wide text-slate-400">Next Class in this Room</div>
              <div class="text-[13px] font-semibold text-slate-900 dark:text-slate-100">{{ $nextInRoom->subject->subj_code ?? '' }} — {{ $nextInRoom->subject->subj_name ?? 'N/A' }}</div>
              <div class="mt-0.5 text-[11px] text-slate-400">{{ $nextInRoom->sch_start_time }} · {{ $nextInRoom->section->sec_name ?? 'N/A' }}</div>
            </div>
          @endif
        @else
          <div class="p-6 text-center text-[13px] text-slate-400">
            You don't have a class in session right now.
          </div>
        @endif
      </div>

      <!-- Weekly Overview with clickable events -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Weekly Overview</div>
            <div class="card-sub">{{ $activeSemester->sem_name ?? 'Current Semester' }} · Click any subject to view details</div>
          </div>
        </div>
        <div class="overflow-auto">
          <div class="grid min-w-[700px] grid-cols-[80px_repeat(6,minmax(0,1fr))] gap-px overflow-hidden rounded-xl bg-slate-200 dark:bg-slate-700">
            <div class="bg-[#1a2d5a] px-2 py-2.5 text-center text-[11px] font-bold uppercase tracking-wide text-white">Time</div>
            @foreach ($weekDays as $day)
              <div class="bg-[#1a2d5a] px-2 py-2.5 text-center text-[11px] font-bold uppercase tracking-wide text-white">{{ $day }}</div>
            @endforeach

            @php
              $allTimes = $schedules->pluck('sch_start_time')->unique()->sort()->values();
              $colorCycle = ['blue', 'amber', 'green', 'teal', 'purple'];
              $subjectColors = [];
              $colorIndex = 0;
            @endphp

            @forelse ($allTimes as $time)
              <div class="bg-slate-100 p-2 text-center font-mono text-[11px] font-semibold text-slate-400 dark:bg-slate-800">{{ \Carbon\Carbon::createFromTimeString($time)->format('g:i') }}</div>
              @foreach ($weekDays as $day)
                @php
                  $sch = $byDay[$day]->firstWhere('sch_start_time', $time);
                  if ($sch) {
                    $subjId = $sch->sch_subj_id;
                    if (!isset($subjectColors[$subjId])) {
                      $subjectColors[$subjId] = $colorCycle[$colorIndex % count($colorCycle)];
                      $colorIndex++;
                    }
                    $color = $subjectColors[$subjId];
                  }
                @endphp
                <div class="relative min-h-[52px] bg-white p-1 dark:bg-slate-900">
                  @if ($sch)
                    @php
                      $eventColorClasses = [
                        'blue' => 'border-blue-600 bg-blue-100 text-blue-900',
                        'amber' => 'border-amber-600 bg-amber-100 text-amber-900',
                        'green' => 'border-green-600 bg-green-100 text-green-900',
                        'teal' => 'border-cyan-600 bg-cyan-100 text-cyan-900',
                        'purple' => 'border-violet-600 bg-violet-100 text-violet-900',
                      ][$color];
                    @endphp
                    <div class="subject-event flex h-full cursor-pointer flex-col justify-center rounded-md border-l-[3px] px-2 py-1.5 text-[11px] font-semibold leading-tight {{ $eventColorClasses }}"
                         data-code="{{ $sch->subject->subj_code ?? '' }}"
                         data-name="{{ $sch->subject->subj_name ?? '' }}"
                         data-units="{{ $sch->subject->subj_units ?? '' }}"
                         data-lec="{{ $sch->subject->subj_lec_hours ?? 0 }}"
                         data-lab="{{ $sch->subject->subj_lab_hours ?? 0 }}"
                         data-dept="{{ $faculty->department->dept_code ?? '' }}"
                         data-room="{{ $sch->room->room_name ?? 'N/A' }}"
                         data-section="{{ $sch->section->sec_name ?? 'N/A' }}"
                         data-schedule="{{ $sch->sch_day }} {{ $sch->sch_start_time }}–{{ $sch->sch_end_time }}">
                      <b>{{ $sch->subject->subj_code ?? '' }}</b>
                      <span>{{ $sch->room->room_name ?? '' }} · {{ $sch->section->sec_name ?? '' }}</span>
                    </div>
                  @endif
                </div>
              @endforeach
            @empty
              <div class="bg-slate-100 p-2 text-center font-mono text-[11px] font-semibold text-slate-400 dark:bg-slate-800">—</div>
              @foreach ($weekDays as $day)
                <div class="relative min-h-[52px] bg-white p-1 dark:bg-slate-900"></div>
              @endforeach
            @endforelse
          </div>
        </div>
        <!-- Legend -->
        <div class="mt-4 flex flex-wrap items-center gap-4 border-t border-slate-200 pt-3.5 dark:border-slate-800">
          @foreach ($schedules->unique('sch_subj_id') as $sch)
            <div class="flex items-center gap-1.5 text-xs">
              <div class="h-3 w-3 rounded-sm border-l-[3px] border-blue-600 bg-blue-100"></div>
              {{ $sch->subject->subj_code ?? '' }} — {{ $sch->subject->subj_name ?? '' }}
            </div>
          @endforeach
          <div class="ml-auto text-xs text-slate-400">Click any subject block to view details</div>
        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- FACULTY SUBJECT DETAIL MODAL -->
<div class="modal-overlay" id="modal-web-subject-detail">
  <div class="modal w-[480px] max-w-[92vw] bg-white p-6 dark:bg-slate-900">
    <div class="modal-header">
      <div class="modal-title">Subject Details</div>
      <button class="modal-close" onclick="closeModal('modal-web-subject-detail')">✕</button>
    </div>
    <div id="wsd-header" class="mb-[18px] rounded-xl bg-gradient-to-br from-blue-600 to-slate-900 px-[18px] py-4">
      <div id="wsd-code" class="mb-1 text-[11px] font-bold uppercase tracking-wide text-white/60"></div>
      <div id="wsd-name" class="text-xl font-extrabold text-white"></div>
    </div>
    <div class="mb-3.5 grid grid-cols-2 gap-3">
      <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
        <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Units</div>
        <div id="wsd-units" class="text-[26px] font-extrabold text-slate-900 dark:text-slate-100"></div>
      </div>
      <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
        <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Hours</div>
        <div id="wsd-hours" class="mt-1 text-[13px] font-semibold text-slate-900 dark:text-slate-100"></div>
      </div>
      <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
        <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Room</div>
        <div id="wsd-room" class="mt-0.5 text-base font-bold text-slate-900 dark:text-slate-100"></div>
      </div>
      <div class="rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
        <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Section</div>
        <div id="wsd-section" class="mt-0.5 text-base font-bold text-slate-900 dark:text-slate-100"></div>
      </div>
    </div>
    <div class="mb-3 rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
      <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Schedule</div>
      <div id="wsd-schedule" class="text-[13px] font-semibold text-slate-900 dark:text-slate-100"></div>
    </div>
    <div class="mb-[18px] rounded-xl bg-slate-50 p-3.5 dark:bg-slate-800">
      <div class="mb-1 text-[10px] font-bold uppercase tracking-wide text-slate-400">Faculty</div>
      <div class="text-[13px] font-semibold text-slate-900 dark:text-slate-100">{{ $faculty->full_name }}</div>
      <div class="mt-0.5 text-[11px] text-slate-400">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-web-subject-detail')">Close</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function openWebSubjectDetail(code, name, units, lec, lab, dept, room, section, schedule) {
  document.getElementById('wsd-code').textContent = code + ' · ' + dept;
  document.getElementById('wsd-name').textContent = name;
  document.getElementById('wsd-units').textContent = units + 'u';
  document.getElementById('wsd-hours').textContent = lec + 'h Lecture' + (parseInt(lab) > 0 ? ' · ' + lab + 'h Lab' : '');
  document.getElementById('wsd-room').textContent = room;
  document.getElementById('wsd-section').textContent = section;
  document.getElementById('wsd-schedule').textContent = schedule;
  openModal('modal-web-subject-detail');
}

// Wire up subject blocks via data attributes (avoids quote-escaping issues)
document.querySelectorAll('.subject-event').forEach(el => {
  el.addEventListener('click', () => {
    openWebSubjectDetail(
      el.dataset.code, el.dataset.name, el.dataset.units, el.dataset.lec,
      el.dataset.lab, el.dataset.dept, el.dataset.room, el.dataset.section,
      el.dataset.schedule
    );
  });
});

// ── LIVE ROOM COUNTDOWN (real start/end from server) ────────────────────
function startWebRoomCountdown() {
  const block = document.getElementById('live-room-block');
  if (!block) return; // no active session right now

  const [sh, sm] = block.dataset.start.split(':').map(Number);
  const [eh, em] = block.dataset.end.split(':').map(Number);
  const start = new Date(); start.setHours(sh, sm, 0, 0);
  const end = new Date(); end.setHours(eh, em, 0, 0);

  function tick() {
    const cur = new Date();
    const remaining = end - cur;
    const total = end - start;
    const el = document.getElementById('web-room-countdown');
    const prog = document.getElementById('web-room-progress');
    if (!el) return;
    if (remaining <= 0) {
      el.textContent = '00:00';
      el.classList.remove('text-amber-300', 'text-green-400');
      el.classList.add('text-red-400');
      if (prog) prog.style.width = '100%';
      return;
    }
    const mins = Math.floor(remaining / 60000);
    const secs = Math.floor((remaining % 60000) / 1000);
    el.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    el.classList.remove('text-red-400', 'text-amber-300', 'text-green-400');
    el.classList.add(mins < 5 ? 'text-red-400' : mins < 15 ? 'text-amber-300' : 'text-green-400');
    if (prog) prog.style.width = Math.min(100, Math.max(0, ((cur - start) / total) * 100)) + '%';
  }
  tick();
  setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
  startWebRoomCountdown();
});
</script>
</body>
</html>