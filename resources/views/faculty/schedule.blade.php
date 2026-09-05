<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — My Schedule</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty/schedule.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">My Schedule</div>
    </div>

    <!-- FACULTY SCHEDULE PAGE -->
    <div id="page-faculty-schedule" class="page active">

      <!-- Currently Using Room -->
      <div class="card" style="margin-bottom:20px;">
        <div class="card-header">
          <div><div class="card-title">Currently Using Room</div><div class="card-sub">Live session status</div></div>
          <span class="badge {{ $currentSchedule ? 'badge-green' : 'badge-grey' }}">
            {{ $currentSchedule ? 'Now in Session' : 'No Active Class' }}
          </span>
        </div>

        @if ($currentSchedule)
          <div style="background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:12px;padding:20px;display:flex;align-items:flex-start;justify-content:space-between;gap:16px;"
               data-start="{{ $currentSchedule->sch_start_time }}" data-end="{{ $currentSchedule->sch_end_time }}" id="live-room-block">
            <div>
              <div style="font-size:10px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.2px;margin-bottom:6px;">Now in Session</div>
              <div style="font-size:22px;font-weight:800;color:#fff;">{{ $currentSchedule->room->room_name ?? 'N/A' }}</div>
              <div style="font-size:14px;color:rgba(255,255,255,0.6);margin-top:3px;">{{ $currentSchedule->subject->subj_code ?? '' }} — {{ $currentSchedule->subject->subj_name ?? 'N/A' }}</div>
              <div style="font-size:12px;color:rgba(255,255,255,0.4);margin-top:2px;">{{ $currentSchedule->section->sec_name ?? 'N/A' }} · {{ $currentSchedule->sch_start_time }}–{{ $currentSchedule->sch_end_time }}</div>
              <div style="margin-top:14px;">
                <div style="display:flex;justify-content:space-between;font-size:11px;color:rgba(255,255,255,0.35);margin-bottom:5px;">
                  <span>{{ $currentSchedule->sch_start_time }}</span><span>{{ $currentSchedule->sch_end_time }}</span>
                </div>
                <div style="height:6px;background:rgba(255,255,255,0.12);border-radius:20px;overflow:hidden;width:280px;">
                  <div id="web-room-progress" style="height:100%;background:#4ade80;border-radius:20px;transition:width 1s linear;width:0%;"></div>
                </div>
              </div>
            </div>
            <div style="text-align:right;flex-shrink:0;">
              <div style="font-size:11px;color:rgba(255,255,255,0.4);margin-bottom:6px;">Time Remaining</div>
              <div id="web-room-countdown" style="font-size:36px;font-weight:800;color:#4ade80;font-family:var(--mono);letter-spacing:2px;">--:--</div>
              <div style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:3px;">min : sec</div>
            </div>
          </div>
          <div style="display:flex;align-items:center;gap:12px;margin-top:14px;padding-top:14px;border-top:1px solid var(--border);">
            <div style="width:36px;height:36px;border-radius:50%;background:#16a34a;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;">
              {{ strtoupper(substr($faculty->fac_first_name,0,1) . substr($faculty->fac_last_name,0,1)) }}
            </div>
            <div><div style="font-size:13px;font-weight:700;color:var(--text);">{{ $faculty->full_name }}</div><div style="font-size:11px;color:var(--text3);">Assigned Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div></div>
            <span class="badge badge-green" style="margin-left:auto;">Active</span>
          </div>
          @if ($nextInRoom)
            <div style="margin-top:12px;padding:12px;background:var(--grey);border-radius:10px;">
              <div style="font-size:11px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Next Class in this Room</div>
              <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $nextInRoom->subject->subj_code ?? '' }} — {{ $nextInRoom->subject->subj_name ?? 'N/A' }}</div>
              <div style="font-size:11px;color:var(--text3);margin-top:2px;">{{ $nextInRoom->sch_start_time }} · {{ $nextInRoom->section->sec_name ?? 'N/A' }}</div>
            </div>
          @endif
        @else
          <div style="padding:24px;text-align:center;color:var(--text3);font-size:13px;">
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
        <div class="schedule-grid-wrap">
          <div class="schedule-grid">
            <div class="sg-header">Time</div>
            @foreach ($weekDays as $day)
              <div class="sg-header">{{ $day }}</div>
            @endforeach

            @php
              $allTimes = $schedules->pluck('sch_start_time')->unique()->sort()->values();
              $colorCycle = ['blue', 'amber', 'green', 'teal', 'purple'];
              $subjectColors = [];
              $colorIndex = 0;
            @endphp

            @forelse ($allTimes as $time)
              <div class="sg-time">{{ \Carbon\Carbon::createFromTimeString($time)->format('g:i') }}</div>
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
                <div class="sg-cell">
                  @if ($sch)
                    <div class="sg-event {{ $color }} subject-event"
                         style="cursor:pointer;"
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
                      <b>{{ $sch->subject->subj_code ?? '' }}</b>
                      <span>{{ $sch->room->room_name ?? '' }} · {{ $sch->section->sec_name ?? '' }}</span>
                    </div>
                  @endif
                </div>
              @endforeach
            @empty
              <div class="sg-time">—</div>
              @foreach ($weekDays as $day)
                <div class="sg-cell"></div>
              @endforeach
            @endforelse
          </div>
        </div>
        <!-- Legend -->
        <div style="display:flex;gap:16px;margin-top:16px;padding-top:14px;border-top:1px solid var(--border);flex-wrap:wrap;align-items:center;">
          @foreach ($schedules->unique('sch_subj_id') as $sch)
            <div style="display:flex;align-items:center;gap:6px;font-size:12px;">
              <div style="width:12px;height:12px;border-radius:3px;background:#dbeafe;border-left:3px solid #2563eb;"></div>
              {{ $sch->subject->subj_code ?? '' }} — {{ $sch->subject->subj_name ?? '' }}
            </div>
          @endforeach
          <div style="font-size:12px;color:var(--text3);margin-left:auto;">Click any subject block to view details</div>
        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- FACULTY SUBJECT DETAIL MODAL -->
<div class="modal-overlay" id="modal-web-subject-detail">
  <div class="modal" style="width:480px;">
    <div class="modal-header">
      <div class="modal-title">Subject Details</div>
      <button class="modal-close" onclick="closeModal('modal-web-subject-detail')">✕</button>
    </div>
    <div id="wsd-header" style="padding:16px 18px;border-radius:12px;margin-bottom:18px;">
      <div id="wsd-code" style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.6);text-transform:uppercase;letter-spacing:1px;margin-bottom:4px;"></div>
      <div id="wsd-name" style="font-size:20px;font-weight:800;color:#fff;"></div>
    </div>
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:14px;">
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Units</div>
        <div id="wsd-units" style="font-size:26px;font-weight:800;color:var(--text);"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Hours</div>
        <div id="wsd-hours" style="font-size:13px;font-weight:600;color:var(--text);margin-top:4px;"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Room</div>
        <div id="wsd-room" style="font-size:16px;font-weight:700;color:var(--text);margin-top:2px;"></div>
      </div>
      <div style="background:var(--grey);border-radius:10px;padding:14px;">
        <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Section</div>
        <div id="wsd-section" style="font-size:16px;font-weight:700;color:var(--text);margin-top:2px;"></div>
      </div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:12px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Schedule</div>
      <div id="wsd-schedule" style="font-size:13px;font-weight:600;color:var(--text);"></div>
    </div>
    <div style="background:var(--grey);border-radius:10px;padding:14px;margin-bottom:18px;">
      <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Faculty</div>
      <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $faculty->full_name }}</div>
      <div style="font-size:11px;color:var(--text3);margin-top:2px;">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div>
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