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
            &middot; Full-time max {{ \App\Http\Controllers\Chair\ChairFacultyLoadController::FULL_TIME_MAX_UNITS }}u
            &middot; Part-time max {{ \App\Http\Controllers\Chair\ChairFacultyLoadController::PART_TIME_MAX_UNITS }}u
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
                  <span class="badge {{ $fl['status_badge'] }}">{{ $fl['remaining'] }}u left</span>
                  <span class="text-[10px] text-slate-400 ml-1">/ {{ $fl['max_units'] }}u</span>
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

{{-- ASSIGN SUBJECT MODAL — subject + faculty + section only (no room / day / time) --}}
<div class="modal-overlay" id="modal-assign">
  <div class="modal-box w-[520px]">
    <div class="modal-header">
      <div class="modal-title">Assign Subject to Faculty</div>
      <button type="button" onclick="closeModal('modal-assign')" class="modal-close">✕</button>
    </div>

    <p class="text-[12px] text-slate-500 mb-3">
      Assigns the subject to the teacher’s load for the current semester.
      Room and schedule time are set later in <strong>PBS</strong> / <strong>PBT</strong>.
    </p>

    <div class="mb-3">
      <label class="field-label">Subject</label>
      <select id="assign-subject" class="field-input" onchange="updateAssignPreview()">
        <option value="">— Select subject —</option>
        @foreach($subjects as $subj)
          <option value="{{ $subj->subj_id }}"
            data-units="{{ (float)$subj->subj_lecture_hours + (float)$subj->subj_lab_hours }}">
            {{ $subj->subj_code }} — {{ $subj->subj_name }}
            ({{ (float)$subj->subj_lecture_hours + (float)$subj->subj_lab_hours }}u)
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="field-label">Faculty Member</label>
      <select id="assign-faculty" class="field-input" onchange="updateAssignPreview()">
        <option value="">— Select faculty —</option>
        @foreach($facultyLoad as $fl)
          <option value="{{ $fl['id'] }}"
            data-units="{{ $fl['total_units'] }}"
            data-max="{{ $fl['max_units'] }}"
            data-parttime="{{ $fl['is_part_time'] ? '1' : '0' }}">
            {{ $fl['name'] }}
            ({{ $fl['total_units'] }}/{{ $fl['max_units'] }}u
            · {{ $fl['is_part_time'] ? 'PT' : 'FT' }})
          </option>
        @endforeach
      </select>
    </div>

    <div class="mb-3">
      <label class="field-label">Section</label>
      <select id="assign-section" class="field-input">
        <option value="">— Select section —</option>
        @foreach($sections as $sec)
          <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
        @endforeach
      </select>
    </div>

    @if($semester)
      <input type="hidden" id="assign-semester" value="{{ $semester->sem_id }}">
      <div class="mb-3 text-[12px] text-slate-500">
        Semester: <strong>{{ $semester->sem_name }}</strong> (current)
      </div>
    @else
      <div class="mb-3 text-[12px] text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
        No active semester found. Set <code>sem_is_active = true</code> on a semester row.
      </div>
    @endif

    <div id="assign-warning" class="hidden bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12.5px] text-amber-800 mb-3"></div>
    <div id="assign-error" class="hidden bg-red-50 border border-red-200 rounded-lg px-3.5 py-2.5 text-[12.5px] text-red-700 mb-3"></div>

    <div class="modal-footer">
      <button type="button" onclick="closeModal('modal-assign')" class="btn btn-secondary">Cancel</button>
      <button type="button" onclick="saveAssignment()" class="btn btn-primary" id="assign-save-btn">Save Assignment</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

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

function openModal(id) { document.getElementById(id).classList.add('active'); }
function closeModal(id) {
  document.getElementById(id).classList.remove('active');
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
  document.getElementById('assign-section').value = '';
  document.getElementById('assign-warning').classList.add('hidden');
  document.getElementById('assign-error').classList.add('hidden');
}

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

  const subjUnits    = parseFloat(subjOpt.dataset.units || 0);
  const currentUnits = parseFloat(facOpt.dataset.units || 0);
  const maxUnits     = parseFloat(facOpt.dataset.max || 30);
  const isPartTime   = facOpt.dataset.parttime === '1';
  const newTotal     = currentUnits + subjUnits;
  const remaining    = Math.max(0, maxUnits - newTotal);
  const facName      = facOpt.textContent.trim().split('(')[0].trim();
  const typeLabel    = isPartTime ? 'part-time' : 'full-time';

  if (newTotal > maxUnits) {
    warning.textContent = `${facName} (${typeLabel}) — ${currentUnits}u/${maxUnits}u. Adding ${subjUnits}u → ${newTotal}u exceeds the maximum!`;
    warning.classList.remove('hidden');
  } else if (newTotal >= maxUnits - 3) {
    warning.textContent = `${facName} (${typeLabel}) — ${currentUnits}u/${maxUnits}u. Adding ${subjUnits}u → ${newTotal}u. Only ${remaining}u left — near maximum.`;
    warning.classList.remove('hidden');
  } else {
    warning.textContent = `${facName} — ${currentUnits}u + ${subjUnits}u = ${newTotal}u / ${maxUnits}u (${remaining}u left).`;
    warning.classList.remove('hidden');
  }
}

async function saveAssignment() {
  const subjId = document.getElementById('assign-subject').value;
  const facId  = document.getElementById('assign-faculty').value;
  const secId  = document.getElementById('assign-section').value;
  const semEl  = document.getElementById('assign-semester');
  const semId  = semEl ? semEl.value : '';
  const errBox = document.getElementById('assign-error');

  errBox.classList.add('hidden');

  if (!subjId || !facId || !secId) {
    showToast('Please select subject, faculty, and section.');
    return;
  }
  if (!semId) {
    showToast('No active semester. Cannot assign.');
    return;
  }

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
        fac_id:  facId,
        sec_id:  secId,
        sem_id:  semId
      })
    });

    const data = await res.json().catch(() => ({}));

    if (!res.ok || !data.success) {
      errBox.textContent = data.message || 'Failed to save assignment.';
      errBox.classList.remove('hidden');
      showToast(data.message || 'Failed to save assignment.');
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

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>

</body>
</html>
