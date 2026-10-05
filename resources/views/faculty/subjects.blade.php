<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<title>SKEDYUL — My Subjects</title>
</head>
<body class="bg-slate-50 font-sans text-slate-900 dark:bg-slate-950 dark:text-slate-100">

<div id="screen-app" class="screen active flex-row">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">

    @php
      // ── Build the weekly grid from the flat $subjects collection ─────────
      $gridDays = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];

      // Rotating palette so each subject keeps a consistent color across the grid
      $palette = ['#e07a5f','#81b29a','#6fa8dc','#3d5a80','#f2a154','#a8d5a2','#c65b7c','#4a90a4'];
      $subjColors = [];
      $colorIdx = 0;

      // day => [ hour => block ]   (block placed only on its start hour; rowspan covers the rest)
      $dayBlocks = [];
      $hasNightSched = false; // true once any class starts at/after 4:30 PM (16:30)

      // Left-side "Summary of Subjects" panel — one row per unique subject code
      $summaryRows = [];

      // Flat list (not keyed by hour) — used both by the header's "today's schedule"
      // feed and, further below, to build the grid.
      $scheduleForJs = [];

      // Messages from the chair/dean — wire this to a real announcements query
      // (e.g. $announcements = Announcement::forFaculty($faculty->id)->latest()->get())
      // once that table/relationship exists; this sample array is just a placeholder.
      $announcements = $announcements ?? [
          ['from' => 'Chair Rodrigo Tan', 'role' => 'BSIS Chair', 'initials' => 'RT', 'color' => '#d97706',
           'message' => 'Please submit your consultation hours schedule by Friday.', 'time' => '2h ago'],
          ['from' => 'Dean Villaceran', 'role' => 'Dean, CCICT', 'initials' => 'DV', 'color' => '#0891b2',
           'message' => 'IS102 will be under maintenance next Monday — classes moved to IS201.', 'time' => 'Yesterday'],
      ];

      // Guard: keep Summary + grid renderable even when controller passes null/empty
      $subjects = $subjects ?? collect();

      foreach ($subjects as $entry) {
          $subj = $entry['subject'];
          $code = $subj->subj_code ?? 'N/A';

          if (!isset($subjColors[$code])) {
              $subjColors[$code] = $palette[$colorIdx % count($palette)];
              $colorIdx++;
          }

          if (!isset($summaryRows[$code])) {
              $summaryRows[$code] = [
                  'code'     => $code,
                  'name'     => $subj->subj_name ?? 'N/A',
                  // wire this to a real enrollment count (e.g. $entry['total_students'])
                  // once that figure is available from the controller
                  'students' => $entry['total_students'] ?? '—',
                  'color'    => $subjColors[$code],
              ];
          }

          foreach ($entry['schedules'] as $sched) {
              $startTs = \Carbon\Carbon::parse($sched['start']);
              $endTs   = \Carbon\Carbon::parse($sched['end']);

              $startHour = (int) $startTs->format('H');
              $endHour   = (int) $endTs->format('H');
              if ((int) $endTs->format('i') > 0) {
                  $endHour++; // round a partial hour up so the block still gets a row
              }
              $duration = max(1, $endHour - $startHour);

              // Night table starts at 4:30 PM — treat 16:30+ (and any 16:xx class) as night
              if ($startHour > 16 || ($startHour === 16 && (int) $startTs->format('i') >= 30)) {
                  $hasNightSched = true;
              }
              // Classes that begin in the 4 PM hour land on the night grid (hour key 16)
              if ($startHour === 16) {
                  // keep startHour = 16 for night row placement
              }

              $scheduleForJs[] = [
                  'code'  => $code,
                  'room'  => $entry['room'],
                  'day'   => $sched['day'],
                  'start' => $startTs->format('H:i'),
                  'end'   => $endTs->format('H:i'),
              ];

              $dayBlocks[$sched['day']][$startHour] = [
                  'code'      => $code,
                  'name'      => $subj->subj_name ?? 'N/A',
                  'units'     => $subj->subj_units ?? '—',
                  'lec'       => $subj->subj_lec_hours ?? 0,
                  'lab'       => $subj->subj_lab_hours ?? 0,
                  'room'      => $entry['room'],
                  'section'   => $entry['sections'],
                  'start'     => $startTs->format('g:i A'),
                  'end'       => $endTs->format('g:i A'),
                  'start_raw' => $startTs->format('H:i'),
                  'end_raw'   => $endTs->format('H:i'),
                  'duration'  => $duration,
                  'color'     => $subjColors[$code],
              ];
          }
      }

      // Track, per day, which hours are already "consumed" by a rowspan above them
      $skipUntil = array_fill_keys($gridDays, 0);

      $dayRange   = range(7, 15);  // 7AM – 4PM (last row: 3–4 PM)
      $nightRange = range(16, 21); // Night table: 4:30 PM – 10PM
    @endphp

    @include('partials.faculty_header', [
        'title' => 'My Subjects',
        'scheduleFeed' => $scheduleForJs,
        'announcements' => $announcements,
    ])

    <!-- FACULTY SUBJECTS PAGE -->
    <div id="page-faculty-subjects" class="page active">

      <!-- CARD WRAPPER (Tailwind) -->
      <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
        <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
          <div>
            <div class="text-base font-bold text-gray-900">My Subjects</div>
            <div class="text-xs text-gray-500 mt-0.5">
              @if ($activeSemester)
                Assigned subjects for {{ $activeSemester->sem_name }}
              @else
                No active semester set
              @endif
            </div>
          </div>

          <!-- DAY / NIGHT TOGGLE -->
          <div class="inline-flex items-center bg-gray-100 rounded-lg p-1 gap-1 shrink-0">
            <button id="btn-view-day" type="button" onclick="switchScheduleView('day')"
                    class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition flex items-center gap-1.5 bg-white text-gray-900 shadow-sm">
              Day
            </button>
            <button id="btn-view-night" type="button" onclick="switchScheduleView('night')"
                    class="px-3.5 py-1.5 rounded-md text-xs font-semibold transition flex items-center gap-1.5 text-gray-500">
              Night
            </button>
          </div>
        </div>
        <div class="overflow-x-auto"><table class="w-full border-collapse">
          <thead>
            <tr>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Code</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Subject Name</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Units</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Lec Hrs</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Lab Hrs</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Sections</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Room</th>
              <th class="whitespace-nowrap border-b-2 border-slate-200 px-3.5 py-2.5 text-left text-[11px] font-bold uppercase tracking-[.6px] text-slate-400 dark:border-slate-700">Schedule</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($subjects as $entry)
              @php $subj = $entry['subject']; @endphp
              <tr class="subject-row cursor-pointer transition-colors hover:[&>td]:bg-slate-50 dark:hover:[&>td]:bg-slate-800"
                data-code="{{ $subj->subj_code ?? '' }}"
                data-name="{{ $subj->subj_name ?? '' }}"
                data-units="{{ $subj->subj_units ?? '' }}"
                data-lec="{{ $subj->subj_lec_hours ?? 0 }}"
                data-lab="{{ $subj->subj_lab_hours ?? 0 }}"
                data-room="{{ $entry['room'] }}"
                data-section="{{ $entry['sections'] }}"
                data-schedule="{{ $entry['schedules']->map(fn($s) => $s['day'] . ' ' . $s['start'] . '–' . $s['end'])->implode(', ') }}">
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200"><span class="font-mono font-bold text-blue-600">{{ $subj->subj_code ?? 'N/A' }}</span></td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200"><b>{{ $subj->subj_name ?? 'N/A' }}</b></td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200">{{ $subj->subj_units ?? '—' }}</td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200">{{ $subj->subj_lec_hours ?? '—' }}</td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200">{{ $subj->subj_lab_hours ?? '—' }}</td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200">{{ $entry['sections'] ?: '—' }}</td>
                <td class="border-b border-slate-100 px-3.5 py-3 text-[13px] text-slate-900 dark:border-slate-800 dark:text-slate-200">{{ $entry['room'] }}</td>
                <td class="subj-schedule border-b border-slate-100 px-3.5 py-3 text-xs text-slate-400 dark:border-slate-800">
                  @foreach ($entry['schedules'] as $sched)
                    <div class="sched-row" data-day="{{ $sched['day'] }}" data-start="{{ $sched['start'] }}" data-end="{{ $sched['end'] }}">
                      {{ $sched['day'] }} {{ $sched['start'] }}–{{ $sched['end'] }}
                      <span class="sched-status-inline"></span>
                    </div>
                  @endforeach
                </td>
              </tr>
            @empty
              <tr><td colspan="8" class="px-3.5 py-8 text-center text-[13px] text-slate-400">No subjects assigned this semester.</td></tr>
            @endforelse
          </tbody>
        </table></div>
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

    <div class="flex justify-end gap-2 mt-2">
      <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200"
              onclick="closeModal('modal-web-subject-detail')">Close</button>
    </div>
  </div>
</div>

<!-- TOAST (Tailwind) -->
<div class="toast fixed bottom-6 right-6 z-[300] bg-gray-900 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg opacity-0 translate-y-2 pointer-events-none transition-all duration-300 [&.show]:opacity-100 [&.show]:translate-y-0 [&.show]:pointer-events-auto"
     id="toast">✅ <span id="toast-msg"></span></div>

<script>
function openWebSubjectDetail(code, name, units, lec, lab, dept, room, section, schedule, color) {
  document.getElementById('wsd-header').style.background = 'linear-gradient(135deg,' + color + ',#0f172a)';
  document.getElementById('wsd-code').textContent = code;
  document.getElementById('wsd-name').textContent = name;
  document.getElementById('wsd-units').textContent = units + 'u';
  document.getElementById('wsd-hours').textContent = lec + 'h Lecture' + (parseInt(lab) > 0 ? ' · ' + lab + 'h Lab' : '');
  document.getElementById('wsd-room').textContent = room;
  document.getElementById('wsd-section').textContent = section;
  document.getElementById('wsd-schedule').textContent = schedule;
  openModal('modal-web-subject-detail');
}

// ── DAY / NIGHT SCHEDULE TOGGLE ─────────────────────────────────────────
function switchScheduleView(view) {
  const dayBtn   = document.getElementById('btn-view-day');
  const nightBtn = document.getElementById('btn-view-night');
  const dayGrid  = document.getElementById('view-day-grid');
  const nightGrid = document.getElementById('view-night-grid');
  if (!dayBtn || !nightBtn || !dayGrid || !nightGrid) return;

  const activeClasses   = ['bg-white', 'text-gray-900', 'shadow-sm'];
  const inactiveClasses = ['text-gray-500'];

  const activate = (btn) => { btn.classList.add(...activeClasses); btn.classList.remove(...inactiveClasses); };
  const deactivate = (btn) => { btn.classList.remove(...activeClasses); btn.classList.add(...inactiveClasses); };

  if (view === 'night') {
    dayGrid.classList.add('hidden');
    nightGrid.classList.remove('hidden');
    activate(nightBtn);
    deactivate(dayBtn);
  } else {
    nightGrid.classList.add('hidden');
    dayGrid.classList.remove('hidden');
    activate(dayBtn);
    deactivate(nightBtn);
  }
}

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

// ── LIVE STATUS BADGE, pinned to the right edge of each block's code line ──
function parseTimeToday(timeStr) {
  const [h, m, s] = timeStr.split(':').map(Number);
  const d = new Date();
  d.setHours(h, m, s || 0, 0);
  return d;
}

const DAY_NAMES = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];

function updateSubjectScheduleStatuses() {
  const now = new Date();
  const todayName = DAY_NAMES[now.getDay()];

  document.querySelectorAll('.subj-block').forEach(cell => {
    const statusEl = cell.querySelector('.sched-status-inline');
    if (!statusEl) return;

    if (cell.dataset.day !== todayName) {
      statusEl.innerHTML = '';
      return;
    }

    const start = parseTimeToday(cell.dataset.start);
    const end = parseTimeToday(cell.dataset.end);

    const badgeBase = 'text-[9px] font-bold px-1.5 py-0.5 rounded-full whitespace-nowrap tracking-wide leading-tight';

    if (now < start) {
      statusEl.innerHTML = ` <span class="badge badge-blue text-[10px]">Today</span>`;
    } else if (now >= start && now <= end) {
      const mins = Math.floor((end - now) / 60000);
      const urgent = mins < 5;
      statusEl.innerHTML = ` <span class="badge ${urgent ? 'badge-red' : 'badge-green'} text-[10px]">${mins}m left</span>`;
    } else {
      statusEl.innerHTML = ` <span class="badge badge-grey text-[10px]">Ended</span>`;
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  updateSubjectScheduleStatuses();
  setInterval(updateSubjectScheduleStatuses, 30000); // refresh every 30s

  // Default view: Night if it's currently 4:30 PM or later, otherwise Day.
  // The buttons always let the user override this manually.
  const now = new Date();
  const isNightHours = now.getHours() > 16 || (now.getHours() === 16 && now.getMinutes() >= 30);
  switchScheduleView(isNightHours ? 'night' : 'day');
});
</script>
</body>
</html>