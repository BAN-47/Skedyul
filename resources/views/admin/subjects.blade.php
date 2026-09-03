<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Subject Management</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

@php
    $subject = $subject ?? collect([]);
    $departments = $departments ?? collect([]);
    $programs = $programs ?? collect([]);
@endphp

<div class="app-shell">
  @include('partials.admin_sidebar')

  <div class="app-main">
    @include('partials.admin_header', ['title' => 'Subject Management'])

    <div class="page-content" id="page-subjects">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Subject Management</div>
          <button type="button" onclick="openModal('modal-add-subject')" class="btn btn-primary">+ Add Subject</button>
        </div>

        <div class="overflow-x-auto">
          <table class="data-table" id="subjects-table">
            <tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Lec Hrs</th><th>Lab Hrs</th><th>Department</th><th>Action</th></tr>
            @foreach ($subject as $s)
              <tr>
                <td><span class="font-mono font-bold">{{ $s->subj_code }}</span></td>
                <td class="font-semibold">{{ $s->subj_name }}</td>
                <td>{{ $s->subj_lecture_hours + $s->subj_lab_hours }}</td>
                <td>{{ $s->subj_lecture_hours }}</td>
                <td>{{ $s->subj_lab_hours }}</td>
                <td>{{ optional($s->department)->dept_name ?? '—' }}</td>
                <td>
                  <div class="flex gap-1.5">
                    <button
                      type="button"
                      class="btn btn-secondary text-[11px] px-3 py-1.5"
                      onclick="openEditSubject(this)"
                      data-id="{{ $s->subj_id }}"
                      data-code="{{ $s->subj_code }}"
                      data-name="{{ $s->subj_name }}"
                      data-lec="{{ $s->subj_lecture_hours }}"
                      data-lab="{{ $s->subj_lab_hours }}"
                      data-dept="{{ $s->subj_dept_id }}"
                      data-prog="{{ $s->subj_prog_id }}"
                      data-action="{{ route('subject.update', $s->subj_id) }}"
                    >Edit</button>

                    <form action="{{ route('subject.destroy', $s->subj_id) }}" method="POST" class="inline" onsubmit="return confirm('Delete: {{ $s->subj_code }}?\nThis action cannot be undone.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>

  </div>
</div>

{{-- ADD SUBJECT MODAL --}}
<div class="modal-overlay" id="modal-add-subject">
  <div class="modal-box w-[500px]">
    <form action="{{ route('subject.store') }}" method="POST">
      @csrf

      <div class="modal-header">
        <div class="modal-title">Add New Subject</div>
        <button class="modal-close" type="button" onclick="closeModal('modal-add-subject')">✕</button>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Subject Code</label>
          <input class="field-input" name="subj_code" value="{{ old('subj_code') }}" placeholder="e.g. CC 314">
          @error('subj_code') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="field-label">Department</label>
          <select class="field-input" name="subj_dept_id">
            <option value="">-- Select Department --</option>
            @foreach ($departments as $dept)
              <option value="{{ $dept->dept_id }}" @selected(old('subj_dept_id') == $dept->dept_id)>{{ $dept->dept_name }}</option>
            @endforeach
          </select>
          @error('subj_dept_id') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="mb-4">
        <label class="field-label">Program</label>
        <select class="field-input" name="subj_prog_id">
          <option value="">-- Select Program --</option>
          @foreach ($programs as $prog)
            <option value="{{ $prog->prog_id }}" @selected(old('subj_prog_id') == $prog->prog_id)>{{ $prog->prog_name }}</option>
          @endforeach
        </select>
        @error('subj_prog_id') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="mb-4">
        <label class="field-label">Subject Name</label>
        <input class="field-input" name="subj_name" value="{{ old('subj_name') }}" placeholder="e.g. Web Systems and Technologies">
        @error('subj_name') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="grid grid-cols-3 gap-3 mb-3">
        <div>
          <label class="field-label">Units</label>
          <input class="field-input bg-slate-50" id="add-subj-units" type="number" value="0" readonly>
        </div>
        <div>
          <label class="field-label">Lecture Hrs</label>
          <input class="field-input" id="add-subj-lec" name="subj_lecture_hours" type="number" min="0" max="6" value="{{ old('subj_lecture_hours', 0) }}" oninput="updateSubjectUnits('add')">
          @error('subj_lecture_hours') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
        </div>
        <div>
          <label class="field-label">Lab Hrs</label>
          <input class="field-input" id="add-subj-lab" name="subj_lab_hours" type="number" min="0" max="6" value="{{ old('subj_lab_hours', 0) }}" oninput="updateSubjectUnits('add')">
          @error('subj_lab_hours') <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="mb-4">
        <label class="field-label">Description (optional)</label>
        <textarea class="field-input resize-y" name="subj_description" rows="2" placeholder="Brief subject description...">{{ old('subj_description') }}</textarea>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" onclick="closeModal('modal-add-subject')">Cancel</button>
        <button class="btn btn-primary" type="submit">Add Subject</button>
      </div>
    </form>
  </div>
</div>

{{-- EDIT SUBJECT MODAL --}}
<div class="modal-overlay" id="modal-edit-subject">
  <div class="modal-box w-[500px]">
    <form id="edit-subj-form" action="" method="POST">
      @csrf
      @method('PUT')

      <div class="modal-header">
        <div class="modal-title">Edit Subject</div>
        <button class="modal-close" type="button" onclick="closeModal('modal-edit-subject')">✕</button>
      </div>

      <div class="grid grid-cols-2 gap-3 mb-3">
        <div>
          <label class="field-label">Subject Code</label>
          <input class="field-input" id="edit-subj-code" name="subj_code" placeholder="e.g. CC 313">
        </div>
        <div>
          <label class="field-label">Department</label>
          <select class="field-input" id="edit-subj-dept" name="subj_dept_id">
            <option value="">-- Select Department --</option>
            @foreach ($departments as $dept)
              <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-4">
        <label class="field-label">Program</label>
        <select class="field-input" id="edit-subj-prog" name="subj_prog_id">
          <option value="">-- Select Program --</option>
          @foreach ($programs as $prog)
            <option value="{{ $prog->prog_id }}">{{ $prog->prog_name }}</option>
          @endforeach
        </select>
      </div>

      <div class="mb-4">
        <label class="field-label">Subject Name</label>
        <input class="field-input" id="edit-subj-name" name="subj_name" placeholder="Subject name">
      </div>

      <div class="grid grid-cols-3 gap-3 mb-3">
        <div>
          <label class="field-label">Units</label>
          <input class="field-input bg-slate-50" id="edit-subj-units" type="number" value="0" readonly>
        </div>
        <div>
          <label class="field-label">Lecture Hrs</label>
          <input class="field-input" id="edit-subj-lec" name="subj_lecture_hours" type="number" min="0" max="6" oninput="updateSubjectUnits('edit')">
        </div>
        <div>
          <label class="field-label">Lab Hrs</label>
          <input class="field-input" id="edit-subj-lab" name="subj_lab_hours" type="number" min="0" max="6" oninput="updateSubjectUnits('edit')">
        </div>
      </div>

      <div class="bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12px] text-amber-800 mb-1">
        ⚠️ Editing a subject may affect existing schedule assignments.
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" type="button" onclick="closeModal('modal-edit-subject')">Cancel</button>
        <button class="btn btn-primary" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>

// ── MODALS ─────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
}

function updateSubjectUnits(prefix) {
  const lecture = Number(document.getElementById(`${prefix}-subj-lec`).value) || 0;
  const lab = Number(document.getElementById(`${prefix}-subj-lab`).value) || 0;
  document.getElementById(`${prefix}-subj-units`).value = lecture + lab;
}

// ── SUBJECTS: ADD ──────────────────────────────────────────────────────────────
// NOTE: this function references element IDs (add-subj-code, add-subj-units, add-subj-dept,
// add-subj-desc) that do not exist in the current "Add Subject" modal above — that modal is a
// real server-submitted form. This appears to be leftover code from an earlier version and is
// carried over unchanged, not fixed, since that's a functional issue rather than a styling one.
function saveAddSubject() {
  const code = document.getElementById('add-subj-code').value.trim();
  const name = document.getElementById('add-subj-name').value.trim();
  if (!code || !name) { alert('Please fill in Subject Code and Name.'); return; }
  const units = document.getElementById('add-subj-units').value || '3';
  const lec   = document.getElementById('add-subj-lec').value || '0';
  const lab   = document.getElementById('add-subj-lab').value || '0';
  const dept  = document.getElementById('add-subj-dept').value;
  const tbody = document.querySelector('#subjects-table');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><span class="font-mono font-bold">${code}</span></td>
    <td class="font-semibold">${name}</td><td>${units}</td><td>${lec}</td><td>${lab}</td><td>${dept}</td>
    <td><div class="flex gap-1.5">
      <button class="btn btn-secondary text-[11px] px-3 py-1.5" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${dept}')">Edit</button>
      <button class="btn btn-danger text-[11px] px-3 py-1.5" onclick="deleteTableRow(this,'${code}')">Delete</button>
    </div></td>`;
  tbody.appendChild(tr);
  ['add-subj-code','add-subj-name','add-subj-units','add-subj-lec','add-subj-lab','add-subj-desc'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-subject');
  showToast('Subject "' + name + '" added successfully!');
}

// ── SUBJECTS: EDIT ─────────────────────────────────────────────────────────────
function openEditSubject(btn) {
  const form = document.getElementById('edit-subj-form');
  form.action = btn.dataset.action;

  document.getElementById('edit-subj-code').value = btn.dataset.code;
  document.getElementById('edit-subj-name').value = btn.dataset.name;
  document.getElementById('edit-subj-lec').value  = btn.dataset.lec;
  document.getElementById('edit-subj-lab').value  = btn.dataset.lab;
  updateSubjectUnits('edit');
  document.getElementById('edit-subj-dept').value = btn.dataset.dept;
  document.getElementById('edit-subj-prog').value = btn.dataset.prog;

  openModal('modal-edit-subject');
}

// ── DELETE ROW ─────────────────────────────────────────────────────────────────
function deleteTableRow(btn, name) {
  if (!confirm('Delete: ' + name + '?\nThis action cannot be undone.')) return;
  const row = btn.closest('tr');
  if (row) {
    row.style.transition = 'opacity 0.3s';
    row.style.opacity = '0';
    setTimeout(() => { row.remove(); }, 300);
  }
  showToast(name + ' deleted successfully.');
}
</script>
</body>
</html>