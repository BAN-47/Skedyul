<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Dean Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    <div class="app-shell">

        @include('partials.dean_sidebar')

        <!-- ══════════ MAIN ══════════ -->
        <div class="app-main">

            @include('partials.dean_header', ['title' => 'Dean Dashboard'])

            <!-- ══════════ DASHBOARD PAGE ══════════ -->
            <div class="page-content">

                <!-- Page heading -->
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex-1">
                        <div class="text-[22px] font-extrabold">Good morning, Dean Villaceran</div>
                        <div class="text-[13px] text-slate-400 mt-0.5">AY 2025–2026 · 1st Semester · CCICT Overview</div>
                    </div>
                    <span class="badge badge-teal">Scheduling Active</span>
                </div>

                <!-- Stat Cards -->
                <div class="stat-grid">
                    <div class="stat-card">
                        <div class="stat-card-bar bg-cyan-600"></div>
                        <div class="stat-label">Total Faculty</div>
                        <div class="stat-value">19</div>
                        <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-amber-600"></div>
                        <div class="stat-label">Subjects Plotted</div>
                        <div class="stat-value">54</div>
                        <div class="stat-sub">of 58 total</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-red-600"></div>
                        <div class="stat-label">Avg. Faculty Load</div>
                        <div class="stat-value">21h</div>
                        <div class="stat-sub">of 30h max</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-bar bg-green-600"></div>
                        <div class="stat-label">Schedules Approved</div>
                        <div class="stat-value">3</div>
                        <div class="stat-sub">Pending: 3 depts</div>
                    </div>
                </div>

                <!-- Department Summary + Pending Approvals -->
                <div class="two-col">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Department Summary</div>
                                <div class="card-sub">Faculty load status per department</div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">BSIS — 8 Faculty</div>
                                <div class="workload-val text-green-600">87%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-green-600" style="width:87%"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">BSIT — 7 Faculty</div>
                                <div class="workload-val text-amber-600">72%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-amber-600" style="width:72%"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">BIT-CT — 4 Faculty</div>
                                <div class="workload-val text-cyan-600">60%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-cyan-600" style="width:60%"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">Overloaded Faculty</div>
                                <div class="workload-val text-red-600">2</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill bg-red-600" style="width:11%"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Pending Approvals</div>
                                <div class="card-sub">Awaiting Dean's signature</div>
                            </div>
                        </div>
                        <div class="approval-item">
                            <div class="approval-avatar" style="background:#d97706;">RT</div>
                            <div class="approval-content">
                                <div class="approval-name">BSIS Schedule — 1st Sem AY 2025–26</div>
                                <div class="approval-detail">Submitted by Chair Rodrigo Tan · 8 faculty · 23 sections ·
                                    0 conflicts</div>
                                <div class="approval-actions">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-blue-600 text-white hover:bg-blue-700"
                                        onclick="showToast('BSIS Schedule approved!')">Approve</button>
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200"
                                        onclick="openModal('modal-review')">Review</button>
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-red-100 text-red-600 hover:bg-red-200"
                                        onclick="showToast('Returned.')">Return</button>
                                </div>
                            </div>
                        </div>
                        <div class="approval-item">
                            <div class="approval-avatar" style="background:#7c3aed;">MC</div>
                            <div class="approval-content">
                                <div class="approval-name">BSIT Schedule — 1st Sem AY 2025–26</div>
                                <div class="approval-detail">Submitted by Chair Maria Cruz · 7 faculty · 19 sections ·
                                    1 conflict flagged</div>
                                <div class="approval-actions">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200"
                                        onclick="openModal('modal-review')">Review</button>
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-red-100 text-red-600 hover:bg-red-200"
                                        onclick="showToast('Returned.')">Return</button>
                                </div>
                            </div>
                        </div>
                        <div class="approval-item">
                            <div class="approval-avatar" style="background:#0891b2;">JL</div>
                            <div class="approval-content">
                                <div class="approval-name">BIT-CT Schedule — 1st Sem AY 2025–26</div>
                                <div class="approval-detail">Submitted by Chair Jose Lim · 4 faculty · 10 sections · 0
                                    conflicts</div>
                                <div class="approval-actions">
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-blue-600 text-white hover:bg-blue-700"
                                        onclick="showToast('BIT-CT approved!')">Approve</button>
                                    <button class="px-3 py-1.5 rounded-lg text-[11px] font-semibold bg-slate-100 text-slate-600 hover:bg-slate-200"
                                        onclick="openModal('modal-review')">Review</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Overload Alerts + Quick Actions -->
                <div class="two-col">
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Overload / Underload Alerts</div>
                            </div>
                        </div>
                        <table>
                            <thead>
                                <tr>
                                    <th>Faculty</th>
                                    <th>Dept</th>
                                    <th>Load</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><b>Carlo Mendoza</b></td>
                                    <td>BSIT</td>
                                    <td><span class="font-mono font-bold text-red-600">31h</span></td>
                                    <td><span class="badge badge-red">Overload</span></td>
                                </tr>
                                <tr>
                                    <td><b>Ana Reyes</b></td>
                                    <td>BSIT</td>
                                    <td><span class="font-mono font-bold text-amber-600">27h</span></td>
                                    <td><span class="badge badge-amber">Near Max</span></td>
                                </tr>
                                <tr>
                                    <td><b>Maria Santos</b></td>
                                    <td>BSIS</td>
                                    <td><span class="font-mono font-bold text-blue-600">18h</span></td>
                                    <td><span class="badge badge-blue">Available</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <div>
                                <div class="card-title">Quick Actions</div>
                            </div>
                        </div>
                        <div class="quick-actions">
                            <div class="quick-btn" onclick="openModal('modal-export')">
                                <div class="quick-btn-label">Export Master Schedule</div>
                            </div>
                            <a href="" class="quick-btn">
                                <div class="quick-btn-label">Faculty Overview</div>
                            </a>
                            <a href="" class="quick-btn">
                                <div class="quick-btn-label">Approve Schedules</div>
                            </a>
                            <div class="quick-btn" onclick="openModal('modal-export')">
                                <div class="quick-btn-label">Workload Report</div>
                            </div>
                            <a href="" class="quick-btn">
                                <div class="quick-btn-label">Deployment Report</div>
                            </a>
                            <div class="quick-btn" onclick="openModal('modal-notify')">
                                <div class="quick-btn-label">Notify All Chairs</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- ══ END DASHBOARD ══ -->

        </div><!-- end app-main -->
    </div><!-- end app-shell -->

    <!-- TOAST -->
    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        /* ── MODALS ──
                       modal-export, modal-notify, and modal-review are defined on their
                       respective pages (Reports / Notify Chairs / Approvals) and are not
                       part of this dashboard extract. Wire them up if you include those
                       modal partials on this page too. */
        function openModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.add('open');
        }

        function closeModal(id) {
            const m = document.getElementById(id);
            if (m) m.classList.remove('open');
        }
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.modal-overlay').forEach(m => {
                m.addEventListener('click', e => {
                    if (e.target === m) m.classList.remove('open');
                });
            });
        });

        /* ── TOAST ── */
        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3200);
        }
    </script>
</body>

</html>