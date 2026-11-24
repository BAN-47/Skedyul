<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Faculty Load Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    <div class="app-shell">

        @include('partials.chair_sidebar')

        <div class="app-main relative">

            <div class="topbar">
                <div class="topbar-title">Faculty Load</div>
                <div class="flex items-center gap-2.5">
                    <span class="badge badge-blue text-[11px]">
                        {{ $program->prog_code ?? ($department->dept_code ?? 'DEPT') }}
                        @if ($academicYear)
                            &middot; {{ $academicYear->ay_academic_year }}
                        @endif
                        @if ($semester)
                            &middot; {{ $semester->sem_name }}
                        @endif
                    </span>
                    <div class="relative flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-slate-100 text-slate-600 text-[13px] font-semibold cursor-pointer hover:bg-slate-200 transition"
                        id="notif-btn" onclick="toggleNotifPanel()">
                        Notifications
                    </div>
                </div>
            </div>

            <div class="hidden absolute top-[60px] right-7 w-[340px] max-h-[420px] overflow-y-auto bg-white rounded-2xl border border-slate-200 shadow-[0_20px_60px_rgba(0,0,0,.15)] z-50"
                id="notif-panel">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <span class="text-[14px] font-bold text-slate-900">Notifications</span>
                </div>
                <div class="px-4 py-6 text-center text-[13px] text-slate-400">No notifications yet.</div>
            </div>

            <div class="page-content">

                @if (session('success'))<div role="status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                @if ($errors->any())<div role="alert" class="mb-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif

                <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
                    <div>
                        <h1 class="text-xl font-bold text-slate-900">Faculty workload</h1>
                        <p class="mt-1 text-sm text-slate-500">Review your department’s faculty hours and workload limits.</p>
                    </div>
                    <form method="GET" action="{{ route('chair.faculty_load') }}" class="flex flex-col gap-1">
                        <label for="faculty-load-semester" class="text-xs font-semibold text-slate-600">
                            Academic year and semester
                        </label>
                        <select id="faculty-load-semester" name="semester" onchange="this.form.submit()"
                            class="min-w-[220px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800">
                            @foreach ($semesters as $option)
                                <option value="{{ $option->sem_id }}"
                                    @selected(($semester?->sem_id ?? '') === $option->sem_id)>
                                    {{ $option->label }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Faculty</p><p class="mt-1 text-2xl font-bold text-slate-900">{{ $facultyStats['total'] }}</p><p class="mt-1 text-xs text-slate-500">In your department</p></article>
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Scheduled hours</p><p class="mt-1 text-2xl font-bold text-blue-700">{{ number_format($facultyStats['scheduled_hours'], 1) }}h</p><p class="mt-1 text-xs text-slate-500">This semester’s department schedules</p></article>
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Needs attention</p><p class="mt-1 text-2xl font-bold {{ $facultyStats['attention'] ? 'text-amber-600' : 'text-emerald-700' }}">{{ $facultyStats['attention'] }}</p><p class="mt-1 text-xs text-slate-500">Near or over a workload limit</p></article>
                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Over limit</p><p class="mt-1 text-2xl font-bold {{ $facultyStats['over_limit'] ? 'text-rose-600' : 'text-emerald-700' }}">{{ $facultyStats['over_limit'] }}</p><p class="mt-1 text-xs text-slate-500">Day, Evening, or shared Part-time cap</p></article>
                </div>

                <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <input id="faculty-load-search" type="search" placeholder="Name, email, designation, or subject" aria-label="Search faculty loads" class="field-input w-full sm:max-w-xs">
                        <select id="faculty-load-employment-filter" class="field-input w-full sm:w-auto" aria-label="Filter by employment"><option value="all">All employment types</option><option value="full_time">Full-time</option><option value="part_time">Part-time</option></select>
                        <select id="faculty-load-designation-filter" class="field-input w-full sm:w-auto" aria-label="Filter by designation"><option value="all">All designations</option><option value="designated">Has designation</option><option value="none">No designation</option></select>
                        <select id="faculty-load-status-filter" class="field-input w-full sm:w-auto" aria-label="Filter by workload status"><option value="all">All workload statuses</option><option value="attention">Near or over limit</option><option value="available">Within limit</option><option value="no_load">No load</option></select>
                        <div class="ml-auto flex flex-wrap items-center gap-2 text-xs text-slate-500">
                        <label for="faculty-load-page-size">Rows</label>
                        <select id="faculty-load-page-size" class="rounded-lg border border-slate-200 bg-white px-2 py-1.5"><option value="10" selected>10</option><option value="25">25</option><option value="50">50</option></select>
                        <span id="faculty-load-page-info" aria-live="polite"></span>
                        <button type="button" id="faculty-load-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                        <button type="button" id="faculty-load-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                        </div>
                </div>

                <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1100px] text-left text-sm" id="faculty-load-table">
                            <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3">Faculty</th><th class="p-3">Rank / designation</th><th class="p-3">Employment</th><th class="p-3">Subjects</th><th class="p-3">Day</th><th class="p-3">Evening</th><th class="p-3">Total load</th><th class="p-3">Status</th></tr></thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($facultyLoad as $fl)
                                    <tr data-load-row data-employment="{{ $fl['employment'] }}" data-designated="{{ $fl['special_position'] ? '1' : '0' }}" data-load-status="{{ $fl['status_color'] === 'rose' ? 'attention' : ($fl['status_color'] === 'amber' ? 'attention' : ($fl['status'] === 'Within Limit' ? 'available' : 'no_load')) }}" data-search="{{ strtolower($fl['name'].' '.$fl['email'].' '.$fl['rank'].' '.$fl['employment'].' '.($fl['special_position'] ?? '').' '.$fl['subjects'].' '.$fl['status']) }}">
                                        <td>
                                            <div class="flex min-w-[200px] items-center gap-3">
                                                <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 text-sm font-bold text-blue-700">@if($fl['profile_image'])<img src="{{ asset('storage/'.$fl['profile_image']) }}" alt="" class="h-full w-full object-cover">@else{{ strtoupper(mb_substr($fl['name'], 0, 1)) }}@endif</span>
                                                <span class="min-w-0"><span class="block truncate font-semibold">{{ $fl['name'] }}</span><span class="block truncate text-xs text-slate-500">{{ $fl['email'] ?? 'No linked account' }}</span></span>
                                            </div>
                                        </td>
                                        <td class="p-3"><div>{{ $fl['rank'] ?: 'Faculty' }}</div><div class="text-xs text-slate-500">{{ $fl['special_position'] ?: 'No special designation' }}</div>
                                            <button type="button" class="mt-2 rounded-lg bg-blue-50 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-100" data-designation-edit data-id="{{ $fl['id'] }}" data-name="{{ $fl['name'] }}" data-position="{{ $fl['special_position'] ?? '' }}" data-employment="{{ $fl['employment'] }}">{{ $fl['special_position'] ? 'Edit designation' : 'Assign designation' }}</button>
                                        </td>
                                        <td class="p-3">{{ $fl['employment'] === 'part_time' ? 'Part-time' : 'Full-time' }}<div class="text-xs {{ $fl['is_active'] ? 'text-emerald-700' : 'text-slate-400' }}">{{ $fl['is_active'] ? 'Active account' : 'Inactive / no account' }}</div></td>
                                        <td class="max-w-[220px] p-3 text-slate-600">{{ $fl['subjects'] }}</td>
                                        <td class="p-3"><strong class="text-emerald-700">{{ number_format($fl['day']['total_hours'], 1) }}h</strong><span class="text-xs text-slate-400"> / {{ $fl['day']['max_hours'] }}h</span>@if($fl['employment'] === 'part_time')<div class="text-xs text-slate-400">Part of shared cap</div>@endif</td>
                                        <td class="p-3"><strong class="text-indigo-700">{{ number_format($fl['evening']['total_hours'], 1) }}h</strong><span class="text-xs text-slate-400"> / {{ $fl['evening']['max_hours'] }}h</span>@if($fl['employment'] === 'part_time')<div class="text-xs text-slate-400">Part of shared cap</div>@endif</td>
                                        <td class="min-w-[175px] p-3"><div class="font-semibold">{{ number_format($fl['total_hours'], 1) }}h <span class="text-xs font-normal text-slate-400">/ {{ $fl['total_max_hours'] }}h{{ $fl['employment'] === 'part_time' ? ' shared' : ' combined caps' }}</span></div><div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100"><div class="h-full rounded-full {{ $fl['status_color'] === 'rose' ? 'bg-rose-500' : ($fl['status_color'] === 'amber' ? 'bg-amber-500' : ($fl['status_color'] === 'emerald' ? 'bg-emerald-500' : 'bg-slate-300')) }}" style="width: {{ $fl['progress'] }}%"></div></div></td>
                                        <td class="p-3"><span class="badge {{ $fl['status_color'] === 'rose' ? 'badge-red' : ($fl['status_color'] === 'amber' ? 'badge-amber' : ($fl['status_color'] === 'emerald' ? 'badge-green' : 'badge-grey')) }}">{{ $fl['status'] }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-8 text-center text-slate-500">No faculty in this
                                            department yet.</td>
                                    </tr>
                                @endforelse
                                <tr id="faculty-load-no-results" class="hidden"><td colspan="8" class="p-8 text-center text-slate-500">No faculty match these filters.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div id="designation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="designation-modal-title" onclick="if(event.target===this)closeDesignationModal()">
        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl">
            <div class="flex items-start justify-between gap-3"><div><h2 id="designation-modal-title" class="text-lg font-bold text-slate-900">Assign special designation</h2><p id="designation-modal-person" class="mt-1 text-sm text-slate-500"></p></div><button type="button" class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600" onclick="closeDesignationModal()">Close</button></div>
            <form id="designation-form" method="POST" action="" class="mt-5">
                @csrf
                @method('PUT')
                <label for="designation-select" class="text-xs font-semibold uppercase tracking-wide text-slate-500">Special designation</label>
                <select id="designation-select" name="special_position" class="field-input mt-1 w-full">
                    <option value="">No special designation</option>
                    @foreach ($designations as $d)
                        <option value="{{ $d->name }}" data-max-hours="{{ $d->day_cap }}">{{ $d->name }}</option>
                    @endforeach
                </select>
                <div id="designation-cap-note" class="mt-3 rounded-lg bg-blue-50 p-3 text-sm text-blue-800"></div>
                <div class="mt-5 flex justify-end gap-2"><button type="button" class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600" onclick="closeDesignationModal()">Cancel</button><button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save designation</button></div>
            </form>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        function toggleNotifPanel() {
            document.getElementById('notif-panel').classList.toggle('hidden');
        }
        document.addEventListener('click', function(e) {
            const panel = document.getElementById('notif-panel');
            const btn = document.getElementById('notif-btn');
            if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
                panel.classList.add('hidden');
            }
        });

        (() => {
            const search = document.getElementById('faculty-load-search');
            const rows = [...document.querySelectorAll('[data-load-row]')];
            const noResults = document.getElementById('faculty-load-no-results');
            const info = document.getElementById('faculty-load-page-info');
            const previous = document.getElementById('faculty-load-prev');
            const next = document.getElementById('faculty-load-next');
            const pageSizeSelect = document.getElementById('faculty-load-page-size');
            const employmentFilter = document.getElementById('faculty-load-employment-filter');
            const designationFilter = document.getElementById('faculty-load-designation-filter');
            const statusFilter = document.getElementById('faculty-load-status-filter');
            let pageSize = Number(pageSizeSelect.value);
            let page = 0;

            function render() {
                const query = search.value.trim().toLowerCase();
                const employment = employmentFilter.value;
                const designation = designationFilter.value;
                const status = statusFilter.value;
                const matches = rows.filter(row => row.dataset.search.includes(query)
                    && (employment === 'all' || row.dataset.employment === employment)
                    && (designation === 'all' || (designation === 'designated' ? row.dataset.designated === '1' : row.dataset.designated === '0'))
                    && (status === 'all' || row.dataset.loadStatus === status));
                const pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
                page = Math.min(page, pageCount - 1);
                const start = page * pageSize;
                rows.forEach(row => { row.style.display = 'none'; });
                matches.slice(start, start + pageSize).forEach(row => { row.style.display = ''; });
                noResults.classList.toggle('hidden', matches.length > 0 || rows.length === 0);
                info.textContent = matches.length ? `Showing ${start + 1}–${Math.min(start + pageSize, matches.length)} of ${matches.length}` : 'Showing 0 of 0';
                previous.disabled = page === 0;
                next.disabled = page >= pageCount - 1;
            }

            search.addEventListener('input', () => { page = 0; render(); });
            employmentFilter.addEventListener('change', () => { page = 0; render(); });
            designationFilter.addEventListener('change', () => { page = 0; render(); });
            statusFilter.addEventListener('change', () => { page = 0; render(); });
            pageSizeSelect.addEventListener('change', () => {
                pageSize = Number(pageSizeSelect.value);
                page = 0;
                render();
            });
            previous.addEventListener('click', () => { page--; render(); });
            next.addEventListener('click', () => { page++; render(); });
            render();
        })();

        const designationModal = document.getElementById('designation-modal');
        const designationForm = document.getElementById('designation-form');
        const designationSelect = document.getElementById('designation-select');
        const designationCapNote = document.getElementById('designation-cap-note');
        let designationEmployment = 'full_time';
        function syncDesignationCap() {
            const option = designationSelect.selectedOptions[0];
            const positionCap = option?.dataset.maxHours || {{ \App\Services\ScheduleAssignmentService::TOTAL_MAX_HOURS }};
            designationCapNote.textContent = designationEmployment === 'part_time'
                ? `Part-time faculty have a shared {{ \App\Services\ScheduleAssignmentService::PART_TIME_MAX_HOURS }}-hour limit across Day and Evening. The selected designation does not replace this shared limit.`
                : `This designation sets the Day teaching limit to ${positionCap} hours. The Evening limit remains {{ \App\Services\ScheduleAssignmentService::EVENING_MAX_HOURS }} hours.`;
        }
        function openDesignationModal(button) {
            designationForm.action = `{{ url('/chair/faculty-load') }}/${button.dataset.id}/special-position`;
            document.getElementById('designation-modal-person').textContent = button.dataset.name;
            designationEmployment = button.dataset.employment;
            designationSelect.value = button.dataset.position || '';
            syncDesignationCap();
            designationModal.classList.remove('hidden');
            designationModal.classList.add('flex');
            designationSelect.focus();
        }
        function closeDesignationModal() { designationModal.classList.add('hidden'); designationModal.classList.remove('flex'); }
        document.querySelectorAll('[data-designation-edit]').forEach(button => button.addEventListener('click', () => openDesignationModal(button)));
        designationSelect.addEventListener('change', syncDesignationCap);
        document.addEventListener('keydown', event => { if (event.key === 'Escape') closeDesignationModal(); });

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3200);
        }
    </script>

</body>

</html>
