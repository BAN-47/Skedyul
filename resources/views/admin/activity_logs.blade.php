<!DOCTYPE html>
<html lang="en">
<head>
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

                <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center">
                    <input id="activity-search" type="search" placeholder="Search user, action, details, or IP..." aria-label="Search activity logs" class="field-input w-full sm:max-w-sm">
                    <input id="activity-date-filter" type="date" aria-label="Filter activity by date" class="field-input w-full sm:w-44">
                    <select id="activity-role-filter" aria-label="Filter by role" class="field-input w-full sm:w-52">
                        <option value="">All roles</option>
                        <option value="system_admin">Admin</option>
                        <option value="department_chair">Department Chair</option>
                        <option value="dean">Dean</option>
                        <option value="faculty">Faculty</option>
                    </select>
                    <button type="button" id="activity-clear-filters" class="btn btn-secondary">Clear</button>
                </div>

                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead><tr>
                            @foreach(['Date & Time','User','Role','Action','Details','IP Address','Result'] as $heading)
                                <th>{{ $heading }}</th>
                            @endforeach
                        </tr></thead>
                        <tbody>
                        @foreach($activityLogs as $log)
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
                                $searchable = strtolower(implode(' ', array_filter([
                                    $actor?->usr_name,
                                    $actor?->usr_email,
                                    $roleLabels[$actor?->usr_role ?? ''] ?? 'User',
                                    $log->al_action,
                                    $description,
                                    $log->al_target_table,
                                    $log->al_target_id,
                                    $log->al_ip_address,
                                ])));
                            @endphp
                            <tr data-activity-row data-role="{{ $actor?->usr_role ?? '' }}" data-date="{{ $localDate->format('Y-m-d') }}" data-search="{{ $searchable }}">
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
                        @endforeach
                        <tr id="activity-no-results" hidden><td colspan="7" class="py-8 text-center text-slate-400">No activity matches these filters.</td></tr>
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div id="activity-page-info" class="text-xs text-slate-400" aria-live="polite"></div>
                    <nav class="flex items-center gap-1.5" aria-label="Activity log pagination">
                        <button type="button" id="activity-prev" class="rounded-xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-500 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">← Prev</button>
                        <div id="activity-page-numbers" class="flex items-center gap-1.5"></div>
                        <button type="button" id="activity-next" class="rounded-xl bg-slate-50 px-4 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 disabled:cursor-not-allowed disabled:opacity-40">Next →</button>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(() => {
    const rows = [...document.querySelectorAll('[data-activity-row]')];
    const search = document.getElementById('activity-search');
    const roleFilter = document.getElementById('activity-role-filter');
    const dateFilter = document.getElementById('activity-date-filter');
    const clearFilters = document.getElementById('activity-clear-filters');
    const pageInfo = document.getElementById('activity-page-info');
    const pageNumbers = document.getElementById('activity-page-numbers');
    const previous = document.getElementById('activity-prev');
    const next = document.getElementById('activity-next');
    const noResults = document.getElementById('activity-no-results');
    const pageSize = 10;
    let currentPage = 1;
    const pageButton = 'min-w-[48px] rounded-xl px-4 py-3 text-sm font-semibold transition';

    function renderPage() {
        const query = search.value.trim().toLowerCase();
        const selectedRole = roleFilter.value;
        const selectedDate = dateFilter.value;
        const matches = rows.filter(row =>
            (!query || row.dataset.search.includes(query)) &&
            (!selectedRole || row.dataset.role === selectedRole) &&
            (!selectedDate || row.dataset.date === selectedDate)
        );
        const totalPages = Math.max(1, Math.ceil(matches.length / pageSize));
        currentPage = Math.min(Math.max(currentPage, 1), totalPages);
        const start = (currentPage - 1) * pageSize;
        rows.forEach(row => { row.hidden = true; });
        matches.slice(start, start + pageSize).forEach(row => { row.hidden = false; });
        noResults.hidden = matches.length > 0;

        pageInfo.textContent = matches.length
            ? `Showing ${start + 1}–${Math.min(start + pageSize, matches.length)} of ${matches.length} activities`
            : 'Showing 0 of 0 activities';
        previous.disabled = currentPage === 1;
        next.disabled = currentPage === totalPages || matches.length === 0;
        pageNumbers.replaceChildren();

        for (let page = 1; page <= totalPages; page++) {
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = String(page);
            button.setAttribute('aria-label', `Page ${page}`);
            button.setAttribute('aria-current', page === currentPage ? 'page' : 'false');
            button.className = `${pageButton} ${page === currentPage ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'}`;
            button.addEventListener('click', () => { currentPage = page; renderPage(); });
            pageNumbers.appendChild(button);
        }
    }

    search.addEventListener('input', () => { currentPage = 1; renderPage(); });
    roleFilter.addEventListener('change', () => { currentPage = 1; renderPage(); });
    dateFilter.addEventListener('change', () => { currentPage = 1; renderPage(); });
    clearFilters.addEventListener('click', () => {
        search.value = '';
        roleFilter.value = '';
        dateFilter.value = '';
        currentPage = 1;
        renderPage();
        search.focus();
    });
    previous.addEventListener('click', () => { currentPage--; renderPage(); });
    next.addEventListener('click', () => { currentPage++; renderPage(); });
    renderPage();
})();
</script>
</body>
</html>
