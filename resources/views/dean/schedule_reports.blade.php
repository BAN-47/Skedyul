<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Faculty Workload</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dean/schedule_reports.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.dean_sidebar')

  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Schedule Reports</div>
      <div class="topbar-actions">
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        <button class="topbar-btn btn-secondary" onclick="showToast('3 pending approvals')">Notifications</button>
      </div>
    </div>

    <div id="page-reports" class="page active">
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div><div style="font-size:20px;font-weight:800;">Schedule Reports</div></div>
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export All</button>
      </div>

      <div class="tab-bar" id="reports-tabs">
        <button class="tab-btn active" onclick="switchTab('reports-tabs','tab-by-dept',this)">By Department</button>
        <button class="tab-btn" onclick="switchTab('reports-tabs','tab-by-faculty',this)">By Faculty</button>
        <button class="tab-btn" onclick="switchTab('reports-tabs','tab-by-section',this)">By Section</button>
      </div>

      <div id="tab-by-dept" class="tab-panel active">
        <div class="card">
          <table>
            <thead><tr><th>Dept</th><th>Section</th><th>Subject</th><th>Faculty</th><th>Day & Time</th></tr></thead>
            <tbody>
              @forelse ($byDepartment as $sl)
                <tr>
                  <td><span class="badge badge-blue">{{ $sl->section->program->department->dept_code ?? 'N/A' }}</span></td>
                  <td>{{ $sl->section->sec_name ?? 'N/A' }}</td>
                  <td>{{ $sl->subject->subj_code ?? '' }} — {{ $sl->subject->subj_name ?? 'N/A' }}</td>
                  <td>{{ $sl->faculty->usr_name ?? 'Unassigned' }}</td>
                  <td>{{ $sl->sl_day }} {{ $sl->sl_time_start }}–{{ $sl->sl_time_end }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center">No schedule data found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div id="tab-by-faculty" class="tab-panel">
        <div class="card">
          <table>
            <thead><tr><th>Faculty</th><th>Subjects</th><th>Load</th></tr></thead>
            <tbody>
              @forelse ($byFaculty as $f)
                <tr>
                  <td><b>{{ $f['name'] }}</b></td>
                  <td>{{ $f['subjects'] ?: '—' }}</td>
                  <td style="{{ $f['hours'] > 24 ? 'color:var(--red);font-weight:700' : '' }}">{{ $f['hours'] }}h</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center">No faculty found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>

      <div id="tab-by-section" class="tab-panel">
        <div class="card">
          <table>
            <thead><tr><th>Section</th><th>Program</th><th>Year</th></tr></thead>
            <tbody>
              @forelse ($sections as $sec)
                <tr>
                  <td>{{ $sec->sec_name }}</td>
                  <td>{{ $sec->program->prog_code ?? 'N/A' }}</td>
                  <td>{{ $sec->sec_year_level }}</td>
                </tr>
              @empty
                <tr><td colspan="3" class="text-center">No sections found.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- MODAL: EXPORT -->
<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header"><div class="modal-title">Export Report</div><button class="modal-close" onclick="closeModal('modal-export')">✕</button></div>
    <div class="modal-body">
      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Report Type</label>
        <select class="field-select">
          <option>Master Schedule</option>
          <option>Faculty Workload Report</option>
          <option>Faculty Deployment Report</option>
          <option>Department Summary</option>
        </select>
      </div>
      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Department</label>
        <select class="field-select">
          <option value="">All Departments</option>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">Format</label>
        <select class="field-select"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
    </div>
  </div>
</div>

<!-- MODAL: NOTIFY CHAIRS -->
<div class="modal-overlay" id="modal-notify">
  <div class="modal" style="width:520px;">
    <div class="modal-header">
      <div class="modal-title">Send Notification to Chairs</div>
      <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
    </div>
    <div class="modal-body">
      <div style="margin-bottom:16px;">
        <div class="field-label" style="margin-bottom:8px;">Recipients</div>
        <div style="display:flex;flex-direction:column;gap:8px;">
          <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" style="width:15px;height:15px;accent-color:var(--blue);">
            <span style="font-size:13px;font-weight:700;color:var(--text);">All Department Chairs</span>
          </label>
          <div style="display:flex;flex-direction:column;gap:6px;padding-left:12px;">
            @foreach ($chairs as $chair)
              <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
                <input type="checkbox" class="chair-check" value="{{ $chair->usr_id }}" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
                <div style="display:flex;align-items:center;gap:8px;">
                  <div style="width:28px;height:28px;border-radius:50%;background:#{{ substr(md5($chair->usr_id), 0, 6) }};display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">
                    {{ collect(explode(' ', $chair->usr_name))->map(fn($n) => $n[0] ?? '')->implode('') }}
                  </div>
                  <div>
                    <div style="font-size:13px;font-weight:600;color:var(--text);">{{ $chair->usr_name }}</div>
                    <div style="font-size:11px;color:var(--text3);">Chair · {{ $chair->department->dept_code ?? 'N/A' }}</div>
                  </div>
                </div>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Notification Type</label>
        <select class="field-select" id="notif-type">
          <option value="info">General Info</option>
          <option value="reminder">Reminder</option>
          <option value="urgent">Urgent</option>
          <option value="deadline">Deadline Notice</option>
        </select>
      </div>

      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Title</label>
        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
      </div>

      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Message</label>
        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <div>
        <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Recently Sent</div>
        <div id="notif-history" style="display:flex;flex-direction:column;gap:6px;">
          <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No notifications sent yet this session.</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
    </div>
  </div>
</div>

<!-- MODAL: REVIEW (populated dynamically via openReview()) -->
<div class="modal-overlay" id="modal-review">
  <div class="modal" style="width:560px;">
    <div class="modal-header"><div class="modal-title" id="review-title">Schedule Review</div><button class="modal-close" onclick="closeModal('modal-review')">✕</button></div>
    <div class="modal-body" id="review-body">
      <!-- filled by openReview() -->
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-review')">Close</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);" onclick="closeModal('modal-review');showToast('Schedule returned.')">Return to Chair</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); }));
});

function toggleAllChairs(master) {
  document.querySelectorAll('.chair-check').forEach(c => c.checked = master.checked);
}
function syncAllChairs() {
  const checks = document.querySelectorAll('.chair-check');
  document.getElementById('notif-all').checked = Array.from(checks).every(c => c.checked);
}

async function sendNotifToChairs() {
  const title = document.getElementById('notif-title').value.trim();
  const message = document.getElementById('notif-message').value.trim();
  const type = document.getElementById('notif-type').value;
  const selected = Array.from(document.querySelectorAll('.chair-check:checked')).map(c => c.value);

  if (!title) { showToast('Please enter a notification title.'); return; }
  if (!message) { showToast('Please enter a message.'); return; }
  if (selected.length === 0) { showToast('Please select at least one recipient.'); return; }

  try {
    const res = await fetch('{{ route("dean.notify") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
      body: JSON.stringify({ title, message, type, recipients: selected }),
    });

    if (!res.ok) throw new Error('Request failed');

    const typeLabels = { info: 'Info', reminder: 'Reminder', urgent: 'Urgent', deadline: 'Deadline' };
    const typeColors = { info: 'badge-blue', reminder: 'badge-amber', urgent: 'badge-red', deadline: 'badge-purple' };
    const timeStr = new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

    const historyEl = document.getElementById('notif-history');
    const empty = historyEl.querySelector('div[style*="text-align:center"]');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.style.cssText = 'background:var(--grey);border-radius:8px;padding:10px 12px;border:1px solid var(--border);';
    item.innerHTML = `
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
        <div style="display:flex;align-items:center;gap:7px;">
          <span class="badge ${typeColors[type]}">${typeLabels[type]}</span>
          <span style="font-size:13px;font-weight:700;color:var(--text);">${title}</span>
        </div>
        <span style="font-size:11px;color:var(--text3);">Sent ${timeStr}</span>
      </div>
      <div style="font-size:12px;color:var(--text2);margin-bottom:4px;">${message}</div>`;
    historyEl.prepend(item);

    document.getElementById('notif-title').value = '';
    document.getElementById('notif-message').value = '';
    document.getElementById('notif-type').value = 'info';

    showToast('Notification sent to ' + selected.length + ' chair' + (selected.length > 1 ? 's' : '') + '!');
  } catch (e) {
    showToast('Failed to send notification.');
  }
}

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show'); setTimeout(() => t.classList.remove('show'), 3000);
}

function switchTab(barId, panelId, btn) {
  const bar = document.getElementById(barId); if (!bar) return;
  bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  ['tab-by-dept', 'tab-by-faculty', 'tab-by-section'].forEach(p => {
    const el = document.getElementById(p); if (el) { el.classList.remove('active'); el.style.display = 'none'; }
  });
  const target = document.getElementById(panelId);
  if (target) { target.style.display = 'block'; target.classList.add('active'); }
}

// Replaces hardcoded deptData + openDept()
async function openDept(deptId, deptCode, deptName) {
  const res = await fetch(`/dean/reports/department/${deptId}`);
  const d = await res.json();

  const facultyRows = d.faculty.map(f => `
    <tr>
      <td><b>${f.name}</b></td>
      <td>${f.rank}</td>
      <td>${f.employment}</td>
      <td><span style="font-family:var(--mono);font-weight:700;">${f.load}</span></td>
      <td><span class="badge">${f.status}</span></td>
    </tr>`).join('');

  document.getElementById('dept-detail-content').innerHTML = `
    <div style="margin-bottom:20px;">
      <div style="font-size:22px;font-weight:800;">${deptCode}</div>
      <div style="font-size:14px;color:var(--text3);margin-top:2px;">${deptName}</div>
    </div>
    <div class="card">
      <table>
        <thead><tr><th>Name</th><th>Rank</th><th>Employment</th><th>Load</th><th>Status</th></tr></thead>
        <tbody>${facultyRows}</tbody>
      </table>
    </div>`;

  document.getElementById('dept-list-view').style.display = 'none';
  document.getElementById('dept-detail-view').style.display = 'block';
}

function closeDept() {
  document.getElementById('dept-list-view').style.display = 'block';
  document.getElementById('dept-detail-view').style.display = 'none';
}

// Replaces hardcoded conflict modal
async function openReview(sectionId, sectionLabel) {
  const res = await fetch(`/dean/reports/conflicts/${sectionId}`);
  const conflicts = await res.json();

  document.getElementById('review-title').textContent = `Schedule Review — ${sectionLabel}`;

  let html = '';
  if (conflicts.length > 0) {
    html += `<div style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
      <strong>${conflicts.length} Conflict(s) Detected</strong></div>`;
  }

  html += `<table><thead><tr><th>Faculty</th><th>Subjects</th><th>Day & Time</th><th>Issue</th></tr></thead><tbody>`;
  conflicts.forEach(c => {
    html += `<tr>
      <td><b>${c.faculty}</b></td>
      <td>${c.subject_a} / ${c.subject_b}</td>
      <td>${c.day} ${c.time}</td>
      <td><span class="badge badge-red">Conflict</span></td>
    </tr>`;
  });
  if (conflicts.length === 0) {
    html += `<tr><td colspan="4" class="text-center">No conflicts found.</td></tr>`;
  }
  html += `</tbody></table>
    <div class="field-group" style="margin-top:16px;">
      <label class="field-label">Return Note</label>
      <textarea class="field-input" rows="3" placeholder="Notes for the chair..."></textarea>
    </div>`;

  document.getElementById('review-body').innerHTML = html;
  openModal('modal-review');
}
</script>
</body>
</html>