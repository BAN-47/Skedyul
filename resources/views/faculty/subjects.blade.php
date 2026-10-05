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
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">My Subjects</div>
    </div>

    <!-- FACULTY SUBJECTS PAGE -->
    <div id="page-faculty-subjects" class="page active">
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">My Subjects</div>
            <div class="card-sub">
              @if ($activeSemester)
                Assigned subjects for {{ $activeSemester->sem_name }}
              @else
                No active semester set
              @endif
            </div>
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
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-web-subject-detail')">Close</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

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

// ── LIVE STATUS PER SCHEDULE ROW (matches dashboard countdown pattern) ────
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

  document.querySelectorAll('.sched-row').forEach(row => {
    const statusEl = row.querySelector('.sched-status-inline');
    if (!statusEl) return;

    if (row.dataset.day !== todayName) {
      statusEl.innerHTML = '';
      return;
    }

    const start = parseTimeToday(row.dataset.start);
    const end = parseTimeToday(row.dataset.end);

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
  setInterval(updateSubjectScheduleStatuses, 30000); // update every 30s, no need for per-second here
});
</script>
</body>
</html>