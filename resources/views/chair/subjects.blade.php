<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Subject Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">
  @include('partials.chair_sidebar')

  <main class="flex-1 overflow-hidden">
    @include('partials.chair_header', ['title' => 'Subject Management', 'badgeText' => 'BSIS Department'])

    <div id="page-subjects" class="page-content">
      <div class="mb-5 flex items-center justify-between gap-3">
        <div>
          <div class="text-[20px] font-extrabold text-slate-900">Subject Management</div>
          <div class="mt-1 text-[13px] text-slate-500">BSIS Department</div>
        </div>
        <button class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-blue-700" onclick="openModal('modal-add-subject')">+ Add Subject</button>
      </div>

      <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="overflow-x-auto">
          <table class="w-full border-collapse text-left">
            <thead>
              <tr>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Code</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Subject Name</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Units</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Lec</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Lab</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Assigned Faculty</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Status</th>
                <th class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Action</th>
              </tr>
            </thead>
            <tbody id="subjects-table">
              @forelse($subjects as $s)
              <tr class="hover:bg-slate-50">
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-mono font-bold text-slate-900">{{ $s->subj_code }}</td>
                <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">{{ $s->subj_name }}</td>
                <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">{{ $s->subj_lecture_hours + $s->subj_lab_hours }}</td>
                <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">{{ $s->subj_lecture_hours }}</td>
                <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">{{ $s->subj_lab_hours }}</td>
                <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">
                  @if($s->assignedFaculty)
                    {{ $s->assignedFaculty }}
                  @else
                    <span class="text-red-600">Unassigned</span>
                  @endif
                </td>
                <td class="border-b border-slate-100 px-3 py-3">
                  @if($s->assignedFaculty)
                    <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Assigned</span>
                  @else
                    <span class="inline-flex rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600">No Faculty</span>
                  @endif
                </td>
                <td class="border-b border-slate-100 px-3 py-3">
                  @if($s->assignedFaculty)
                    <button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50"
                      onclick="openEditSubject('{{ $s->subj_id }}','{{ $s->subj_code }}','{{ addslashes($s->subj_name) }}','{{ $s->subj_lecture_hours }}','{{ $s->subj_lab_hours }}','{{ $s->subj_dept_id }}')">
                      Edit
                    </button>
                  @else
                    <button class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-blue-700"
                      onclick="openAssignModal('{{ $s->subj_id }}','{{ $s->subj_code }}','{{ addslashes($s->subj_name) }}','{{ $s->subj_lecture_hours + $s->subj_lab_hours }}')">
                      Assign
                    </button>
                  @endif
                  <form action="{{ route('chair.subject.destroy', $s->subj_id) }}" method="POST" class="inline" onsubmit="return confirm('Deactivate {{ $s->subj_code }}? It will be hidden from active lists but kept for historical records.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="ml-2 rounded-lg bg-red-50 px-3 py-1.5 text-[11px] font-semibold text-red-600 transition hover:bg-red-100">Delete</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="8" class="px-3 py-5 text-center text-sm text-slate-500">No subjects found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>
</div>

{{-- ADD SUBJECT MODAL --}}
<div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-add-subject-backdrop"></div>
<div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="modal-add-subject">
  <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
    <form action="{{ route('chair.subject.store') }}" method="POST">
      @csrf
      <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
        <div class="text-lg font-bold text-slate-900">Add New Subject</div>
        <button type="button" class="text-2xl text-slate-500 hover:text-slate-700" onclick="closeModal('modal-add-subject')">×</button>
      </div>
      <div class="space-y-4 p-5">
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Subject Code</label>
            <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_code" value="{{ old('subj_code') }}" placeholder="e.g. CC 314">
            @error('subj_code') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Department</label>
            <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_dept_id">
              <option value="">-- Select --</option>
              @foreach($departments as $dept)
                <option value="{{ $dept->dept_id }}" @selected(old('subj_dept_id') == $dept->dept_id)>{{ $dept->dept_name }}</option>
              @endforeach
            </select>
            @error('subj_dept_id') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
          </div>
        </div>
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Program</label>
          <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_prog_id">
            <option value="">-- Select --</option>
            @foreach($programs as $prog)
              <option value="{{ $prog->prog_id }}" @selected(old('subj_prog_id') == $prog->prog_id)>{{ $prog->prog_name }}</option>
            @endforeach
          </select>
          @error('subj_prog_id') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Subject Name</label>
          <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_name" value="{{ old('subj_name') }}" placeholder="e.g. Web Systems and Technologies">
          @error('subj_name') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lecture Hrs</label>
            <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_lecture_hours" type="number" min="0" max="6" value="{{ old('subj_lecture_hours', 0) }}">
            @error('subj_lecture_hours') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
          </div>
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lab Hrs</label>
            <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" name="subj_lab_hours" type="number" min="0" max="6" value="{{ old('subj_lab_hours', 0) }}">
            @error('subj_lab_hours') <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
        <button type="button" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700" onclick="closeModal('modal-add-subject')">Cancel</button>
        <button type="submit" class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700">Add Subject</button>
      </div>
    </form>
  </div>
</div>

{{-- EDIT SUBJECT MODAL --}}
<div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-edit-subject-backdrop"></div>
<div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="modal-edit-subject">
  <div class="w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
    <form id="edit-subj-form" method="POST">
      @csrf
      @method('PUT')
      <input type="hidden" id="edit-subj-dept-id" name="subj_dept_id">
      <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
        <div class="text-lg font-bold text-slate-900">Edit Subject</div>
        <button type="button" class="text-2xl text-slate-500 hover:text-slate-700" onclick="closeModal('modal-edit-subject')">×</button>
      </div>
      <div class="space-y-4 p-5">
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Subject Code</label>
          <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="edit-subj-code" name="subj_code">
        </div>
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Subject Name</label>
          <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="edit-subj-name" name="subj_name">
        </div>
        <div class="grid gap-4 md:grid-cols-2">
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lecture Hrs</label>
            <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="edit-subj-lec" name="subj_lecture_hours" type="number" min="0" max="6">
          </div>
          <div>
            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lab Hrs</label>
            <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="edit-subj-lab" name="subj_lab_hours" type="number" min="0" max="6">
          </div>
        </div>
      </div>
      <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
        <button type="button" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700" onclick="closeModal('modal-edit-subject')">Cancel</button>
        <button type="submit" class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700">Save Changes</button>
      </div>
    </form>
  </div>
</div>

{{--
    ASSIGN FACULTY MODAL
    NOTE: this posts to chair.faculty_load.assign (the same endpoint the
    Faculty Load page uses) rather than a new endpoint here, so there's one
    place that creates Study_Load + Schedule rows and handles the DB's
    conflict-detection constraints, instead of two copies of that logic.
--}}
<div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-assign-backdrop"></div>
<div class="fixed inset-0 z-50 hidden items-center justify-center p-4" id="modal-assign">
  <div class="w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
      <div>
        <div class="text-lg font-bold text-slate-900">Assign Faculty</div>
        <div id="assign-subj-title" class="text-[12px] text-slate-500 mt-0.5"></div>
      </div>
      <button type="button" class="text-2xl text-slate-500 hover:text-slate-700" onclick="closeModal('modal-assign')">×</button>
    </div>
    <div class="space-y-4 p-5">
      <input type="hidden" id="assign-subj-id">
      <div>
        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Select Faculty</label>
        <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="assign-faculty">
          <option value="">— Choose Faculty —</option>
          @foreach($faculty as $f)
            <option value="{{ $f['id'] }}">{{ $f['name'] }} ({{ $f['units'] }}u/30u)</option>
          @endforeach
        </select>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Day</label>
          <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="assign-day">
            <option value="Monday">Monday</option>
            <option value="Tuesday">Tuesday</option>
            <option value="Wednesday">Wednesday</option>
            <option value="Thursday">Thursday</option>
            <option value="Friday">Friday</option>
            <option value="Saturday">Saturday</option>
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Time Slot</label>
          <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="assign-timeslot">
            <option value="07:00:00|08:30:00">7:00 – 8:30 AM</option>
            <option value="08:30:00|10:00:00">8:30 – 10:00 AM</option>
            <option value="10:00:00|11:30:00">10:00 – 11:30 AM</option>
            <option value="11:30:00|13:00:00">11:30 AM – 1:00 PM</option>
            <option value="13:00:00|14:30:00">1:00 – 2:30 PM</option>
            <option value="14:30:00|16:00:00">2:30 – 4:00 PM</option>
            <option value="16:00:00|17:30:00">4:00 – 5:30 PM</option>
          </select>
        </div>
      </div>
      <div class="grid gap-4 md:grid-cols-2">
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Room</label>
          <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="assign-room">
            <option value="">— Select room —</option>
            @foreach($rooms as $room)
              <option value="{{ $room->room_id }}">{{ $room->room_name }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Section</label>
          <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" id="assign-section">
            <option value="">— Select section —</option>
            @foreach($section as $sec)
              <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div id="assign-conflict" class="hidden bg-red-50 border border-red-200 rounded-lg px-3.5 py-2.5 text-[12.5px] text-red-700">
        <div class="font-bold mb-0.5">Schedule Conflict!</div>
        <div id="assign-conflict-message"></div>
      </div>
    </div>
    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
      <button type="button" class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700" onclick="closeModal('modal-assign')">Cancel</button>
      <button type="button" class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700" id="assign-save-btn" onclick="saveAssign()">Assign Faculty</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
const SUBJECT_UPDATE_URL_TEMPLATE = "{{ route('chair.subject.update', ['id' => '__ID__']) }}";
const ASSIGN_URL = "{{ route('chair.faculty_load.assign') }}";

// ── MODALS ───────────────────────────────────────────────────────────────
function openModal(id) {
  const node = document.getElementById(id);
  const backdrop = document.getElementById(id + '-backdrop');
  if (node) { node.classList.remove('hidden'); node.classList.add('flex'); }
  if (backdrop) backdrop.classList.remove('hidden');
}
function closeModal(id) {
  const node = document.getElementById(id);
  const backdrop = document.getElementById(id + '-backdrop');
  if (node) { node.classList.add('hidden'); node.classList.remove('flex'); }
  if (backdrop) backdrop.classList.add('hidden');
}

// ── EDIT SUBJECT ─────────────────────────────────────────────────────────
function openEditSubject(id, code, name, lec, lab, deptId) {
  document.getElementById('edit-subj-form').action = SUBJECT_UPDATE_URL_TEMPLATE.replace('__ID__', id);
  document.getElementById('edit-subj-code').value = code;
  document.getElementById('edit-subj-name').value = name;
  document.getElementById('edit-subj-lec').value = lec;
  document.getElementById('edit-subj-lab').value = lab;
  document.getElementById('edit-subj-dept-id').value = deptId;
  openModal('modal-edit-subject');
}

// ── ASSIGN FACULTY ───────────────────────────────────────────────────────
function openAssignModal(subjId, code, name, units) {
  document.getElementById('assign-subj-id').value = subjId;
  document.getElementById('assign-subj-title').textContent = `${code} — ${name} (${units}u)`;
  document.getElementById('assign-faculty').value = '';
  document.getElementById('assign-room').value = '';
  document.getElementById('assign-section').value = '';
  document.getElementById('assign-conflict').classList.add('hidden');
  openModal('modal-assign');
}

async function saveAssign() {
  const subjId = document.getElementById('assign-subj-id').value;
  const facId  = document.getElementById('assign-faculty').value;
  const day    = document.getElementById('assign-day').value;
  const slot   = document.getElementById('assign-timeslot').value;
  const roomId = document.getElementById('assign-room').value;
  const secId  = document.getElementById('assign-section').value;

  document.getElementById('assign-conflict').classList.add('hidden');

  if (!facId || !roomId || !secId) {
    showToast('Please fill in all fields.');
    return;
  }

  const [startTime, endTime] = slot.split('|');
  const btn = document.getElementById('assign-save-btn');
  btn.disabled = true;
  btn.textContent = 'Saving...';

  try {
    const res = await fetch(ASSIGN_URL, {
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
        showToast(data.message || 'Failed to assign faculty.');
      }
      return;
    }

    showToast(data.message || 'Faculty assigned successfully!');
    closeModal('modal-assign');
    setTimeout(() => window.location.reload(), 600);
  } catch (err) {
    showToast('Failed to assign faculty. Please try again.');
    console.error(err);
  } finally {
    btn.disabled = false;
    btn.textContent = 'Assign Faculty';
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