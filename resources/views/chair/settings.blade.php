<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Settings</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/chair/settings.css') }}">
</head>
<style>
  html, body { overflow: hidden; }
  .sidebar-nav { scrollbar-width: none; -ms-overflow-style: none; }
  .sidebar-nav::-webkit-scrollbar { display: none; }
  .page-content {
    overflow-y: hidden;
    }
  .page-content::-webkit-scrollbar { display: none; }
</style>
<body>

<div class="screen active" style="display:flex;">
  @include('partials.chair_sidebar')

  <!-- MAIN -->
  <div class="main">

    <!-- TOPBAR -->
    <div class="topbar">
      <div class="topbar-title">Settings</div>
      <div class="topbar-semester-badge">BSIS · AY 2025–26 · 1st Sem</div>
      <div id="topbar-notif-bell" style="position:relative;">
        <button onclick="toggleNotifDropdown()" class="topbar-notif-btn">
          Notifications
          <span id="notif-dot" class="topbar-notif-dot"></span>
        </button>
        <div id="notif-dropdown" class="notif-dropdown">
          <div class="notif-dropdown-header">Notifications</div>
          <div id="notif-list" style="max-height:320px;overflow-y:auto;"></div>
          <div class="notif-dropdown-footer">
            <button onclick="markAllRead()" class="notif-mark-all-btn">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    <div class="page-content">

      <!-- Page header -->
      <div class="page-header">
        <div>
          <div class="page-heading">Settings</div>
          <div class="page-subheading">Configure SKEDYUL for your department</div>
        </div>
      </div>

      <!-- Settings layout: left nav + right content -->
      <div class="settings-layout">

        <!-- Left nav -->
        <div class="settings-leftnav">
          <div class="settings-leftnav-label">Settings</div>
          <button class="settings-navitem active"  onclick="showSection('general', this)">General</button>
          <button class="settings-navitem"          onclick="showSection('academic', this)">Academic Year</button>
          <button class="settings-navitem"          onclick="showSection('workload', this)">Workload Rules</button>
          <button class="settings-navitem"          onclick="showSection('notifications', this)">Notifications</button>
          <button class="settings-navitem"          onclick="showSection('security', this)">Security</button>
          <button class="settings-navitem"          onclick="showSection('system', this)">System Info</button>
        </div>

        <!-- Right content -->
        <div id="settings-content">

          <!-- ── GENERAL ── -->
          <div id="settings-general">

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Institution Details</div>
                  <div class="card-sub">Basic information about your school</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Institution Name</label>
                  <input class="field-input" value="Cebu Technological University">
                </div>
                <div class="field-group">
                  <label class="field-label">Campus</label>
                  <input class="field-input" value="Main Campus">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">College / Unit</label>
                  <input class="field-input" value="College of Computing, Information and Communications Technology">
                </div>
                <div class="field-group">
                  <label class="field-label">Abbreviation</label>
                  <input class="field-input" value="CCICT">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Contact Email</label>
                  <input class="field-input" type="email" value="ccict@ctu.edu.ph">
                </div>
                <div class="field-group">
                  <label class="field-label">Phone</label>
                  <input class="field-input" value="(032) 401-7777">
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Institution details saved!')">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Appearance</div>
                  <div class="card-sub">Theme, language, and display format preferences</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Theme</label>
                  <select class="field-select"><option selected>Light</option><option>Dark</option></select>
                </div>
                <div class="field-group">
                  <label class="field-label">Language</label>
                  <select class="field-select"><option selected>English</option><option>Filipino</option><option>Cebuano</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Time Format</label>
                  <select class="field-select"><option selected>12-hour (AM/PM)</option><option>24-hour</option></select>
                </div>
                <div class="field-group">
                  <label class="field-label">Date Format</label>
                  <select class="field-select"><option>MM/DD/YYYY</option><option selected>DD/MM/YYYY</option><option>YYYY-MM-DD</option></select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Appearance settings saved!')">Save Changes</button>
              </div>
            </div>

          </div><!-- end settings-general -->

          <!-- ── ACADEMIC YEAR ── -->
          <div id="settings-academic" style="display:none;">

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Current Academic Year</div>
                  <div class="card-sub">Active semester configuration</div>
                </div>
                <span class="badge badge-green">Active</span>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Academic Year</label>
                  <input class="field-input" value="2025–2026">
                </div>
                <div class="field-group">
                  <label class="field-label">Semester</label>
                  <select class="field-select"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Start Date</label>
                  <input class="field-input" type="date" value="2025-08-11">
                </div>
                <div class="field-group">
                  <label class="field-label">End Date</label>
                  <input class="field-input" type="date" value="2025-12-20">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Schedule Submission Deadline</label>
                  <input class="field-input" type="date" value="2025-07-25">
                </div>
                <div class="field-group">
                  <label class="field-label">School Days</label>
                  <select class="field-select"><option selected>Monday – Saturday</option><option>Monday – Friday</option></select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Academic year settings saved!')">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Schedule Constraints</div>
                  <div class="card-sub">Define scheduling rules for this semester</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Max Faculty Load (hrs / week)</label>
                  <input class="field-input" type="number" value="30" min="1" max="60">
                </div>
                <div class="field-group">
                  <label class="field-label">Min Faculty Load (hrs / week)</label>
                  <input class="field-input" type="number" value="12" min="1" max="60">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Class Start Time</label>
                  <input class="field-input" type="time" value="07:00">
                </div>
                <div class="field-group">
                  <label class="field-label">Class End Time</label>
                  <input class="field-input" type="time" value="21:00">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Class Duration</label>
                  <select class="field-select"><option selected>90 minutes (1.5 hrs)</option><option>60 minutes</option><option>120 minutes</option></select>
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

          </div><!-- end settings-academic -->

          <!-- ── WORKLOAD RULES ── -->
          <div id="settings-workload" style="display:none;">

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Faculty Load Limits</div>
                  <div class="card-sub">Define maximum and minimum unit loads per faculty type</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Max Faculty Load (units/week) — Full-time</label>
                  <input class="field-input" type="number" value="30">
                </div>
                <div class="field-group">
                  <label class="field-label">Min Faculty Load (units/week) — Full-time</label>
                  <input class="field-input" type="number" value="12">
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Max Faculty Load (units/week) — Part-time</label>
                  <input class="field-input" type="number" value="18">
                </div>
                <div class="field-group">
                  <label class="field-label">Near-Max Warning Threshold</label>
                  <select class="field-select"><option selected>3 units before max</option><option>6 units before max</option><option>9 units before max</option></select>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Load limits saved!')">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Conflict Detection Rules</div>
                  <div class="card-sub">Control how scheduling conflicts are enforced</div>
                </div>
              </div>
              <div style="display:flex;flex-direction:column;gap:12px;margin-top:4px;">
                <div class="toggle-row">
                  <div>
                    <div class="toggle-row-title">Block Faculty Double-Booking</div>
                    <div class="toggle-row-sub">Prevent assigning one faculty to two subjects at the same time</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="toggle-row">
                  <div>
                    <div class="toggle-row-title">Block Room Double-Booking</div>
                    <div class="toggle-row-sub">Prevent same room from being assigned to two classes simultaneously</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="toggle-row">
                  <div>
                    <div class="toggle-row-title">Block Overload Assignments</div>
                    <div class="toggle-row-sub">Disable save button when assignment would exceed maximum units</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="toggle-row">
                  <div>
                    <div class="toggle-row-title">Require Conflict Resolution Before Submission</div>
                    <div class="toggle-row-sub">Block Dean submission if any unresolved conflicts exist</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Conflict rules saved!')">Save Changes</button>
              </div>
            </div>

          </div><!-- end settings-workload -->

          <!-- ── NOTIFICATIONS ── -->
          <div id="settings-notifications" style="display:none;">

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Notification Preferences</div>
                  <div class="card-sub">Choose what alerts you receive</div>
                </div>
              </div>
              <div style="display:flex;flex-direction:column;gap:10px;margin-top:4px;">
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Schedule Conflicts</div>
                    <div class="notif-row-sub">Get notified when a scheduling conflict is detected</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Faculty Overload</div>
                    <div class="notif-row-sub">Notify when a faculty member exceeds their max load</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Faculty Near-Max Load</div>
                    <div class="notif-row-sub">Warn when a faculty is within 3 units of their maximum</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Dean Approval Status</div>
                    <div class="notif-row-sub">Notify when the Dean approves or returns the schedule</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Submission Deadline Reminder</div>
                    <div class="notif-row-sub">Remind 3 days before the schedule submission deadline</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Email on Faculty Assignment</div>
                    <div class="notif-row-sub">Send email to faculty when a subject is assigned to them</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="notif-row">
                  <div>
                    <div class="notif-row-title">Login Activity</div>
                    <div class="notif-row-sub">Notify on new logins from unrecognized devices</div>
                  </div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:16px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Notification settings saved!')">Save Preferences</button>
              </div>
            </div>

          </div><!-- end settings-notifications -->

          <!-- ── SECURITY ── -->
          <div id="settings-security" style="display:none;">

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Personal Information</div>
                  <div class="card-sub">Your professional profile — read-only</div>
                </div>
                <span class="badge badge-grey">Read-only</span>
              </div>
              <div class="profile-banner">
                <div class="profile-banner-avatar">RT</div>
                <div>
                  <div class="profile-banner-name">Rodrigo Tan</div>
                  <div class="profile-banner-role">Department Chair · BSIS · CCICT</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Full Name</label>
                  <div class="field-readonly">Rodrigo Tan</div>
                </div>
                <div class="field-group">
                  <label class="field-label">Employee ID</label>
                  <div class="field-readonly">CTU-2019-0042</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Email Address</label>
                  <div class="field-readonly">r.tan@ctu.edu.ph</div>
                </div>
                <div class="field-group">
                  <label class="field-label">Contact Number</label>
                  <div class="field-readonly">(032) 401-1111</div>
                </div>
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">Office Location</label>
                  <div class="field-readonly">Room 301, ICT Building</div>
                </div>
                <div class="field-group">
                  <label class="field-label">Role</label>
                  <div class="field-readonly">Department Chair</div>
                </div>
              </div>
              <div class="info-note">
                Professional information is managed by the Technical Administrator. Contact the system administrator to request changes.
              </div>
            </div>

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">Change Password</div>
                  <div class="card-sub">Update your account password</div>
                </div>
              </div>
              <div class="field-group">
                <label class="field-label">Current Password</label>
                <input class="field-input" type="password" placeholder="Enter your current password">
              </div>
              <div class="form-row">
                <div class="field-group">
                  <label class="field-label">New Password</label>
                  <input class="field-input" type="password" placeholder="Min. 8 characters">
                </div>
                <div class="field-group">
                  <label class="field-label">Confirm New Password</label>
                  <input class="field-input" type="password" placeholder="Re-enter new password">
                </div>
              </div>
              <div style="display:flex;justify-content:flex-end;margin-top:4px;">
                <button class="topbar-btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Session &amp; Access</div>
                  <div class="card-sub">Manage login security settings</div>
                </div>
              </div>
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

          </div><!-- end settings-security -->

          <!-- ── SYSTEM INFO ── -->
          <div id="settings-system" style="display:none;">

            <div class="card" style="margin-bottom:16px;">
              <div class="card-header">
                <div>
                  <div class="card-title">System Information</div>
                  <div class="card-sub">Current environment details</div>
                </div>
                <span class="badge badge-green">All Systems Normal</span>
              </div>
              <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;">
                <div class="env-cell"><div class="env-cell-label">Application</div><div class="env-cell-val">SKEDYUL v1.0.0</div></div>
                <div class="env-cell"><div class="env-cell-label">Host / Deploy</div><div class="env-cell-val">Vercel (Production)</div></div>
                <div class="env-cell"><div class="env-cell-label">Database</div><div class="env-cell-val">Supabase PostgreSQL</div></div>
                <div class="env-cell"><div class="env-cell-label">Authentication</div><div class="env-cell-val">JWT + Laravel Sanctum</div></div>
                <div class="env-cell"><div class="env-cell-label">Frontend Stack</div><div class="env-cell-val">Tailwind CSS 4 + Vanilla JS</div></div>
                <div class="env-cell"><div class="env-cell-label">Mobile App</div><div class="env-cell-val">React Native</div></div>
                <div class="env-cell"><div class="env-cell-label">Last Backup</div><div class="env-cell-val" style="color:var(--green);">Today, 06:00 AM</div></div>
                <div class="env-cell"><div class="env-cell-label">Uptime (30 days)</div><div class="env-cell-val" style="color:var(--green);">99.98%</div></div>
              </div>
            </div>

            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title" style="color:var(--red);">Danger Zone</div>
                  <div class="card-sub">Irreversible actions — proceed with caution</div>
                </div>
              </div>
              <div style="display:flex;flex-direction:column;gap:10px;">
                <div class="danger-row">
                  <div>
                    <div class="danger-row-title">Clear All Schedules</div>
                    <div class="danger-row-sub">Removes all schedule assignments for the current semester</div>
                  </div>
                  <button class="btn-danger-sm" onclick="showToast('Action cancelled — confirmation required.')">Clear</button>
                </div>
                <div class="danger-row">
                  <div>
                    <div class="danger-row-title">Reset System Data</div>
                    <div class="danger-row-sub">Wipes all records and resets to factory state</div>
                  </div>
                  <button class="btn-danger-sm" onclick="showToast('Action cancelled — confirmation required.')">Reset</button>
                </div>
              </div>
            </div>

          </div><!-- end settings-system -->

        </div><!-- end settings-content -->
      </div><!-- end settings-layout -->

    </div><!-- end page-content -->
  </div><!-- end main -->
</div>

<!-- TOAST -->
<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
// ── NOTIFICATIONS ─────────────────────────────────────────────────────────────
const CHAIR_NOTIFS = [
  { dot:'var(--red)',   text:'<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time:'Today, 08:30 AM', unread:true },
  { dot:'var(--amber)', text:'<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time:'Today, 08:00 AM', unread:true },
  { dot:'var(--blue)',  text:'<b>Reminder</b> — Schedule submission deadline is Friday.', time:'Yesterday, 4:00 PM', unread:false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
    <div class="notif-drop-item ${n.unread ? 'unread' : ''}" onclick="markRead(this)">
      <div class="notif-drop-dot" style="background:${n.dot};"></div>
      <div>
        <div class="notif-drop-text">${n.text}</div>
        <div class="notif-drop-time">${n.time}</div>
      </div>
    </div>`).join('');
  updateNotifCount();
}

let notifOpen = false;
function toggleNotifDropdown() {
  notifOpen = !notifOpen;
  document.getElementById('notif-dropdown').classList.toggle('open', notifOpen);
}
document.addEventListener('click', e => {
  const bell = document.getElementById('topbar-notif-bell');
  if (bell && !bell.contains(e.target)) {
    notifOpen = false;
    document.getElementById('notif-dropdown').classList.remove('open');
  }
});
function markRead(el) { el.classList.remove('unread'); updateNotifCount(); }
function markAllRead() {
  document.querySelectorAll('.notif-drop-item.unread').forEach(el => el.classList.remove('unread'));
  updateNotifCount();
}
function updateNotifCount() {
  const unread = document.querySelectorAll('.notif-drop-item.unread').length;
  const dot = document.getElementById('notif-dot');
  if (dot) dot.style.display = unread > 0 ? 'block' : 'none';
}
renderNotifList();

// ── SETTINGS NAV ──────────────────────────────────────────────────────────────
const SECTIONS = ['general','academic','workload','notifications','security','system'];

function showSection(id, el) {
  SECTIONS.forEach(s => {
    const elem = document.getElementById('settings-' + s);
    if (elem) elem.style.display = 'none';
  });
  const target = document.getElementById('settings-' + id);
  if (target) target.style.display = 'block';
  document.querySelectorAll('.settings-navitem').forEach(i => i.classList.remove('active'));
  el.classList.add('active');
}

// ── TOGGLE SWITCHES ───────────────────────────────────────────────────────────
function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (track) {
    if (input.checked) { track.classList.add('on'); }
    else               { track.classList.remove('on'); }
  }
}

// ── TOAST ──────────────────────────────────────────────────────────────────────
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
</script>
</body>
</html>