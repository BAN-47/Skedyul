<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Faculty Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/faculty/faculty_dashboard.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">My Dashboard</div>
    </div>

    <!-- FACULTY DASHBOARD PAGE -->
    <div id="page-faculty-dashboard" class="page active">
      <!-- Welcome Banner with rotating quote -->
      <div style="background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 60%,#1a2d5a 100%);border-radius:16px;padding:24px 28px;margin-bottom:24px;position:relative;overflow:hidden;">
        <div style="position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,0.03) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,0.03) 1px,transparent 1px);background-size:28px 28px;pointer-events:none;"></div>
        <div style="position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:24px;">
          <div style="flex:1;">
            <div style="font-size:11px;font-weight:700;color:rgba(255,255,255,0.4);text-transform:uppercase;letter-spacing:1.5px;margin-bottom:6px;">Welcome back, {{ $faculty->full_name }}</div>
            <div style="font-size:20px;line-height:1.35;font-weight:700;color:#fff;margin-bottom:10px;font-style:italic;" id="fac-quote-text">"The art of teaching is the art of assisting discovery."</div>
            <div style="font-size:12px;color:rgba(255,255,255,0.4);font-weight:600;" id="fac-quote-author">— Mark Van Doren</div>
            <div style="display:flex;align-items:center;gap:8px;margin-top:14px;">
              <button onclick="prevFacQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:#fff;cursor:pointer;font-size:13px;">&#8249;</button>
              <div id="fac-quote-dots" style="display:flex;gap:5px;"></div>
              <button onclick="nextFacQuote()" style="width:28px;height:28px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:#fff;cursor:pointer;font-size:13px;">&#8250;</button>
            </div>
          </div>
          <div style="text-align:right;flex-shrink:0;">
            <div style="font-size:44px;opacity:0.12;line-height:1;margin-bottom:10px;">"</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.3);">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Dept</div>
            <div style="font-size:11px;color:rgba(255,255,255,0.3);margin-top:2px;">
              @if ($faculty->department)
                AY {{ now()->year }}–{{ now()->year + 1 }}
              @endif
            </div>
          </div>
        </div>
      </div>

      <div class="stat-grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:24px;">
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Teaching Load</div><div class="stat-value">{{ $totalHours }}h</div><div class="stat-sub">of 30h max</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">My Subjects</div><div class="stat-value">{{ $mySubjects->count() }}</div><div class="stat-sub">This semester</div></div>
        <div class="stat-card" style="--accent:#d97706"><div class="stat-label">My Sections</div><div class="stat-value">{{ $mySections->count() }}</div><div class="stat-sub">{{ $mySections->implode(', ') ?: 'None assigned' }}</div></div>
      </div>

      <div class="row">
        <div style="flex:1;">
          <div class="card">
            <div class="card-header"><div><div class="card-title">Today's Schedule — {{ $today }}</div><div class="card-sub">Current Semester</div></div></div>
            <div class="table-wrap"><table>
              <tr><th>Time</th><th>Subject</th><th>Room</th><th>Section</th><th>Status</th></tr>
              @forelse ($todaySchedule as $sch)
                <tr class="sched-row" data-start="{{ $sch->sch_start_time }}" data-end="{{ $sch->sch_end_time }}">
                  <td style="font-family:var(--mono);font-size:12px;color:var(--text3);">{{ $sch->sch_start_time }}–{{ $sch->sch_end_time }}</td>
                  <td><b>{{ $sch->subject->subj_code ?? '' }} — {{ $sch->subject->subj_name ?? 'N/A' }}</b></td>
                  <td>{{ $sch->room->room_name ?? 'N/A' }}</td>
                  <td>{{ $sch->section->sec_name ?? 'N/A' }}</td>
                  <td class="sched-status">
                    <span class="badge badge-grey">—</span>
                  </td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center" style="text-align: center;">No classes scheduled today.</td></tr>
              @endforelse
            </table></div>
          </div>
        </div>
        <div style="width:280px;flex-shrink:0;">
          <div class="card">
            <div class="card-header"><div class="card-title">My Subjects</div></div>
            @forelse ($mySubjects as $i => $subj)
              @php $colors = ['var(--blue)', 'var(--amber)', 'var(--green)', 'var(--purple)', 'var(--teal)']; @endphp
              <div class="workload-item">
                <div class="workload-header">
                  <div class="workload-name">{{ $subj->subj_code }} — {{ $subj->subj_name }}</div>
                  <div class="workload-val" style="color:{{ $colors[$i % count($colors)] }};">{{ $subj->subj_units ?? '' }}u</div>
                </div>
                <div class="workload-bar"><div class="workload-fill" style="width:100%;background:{{ $colors[$i % count($colors)] }};"></div></div>
              </div>
            @empty
              <div style="font-size:13px;color:var(--text3);text-align:center;padding:12px;">No subjects assigned.</div>
            @endforelse
            <div style="margin-top:12px;padding-top:12px;border-top:1px solid var(--border);display:flex;justify-content:space-between;font-size:12px;">
              <div><div style="color:var(--text3);">Total Load</div><div style="font-weight:800;font-size:18px;color:var(--text);">{{ $totalHours }}h</div></div>
              <div style="text-align:right;"><div style="color:var(--text3);">Max Load</div><div style="font-weight:800;font-size:18px;color:var(--green);">30h</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- MODALS (unchanged structure — kept as-is, static UI shells) -->
<div class="modal-overlay" id="modal-assign">
  <div class="modal">
    <div class="modal-header">
      <div class="modal-title">Assign Subject to Schedule</div>
      <button class="modal-close" onclick="closeModal('modal-assign')">✕</button>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Faculty Member</label><select class="field-select"><option>{{ $faculty->full_name }}</option></select></div>
      <div class="field-group"><label class="field-label">Subject</label>
        <select class="field-select">
          @foreach ($mySubjects as $subj)
            <option>{{ $subj->subj_code }} — {{ $subj->subj_name }}</option>
          @endforeach
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="field-group"><label class="field-label">Section</label>
        <select class="field-select">
          @foreach ($mySections as $sec)
            <option>{{ $sec }}</option>
          @endforeach
        </select>
      </div>
      <div class="field-group"><label class="field-label">Room</label><select class="field-select"><option>Room 301</option><option>Room 302</option><option>Lab 1</option><option>Lab 2</option></select></div>
    </div>
    <div class="form-row three">
      <div class="field-group"><label class="field-label">Day</label><select class="field-select"><option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option></select></div>
      <div class="field-group"><label class="field-label">Start Time</label><select class="field-select"><option>7:00 AM</option><option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option></select></div>
      <div class="field-group"><label class="field-label">End Time</label><select class="field-select"><option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option><option>5:30 PM</option></select></div>
    </div>
    <div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;">
      ⚡ System will automatically check for conflicts before saving.
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-assign')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-assign');showToast('Subject assigned! No conflicts detected ✓')">Check & Assign</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── SUBJECTS ───────────────────────────────────────────────────────────────
function openEditSubject(code, name, units, lec, lab, dept) {
  document.getElementById('edit-subj-code').value  = code;
  document.getElementById('edit-subj-name').value  = name;
  document.getElementById('edit-subj-units').value = units;
  document.getElementById('edit-subj-lec').value   = lec;
  document.getElementById('edit-subj-lab').value   = lab;
  setSelectValue('edit-subj-dept', dept);
  document.getElementById('edit-subj-save-btn').onclick = () => {
    closeModal('modal-edit-subject');
    showToast('Subject "' + document.getElementById('edit-subj-name').value + '" updated!');
  };
  openModal('modal-edit-subject');
}

function saveAddSubject() {
  const code = document.getElementById('add-subj-code').value.trim();
  const name = document.getElementById('add-subj-name').value.trim();
  if (!code || !name) { alert('Please fill in Subject Code and Name.'); return; }
  const units = document.getElementById('add-subj-units').value || '3';
  const lec   = document.getElementById('add-subj-lec').value || '0';
  const lab   = document.getElementById('add-subj-lab').value || '0';
  const dept  = document.getElementById('add-subj-dept').value;
  const tbody = document.querySelector('#subjects-table');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><span style="font-family:var(--mono);font-weight:700">${code}</span></td>
      <td>${name}</td><td>${units}</td><td>${lec}</td><td>${lab}</td><td>${dept}</td>
      <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${dept}')">Edit</button><button class="topbar-btn" style="padding:4px 10px;font-size:11px;background:var(--red-light);color:var(--red);margin-left:4px;" onclick="deleteTableRow(this,'${code}')">Delete</button></td>`;
    tbody.appendChild(tr);
  }
  ['add-subj-code','add-subj-name','add-subj-units','add-subj-lec','add-subj-lab','add-subj-desc'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-subject');
  showToast('Subject "' + name + '" added successfully!');
}

// ── ROOMS ──────────────────────────────────────────────────────────────────
function saveAddRoom() {
  const name = document.getElementById('add-room-name').value.trim();
  if (!name) { alert('Please enter a room name.'); return; }
  const type       = document.getElementById('add-room-type').value;
  const capacity   = document.getElementById('add-room-capacity').value || '—';
  const location   = document.getElementById('add-room-location').value || '—';
  const facilities = document.getElementById('add-room-facilities').value || '—';
  const tbody = document.querySelector('#rooms-table');
  if (tbody) {
    const tr = document.createElement('tr');
    tr.innerHTML = `<td><b>${name}</b></td><td>${type}</td><td>${capacity}</td>
      <td><span class="badge badge-green">Available</span></td>
      <td><button class="topbar-btn btn-secondary" style="padding:4px 10px;font-size:11px;"
        onclick="openViewRoom('${name}','${type}','${capacity}','Available','—','—','${location}','${facilities}')">View</button></td>`;
    tbody.appendChild(tr);
  }
  ['add-room-name','add-room-capacity','add-room-location','add-room-facilities'].forEach(id => document.getElementById(id).value = '');
  closeModal('modal-add-room');
  showToast('Room "' + name + '" added successfully!');
}

function openViewRoom(name, type, capacity, status, assignment, faculty, location, facilities) {
  document.getElementById('vr-name').textContent     = name;
  document.getElementById('vr-type').textContent     = type;
  document.getElementById('vr-capacity').textContent = capacity;
  document.getElementById('vr-location').textContent = location;
  document.getElementById('vr-assignment').textContent = assignment;
  document.getElementById('vr-faculty').textContent  = faculty !== '—' ? 'Faculty: ' + faculty : '';
  document.getElementById('vr-facilities').textContent = facilities;
  const colors = { 'Available':'badge-green', 'In Use':'badge-amber', 'Under Maintenance':'badge-red' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="badge ${colors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-view-room');
}

function openEditRoom(name, type, capacity, status, location, facilities) {
  document.getElementById('edit-room-header').textContent = name;
  document.getElementById('edit-room-name').value       = name;
  document.getElementById('edit-room-capacity').value   = capacity;
  document.getElementById('edit-room-location').value   = location;
  document.getElementById('edit-room-facilities').value = facilities;
  setSelectValue('edit-room-type', type);
  setSelectValue('edit-room-status', status);
  document.getElementById('edit-room-save-btn').onclick = () => {
    closeModal('modal-edit-room');
    showToast('Room "' + document.getElementById('edit-room-name').value + '" updated successfully!');
  };
  openModal('modal-edit-room');
}

// ── USERS ──────────────────────────────────────────────────────────────────
function openEditUser(name, role, dept, email, employment, status, avatar, color, office, contact, about) {
  document.getElementById('edit-avatar').textContent = avatar;
  document.getElementById('edit-avatar').style.background = color;
  document.getElementById('edit-avatar-name').textContent = name;
  document.getElementById('edit-avatar-role').textContent = role + ' · ' + dept;
  document.getElementById('edit-name').value = name;
  document.getElementById('edit-email').value = email;
  document.getElementById('edit-office').value = office;
  document.getElementById('edit-contact').value = contact;
  document.getElementById('edit-about').value = about;
  setSelectValue('edit-role', role);
  setSelectValue('edit-dept', dept);
  setSelectValue('edit-employment', employment);
  setSelectValue('edit-status', status);
  document.getElementById('edit-save-btn').onclick = () => {
    closeModal('modal-edit-user');
    showToast('Changes saved for ' + document.getElementById('edit-name').value + ' ✓');
  };
  openModal('modal-edit-user');
}

function setSelectValue(id, value) {
  const sel = document.getElementById(id);
  for (let i = 0; i < sel.options.length; i++) {
    if (sel.options[i].value === value || sel.options[i].text === value) {
      sel.selectedIndex = i; break;
    }
  }
}

function openUserProfile(name, role, dept, email, employment, status, avatar, color, personal, about, office, contact) {
  document.getElementById('profile-avatar').textContent = avatar;
  document.getElementById('profile-avatar').style.background = color;
  document.getElementById('profile-name').textContent = name;
  document.getElementById('profile-role').textContent = role;
  document.getElementById('profile-dept').textContent = dept;
  document.getElementById('profile-email').textContent = email;
  document.getElementById('profile-employment').textContent = employment;
  document.getElementById('profile-personal').textContent = personal;
  document.getElementById('profile-about').textContent = about;
  document.getElementById('profile-office').textContent = office;
  document.getElementById('profile-contact').textContent = contact;
  const statusColors = { Active:'badge-green', Pending:'badge-amber', Inactive:'badge-red' };
  document.getElementById('profile-status-badge').innerHTML = `<span class="badge ${statusColors[status]||'badge-grey'}">${status}</span>`;
  openModal('modal-user-profile');
}

// ── PAGINATION ─────────────────────────────────────────────────────────────
let currentPage = 1;
const rowsPerPage = 10;
let filteredRows = [];

function initPagination() {
  filteredRows = Array.from(document.querySelectorAll('#users-table .user-row'));
  renderPage();
}
function renderPage() {
  const total = filteredRows.length;
  const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
  if (currentPage > totalPages) currentPage = totalPages;
  filteredRows.forEach((row, i) => {
    const start = (currentPage - 1) * rowsPerPage;
    row.style.display = (i >= start && i < start + rowsPerPage) ? '' : 'none';
  });
  const start = Math.min((currentPage - 1) * rowsPerPage + 1, total);
  const end = Math.min(currentPage * rowsPerPage, total);
  const info = document.getElementById('page-info');
  if (info) info.textContent = total === 0 ? 'No results found' : `Showing ${start}–${end} of ${total} users`;
  const prevBtn = document.getElementById('btn-prev');
  const nextBtn = document.getElementById('btn-next');
  if (prevBtn) { prevBtn.disabled = currentPage === 1; prevBtn.style.opacity = currentPage === 1 ? '0.4' : '1'; }
  if (nextBtn) { nextBtn.disabled = currentPage === totalPages; nextBtn.style.opacity = currentPage === totalPages ? '0.4' : '1'; }
  const container = document.getElementById('page-numbers');
  if (container) {
    container.innerHTML = '';
    for (let p = 1; p <= totalPages; p++) {
      const btn = document.createElement('button');
      btn.textContent = p;
      btn.className = 'topbar-btn ' + (p === currentPage ? 'btn-primary' : 'btn-secondary');
      btn.style.cssText = 'padding:6px 11px;font-size:13px;min-width:36px;';
      btn.onclick = () => { currentPage = p; renderPage(); };
      container.appendChild(btn);
    }
  }
}
function changePage(dir) {
  const total = filteredRows.length;
  const totalPages = Math.max(1, Math.ceil(total / rowsPerPage));
  currentPage = Math.max(1, Math.min(currentPage + dir, totalPages));
  renderPage();
}
function filterUsers(query) {
  const q = query.toLowerCase();
  const all = Array.from(document.querySelectorAll('#users-table .user-row'));
  filteredRows = q ? all.filter(row => row.textContent.toLowerCase().includes(q)) : all;
  all.forEach(r => r.style.display = 'none');
  currentPage = 1;
  renderPage();
}

// ── NOTIFICATIONS (bell dropdown) ───────────────────────────────────────────
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

// ── DAY TABS (faculty schedule page) ────────────────────────────────────────
function showWebDay(day, el) {
  document.querySelectorAll('.web-day-panel').forEach(p => p.style.display = 'none');
  document.getElementById('wday-' + day).style.display = 'block';
  const card = el.closest('.card');
  if (card) card.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
}

function openWebSubjectDetail(code, name, units, lec, lab, dept, room, section, schedule, color) {
  document.getElementById('wsd-header').style.background = 'linear-gradient(135deg,' + color + ',#0f172a)';
  document.getElementById('wsd-code').textContent = code + ' · ' + dept;
  document.getElementById('wsd-name').textContent = name;
  document.getElementById('wsd-units').textContent = units + 'u';
  document.getElementById('wsd-hours').textContent = lec + 'h Lecture' + (parseInt(lab) > 0 ? ' · ' + lab + 'h Lab' : '');
  document.getElementById('wsd-room').textContent = room;
  document.getElementById('wsd-section').textContent = section;
  document.getElementById('wsd-schedule').textContent = schedule;
  openModal('modal-web-subject-detail');
}

function startWebRoomCountdown() {
  const end = new Date(); end.setHours(8, 30, 0, 0);
  const start = new Date(); start.setHours(7, 0, 0, 0);
  function tick() {
    const cur = new Date();
    const remaining = end - cur;
    const total = end - start;
    const el = document.getElementById('web-room-countdown');
    const prog = document.getElementById('web-room-progress');
    if (!el) return;
    if (remaining <= 0) { el.textContent = '00:00'; el.style.color = '#f87171'; if (prog) prog.style.width = '100%'; return; }
    const mins = Math.floor(remaining / 60000);
    const secs = Math.floor((remaining % 60000) / 1000);
    el.textContent = String(mins).padStart(2,'0') + ':' + String(secs).padStart(2,'0');
    el.style.color = mins < 5 ? '#f87171' : mins < 15 ? '#fbbf24' : '#4ade80';
    if (prog) prog.style.width = Math.min(100, Math.max(0, ((total - remaining) / total) * 100)) + '%';
  }
  tick(); setInterval(tick, 1000);
}

// ── FACULTY SETTINGS ─────────────────────────────────────────────────────
function showFacSettingsSection(section, el) {
  ['profile','security','notifications'].forEach(s => {
    const elem = document.getElementById('fac-settings-' + s);
    if (elem) elem.style.display = 'none';
  });
  const target = document.getElementById('fac-settings-' + section);
  if (target) target.style.display = 'block';
  document.querySelectorAll('#page-faculty-settings .settings-nav-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}
function facPreviewPic(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const p = document.getElementById('fac-pic-preview');
    p.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    showToast('Profile photo updated!');
  };
  reader.readAsDataURL(input.files[0]);
}
function facResetPic() {
  const p = document.getElementById('fac-pic-preview');
  if (p) p.innerHTML = '{{ strtoupper(substr($faculty->fac_first_name,0,1) . substr($faculty->fac_last_name,0,1)) }}';
  document.getElementById('fac-pic-upload').value = '';
  showToast('Profile photo removed.');
}

// ── FACULTY QUOTES ───────────────────────────────────────────────────────
const FAC_QUOTES = [
  { text: '"The art of teaching is the art of assisting discovery."', author: '— Mark Van Doren' },
  { text: '"A good teacher can inspire hope, ignite the imagination, and instill a love of learning."', author: '— Brad Henry' },
  { text: '"Teaching is the one profession that creates all other professions."', author: '— Unknown' },
  { text: '"The mediocre teacher tells. The good teacher explains. The great teacher inspires."', author: '— William Arthur Ward' },
  { text: '"To teach is to touch a life forever."', author: '— Unknown' },
  { text: '"Education is not preparation for life; education is life itself."', author: '— John Dewey' },
];
let facQuoteIndex = 0;
let facQuoteTimer = null;

function renderFacQuote() {
  const q = FAC_QUOTES[facQuoteIndex];
  const t = document.getElementById('fac-quote-text');
  const a = document.getElementById('fac-quote-author');
  const d = document.getElementById('fac-quote-dots');
  if (!t) return;
  t.style.opacity = '0'; a.style.opacity = '0';
  setTimeout(() => {
    t.textContent = q.text; a.textContent = q.author;
    t.style.transition = 'opacity 0.5s'; a.style.transition = 'opacity 0.5s';
    t.style.opacity = '1'; a.style.opacity = '1';
  }, 300);
  if (d) {
    d.innerHTML = '';
    FAC_QUOTES.forEach((_, i) => {
      const dot = document.createElement('div');
      dot.style.cssText = `width:6px;height:6px;border-radius:50%;background:${i===facQuoteIndex?'rgba(255,255,255,0.9)':'rgba(255,255,255,0.25)'};cursor:pointer;transition:background 0.3s;`;
      dot.onclick = () => { facQuoteIndex = i; renderFacQuote(); resetFacQuoteTimer(); };
      d.appendChild(dot);
    });
  }
}
function nextFacQuote() { facQuoteIndex = (facQuoteIndex + 1) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function prevFacQuote() { facQuoteIndex = (facQuoteIndex - 1 + FAC_QUOTES.length) % FAC_QUOTES.length; renderFacQuote(); resetFacQuoteTimer(); }
function resetFacQuoteTimer() { clearInterval(facQuoteTimer); facQuoteTimer = setInterval(nextFacQuote, 6000); }
function initFacQuotes() { facQuoteIndex = Math.floor(Math.random() * FAC_QUOTES.length); renderFacQuote(); resetFacQuoteTimer(); }

// ── DELETE FUNCTIONS ─────────────────────────────────────────────────────
function deleteCurrentUser(modalId) {
  const name = document.getElementById('edit-name')
    ? document.getElementById('edit-name').value
    : document.getElementById('profile-name').textContent;
  if (!confirm('Delete user: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  const rows = document.querySelectorAll('#users-table .user-row, #users-table tr');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => { row.remove(); initPagination(); }, 300);
    }
  });
  showToast(name + ' deleted successfully.');
}
function deleteCurrentRoom(modalId) {
  const name = document.getElementById('vr-name') ? document.getElementById('vr-name').textContent
    : (document.getElementById('edit-room-name') ? document.getElementById('edit-room-name').value : 'Room');
  if (!confirm('Delete room: ' + name + '?\nThis action cannot be undone.')) return;
  closeModal(modalId);
  const rows = document.querySelectorAll('#rooms-table tr');
  rows.forEach(row => {
    if (row.textContent.includes(name)) {
      row.style.transition = 'opacity 0.3s';
      row.style.opacity = '0';
      setTimeout(() => row.remove(), 300);
    }
  });
  showToast(name + ' deleted successfully.');
}
function deleteTableRow(btn, name) {
  if (!confirm('Delete: ' + name + '?\nThis action cannot be undone.')) return;
  const row = btn.closest('tr');
  if (row) {
    row.style.transition = 'opacity 0.3s';
    row.style.opacity = '0';
    setTimeout(() => {
      row.remove();
      if (document.getElementById('users-table')) initPagination();
    }, 300);
  }
  showToast(name + ' deleted successfully.');
}

// ── MODALS ────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ─────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── TABS ──────────────────────────────────────────────────────────────────
document.querySelectorAll('.tab-bar').forEach(bar => {
  bar.querySelectorAll('.tab-btn').forEach(btn => {
    btn.onclick = () => { bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active')); btn.classList.add('active'); };
  });
});

// ── INIT ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initFacQuotes();
});

// ── LIVE CLASS COUNTDOWN ────────────────────────────────────────────────
function parseTimeToday(timeStr) {
  // Expects "HH:MM" or "HH:MM:SS" (24hr, matches your sch_start_time/sch_end_time format)
  const [h, m, s] = timeStr.split(':').map(Number);
  const d = new Date();
  d.setHours(h, m, s || 0, 0);
  return d;
}

function updateScheduleStatuses() {
  const now = new Date();
  document.querySelectorAll('.sched-row').forEach(row => {
    const start = parseTimeToday(row.dataset.start);
    const end = parseTimeToday(row.dataset.end);
    const statusCell = row.querySelector('.sched-status');
    if (!statusCell) return;

    if (now < start) {
      const mins = Math.ceil((start - now) / 60000);
      statusCell.innerHTML = `<span class="badge badge-blue">Starts in ${mins}m</span>`;
    } else if (now >= start && now <= end) {
      const remaining = end - now;
      const mins = Math.floor(remaining / 60000);
      const secs = Math.floor((remaining % 60000) / 1000);
      const total = end - start;
      const pct = Math.min(100, Math.max(0, ((now - start) / total) * 100));
      const urgent = mins < 5;

      statusCell.innerHTML = `
        <div style="min-width:110px;">
          <span class="badge ${urgent ? 'badge-red' : 'badge-green'}">${mins}m ${secs}s left</span>
          <div style="height:4px;background:var(--grey2);border-radius:4px;margin-top:4px;overflow:hidden;">
            <div style="height:100%;width:${pct}%;background:${urgent ? 'var(--red)' : 'var(--green)'};transition:width 1s linear;"></div>
          </div>
        </div>`;
    } else {
      statusCell.innerHTML = `<span class="badge badge-grey">Ended</span>`;
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initFacQuotes();
  updateScheduleStatuses();
  setInterval(updateScheduleStatuses, 1000);
});
</script>
</body>
</html>