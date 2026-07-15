<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Subject Management</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/subjects.css') }}">
</head>
<style>
  html, body { overflow: hidden; }

  .sidebar-nav {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .sidebar-nav::-webkit-scrollbar { display: none; }

  .page {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .page::-webkit-scrollbar { display: none; }

  .schedule-grid-wrap {
    scrollbar-width: none;
    -ms-overflow-style: none;
  }
  .schedule-grid-wrap::-webkit-scrollbar { display: none; }
</style>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Subject Management</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">2</span>
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

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-size:20px;font-weight:800;color:var(--text);">Subject Management</div>
          <div style="font-size:13px;color:var(--text3);">BSIS Department</div>
        </div>
        <button class="topbar-btn btn-primary" onclick="openModal('modal-add-subject')">+ Add Subject</button>
      </div>

      <div class="card">
        <div class="table-wrap">
          <table>
            <thead>
              <tr><th>Code</th><th>Subject Name</th><th>Units</th><th>Lec</th><th>Lab</th><th>Assigned Faculty</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody id="subjects-table">
              @forelse($subjects as $s)
              <tr>
                <td><span style="font-family:var(--mono);font-weight:700">{{ $s->subj_code }}</span></td>
                <td><b>{{ $s->subj_name }}</b></td>
                <td>{{ $s->subj_lecture_hours + $s->subj_lab_hours }}</td>
                <td>{{ $s->subj_lecture_hours }}</td>
                <td>{{ $s->subj_lab_hours }}</td>
                <td>
                  @if($s->assignedFaculty)
                    {{ $s->assignedFaculty }}
                  @else
                    <span style="color:var(--red);">Unassigned</span>
                  @endif
                </td>
                <td>
                  @if($s->assignedFaculty)
                    <span class="badge badge-green">Assigned</span>
                  @else
                    <span class="badge badge-red">No Faculty</span>
                  @endif
                </td>
                <td>
                  @if($s->assignedFaculty)
                    <button
                      class="topbar-btn btn-secondary"
                      style="padding:4px 10px;font-size:11px;"
                      onclick="openEditSubject('{{ $s->subj_id }}','{{ $s->subj_code }}','{{ addslashes($s->subj_name) }}','{{ $s->subj_lecture_hours }}','{{ $s->subj_lab_hours }}','{{ addslashes($s->assignedFaculty) }}')"
                    >Edit</button>
                  @else
                    <button
                      class="topbar-btn btn-primary"
                      style="padding:4px 10px;font-size:11px;"
                      onclick="openAssignModal('{{ $s->subj_id }}','{{ $s->subj_code }}','{{ addslashes($s->subj_name) }}','{{ $s->subj_lecture_hours + $s->subj_lab_hours }}')"
                    >Assign</button>
                  @endif
                  <form action="{{ route('chair.subject.destroy', $s->subj_id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Deactivate {{ $s->subj_code }}? It will be hidden from active lists but kept for historical records.');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;">Delete</button>
                  </form>
                </td>
              </tr>
              @empty
              <tr><td colspan="8" style="text-align:center;">No subjects found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- ADD SUBJECT MODAL -->
<div class="modal-overlay" id="modal-add-subject">
  <div class="modal" style="width:500px;">
    <form action="{{ route('chair.subject.store') }}" method="POST">
      @csrf
      <div class="modal-header">
        <div class="modal-title">Add New Subject</div>
        <button type="button" class="modal-close" onclick="closeModal('modal-add-subject')">×</button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div class="field-group">
            <label class="field-label">Subject Code</label>
            <input class="field-input" name="subj_code" value="{{ old('subj_code') }}" placeholder="e.g. CC 314">
            @error('subj_code') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
          </div>
          <div class="field-group">
            <label class="field-label">Department</label>
            <select class="field-select" name="subj_dept_id">
              <option value="">-- Select --</option>
              @foreach($departments as $dept)
                <option value="{{ $dept->dept_id }}" @selected(old('subj_dept_id') == $dept->dept_id)>{{ $dept->dept_name }}</option>
              @endforeach
            </select>
            @error('subj_dept_id') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="field-group" style="margin-bottom:16px;">
          <label class="field-label">Program</label>
          <select class="field-select" name="subj_prog_id">
            <option value="">-- Select --</option>
            @foreach($programs as $prog)
              <option value="{{ $prog->prog_id }}" @selected(old('subj_prog_id') == $prog->prog_id)>{{ $prog->prog_name }}</option>
            @endforeach
          </select>
          @error('subj_prog_id') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
        </div>

        <div class="field-group" style="margin-bottom:16px;">
          <label class="field-label">Subject Name</label>
          <input class="field-input" name="subj_name" value="{{ old('subj_name') }}" placeholder="e.g. Web Systems and Technologies">
          @error('subj_name') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
        </div>

        <div class="form-row three">
          <div class="field-group">
            <label class="field-label">Lecture Hrs</label>
            <input class="field-input" name="subj_lecture_hours" type="number" min="0" max="6" value="{{ old('subj_lecture_hours', 0) }}">
            @error('subj_lecture_hours') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
          </div>
          <div class="field-group">
            <label class="field-label">Lab Hrs</label>
            <input class="field-input" name="subj_lab_hours" type="number" min="0" max="6" value="{{ old('subj_lab_hours', 0) }}">
            @error('subj_lab_hours') <div style="color:var(--red);font-size:12px;">{{ $message }}</div> @enderror
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="topbar-btn btn-secondary" onclick="closeModal('modal-add-subject')">Cancel</button>
        <button type="submit" class="topbar-btn btn-primary">Add Subject</button>
      </div>
    </form>
  </div>
</div>

<!-- EDIT SUBJECT MODAL -->
<div class="modal-overlay" id="modal-edit-subject">
  <div class="modal" style="width:500px;">
    <form id="edit-subj-form" method="POST">
      @csrf
      @method('PUT')
      <div class="modal-header">
        <div class="modal-title">Edit Subject</div>
        <button type="button" class="modal-close" onclick="closeModal('modal-edit-subject')">×</button>
      </div>
      <div class="modal-body">
        <div class="form-row">
          <div class="field-group">
            <label class="field-label">Subject Code</label>
            <input class="field-input" id="edit-subj-code" name="subj_code">
          </div>
          <div class="field-group">
            <label class="field-label">Department</label>
            <select class="field-select" id="edit-subj-dept" name="subj_dept_id">
              @foreach($departments as $dept)
                <option value="{{ $dept->dept_id }}">{{ $dept->dept_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="field-group" style="margin-bottom:16px;">
          <label class="field-label">Subject Name</label>
          <input class="field-input" id="edit-subj-name" name="subj_name">
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
        <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;">
          ⚠️ Editing a subject may affect existing schedule assignments.
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="topbar-btn btn-secondary" onclick="closeModal('modal-edit-subject')">Cancel</button>
        <button type="submit" class="topbar-btn btn-primary">Save Changes</button>
      </div>
    </form>
  </div>
</div>

<!-- QUICK ASSIGN MODAL (for unassigned subjects) -->
<div class="modal-overlay" id="modal-assign">
  <div class="modal" style="width:480px;">
    <form action="{{ route('schedule.store') }}" method="POST">
      @csrf
      <input type="hidden" name="sch_subj_id" id="assign-subj-id" value="">

      <div class="modal-header">
        <div class="modal-title">Assign Faculty to Subject</div>
        <button type="button" class="modal-close" onclick="closeModal('modal-assign')">×</button>
      </div>
      <div class="modal-body">
        <div id="assign-subject-info" style="background:linear-gradient(135deg,#0f172a,#1e3a8a);border-radius:10px;padding:14px 16px;margin-bottom:16px;">
          <div style="font-size:13px;font-weight:700;color:#fff;" id="assign-subj-title"></div>
          <div style="font-size:12px;color:rgba(255,255,255,0.5);margin-top:3px;" id="assign-subj-meta"></div>
        </div>

        <div class="field-group" style="margin-bottom:16px;">
          <label class="field-label">Select Faculty</label>
          <select class="field-select" name="sch_fac_id">
            <option value="">— Choose Faculty —</option>
            {{-- Needs a real $faculty collection with computed load --}}
          </select>
        </div>

        <div class="form-row">
          <div class="field-group">
            <label class="field-label">Day</label>
            <select class="field-select" name="sch_day">
              <option>Monday</option><option>Tuesday</option><option>Wednesday</option>
              <option>Thursday</option><option>Friday</option>
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Time Slot</label>
            <div style="display:flex;gap:8px;">
              <input class="field-input" type="time" name="sch_start_time">
              <input class="field-input" type="time" name="sch_end_time">
            </div>
          </div>
        </div>

        <div class="form-row">
          <div class="field-group">
            <label class="field-label">Room</label>
            <select class="field-select" name="sch_room_id">
              {{-- Needs a real $rooms collection --}}
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Section</label>
            <select class="field-select" name="sch_sec_id">
              @foreach($section as $sec)
                <option value="{{ $sec->sec_id }}">{{ $sec->sec_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="topbar-btn btn-secondary" onclick="closeModal('modal-assign')">Cancel</button>
        <button type="submit" class="topbar-btn btn-primary">Assign Faculty</button>
      </div>
    </form>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];
function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
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
function markRead(el) { el.classList.remove('unread'); updateNotifCount(); }
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

// ── MODALS ─────────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

function openAssignModal(subjId, code, name, units) {
  document.getElementById('assign-subj-id').value = subjId;
  document.getElementById('assign-subj-title').textContent = `${code} — ${name}`;
  document.getElementById('assign-subj-meta').textContent = `${units} units`;
  openModal('modal-assign');
}

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  if (!sel) return;
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].text === value || sel.options[i].value === value) {
      sel.selectedIndex = i; break;
    }
  }
}

// ── ADD SUBJECT ────────────────────────────────────────────────────────────────
function saveAddSubject() {
  const code    = document.getElementById('add-subj-code').value.trim();
  const name    = document.getElementById('add-subj-name').value.trim();
  if (!code || !name) { alert('Please fill in Subject Code and Name.'); return; }
  const units   = document.getElementById('add-subj-units').value || '3';
  const lec     = document.getElementById('add-subj-lec').value || '0';
  const lab     = document.getElementById('add-subj-lab').value || '0';
  const faculty = document.getElementById('add-subj-faculty').value;
  const facultyCell = faculty ? faculty : `<span style="color:var(--red);">Unassigned</span>`;
  const statusBadge = faculty ? '<span class="badge badge-green">Assigned</span>' : '<span class="badge badge-red">No Faculty</span>';
  const actionBtn   = faculty
    ? `<button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${faculty}')">Edit</button>`
    : `<button class="topbar-btn btn-primary" style="padding:4px 10px;font-size:11px;" onclick="openModal('modal-assign')">Assign</button>`;
  const tbody = document.getElementById('subjects-table');
  const tr = document.createElement('tr');
  tr.innerHTML = `<td><span style="font-family:var(--mono);font-weight:700">${code}</span></td>
    <td><b>${name}</b></td><td>${units}</td><td>${lec}</td><td>${lab}</td>
    <td>${facultyCell}</td><td>${statusBadge}</td><td>${actionBtn}</td>`;
  tbody.appendChild(tr);
  ['add-subj-code','add-subj-name','add-subj-units','add-subj-lec','add-subj-lab'].forEach(id => {
    document.getElementById(id).value = '';
  });
  closeModal('modal-add-subject');
  showToast(`Subject "${name}" added successfully!`);
}

// ── EDIT SUBJECT ───────────────────────────────────────────────────────────────
function openEditSubject(id, code, name, units, lec, lab, faculty) {
  document.getElementById('edit-subj-form').action = `/chair/subjects/${id}`;
  document.getElementById('edit-subj-code').value  = code;
  document.getElementById('edit-subj-name').value  = name;
  document.getElementById('edit-subj-lec').value   = lec;
  document.getElementById('edit-subj-lab').value   = lab;
  openModal('modal-edit-subject');
}

// ── DELETE SUBJECT ─────────────────────────────────────────────────────────────
function deleteSubject() {
  const name = document.getElementById('edit-subj-name').value;
  if (!confirm(`Delete subject: ${name}?\nThis cannot be undone.`)) return;
  document.getElementById('delete-subj-form').submit();
}

// ── QUICK ASSIGN ───────────────────────────────────────────────────────────────
function quickAssign() {
  const sel = document.getElementById('quick-assign-faculty');
  const raw = sel.options[sel.selectedIndex].text;
  if (!raw || raw === '— Choose Faculty —') { alert('Please select a faculty member.'); return; }
  const faculty = raw.split(' (')[0];
  closeModal('modal-assign');
  document.querySelectorAll('#subjects-table tr').forEach(row => {
    if (row.textContent.includes('CC 501')) {
      const cells = row.querySelectorAll('td');
      if (cells[5]) cells[5].innerHTML = faculty;
      if (cells[6]) cells[6].innerHTML = '<span class="badge badge-green">Assigned</span>';
      if (cells[7]) cells[7].innerHTML = `<button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditSubject('CC 501','System Integration','3','2','1','${faculty}')">Edit</button>`;
    }
  });
  showToast(`CC 501 assigned to ${faculty}!`);
}
</script>
</body>
</html>