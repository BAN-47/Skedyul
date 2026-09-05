<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">
    @include('partials.admin_sidebar')
    <div class="app-main">

        {{-- TOPBAR --}}
        @include('partials.admin_header', ['title' => 'Technical Admin Reports'])


        {{-- FLASH --}}
        @if(session("success"))
            <script>document.addEventListener("DOMContentLoaded",()=>showToast("✅ {{ session("success") }}"));</script>
        @endif

        <div class="page-content">

            {{-- SEMESTER BADGE --}}
            @if($semester)
            <div class="flex items-center gap-2 mb-5">
                <span class="badge badge-blue">📅 {{ $semester->sem_name }}</span>
                <span class="text-[13px] text-slate-400">Current active semester</span>
            </div>
            @else
            <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-700 text-[13px]">
                ⚠️ No active semester found. Please activate a semester in Settings.
            </div>
            @endif

            {{-- TABS --}}
            <div class="flex gap-1 mb-5 bg-white border border-slate-200 rounded-xl p-1 w-fit">
                @foreach([
                    ['section', '🏫 By Section'],
                    ['teacher', '👨‍🏫 By Teacher'],
                    ['room',    '🚪 By Room'],
                ] as [$key, $label])
                <button
                    onclick="switchTab('{{ $key }}')"
                    id="tab-{{ $key }}"
                    class="px-5 py-2 rounded-lg text-[13px] font-semibold transition-all">
                    {{ $label }}
                </button>
                @endforeach
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY SECTION
            ═════════════════════════════════════════════ --}}
            <div id="panel-section" class="tab-panel">
                @if($bySection->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        No schedule data found for sections this semester.
                    </div>
                @else
                @foreach($bySection as $sectionName => $rows)
                @php $first = $rows->first(); @endphp
                <div class="card mb-4">
                    {{-- Section Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="card-title">{{ $sectionName }}</div>
                            <div class="card-sub">
                                {{ $first->prog_code ?? 'N/A' }} · Year {{ $first->sec_year_level ?? '' }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge badge-blue">{{ $rows->whereNotNull('subj_code')->count() }} subjects</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Faculty</th>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows->whereNotNull('subj_code') as $row)
                                <tr>
                                    <td><span class="font-mono text-[12px] text-slate-600">{{ $row->subj_code }}</span></td>
                                    <td class="font-semibold">{{ $row->subj_name }}</td>
                                    <td>{{ $row->faculty_name ?? '—' }}</td>
                                    <td>{{ $row->day ?? '—' }}</td>
                                    <td class="font-mono text-[12px]">
                                        @if($row->start_time && $row->end_time)
                                            {{ \Carbon\Carbon::parse($row->start_time)->format('g:i A') }}
                                            – {{ \Carbon\Carbon::parse($row->end_time)->format('g:i A') }}
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $row->room_name ?? '—' }}</td>
                                </tr>
                                @endforeach

                                @if($rows->whereNotNull('subj_code')->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-slate-400">No schedules plotted yet for this section.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY TEACHER
            ═════════════════════════════════════════════ --}}
            <div id="panel-teacher" class="tab-panel hidden">
                @if($byTeacher->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        No faculty schedule data found for this semester.
                    </div>
                @else
                @foreach($byTeacher as $facultyName => $rows)
                @php
                    $first = $rows->first();
                    $totalHours = $first->total_hours ?? 0;
                    $maxHours = 30;
                    $pct = min(100, round(($totalHours / $maxHours) * 100));
                    $barColor = $totalHours > 30 ? 'bg-red-600' : ($totalHours >= 24 ? 'bg-amber-500' : 'bg-green-600');
                    $textColor = $totalHours > 30 ? 'text-red-600' : ($totalHours >= 24 ? 'text-amber-500' : 'text-green-600');
                @endphp
                <div class="card mb-4">
                    {{-- Faculty Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold text-[15px] flex-shrink-0">
                                {{ strtoupper(substr($facultyName, 0, 1)) }}
                            </div>
                            <div>
                                <div class="card-title">{{ $facultyName }}</div>
                                <div class="card-sub">
                                    {{ $first->dept_code ?? 'N/A' }} ·
                                    <span class="badge {{ $first->fac_employment_type === 'full_time' ? 'badge-blue' : 'badge-grey' }}" style="font-size:10px;padding:2px 7px;">
                                        {{ $first->fac_employment_type === 'full_time' ? 'Full-time' : 'Part-time' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-[11px] text-slate-400 mb-1">Total Load</div>
                            <div class="text-[20px] font-extrabold {{ $textColor }}">
                                {{ $totalHours }} <span class="text-[13px] font-normal text-slate-400">/ 30 hrs</span>
                            </div>
                        </div>
                    </div>

                    {{-- Workload bar --}}
                    <div class="workload-bar mb-4">
                        <div class="workload-fill {{ $barColor }}" style="width:{{ $pct }}%"></div>
                    </div>

                    @if($totalHours > 30)
                    <div class="mb-4 px-3 py-2 rounded-lg bg-red-50 border border-red-200 text-red-700 text-[12px] font-semibold">
                        ⚠️ Overloaded — {{ $totalHours - 30 }} hour(s) over the 30-hour limit.
                    </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Section</th>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Room</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows->whereNotNull('subj_code') as $row)
                                <tr>
                                    <td><span class="font-mono text-[12px] text-slate-600">{{ $row->subj_code }}</span></td>
                                    <td class="font-semibold">{{ $row->subj_name }}</td>
                                    <td>{{ $row->sec_name ?? '—' }}</td>
                                    <td>{{ $row->day ?? '—' }}</td>
                                    <td class="font-mono text-[12px]">
                                        @if($row->start_time && $row->end_time)
                                            {{ \Carbon\Carbon::parse($row->start_time)->format('g:i A') }}
                                            – {{ \Carbon\Carbon::parse($row->end_time)->format('g:i A') }}
                                        @else —
                                        @endif
                                    </td>
                                    <td>{{ $row->room_name ?? '—' }}</td>
                                </tr>
                                @endforeach

                                @if($rows->whereNotNull('subj_code')->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-slate-400">No schedule assigned yet.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
                @endif
            </div>

            {{-- ════════════════════════════════════════════
                 TAB: BY ROOM
            ═════════════════════════════════════════════ --}}
            <div id="panel-room" class="tab-panel hidden">
                @if($byRoom->isEmpty())
                    <div class="card text-center py-12 text-slate-400">
                        No rooms found in the system.
                    </div>
                @else
                @foreach($byRoom as $roomName => $rows)
                @php
                    $first = $rows->first();
                    $bookingCount = $rows->whereNotNull('day')->count();
                    $isAvailable = $first->room_is_available;
                @endphp
                <div class="card mb-4">
                    {{-- Room Header --}}
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <div class="card-title">{{ $roomName }}</div>
                            <div class="card-sub flex items-center gap-2 mt-1">
                                <span class="badge badge-grey">{{ ucfirst($first->room_type ?? 'N/A') }}</span>
                                <span class="text-slate-400">Capacity: {{ $first->room_capacity ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="badge {{ $isAvailable ? 'badge-green' : 'badge-red' }}">
                                {{ $isAvailable ? 'Available' : 'Unavailable' }}
                            </span>
                            <span class="badge badge-blue">{{ $bookingCount }} booking{{ $bookingCount !== 1 ? 's' : '' }}</span>
                        </div>
                    </div>

                    @if($bookingCount > 0)
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Section</th>
                                    <th>Faculty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows->whereNotNull('day') as $row)
                                <tr>
                                    <td class="font-semibold">{{ $row->day }}</td>
                                    <td class="font-mono text-[12px]">
                                        {{ \Carbon\Carbon::parse($row->start_time)->format('g:i A') }}
                                        – {{ \Carbon\Carbon::parse($row->end_time)->format('g:i A') }}
                                    </td>
                                    <td>
                                        <span class="font-mono text-[12px] text-slate-500">{{ $row->subj_code }}</span>
                                        <div class="text-[12px] text-slate-600">{{ $row->subj_name }}</div>
                                    </td>
                                    <td>{{ $row->sec_name ?? '—' }}</td>
                                    <td>{{ $row->faculty_name ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-6 text-[13px] text-slate-400">No bookings for this room this semester.</div>
                    @endif
                </div>
                @endforeach
                @endif
            </div>

        </div>{{-- end page-content --}}
    </div>{{-- end app-main --}}
</div>{{-- end app-shell --}}

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── TABS ──────────────────────────────────────────────────────────────────
const TABS = ['section','teacher','room'];

function switchTab(active) {
    TABS.forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== active);
        const btn = document.getElementById('tab-' + t);
        if (t === active) {
            btn.classList.add('bg-blue-600','text-white','shadow-sm');
            btn.classList.remove('text-slate-500','hover:bg-slate-100');
        } else {
            btn.classList.remove('bg-blue-600','text-white','shadow-sm');
            btn.classList.add('text-slate-500','hover:bg-slate-100');
        }
    });
    localStorage.setItem('reports_tab', active);
}

// Restore last active tab
const savedTab = localStorage.getItem('reports_tab') || 'section';
switchTab(savedTab);

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
    const t = document.getElementById('toast');
    document.getElementById('toast-msg').textContent = msg;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3000);
}

// ── PRINT ──────────────────────────────────────────────────────────────────
function printReport() { window.print(); }

// ── EXPORT CSV ────────────────────────────────────────────────────────────
function exportCSV() {
    const activeTab = localStorage.getItem('reports_tab') || 'section';
    const panel = document.getElementById('panel-' + activeTab);
    const tables = panel.querySelectorAll('table');
    if (!tables.length) { showToast('No data to export.'); return; }

    let csv = '';
    tables.forEach(table => {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            const cols = [...row.querySelectorAll('th,td')].map(c => `"${c.innerText.trim()}"`);
            csv += cols.join(',') + '\n';
        });
        csv += '\n';
    });

    const blob = new Blob([csv], { type: 'text/csv' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `skedyul-report-by-${activeTab}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    showToast('Report exported as CSV!');
}
</script>
</body>
</html>