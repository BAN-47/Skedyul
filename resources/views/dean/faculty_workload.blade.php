<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Faculty Workload</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dean/faculty_workload.css') }}">
</head>

<body>

    <div class="screen active" style="display:flex;">
        @include('partials.dean_sidebar')

        <!-- MAIN -->
        <div class="main">
            <div class="topbar">
                <div class="topbar-title">Faculty Workload</div>
                <div class="topbar-actions">
                    <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export PDF</button>
                    <button class="topbar-btn btn-secondary"
                        onclick="showToast('No new notifications.')">Notifications</button>
                </div>
            </div>

            <div id="page-faculty" class="page active">

                <!-- Page header -->
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px;">
                    <div>
                        <div style="font-size:20px;font-weight:800;color:var(--text);">Faculty Workload Overview</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:3px;">
                            AY {{ $academicYear->ay_year_label ?? 'N/A' }} · {{ $semester->sem_name ?? 'N/A' }}
                        </div>
                    </div>
                </div>

                <!-- Stat cards -->
                <div class="stat-grid" style="grid-template-columns:repeat(4,1fr);margin-bottom:24px;">
                    <div class="stat-card" style="--accent:#2563eb">
                        <div class="stat-label">Total Faculty</div>
                        <div class="stat-value">{{ $totalFaculty }}</div>
                        <div class="stat-sub">Across all departments</div>
                    </div>
                    <div class="stat-card" style="--accent:#16a34a">
                        <div class="stat-label">OK Load</div>
                        <div class="stat-value">{{ $okCount }}</div>
                        <div class="stat-sub">Within safe range</div>
                    </div>
                    <div class="stat-card" style="--accent:#d97706">
                        <div class="stat-label">Near Max</div>
                        <div class="stat-value">{{ $nearMaxCount }}</div>
                        <div class="stat-sub">3h or less remaining</div>
                    </div>
                    <div class="stat-card" style="--accent:#dc2626">
                        <div class="stat-label">Overloaded</div>
                        <div class="stat-value">{{ $overloadCount }}</div>
                        <div class="stat-sub">Exceeds 30h limit</div>
                    </div>
                </div>

                <!-- Overload alert -->
                @if ($overloadedFaculty->count() > 0)
                    <div class="conflict-alert" style="margin-bottom:20px;">
                        <div class="conflict-alert-text">
                            @foreach ($overloadedFaculty as $i => $of)
                                <strong>Overload Detected — {{ $of['name'] }}</strong>
                                Currently assigned {{ $of['hours'] }}h/30h maximum. The Department Chair should
                                reassign one subject to resolve this.
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Faculty workload table -->
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Faculty Load Summary</div>
                            <div class="card-sub">All faculty across all departments</div>
                        </div>
                        <input class="field-input" placeholder="Search faculty..."
                            style="width:200px;padding:8px 12px;font-size:13px;" oninput="filterFaculty(this.value)">
                    </div>
                    <div class="table-wrap">
                        <table id="faculty-table">
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Department</th>
                                    <th>Employment</th>
                                    <th>Subjects</th>
                                    <th>Total Load</th>
                                    <th>Load Bar</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($facultyRows as $row)
                                    <tr @if ($row['status'] === 'Overload') style="background:#fff5f5;" @endif>
                                        <td><b>{{ $row['name'] }}</b></td>
                                        <td>{{ $row['department'] }}</td>
                                        <td>{{ $row['employment'] }}</td>
                                        <td style="font-size:12px;color:var(--text2);">{{ $row['subjects'] }}</td>
                                        <td><span
                                                style="font-family:var(--mono);font-weight:700;color:var(--{{ $row['statusColor'] }});">{{ $row['hours'] }}h</span>
                                        </td>
                                        <td style="min-width:120px;">
                                            <div class="workload-bar">
                                                <div class="workload-fill"
                                                    style="width:{{ $row['percent'] }}%;background:var(--{{ $row['statusColor'] }});">
                                                </div>
                                            </div>
                                            <div
                                                style="font-size:10px;color:{{ $row['status'] === 'Overload' ? 'var(--red)' : 'var(--text3)' }};margin-top:2px;{{ $row['status'] === 'Overload' ? 'font-weight:600;' : '' }}">
                                                {{ $row['hours'] }}/30h{{ $row['status'] === 'Overload' ? ' — OVER' : '' }}
                                            </div>
                                        </td>
                                        <td><span
                                                class="badge badge-{{ $row['statusColor'] }}">{{ $row['status'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7"
                                            style="text-align:center;padding:24px;color:var(--text3);font-size:13px;">
                                            No faculty records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- EXPORT MODAL -->
    <div class="modal-overlay" id="modal-export">
        <div class="modal" style="width:440px;">
            <div class="modal-header">
                <div class="modal-title">Export Report</div>
                <button class="modal-close" onclick="closeModal('modal-export')">✕</button>
            </div>
            <div class="modal-body">
                <div class="field-group" style="margin-bottom:14px;">
                    <label class="field-label">Report Type</label>
                    <select class="field-select">
                        <option>Faculty Workload Report</option>
                        <option>Master Schedule</option>
                        <option>Faculty Deployment Report</option>
                        <option>Department Summary</option>
                    </select>
                </div>
                <div class="field-group" style="margin-bottom:14px;">
                    <label class="field-label">Department</label>
                    <select class="field-select">
                        <option>All Departments</option>
                        <option>BSIS</option>
                        <option>BSIT</option>
                        <option>BIT-CT</option>
                    </select>
                </div>
                <div class="field-group">
                    <label class="field-label">Format</label>
                    <select class="field-select">
                        <option>PDF</option>
                        <option>Excel (.xlsx)</option>
                        <option>Word (.docx)</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
                <button class="topbar-btn btn-primary"
                    onclick="closeModal('modal-export');showToast('Report exported successfully!')">Download</button>
            </div>
        </div>
    </div>

    <!-- TOAST -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        // ── MODALS ─────────────────────────────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.remove('open');
            });
        });

        // ── TOAST ──────────────────────────────────────────────────────────────────────
        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        // ── SEARCH FILTER ──────────────────────────────────────────────────────────────
        function filterFaculty(query) {
            const q = query.toLowerCase();
            document.querySelectorAll('#faculty-table tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
            });
        }
    </script>
</body>

</html>
