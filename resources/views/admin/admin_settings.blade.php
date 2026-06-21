<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Settings</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/admin/admin_settings.css') }}">
</head>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.admin_sidebar')

  <!-- MAIN -->
  <div class="main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Settings</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" style="padding:8px 14px;border-radius:8px;background:var(--grey2);border:none;font-family:var(--font);font-size:13px;font-weight:600;color:var(--text2);cursor:pointer;display:flex;align-items:center;gap:6px;">
          Notifications <span id="notif-count" style="background:var(--red);color:#fff;font-size:10px;font-weight:700;padding:2px 7px;border-radius:20px;">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;position:absolute;top:44px;right:0;width:340px;background:var(--white);border:1px solid var(--border);border-radius:14px;box-shadow:0 8px 32px rgba(0,0,0,0.12);z-index:100;overflow:hidden;">
          <div style="padding:14px 16px;border-bottom:1px solid var(--border);font-size:14px;font-weight:700;color:var(--text);">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div style="padding:10px 16px;border-top:1px solid var(--border);text-align:center;">
            <button onclick="markAllRead()" style="font-size:12px;color:var(--blue);font-weight:600;background:none;border:none;cursor:pointer;font-family:var(--font);">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    <div id="page-settings" class="page active">

      <div style="display:grid;grid-template-columns:220px 1fr;gap:24px;align-items:start;">

        <!-- Left nav -->
        <div class="card" style="padding:12px 0;">
          <div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1px;padding:8px 20px 4px;">Settings</div>
          <div class="settings-nav-item active" onclick="showSettingsSection('profile',this)">Personal Info</div>
          <div class="settings-nav-item" onclick="showSettingsSection('general',this)">General</div>
          <div class="settings-nav-item" onclick="showSettingsSection('academic',this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showSettingsSection('notifications',this)">Notifications</div>
          <div class="settings-nav-item" onclick="showSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showSettingsSection('system',this)">System Info</div>
        </div>

        <!-- Right content -->
        <div id="settings-content">

          <!-- PERSONAL INFO -->
          <div id="settings-profile">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Profile Picture</div><div class="card-sub">Click the avatar to upload a new photo</div></div></div>
              <div style="display:flex;align-items:center;gap:24px;padding:8px 0;">
                <div style="position:relative;flex-shrink:0;">
                  <div id="profile-pic-preview" style="width:96px;height:96px;border-radius:50%;background:var(--grey2);display:flex;align-items:center;justify-content:center;font-size:34px;font-weight:800;color:#fff;border:3px solid var(--border);overflow:hidden;"></div>
                  <div onclick="document.getElementById('pic-upload').click()" style="position:absolute;bottom:0;right:0;width:30px;height:30px;background:var(--blue);border-radius:50%;display:flex;align-items:center;justify-content:center;cursor:pointer;border:2px solid white;font-size:14px;">✏️</div>
                  <input type="file" id="pic-upload" accept="image/*" style="display:none;" onchange="previewProfilePic(this)">
                </div>
                <div>
                  <div style="font-size:14px;font-weight:700;color:var(--text);">Tech Admin</div>
                  <div style="font-size:12px;color:var(--text3);margin-top:2px;">Technical Administrator · CCICT</div>
                  <div style="display:flex;gap:8px;margin-top:12px;">
                    <button class="topbar-btn btn-primary" style="font-size:12px;padding:7px 14px;" onclick="document.getElementById('pic-upload').click()">Upload Photo</button>
                    <button class="topbar-btn btn-secondary" style="font-size:12px;padding:7px 14px;" onclick="resetProfilePic()">Remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your name, rank, and contact details</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">First Name</label><input class="field-input" id="pi-firstname" value="Tech"></div>
                <div class="field-group"><label class="field-label">Last Name</label><input class="field-input" id="pi-lastname" value="Admin"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Middle Name</label><input class="field-input" id="pi-middlename" placeholder="Optional"></div>
                <div class="field-group"><label class="field-label">Suffix</label>
                  <select class="field-select" id="pi-suffix"><option value="">None</option><option>Jr.</option><option>Sr.</option><option>II</option><option>III</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Rank / Title</label>
                  <select class="field-select" id="pi-rank">
                    <option selected>Technical Administrator</option>
                    <option>Senior Technical Administrator</option>
                    <option>IT Officer</option>
                    <option>Systems Analyst</option>
                    <option>Network Administrator</option>
                  </select>
                </div>
                <div class="field-group"><label class="field-label">Employee ID</label><input class="field-input" id="pi-empid" value="CTU-2024-001"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Gender</label>
                  <select class="field-select" id="pi-gender"><option>Male</option><option>Female</option><option>Prefer not to say</option></select>
                </div>
                <div class="field-group"><label class="field-label">Civil Status</label>
                  <select class="field-select" id="pi-civil"><option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Date of Birth</label><input class="field-input" id="pi-dob" type="date" value="1990-01-01"></div>
                <div class="field-group"><label class="field-label">Nationality</label><input class="field-input" id="pi-nationality" value="Filipino"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Contact & Office Details</div><div class="card-sub">How others can reach you</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Email Address</label><input class="field-input" id="pi-email" type="email" value="admin@ctu.edu.ph"></div>
                <div class="field-group"><label class="field-label">Phone Number</label><input class="field-input" id="pi-phone" value="(032) 401-0000"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Office Location</label><input class="field-input" id="pi-office" value="ICT Building, Room 100"></div>
                <div class="field-group"><label class="field-label">Department</label><input class="field-input" id="pi-dept" value="CCICT" readonly style="background:var(--grey2);cursor:not-allowed;"></div>
              </div>
              <div class="field-group" style="margin-bottom:16px;"><label class="field-label">Bio / About</label><textarea class="field-input" id="pi-bio" rows="3" style="resize:vertical;" placeholder="Brief description about yourself...">Technical Administrator of the CCICT, Cebu Technological University.</textarea></div>
              <div style="display:flex;justify-content:flex-end;">
                <button class="topbar-btn btn-primary" onclick="showToast('Contact details saved successfully!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- GENERAL -->
          <div id="settings-general" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Institution Details</div><div class="card-sub">Basic information about your school</div></div></div>
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
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Institution details saved!')">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><div><div class="card-title">Appearance</div><div class="card-sub">Customize the look of SKEDYUL</div></div></div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Theme</label>
                  <select class="field-select" id="theme-select" onchange="applyTheme(this.value)">
                    <option value="light" selected>Light</option>
                    <option value="dark">Dark</option>
                  </select>
                </div>
                <div class="field-group">
                  <label class="field-label">Language</label>
                  <select class="field-select">
                    <option selected>English</option>
                    <option>Filipino</option>
                    <option>Cebuano</option>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Date Format</label>
                  <select class="field-select">
                    <option>MM/DD/YYYY</option>
                    <option selected>DD/MM/YYYY</option>
                    <option>YYYY-MM-DD</option>
                  </select>
                </div>
                <div class="field-group">
                  <label class="field-label">Time Format</label>
                  <select class="field-select">
                    <option selected>12-hour (AM/PM)</option>
                    <option>24-hour</option>
                  </select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Appearance settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- ACADEMIC YEAR -->
          <div id="settings-academic" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Current Academic Year</div><div class="card-sub">Active semester configuration</div></div><span class="badge badge-green">Active</span></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Academic Year</label><input class="field-input" value="2025–2026"></div>
                <div class="field-group">
                  <label class="field-label">Semester</label>
                  <select class="field-select"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Start Date</label><input class="field-input" type="date" value="2025-08-11"></div>
                <div class="field-group"><label class="field-label">End Date</label><input class="field-input" type="date" value="2025-12-20"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Academic year settings saved!')">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Schedule Constraints</div><div class="card-sub">Define scheduling rules for this semester</div></div></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Max Faculty Load (hrs/week)</label><input class="field-input" type="number" value="30"></div>
                <div class="field-group"><label class="field-label">Min Faculty Load (hrs/week)</label><input class="field-input" type="number" value="12"></div>
              </div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">Class Start Time</label><input class="field-input" type="time" value="07:00"></div>
                <div class="field-group"><label class="field-label">Class End Time</label><input class="field-input" type="time" value="21:00"></div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">School Days</label>
                  <select class="field-select"><option selected>Monday – Saturday</option><option>Monday – Friday</option></select>
                </div>
                <div class="field-group">
                  <label class="field-label">Conflict Detection</label>
                  <select class="field-select"><option selected>Enabled (strict)</option><option>Enabled (warnings only)</option><option>Disabled</option></select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Schedule constraints saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- NOTIFICATIONS -->
          <div id="settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive</div></div></div>
              <div style="display:flex;flex-direction:column;gap:16px;margin-top:4px;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Schedule Conflicts</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Get notified when a scheduling conflict is detected</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">New User Registration</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Alert when a new account is created or pending approval</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Faculty Overload</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Notify when a faculty member exceeds their max load</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Room Double-Booking</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Alert when a room is assigned to two classes at the same time</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">System Backups</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Receive confirmation after each automatic backup</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:var(--grey);border-radius:10px;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Login Activity</div><div style="font-size:12px;color:var(--text3);margin-top:2px;">Notify on new logins from unrecognized devices</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Notification settings saved!')">Save Preferences</button>
              </div>
            </div>
          </div>

          <!-- SECURITY -->
          <div id="settings-security" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your admin account password</div></div></div>
              <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Current Password</label><input class="field-input" type="password" placeholder="••••••••"></div>
              <div class="form-row">
                <div class="field-group"><label class="field-label">New Password</label><input class="field-input" type="password" placeholder="Min. 8 characters"></div>
                <div class="field-group"><label class="field-label">Confirm New Password</label><input class="field-input" type="password" placeholder="Re-enter new password"></div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Session & Access</div><div class="card-sub">Manage login security settings</div></div></div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Session Timeout</label>
                  <select class="field-select"><option>15 minutes</option><option selected>30 minutes</option><option>1 hour</option><option>Never</option></select>
                </div>
                <div class="field-group">
                  <label class="field-label">Max Login Attempts</label>
                  <select class="field-select"><option>3</option><option selected>5</option><option>10</option></select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Security settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- SYSTEM INFO -->
          <div id="settings-system" style="display:none;">
            <div class="card" style="margin-bottom:16px;">
              <div class="card-header"><div><div class="card-title">System Information</div><div class="card-sub">Current environment details</div></div></div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Application</div><div style="font-size:13px;font-weight:600;color:var(--text);">SKEDYUL v1.0.0</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Host</div><div style="font-size:13px;font-weight:600;color:var(--text);">Vercel (Production)</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Database</div><div style="font-size:13px;font-weight:600;color:var(--text);">Supabase PostgreSQL</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Auth</div><div style="font-size:13px;font-weight:600;color:var(--text);">JWT + Laravel Sanctum</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Frontend</div><div style="font-size:13px;font-weight:600;color:var(--text);">Bootstrap 5 + Vanilla JS</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Mobile App</div><div style="font-size:13px;font-weight:600;color:var(--text);">React Native</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Last Backup</div><div style="font-size:13px;font-weight:600;color:var(--green);">Today, 06:00 AM ✓</div></div>
                <div style="background:var(--grey);border-radius:10px;padding:14px;"><div style="font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:4px;">Uptime</div><div style="font-size:13px;font-weight:600;color:var(--text);">99.98% (last 30 days)</div></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Danger Zone</div><div class="card-sub">Irreversible actions — proceed with caution</div></div></div>
              <div style="display:flex;flex-direction:column;gap:10px;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;border:1px solid var(--red-light);border-radius:10px;background:#fff5f5;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Clear All Schedules</div><div style="font-size:12px;color:var(--text3);">Removes all schedule assignments for the current semester</div></div>
                  <button class="topbar-btn" style="background:#fee2e2;color:var(--red);padding:6px 14px;font-size:12px;" onclick="showToast('Action cancelled — confirmation required.')">Clear</button>
                </div>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;border:1px solid var(--red-light);border-radius:10px;background:#fff5f5;">
                  <div><div style="font-size:13px;font-weight:600;color:var(--text);">Reset System Data</div><div style="font-size:12px;color:var(--text3);">Wipes all records and resets to factory state</div></div>
                  <button class="topbar-btn" style="background:#fee2e2;color:var(--red);padding:6px 14px;font-size:12px;" onclick="showToast('Action cancelled — confirmation required.')">Reset</button>
                </div>
              </div>
            </div>
          </div>

        </div><!-- end settings-content -->
      </div>
    </div>

  </div>
</div>

<!-- TOAST -->
<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const ADMIN_NOTIFS = [
  { dot:'var(--red)', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'var(--green)', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'var(--blue)', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = ADMIN_NOTIFS.map((n) => `
    <div class="notif-drop-item ${n.unread?'unread':''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div><div class="notif-drop-text">${n.text}</div><div class="notif-drop-time">${n.time}</div></div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  const dd = document.getElementById('notif-dropdown');
  if (dd) dd.style.display = notifOpen ? 'block' : 'none';
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    const dd = document.getElementById('notif-dropdown');
    if (dd) dd.style.display = 'none';
  }
});
function markRead(el) {
  el.classList.remove('unread');
  updateNotifCount();
}
function markAllRead() {
  document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('.notif-drop-item.unread').length;
  const badge = document.getElementById('notif-count');
  if (badge) { badge.textContent = unread; badge.style.display = unread > 0 ? 'inline' : 'none'; }
}
renderNotifList();

// ── TOAST ──────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

// ── THEME ──────────────────────────────────────────────────────────────────────
function applyTheme(theme) {
  if (theme === 'dark') {
    document.body.classList.add('dark');
  } else {
    document.body.classList.remove('dark');
  }
  showToast('Theme switched to ' + (theme === 'dark' ? 'Dark' : 'Light') + ' mode!');
}

// ── SETTINGS NAV ───────────────────────────────────────────────────────────────
function showSettingsSection(section, el) {
  ['profile','general','academic','notifications','security','system'].forEach(s => {
    const elem = document.getElementById('settings-' + s);
    if (elem) elem.style.display = 'none';
  });
  const target = document.getElementById('settings-' + section);
  if (target) target.style.display = 'block';
  document.querySelectorAll('.settings-nav-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}

// ── PROFILE PICTURE ────────────────────────────────────────────────────────────
function previewProfilePic(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const preview = document.getElementById('profile-pic-preview');
    preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
    showToast('Profile photo updated!');
  };
  reader.readAsDataURL(input.files[0]);
}

function resetProfilePic() {
  document.getElementById('profile-pic-preview').innerHTML = '';
  document.getElementById('pic-upload').value = '';
  showToast('Profile photo removed.');
}

// ── PERSONAL INFO ──────────────────────────────────────────────────────────────
function savePersonalInfo() {
  const first = document.getElementById('pi-firstname').value.trim();
  const last  = document.getElementById('pi-lastname').value.trim();
  const rank  = document.getElementById('pi-rank').value;
  if (!first || !last) { alert('First and Last name are required.'); return; }
  const sbName = document.getElementById('sb-name');
  const sbRole = document.getElementById('sb-role');
  if (sbName) sbName.textContent = first + ' ' + last;
  if (sbRole) sbRole.textContent = rank;
  showToast('Personal info saved — ' + first + ' ' + last + '!');
}

// ── NOTIFICATION TOGGLE SWITCHES ────────────────────────────────────────────────
function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (input.checked) { track.classList.add('on'); }
  else { track.classList.remove('on'); }
  showToast('Notification preference updated!');
}
</script>
</body>
</html>