<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Workload</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dean/faculty_workload.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.dean_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title">Faculty Workload</div>
      <div class="topbar-actions">
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export PDF</button>
        <button class="topbar-btn btn-secondary" onclick="showToast('No new notifications.')">Notifications</button>
      </div>
    </div>

    <div id="page-faculty" class="page active">

      <!-- Page header -->
      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
        <div>
          <div style="font-size:20px;font-weight:800;color:var(--text);">Faculty Workload Overview</div>
          <div style="font-size:13px;color:var(--text3);margin-top:3px;">AY 2025–26 · 1st Semester</div>
        </div>
      </div>

      <!-- Stat cards -->
      <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
        <div class="stat-card" style="--accent:#2563eb"><div class="stat-label">Total Faculty</div><div class="stat-value">6</div><div class="stat-sub">Across all departments</div></div>
        <div class="stat-card" style="--accent:#16a34a"><div class="stat-label">OK Load</div><div class="stat-value">3</div><div class="stat-sub">Within safe range</div></div>
        <div class="stat-card" style="--accent:#d97706"><div class="stat-label">Near Max</div><div class="stat-value">1</div><div class="stat-sub">3h or less remaining</div></div>
        <div class="stat-card" style="--accent:#dc2626"><div class="stat-label">Overloaded</div><div class="stat-value">1</div><div class="stat-sub">Exceeds 30h limit</div></div>
      </div>

      <!-- Overload alert -->
      <div class="conflict-alert" style="margin-bottom:20px;">
        <div class="conflict-alert-text">
          <strong>Overload Detected — Carlo Mendoza</strong>
          Currently assigned 31h/30h maximum. The Department Chair should reassign one subject to resolve this.
        </div>
      </div>

      <!-- Faculty workload table -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Faculty Load Summary</div>
            <div class="card-sub">All faculty across BSIS, BSIT, BIT-CT departments</div>
          </div>
          <input class="field-input" placeholder="Search faculty..." style="width:200px;padding:8px 12px;font-size:13px;" oninput="filterFaculty(this.value)">
        </div>
        <div class="table-wrap">
          <table id="faculty-table">
            <thead>
              <tr>
                <th>Faculty</th>
                <th>Department</th>
                <th>Employment</th>
                <th>Subjects</th>
                <th>Total Load</th>
                <th>Load Bar</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Jerome Bautista</b></td>
                <td>BSIS</td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text2);">CC 313, CC 401, IT 302</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--green);">24h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:80%;background:var(--green);"></div></div>
                  <div style="font-size:10px;color:var(--text3);margin-top:2px;">24/30h</div>
                </td>
                <td><span class="badge badge-green">OK</span></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td>
                <td>BSIT</td>
                <td>Part-time</td>
                <td style="font-size:12px;color:var(--text2);">IT 401, CC 201, GE 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--amber);">27h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:90%;background:var(--amber);"></div></div>
                  <div style="font-size:10px;color:var(--text3);margin-top:2px;">27/30h</div>
                </td>
                <td><span class="badge badge-amber">Near Max</span></td>
              </tr>
              <tr style="background:#fff5f5;">
                <td><b>Carlo Mendoza</b></td>
                <td>BSIT</td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text2);">CC 101, CC 102, IT 201, CC 311</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--red);">31h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:100%;background:var(--red);"></div></div>
                  <div style="font-size:10px;color:var(--red);margin-top:2px;font-weight:600;">31/30h — OVER</div>
                </td>
                <td><span class="badge badge-red">Overload</span></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td>
                <td>BSIS</td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text2);">GE 102, IT 101</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--blue);">18h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--blue);"></div></div>
                  <div style="font-size:10px;color:var(--text3);margin-top:2px;">18/30h</div>
                </td>
                <td><span class="badge badge-blue">Available</span></td>
              </tr>
              <tr>
                <td><b>Noel Garcia</b></td>
                <td>BIT-CT</td>
                <td>Full-time</td>
                <td style="font-size:12px;color:var(--text2);">GE 201, CT 101, CT 201</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--green);">21h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:70%;background:var(--green);"></div></div>
                  <div style="font-size:10px;color:var(--text3);margin-top:2px;">21/30h</div>
                </td>
                <td><span class="badge badge-green">OK</span></td>
              </tr>
              <tr>
                <td><b>Liza Cruz</b></td>
                <td>BSIT</td>
                <td>Part-time</td>
                <td style="font-size:12px;color:var(--text2);">GE 103</td>
                <td><span style="font-family:var(--mono);font-weight:700;color:var(--teal);">9h</span></td>
                <td style="min-width:120px;">
                  <div class="workload-bar"><div class="workload-fill" style="width:30%;background:var(--teal);"></div></div>
                  <div style="font-size:10px;color:var(--text3);margin-top:2px;">9/30h</div>
                </td>
                <td><span class="badge badge-teal">Part-time</span></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>

<!-- EXPORT MODAL -->
<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header">
      <div class="modal-title">Export Report</div>
      <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
    </div>
    <div class="modal-body">
      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Report Type</label>
        <select class="field-select">
          <option>Faculty Workload Report</option>
          <option>Master Schedule</option>
          <option>Faculty Deployment Report</option>
          <option>Department Summary</option>
        </select>
      </div>
      <div class="field-group" style="margin-bottom:14px;">
        <label class="field-label">Department</label>
        <select class="field-select">
          <option>All Departments</option>
          <option>BSIS</option>
          <option>BSIT</option>
          <option>BIT-CT</option>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">Format</label>
        <select class="field-select">
          <option>PDF</option>
          <option>Excel (.xlsx)</option>
          <option>Word (.docx)</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported successfully!')">Download</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── MODALS ─────────────────────────────────────────────────────────────────────
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── SEARCH FILTER ──────────────────────────────────────────────────────────────
function filterFaculty(query) {
  const q = query.toLowerCase();
  document.querySelectorAll('#faculty-table tbody tr').forEach(row => {
    row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
}
</script>
</body>
</html>