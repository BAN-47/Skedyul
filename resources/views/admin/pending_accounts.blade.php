<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL — Pending Account Approval</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
  <div class="app-shell">
    @include('partials.admin_sidebar')

    <div class="app-main">
      @include('partials.admin_header', ['title' => 'Pending Account Approval'])

      <main class="page-content">
        @if(session('success'))
          <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800" role="status">
            {{ session('success') }}
          </div>
        @endif
        @if($errors->any())
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            {{ $errors->first() }}
          </div>
        @endif

        <div class="card mb-4">
          <div class="card-header">
            <div>
              <div class="card-title">Faculty Registrations Awaiting Review</div>
              <div class="card-sub">Check the submitted information and faculty ID photo before deciding.</div>
            </div>
            <span class="badge badge-amber">{{ $pendingAccounts->count() }} pending</span>
          </div>
        </div>

        @forelse($pendingAccounts as $review)
          @php
            $user = $review->user;
            $faculty = $user?->faculty;
            $displayName = trim(implode(' ', array_filter([
              $user?->usr_first_name,
              $user?->usr_middle_name,
              $user?->usr_last_name,
              $user?->usr_suffix,
            ]))) ?: ($user?->usr_name ?? 'Unknown applicant');
          @endphp

          <section class="card mb-4" aria-labelledby="applicant-{{ $review->fvr_id }}">
            @if($review->possible_name_match)
              <div class="mx-4 mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <strong>Possible name match:</strong> another faculty profile has the same first and last name. Compare the employee ID and uploaded photo; a matching name alone does not mean it is the same person.
              </div>
            @endif
            <div class="card-header">
              <div>
                <h2 id="applicant-{{ $review->fvr_id }}" class="card-title">{{ $displayName }}</h2>
                <div class="card-sub">Submitted {{ $review->created_at?->format('M j, Y · g:i A') ?? 'date unavailable' }}</div>
              </div>
              <span class="badge badge-amber">Pending review</span>
            </div>

            <div class="pending-account-grid p-4">
              <div class="pending-account-details">
                <div><div class="pending-account-label">Email</div><div class="pending-account-value">{{ $user?->usr_email ?? '—' }}</div></div>
                <div><div class="pending-account-label">Employee ID</div><div class="pending-account-value">{{ $faculty?->fac_employee_id ?? $user?->usr_employee_id ?? '—' }}</div></div>
                <div><div class="pending-account-label">Phone</div><div class="pending-account-value">{{ $faculty?->fac_phone_number ?? '—' }}</div></div>
                <div><div class="pending-account-label">Date of birth</div><div class="pending-account-value">{{ $faculty?->fac_dob ? \Illuminate\Support\Carbon::parse($faculty->fac_dob)->format('M j, Y') : '—' }}</div></div>
                <div><div class="pending-account-label">Gender</div><div class="pending-account-value">{{ $faculty?->fac_gender ?? '—' }}</div></div>
                <div><div class="pending-account-label">Nationality</div><div class="pending-account-value">{{ $faculty?->fac_nationality ?? '—' }}</div></div>
                <div><div class="pending-account-label">College</div><div class="pending-account-value">{{ $faculty?->college?->college_name ?? '—' }}</div></div>
                <div><div class="pending-account-label">Department / Program</div><div class="pending-account-value">{{ $faculty?->program?->dept_name ?? '—' }}</div></div>
                <div><div class="pending-account-label">Rank / Title</div><div class="pending-account-value">{{ $faculty?->fac_rank ?? '—' }}</div></div>
                <div><div class="pending-account-label">Employment</div><div class="pending-account-value">{{ ($faculty?->fac_employment_type ?? '') === 'part_time' ? 'Part-time' : 'Full-time' }}</div></div>
                <div class="col-span-full"><div class="pending-account-label">Address</div><div class="pending-account-value">{{ $faculty?->fac_address ?? '—' }}</div></div>
              </div>

              <div>
                <div class="mb-2 flex items-center justify-between">
                  <h3 class="text-sm font-bold text-slate-800">Uploaded faculty ID</h3>
                  <a href="{{ route('admin.pending-accounts.photo', $review->fvr_id) }}" target="_blank" rel="noopener" class="text-xs font-semibold text-blue-700 hover:underline">Open full size</a>
                </div>
                <a href="{{ route('admin.pending-accounts.photo', $review->fvr_id) }}" target="_blank" rel="noopener" aria-label="Open {{ $displayName }}'s faculty ID photo">
                  <img class="pending-account-photo" src="{{ route('admin.pending-accounts.photo', $review->fvr_id) }}" alt="Faculty ID photo for {{ $displayName }}" loading="lazy">
                </a>
              </div>
            </div>

            <div class="flex flex-wrap justify-end gap-2 border-t border-slate-100 p-4">
              <button type="button" class="btn btn-secondary" onclick="openRejectDialog('{{ route('admin.pending-accounts.reject', $review->fvr_id) }}', @js($displayName))">Reject</button>
              <form method="POST" action="{{ route('admin.pending-accounts.approve', $review->fvr_id) }}" onsubmit="return confirm('Approve this faculty account? The applicant will be able to sign in.')">
                @csrf
                <button type="submit" class="btn btn-primary">Approve Account</button>
              </form>
            </div>
          </section>
        @empty
          <div class="card p-10 text-center">
            <div class="mb-2 text-3xl" aria-hidden="true">✅</div>
            <div class="font-bold text-slate-800">No pending faculty registrations</div>
            <p class="mt-1 text-sm text-slate-500">New public registrations will appear here with their submitted ID photo.</p>
          </div>
        @endforelse

        @if($rejectedAccounts->isNotEmpty())
          <section class="card mt-6 overflow-hidden">
            <div class="card-header">
              <div>
                <h2 class="card-title">Recent Rejection Decisions</h2>
                <p class="card-sub">The rejected account and faculty profile were deleted. These decision records are retained for the audit trail.</p>
              </div>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full min-w-[720px] text-left text-sm">
                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                  <tr>
                    <th class="px-4 py-3">Applicant</th>
                    <th class="px-4 py-3">Reason</th>
                    <th class="px-4 py-3">Decision by</th>
                    <th class="px-4 py-3">Date</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                  @foreach($rejectedAccounts as $decision)
                    <tr>
                      <td class="px-4 py-3 align-top">
                        <div class="font-semibold text-slate-800">{{ $decision->fvr_applicant_name ?: 'Applicant record' }}</div>
                        <div class="text-xs text-slate-500">{{ $decision->fvr_applicant_email ?: '—' }}</div>
                      </td>
                      <td class="max-w-lg whitespace-pre-line px-4 py-3 align-top text-slate-700">{{ $decision->fvr_decision_note ?: 'No reason recorded.' }}</td>
                      <td class="px-4 py-3 align-top text-slate-700">{{ $decision->reviewer?->usr_name ?? 'Administrator' }}</td>
                      <td class="whitespace-nowrap px-4 py-3 align-top text-slate-600">{{ $decision->fvr_reviewed_at?->format('M j, Y · g:i A') ?? '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>
        @endif
      </main>
    </div>
  </div>

  <div id="reject-backdrop" class="reject-backdrop" role="presentation" onclick="if(event.target===this) closeRejectDialog()">
    <section class="reject-dialog" role="dialog" aria-modal="true" aria-labelledby="reject-title" aria-describedby="reject-description">
      <div class="mb-4 flex items-start justify-between gap-4">
        <h2 id="reject-title" class="text-xl font-bold text-red-600">Reject Faculty Account</h2>
        <button type="button" onclick="closeRejectDialog()" aria-label="Close" class="rounded-md px-2 text-2xl leading-none text-slate-400 hover:bg-slate-100">&times;</button>
      </div>
      <p id="reject-description" class="text-center text-sm text-slate-600">You are rejecting <strong id="reject-applicant" class="text-slate-800"></strong>.</p>
      <p class="mt-3 text-center text-sm text-slate-600">Please explain why this registration is being rejected:</p>

      <form id="reject-form" method="POST" class="mt-3" onsubmit="return validateRejectDialog()">
        @csrf
        <label for="reject-reason" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-600">Rejection note <span class="text-red-600">*</span></label>
        <textarea id="reject-reason" name="decision_note" required minlength="5" maxlength="2000" rows="3" class="reject-note-input w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="For example: The uploaded faculty ID is unreadable."></textarea>

        <p class="mt-2 text-center text-xs text-slate-400">The faculty and user records will be deleted. The reason and decision details will remain in the rejection history.</p>

        <div class="mt-5 flex justify-end gap-2">
          <button type="button" onclick="closeRejectDialog()" class="rounded-lg bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-200">Cancel</button>
          <button id="reject-submit" type="submit" disabled class="rounded-lg bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-300 disabled:cursor-not-allowed">Reject Account</button>
        </div>
      </form>
    </section>
  </div>

  <script>
    let rejectPreviousFocus = null;

    function openRejectDialog(action, applicantName) {
      const backdrop = document.getElementById('reject-backdrop');
      rejectPreviousFocus = document.activeElement;
      document.getElementById('reject-form').action = action;
      document.getElementById('reject-applicant').textContent = applicantName;
      document.getElementById('reject-reason').value = '';
      updateRejectButton();
      backdrop.style.display = 'flex';
      document.body.style.overflow = 'hidden';
      document.getElementById('reject-reason').focus();
    }

    function closeRejectDialog() {
      document.getElementById('reject-backdrop').style.display = 'none';
      document.body.style.overflow = '';
      rejectPreviousFocus?.focus();
    }

    function updateRejectButton() {
      const hasReason = document.getElementById('reject-reason').value.trim().length >= 5;
      const button = document.getElementById('reject-submit');
      button.disabled = !hasReason;
      button.className = button.disabled
        ? 'rounded-lg bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-300 disabled:cursor-not-allowed'
        : 'rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-red-700';
    }

    function validateRejectDialog() {
      return document.getElementById('reject-reason').value.trim().length >= 5;
    }

    document.getElementById('reject-reason').addEventListener('input', updateRejectButton);
    document.addEventListener('keydown', event => {
      if (event.key === 'Escape' && document.getElementById('reject-backdrop').style.display === 'flex') closeRejectDialog();
    });
  </script>
</body>

</html>
