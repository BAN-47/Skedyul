<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Workload</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/dean/settings.css') }}">
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.dean_sidebar')

  <!-- Main -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Settings</div>
      <div class="topbar-actions">
        <button class="topbar-btn btn-primary" onclick="openModal('modal-export')">Export Report</button>
        <button class="topbar-btn btn-secondary" onclick="showToast('3 pending approvals')">Notifications</button>
      </div>
    </div>

    <!-- SETTINGS PAGE -->
    <!-- ══════════════════════════════════════
         SETTINGS PAGE — 5 TABS
    ═══════════════════════════════════════ -->
    <div id="page-settings" class="page active">
      <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:start;">

        <div class="card" style="padding:12px 0;">
          <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:8px 20px 4px;">Settings</div>
          <div class="settings-nav-item active" onclick="showDeanSettingsSection('profile',this)">Personal Info</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('general',this)">General</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('academic',this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('notifications',this)">Notifications</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('system',this)">System Info</div>
        </div>

        <div id="dean-settings-content">

          <!-- PERSONAL INFO -->
          <div id="dsec-profile">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Profile Picture</div><div class="card-sub">Upload a new profile photo</div></div></div>
              <div style="display:flex;align-items:center;gap:24px;padding:8px 0;">
                <div style="position:relative;flex-shrink:0;">
                  <div style="width:96px;height:96px;border-radius:50%;background:var(--navy);display:flex;align-items:center;justify-content:center;font-size:28px;font-weight:800;color:#fff;border:3px solid var(--border);">D</div>
                  <div style="position:absolute;bottom:0;right:0;width:30px;height:30px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;font-size:13px;font-weight:700;">+</div>
                </div>
                <div>
                  <div style="font-size:15px;font-weight:700;color:var(--text);">Ma. Emie Villaceran, DIT, Ph.D.</div>
                  <div style="font-size:12px;color:var(--text3);margin-top:2px;">Dean · CCICT</div>
                  <div style="display:flex;gap:8px;margin-top:12px;">
                    <button class="topbar-btn btn-primary" style="font-size:12px;padding:7px 14px;" onclick="showToast('Upload feature coming soon.')">Upload Photo</button>
                    <button class="topbar-btn btn-secondary" style="font-size:12px;padding:7px 14px;" onclick="showToast('Photo removed.')">Remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your personal details</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">First Name</label><input class="field-input" value="Ma. Emie"></div>
                <div class="field-group"><label class="field-label">Last Name</label><input class="field-input" value="Villaceran"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Middle Name</label><input class="field-input" placeholder="Optional"></div>
                <div class="field-group"><label class="field-label">Suffix</label>
                  <select class="field-select"><option value="">None</option><option>Jr.</option><option>Sr.</option><option>II</option><option>III</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Title / Degree</label><input class="field-input" value="DIT, Ph.D."></div>
                <div class="field-group"><label class="field-label">Employee ID</label><input class="field-input" value="CTU-2006-0001"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Gender</label>
                  <select class="field-select"><option>Male</option><option selected>Female</option><option>Prefer not to say</option></select>
                </div>
                <div class="field-group"><label class="field-label">Civil Status</label>
                  <select class="field-select"><option>Single</option><option selected>Married</option><option>Widowed</option><option>Separated</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Date of Birth</label><input class="field-input" type="date" value="1975-03-12"></div>
                <div class="field-group"><label class="field-label">Nationality</label><input class="field-input" value="Filipino"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Personal info saved successfully!')">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Contact & Office Details</div><div class="card-sub">How others can reach you</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Email Address</label><input class="field-input" type="email" value="villaceran.emie@ctu.edu.ph"></div>
                <div class="field-group"><label class="field-label">Phone Number</label><input class="field-input" value="(032) 401-7777"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Office Location</label><input class="field-input" value="Room 401, Admin Building"></div>
                <div class="field-group"><label class="field-label">College / Unit</label><input class="field-input" value="CCICT" readonly style="background:var(--grey2);cursor:not-allowed;"></div>
              </div>
              <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Bio / About</label><textarea class="field-input" rows="3" style="resize:vertical;">Dean of the College of Computing, Information and Communications Technology, Cebu Technological University.</textarea></div>
              <div style="display:flex;justify-content:flex-end;">
                <button class="topbar-btn btn-primary" onclick="showToast('Contact details saved successfully!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- GENERAL -->
          <div id="dsec-general" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Institution Details</div><div class="card-sub">Basic information about your school and college</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Institution Name</label><input class="field-input" value="Cebu Technological University"></div>
                <div class="field-group"><label class="field-label">Campus</label><input class="field-input" value="Main Campus"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">College / Unit</label><input class="field-input" value="College of Computing, Information and Communications Technology"></div>
                <div class="field-group"><label class="field-label">Abbreviation</label><input class="field-input" value="CCICT"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Contact Email</label><input class="field-input" type="email" value="ccict@ctu.edu.ph"></div>
                <div class="field-group"><label class="field-label">Phone</label><input class="field-input" value="(032) 401-7777"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Institution details saved!')">Save Changes</button></div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Appearance</div><div class="card-sub">Theme, language, and display format preferences</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Theme</label><select class="field-select" onchange="applyTheme(this.value)"><option value="light" selected>Light</option><option value="dark">Dark</option></select></div>
                <div class="field-group"><label class="field-label">Language</label><select class="field-select"><option selected>English</option><option>Filipino</option><option>Cebuano</option></select></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Date Format</label><select class="field-select"><option>MM/DD/YYYY</option><option selected>DD/MM/YYYY</option><option>YYYY-MM-DD</option></select></div>
                <div class="field-group"><label class="field-label">Time Format</label><select class="field-select"><option selected>12-hour (AM/PM)</option><option>24-hour</option></select></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Appearance settings saved!')">Save Changes</button></div>
            </div>
          </div>

          <!-- ACADEMIC YEAR -->
          <div id="dsec-academic" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Current Academic Year</div><div class="card-sub">Active semester configuration</div></div><span class="badge badge-green">Active</span></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Academic Year</label><input class="field-input" value="2025-2026"></div>
                <div class="field-group"><label class="field-label">Semester</label><select class="field-select"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Start Date</label><input class="field-input" type="date" value="2025-08-11"></div>
                <div class="field-group"><label class="field-label">End Date</label><input class="field-input" type="date" value="2025-12-20"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Schedule Submission Deadline</label><input class="field-input" type="date" value="2025-07-25"></div>
                <div class="field-group"><label class="field-label">School Days</label><select class="field-select"><option selected>Monday - Saturday</option><option>Monday - Friday</option></select></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Academic year settings saved!')">Save Changes</button></div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Schedule Constraints</div><div class="card-sub">Define scheduling rules and limits for this semester</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Max Faculty Load (hrs / week)</label><input class="field-input" type="number" value="30" min="1" max="60"></div>
                <div class="field-group"><label class="field-label">Min Faculty Load (hrs / week)</label><input class="field-input" type="number" value="12" min="1" max="60"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Class Start Time</label><input class="field-input" type="time" value="07:00"></div>
                <div class="field-group"><label class="field-label">Class End Time</label><input class="field-input" type="time" value="21:00"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Class Duration</label><select class="field-select"><option selected>90 minutes (1.5 hrs)</option><option>60 minutes</option><option>120 minutes</option></select></div>
                <div class="field-group"><label class="field-label">Conflict Detection</label><select class="field-select"><option selected>Enabled (strict)</option><option>Enabled (warnings only)</option><option>Disabled</option></select></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Schedule constraints saved!')">Save Changes</button></div>
            </div>
          </div>

          <!-- NOTIFICATIONS -->
          <div id="dsec-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive as Dean</div></div></div>
              <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                <div class="notif-row"><div><div class="notif-row-title">Schedule Submitted for Approval</div><div class="notif-row-sub">Notify when a Chair submits a department schedule for review</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Faculty Overload Alert</div><div class="notif-row-sub">Notify when any faculty member exceeds their maximum unit load</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Submission Deadline Reminder</div><div class="notif-row-sub">Remind 3 days before the schedule submission deadline</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Schedule Conflicts Detected</div><div class="notif-row-sub">Get notified when a conflict is detected in any department</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Room Double-Booking</div><div class="notif-row-sub">Alert when a room is assigned to two classes at the same time</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Weekly Workload Digest</div><div class="notif-row-sub">Receive a weekly email summary of faculty loads across all departments</div></div><label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Schedule Approval Reminders</div><div class="notif-row-sub">Remind when pending schedules have not been acted on for 48 hours</div></div><label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">Login Activity</div><div class="notif-row-sub">Notify on new logins from unrecognized devices or browsers</div></div><label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label></div>
                <div class="notif-row"><div><div class="notif-row-title">System Backups</div><div class="notif-row-sub">Receive confirmation after each automatic system backup</div></div><label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:18px;"><button class="topbar-btn btn-primary" onclick="showToast('Notification preferences saved!')">Save Preferences</button></div>
            </div>
          </div>

          <!-- SECURITY -->
          <div id="dsec-security" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your Dean portal account password</div></div></div>
              <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Current Password</label><input class="field-input" type="password" placeholder="Enter your current password"></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">New Password</label><input class="field-input" type="password" placeholder="Min. 8 characters" id="pw-new" oninput="checkPwStrength(this.value)"><div class="pw-strength"><div class="pw-strength-fill" id="pw-bar"></div></div></div>
                <div class="field-group"><label class="field-label">Confirm New Password</label><input class="field-input" type="password" placeholder="Re-enter new password"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button></div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Session & Access</div><div class="card-sub">Manage login security and session behaviour</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Session Timeout</label><select class="field-select"><option>15 minutes</option><option selected>30 minutes</option><option>1 hour</option><option>Never</option></select></div>
                <div class="field-group"><label class="field-label">Max Login Attempts</label><select class="field-select"><option>3</option><option selected>5</option><option>10</option></select></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;"><button class="topbar-btn btn-primary" onclick="showToast('Security settings saved!')">Save Changes</button></div>
            </div>
          </div>

          <!-- SYSTEM INFO -->
          <div id="dsec-system" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">System Information</div><div class="card-sub">Current environment and infrastructure details</div></div><span class="badge badge-green">All Systems Normal</span></div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="env-cell"><div class="env-cell-label">Application</div><div class="env-cell-val">SKEDYUL v1.0.0</div></div>
                <div class="env-cell"><div class="env-cell-label">Host / Deploy</div><div class="env-cell-val">Vercel (Production)</div></div>
                <div class="env-cell"><div class="env-cell-label">Database</div><div class="env-cell-val">Supabase PostgreSQL</div></div>
                <div class="env-cell"><div class="env-cell-label">Authentication</div><div class="env-cell-val">JWT + Laravel Sanctum</div></div>
                <div class="env-cell"><div class="env-cell-label">Frontend Stack</div><div class="env-cell-val">Tailwind CSS 4 + Vanilla JS</div></div>
                <div class="env-cell"><div class="env-cell-label">Mobile App</div><div class="env-cell-val">React Native</div></div>
                <div class="env-cell"><div class="env-cell-label">Last Backup</div><div class="env-cell-val" style="color:var(--green);">Today, 06:00 AM</div></div>
                <div class="env-cell"><div class="env-cell-label">Uptime (30 days)</div><div class="env-cell-val" style="color:var(--green);">99.98%</div></div>
                <div class="env-cell"><div class="env-cell-label">Environment</div><div class="env-cell-val">Production</div></div>
                <div class="env-cell"><div class="env-cell-label">API Version</div><div class="env-cell-val">v2.3.1</div></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title" style="color:var(--red);">Danger Zone</div><div class="card-sub">Irreversible actions — proceed with extreme caution</div></div></div>
              <div style="display:flex;flex-direction:column;gap:10px;">
                <div class="danger-row"><div><div class="danger-row-title">Clear All Schedules</div><div class="danger-row-sub">Removes all schedule assignments for the current semester. Cannot be undone.</div></div><button class="btn-danger" onclick="showToast('Action cancelled — confirmation required.')">Clear</button></div>
                <div class="danger-row"><div><div class="danger-row-title">Archive Current Semester</div><div class="danger-row-sub">Locks and archives all data for the active semester before rolling over.</div></div><button class="btn-danger" onclick="showToast('Action cancelled — confirmation required.')">Archive</button></div>
                <div class="danger-row"><div><div class="danger-row-title">Reset System Data</div><div class="danger-row-sub">Wipes all records and resets SKEDYUL to factory state. All data will be lost.</div></div><button class="btn-danger" onclick="showToast('Action cancelled — confirmation required.')">Reset</button></div>
              </div>
            </div>
          </div>

        </div><!-- /dean-settings-content -->
      </div>
    </div><!-- /page-settings -->

  </div><!-- /main -->
</div><!-- /screen-app -->

<!-- MODAL: EXPORT -->
<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header"><div class="modal-title">Export Report</div><button class="modal-close" onclick="closeModal('modal-export')">✕</button></div>
    <div class="modal-body">
      <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Report Type</label><select class="field-select"><option>Master Schedule</option><option>Faculty Workload Report</option><option>Faculty Deployment Report</option><option>Department Summary</option></select></div>
      <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Department</label><select class="field-select"><option>All Departments</option><option>BSIS</option><option>BSIT</option><option>BIT-CT</option></select></div>
      <div class="field-group"><label class="field-label">Format</label><select class="field-select"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select></div>
    </div>
    <div class="modal-footer"><button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button><button class="topbar-btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button></div>
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
          <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" style="width:15px;height:15px;accent-color:var(--blue);">
            <span style="font-size:13px;font-weight:700;color:var(--text);">All Department Chairs</span>
          </label>
          <div style="display:flex;flex-direction:column;gap:6px;padding-left:12px;">
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#d97706;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">RT</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Rodrigo Tan</div><div style="font-size:11px;color:var(--text3);">Chair · BSIS</div></div>
              </div>
            </label>
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">MC</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Maria Cruz</div><div style="font-size:11px;color:var(--text3);">Chair · BSIT</div></div>
              </div>
            </label>
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#0891b2;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">JL</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Jose Lim</div><div style="font-size:11px;color:var(--text3);">Chair · BIT-CT</div></div>
              </div>
            </label>
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
        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <!-- Sent History -->
      <div>
        <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Recently Sent</div>
        <div id="notif-history" style="display:flex;flex-direction:column;gap:6px;">
          <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No notifications sent yet this session.</div>
        </div>
      </div>

    </div>
    <div class="modal-footer">
      <button class="topbar-btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="topbar-btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
    </div>
  </div>
</div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<div class="modal-overlay" id="modal-export">
  <div class="modal" style="width:440px;">
    <div class="modal-header"><div class="modal-title">Export Report</div><button class="modal-close" onclick="closeModal('modal-export')">✕</button></div>
    <div class="modal-body">
      <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Report Type</label><select class="field-select"><option>Master Schedule</option><option>Faculty Workload Report</option><option>Faculty Deployment Report</option><option>Department Summary</option></select></div>
      <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Department</label><select class="field-select"><option>All Departments</option><option>BSIS</option><option>BSIT</option><option>BIT-CT</option></select></div>
      <div class="field-group"><label class="field-label">Format</label><select class="field-select"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select></div>
    </div>
    <div class="modal-footer"><button class="topbar-btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button><button class="topbar-btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button></div>
  </div>
</div>
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
          <label style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" style="width:15px;height:15px;accent-color:var(--blue);">
            <span style="font-size:13px;font-weight:700;color:var(--text);">All Department Chairs</span>
          </label>
          <div style="display:flex;flex-direction:column;gap:6px;padding-left:12px;">
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#d97706;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">RT</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Rodrigo Tan</div><div style="font-size:11px;color:var(--text3);">Chair · BSIS</div></div>
              </div>
            </label>
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#7c3aed;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">MC</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Maria Cruz</div><div style="font-size:11px;color:var(--text3);">Chair · BSIT</div></div>
              </div>
            </label>
            <label style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
              <input type="checkbox" class="chair-check" checked onchange="syncAllChairs()" style="width:14px;height:14px;accent-color:var(--blue);">
              <div style="display:flex;align-items:center;gap:8px;">
                <div style="width:28px;height:28px;border-radius:50%;background:#0891b2;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">JL</div>
                <div><div style="font-size:13px;font-weight:600;color:var(--text);">Jose Lim</div><div style="font-size:11px;color:var(--text3);">Chair · BIT-CT</div></div>
              </div>
            </label>
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
        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <!-- Sent History -->
      <div>
        <div style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">Recently Sent</div>
        <div id="notif-history" style="display:flex;flex-direction:column;gap:6px;">
          <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No notifications sent yet this session.</div>
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
    <div class="modal-header"><div class="modal-title">Schedule Review — BSIT</div><button class="modal-close" onclick="closeModal('modal-review')">✕</button></div>
    <div class="modal-body">
      <div style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;"><strong>1 Conflict Detected:</strong> Carlo Mendoza is scheduled for CC 311 and IT 201 at the same time on Monday 8:30–10:00 AM.</div>
      <table><thead><tr><th>Faculty</th><th>Subject</th><th>Day & Time</th><th>Room</th><th>Issue</th></tr></thead><tbody>
        <tr><td><b>Carlo Mendoza</b></td><td>IT 201</td><td>Mon 8:30–10:00</td><td>Lab 1</td><td><span class="badge badge-red">Conflict</span></td></tr>
        <tr><td><b>Carlo Mendoza</b></td><td>CC 311</td><td>Mon 8:30–10:00</td><td>Room 205</td><td><span class="badge badge-red">Conflict</span></td></tr>
        <tr><td><b>Ana Reyes</b></td><td>IT 401</td><td>Tue 7:00–8:30</td><td>Room 206</td><td><span class="badge badge-green">OK</span></td></tr>
      </tbody></table>
      <div class="field-group" style="margin-top:16px;"><label class="field-label">Return Note</label><textarea class="field-input" rows="3">Please resolve the scheduling conflict for Carlo Mendoza before resubmitting.</textarea></div>
    </div>
    <div class="modal-footer"><button class="topbar-btn btn-secondary" onclick="closeModal('modal-review')">Close</button><button class="topbar-btn" style="background:var(--red-light);color:var(--red);" onclick="closeModal('modal-review');showToast('Schedule returned to Chair Cruz.')">Return to Chair</button></div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>

function doLogin() {
  document.getElementById('screen-login').classList.remove('active');
  const app = document.getElementById('screen-app');
  app.classList.add('active'); app.style.display='flex';
}
function logout() {
  document.getElementById('screen-app').classList.remove('active');
  document.getElementById('screen-app').style.display='none';
  document.getElementById('screen-login').classList.add('active');
}
function goToPage(id) {
  document.querySelectorAll('.page').forEach(p => { p.classList.remove('active'); p.style.display='none'; });
  const page = document.getElementById(id);
  if (!page) return;
  page.style.display='block'; page.classList.add('active');
  const titles = {'page-dashboard':'Dean Dashboard','page-faculty':'Faculty Workload Overview','page-departments':'Department Overview','page-approvals':'Schedule Approvals','page-overload':'Overload Alerts','page-reports':'Schedule Reports','page-deployment':'Faculty Deployment Report','page-settings':'Settings'};
  document.getElementById('topbar-title').textContent = titles[id] || 'SKEDYUL';
}
function setActiveNav(el) {
  document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
  el.classList.add('active');
}
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('.modal-overlay').forEach(m => { m.addEventListener('click', e => { if (e.target===m) m.classList.remove('open'); }); });
});

function showSettingsTab(tab, el) {
  ['general','academic','notifications','security','system'].forEach(s => {
    const p = document.getElementById('stab-'+s); if (p) p.style.display='none';
  });
  const t = document.getElementById('stab-'+tab); if (t) t.style.display='block';
  document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('active'));
  el.classList.add('active');
}
function showDeanSettingsSection(section, el) {
  ['profile','general','academic','notifications','security','system'].forEach(s => {
    const p = document.getElementById('dsec-'+s); if (p) p.style.display='none';
  });
  const t = document.getElementById('dsec-'+section); if (t) t.style.display='block';
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
  const names = ['Rodrigo Tan (BSIS)', 'Maria Cruz (BSIT)', 'Jose Lim (BIT-CT)'];
  checks.forEach((c, i) => { if (c.checked) selected.push(names[i]); });

  if (!title) { showToast('Please enter a notification title.'); return; }
  if (!message) { showToast('Please enter a message.'); return; }
  if (selected.length === 0) { showToast('Please select at least one recipient.'); return; }

  const typeColors = { info:'badge-blue', reminder:'badge-amber', urgent:'badge-red', deadline:'badge-purple' };
  const typeLabels = { info:'Info', reminder:'Reminder', urgent:'Urgent', deadline:'Deadline' };
  const now = new Date();
  const timeStr = now.toLocaleTimeString('en-US', { hour:'numeric', minute:'2-digit', hour12:true });

  const historyEl = document.getElementById('notif-history');
  // Remove empty state message
  const empty = historyEl.querySelector('div[style*="text-align:center"]');
  if (empty) empty.remove();

  const item = document.createElement('div');
  item.style.cssText = 'background:var(--grey);border-radius:8px;padding:10px 12px;border:1px solid var(--border);';
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
  theme==='dark' ? document.body.classList.add('dark') : document.body.classList.remove('dark');
  showToast('Theme switched to '+(theme==='dark'?'Dark':'Light')+' mode!');
}
function checkPwStrength(val) {
  const bar = document.getElementById('pw-bar'); if (!bar) return;
  let score=0;
  if (val.length>=8) score++;
  if (/[A-Z]/.test(val)) score++;
  if (/[0-9]/.test(val)) score++;
  if (/[^A-Za-z0-9]/.test(val)) score++;
  const colors=['#dc2626','#d97706','#16a34a','#0891b2'];
  const widths=['25%','50%','75%','100%'];
  bar.style.width = val.length ? (widths[score-1]||'10%') : '0';
  bar.style.background = val.length ? (colors[score-1]||'#dc2626') : 'transparent';
}
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show'); setTimeout(()=>t.classList.remove('show'),3000);
}
function switchTab(barId, panelId, btn) {
  const bar = document.getElementById(barId); if (!bar) return;
  bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  ['tab-by-dept','tab-by-faculty','tab-by-section'].forEach(p => {
    const el = document.getElementById(p); if (el) { el.classList.remove('active'); el.style.display='none'; }
  });
  const target = document.getElementById(panelId);
  if (target) { target.style.display='block'; target.classList.add('active'); }
}

const deptData = {
  bsis: {
    code: 'BSIS', color: 'var(--blue)',
    name: 'Bachelor of Science in Information Systems',
    chair: 'Rodrigo Tan', sections: 23, avgLoad: '22h', maxLoad: '30h', loadPct: 73,
    loadColor: 'var(--blue)', scheduleStatus: 'Submitted', statusBadge: 'badge-green',
    faculty: [
      { name: 'Rodrigo Tan',    rank: 'Associate Professor II',  employment: 'Full-time', load: '24h', status: 'OK',       badge: 'badge-green' },
      { name: 'Jerome Bautista',rank: 'Assistant Professor III', employment: 'Full-time', load: '24h', status: 'OK',       badge: 'badge-green' },
      { name: 'Maria Santos',   rank: 'Instructor I',            employment: 'Full-time', load: '18h', status: 'Available',badge: 'badge-blue'  },
      { name: 'Patricia Reyes', rank: 'Assistant Professor I',   employment: 'Full-time', load: '21h', status: 'OK',       badge: 'badge-green' },
      { name: 'Ronald Diaz',    rank: 'Instructor II',           employment: 'Full-time', load: '22h', status: 'OK',       badge: 'badge-green' },
      { name: 'Cynthia Uy',     rank: 'Lecturer',                employment: 'Part-time', load: '12h', status: 'Part-time',badge: 'badge-teal'  },
      { name: 'Ben Flores',     rank: 'Instructor I',            employment: 'Part-time', load: '9h',  status: 'Part-time',badge: 'badge-teal'  },
      { name: 'Alma Ramos',     rank: 'Professor I',             employment: 'Full-time', load: '27h', status: 'Near Max', badge: 'badge-amber' },
    ]
  },
  bsit: {
    code: 'BSIT', color: 'var(--purple)',
    name: 'Bachelor of Science in Information Technology',
    chair: 'Maria Cruz', sections: 19, avgLoad: '24h', maxLoad: '30h', loadPct: 80,
    loadColor: 'var(--amber)', scheduleStatus: 'Has Conflicts', statusBadge: 'badge-amber',
    faculty: [
      { name: 'Maria Cruz',     rank: 'Associate Professor III', employment: 'Full-time', load: '21h', status: 'OK',       badge: 'badge-green' },
      { name: 'Ana Reyes',      rank: 'Assistant Professor II',  employment: 'Part-time', load: '27h', status: 'Near Max', badge: 'badge-amber' },
      { name: 'Carlo Mendoza',  rank: 'Instructor II',           employment: 'Full-time', load: '31h', status: 'Overload', badge: 'badge-red'   },
      { name: 'Liza Cruz',      rank: 'Lecturer',                employment: 'Part-time', load: '9h',  status: 'Part-time',badge: 'badge-teal'  },
      { name: 'Marco Villena',  rank: 'Instructor I',            employment: 'Full-time', load: '24h', status: 'OK',       badge: 'badge-green' },
      { name: 'Susan Dela Cruz',rank: 'Assistant Professor I',   employment: 'Full-time', load: '21h', status: 'OK',       badge: 'badge-green' },
      { name: 'Dennis Yap',     rank: 'Instructor III',          employment: 'Full-time', load: '22h', status: 'OK',       badge: 'badge-green' },
    ]
  },
  bitct: {
    code: 'BIT-CT', color: 'var(--teal)',
    name: 'Bachelor of Industrial Technology — Computer Technology',
    chair: 'Jose Lim', sections: 10, avgLoad: '18h', maxLoad: '30h', loadPct: 60,
    loadColor: 'var(--teal)', scheduleStatus: 'Submitted', statusBadge: 'badge-green',
    faculty: [
      { name: 'Jose Lim',       rank: 'Associate Professor I',   employment: 'Full-time', load: '21h', status: 'OK',       badge: 'badge-green' },
      { name: 'Noel Garcia',    rank: 'Instructor III',          employment: 'Full-time', load: '21h', status: 'OK',       badge: 'badge-green' },
      { name: 'Teresa Abella',  rank: 'Assistant Professor II',  employment: 'Full-time', load: '18h', status: 'OK',       badge: 'badge-green' },
      { name: 'Ryan Cobrado',   rank: 'Lecturer',                employment: 'Part-time', load: '12h', status: 'Part-time',badge: 'badge-teal'  },
    ]
  }
};

function openDept(key) {
  const d = deptData[key];
  if (!d) return;
  const facultyRows = d.faculty.map(f => `
    <tr>
      <td><b>${f.name}</b></td>
      <td>${f.rank}</td>
      <td>${f.employment}</td>
      <td><span style="font-family:var(--mono);font-weight:700;">${f.load}</span></td>
      <td><span class="badge ${f.badge}">${f.status}</span></td>
    </tr>`).join('');

  document.getElementById('dept-detail-content').innerHTML = `
    <div style="margin-bottom:20px;">
      <div style="font-size:22px;font-weight:800;color:${d.color}">${d.code}</div>
      <div style="font-size:14px;color:var(--text3);margin-top:2px;">${d.name}</div>
    </div>
    <div class="dept-info-grid">
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.faculty.length}</div><div class="dept-info-cell-label">Faculty</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.sections}</div><div class="dept-info-cell-label">Sections</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.avgLoad}</div><div class="dept-info-cell-label">Avg Load</div></div>
      <div class="dept-info-cell"><div class="dept-info-cell-val">${d.maxLoad}</div><div class="dept-info-cell-label">Max Load</div></div>
    </div>
    <div class="card" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px;">
        <div style="font-size:13px;font-weight:600;color:var(--text2)">Department Load — ${d.avgLoad} / ${d.maxLoad}</div>
        <div style="display:flex;gap:8px;"><span class="badge ${d.statusBadge}">${d.scheduleStatus}</span><span class="badge badge-grey">Chair: ${d.chair}</span></div>
      </div>
      <div class="workload-bar" style="height:10px;"><div class="workload-fill" style="width:${d.loadPct}%;background:${d.loadColor}"></div></div>
    </div>
    <div class="card">
      <div class="card-header"><div><div class="card-title">Faculty Members</div><div class="card-sub">${d.faculty.length} faculty in this department</div></div></div>
      <div class="table-wrap">
        <table>
          <thead><tr><th>Name</th><th>Rank</th><th>Employment</th><th>Load</th><th>Status</th></tr></thead>
          <tbody>${facultyRows}</tbody>
        </table>
      </div>
    </div>`;

  document.getElementById('dept-list-view').style.display = 'none';
  document.getElementById('dept-detail-view').style.display = 'block';
  document.getElementById('topbar-title').textContent = d.code + ' — Department Details';
}

function closeDept() {
  document.getElementById('dept-list-view').style.display = 'block';
  document.getElementById('dept-detail-view').style.display = 'none';
  document.getElementById('topbar-title').textContent = 'Department Overview';
}

</script>
</body>
</html>