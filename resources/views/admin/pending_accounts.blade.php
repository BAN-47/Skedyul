<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL — Pending Account Approval</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    .pending-account-grid { display:grid; grid-template-columns:minmax(0, 1fr) minmax(260px, 340px); gap:1.25rem; }
    .pending-account-photo { width:100%; max-height:390px; object-fit:contain; border:1px solid #e2e8f0; border-radius:12px; background:#f8fafc; }
    .pending-account-details { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:.85rem 1.25rem; }
    .pending-account-label { color:#64748b; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    .pending-account-value { margin-top:3px; color:#0f172a; font-size:13px; overflow-wrap:anywhere; }
    @media (max-width:850px) { .pending-account-grid { grid-template-columns:1fr; } .pending-account-photo { max-height:520px; } }
    @media (max-width:520px) { .pending-account-details { grid-template-columns:1fr; } }
  </style>
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
              <form method="POST" action="{{ route('admin.pending-accounts.reject', $review->fvr_id) }}" onsubmit="return confirm('Reject this faculty account registration?')">
                @csrf
                <button type="submit" class="btn btn-secondary">Reject</button>
              </form>
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
      </main>
    </div>
  </div>
</body>

</html>
