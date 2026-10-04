<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@vite(['resources/css/app.css', 'resources/js/app.js'])
<title>SKEDYUL — Faculty Dashboard</title>

</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">

    @php
      // Flatten $todaySchedule into the shape faculty_header.blade.php expects
      // for its "Today's Schedule" notification feed.
      $scheduleForJs = [];
      $todayName = $today ?? \Carbon\Carbon::now()->format('l');
      foreach (($todaySchedule ?? collect()) as $sch) {
          $scheduleForJs[] = [
              'code'  => $sch->subject->subj_code ?? 'N/A',
              'room'  => $sch->room->room_name ?? 'N/A',
              'day'   => $todayName,
              'start' => \Carbon\Carbon::parse($sch->sch_start_time)->format('H:i'),
              'end'   => \Carbon\Carbon::parse($sch->sch_end_time)->format('H:i'),
          ];
      }
    @endphp

    @include('partials.faculty_header', [
        'title' => 'My Dashboard',
        'scheduleFeed' => $scheduleForJs,
        'announcements' => $announcements ?? null,
    ])

    <!-- FACULTY DASHBOARD PAGE -->
    <div id="page-faculty-dashboard" class="page active">

      <!-- Welcome Banner with rotating quote (Tailwind) -->
      <div class="relative overflow-hidden rounded-2xl mb-6 px-7 py-6 bg-gradient-to-br from-slate-900 via-blue-900 to-[#1a2d5a]">
        <div class="absolute inset-0 pointer-events-none bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:28px_28px]"></div>
        <div class="relative z-10 flex items-start justify-between gap-6">
          <div class="flex-1">
            <div class="text-[11px] font-bold text-white/40 uppercase tracking-[1.5px] mb-1.5">Welcome back, {{ $faculty->full_name }}</div>
            <div class="text-xl leading-snug font-bold text-white mb-2.5 italic" id="fac-quote-text">"The art of teaching is the art of assisting discovery."</div>
            <div class="text-xs text-white/40 font-semibold" id="fac-quote-author">— Mark Van Doren</div>
            <div class="flex items-center gap-2 mt-3.5">
              <button onclick="prevFacQuote()" class="w-7 h-7 rounded-full bg-white/10 border-none text-white cursor-pointer text-[13px]">&#8249;</button>
              <div id="fac-quote-dots" class="flex gap-1.5"></div>
              <button onclick="nextFacQuote()" class="w-7 h-7 rounded-full bg-white/10 border-none text-white cursor-pointer text-[13px]">&#8250;</button>
            </div>
          </div>
          <div class="text-right shrink-0">
            <div class="text-[44px] opacity-10 leading-none mb-2.5">"</div>
            <div class="text-[11px] text-white/30">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Dept</div>
            <div class="text-[11px] text-white/30 mt-0.5">
              @if ($faculty->department)
                AY {{ now()->year }}–{{ now()->year + 1 }}
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- STAT CARDS (Tailwind) -->
      <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="relative bg-white rounded-2xl border border-gray-200 shadow-sm p-4 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-blue-600"></div>
          <div class="text-xs text-gray-500 font-semibold mb-1">Teaching Load</div>
          <div class="text-2xl font-extrabold text-gray-900">{{ $totalHours }}h</div>
          <div class="text-[11px] text-gray-400 mt-0.5">of 30h max</div>
        </div>
        <div class="relative bg-white rounded-2xl border border-gray-200 shadow-sm p-4 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-green-600"></div>
          <div class="text-xs text-gray-500 font-semibold mb-1">My Subjects</div>
          <div class="text-2xl font-extrabold text-gray-900">{{ $mySubjects->count() }}</div>
          <div class="text-[11px] text-gray-400 mt-0.5">This semester</div>
        </div>
        <div class="relative bg-white rounded-2xl border border-gray-200 shadow-sm p-4 overflow-hidden">
          <div class="absolute top-0 left-0 right-0 h-1 bg-amber-600"></div>
          <div class="text-xs text-gray-500 font-semibold mb-1">My Sections</div>
          <div class="text-2xl font-extrabold text-gray-900">{{ $mySections->count() }}</div>
          <div class="text-[11px] text-gray-400 mt-0.5">{{ collect($mySections)->implode(', ') ?: 'None assigned' }}</div>
        </div>
      </div>

      <div class="flex gap-5 items-start flex-wrap">

        <!-- TODAY'S SCHEDULE -->
        <div class="flex-1 min-w-[420px]">
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="mb-4">
              <div class="text-base font-bold text-gray-900">Today's Schedule — {{ $today }}</div>
              <div class="text-xs text-gray-500 mt-0.5">Current Semester</div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-sm border-collapse">
                <thead>
                  <tr>
                    <th class="text-left text-[11px] font-bold uppercase tracking-wide text-gray-500 px-3 py-2 border-b border-gray-200">Time</th>
                    <th class="text-left text-[11px] font-bold uppercase tracking-wide text-gray-500 px-3 py-2 border-b border-gray-200">Subject</th>
                    <th class="text-left text-[11px] font-bold uppercase tracking-wide text-gray-500 px-3 py-2 border-b border-gray-200">Room</th>
                    <th class="text-left text-[11px] font-bold uppercase tracking-wide text-gray-500 px-3 py-2 border-b border-gray-200">Section</th>
                    <th class="text-left text-[11px] font-bold uppercase tracking-wide text-gray-500 px-3 py-2 border-b border-gray-200">Status</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse ($todaySchedule as $sch)
                    <tr class="sched-row border-b border-gray-100" data-start="{{ $sch->sch_start_time }}" data-end="{{ $sch->sch_end_time }}">
                      <td class="px-3 py-2.5 font-mono text-xs text-gray-400 whitespace-nowrap">{{ $sch->sch_start_time }}–{{ $sch->sch_end_time }}</td>
                      <td class="px-3 py-2.5"><b class="text-gray-900">{{ $sch->subject->subj_code ?? '' }} — {{ $sch->subject->subj_name ?? 'N/A' }}</b></td>
                      <td class="px-3 py-2.5 text-gray-700">{{ $sch->room->room_name ?? 'N/A' }}</td>
                      <td class="px-3 py-2.5 text-gray-700">{{ $sch->section->sec_name ?? 'N/A' }}</td>
                      <td class="px-3 py-2.5 sched-status">
                        <span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">—</span>
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="5" class="text-center text-gray-400 text-sm py-8">No classes scheduled today.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- MY SUBJECTS -->
        <div class="w-full sm:w-[280px] shrink-0">
          <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
            <div class="text-base font-bold text-gray-900 mb-4">My Subjects</div>
            @forelse ($mySubjects as $i => $subj)
              @php
                $colors = ['#2563eb', '#d97706', '#16a34a', '#7c3aed', '#0d9488'];
                $wc = $colors[$i % count($colors)];
              @endphp
              <div class="mb-3 last:mb-0">
                <div class="flex items-center justify-between mb-1">
                  <div class="text-xs font-semibold text-gray-700">{{ $subj->subj_code }} — {{ $subj->subj_name }}</div>
                  <div class="text-xs font-bold" style="color:{{ $wc }};">{{ $subj->subj_units ?? '' }}u</div>
                </div>
                <div class="h-1.5 bg-gray-100 rounded-full overflow-hidden">
                  <div class="h-full rounded-full" style="width:100%;background:{{ $wc }};"></div>
                </div>
              </div>
            @empty
              <div class="text-sm text-gray-400 text-center py-3">No subjects assigned.</div>
            @endforelse
            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between text-xs">
              <div>
                <div class="text-gray-400">Total Load</div>
                <div class="font-extrabold text-lg text-gray-900">{{ $totalHours }}h</div>
              </div>
              <div class="text-right">
                <div class="text-gray-400">Max Load</div>
                <div class="font-extrabold text-lg text-green-600">30h</div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- ASSIGN SUBJECT MODAL (Tailwind) -->
<div class="modal-overlay fixed inset-0 z-[200] hidden items-center justify-center bg-black/40 backdrop-blur-sm [&.open]:flex" id="modal-assign">
  <div class="bg-white rounded-2xl shadow-2xl w-full max-w-[560px] max-h-[90vh] overflow-y-auto p-6">
    <div class="flex items-center justify-between mb-5">
      <div class="text-lg font-bold text-gray-900">Assign Subject to Schedule</div>
      <button class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-500 flex items-center justify-center text-sm" onclick="closeModal('modal-assign')">✕</button>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Faculty Member</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          <option>{{ $faculty->full_name }}</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Subject</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          @foreach ($mySubjects as $subj)
            <option>{{ $subj->subj_code }} — {{ $subj->subj_name }}</option>
          @endforeach
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-3 mb-3">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Section</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          @foreach ($mySections as $sec)
            <option>{{ $sec }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Room</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          <option>Room 301</option><option>Room 302</option><option>Lab 1</option><option>Lab 2</option>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-4">
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Day</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          <option>Monday</option><option>Tuesday</option><option>Wednesday</option><option>Thursday</option><option>Friday</option><option>Saturday</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">Start Time</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          <option>7:00 AM</option><option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option>
        </select>
      </div>
      <div>
        <label class="block text-xs font-semibold text-gray-500 mb-1">End Time</label>
        <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
          <option>8:30 AM</option><option>10:00 AM</option><option>11:30 AM</option><option>1:00 PM</option><option>2:30 PM</option><option>4:00 PM</option><option>5:30 PM</option>
        </select>
      </div>
    </div>

    <div class="bg-amber-50 border border-amber-300 rounded-lg px-3.5 py-2.5 text-xs text-amber-800">
      ⚡ System will automatically check for conflicts before saving.
    </div>

    <div class="flex justify-end gap-2 mt-5">
      <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="closeModal('modal-assign')">Cancel</button>
      <button class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700" onclick="closeModal('modal-assign');showToast('Subject assigned! No conflicts detected ✓')">Check & Assign</button>
    </div>
  </div>
</div>

<!-- TOAST (Tailwind) -->
<div class="toast fixed bottom-6 right-6 z-[300] bg-gray-900 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg opacity-0 translate-y-2 pointer-events-none transition-all duration-300 [&.show]:opacity-100 [&.show]:translate-y-0 [&.show]:pointer-events-auto" id="toast">✅ <span id="toast-msg"></span></div>

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
    tr.innerHTML = `<td><span class="font-mono font-bold text-xs text-gray-900">${code}</span></td>
      <td>${name}</td><td>${units}</td><td>${lec}</td><td>${lab}</td><td>${dept}</td>
      <td>
        <button class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="openEditSubject('${code}','${name}','${units}','${lec}','${lab}','${dept}')">Edit</button>
        <button class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-red-100 text-red-600 hover:bg-red-200 ml-1" onclick="deleteTableRow(this,'${code}')">Delete</button>
      </td>`;
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
      <td><span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full bg-green-100 text-green-600">Available</span></td>
      <td><button class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200"
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
  const colors = { 'Available':'bg-green-100 text-green-600', 'In Use':'bg-amber-100 text-amber-600', 'Under Maintenance':'bg-red-100 text-red-600' };
  document.getElementById('vr-status-badge').innerHTML = `<span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full ${colors[status]||'bg-gray-100 text-gray-500'}">${status}</span>`;
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
  const statusColors = { Active:'bg-green-100 text-green-600', Pending:'bg-amber-100 text-amber-600', Inactive:'bg-red-100 text-red-600' };
  document.getElementById('profile-status-badge').innerHTML = `<span class="inline-block text-[11px] font-bold px-2 py-0.5 rounded-full ${statusColors[status]||'bg-gray-100 text-gray-500'}">${status}</span>`;
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
      btn.className = 'px-2.5 py-1.5 rounded-lg text-[13px] font-semibold min-w-[36px] ' +
        (p === currentPage ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200');
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
      dot.className = 'w-1.5 h-1.5 rounded-full cursor-pointer transition-colors duration-300 ' +
        (i === facQuoteIndex ? 'bg-white/90' : 'bg-white/25');
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

// ── LIVE CLASS COUNTDOWN (Today's Schedule table) ───────────────────────
function parseTimeToday(timeStr) {
  // Expects "HH:MM" or "HH:MM:SS" (24hr, matches your sch_start_time/sch_end_time format)
  const [h, m, s] = timeStr.split(':').map(Number);
  const d = new Date();
  d.setHours(h, m, s || 0, 0);
  return d;
}

function updateScheduleStatuses() {
  const now = new Date();
  const badgeBase = 'inline-block text-[11px] font-bold px-2 py-0.5 rounded-full';

  document.querySelectorAll('.sched-row').forEach(row => {
    const start = parseTimeToday(row.dataset.start);
    const end = parseTimeToday(row.dataset.end);
    const statusCell = row.querySelector('.sched-status');
    if (!statusCell) return;

    if (now < start) {
      const mins = Math.ceil((start - now) / 60000);
      statusCell.innerHTML = `<span class="${badgeBase} bg-blue-100 text-blue-600">Starts in ${mins}m</span>`;
    } else if (now >= start && now <= end) {
      const remaining = end - now;
      const mins = Math.floor(remaining / 60000);
      const secs = Math.floor((remaining % 60000) / 1000);
      const total = end - start;
      const pct = Math.min(100, Math.max(0, ((now - start) / total) * 100));
      const urgent = mins < 5;

      statusCell.innerHTML = `
        <div class="min-w-[110px]">
          <span class="${badgeBase} ${urgent ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'}">${mins}m ${secs}s left</span>
          <div class="h-1 bg-gray-200 rounded mt-1 overflow-hidden">
            <div class="h-full transition-[width] duration-1000 ease-linear ${urgent ? 'bg-red-500' : 'bg-green-500'}" style="width:${pct}%;"></div>
          </div>
        </div>`;
    } else {
      statusCell.innerHTML = `<span class="${badgeBase} bg-gray-100 text-gray-500">Ended</span>`;
    }
  });
}

// ── INIT ──────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  initFacQuotes();
  updateScheduleStatuses();
  setInterval(updateScheduleStatuses, 1000);
});
</script>
</body>
</html>