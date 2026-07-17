{{-- resources/views/dean/partials/review_modal_content.blade.php --}}
{{-- Loaded via AJAX by openReview() in pending_approvals.blade.php --}}

@if($conflictCount > 0)
<div style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
  <strong>{{ $conflictCount }} Conflict{{ $conflictCount > 1 ? 's' : '' }} Detected:</strong>
  Faculty members are double-booked at the same day and time. Resolve before approving.
</div>
@else
<div style="background:var(--green-light);border:1px solid #bbf7d0;border-left:4px solid var(--green);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#14532d;">
  <strong>✓ No conflicts found.</strong> This schedule is ready for approval.
</div>
@endif

{{-- Submission meta --}}
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-bottom:16px;">
  <div style="background:var(--grey);border-radius:10px;padding:12px;">
    <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:2px;">Department</div>
    <div style="font-weight:700;font-size:14px;">{{ $submission->department->dept_code ?? '—' }}</div>
  </div>
  <div style="background:var(--grey);border-radius:10px;padding:12px;">
    <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:2px;">Submitted By</div>
    <div style="font-weight:600;font-size:13px;">
      {{ $submission->submittedBy->usr_fname ?? '' }} {{ $submission->submittedBy->usr_lname ?? '' }}
    </div>
  </div>
  <div style="background:var(--grey);border-radius:10px;padding:12px;">
    <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;margin-bottom:2px;">Submitted</div>
    <div style="font-weight:600;font-size:13px;">
      {{ \Carbon\Carbon::parse($submission->schsub_submitted_at)->format('M d, Y h:i A') }}
    </div>
  </div>
</div>

@if($submission->schsub_remarks)
<div style="background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:10px 14px;font-size:12px;color:#92400e;margin-bottom:14px;">
  <strong>Previous Remark:</strong> {{ $submission->schsub_remarks }}
</div>
@endif

{{-- Schedule table --}}
<div class="table-wrap" style="max-height:280px;overflow-y:auto;">
  <table>
    <thead>
      <tr>
        <th>Faculty</th>
        <th>Subject</th>
        <th>Section</th>
        <th>Day & Time</th>
        <th>Room</th>
        <th>Issue</th>
      </tr>
    </thead>
    <tbody>
      @forelse($schedules as $sch)
      <tr style="{{ $sch->has_conflict ? 'background:#fff1f2;' : '' }}">
        <td><b>{{ $sch->faculty_name }}</b></td>
        <td style="font-family:var(--mono);font-size:12px;">{{ $sch->subject_code }}</td>
        <td>{{ $sch->section_name }}</td>
        <td style="font-size:12px;">
          {{ $sch->sch_day }} {{ \Carbon\Carbon::parse($sch->sch_start_time)->format('g:i A') }}–{{ \Carbon\Carbon::parse($sch->sch_end_time)->format('g:i A') }}
        </td>
        <td>{{ $sch->room_name }}</td>
        <td>
          <span class="badge {{ $sch->has_conflict ? 'badge-red' : 'badge-green' }}">
            {{ $sch->has_conflict ? 'Conflict' : 'OK' }}
          </span>
        </td>
      </tr>
      @empty
      <tr>
        <td colspan="6" style="text-align:center;color:var(--text3);padding:24px;">
          No schedule entries found for this submission.
        </td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>