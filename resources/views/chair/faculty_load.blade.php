<!DOCTYPE html>
<html lang="en">

<head>
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
                <div class="topbar-title">Faculty Load Management</div>
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

                <div class="flex items-start justify-between mb-6">
                    <div>
                        <div class="text-[22px] font-extrabold">Faculty Load Management</div>
                        <div class="text-[13px] text-slate-400 mt-0.5">
                            {{ $department->dept_name ?? 'Department' }}
                            @if ($program)
                                &middot; {{ $program->prog_code }}
                            @endif
                            &middot; Full-time max
                            {{ \App\Http\Controllers\Chair\ChairFacultyLoadController::FULL_TIME_MAX_UNITS }}u
                            &middot; Part-time max
                            {{ \App\Http\Controllers\Chair\ChairFacultyLoadController::PART_TIME_MAX_UNITS }}u
                        </div>
                    </div>
                </div>

                <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <input id="faculty-load-search" type="search" placeholder="Search faculty, course, or status..." aria-label="Search faculty loads"
                        class="w-full max-w-sm rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">
                    <div class="flex items-center gap-3 text-xs text-slate-500">
                        <span id="faculty-load-page-info" aria-live="polite"></span>
                        <button type="button" id="faculty-load-prev" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                        <button type="button" id="faculty-load-next" class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                    </div>
                </div>

                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="data-table" id="faculty-load-table">
                            <thead>
                                <tr>
                                    @foreach (['Faculty', 'Employment', 'Course Code', 'Total Units', 'Units Left', 'Status'] as $h)
                                        <th>{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($facultyLoad as $fl)
                                    <tr data-load-row data-search="{{ strtolower($fl['name'].' '.$fl['employment'].' '.$fl['subjects'].' '.$fl['status_label']) }}">
                                        <td class="font-semibold">{{ $fl['name'] }}</td>
                                        <td>{{ $fl['employment'] === 'part_time' ? 'Part-time' : 'Full-time' }}</td>
                                        <td class="text-slate-500">{{ $fl['subjects'] }}</td>
                                        <td><span class="font-mono font-bold">{{ $fl['total_units'] }}u</span></td>
                                        <td>
                                            <span class="badge {{ $fl['status_badge'] }}">{{ $fl['remaining'] }}u
                                                left</span>
                                            <span class="text-[10px] text-slate-400 ml-1">/
                                                {{ $fl['max_units'] }}u</span>
                                        </td>
                                        <td><span
                                                class="badge {{ $fl['status_badge'] }}">{{ $fl['status_label'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-8 text-slate-400">No faculty in this
                                            department yet.</td>
                                    </tr>
                                @endforelse
                                <tr id="faculty-load-no-results" class="hidden">
                                    <td colspan="6" class="text-center py-8 text-slate-400">No faculty loads match your search.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
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
            const pageSize = 10;
            let page = 0;

            function render() {
                const query = search.value.trim().toLowerCase();
                const matches = rows.filter(row => row.dataset.search.includes(query));
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
            previous.addEventListener('click', () => { page--; render(); });
            next.addEventListener('click', () => { page++; render(); });
            render();
        })();

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3200);
        }
    </script>

</body>

</html>
