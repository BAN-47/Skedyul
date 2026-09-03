<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Faculty Workload</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

 @include('partials.dean_sidebar')

  <div class="app-main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Faculty Deployment</div>
      <div class="flex items-center gap-2.5">
        <button class="btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        <button class="btn btn-secondary" onclick="showToast('3 pending approvals')">Notifications</button>
      </div>
    </div>

    <div class="page-content">
      <div class="flex items-center justify-between mb-5">
        <div><div class="text-[20px] font-extrabold">Faculty Deployment Report</div></div>
        <button class="btn btn-primary" onclick="openModal('modal-export')">Export PDF</button>
      </div>
      <div class="card">
        <table>
          <thead><tr><th>#</th><th>Faculty Name</th><th>Department</th><th>Subjects</th><th>Total Load</th><th>Employment</th></tr></thead>
          <tbody>
            @forelse ($faculty as $i => $f)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td><b>{{ $f['name'] }}</b></td>
              <td>{{ $f['department'] }}</td>
              <td>{{ $f['subjects'] }}</td>
              <td class="{{ $f['hours'] > 30 ? 'text-red-600' : '' }}">{{ $f['hours'] }}h</td>
              <td>
                <span class="badge {{ $f['employment'] === 'full_time' ? 'badge-green' : 'badge-blue' }}">
                  {{ Str::of($f['employment'])->replace('_', ' ')->title() }}
                </span>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center">No faculty found.</td></tr>
          @endforelse
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
          <option>Faculty Workload Report</option>
          <option selected>Faculty Deployment Report</option>
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

<!-- MODAL: NOTIFY CHAIRS -->
<div class="modal-overlay" id="modal-notify">
  <div class="modal-box w-[520px]">
    <div class="modal-header">
      <div class="modal-title">Send Notification to Chairs</div>
      <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
    </div>
    <div>
      <div class="mb-4">
        <div class="field-label mb-2">Recipients</div>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2.5 px-3.5 py-2.5 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" class="w-[15px] h-[15px] accent-blue-600">
            <span class="text-[13px] font-bold text-slate-900">All Department Chairs</span>
          </label>
          <div class="flex flex-col gap-1.5 pl-3">
            @foreach ($chairs as $chair)
              <label class="flex items-center gap-2.5 px-3.5 py-2 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
                <input type="checkbox" class="chair-check w-3.5 h-3.5 accent-blue-600" value="{{ $chair->dc_usr_id }}" checked onchange="syncAllChairs()">
                <div class="flex items-center gap-2">
                  <div class="w-7 h-7 rounded-full flex items-center justify-center text-[11px] font-bold text-white flex-shrink-0" style="background:#{{ substr(md5($chair->dc_id), 0, 6) }};">
                    {{ collect(explode(' ', $chair->full_name))->map(fn($n) => $n[0] ?? '')->implode('') }}
                  </div>
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">{{ $chair->full_name }}</div>
                    <div class="text-[11px] text-slate-400">Chair · {{ $chair->department->dept_code ?? 'N/A' }}</div>
                  </div>
                </div>
              </label>
            @endforeach
          </div>
        </div>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Notification Type</label>
        <select class="field-input" id="notif-type">
          <option value="info">General Info</option>
          <option value="reminder">Reminder</option>
          <option value="urgent">Urgent</option>
          <option value="deadline">Deadline Notice</option>
        </select>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Title</label>
        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
      </div>

      <div class="mb-3.5">
        <label class="field-label">Message</label>
        <textarea class="field-input resize-y" id="notif-message" rows="4" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <div>
        <div class="text-[12px] font-bold text-slate-400 uppercase tracking-[.6px] mb-2">Recently Sent</div>
        <div id="notif-history" class="flex flex-col gap-1.5">
          <div class="text-[12px] text-slate-400 p-2 text-center">No notifications sent yet this session.</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
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
    const res = await fetch('{{ route("dean.faculty_deployment.notify") }}', {
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
    const empty = historyEl.querySelector('div.text-center');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.className = 'bg-slate-50 rounded-lg px-3 py-2.5 border border-slate-200';
    item.innerHTML = `
      <div class="flex items-center justify-between mb-1">
        <div class="flex items-center gap-1.5">
          <span class="badge ${typeColors[type]}">${typeLabels[type]}</span>
          <span class="text-[13px] font-bold text-slate-900">${title}</span>
        </div>
        <span class="text-[11px] text-slate-400">Sent ${timeStr}</span>
      </div>
      <div class="text-[12px] text-slate-600 mb-1">${message}</div>`;
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
</script>
</body>
</html>