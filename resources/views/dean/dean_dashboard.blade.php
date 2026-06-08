<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Dean Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dean_dashboard.css') }}">
</head>
<body>

<div class="app-wrapper">

@include('partials.dean_sidebar')

  <!-- ══════════ MAIN ══════════ -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Dean Dashboard</div>
      <div style="display:flex;align-items:center;gap:10px;">
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        <button class="topbar-btn btn-secondary" onclick="showToast('3 pending approvals')">Notifications</button>
      </div>
    </div>
    <!-- END TOPBAR -->

    <!-- ══════════ DASHBOARD PAGE ══════════ -->
    <div class="page-content">

      <!-- Page heading -->
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
        <div style="flex:1;">
          <div style="font-size:22px;font-weight:800;">Good morning, Dean Villaceran</div>
          <div style="font-size:13px;color:var(--text3);margin-top:3px;">AY 2025–2026 · 1st Semester · CCICT Overview</div>
        </div>
        <span class="badge badge-teal">Scheduling Active</span>
      </div>

      <!-- Stat Cards -->
      <div class="stat-grid">
        <div class="stat-card" style="--accent:#0891b2">
          <div class="stat-label">Total Faculty</div>
          <div class="stat-value">19</div>
          <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
        </div>
        <div class="stat-card" style="--accent:#d97706">
          <div class="stat-label">Subjects Plotted</div>
          <div class="stat-value">54</div>
          <div class="stat-sub">of 58 total</div>
        </div>
        <div class="stat-card" style="--accent:#dc2626">
          <div class="stat-label">Avg. Faculty Load</div>
          <div class="stat-value">21h</div>
          <div class="stat-sub">of 30h max</div>
        </div>
        <div class="stat-card" style="--accent:#16a34a">
          <div class="stat-label">Schedules Approved</div>
          <div class="stat-value">3</div>
          <div class="stat-sub">Pending: 3 depts</div>
        </div>
      </div>

      <!-- Row 1: Dept Summary + Pending Approvals -->
      <div class="two-col">

        <!-- Department Summary -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Department Summary</div>
              <div class="card-sub">Faculty load status per department</div>
            </div>
          </div>
          <div class="workload-item">
            <div class="workload-header"><div class="workload-name">BSIS — 8 Faculty</div><div class="workload-val" style="color:var(--green)">87%</div></div>
            <div class="workload-bar"><div class="workload-fill" style="width:87%;background:var(--green)"></div></div>
          </div>
          <div class="workload-item">
            <div class="workload-header"><div class="workload-name">BSIT — 7 Faculty</div><div class="workload-val" style="color:var(--amber)">72%</div></div>
            <div class="workload-bar"><div class="workload-fill" style="width:72%;background:var(--amber)"></div></div>
          </div>
          <div class="workload-item">
            <div class="workload-header"><div class="workload-name">BIT-CT — 4 Faculty</div><div class="workload-val" style="color:var(--teal)">60%</div></div>
            <div class="workload-bar"><div class="workload-fill" style="width:60%;background:var(--blue)"></div></div>
          </div>
          <div class="workload-item">
            <div class="workload-header"><div class="workload-name">Overloaded Faculty</div><div class="workload-val" style="color:var(--red)">2</div></div>
            <div class="workload-bar"><div class="workload-fill" style="width:11%;background:var(--red)"></div></div>
          </div>
        </div>

        <!-- Pending Approvals -->
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">Pending Approvals</div>
              <div class="card-sub">Awaiting Dean's signature</div>
            </div>
          </div>

          <div class="approval-item">
            <div class="approval-avatar" style="background:#d97706;">RT</div>
            <div class="approval-content">
              <div class="approval-name">BSIS Schedule — 1st Sem AY 2025–26</div>
              <div class="approval-detail">Submitted by Chair Rodrigo Tan · 8 faculty · 23 sections · 0 conflicts</div>
              <div class="approval-actions">
                <button class="topbar-btn btn-primary" style="padding:5px 12px;font-size:11px;" onclick="showToast('BSIS Schedule approved!')">Approve</button>
                <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;" onclick="openModal('modal-review')">Review</button>
                <button class="topbar-btn" style="padding:5px 12px;font-size:11px;background:var(--red-light);color:var(--red);" onclick="showToast('Returned.')">Return</button>
              </div>
            </div>
          </div>

          <div class="approval-item">
            <div class="approval-avatar" style="background:#7c3aed;">MC</div>
            <div class="approval-content">
              <div class="approval-name">BSIT Schedule — 1st Sem AY 2025–26</div>
              <div class="approval-detail">Submitted by Chair Maria Cruz · 7 faculty · 19 sections · 1 conflict flagged</div>
              <div class="approval-actions">
                <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;" onclick="openModal('modal-review')">Review</button>
                <button class="topbar-btn" style="padding:5px 12px;font-size:11px;background:var(--red-light);color:var(--red);" onclick="showToast('Returned.')">Return</button>
              </div>
            </div>
          </div>

          <div class="approval-item">
            <div class="approval-avatar" style="background:#0891b2;">JL</div>
            <div class="approval-content">
              <div class="approval-name">BIT-CT Schedule — 1st Sem AY 2025–26</div>
              <div class="approval-detail">Submitted by Chair Jose Lim · 4 faculty · 10 sections · 0 conflicts</div>
              <div class="approval-actions">
                <button class="topbar-btn btn-primary" style="padding:5px 12px;font-size:11px;" onclick="showToast('BIT-CT approved!')">Approve</button>
                <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;" onclick="openModal('modal-review')">Review</button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Row 2: Overload Alerts + Quick Actions -->
      <div class="two-col">

        <!-- Overload / Underload Alerts -->
        <div class="card">
          <div class="card-header">
            <div><div class="card-title">Overload / Underload Alerts</div></div>
          </div>
          <table>
            <thead>
              <tr><th>Faculty</th><th>Dept</th><th>Load</th><th>Status</th></tr>
            </thead>
            <tbody>
              <tr>
                <td><b>Carlo Mendoza</b></td><td>BSIT</td>
                <td><span style="font-family:var(--mono);color:var(--red);font-weight:700">31h</span></td>
                <td><span class="badge badge-red">Overload</span></td>
              </tr>
              <tr>
                <td><b>Ana Reyes</b></td><td>BSIT</td>
                <td><span style="font-family:var(--mono);color:var(--amber);font-weight:700">27h</span></td>
                <td><span class="badge badge-amber">Near Max</span></td>
              </tr>
              <tr>
                <td><b>Maria Santos</b></td><td>BSIS</td>
                <td><span style="font-family:var(--mono);color:var(--blue);font-weight:700">18h</span></td>
                <td><span class="badge badge-blue">Available</span></td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Quick Actions -->
        <div class="card">
          <div class="card-header">
            <div><div class="card-title">Quick Actions</div></div>
          </div>
          <div class="quick-actions">
            <div class="quick-btn" onclick="openModal('modal-export')"><div class="quick-btn-label">Export Master Schedule</div></div>
            <div class="quick-btn" onclick="showToast('Opening Faculty Overview...')"><div class="quick-btn-label">Faculty Overview</div></div>
            <div class="quick-btn" onclick="showToast('Opening Approvals...')"><div class="quick-btn-label">Approve Schedules</div></div>
            <div class="quick-btn" onclick="openModal('modal-export')"><div class="quick-btn-label">Workload Report</div></div>
            <div class="quick-btn" onclick="showToast('Opening Deployment Report...')"><div class="quick-btn-label">Deployment Report</div></div>
            <div class="quick-btn" onclick="showToast('Notify Chairs feature coming soon.')"><div class="quick-btn-label">Notify All Chairs</div></div>
          </div>
        </div>

      </div>
    </div>
    <!-- ══ END DASHBOARD ══ -->

  </div><!-- end main -->
</div><!-- end app-wrapper -->

<!-- MODAL: EXPORT -->
<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header">
      <div class="modal-title">Export Report</div>
      <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
    </div>
    <div class="modal-body">
      <div class="field-group"><label class="field-label">Report Type</label>
        <select class="field-select"><option>Master Schedule</option><option>Faculty Workload Report</option><option>Faculty Deployment Report</option><option>Department Summary</option></select>
      </div>
      <div class="field-group"><label class="field-label">Department</label>
        <select class="field-select"><option>All Departments</option><option>BSIS</option><option>BSIT</option><option>BIT-CT</option></select>
      </div>
      <div class="field-group"><label class="field-label">Format</label>
        <select class="field-select"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
    </div>
  </div>
</div>

<!-- MODAL: REVIEW -->
<div class="modal-overlay" id="modal-review">
  <div class="modal" style="width:560px;">
    <div class="modal-header">
      <div class="modal-title">Schedule Review — BSIT</div>
      <button class="modal-close" onclick="closeModal('modal-review')">✕</button>
    </div>
    <div class="modal-body">
      <div style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
        <strong>1 Conflict Detected:</strong> Carlo Mendoza is scheduled for CC 311 and IT 201 at the same time on Monday 8:30–10:00 AM.
      </div>
      <table>
        <thead><tr><th>Faculty</th><th>Subject</th><th>Day & Time</th><th>Room</th><th>Issue</th></tr></thead>
        <tbody>
          <tr><td><b>Carlo Mendoza</b></td><td>IT 201</td><td>Mon 8:30–10:00</td><td>Lab 1</td><td><span class="badge badge-red">Conflict</span></td></tr>
          <tr><td><b>Carlo Mendoza</b></td><td>CC 311</td><td>Mon 8:30–10:00</td><td>Room 205</td><td><span class="badge badge-red">Conflict</span></td></tr>
          <tr><td><b>Ana Reyes</b></td><td>IT 401</td><td>Tue 7:00–8:30</td><td>Room 206</td><td><span class="badge badge-green">OK</span></td></tr>
        </tbody>
      </table>
      <div class="field-group" style="margin-top:16px;">
        <label class="field-label">Return Note</label>
        <textarea class="field-select" rows="3" style="resize:vertical;padding:10px 12px;">Please resolve the scheduling conflict for Carlo Mendoza before resubmitting.</textarea>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-review')">Close</button>
      <button class="topbar-btn" style="background:var(--red-light);color:var(--red);" onclick="closeModal('modal-review');showToast('Schedule returned to Chair Cruz.')">Return to Chair</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.querySelectorAll('.modal-overlay').forEach(m => {
  m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
});
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>
