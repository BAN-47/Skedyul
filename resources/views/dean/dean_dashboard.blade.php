<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.favicon')
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
            <div class="topbar">
                <div class="topbar-title">Dean Dashboard</div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('dean.schedule_reports') }}" class="btn btn-primary">Export Report</a>
                    <a href="{{ route('dean.pending_approvals') }}" class="btn btn-secondary flex items-center gap-1.5">
                        Pending Approvals
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
                        <div class="text-[22px] font-extrabold">
                            Hello, Dean {{ $greetingName ?? 'Dean' }}
                        </div>
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
                        <div class="stat-sub">Day follows designation · Evening max 30h</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-green-600"></div>
                        <div class="stat-label">Schedules Approved</div>
                        <div class="stat-value">{{ $scheduledApprovedCount ?? 0 }}</div>
                        <div class="stat-sub">
                            Pending: {{ $pendingDeptCount ?? 0 }}
                            dept{{ ($pendingDeptCount ?? 0) === 1 ? '' : 's' }}
                        </div>
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
                            @php
                                $c = $colorMap[$dept['color'] ?? 'slate'] ?? $colorMap['slate'];
                            @endphp
                            <div class="workload-item">
                                <div class="workload-header">
                                    <div class="workload-name">
                                        {{ $dept['name'] }} — {{ $dept['count'] }} Faculty
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
                                <div class="card-sub">
                                    Showing {{ count($pendingApprovals ?? []) }} of {{ $pendingDeptCount ?? 0 }}
                                    pending
                                </div>
                            </div>
                            <a href="{{ route('dean.pending_approvals') }}"
                                class="topbar-btn btn-primary px-3 py-2 text-xs">
                                Go to Pending Approvals
                            </a>
                        </div>

                        @forelse($pendingApprovals ?? [] as $item)
                            <div class="approval-item">
                                <div class="approval-avatar" style="background:{{ $item['color'] ?? '#64748b' }};">
                                    {{ $item['initials'] ?? 'CH' }}
                                </div>
                                <div class="approval-content">
                                    <div class="approval-name">{{ $item['title'] ?? 'Schedule' }}</div>
                                    <div class="approval-detail">{{ $item['detail'] ?? '' }}</div>
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
                            <div class="card-title">Campus Schedule Rhythm</div>
                            <div class="card-sub">Active classes by weekday, separated by shift</div>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] font-semibold text-slate-500">
                            <span class="inline-flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-sm bg-blue-600"></i>Day</span>
                            <span class="inline-flex items-center gap-1"><i class="h-2.5 w-2.5 rounded-sm bg-cyan-500"></i>Evening</span>
                        </div>
                    </div>
                    @php $scheduleDayMax = max(1, $weeklyScheduleCounts->max(fn ($item) => $item['day_count'] + $item['evening_count'])); @endphp
                    <div class="grid grid-cols-7 gap-3 pt-2" role="img" aria-label="Campus classes by weekday and shift">
                        @foreach($weeklyScheduleCounts as $item)
                            @php $dayHeight = $item['day_count'] ? max(7, round($item['day_count'] / $scheduleDayMax * 100)) : 0; $eveningHeight = $item['evening_count'] ? max(7, round($item['evening_count'] / $scheduleDayMax * 100)) : 0; @endphp
                            <div class="flex h-36 flex-col items-center justify-end gap-2 rounded-xl bg-slate-50 px-2 pt-2">
                                <span class="text-xs font-bold text-slate-600">{{ $item['day_count'] + $item['evening_count'] }}</span>
                                <div class="flex h-24 w-full items-end justify-center gap-1">
                                    <div class="w-1/3 rounded-t-md bg-blue-600" title="Day: {{ $item['day_count'] }}" style="height:{{ $dayHeight }}%"></div>
                                    <div class="w-1/3 rounded-t-md bg-cyan-500" title="Evening: {{ $item['evening_count'] }}" style="height:{{ $eveningHeight }}%"></div>
                                </div>
                                <span class="text-[10px] font-semibold text-slate-500">{{ $item['day'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <div>
                            <div class="card-title">My Recent Activity</div>
                            <div class="card-sub">Your latest sign-ins and account changes</div>
                        </div>
                        <div id="dean-activity-pagination" class="flex items-center gap-2 text-xs text-slate-500">
                            <span id="dean-activity-page-info" aria-live="polite"></span>
                            <button type="button" id="dean-activity-prev" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                            <button type="button" id="dean-activity-next" class="rounded-lg border border-slate-200 bg-white px-2 py-1 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                        </div>
                    </div>

                    <div class="space-y-0">
                        @forelse($recentActivity ?? [] as $activity)
                            @php
                                $activityDetail = preg_replace(
                                    '/^Role: [^;]+; /',
                                    '',
                                    (string) $activity->al_description,
                                );
                                $activityDetail = preg_replace('/; HTTP \d{3}$/', '', $activityDetail);
                                $activityTime = \Illuminate\Support\Carbon::parse($activity->al_created_at)->timezone(
                                    config('app.timezone'),
                                );
                                $activityIsLogin = in_array($activity->al_action, ['Logged in', 'Logged out'], true);
                            @endphp

                            <div data-dean-activity class="flex items-start gap-3.5 border-b border-slate-100 py-3.5 last:border-b-0">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full {{ $activityIsLogin ? 'bg-blue-50 text-blue-600' : 'bg-slate-100 text-slate-600' }}">
                                    <i
                                        class="ti {{ $activity->al_action === 'Logged in' ? 'ti-login' : ($activity->al_action === 'Logged out' ? 'ti-logout' : 'ti-edit') }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1">
                                        <div class="text-[13px] font-semibold text-slate-700">
                                            {{ $activity->al_action }}
                                        </div>
                                        <time class="whitespace-nowrap text-[11px] font-medium text-slate-400"
                                            datetime="{{ $activityTime->toIso8601String() }}">
                                            {{ $activityTime->format('M j, Y · g:i A') }}
                                        </time>
                                    </div>
                                    <div class="mt-1 break-words text-[12px] text-slate-500">
                                        {{ $activityDetail ?: $activity->al_target_table }}
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-sm text-slate-400">
                                No activity recorded yet.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF = document.querySelector('meta[name="csrf-token"]').content;

        (() => {
            const rows = [...document.querySelectorAll('[data-dean-activity]')];
            const pagination = document.getElementById('dean-activity-pagination');
            const info = document.getElementById('dean-activity-page-info');
            const previous = document.getElementById('dean-activity-prev');
            const next = document.getElementById('dean-activity-next');
            const pageSize = 4;
            let page = 0;
            if (!pagination || !info || !previous || !next) return;
            pagination.classList.toggle('hidden', rows.length <= pageSize);

            function renderPage() {
                const pageCount = Math.max(1, Math.ceil(rows.length / pageSize));
                page = Math.min(Math.max(page, 0), pageCount - 1);
                const start = page * pageSize;
                rows.forEach((row, index) => row.classList.toggle('hidden', index < start || index >= start + pageSize));
                info.textContent = rows.length ? `Showing ${start + 1}–${Math.min(start + pageSize, rows.length)} of ${rows.length}` : 'No activity';
                previous.disabled = page === 0;
                next.disabled = page >= pageCount - 1;
            }

            previous.addEventListener('click', () => { page--; renderPage(); });
            next.addEventListener('click', () => { page++; renderPage(); });
            renderPage();
        })();

        function showToast(msg) {
            const toast = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }

        async function approveSubmission(id, button) {
            if (!confirm('Approve this schedule submission?')) return;

            button.disabled = true;

            try {
                const response = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showToast(data.message || 'Failed to approve.');
                    button.disabled = false;
                    return;
                }

                showToast(data.message || 'Schedule approved.');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                showToast('Failed to approve.');
                button.disabled = false;
            }
        }

        async function returnSubmission(id, button) {
            if (!confirm('Return this schedule to the chair?')) return;

            button.disabled = true;

            try {
                const response = await fetch(`{{ url('/dean/pending-approvals') }}/${id}/return`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    showToast(data.message || 'Failed to return.');
                    button.disabled = false;
                    return;
                }

                showToast(data.message || 'Returned to chair.');
                setTimeout(() => location.reload(), 500);
            } catch (error) {
                showToast('Failed to return.');
                button.disabled = false;
            }
        }
    </script>
</body>

</html>
