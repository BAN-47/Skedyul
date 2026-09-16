<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Faculty Load Management</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

@include('partials.chair_sidebar')

<div class="app-main relative">

    {{--
        NOTE: this topbar + notification panel is copy-pasted from chair_dashboard.blade.php,
        the same duplication problem we fixed for the admin pages earlier. Once this page
        is working, it's worth extracting into partials/chair_header.blade.php the same way
        we did partials/admin_header.blade.php — happy to do that next if you want.
    --}}
    <div class="topbar">
      <div class="topbar-title">Faculty Load Management</div>
      <div class="flex items-center gap-2.5">
        <span class="badge badge-blue text-[11px]">
          {{ $program->prog_code ?? $department->dept_code ?? 'DEPT' }}
          @if($academicYear) &middot; {{ $academicYear->ay_academic_year }} @endif
          @if($semester) &middot; {{ $semester->sem_name }} @endif
        </span>
        <div class="relative flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-slate-100 text-slate-600 text-[13px] font-semibold cursor-pointer hover:bg-slate-200 transition"
             id="notif-btn" onclick="toggleNotifPanel()">
          Notifications
        </div>
      </div>
    </div>

    <div class="hidden absolute top-[60px] right-7 w-[340px] max-h-[420px] overflow-y-auto bg-white rounded-2xl border border-slate-200 shadow-[0_20px_60px_rgba(0,0,0,.15)] z-50"
         id="notif-panel">
      <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
        <span class="text-[14px] font-bold text-slate-900">Notifications</span>
      </div>
      <div class="px-4 py-6 text-center text-[13px] text-slate-400">No notifications yet.</div>
    </div>

    <div class="page-content">

      <div class="flex items-start justify-between mb-6">
        <div>
          <div class="text-[22px] font-extrabold">Faculty Load Management</div>
          <div class="text-[13px] text-slate-400 mt-0.5">
            {{ $department->dept_name ?? 'Department' }}
            @if($program) &middot; {{ $program->prog_code }} @endif
            &middot; Max {{ \App\Http\Controllers\Chair\ChairFacultyLoadController::FULL_TIME_MAX_UNITS }} units/week
          </div>
        </div>
        <button type="button" onclick="openAssignModal()" class="btn btn-primary">+ Assign Subject</button>
      </div>

      <div class="card">
        <div class="overflow-x-auto">
          <table class="data-table" id="faculty-load-table">
            <thead>
              <tr>
                @foreach(['Faculty','Employment','Subjects','Total Units','Units Left','Status','Action'] as $h)
                <th>{{ $h }}</th>
                @endforeach
              </tr>
            </thead>
            <tbody>
              @forelse($facultyLoad as $fl)
              <tr>
                <td class="font-semibold">{{ $fl['name'] }}</td>
                <td>{{ $fl['employment'] === 'part_time' ? 'Part-time' : 'Full-time' }}</td>
                <td class="text-slate-500">{{ $fl['subjects'] }}</td>
                <td><span class="font-mono font-bold">{{ $fl['total_units'] }}u</span></td>
                <td>
                  @if($fl['is_part_time'])
                    <span class="text-slate-400">—</span>
                  @else
                    <span class="badge {{ $fl['status_badge'] }}">{{ $fl['remaining'] }}u left</span>
                  @endif
                </td>
                <td><span class="badge {{ $fl['status_badge'] }}">{{ $fl['status_label'] }}</span></td>
                <td>
                  @if($fl['action_style'] === 'disabled')
                    <button type="button" class="btn btn-secondary text-[11px] px-3 py-1.5 opacity-50 cursor-not-allowed" disabled>{{ $fl['action_label'] }}</button>
                  @else
                    <button type="button"
                      onclick="openAssignModal('{{ $fl['id'] }}')"
                      class="btn {{ $fl['action_style'] === 'primary' ? 'btn-primary' : 'btn-secondary' }} text-[11px] px-3 py-1.5">
                      {{ $fl['action_label'] }}
                    </button>
                  @endif
                </td>
              </tr>
              @empty
              <tr><td colspan="7" class="text-center py-8 text-slate-400">No faculty in this department yet.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
</div>
</div>

{{-- ASSIGN SUBJECT MODAL --}}
<div class="modal-overlay" id="modal-assign">
  <div class="modal-box w-[560px]">
    <div class="modal-header">
      <div class="modal-title">Assign Subject to Faculty</div>
      <button type="button" onclick="closeModal('modal-assign')" class="modal-close">✕</button>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Subject</label>
        <select id="assign-subject" class="field-input" onchange="updateAssignPreview()">
          <option value="">— Select subject —</option>
          @foreach($subjects as $subj)
            <option value="{{ $subj->subj_id }}" data-units="{{ $subj->subj_lecture_hours + $subj->subj_lab_hours }}">
              {{ $subj->subj_code }} — {{ $subj->subj_name }} ({{ $subj->subj_lecture_hours + $subj->subj_lab_hours }}u)
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="field-label">Faculty Member</label>
        <select id="assign-faculty" class="field-input" onchange="updateAssignPreview()">
          <option value="">— Select faculty —</option>
          @foreach($facultyLoad as $fl)
            <option value="{{ $fl['id'] }}"
              data-units="{{ $fl['total_units'] }}"
              data-max="{{ $fl['max_units'] }}"
              data-parttime="{{ $fl['is_part_time'] ? '1' : '0' }}">
              {{ $fl['name'] }}
            </option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Near-max advisory (informational only, doesn't block saving) --}}
    <div id="assign-warning" class="hidden bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12.5px] text-amber-800 mb-3"></div>

    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="field-label">Day</label>
        <select id="assign-day" class="field-input">
          <option value="Monday">Monday</option>
          <option value="Tuesday">Tuesday</option>
          <option value="Wednesday">Wednesday</option>
          <option value="Thursday">Thursday</option>
          <option value="Friday">Friday</option>
          <option value="Saturday">Saturday</option>
        </select>
      </div>
      <div>
        <label class="field-label">Time Slot</label>
        <select id="assign-timeslot" class="field-input">
          <option value="07:00:00|08:30:00">7:00 – 8:30 AM</option>
          <option value="08:30:00|10:00:00">8:30 – 10:00 AM</option>
          <option value="10:00:00|11:30:00">10:00 – 11:30 AM</option>
          <option value="11:30:00|13:00:00">11:30 AM – 1:00 PM</option>
          <option value="13:00:00|14:30:00">1:00 – 2:30 PM</option>
          <option value="14:30:00|16:00:00">2:30 – 4:00 PM</option>
          <option value="16:00:00|17:30:00">4:00 – 5:30 PM</option>
          <option value="17:30:00|19:00:00">5:30 – 7:00 PM</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-1">
      <div>
        <label class="field-label">Room</label>
        <select id="assign-room" class="field-input">
          <option value="">— Select room —</option>
          @foreach($rooms as $room)
            <option value="{{ $room->room_id }}">{{ $room->room_name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="field-label">Section</label>
        <select id="assign-section" class="field-input">
          <option value="">— Select section —</option>
          @foreach($sections as $sec)
            <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    {{-- Conflict banner (set by the server after Save Assignment, from the DB exclusion constraint) --}}
    <div id="assign-conflict" class="hidden bg-red-50 border border-red-200 rounded-lg px-3.5 py-2.5 text-[12.5px] text-red-700 mt-3">
      <div class="font-bold mb-0.5">Schedule Conflict!</div>
      <div id="assign-conflict-message"></div>
    </div>

    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-assign')" class="btn btn-secondary">Cancel</button>
      <button type="button" onclick="saveAssignment()" class="btn btn-primary" id="assign-save-btn">Save Assignment</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

// ── TOPBAR NOTIF PANEL (copied from chair_dashboard.blade.php) ─────────────
function toggleNotifPanel() {
  document.getElementById('notif-panel').classList.toggle('hidden');
}
document.addEventListener('click', function(e) {
  const panel = document.getElementById('notif-panel');
  const btn   = document.getElementById('notif-btn');
  if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
    panel.classList.add('hidden');
  }
});

// ── MODAL ────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) {
  document.getElementById(id).classList.remove('open');
  resetAssignForm();
}
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) closeModal(m.id); });
});

function openAssignModal(preselectFacultyId) {
  resetAssignForm();
  if (preselectFacultyId) {
    document.getElementById('assign-faculty').value = preselectFacultyId;
    updateAssignPreview();
  }
  openModal('modal-assign');
}

function resetAssignForm() {
  document.getElementById('assign-subject').value = '';
  document.getElementById('assign-faculty').value = '';
  document.getElementById('assign-room').value = '';
  document.getElementById('assign-section').value = '';
  document.getElementById('assign-warning').classList.add('hidden');
  document.getElementById('assign-conflict').classList.add('hidden');
}

// ── LIVE "NEAR MAX" PREVIEW (client-side only, informational) ──────────────
function updateAssignPreview() {
  const subjSel = document.getElementById('assign-subject');
  const facSel  = document.getElementById('assign-faculty');
  const warning = document.getElementById('assign-warning');

  const subjOpt = subjSel.options[subjSel.selectedIndex];
  const facOpt  = facSel.options[facSel.selectedIndex];

  if (!subjSel.value || !facSel.value) {
    warning.classList.add('hidden');
    return;
  }

  const subjUnits   = parseFloat(subjOpt.dataset.units || 0);
  const currentUnits = parseFloat(facOpt.dataset.units || 0);
  const maxUnits     = parseFloat(facOpt.dataset.max || 30);
  const isPartTime   = facOpt.dataset.parttime === '1';
  const newTotal     = currentUnits + subjUnits;
  const remaining    = Math.max(0, maxUnits - newTotal);
  const facName      = facOpt.textContent.trim();

  if (isPartTime) {
    warning.textContent = `${facName} is part-time — verify this additional load is appropriate before assigning.`;
    warning.classList.remove('hidden');
    return;
  }

  if (newTotal > maxUnits) {
    warning.textContent = `${facName} — currently ${currentUnits}u/${maxUnits}u. Adding ${subjUnits}u → ${newTotal}u exceeds the maximum load!`;
    warning.classList.remove('hidden');
  } else if (newTotal >= maxUnits - 3) {
    warning.textContent = `${facName} — currently ${currentUnits}u/${maxUnits}u. Adding ${subjUnits}u → ${newTotal}u. Only ${remaining} unit(s) left — near maximum!`;
    warning.classList.remove('hidden');
  } else {
    warning.classList.add('hidden');
  }
}

// ── SAVE ASSIGNMENT ──────────────────────────────────────────────────────
async function saveAssignment() {
  const subjId  = document.getElementById('assign-subject').value;
  const facId   = document.getElementById('assign-faculty').value;
  const day     = document.getElementById('assign-day').value;
  const slot    = document.getElementById('assign-timeslot').value;
  const roomId  = document.getElementById('assign-room').value;
  const secId   = document.getElementById('assign-section').value;

  document.getElementById('assign-conflict').classList.add('hidden');

  if (!subjId || !facId || !roomId || !secId) {
    showToast('Please fill in all fields.');
    return;
  }

  const [startTime, endTime] = slot.split('|');
  const saveBtn = document.getElementById('assign-save-btn');
  saveBtn.disabled = true;
  saveBtn.textContent = 'Saving...';

  try {
    const res = await fetch("{{ route('chair.faculty_load.assign') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF_TOKEN,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        subj_id: subjId,
        fac_id: facId,
        sec_id: secId,
        room_id: roomId,
        sch_day: day,
        sch_start_time: startTime,
        sch_end_time: endTime
      })
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.conflict) {
        document.getElementById('assign-conflict-message').textContent = data.message;
        document.getElementById('assign-conflict').classList.remove('hidden');
      } else {
        showToast(data.message || 'Failed to save assignment.');
      }
      return;
    }

    showToast(data.message || 'Subject assigned successfully!');
    closeModal('modal-assign');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast('Failed to save assignment. Please try again.');
    console.error(err);
  } finally {
    saveBtn.disabled = false;
    saveBtn.textContent = 'Save Assignment';
  }
}

// ── TOAST ────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>

</body>
</html>