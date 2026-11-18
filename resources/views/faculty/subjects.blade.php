<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
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
      $scheduleForJs = [];
      $subjects = $subjects ?? collect();

      foreach ($subjects as $entry) {
          $subj = $entry['subject'];
          $code = $subj->subj_code ?? 'N/A';
          foreach ($entry['schedules'] as $sched) {
              $startTs = \Carbon\Carbon::parse($sched['start']);
              $endTs   = \Carbon\Carbon::parse($sched['end']);
              $scheduleForJs[] = [
                  'code'  => $code,
                  'room'  => $entry['room'],
                  'day'   => $sched['day'],
                  'start' => $startTs->format('H:i'),
                  'end'   => $endTs->format('H:i'),
              ];
          }
      }
    @endphp

    @include('partials.faculty_header', [
        'title' => 'My Subjects',
        'semesterLabel' => $activeSemester
            ? trim(($activeSemester->sem_name ?? '') . ' ' . ($activeSemester->academicYear?->ay_academic_year ?? ''))
            : 'No active semester',
        'activeSemester' => $activeSemester,
        'scheduleFeed' => $scheduleForJs,
    ])

    <!-- FACULTY SUBJECTS PAGE -->
    <div id="page-faculty-subjects" class="page active faculty-page-enter">

      @php
        $daySubjectCount = $subjects->filter(fn ($entry) => $entry['schedules']->contains(fn ($schedule) => (int) \Illuminate\Support\Carbon::parse($schedule['start'])->format('H') < 16))->count();
        $eveningSubjectCount = $subjects->filter(fn ($entry) => $entry['schedules']->contains(fn ($schedule) => (int) \Illuminate\Support\Carbon::parse($schedule['start'])->format('H') >= 16))->count();
        $semesterText = $activeSemester
          ? trim(($activeSemester->sem_name ?? '') . ' ' . ($activeSemester->academicYear?->ay_academic_year ?? ''))
          : 'No active semester';
      @endphp

      <div class="mb-4 flex flex-wrap items-end justify-between gap-4">
        <div>
          <div class="mb-1 text-[10px] font-bold uppercase tracking-[.16em] text-slate-400">Academics <span class="mx-1 text-slate-300">/</span> Faculty Workload <span class="mx-1 text-slate-300">/</span> <span class="text-blue-600">My Subjects</span></div>
          <h1 class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">My Subjects &amp; Teaching Load</h1>
          <p class="mt-1 text-xs text-slate-500">Your assigned courses, scheduled sections, and weekly workload.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
          <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-[10px] font-bold text-emerald-700"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>{{ $semesterText }}{{ $activeSemester?->sem_is_active ? ' · Active' : '' }}</span>
          <a href="{{ route('faculty.schedule') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg bg-blue-600 px-3.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700"><i class="ti ti-calendar-week" aria-hidden="true"></i>Weekly Timetable</a>
        </div>
      </div>

      <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 2xl:grid-cols-4">
        <article class="faculty-subject-summary border-t-2 border-blue-600">
          <div class="flex items-start justify-between"><span class="faculty-summary-label">Assigned Subjects</span><span class="faculty-summary-icon bg-blue-50 text-blue-600"><i class="ti ti-book-2" aria-hidden="true"></i></span></div>
          <div class="mt-3 flex items-baseline gap-2"><strong>{{ $subjects->count() }}</strong><span>Active this semester</span></div>
          <div class="mt-2 text-[10px] text-slate-500">{{ $daySubjectCount }} day · {{ $eveningSubjectCount }} evening</div>
        </article>
        <article class="faculty-subject-summary border-t-2 border-emerald-500">
          <div class="flex items-start justify-between"><span class="faculty-summary-label">Weekly Contact Load</span><span class="faculty-summary-icon bg-emerald-50 text-emerald-600"><i class="ti ti-clock-hour-4" aria-hidden="true"></i></span></div>
          <div class="mt-3 flex items-baseline gap-2"><strong>{{ number_format($weeklyContactLoad, 1) }}</strong><span>hours / week</span></div>
          <div class="mt-2 h-1.5 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full bg-emerald-500" style="width:{{ min(100, round($weeklyContactLoad / max(1, $maxHours) * 100)) }}%"></div></div>
          <div class="mt-1 text-[10px] text-slate-500">{{ number_format($maxHours, 1) }}h weekly load limit</div>
        </article>
        <article class="faculty-subject-summary border-t-2 border-violet-500">
          <div class="flex items-start justify-between"><span class="faculty-summary-label">Enrolled Students</span><span class="faculty-summary-icon bg-violet-50 text-violet-600"><i class="ti ti-users" aria-hidden="true"></i></span></div>
          <div class="mt-3 flex items-baseline gap-2"><strong>{{ $enrolledStudents }}</strong><span>across {{ $sectionCount }} sections</span></div>
          <div class="mt-2 text-[10px] text-slate-500">Based on section enrollment records</div>
        </article>
        <article class="faculty-subject-summary border-t-2 border-cyan-500">
          <div class="flex items-start justify-between"><span class="faculty-summary-label">Lab Subjects &amp; Rooms</span><span class="faculty-summary-icon bg-cyan-50 text-cyan-600"><i class="ti ti-device-laptop" aria-hidden="true"></i></span></div>
          <div class="mt-3 flex items-baseline gap-2"><strong>{{ $labSubjectCount }}</strong><span>lab subjects</span></div>
          <div class="mt-2 truncate text-[10px] text-slate-500" title="{{ $labRoomNames->implode(', ') }}">{{ $labRoomNames->isNotEmpty() ? $labRoomNames->implode(', ') : 'No lab room assigned' }}</div>
        </article>
      </div>

      <section aria-label="Search and filter subjects" class="mb-4 flex flex-col gap-3 rounded-xl border border-slate-200 bg-white p-3 sm:flex-row sm:items-center sm:justify-between">
        <label class="relative block min-w-0 flex-1 sm:max-w-md">
          <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
          <input id="subject-search" type="search" placeholder="Search course code, title, section, or room" class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-3 text-xs text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-blue-400 focus:bg-white focus:ring-2 focus:ring-blue-100" autocomplete="off">
        </label>
        <div class="inline-flex w-full items-center gap-1 overflow-x-auto rounded-lg bg-slate-100 p-1 sm:w-auto" role="group" aria-label="Filter subjects by shift">
          <button type="button" class="subject-shift-filter active" data-subject-shift="all">All shifts <span>{{ $subjects->count() }}</span></button>
          <button type="button" class="subject-shift-filter" data-subject-shift="day">☀ Day <span>{{ $daySubjectCount }}</span></button>
          <button type="button" class="subject-shift-filter" data-subject-shift="evening">☾ Evening <span>{{ $eveningSubjectCount }}</span></button>
        </div>
      </section>

      <div id="faculty-subject-cards" class="grid grid-cols-1 gap-3 2xl:grid-cols-2">
        @forelse ($subjects as $entry)
          @php
            $subj = $entry['subject'];
            $code = $subj->subj_code ?? 'N/A';
            $name = $subj->subj_name ?? 'N/A';
            $hasDay = $entry['schedules']->contains(fn ($schedule) => (int) \Illuminate\Support\Carbon::parse($schedule['start'])->format('H') < 16);
            $hasEvening = $entry['schedules']->contains(fn ($schedule) => (int) \Illuminate\Support\Carbon::parse($schedule['start'])->format('H') >= 16);
            $courseHours = \App\Services\ScheduleAssignmentService::courseHours($subj);
            $scheduleText = $entry['schedules']->map(fn ($schedule) => $schedule['day'] . ' ' . \Illuminate\Support\Carbon::parse($schedule['start'])->format('g:i A') . '–' . \Illuminate\Support\Carbon::parse($schedule['end'])->format('g:i A'))->implode(', ');
          @endphp
          <article class="faculty-subject-card" data-subject-card data-code="{{ $code }}" data-name="{{ $name }}" data-section="{{ $entry['sections'] }}" data-room="{{ $entry['room'] }}" data-day="{{ $hasDay ? '1' : '0' }}" data-evening="{{ $hasEvening ? '1' : '0' }}" data-units="{{ $subj->subj_units ?? '' }}" data-lec="{{ $subj->subj_lec_hours ?? 0 }}" data-lab="{{ $subj->subj_lab_hours ?? 0 }}" data-schedule="{{ $scheduleText }}" data-color="#2563eb">
            <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-start sm:justify-between sm:p-5">
              <div class="flex min-w-0 items-start gap-3">
                <span class="faculty-subject-code">{{ $code }}</span>
                <div class="min-w-0">
                  <h2 class="break-words text-sm font-extrabold leading-snug text-slate-900">{{ $name }}</h2>
                  <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                    <span class="faculty-subject-badge">{{ number_format((float) ($subj->subj_units ?? 0), 1) }} Academic Units</span>
                    @if($hasDay)<span class="faculty-shift-badge day">☀ Day</span>@endif
                    @if($hasEvening)<span class="faculty-shift-badge evening">☾ Evening</span>@endif
                  </div>
                  <div class="mt-2 text-[10px] text-slate-500">{{ number_format((float) ($subj->subj_lec_hours ?? 0), 1) }}h lecture · {{ number_format((float) ($subj->subj_lab_hours ?? 0), 1) }}h lab weekly</div>
                </div>
              </div>
              <button type="button" class="faculty-subject-details" onclick="openWebSubjectDetailFromCard(this)"><i class="ti ti-list-details" aria-hidden="true"></i> View details</button>
            </div>
            <div class="grid grid-cols-1 gap-3 border-t border-slate-100 bg-slate-50/70 px-4 py-3 sm:grid-cols-3 sm:px-5">
              <div><div class="faculty-subject-meta-label">Schedule &amp; Shift</div><div class="mt-1 space-y-1 text-[11px] font-semibold text-slate-700">
                @foreach ($entry['schedules'] as $sched)
                  @php $scheduleShift = (int) \Illuminate\Support\Carbon::parse($sched['start'])->format('H') >= 16 ? 'evening' : 'day'; @endphp
                  <div class="subject-schedule-line" data-subject-schedule data-schedule-shift="{{ $scheduleShift }}" data-day="{{ $sched['day'] }}" data-start="{{ \Illuminate\Support\Carbon::parse($sched['start'])->format('H:i:s') }}" data-end="{{ \Illuminate\Support\Carbon::parse($sched['end'])->format('H:i:s') }}">{{ $sched['day'] }} · {{ \Illuminate\Support\Carbon::parse($sched['start'])->format('g:i A') }}–{{ \Illuminate\Support\Carbon::parse($sched['end'])->format('g:i A') }} <span class="faculty-shift-badge {{ $scheduleShift }}">{{ ucfirst($scheduleShift) }}</span><span class="sched-status-inline"></span></div>
                @endforeach
              </div></div>
              <div><div class="faculty-subject-meta-label">Sections &amp; Students</div><div class="mt-1 text-xs font-bold text-slate-800">{{ $entry['sections'] ?: 'No section listed' }}</div><div class="mt-0.5 text-[10px] text-slate-500">{{ $entry['students'] }} enrolled</div></div>
              <div><div class="faculty-subject-meta-label">Assigned Room</div><div class="mt-1 text-xs font-bold text-slate-800">{{ $entry['room'] }}</div><div class="mt-0.5 text-[10px] text-slate-500">{{ number_format((float) $courseHours, 1) }} workload hours</div></div>
            </div>
          </article>
        @empty
          <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center">
            <i class="ti ti-books text-3xl text-slate-300" aria-hidden="true"></i>
            <p class="mt-2 text-sm font-bold text-slate-700">No subjects assigned this semester</p>
            <p class="mt-1 text-xs text-slate-500">Your approved course assignments will appear here.</p>
          </div>
        @endforelse
        <div id="subject-search-empty" class="hidden col-span-full rounded-xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center text-xs text-slate-500">No subjects match your search and shift filters.</div>
      </div>

      <footer class="mt-7 flex flex-col gap-2 border-t border-slate-200 pt-4 text-[10px] text-slate-400 sm:flex-row sm:items-center sm:justify-between">
        <span><strong class="font-extrabold tracking-wide text-slate-600">SKEDYUL</strong> Faculty Information System · Academic Timetabling &amp; Workload</span>
        <span>{{ $faculty->department->dept_code ?? 'Faculty' }} Department · {{ $semesterText }}</span>
      </footer>
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

// Search and filter assigned subjects without hiding the underlying schedule data.
function applyFacultySubjectFilters() {
  const query = (document.getElementById('subject-search')?.value || '').trim().toLowerCase();
  const activeShift = document.querySelector('.subject-shift-filter.active')?.dataset.subjectShift || 'all';
  let visibleCount = 0;

  document.querySelectorAll('[data-subject-card]').forEach(card => {
    const searchText = [card.dataset.code, card.dataset.name, card.dataset.section, card.dataset.room].join(' ').toLowerCase();
    const matchesSearch = !query || searchText.includes(query);
    const matchesShift = activeShift === 'all' || card.dataset[activeShift] === '1';
    const visible = matchesSearch && matchesShift;
    card.classList.toggle('hidden', !visible);
    if (visible) visibleCount++;

    card.querySelectorAll('[data-schedule-shift]').forEach(line => {
      line.classList.toggle('hidden', activeShift !== 'all' && line.dataset.scheduleShift !== activeShift);
    });
  });

  const emptyState = document.getElementById('subject-search-empty');
  if (emptyState) emptyState.classList.toggle('hidden', visibleCount > 0 || !document.querySelector('[data-subject-card]'));
}

function openWebSubjectDetailFromCard(button) {
  const card = button?.closest('[data-subject-card]');
  if (!card) return;
  openWebSubjectDetail(
    card.dataset.code, card.dataset.name, card.dataset.units,
    card.dataset.lec, card.dataset.lab, '', card.dataset.room,
    card.dataset.section, card.dataset.schedule, card.dataset.color
  );
}

document.querySelectorAll('.subject-shift-filter').forEach(button => {
  button.addEventListener('click', () => {
    document.querySelectorAll('.subject-shift-filter').forEach(filter => {
      filter.classList.toggle('active', filter === button);
      filter.setAttribute('aria-pressed', String(filter === button));
    });
    applyFacultySubjectFilters();
  });
});
document.getElementById('subject-search')?.addEventListener('input', applyFacultySubjectFilters);

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

// ── Live class status on each scheduled subject ──────────────────────────
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

  document.querySelectorAll('[data-subject-schedule]').forEach(schedule => {
    const statusEl = schedule.querySelector('.sched-status-inline');
    if (!statusEl) return;

    if (schedule.dataset.day !== todayName) {
      statusEl.innerHTML = '';
      return;
    }

    const start = parseTimeToday(schedule.dataset.start);
    const end = parseTimeToday(schedule.dataset.end);

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
  applyFacultySubjectFilters();
  updateSubjectScheduleStatuses();
  setInterval(updateSubjectScheduleStatuses, 30000); // refresh every 30s
});
</script>
</body>
</html>
