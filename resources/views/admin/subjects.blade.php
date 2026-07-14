<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Subject Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/subjects.css') }}">
</head>
<body>

@php
    $subject = $subject ?? collect([]);
    $departments = $departments ?? collect([]);
    $programs = $programs ?? collect([]);
@endphp

<div class="screen active" style="display:flex;">
  @include('partials.admin_sidebar')
 
  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Subject Management</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>
 
    <div id="page-subjects" class="page active">
      <div class="card">
        <div class="card-header">
          <div class="card-title">Subject Management</div>
          <button class="topbar-btn btn-primary" type="button" onclick="openModal('modal-add-subject')">+ Add Subject</button>
        </div>
 
        <div class="table-wrap">
          <table id="subjects-table">
            <tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Lec Hrs</th><th>Lab Hrs</th><th>Department</th><th>Action</th></tr>
            @foreach ($subject as $s)
              <tr>
                <td><span style="font-family:var(--mono);font-weight:700">{{ $s->subj_code }}</span></td>
                <td>{{ $s->subj_name }}</td>
                <td>{{ $s->subj_lecture_hours + $s->subj_lab_hours }}</td>
                <td>{{ $s->subj_lecture_hours }}</td>
                <td>{{ $s->subj_lab_hours }}</td>
                <td>{{ optional($s->department)->dept_name ?? '—' }}</td>
                <td>
                  <button
                    type="button"
                    class="topbar-btn btn-secondary"
                    style="padding:4px 10px;font-size:11px;"
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
 
                  <form action="{{ route('subject.destroy', $s->subj_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Delete: {{ $s->subj_code }}?\nThis action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;">Delete</button>
                  </form>
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
 
  </div>
</div>

<div class="modal-overlay" id="modal-add-subject">
  <div class="modal" style="width:500px;">
    <form action="{{ route('subject.store') }}" method="POST">
      @csrf
 
      <div class="modal-header">
        <div class="modal-title">Add New Subject</div>
        <button class="modal-close" type="button" onclick="closeModal('modal-add-subject')">✕</button>
      </div>
 
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Subject Code</label>
          <input class="field-input" name="subj_code" value="{{ old('subj_code') }}" placeholder="e.g. CC 314">
          @error('subj_code') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
        <div class="field-group">
          <label class="field-label">Department</label>
          <select class="field-select" name="subj_dept_id">
            <option value="">-- Select Department --</option>
            @foreach ($departments as $dept)
              <option value="{{ $dept->dept_id }}" @selected(old('subj_dept_id') == $dept->dept_id)>{{ $dept->dept_name }}</option>
            @endforeach
          </select>
          @error('subj_dept_id') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
      </div>
 
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Program</label>
        <select class="field-select" name="subj_prog_id">
          <option value="">-- Select Program --</option>
          @foreach ($programs as $prog)
            <option value="{{ $prog->prog_id }}" @selected(old('subj_prog_id') == $prog->prog_id)>{{ $prog->prog_name }}</option>
          @endforeach
        </select>
        @error('subj_prog_id') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
      </div>
 
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Subject Name</label>
        <input class="field-input" name="subj_name" value="{{ old('subj_name') }}" placeholder="e.g. Web Systems and Technologies">
        @error('subj_name') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
      </div>
 
      <div class="form-row three">
        <div class="field-group">
          <label class="field-label">Lecture Hrs</label>
          <input class="field-input" name="subj_lecture_hours" type="number" min="0" max="6" value="{{ old('subj_lecture_hours', 0) }}">
          @error('subj_lecture_hours') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
        <div class="field-group">
          <label class="field-label">Lab Hrs</label>
          <input class="field-input" name="subj_lab_hours" type="number" min="0" max="6" value="{{ old('subj_lab_hours', 0) }}">
          @error('subj_lab_hours') <div style="color:var(--red);font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Description (optional)</label>
        <textarea class="field-input" name="subj_description" rows="2" placeholder="Brief subject description..." style="resize:vertical;">{{ old('subj_description') }}</textarea>
      </div>
 
      <div class="modal-footer">
        <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-add-subject')">Cancel</button>
        <button class="topbar-btn btn-primary" type="submit">Add Subject</button>
      </div>
    </form>
  </div>
</div>
 
<div class="modal-overlay" id="modal-edit-subject">
  <div class="modal" style="width:500px;">
    <form id="edit-subj-form" action="" method="POST">
      @csrf
      @method('PUT')
 
      <div class="modal-header">
        <div class="modal-title">Edit Subject</div>
        <button class="modal-close" type="button" onclick="closeModal('modal-edit-subject')">✕</button>
      </div>
 
      <div class="form-row">
        <div class="field-group">
          <label class="field-label">Subject Code</label>
          <input class="field-input" id="edit-subj-code" name="subj_code" placeholder="e.g. CC 313">
        </div>
        <div class="field-group">
          <label class="field-label">Department</label>
          <select class="field-select" id="edit-subj-dept" name="subj_dept_id">
            <option value="">-- Select Department --</option>
            @foreach ($departments as $dept)
              <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
            @endforeach
          </select>
        </div>
      </div>
 
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Program</label>
        <select class="field-select" id="edit-subj-prog" name="subj_prog_id">
          <option value="">-- Select Program --</option>
          @foreach ($programs as $prog)
            <option value="{{ $prog->prog_id }}">{{ $prog->prog_name }}</option>
          @endforeach
        </select>
      </div>
 
      <div class="field-group" style="margin-bottom:16px;">
        <label class="field-label">Subject Name</label>
        <input class="field-input" id="edit-subj-name" name="subj_name" placeholder="Subject name">
      </div>
 
      <div class="form-row three">
        <div class="field-group">
          <label class="field-label">Lecture Hrs</label>
          <input class="field-input" id="edit-subj-lec" name="subj_lecture_hours" type="number" min="0" max="6">
        </div>
        <div class="field-group">
          <label class="field-label">Lab Hrs</label>
          <input class="field-input" id="edit-subj-lab" name="subj_lab_hours" type="number" min="0" max="6">
        </div>
      </div>
 
      <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:4px;">
        ⚠️ Editing a subject may affect existing schedule assignments.
      </div>
 
      <div class="modal-footer">
        <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-edit-subject')">Cancel</button>
        <button class="topbar-btn btn-primary" type="submit">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const ADMIN_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'var(--blue)', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = ADMIN_NOTIFS.map((n) => `
    <div class="notif-drop-item ${n.unread?'unread':''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  const dd = document.getElementById('notif-dropdown');
  if (dd) dd.style.display = notifOpen ? 'block' : 'none';
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = 'none';
  }
});
function markRead(el) {
  el.classList.remove('unread');
  updateNotifCount();
}
function markAllRead() {
  document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('.notif-drop-item.unread').length;
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline' : 'none'; }
}
renderNotifList();

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

// ── SUBJECTS: ADD ──────────────────────────────────────────────────────────────
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
  tr.innerHTML = `<td><span style="font-family:var(--mono);font-weight:700">${code}</span></td>
    <td>${name}</td><td>${units}</td><td>${lec}</td><td>${lab}</td><td>${dept}</td>
    <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${dept}')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'${code}')">Delete</button></td>`;
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