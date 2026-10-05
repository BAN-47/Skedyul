<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — My Schedule</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">

    @php
      // Flatten $schedules into the shape faculty_header.blade.php expects
      // for its "Today's Schedule" notification feed.
      $scheduleForJs = [];
      foreach (($schedules ?? collect()) as $sch) {
        $scheduleForJs[] = [
          'code'  => $sch->subject->subj_code ?? 'N/A',
          'room'  => $sch->room->room_name ?? 'N/A',
          'day'   => $sch->sch_day ?? '',
          'start' => \Carbon\Carbon::parse($sch->sch_start_time)->format('H:i'),
          'end'   => \Carbon\Carbon::parse($sch->sch_end_time)->format('H:i'),
        ];
      }
    @endphp

    @include('partials.faculty_header', [
        'title' => 'My Schedule',
        'scheduleFeed' => $scheduleForJs,
        'announcements' => $announcements ?? null,
    ])

    <!-- FACULTY SCHEDULE PAGE -->
    <div id="page-faculty-schedule" class="page active">

      <!-- Currently Using Room -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-5">
        <div class="flex items-center justify-between mb-4">
          <div>
            <div class="text-base font-bold text-gray-900">Currently Using Room</div>
            <div class="text-xs text-gray-500 mt-0.5">Live session status</div>
          </div>
          <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-full {{ $currentSchedule ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-500' }}">
            {{ $currentSchedule ? 'Now in Session' : 'No Active Class' }}
          </span>
        </div>

        @if ($currentSchedule)
          <div class="rounded-xl p-5 flex items-start justify-between gap-4 bg-gradient-to-br from-slate-900 to-blue-900"
               data-start="{{ $currentSchedule->sch_start_time }}" data-end="{{ $currentSchedule->sch_end_time }}" id="live-room-block">
            <div>
              <div class="text-[10px] font-bold text-white/40 uppercase tracking-[1.2px] mb-1.5">Now in Session</div>
              <div class="text-xl font-extrabold text-white">{{ $currentSchedule->room->room_name ?? 'N/A' }}</div>
              <div class="text-sm text-white/60 mt-0.5">{{ $currentSchedule->subject->subj_code ?? '' }} — {{ $currentSchedule->subject->subj_name ?? 'N/A' }}</div>
              <div class="text-xs text-white/40 mt-0.5">{{ $currentSchedule->section->sec_name ?? 'N/A' }} · {{ $currentSchedule->sch_start_time }}–{{ $currentSchedule->sch_end_time }}</div>
              <div class="mt-3.5">
                <div class="flex justify-between text-[11px] text-white/35 mb-1.5">
                  <span>{{ $currentSchedule->sch_start_time }}</span><span>{{ $currentSchedule->sch_end_time }}</span>
                </div>
                <div class="h-1.5 bg-white/10 rounded-full overflow-hidden w-[280px]">
                  <div id="web-room-progress" class="h-full bg-green-400 rounded-full transition-[width] duration-1000 ease-linear" style="width:0%;"></div>
                </div>
              </div>
            </div>
            <div class="text-right shrink-0">
              <div class="text-[11px] text-white/40 mb-1.5">Time Remaining</div>
              <div id="web-room-countdown" class="text-4xl font-extrabold text-green-400 font-mono tracking-[2px]">--:--</div>
              <div class="text-[11px] text-white/30 mt-0.5">min : sec</div>
            </div>
          </div>

          <div class="flex items-center gap-3 mt-3.5 pt-3.5 border-t border-gray-200">
            <div class="w-9 h-9 rounded-full bg-green-600 flex items-center justify-center text-[13px] font-extrabold text-white shrink-0">
              {{ strtoupper(substr($faculty->fac_first_name,0,1) . substr($faculty->fac_last_name,0,1)) }}
            </div>
            <div>
              <div class="text-[13px] font-bold text-gray-900">{{ $faculty->full_name }}</div>
              <div class="text-[11px] text-gray-500">Assigned Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div>
            </div>
            <span class="inline-block text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-100 text-green-600 ml-auto">Active</span>
          </div>

          @if ($nextInRoom)
            <div class="mt-3 p-3 bg-gray-50 rounded-[10px]">
              <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wide mb-1">Next Class in this Room</div>
              <div class="text-[13px] font-semibold text-gray-900">{{ $nextInRoom->subject->subj_code ?? '' }} — {{ $nextInRoom->subject->subj_name ?? 'N/A' }}</div>
              <div class="text-[11px] text-gray-500 mt-0.5">{{ $nextInRoom->sch_start_time }} · {{ $nextInRoom->section->sec_name ?? 'N/A' }}</div>
            </div>
          @endif
        @else
          <div class="py-6 text-center text-gray-500 text-sm">
            You don't have a class in session right now.
          </div>
        @endif
      </div>

      <!-- Weekly Overview with clickable events -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="mb-4">
          <div class="text-base font-bold text-gray-900">Weekly Overview</div>
          <div class="text-xs text-gray-500 mt-0.5">{{ $activeSemester->sem_name ?? 'Current Semester' }} · Click any subject to view details</div>
        </div>

        @php
          $allTimes = $schedules->pluck('sch_start_time')->unique()->sort()->values();
          $colorCycle = ['blue', 'amber', 'green', 'teal', 'purple'];
          $subjectColors = [];
          $colorIndex = 0;

          // Tailwind class sets per color key (replaces schedule.css's .sg-event.{color} rules)
          $colorMap = [
            'blue'   => 'bg-blue-50 border-blue-600 text-blue-700',
            'amber'  => 'bg-amber-50 border-amber-600 text-amber-700',
            'green'  => 'bg-green-50 border-green-600 text-green-700',
            'teal'   => 'bg-teal-50 border-teal-600 text-teal-700',
            'purple' => 'bg-purple-50 border-purple-600 text-purple-700',
          ];
        @endphp

        <div class="overflow-x-auto rounded-xl border border-gray-200">
          <div class="grid min-w-[720px]" style="grid-template-columns: 80px repeat({{ count($weekDays) }}, 1fr);">
            <div class="bg-gray-100 text-gray-900 font-bold text-xs uppercase tracking-wide px-2 py-2.5 text-center border-b border-gray-200">Time</div>
            @foreach ($weekDays as $day)
              <div class="bg-gray-100 text-gray-900 font-bold text-xs uppercase tracking-wide px-2 py-2.5 text-center border-b border-l border-gray-200">{{ $day }}</div>
            @endforeach

            @forelse ($allTimes as $time)
              <div class="text-[11px] font-semibold text-gray-500 font-mono px-2 py-2 text-center border-t border-gray-100 whitespace-nowrap">{{ \Carbon\Carbon::createFromTimeString($time)->format('g:i') }}</div>
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
                <div class="border-t border-l border-gray-100 p-1 min-h-[56px]">
                  @if ($sch)
                    <div class="subject-event rounded-lg border-l-4 px-2 py-1.5 text-[11px] leading-tight cursor-pointer hover:brightness-95 transition {{ $colorMap[$color] ?? $colorMap['blue'] }}"
                         data-code="{{ $sch->subject->subj_code ?? '' }}"
                         data-name="{{ $sch->subject->subj_name ?? '' }}"
                         data-units="{{ $sch->subject->subj_units ?? '' }}"
                         data-lec="{{ $sch->subject->subj_lec_hours ?? 0 }}"
                         data-lab="{{ $sch->subject->subj_lab_hours ?? 0 }}"
                         data-dept="{{ $faculty->department->dept_code ?? '' }}"
                         data-room="{{ $sch->room->room_name ?? 'N/A' }}"
                         data-section="{{ $sch->section->sec_name ?? 'N/A' }}"
                         data-schedule="{{ $sch->sch_day }} {{ $sch->sch_start_time }}–{{ $sch->sch_end_time }}"
                         data-color="#2563eb">
                      <b class="block font-bold">{{ $sch->subject->subj_code ?? '' }}</b>
                      <span class="block opacity-80 mt-0.5">{{ $sch->room->room_name ?? '' }} · {{ $sch->section->sec_name ?? '' }}</span>
                    </div>
                  @endif
                </div>
              @endforeach
            @empty
              <div class="text-[11px] font-semibold text-gray-500 px-2 py-2 text-center border-t border-gray-100">—</div>
              @foreach ($weekDays as $day)
                <div class="border-t border-l border-gray-100 p-1 min-h-[56px]"></div>
              @endforeach
            @endforelse
          </div>
        </div>

        <!-- Legend -->
        <div class="flex gap-4 mt-4 pt-3.5 border-t border-gray-200 flex-wrap items-center">
          @foreach ($schedules->unique('sch_subj_id') as $sch)
            <div class="flex items-center gap-1.5 text-xs text-gray-700">
              <div class="w-3 h-3 rounded-[3px] bg-blue-100 border-l-4 border-blue-600"></div>
              {{ $sch->subject->subj_code ?? '' }} — {{ $sch->subject->subj_name ?? '' }}
            </div>
          @endforeach
          <div class="text-xs text-gray-400 ml-auto">Click any subject block to view details</div>
        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- FACULTY SUBJECT DETAIL MODAL (Tailwind) -->
<div class="modal-overlay fixed inset-0 z-[200] hidden items-center justify-center bg-black/40 backdrop-blur-sm [&.open]:flex" id="modal-web-subject-detail">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[480px] max-h-[90vh] overflow-y-auto p-6">
    <div class="flex items-center justify-between mb-5">
      <div class="text-lg font-bold text-gray-900">Subject Details</div>
      <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm" onclick="closeModal('modal-web-subject-detail')">✕</button>
    </div>

    <div id="wsd-header" class="px-[18px] py-4 rounded-xl mb-[18px]">
      <div id="wsd-code" class="text-[11px] font-bold text-white/60 uppercase tracking-wide mb-1"></div>
      <div id="wsd-name" class="text-xl font-extrabold text-white"></div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-3.5">
      <div class="bg-gray-50 rounded-[10px] p-3.5">
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Units</div>
        <div id="wsd-units" class="text-2xl font-extrabold text-gray-900"></div>
      </div>
      <div class="bg-gray-50 rounded-[10px] p-3.5">
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Hours</div>
        <div id="wsd-hours" class="text-[13px] font-semibold text-gray-900 mt-1"></div>
      </div>
      <div class="bg-gray-50 rounded-[10px] p-3.5">
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Room</div>
        <div id="wsd-room" class="text-base font-bold text-gray-900 mt-0.5"></div>
      </div>
      <div class="bg-gray-50 rounded-[10px] p-3.5">
        <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Section</div>
        <div id="wsd-section" class="text-base font-bold text-gray-900 mt-0.5"></div>
      </div>
    </div>

    <div class="bg-gray-50 rounded-[10px] p-3.5 mb-3">
      <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Schedule</div>
      <div id="wsd-schedule" class="text-[13px] font-semibold text-gray-900"></div>
    </div>

    <div class="bg-gray-50 rounded-[10px] p-3.5 mb-[18px]">
      <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wide mb-1">Faculty</div>
      <div class="text-[13px] font-semibold text-gray-900">{{ $faculty->full_name }}</div>
      <div class="text-[11px] text-gray-500 mt-0.5">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div>
    </div>

    <div class="flex justify-end gap-2 mt-2">
      <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="closeModal('modal-web-subject-detail')">Close</button>
    </div>
  </div>
</div>

<!-- TOAST (Tailwind) -->
<div class="toast fixed bottom-6 right-6 z-[300] bg-gray-900 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg opacity-0 translate-y-2 pointer-events-none transition-all duration-300 [&.show]:opacity-100 [&.show]:translate-y-0 [&.show]:pointer-events-auto" id="toast">✅ <span id="toast-msg"></span></div>

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

function openWebSubjectDetail(code, name, units, lec, lab, dept, room, section, schedule, color) {
  document.getElementById('wsd-header').style.background = 'linear-gradient(135deg,' + color + ',#0f172a)';
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
      el.dataset.schedule, el.dataset.color
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
    if (remaining <= 0) { el.textContent = '00:00'; el.style.color = '#f87171'; if (prog) prog.style.width = '100%'; return; }
    const mins = Math.floor(remaining / 60000);
    const secs = Math.floor((remaining % 60000) / 1000);
    el.textContent = String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
    el.style.color = mins < 5 ? '#f87171' : mins < 15 ? '#fbbf24' : '#4ade80';
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