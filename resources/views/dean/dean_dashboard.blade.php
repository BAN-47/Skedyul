<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Dean Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    @php
        $colorMap = [
            'green' => ['text' => 'text-green-600', 'bar' => 'bg-green-600'],
            'amber' => ['text' => 'text-amber-600', 'bar' => 'bg-amber-600'],
            'cyan' => ['text' => 'text-cyan-600', 'bar' => 'bg-cyan-600'],
            'blue' => ['text' => 'text-blue-600', 'bar' => 'bg-blue-600'],
            'slate' => ['text' => 'text-slate-500', 'bar' => 'bg-slate-400'],
            'red' => ['text' => 'text-red-600', 'bar' => 'bg-red-600'],
        ];
        $statusBadge = [
            'Overload' => 'badge badge-red',
            'Near Max' => 'badge badge-amber',
            'Available' => 'badge badge-blue',
            'No Load' => 'badge badge-slate',
        ];
    @endphp

    <div class="app-shell">
        @include('partials.dean_sidebar')

        <div class="app-main">
            {{-- Topbar only (not the full dean_header layout) --}}
            <div class="topbar">
                <div class="topbar-title">Dean Dashboard</div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dean.schedule_reports') }}" class="btn btn-primary">Export Report</a>
                    <a href="{{ route('dean.pending_approvals') }}" class="btn btn-secondary flex items-center gap-1.5">
                        Notifications
                        @if (($pendingDeptCount ?? 0) > 0)
                            <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">
                                {{ $pendingDeptCount }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <div class="page-content overflow-y-auto" style="max-height: calc(100vh - 64px); padding-bottom: 32px;">

                <div class="flex items-center gap-3 mb-6">
                    <div class="flex-1">
                        <div class="text-[22px] font-extrabold">{{ $greeting ?? 'Hello' }},
                            {{ $greetingName ?? 'Dean' }}</div>
                        <div class="text-[13px] text-slate-400 mt-0.5">
                            AY {{ $ayLabel ?? '—' }} · {{ $semLabel ?? '—' }} · CCICT Overview
                        </div>
                    </div>
                    <span class="badge badge-teal">Scheduling Active</span>
                </div>

                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-card-bar bg-cyan-600"></div>
                        <div class="stat-label">Total Faculty</div>
                        <div class="stat-value">{{ $totalFaculty ?? 0 }}</div>
                        <div class="stat-sub">{{ $programLabels ?? 'BSIS · BSIT · BIT-CT' }}</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-amber-600"></div>
                        <div class="stat-label">Subjects Plotted</div>
                        <div class="stat-value">{{ $subjectsPlotted ?? 0 }}</div>
                        <div class="stat-sub">of {{ $subjectsTotal ?? 0 }} total</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-red-600"></div>
                        <div class="stat-label">Avg. Faculty Load</div>
                        <div class="stat-value">{{ $avgFacultyLoad ?? 0 }}h</div>
                        <div class="stat-sub">FT max 30h · PT max 22h</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-green-600"></div>
                        <div class="stat-label">Schedules Approved</div>
                        <div class="stat-value">{{ $scheduledApprovedCount ?? 0 }}</div>
                        <div class="stat-sub">Pending: {{ $pendingDeptCount ?? 0 }}
                            dept{{ ($pendingDeptCount ?? 0) === 1 ? '' : 's' }}</div>
                    </div>
                </div>

                <div class="two-col">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Department Summary</div>
                                <div class="card-sub">Faculty load status per program</div>
                            </div>
                        </div>

                        @forelse($deptSummary ?? [] as $dept)
                            @php $c = $colorMap[$dept['color'] ?? 'slate'] ?? $colorMap['slate']; @endphp
                            <div class="workload-item">
                                <div class="workload-header">
                                    <div class="workload-name">{{ $dept['name'] }} — {{ $dept['count'] }} Faculty
                                    </div>
                                    <div class="workload-val {{ $c['text'] }}">{{ $dept['percent'] }}%</div>
                                </div>
                                <div class="workload-bar">
                                    <div class="workload-fill {{ $c['bar'] }}"
                                        style="width:{{ $dept['percent'] }}%"></div>
                                </div>
                            </div>
                        @empty
                            <div class="px-1 py-4 text-sm text-slate-400">No program data yet.</div>
                        @endforelse

                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">Overloaded Faculty</div>
                                <div class="workload-val text-red-600">{{ $overloadCount ?? 0 }}</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-red-600"
                                    style="width:{{ min(100, ($overloadCount ?? 0) * 10) }}%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Pending Approvals</div>
                                <div class="card-sub">Awaiting Dean's signature</div>
                            </div>
                            <a href="{{ route('dean.pending_approvals') }}"
                                class="text-[12px] font-semibold text-blue-600 hover:underline">View all</a>
                        </div>

                        @forelse($pendingApprovals ?? [] as $item)
                            <div class="approval-item">
                                <div class="approval-avatar" style="background:{{ $item['color'] ?? '#64748b' }};">
                                    {{ $item['initials'] ?? 'CH' }}</div>
                                <div class="approval-content">
                                    <div class="approval-name">{{ $item['title'] ?? 'Schedule' }}</div>
                                    <div class="approval-detail">{{ $item['detail'] ?? '' }}</div>
                                    <div class="approval-actions">
                                        <button type="button"
                                            class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-blue-600 text-white hover:bg-blue-700"
                                            onclick="approveSubmission('{{ $item['id'] }}', this)">Approve</button>
                                        <a href="{{ route('dean.pending_approvals.review', $item['id']) }}"
                                            class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200 inline-block">Review</a>
                                        <button type="button"
                                            class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-red-100 text-red-600 hover:bg-red-200"
                                            onclick="returnSubmission('{{ $item['id'] }}', this)">Return</button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="px-1 py-6 text-sm text-slate-400 text-center">
                                No pending schedule submissions.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <div>
                            <div class="card-title">My Recent Activity</div>
                            <div class="card-sub">Your latest sign-ins and account changes</div>
                        </div>
                    </div>
                    <div class="space-y-0">
                        @forelse($recentActivity ?? [] as $activity)
                            @php
                                $activityDetail = preg_replace('/^Role: [^;]+; /', '', (string) $activity->al_description);
                                $activityDetail = preg_replace('/; HTTP \d{3}$/', '', $activityDetail);
                                $activityTime = \Illuminate\Support\Carbon::parse($activity->al_created_at)->timezone(config('app.timezone'));
                                $activityIsLogin = in_array($activity->al_action, ['Logged in', 'Logged out'], true);
                            @endphp
                            <div class="flex items-start gap-3.5 border-b border-slate-100 py-3.5 last:border-b-0">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $activityIsLogin ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
                                    <i class="ti {{ $activity->al_action === 'Logged in' ? 'ti-login' : ($activity->al_action === 'Logged out' ? 'ti-logout' : 'ti-edit') }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                        <div class="text-[13px] font-semibold text-slate-700">{{ $activity->al_action }}</div>
                                        <time class="whitespace-nowrap text-[11px] font-medium text-slate-400" datetime="{{ $activityTime->toIso8601String() }}">{{ $activityTime->format('M j, Y · g:i A') }}</time>
                                    </div>
                                    <div class="mt-1 break-words text-[12px] text-slate-500">{{ $activityDetail ?: $activity->al_target_table }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-slate-400">No activity recorded yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        async function approveSubmission(id, btn) {
            if (!confirm('Approve this schedule submission?')) return;
            btn.disabled = true;
            try {
                const res = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    showToast(data.message || 'Failed to approve.');
                    btn.disabled = false;
                    return;
                }
                showToast(data.message || 'Schedule approved.');
                setTimeout(() => location.reload(), 500);
            } catch (e) {
                showToast('Failed to approve.');
                btn.disabled = false;
            }
        }

        async function returnSubmission(id, btn) {
            if (!confirm('Return this schedule to the chair?')) return;
            btn.disabled = true;
            try {
                const res = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) {
                    showToast(data.message || 'Failed to return.');
                    btn.disabled = false;
                    return;
                }
                showToast(data.message || 'Returned to chair.');
                setTimeout(() => location.reload(), 500);
            } catch (e) {
                showToast('Failed to return.');
                btn.disabled = false;
            }
        }
    </script>
</body>

</html>
