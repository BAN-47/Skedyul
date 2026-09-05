<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — My Subjects</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty/subjects.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

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
        <div class="table-wrap"><table>
          <tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Lec Hrs</th><th>Lab Hrs</th><th>Sections</th><th>Room</th><th>Schedule</th></tr>
          @forelse ($subjects as $entry)
            @php $subj = $entry['subject']; @endphp
            <tr class="subject-row"
              data-code="{{ $subj->subj_code ?? '' }}"
              data-name="{{ $subj->subj_name ?? '' }}"
              data-units="{{ $subj->subj_units ?? '' }}"
              data-lec="{{ $subj->subj_lec_hours ?? 0 }}"
              data-lab="{{ $subj->subj_lab_hours ?? 0 }}"
              data-room="{{ $entry['room'] }}"
              data-section="{{ $entry['sections'] }}"
              data-schedule="{{ $entry['schedules']->map(fn($s) => $s['day'] . ' ' . $s['start'] . '–' . $s['end'])->implode(', ') }}"
              style="cursor:pointer;">
            <td><span style="font-family:var(--mono);font-weight:700;color:var(--blue);">{{ $subj->subj_code ?? 'N/A' }}</span></td>
            <td><b>{{ $subj->subj_name ?? 'N/A' }}</b></td>
            <td>{{ $subj->subj_units ?? '—' }}</td>
            <td>{{ $subj->subj_lec_hours ?? '—' }}</td>
            <td>{{ $subj->subj_lab_hours ?? '—' }}</td>
            <td>{{ $entry['sections'] ?: '—' }}</td>
            <td>{{ $entry['room'] }}</td>
            <td class="subj-schedule" style="font-size:12px;color:var(--text3);">
              @foreach ($entry['schedules'] as $sched)
                <div class="sched-row" data-day="{{ $sched['day'] }}" data-start="{{ $sched['start'] }}" data-end="{{ $sched['end'] }}">
                  {{ $sched['day'] }} {{ $sched['start'] }}–{{ $sched['end'] }}
                  <span class="sched-status-inline"></span>
                </div>
              @endforeach
            </td>
          </tr>
          @empty
            <tr><td colspan="8" class="text-center">No subjects assigned this semester.</td></tr>
          @endforelse
        </table></div>
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
      statusEl.innerHTML = ` <span class="badge badge-blue" style="font-size:10px;">Today</span>`;
    } else if (now >= start && now <= end) {
      const mins = Math.floor((end - now) / 60000);
      const urgent = mins < 5;
      statusEl.innerHTML = ` <span class="badge ${urgent ? 'badge-red' : 'badge-green'}" style="font-size:10px;">${mins}m left</span>`;
    } else {
      statusEl.innerHTML = ` <span class="badge badge-grey" style="font-size:10px;">Ended</span>`;
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