<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Pending Approvals</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dean/pending_approvals.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.dean_sidebar')

  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Pending Approvals</div>
      <div class="topbar-actions">
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        <button class="topbar-btn btn-secondary" onclick="openModal('modal-notify')">Notify Chairs</button>
      </div>
    </div>

    {{-- ── FLASH ── --}}
    @if(session("success"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("✅ {{ session("success") }}"));</script>
    @endif
    @if(session("error"))
      <script>document.addEventListener("DOMContentLoaded", () => showToast("❌ {{ session("error") }}"));</script>
    @endif

    <div id="page-approvals" class="page active">

      {{-- ── STAT CARDS ── --}}
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:20px;">
        <div class="stat-card" style="--accent:#d97706;">
          <div class="stat-icon">⏳</div>
          <div class="stat-label">Pending</div>
          <div class="stat-value">{{ $pendingCount }}</div>
          <div class="stat-sub">Awaiting your review</div>
        </div>
        <div class="stat-card" style="--accent:#16a34a;">
          <div class="stat-icon">✅</div>
          <div class="stat-label">Approved</div>
          <div class="stat-value">{{ $approvedCount }}</div>
          <div class="stat-sub">This semester</div>
        </div>
        <div class="stat-card" style="--accent:#dc2626;">
          <div class="stat-icon">↩️</div>
          <div class="stat-label">Returned</div>
          <div class="stat-value">{{ $returnedCount }}</div>
          <div class="stat-sub">Sent back to chair</div>
        </div>
      </div>

      <div style="margin-bottom:20px;">
        <div style="font-size:20px;font-weight:800;">Schedule Approvals</div>
        @if($semester)
          <div style="font-size:13px;color:var(--text3);margin-top:2px;">
            {{ $semester->sem_name }} · AY {{ $semester->academicYear->ay_academic_year ?? '' }}
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
                    {{ $sub->submittedBy->usr_fname }} {{ $sub->submittedBy->usr_lname }}
                  @else
                    <span style="color:var(--text3);">Unknown</span>
                  @endif
                </td>
                <td style="font-size:12px;color:var(--text2);">
                  {{ \Carbon\Carbon::parse($sub->schsub_submitted_at)->format('M d, Y') }}
                </td>
                <td>{{ $sub->faculty_count }}</td>
                <td>
                  @if($sub->conflict_count > 0)
                    <span style="color:var(--red);font-weight:700;">{{ $sub->conflict_count }}</span>
                  @else
                    <span style="color:var(--green);font-weight:700;">0</span>
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
                    <button class="topbar-btn btn-secondary"
                      style="padding:4px 10px;font-size:11px;"
                      onclick="openReview(
                        '{{ $sub->schsub_id }}',
                        '{{ $sub->department->dept_code ?? "" }}',
                        {{ $sub->conflict_count }}
                      )">
                      Review
                    </button>
                  @else
                    <span style="font-size:12px;color:var(--text3);">Approved</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="7" style="text-align:center;padding:40px;color:var(--text3);">
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

{{-- ══════════════════════════════════════════════════════════════
     MODAL: REVIEW — approve or return
     ══════════════════════════════════════════════════════════════ --}}
<div class="modal-overlay" id="modal-review">
  <div class="modal" style="width:620px;">
    <div class="modal-header">
      <div class="modal-title" id="review-modal-title">Schedule Review</div>
      <button class="modal-close" type="button" onclick="closeModal('modal-review')">✕</button>
    </div>
    <div class="modal-body" id="review-modal-body">
      <div style="text-align:center;padding:40px;color:var(--text3);">Loading schedules…</div>
    </div>

    {{-- APPROVE FORM --}}
    <form id="form-approve" method="POST" action="" style="display:none;">
      @csrf
      @method('POST')
      <input type="hidden" name="_action" value="approve">
      <textarea name="remarks" style="display:none;"></textarea>
    </form>

    {{-- RETURN FORM --}}
    <form id="form-return" method="POST" action="">
      @csrf
      @method('POST')
      <div class="modal-body" style="padding-top:0;">
        <div class="field-group" style="margin-top:12px;">
          <label class="field-label">Return Note <span style="color:var(--red)">*</span></label>
          <textarea class="field-input" name="remarks" id="return-note" rows="3"
            placeholder="Explain what the Chair needs to fix before resubmitting…"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button class="topbar-btn btn-secondary" type="button" onclick="closeModal('modal-review')">Close</button>
        <button class="topbar-btn"
          type="button"
          id="btn-return"
          style="background:var(--red-light);color:var(--red);">
          ↩ Return to Chair
        </button>
        <button class="topbar-btn btn-primary"
          type="button"
          id="btn-approve">
          ✓ Approve Schedule
        </button>
      </div>
    </form>
  </div>
</div>

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
// Fetches schedule detail from the server then opens the modal
let currentSubId = null;

function openReview(subId, deptCode, conflictCount) {
  currentSubId = subId;

  // Update modal title
  document.getElementById('review-modal-title').textContent = 'Schedule Review — ' + deptCode;

  // Show loading state
  document.getElementById('review-modal-body').innerHTML =
    '<div style="text-align:center;padding:40px;color:var(--text3);">Loading schedules…</div>';

  // Set form actions
  document.getElementById('form-approve').action = '/dean/pending-approvals/' + subId + '/approve';
  document.getElementById('form-return').action  = '/dean/pending-approvals/' + subId + '/return';

  // Wire approve button — block if there are conflicts
  document.getElementById('btn-approve').onclick = () => {
    if (conflictCount > 0) {
      showToast('❌ Cannot approve — resolve ' + conflictCount + ' conflict(s) first.');
      return;
    }
    if (!confirm('Approve the schedule for ' + deptCode + '?')) return;
    document.getElementById('form-approve').submit();
  };

  // Wire return button
  document.getElementById('btn-return').onclick = () => {
    const note = document.getElementById('return-note').value.trim();
    if (!note) { showToast('Please enter a return note before sending.'); return; }
    if (!confirm('Return schedule to Chair with your note?')) return;
    document.getElementById('form-return').submit();
  };

  // Fetch schedule rows via AJAX
  fetch('/dean/pending-approvals/' + subId + '/review', {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.text())
  .then(html => {
    document.getElementById('review-modal-body').innerHTML = html;
  })
  .catch(() => {
    document.getElementById('review-modal-body').innerHTML =
      '<div style="color:var(--red);padding:20px;">Failed to load schedules. Please try again.</div>';
  });

  openModal('modal-review');
}

// ── NOTIFY CHAIRS (JS-only session history) ────────────────────────────────
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