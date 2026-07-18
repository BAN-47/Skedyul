<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Dean Dashboard</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/dean/departments.css') }}">
</head>

<body>

    <div id="screen-app" class="screen active" style="flex-direction:row;">

        @include('partials.dean_sidebar')

        <!-- Main -->
        <div class="main">
            <div class="topbar">
                <div class="topbar-title" id="topbar-title">Department Overview</div>
                <div class="topbar-actions">
                    <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
                    <button class="topbar-btn btn-secondary"
                        onclick="showToast('3 pending approvals')">Notifications</button>
                </div>
            </div>

            <!-- DEPARTMENTS PAGE -->
            <div id="page-departments" class="page active">
                <div id="dept-list-view">
                    <div style="margin-bottom:20px;">
                        <div style="font-size:20px;font-weight:800;">Department Overview</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:3px;">Select a department to view
                            details and faculty</div>
                    </div>
                    <div class="three-col">
                        @forelse ($deptData as $deptId => $d)
                            <div class="dept-card" onclick="openDept('{{ $deptId }}')">
                                <div class="dept-card-header">
                                    <div>
                                        <div class="dept-card-code" style="color:{{ $d['color'] }}">
                                            {{ $d['code'] }}</div>
                                        <div class="dept-card-name">{{ $d['name'] }}</div>
                                    </div><span class="dept-card-arrow">›</span>
                                </div>
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
                                    <div
                                        style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;">
                                        <div style="font-size:20px;font-weight:800;">{{ $d['facultyCount'] }}</div>
                                        <div style="font-size:11px;color:var(--text3)">Faculty</div>
                                    </div>
                                    <div
                                        style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;">
                                        <div style="font-size:20px;font-weight:800;">{{ $d['sections'] }}</div>
                                        <div style="font-size:11px;color:var(--text3)">Sections</div>
                                    </div>
                                </div>
                                <div style="margin-bottom:8px;font-size:12px;font-weight:600;color:var(--text2)">Avg
                                    Load: {{ $d['avgLoad'] }} / {{ $d['maxLoad'] }} max</div>
                                <div class="workload-bar">
                                    <div class="workload-fill"
                                        style="width:{{ $d['loadPct'] }}%;background:{{ $d['loadColor'] }}"></div>
                                </div>
                                <div style="margin-top:12px;display:flex;justify-content:space-between;"><span
                                        class="badge badge-{{ $d['statusBadge'] }}">{{ $d['scheduleStatus'] }}</span>
                                </div>
                            </div>
                        @empty
                            <div style="color:var(--text3);font-size:13px;">No departments found.</div>
                        @endforelse
                    </div>
                </div>
                <div id="dept-detail-view" style="display:none;">
                    <button class="topbar-btn btn-secondary" onclick="closeDept()" style="margin-bottom:20px;">Back to
                        Departments</button>
                    <div id="dept-detail-content"></div>
                </div>
                <div id="program-detail-view" style="display:none;">
                    <button class="topbar-btn btn-secondary" onclick="closeProgram()" style="margin-bottom:20px;">Back
                        to Programs</button>
                    <div id="program-detail-content"></div>
                </div>

            </div><!-- end .main -->
        </div><!-- end #screen-app -->

        <!-- MODAL: EXPORT -->
        <div class="modal-overlay" id="modal-export">
            <div class="modal" style="width:440px;">
                <div class="modal-header">
                    <div class="modal-title">Export Report</div><button class="modal-close"
                        onclick="closeModal('modal-export')">✕</button>
                </div>
                <div class="modal-body">
                    <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Report
                            Type</label><select class="field-select">
                            <option>Master Schedule</option>
                            <option>Faculty Workload Report</option>
                            <option>Faculty Deployment Report</option>
                            <option>Department Summary</option>
                        </select></div>
                    <div class="field-group" style="margin-bottom:14px;"><label
                            class="field-label">Department</label><select class="field-select">
                            <option>All Departments</option>
                            @foreach ($deptData as $d)
                                <option>{{ $d['code'] }}</option>
                            @endforeach
                        </select></div>
                    <div class="field-group"><label class="field-label">Format</label><select class="field-select">
                            <option>PDF</option>
                            <option>Excel (.xlsx)</option>
                            <option>Word (.docx)</option>
                        </select></div>
                </div>
                <div class="modal-footer"><button class="topbar-btn btn-secondary"
                        onclick="closeModal('modal-export')">Cancel</button><button class="topbar-btn btn-primary"
                        onclick="closeModal('modal-export');showToast('Report exported!')">Download</button></div>
            </div>
        </div>
        <!-- MODAL: NOTIFY CHAIRS -->
        <div class="modal-overlay" id="modal-notify">
            <div class="modal" style="width:520px;">
                <div class="modal-header">
                    <div class="modal-title">Send Notification to Chairs</div>
                    <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
                </div>
                <div class="modal-body">

                    <!-- Recipients -->
                    <div style="margin-bottom:16px;">
                        <div class="field-label" style="margin-bottom:8px;">Recipients</div>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <label
                                style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
                                <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)"
                                    style="width:15px;height:15px;accent-color:var(--blue);">
                                <span style="font-size:13px;font-weight:700;color:var(--text);">All Department
                                    Chairs</span>
                            </label>
                            <div style="display:flex;flex-direction:column;gap:6px;padding-left:12px;">
                                {{-- Chairs are assigned per-program now (BSIS / BSIT / BIT-CT each have their
                                     own chair), so this list loops over every program across all departments
                                     the dean oversees, rather than one row per department. --}}
                                @foreach ($deptData as $d)
                                    @foreach ($d['programs'] as $p)
                                        <label
                                            style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
                                            <input type="checkbox" class="chair-check" checked
                                                onchange="syncAllChairs()"
                                                style="width:14px;height:14px;accent-color:var(--blue);">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <div
                                                    style="width:28px;height:28px;border-radius:50%;background:#d97706;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">
                                                    {{ strtoupper(substr($p['chair'], 0, 2)) }}</div>
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:var(--text);">
                                                        {{ $p['chair'] }}</div>
                                                    <div style="font-size:11px;color:var(--text3);">Chair ·
                                                        {{ $p['code'] }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Notification Type</label>
                        <select class="field-select" id="notif-type">
                            <option value="info">General Info</option>
                            <option value="reminder">Reminder</option>
                            <option value="urgent">Urgent</option>
                            <option value="deadline">Deadline Notice</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Title</label>
                        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
                    </div>

                    <!-- Message -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Message</label>
                        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;"
                            placeholder="Write your message to the chairs..."></textarea>
                    </div>

                    <!-- Sent History -->
                    <div>
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                            Recently Sent</div>
                        <div id="notif-history" style="display:flex;flex-direction:column;gap:6px;">
                            <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No
                                notifications sent yet this session.</div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="topbar-btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
                    <button class="topbar-btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
                </div>
            </div>
        </div>
        <div class="modal-overlay" id="modal-review">
            <div class="modal" style="width:560px;">
                <div class="modal-header">
                    <div class="modal-title">Schedule Review — BSIT</div><button class="modal-close"
                        onclick="closeModal('modal-review')">✕</button>
                </div>
                <div class="modal-body">
                    <div
                        style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                        <strong>1 Conflict Detected:</strong> Carlo Mendoza is scheduled for CC 311 and IT 201 at the
                        same time on Monday 8:30–10:00 AM.
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th>Subject</th>
                                <th>Day & Time</th>
                                <th>Room</th>
                                <th>Issue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Carlo Mendoza</b></td>
                                <td>IT 201</td>
                                <td>Mon 8:30–10:00</td>
                                <td>Lab 1</td>
                                <td><span class="badge badge-red">Conflict</span></td>
                            </tr>
                            <tr>
                                <td><b>Carlo Mendoza</b></td>
                                <td>CC 311</td>
                                <td>Mon 8:30–10:00</td>
                                <td>Room 205</td>
                                <td><span class="badge badge-red">Conflict</span></td>
                            </tr>
                            <tr>
                                <td><b>Ana Reyes</b></td>
                                <td>IT 401</td>
                                <td>Tue 7:00–8:30</td>
                                <td>Room 206</td>
                                <td><span class="badge badge-green">OK</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="field-group" style="margin-top:16px;"><label class="field-label">Return Note</label>
                        <textarea class="field-input" rows="3">Please resolve the scheduling conflict for Carlo Mendoza before resubmitting.</textarea>
                    </div>
                </div>
                <div class="modal-footer"><button class="topbar-btn btn-secondary"
                        onclick="closeModal('modal-review')">Close</button><button class="topbar-btn"
                        style="background:var(--red-light);color:var(--red);"
                        onclick="closeModal('modal-review');showToast('Schedule returned to Chair Cruz.')">Return to
                        Chair</button></div>
            </div>
        </div>

        <div class="toast" id="toast"><span id="toast-msg"></span></div>

        <script>
            function doLogin() {
                document.getElementById('screen-login').classList.remove('active');
                const app = document.getElementById('screen-app');
                app.classList.add('active');
                app.style.display = 'flex';
            }

            function logout() {
                document.getElementById('screen-app').classList.remove('active');
                document.getElementById('screen-app').style.display = 'none';
                document.getElementById('screen-login').classList.add('active');
            }

            function goToPage(id) {
                document.querySelectorAll('.page').forEach(p => {
                    p.classList.remove('active');
                    p.style.display = 'none';
                });
                const page = document.getElementById(id);
                if (!page) return;
                page.style.display = 'block';
                page.classList.add('active');
                const titles = {
                    'page-dashboard': 'Dean Dashboard',
                    'page-faculty': 'Faculty Workload Overview',
                    'page-departments': 'Department Overview',
                    'page-approvals': 'Schedule Approvals',
                    'page-overload': 'Overload Alerts',
                    'page-reports': 'Schedule Reports',
                    'page-deployment': 'Faculty Deployment Report',
                    'page-settings': 'Settings'
                };
                document.getElementById('topbar-title').textContent = titles[id] || 'SKEDYUL';
            }

            function setActiveNav(el) {
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                el.classList.add('active');
            }

            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.modal-overlay').forEach(m => {
                    m.addEventListener('click', e => {
                        if (e.target === m) m.classList.remove('open');
                    });
                });
            });

            function showSettingsTab(tab, el) {
                ['general', 'academic', 'notifications', 'security', 'system'].forEach(s => {
                    const p = document.getElementById('stab-' + s);
                    if (p) p.style.display = 'none';
                });
                const t = document.getElementById('stab-' + tab);
                if (t) t.style.display = 'block';
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('active'));
                el.classList.add('active');
            }

            function showDeanSettingsSection(section, el) {
                ['profile', 'general', 'academic', 'notifications', 'security', 'system'].forEach(s => {
                    const p = document.getElementById('dsec-' + s);
                    if (p) p.style.display = 'none';
                });
                const t = document.getElementById('dsec-' + section);
                if (t) t.style.display = 'block';
                document.querySelectorAll('#page-settings .settings-nav-item').forEach(i => i.classList.remove('active'));
                el.classList.add('active');
            }

            function toggleAllChairs(master) {
                document.querySelectorAll('.chair-check').forEach(c => c.checked = master.checked);
            }

            function syncAllChairs() {
                const checks = document.querySelectorAll('.chair-check');
                const allChecked = Array.from(checks).every(c => c.checked);
                document.getElementById('notif-all').checked = allChecked;
            }

            function sendNotifToChairs() {
                const title = document.getElementById('notif-title').value.trim();
                const message = document.getElementById('notif-message').value.trim();
                const type = document.getElementById('notif-type').value;
                const checks = document.querySelectorAll('.chair-check');
                const selected = [];
                // Chairs are per-program now, so flatten every department's
                // programs into one list of "Chair Name (PROG_CODE)" strings —
                // this must line up 1:1 with the recipient rows rendered above.
                const names = @json(collect($deptData)->flatMap(fn($d) => collect($d['programs'])->map(fn($p) => $p['chair'] . ' (' . $p['code'] . ')'))->values());
                checks.forEach((c, i) => {
                    if (c.checked) selected.push(names[i]);
                });

                if (!title) {
                    showToast('Please enter a notification title.');
                    return;
                }
                if (!message) {
                    showToast('Please enter a message.');
                    return;
                }
                if (selected.length === 0) {
                    showToast('Please select at least one recipient.');
                    return;
                }

                const typeColors = {
                    info: 'badge-blue',
                    reminder: 'badge-amber',
                    urgent: 'badge-red',
                    deadline: 'badge-purple'
                };
                const typeLabels = {
                    info: 'Info',
                    reminder: 'Reminder',
                    urgent: 'Urgent',
                    deadline: 'Deadline'
                };
                const now = new Date();
                const timeStr = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                const historyEl = document.getElementById('notif-history');
                // Remove empty state message
                const empty = historyEl.querySelector('div[style*="text-align:center"]');
                if (empty) empty.remove();

                const item = document.createElement('div');
                item.style.cssText =
                    'background:var(--grey);border-radius:8px;padding:10px 12px;border:1px solid var(--border);';
                item.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
      <div style="display:flex;align-items:center;gap:7px;">
        <span class="badge ${typeColors[type]}">${typeLabels[type]}</span>
        <span style="font-size:13px;font-weight:700;color:var(--text);">${title}</span>
      </div>
      <span style="font-size:11px;color:var(--text3);">Sent ${timeStr}</span>
    </div>
    <div style="font-size:12px;color:var(--text2);margin-bottom:4px;">${message}</div>
    <div style="font-size:11px;color:var(--text3);">To: ${selected.join(', ')}</div>`;

                historyEl.prepend(item);

                // Clear fields
                document.getElementById('notif-title').value = '';
                document.getElementById('notif-message').value = '';
                document.getElementById('notif-type').value = 'info';

                showToast('Notification sent to ' + selected.length + ' chair' + (selected.length > 1 ? 's' : '') + '!');
            }

            function toggleSwitch(input) {
                const track = input.nextElementSibling;
                input.checked ? track.classList.add('on') : track.classList.remove('on');
                showToast('Preference updated!');
            }

            function applyTheme(theme) {
                theme === 'dark' ? document.body.classList.add('dark') : document.body.classList.remove('dark');
                showToast('Theme switched to ' + (theme === 'dark' ? 'Dark' : 'Light') + ' mode!');
            }

            function checkPwStrength(val) {
                const bar = document.getElementById('pw-bar');
                if (!bar) return;
                let score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                const colors = ['#dc2626', '#d97706', '#16a34a', '#0891b2'];
                const widths = ['25%', '50%', '75%', '100%'];
                bar.style.width = val.length ? (widths[score - 1] || '10%') : '0';
                bar.style.background = val.length ? (colors[score - 1] || '#dc2626') : 'transparent';
            }

            function showToast(msg) {
                const t = document.getElementById('toast');
                document.getElementById('toast-msg').textContent = msg;
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 3000);
            }

            function switchTab(barId, panelId, btn) {
                const bar = document.getElementById(barId);
                if (!bar) return;
                bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                ['tab-by-dept', 'tab-by-faculty', 'tab-by-section'].forEach(p => {
                    const el = document.getElementById(p);
                    if (el) {
                        el.classList.remove('active');
                        el.style.display = 'none';
                    }
                });
                const target = document.getElementById(panelId);
                if (target) {
                    target.style.display = 'block';
                    target.classList.add('active');
                }
            }

            // Real department + faculty data from the database (built in DeanDepartmentController)
            const deptData = @json($deptData);

            let currentDeptKey = null;

            // Auto-open the department directly — skip the list-click step
            // when the dean only oversees a single department (e.g. CCICT).
            // Once more departments exist, the list view returns automatically.
            document.addEventListener('DOMContentLoaded', function() {
                const keys = Object.keys(deptData);
                if (keys.length === 1) {
                    openDept(keys[0]);
                }
            });

            function openDept(key) {
                const d = deptData[key];
                if (!d) return;
                currentDeptKey = key;

                const programKeys = Object.keys(d.programs);
                const programCards = programKeys.map(pKey => {
                    const p = d.programs[pKey];
                    return `
    <div class="dept-card" onclick="openProgram('${key}','${pKey}')">
      <div class="dept-card-header"><div><div class="dept-card-code" style="color:${p.color}">${p.code}</div><div class="dept-card-name">${p.name}</div></div><span class="dept-card-arrow">›</span></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
        <div style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:800;">${p.facultyCount}</div><div style="font-size:11px;color:var(--text3)">Faculty</div></div>
        <div style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:800;">${p.sections}</div><div style="font-size:11px;color:var(--text3)">Sections</div></div>
      </div>
      <div style="margin-bottom:8px;font-size:12px;font-weight:600;color:var(--text2)">Avg Load: ${p.avgLoad} / ${p.maxLoad} max</div>
      <div class="workload-bar"><div class="workload-fill" style="width:${p.loadPct}%;background:${p.loadColor}"></div></div>
      <div style="margin-top:12px;"><span class="badge badge-grey">Chair: ${p.chair}</span></div>
    </div>`;
                }).join('');

                document.getElementById('dept-detail-content').innerHTML = `
    <div style="margin-bottom:20px;">
      <div style="font-size:22px;font-weight:800;color:${d.color}">${d.code}</div>
      <div style="font-size:14px;color:var(--text3);margin-top:2px;">${d.name}</div>
    </div>
    <div class="dept-info-grid">
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.facultyCount}</div><div class="dept-info-cell-label">Faculty</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.sections}</div><div class="dept-info-cell-label">Sections</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.avgLoad}</div><div class="dept-info-cell-label">Avg Load</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.maxLoad}</div><div class="dept-info-cell-label">Max Load</div></div>
    </div>
    <div class="card" style="margin-bottom:20px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-size:13px;font-weight:600;color:var(--text2)">Department Load — ${d.avgLoad} / ${d.maxLoad}</div>
        <div style="display:flex;gap:8px;"><span class="badge badge-${d.statusBadge}">${d.scheduleStatus}</span></div>
      </div>
      <div class="workload-bar" style="height:10px;"><div class="workload-fill" style="width:${d.loadPct}%;background:${d.loadColor}"></div></div>
    </div>
    <div style="margin-bottom:12px;">
      <div style="font-size:16px;font-weight:800;color:var(--text);">Programs</div>
      <div style="font-size:13px;color:var(--text3);margin-top:2px;">Select a program to view its faculty</div>
    </div>
    <div class="three-col">
      ${programCards || `<div style="color:var(--text3);font-size:13px;">No programs found for this department.</div>`}
    </div>`;

                document.getElementById('dept-list-view').style.display = 'none';
                document.getElementById('dept-detail-view').style.display = 'block';
                document.getElementById('topbar-title').textContent = d.code + ' — Department Details';

                // Hide "Back to Departments" when it's the only department —
                // there's nothing meaningful to go back to.
                const backBtn = document.querySelector('#dept-detail-view > .topbar-btn');
                if (backBtn) {
                    backBtn.style.display = Object.keys(deptData).length === 1 ? 'none' : 'inline-flex';
                }
            }

            function closeDept() {
                document.getElementById('dept-list-view').style.display = 'block';
                document.getElementById('dept-detail-view').style.display = 'none';
                document.getElementById('topbar-title').textContent = 'Department Overview';
            }

            function openProgram(deptKey, progKey) {
                const d = deptData[deptKey];
                if (!d) return;
                const p = d.programs[progKey];
                if (!p) return;

                const rows = p.faculty.map(f => `
    <tr>
      <td><b>${f.name}</b></td>
      <td>${f.rank}</td>
      <td>${f.employment}</td>
      <td><span style="font-family:var(--mono);font-weight:700;">${f.load}</span></td>
      <td><span class="badge ${f.badge}">${f.status}</span></td>
    </tr>`).join('');

                document.getElementById('program-detail-content').innerHTML = `
    <div style="margin-bottom:20px;">
      <div style="font-size:22px;font-weight:800;color:${p.color}">${p.code}</div>
      <div style="font-size:14px;color:var(--text3);margin-top:2px;">${p.name}</div>
      <div style="font-size:12px;color:var(--text3);margin-top:6px;">Chair: <b style="color:var(--text2)">${p.chair}</b></div>
    </div>
    <div class="dept-info-grid">
      <div class="dept-info-cell"><div class="dept-info-cell-val">${p.facultyCount}</div><div class="dept-info-cell-label">Faculty</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${p.sections}</div><div class="dept-info-cell-label">Sections</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${p.avgLoad}</div><div class="dept-info-cell-label">Avg Load</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${p.maxLoad}</div><div class="dept-info-cell-label">Max Load</div></div>
    </div>
    <div class="card">
      <div class="card-header"><div><div class="card-title">Faculty Members</div><div class="card-sub">${p.faculty.length} faculty in this program</div></div></div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Rank</th><th>Employment</th><th>Load</th><th>Status</th></tr></thead>
          <tbody>${rows || `<tr><td colspan="5" style="text-align:center;padding:16px;color:var(--text3);font-size:13px;">No faculty assigned to this program yet.</td></tr>`}</tbody>
        </table>
      </div>
    </div>`;

                document.getElementById('dept-detail-view').style.display = 'none';
                document.getElementById('program-detail-view').style.display = 'block';
                document.getElementById('topbar-title').textContent = p.code + ' — Faculty';
            }

            function closeProgram() {
                document.getElementById('program-detail-view').style.display = 'none';
                document.getElementById('dept-detail-view').style.display = 'block';
                if (currentDeptKey) {
                    document.getElementById('topbar-title').textContent = deptData[currentDeptKey].code +
                        ' — Department Details';
                }
            }
        </script>
</body>

</html>
