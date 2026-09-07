<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Pending Approvals</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">


  <div class="main">
            @include('partials.dean_header', ['title' => 'Dean Pending Approvals Overview'])


    {{-- ── FLASH ── --}}
    @if(session("success"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("✅ {{ session("success") }}"));</script>
    @endif
    @if(session("error"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("❌ {{ session("error") }}"));</script>
    @endif

    <div id="page-approvals" class="page active">

      {{-- ── STAT CARDS ── --}}
      <div class="mb-5 grid grid-cols-1 gap-3.5 sm:grid-cols-3">
        <div class="stat-card before:bg-amber-600">
          <div class="stat-icon">⏳</div>
          <div class="stat-label">Pending</div>
          <div class="stat-value">{{ $pendingCount }}</div>
          <div class="stat-sub">Awaiting your review</div>
        </div>
        <div class="stat-card before:bg-green-600">
          <div class="stat-icon">✅</div>
          <div class="stat-label">Approved</div>
          <div class="stat-value">{{ $approvedCount }}</div>
          <div class="stat-sub">This semester</div>
        </div>
        <div class="stat-card before:bg-red-600">
          <div class="stat-icon">↩️</div>
          <div class="stat-label">Returned</div>
          <div class="stat-value">{{ $returnedCount }}</div>
          <div class="stat-sub">Sent back to chair</div>
        </div>
      </div>

      <div class="mb-5">
        <div class="text-xl font-extrabold">Schedule Approvals</div>
        @if($semester)
          <div class="mt-0.5 text-[13px] text-slate-400">
            {{ $semester->sem_name }} &middot; AY {{ $semester->academicYear->ay_academic_year ?? '' }}
          </div>
        @endif
      </div>

      {{-- ── SUBMISSIONS TABLE ── --}}
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Submitted Schedules</div>
            <div class="card-sub">{{ $pendingCount }} pending approval</div>
          </div>
        </div>

        <div class="table-wrap">
          <table>
            <thead>
              <tr>
                <th>Department</th>
                <th>Chair</th>
                <th>Submitted</th>
                <th>Faculty</th>
                <th>Conflicts</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse($submissions as $sub)
              <tr>
                <td><b>{{ $sub->department->dept_code ?? '—' }}</b></td>
                <td>
                  @if($sub->submittedBy)
                    {{ $sub->submittedBy->usr_name }}
                  @else
                    <span class="text-slate-400">Unknown</span>
                  @endif
                </td>
                <td class="text-xs text-slate-600">
                  {{ \Carbon\Carbon::parse($sub->schsub_submitted_at)->format('M d, Y') }}
                </td>
                <td>{{ $sub->faculty_count }}</td>
                <td>
                  @if($sub->conflict_count > 0)
                    <span class="font-bold text-red-600">{{ $sub->conflict_count }}</span>
                  @else
                    <span class="font-bold text-green-600">0</span>
                  @endif
                </td>
                <td>
                  @php
                    $statusBadge = match($sub->schsub_status) {
                      'approved' => 'badge-green',
                      'returned' => 'badge-red',
                      default    => 'badge-amber',
                    };
                    $statusLabel = match($sub->schsub_status) {
                      'approved' => 'Approved',
                      'returned' => 'Returned',
                      default    => $sub->conflict_count > 0 ? 'Has Conflict' : 'Pending',
                    };
                  @endphp
                  <span class="badge {{ $statusBadge }}">{{ $statusLabel }}</span>
                </td>
                <td>
                  @if($sub->schsub_status === 'pending' || $sub->schsub_status === 'returned')
                    <button class="topbar-btn btn-secondary px-2.5 py-1 text-[11px]"
                      onclick="openReview(
                        '{{ $sub->schsub_id }}',
                        '{{ $sub->department->dept_code ?? "" }}',
                        {{ $sub->conflict_count }},
                        '{{ route('dean.pending_approvals.review', $sub->schsub_id) }}'
                      )">
                      Review
                    </button>
                  @else
                    <span class="text-xs text-slate-400">Approved</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" class="p-10 text-center text-slate-400">
                  No schedule submissions found for this semester.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div id="review-modal-container"></div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: EXPORT
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header">
      <div class="modal-title">Export Report</div>
      <button class="modal-close" type="button" onclick="closeModal('modal-export')">✕</button>
    </div>
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
          <option>All Departments</option>
          <option>BSIS</option><option>BSIT</option><option>BIT-CT</option>
        </select>
      </div>
      <div class="field-group">
        <label class="field-label">Format</label>
        <select class="field-select">
          <option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option>
        </select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-export')">Cancel</button>
      <button class="topbar-btn btn-primary" type="button"
        onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
    </div>
  </div>
</div>

{{-- ══════════════════════════════════════════════════════════════
     MODAL: NOTIFY CHAIRS
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-notify">
  <div class="modal" style="width:520px;">
    <div class="modal-header">
      <div class="modal-title">Send Notification to Chairs</div>
      <button class="modal-close" type="button" onclick="closeModal('modal-notify')">✕</button>
    </div>
    <div class="modal-body">
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
        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;"
          placeholder="Write your message to the chairs..."></textarea>
      </div>
      <div>
        <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Recently Sent</div>
        <div id="notif-history">
          <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No notifications sent yet this session.</div>
        </div>
      </div>
    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="topbar-btn btn-primary" type="button" onclick="sendNotif()">Send Notification</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── MODALS ────────────────────────────────────────────────────────────────
function openModal(id)  { document.getElementById(id).classList.add('open'); }
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

// ── REVIEW MODAL ──────────────────────────────────────────────────────────
function openReview(subId, deptCode, conflictCount, reviewUrl) {
  const container = document.getElementById('review-modal-container');
  container.innerHTML = '<div class="modal-overlay open p-5"><div class="modal !w-[900px] max-w-[calc(100vw-2.5rem)]"><div class="modal-body p-10 text-center text-slate-400">Loading schedules…</div></div></div>';

  fetch(reviewUrl, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(response => {
    if (!response.ok) throw new Error('Review request failed.');
    return response.text();
  })
  .then(html => {
    container.innerHTML = html;
    const modal = document.getElementById('modal-review');
    modal.addEventListener('click', event => {
      if (event.target === modal) closeModal('modal-review');
    });

    document.getElementById('btn-approve').onclick = () => {
      if (conflictCount > 0) {
        showToast('❌ Cannot approve — resolve ' + conflictCount + ' conflict(s) first.');
        return;
      }
      if (!confirm('Approve the schedule for ' + deptCode + '?')) return;
      document.getElementById('form-approve').submit();
    };

    document.getElementById('btn-return').onclick = () => {
      const note = document.getElementById('return-note').value.trim();
      if (!note) { showToast('Please enter a return note before sending.'); return; }
      if (!confirm('Return schedule to Chair with your note?')) return;
      document.getElementById('form-return').submit();
    };

    openModal('modal-review');
  })
  .catch(() => {
    container.innerHTML = '<div class="modal-overlay open p-5"><div class="modal !w-[900px] max-w-[calc(100vw-2.5rem)]"><div class="modal-body p-5 text-sm text-red-700">Failed to load schedules. Please close this window and try again.</div></div></div>';
  });
}

// ── NOTIFY CHAIRS ────────────────────────────────────────────────────────
function sendNotif() {
  const title   = document.getElementById('notif-title').value.trim();
  const message = document.getElementById('notif-message').value.trim();
  const type    = document.getElementById('notif-type').value;
  if (!title)   { showToast('Please enter a title.'); return; }
  if (!message) { showToast('Please enter a message.'); return; }

  const typeColors  = { info:'badge-blue', reminder:'badge-amber', urgent:'badge-red', deadline:'badge-grey' };
  const typeLabels  = { info:'Info', reminder:'Reminder', urgent:'Urgent', deadline:'Deadline' };
  const now         = new Date();
  const timeStr     = now.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true });
  const historyEl   = document.getElementById('notif-history');
  const empty       = historyEl.querySelector('[style*="text-align:center"]');
  if (empty) empty.remove();

  const item = document.createElement('div');
  item.style.cssText = 'background:var(--grey);border-radius:8px;padding:10px 12px;border:1px solid var(--border);margin-bottom:6px;';
  item.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
      <div style="display:flex;align-items:center;gap:7px;">
        <span class="badge ${typeColors[type]}">${typeLabels[type]}</span>
        <span style="font-size:13px;font-weight:700;color:var(--text);">${title}</span>
      </div>
      <span style="font-size:11px;color:var(--text3);">Sent ${timeStr}</span>
    </div>
    <div style="font-size:12px;color:var(--text2);">${message}</div>`;
  historyEl.prepend(item);

  document.getElementById('notif-title').value   = '';
  document.getElementById('notif-message').value = '';
  showToast('Notification sent to all Chairs!');
}
</script>
</body>
</html>