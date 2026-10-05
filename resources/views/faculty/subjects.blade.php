<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — My Subjects</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

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

        {{-- Always show Summary + Schedule grid (empty cells when no data) --}}
          <div class="flex gap-5 items-start flex-wrap">

            <!-- LEFT: SUMMARY OF SUBJECTS ─────────────────────────────────── -->
            <div class="w-full sm:w-64 shrink-0 border border-gray-200 rounded-lg overflow-hidden font-sans self-start">
              <div class="bg-blue-600 text-white font-bold text-sm uppercase tracking-wide text-center py-3">
                Summary
              </div>
              <table class="w-full border-collapse text-xs">
                <thead>
                  <tr>
                    <th class="text-left text-[10px] font-bold uppercase tracking-wide text-gray-500 px-2.5 py-2 border-b border-gray-200">Code</th>
                    <th class="text-left text-[10px] font-bold uppercase tracking-wide text-gray-500 px-2.5 py-2 border-b border-gray-200">Descriptive Title</th>
                    <th class="text-left text-[10px] font-bold uppercase tracking-wide text-gray-500 px-2.5 py-2 border-b border-gray-200">Students</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($summaryRows as $row)
                    <tr>
                      <td class="px-2.5 py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }} text-gray-900 align-top">
                        <span class="inline-block w-2 h-2 rounded-full mr-1.5 align-middle" style="background:{{ $row['color'] }};"></span>{{ $row['code'] }}
                      </td>
                      <td class="px-2.5 py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }} text-gray-900 align-top">{{ $row['name'] }}</td>
                      <td class="px-2.5 py-2 {{ $loop->last ? '' : 'border-b border-gray-200' }} text-gray-900 align-top">{{ $row['students'] }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="px-2.5 py-6 text-center text-gray-400 text-[11px]">No subjects plotted yet.</td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            <!-- DAY / NIGHT GRIDS (toggled via JS, right side of Summary) -->
            <div class="flex-1 min-w-[480px]">

            <!-- DAY GRID (7AM – 4PM) ─────────────────────────────────────── -->
            <div id="view-day-grid" class="overflow-x-auto rounded-xl border border-gray-200">
              <table class="border-collapse w-full min-w-[760px]">
                <thead>
                  <tr>
                    <th class="w-24 border-b border-r border-gray-200 bg-blue-600 text-white font-sans font-bold text-[11px] uppercase tracking-wide px-1.5 py-3">Time</th>
                    @foreach ($gridDays as $day)
                      <th class="border-b border-r last:border-r-0 border-gray-200 bg-blue-600 text-white font-sans font-bold text-sm px-1.5 py-3">{{ $day }}</th>
                    @endforeach
                  </tr>
                </thead>
                <tbody>
                  @foreach ($dayRange as $hour)

                    {{-- Solid noon-break band, matching the plotter's style, when nothing is scheduled 12–1 --}}
                    @if ($hour === 12 && collect($gridDays)->every(fn($d) => empty($dayBlocks[$d][12] ?? null)))
                      <tr>
                        <td class="border-b border-r border-gray-200 bg-white text-gray-700 font-bold text-xs px-1 py-4 whitespace-nowrap text-center align-middle">12–1 PM</td>
                        <td colspan="{{ count($gridDays) }}"
                            class="bg-gray-900 text-white font-bold text-xs tracking-[3px] text-center align-middle py-4">
                          NOON BREAK
                        </td>
                      </tr>
                      @continue
                    @endif

                    <tr>
                      <td class="border-b border-r border-gray-200 bg-white text-gray-700 font-bold text-xs px-1 py-4 whitespace-nowrap text-center align-middle">{{ $hour > 12 ? $hour - 12 : $hour }}–{{ ($hour + 1) > 12 ? ($hour + 1) - 12 : $hour + 1 }} {{ $hour + 1 > 12 ? 'PM' : 'AM' }}</td>
                      @foreach ($gridDays as $day)
                        @if ($skipUntil[$day] > $hour)
                          {{-- covered by a rowspan from an earlier row --}}
                        @elseif ($block = $dayBlocks[$day][$hour] ?? null)
                          @php $skipUntil[$day] = $hour + $block['duration']; @endphp
                          <td class="subj-block border-b border-r last:border-r-0 border-gray-200 text-center align-middle cursor-pointer p-1.5 text-white font-sans transition duration-150 hover:brightness-110"
                              rowspan="{{ $block['duration'] }}"
                              style="background:{{ $block['color'] }};"
                              data-day="{{ $day }}"
                              data-start="{{ $block['start_raw'] }}"
                              data-end="{{ $block['end_raw'] }}"
                              onclick="openWebSubjectDetail(
                                '{{ $block['code'] }}',
                                '{{ addslashes($block['name']) }}',
                                '{{ $block['units'] }}',
                                '{{ $block['lec'] }}',
                                '{{ $block['lab'] }}',
                                '',
                                '{{ addslashes($block['room']) }}',
                                '{{ addslashes($block['section']) }}',
                                '{{ $day }} {{ $block['start'] }}–{{ $block['end'] }}',
                                '{{ $block['color'] }}'
                              )">
                            <div class="flex items-center justify-between gap-1.5">
                              <span class="font-mono font-bold text-xs tracking-wide">{{ $block['code'] }}</span>
                              <span class="sched-status-inline"></span>
                            </div>
                            <div class="text-[11px] opacity-90 mt-0.5">{{ $block['section'] }}</div>
                            <div class="text-[11px] opacity-80 mt-0.5">{{ $block['room'] }}</div>
                          </td>
                        @else
                          <td class="border-b border-r last:border-r-0 border-gray-200 bg-white h-14"></td>
                        @endif
                      @endforeach
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div><!-- end view-day-grid -->

            <!-- NIGHT GRID (starts 4:30 PM) — toggled via JS -->
            <div id="view-night-grid" class="hidden">
              @php
                // Labels for night rows: first slot is 4:30–5:30, then hourly
                $nightLabels = [
                  16 => '4:30–5:30 PM',
                  17 => '5:30–6:30 PM',
                  18 => '6:30–7:30 PM',
                  19 => '7:30–8:30 PM',
                  20 => '8:30–9:30 PM',
                  21 => '9:30–10:30 PM',
                ];
              @endphp
              <div class="flex items-center gap-2 mb-2.5 font-sans font-bold text-sm text-gray-900">
                <span class="w-2 h-2 rounded-full bg-[#3d5a80]"></span> Night Schedule
              </div>
              <div class="overflow-x-auto rounded-xl border border-gray-200">
                <table class="border-collapse w-full min-w-[760px]">
                  <thead>
                    <tr>
                      <th class="w-24 border-b border-r border-gray-200 bg-blue-600 text-white font-sans font-bold text-[11px] uppercase tracking-wide px-1.5 py-3">Time</th>
                      @foreach ($gridDays as $day)
                        <th class="border-b border-r last:border-r-0 border-gray-200 bg-blue-600 text-white font-sans font-bold text-sm px-1.5 py-3">{{ $day }}</th>
                      @endforeach
                    </tr>
                  </thead>
                  <tbody>
                    @php $nightSkipUntil = array_fill_keys($gridDays, 0); @endphp
                    @foreach ($nightRange as $hour)
                      <tr>
                        <td class="border-b border-r border-gray-200 bg-white text-gray-700 font-bold text-xs px-1 py-4 whitespace-nowrap text-center align-middle">{{ $nightLabels[$hour] ?? (($hour - 12) . '–' . ($hour + 1 - 12) . ' PM') }}</td>
                        @foreach ($gridDays as $day)
                          @if ($nightSkipUntil[$day] > $hour)
                          @elseif ($block = $dayBlocks[$day][$hour] ?? null)
                            @php $nightSkipUntil[$day] = $hour + $block['duration']; @endphp
                            <td class="subj-block border-b border-r last:border-r-0 border-gray-200 text-center align-middle cursor-pointer p-1.5 text-white font-sans transition duration-150 hover:brightness-110"
                                rowspan="{{ $block['duration'] }}"
                                style="background:{{ $block['color'] }};"
                                data-day="{{ $day }}"
                                data-start="{{ $block['start_raw'] }}"
                                data-end="{{ $block['end_raw'] }}"
                                onclick="openWebSubjectDetail(
                                  '{{ $block['code'] }}',
                                  '{{ addslashes($block['name']) }}',
                                  '{{ $block['units'] }}',
                                  '{{ $block['lec'] }}',
                                  '{{ $block['lab'] }}',
                                  '',
                                  '{{ addslashes($block['room']) }}',
                                  '{{ addslashes($block['section']) }}',
                                  '{{ $day }} {{ $block['start'] }}–{{ $block['end'] }}',
                                  '{{ $block['color'] }}'
                                )">
                              <div class="flex items-center justify-between gap-1.5">
                                <span class="font-mono font-bold text-xs tracking-wide">{{ $block['code'] }}</span>
                                <span class="sched-status-inline"></span>
                              </div>
                              <div class="text-[11px] opacity-90 mt-0.5">{{ $block['section'] }}</div>
                              <div class="text-[11px] opacity-80 mt-0.5">{{ $block['room'] }}</div>
                            </td>
                          @else
                            <td class="border-b border-r last:border-r-0 border-gray-200 bg-white h-14"></td>
                          @endif
                        @endforeach
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div><!-- end view-night-grid -->

            </div><!-- end flex-1 day/night column -->

          </div><!-- end grid layout (Summary + Day/Night column) -->

      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- FACULTY SUBJECT DETAIL MODAL (Tailwind) -->
<div class="modal-overlay fixed inset-0 z-[200] hidden items-center justify-center bg-black/40 backdrop-blur-sm [&.open]:flex"
     id="modal-web-subject-detail">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[480px] max-h-[90vh] overflow-y-auto p-6">

    <div class="flex items-center justify-between mb-5">
      <div class="text-lg font-bold text-gray-900">Subject Details</div>
      <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm"
              onclick="closeModal('modal-web-subject-detail')">✕</button>
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
      statusEl.innerHTML = `<span class="${badgeBase} bg-white/30 text-white">Today</span>`;
    } else if (now >= start && now <= end) {
      const mins = Math.floor((end - now) / 60000);
      const urgent = mins < 5;
      const variant = urgent ? 'bg-red-500 text-white' : 'bg-green-500 text-green-950';
      statusEl.innerHTML = `<span class="${badgeBase} ${variant}">${mins}m left</span>`;
    } else {
      statusEl.innerHTML = `<span class="${badgeBase} bg-black/30 text-white">Ended</span>`;
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