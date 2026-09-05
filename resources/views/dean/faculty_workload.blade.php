<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Workload</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

  <div class="app-main">

            @include('partials.dean_header', ['title' => 'Dean Faculty Workload Overview'])

    <div class="page-content">
      <div class="flex items-center justify-between mb-5">
        <div>
          <div class="text-[20px] font-extrabold">Faculty Workload Overview</div>
          <div class="text-[13px] text-slate-400 mt-0.5">AY 2025–26 · 1st Semester</div>
        </div>
        <button class="btn btn-primary" onclick="openModal('modal-export')">Export PDF</button>
      </div>

      <div class="card">
        <table>
          <thead>
            <tr>
              <th>Faculty</th>
              <th>Department</th>
              <th>Employment</th>
              <th>Subjects</th>
              <th>Total Load</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><b>Jerome Bautista</b></td>
              <td>BSIS</td>
              <td>Full-time</td>
              <td>CC 313, CC 401, IT 302</td>
              <td class="font-mono font-bold text-green-600">24h</td>
              <td><span class="badge badge-green">OK</span></td>
            </tr>
            <tr>
              <td><b>Ana Reyes</b></td>
              <td>BSIT</td>
              <td>Part-time</td>
              <td>IT 401, CC 201, GE 101</td>
              <td class="font-mono font-bold text-amber-600">27h</td>
              <td><span class="badge badge-amber">Near Max</span></td>
            </tr>
            <tr>
              <td><b>Carlo Mendoza</b></td>
              <td>BSIT</td>
              <td>Full-time</td>
              <td>CC 101, CC 102, IT 201, CC 311</td>
              <td class="font-mono font-bold text-red-600">31h</td>
              <td><span class="badge badge-red">Overload</span></td>
            </tr>
            <tr>
              <td><b>Maria Santos</b></td>
              <td>BSIS</td>
              <td>Full-time</td>
              <td>GE 102, IT 101</td>
              <td class="font-mono font-bold text-blue-600">18h</td>
              <td><span class="badge badge-blue">Available</span></td>
            </tr>
            <tr>
              <td><b>Noel Garcia</b></td>
              <td>BIT-CT</td>
              <td>Full-time</td>
              <td>GE 201, CT 101, CT 201</td>
              <td class="font-mono font-bold text-green-600">21h</td>
              <td><span class="badge badge-green">OK</span></td>
            </tr>
            <tr>
              <td><b>Liza Cruz</b></td>
              <td>BSIT</td>
              <td>Part-time</td>
              <td>GE 103</td>
              <td class="font-mono font-bold text-blue-600">9h</td>
              <td><span class="badge badge-teal">Part-time</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div><!-- end app-main -->
</div><!-- end app-shell -->

<!-- MODAL: EXPORT -->
<div class="modal-overlay" id="modal-export">
  <div class="modal-box w-[440px]">
    <div class="modal-header">
      <div class="modal-title">Export Report</div>
      <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
    </div>
    <div>
      <div class="mb-3.5">
        <label class="field-label">Report Type</label>
        <select class="field-input">
          <option>Master Schedule</option>
          <option selected>Faculty Workload Report</option>
          <option>Faculty Deployment Report</option>
          <option>Department Summary</option>
        </select>
      </div>
      <div class="mb-3.5">
        <label class="field-label">Department</label>
        <select class="field-input"><option value="">All Departments</option></select>
      </div>
      <div>
        <label class="field-label">Format</label>
        <select class="field-input"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
      <button class="btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function openModal(id) {
  document.getElementById(id).classList.add('open');
  if (id === 'modal-notify' && typeof loadChairs === 'function') loadChairs();
}
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.modal-overlay').forEach(m => m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); }));
});

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show'); setTimeout(() => t.classList.remove('show'), 3000);
}
</script>
</body>
</html>