<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Dean Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dean/dean_dashboard.css') }}">
</head>

<body>

    <div class="app-wrapper">

        @include('partials.dean_sidebar')

        <!-- ══════════ MAIN ══════════ -->
        <div class="main">

            <!-- TOPBAR -->
            <div class="topbar">
                <div class="topbar-title">Dean Dashboard</div>
                <div class="topbar-actions">
                    <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
                    <button class="topbar-btn btn-secondary"
                        onclick="showToast('3 pending approvals')">Notifications</button>
                </div>
            </div>

            <!-- ══════════ DASHBOARD PAGE ══════════ -->
            <div class="page-content" style="animation:fadeIn .3s ease;">

                <!-- Page heading -->
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:24px;">
                    <div style="flex:1;">
                        <div style="font-size:22px;font-weight:800;">Good morning, Dean Villaceran</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:3px;">AY 2025–2026 · 1st Semester ·
                            CCICT Overview</div>
                    </div>
                    <span class="badge badge-teal">Scheduling Active</span>
                </div>

                <!-- Stat Cards -->
                <div class="stat-grid">
                    <div class="stat-card" style="--accent:#0891b2">
                        <div class="stat-label">Total Faculty</div>
                        <div class="stat-value">19</div>
                        <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
                    </div>
                    <div class="stat-card" style="--accent:#d97706">
                        <div class="stat-label">Subjects Plotted</div>
                        <div class="stat-value">54</div>
                        <div class="stat-sub">of 58 total</div>
                    </div>
                    <div class="stat-card" style="--accent:#dc2626">
                        <div class="stat-label">Avg. Faculty Load</div>
                        <div class="stat-value">21h</div>
                        <div class="stat-sub">of 30h max</div>
                    </div>
                    <div class="stat-card" style="--accent:#16a34a">
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
                                <div class="workload-val" style="color:var(--green)">87%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill" style="width:87%;background:var(--green)"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">BSIT — 7 Faculty</div>
                                <div class="workload-val" style="color:var(--amber)">72%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill" style="width:72%;background:var(--amber)"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">BIT-CT — 4 Faculty</div>
                                <div class="workload-val" style="color:var(--teal)">60%</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill" style="width:60%;background:var(--blue)"></div>
                            </div>
                        </div>
                        <div class="workload-item">
                            <div class="workload-header">
                                <div class="workload-name">Overloaded Faculty</div>
                                <div class="workload-val" style="color:var(--red)">2</div>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill" style="width:11%;background:var(--red)"></div>
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
                                    <button class="topbar-btn btn-primary" style="padding:5px 12px;font-size:11px;"
                                        onclick="showToast('BSIS Schedule approved!')">Approve</button>
                                    <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;"
                                        onclick="openModal('modal-review')">Review</button>
                                    <button class="topbar-btn"
                                        style="padding:5px 12px;font-size:11px;background:var(--red-light);color:var(--red);"
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
                                    <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;"
                                        onclick="openModal('modal-review')">Review</button>
                                    <button class="topbar-btn"
                                        style="padding:5px 12px;font-size:11px;background:var(--red-light);color:var(--red);"
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
                                    <button class="topbar-btn btn-primary" style="padding:5px 12px;font-size:11px;"
                                        onclick="showToast('BIT-CT approved!')">Approve</button>
                                    <button class="topbar-btn btn-secondary" style="padding:5px 12px;font-size:11px;"
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
                                    <td><span
                                            style="font-family:var(--mono);color:var(--red);font-weight:700">31h</span>
                                    </td>
                                    <td><span class="badge badge-red">Overload</span></td>
                                </tr>
                                <tr>
                                    <td><b>Ana Reyes</b></td>
                                    <td>BSIT</td>
                                    <td><span
                                            style="font-family:var(--mono);color:var(--amber);font-weight:700">27h</span>
                                    </td>
                                    <td><span class="badge badge-amber">Near Max</span></td>
                                </tr>
                                <tr>
                                    <td><b>Maria Santos</b></td>
                                    <td>BSIS</td>
                                    <td><span
                                            style="font-family:var(--mono);color:var(--blue);font-weight:700">18h</span>
                                    </td>
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

        </div><!-- end main -->
    </div><!-- end app-wrapper -->

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
