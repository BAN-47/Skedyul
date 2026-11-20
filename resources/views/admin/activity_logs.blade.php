<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Activity Logs</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
<div class="app-shell">
    @include('partials.admin_sidebar')
    <div class="app-main">
        @include('partials.admin_header', ['title' => 'System Activity Logs'])
        <div class="page-content">
            <div class="grid grid-cols-3 gap-3 mb-4">
                <div class="stat-card"><div class="stat-card-bar bg-blue-600"></div><div class="stat-label">Total Activities</div><div class="stat-value">{{ number_format($totalActivities) }}</div><div class="stat-sub">All recorded audit events</div></div>
                <div class="stat-card"><div class="stat-card-bar bg-green-600"></div><div class="stat-label">Today</div><div class="stat-value">{{ number_format($todayActivities) }}</div><div class="stat-sub">Events recorded today</div></div>
                <div class="stat-card"><div class="stat-card-bar bg-cyan-600"></div><div class="stat-label">Successful Logins</div><div class="stat-value">{{ number_format($loginActivities) }}</div><div class="stat-sub">Recorded sign-ins</div></div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Activity History</div>
                        <div class="card-sub">Search by user, action, route, details, IP address, or date</div>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.activity-logs') }}" class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input name="search" type="search" value="{{ request('search') }}" placeholder="Search user, action, details, or IP..." aria-label="Search activity logs" class="field-input w-full sm:max-w-sm">
                    <input name="date" type="date" value="{{ request('date') }}" aria-label="Filter activity by date" class="field-input w-full sm:w-44">
                    <select name="role" aria-label="Filter by role" class="field-input w-full sm:w-52">
                        <option value="">All roles</option>
                        <option value="system_admin" @selected(request('role') === 'system_admin')>Admin</option>
                        <option value="department_chair" @selected(request('role') === 'department_chair')>Department Chair</option>
                        <option value="dean" @selected(request('role') === 'dean')>Dean</option>
                        <option value="faculty" @selected(request('role') === 'faculty')>Faculty</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-secondary text-center">Clear</a>
                </form>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr>
                            @foreach(['Date & Time','User','Role','Action','Details','IP Address','Result'] as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                        @forelse($activityLogs as $log)
                            @php
                                $actor = $log->user;
                                $roleLabels = [
                                    'system_admin' => 'Admin',
                                    'department_chair' => 'Department Chair',
                                    'dean' => 'Dean',
                                    'faculty' => 'Faculty',
                                ];
                                $isFailed = str_starts_with((string) $log->al_action, 'Failed ');
                                $description = preg_replace('/^Role: [^;]+; /', '', (string) $log->al_description);
                                $description = preg_replace('/; HTTP \d{3}$/', '', $description);
                            @endphp
                            @php
                                $localDate = \Illuminate\Support\Carbon::parse($log->al_created_at)->timezone(config('app.timezone'));
                            @endphp
                            <tr>
                                <td class="whitespace-nowrap font-mono text-[11px] text-slate-500">{{ $localDate->format('M j, Y g:i A') }}</td>
                                <td class="font-semibold">{{ $actor?->usr_name ?? 'Deleted user' }}<div class="text-[11px] font-normal text-slate-400">{{ $actor?->usr_email ?? '' }}</div></td>
                                <td><span class="badge badge-grey">{{ $roleLabels[$actor?->usr_role ?? ''] ?? 'User' }}</span></td>
                                <td>{{ $log->al_action }}</td>
                                <td class="min-w-[220px] text-slate-500">
                                    <div>{{ $description ?: $log->al_target_table }}</div>
                                    @if($log->al_target_id)<div class="mt-1 font-mono text-[10px] text-slate-400">ID: {{ $log->al_target_id }}</div>@endif
                                </td>
                                <td class="font-mono text-xs text-slate-500">{{ $log->al_ip_address ?: '—' }}</td>
                                <td><span class="badge {{ $isFailed ? 'badge-red' : 'badge-green' }}">{{ $isFailed ? 'Warning' : 'Success' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-8 text-center text-slate-400">No activity matches these filters.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex min-w-0 flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="shrink-0 text-xs text-slate-400" aria-live="polite">
                        @if ($activityLogs->total())
                            Showing {{ number_format($activityLogs->firstItem()) }}–{{ number_format($activityLogs->lastItem()) }} of {{ number_format($activityLogs->total()) }} activities
                        @else
                            Showing 0 of 0 activities
                        @endif
                    </div>
                    <div class="min-w-0">{{ $activityLogs->links() }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
