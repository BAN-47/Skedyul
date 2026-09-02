<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Settings</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

  @include('partials.admin_sidebar')

  <div class="app-main">
    <div class="topbar">
      <div class="topbar-title" id="topbar-title">Settings</div>
      <div id="topbar-notif-bell" class="relative">
        <button onclick="toggleNotifDropdown()"
          class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-slate-100 text-slate-500 text-[13px] font-semibold">
          Notifications <span id="notif-count" class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">3</span>
        </button>
        <div id="notif-dropdown" style="display:none;"
          class="absolute top-11 right-0 w-[340px] bg-white border border-slate-200 rounded-2xl shadow-[0_8px_32px_rgba(0,0,0,.12)] z-[100] overflow-hidden">
          <div class="px-4 py-3.5 border-b border-slate-200 text-sm font-bold text-slate-900">Notifications</div>
          <div id="notif-list" class="max-h-80 overflow-y-auto"></div>
          <div class="px-4 py-2.5 border-t border-slate-200 text-center">
            <button onclick="markAllRead()" class="text-[12px] text-blue-600 font-semibold bg-transparent border-none cursor-pointer">Mark all as read</button>
          </div>
        </div>
      </div>
    </div>

    <div class="page-content">

      <div class="grid grid-cols-[220px_1fr] gap-6 items-start">

        {{-- Left nav --}}
        <div class="card py-3 px-0">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">Settings</div>
          <div class="settings-nav-item active" onclick="showSettingsSection('profile',this)">Personal Info</div>
          <div class="settings-nav-item" onclick="showSettingsSection('general',this)">General</div>
          <div class="settings-nav-item" onclick="showSettingsSection('academic',this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showSettingsSection('notifications',this)">Notifications</div>
          <div class="settings-nav-item" onclick="showSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showSettingsSection('system',this)">System Info</div>
        </div>

        {{-- Right content --}}
        <div id="settings-content">

          {{-- PERSONAL INFO --}}
          <div id="settings-profile">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Profile Picture</div><div class="card-sub">Click the avatar to upload a new photo</div></div></div>
              <div class="flex items-center gap-6 py-2">
                <div class="relative flex-shrink-0">
                  <div id="profile-pic-preview" class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-3xl font-extrabold text-white border-4 border-slate-200 overflow-hidden"></div>
                  <div onclick="document.getElementById('pic-upload').click()"
                    class="absolute bottom-0 right-0 w-[30px] h-[30px] bg-blue-600 rounded-full flex items-center justify-center cursor-pointer border-2 border-white text-sm">✏️</div>
                  <input type="file" id="pic-upload" accept="image/*" class="hidden" onchange="previewProfilePic(this)">
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-900">Tech Admin</div>
                  <div class="text-xs text-slate-400 mt-0.5">Technical Administrator · CCICT</div>
                  <div class="flex gap-2 mt-3">
                    <button class="btn btn-primary text-xs px-3.5 py-1.5" onclick="document.getElementById('pic-upload').click()">Upload Photo</button>
                    <button class="btn btn-secondary text-xs px-3.5 py-1.5" onclick="resetProfilePic()">Remove</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your name, rank, and contact details</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">First Name</label><input class="field-input" id="pi-firstname" value="Tech"></div>
                <div><label class="field-label">Last Name</label><input class="field-input" id="pi-lastname" value="Admin"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Middle Name</label><input class="field-input" id="pi-middlename" placeholder="Optional"></div>
                <div><label class="field-label">Suffix</label>
                  <select class="field-input" id="pi-suffix"><option value="">None</option><option>Jr.</option><option>Sr.</option><option>II</option><option>III</option></select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Rank / Title</label>
                  <select class="field-input" id="pi-rank">
                    <option selected>Technical Administrator</option>
                    <option>Senior Technical Administrator</option>
                    <option>IT Officer</option>
                    <option>Systems Analyst</option>
                    <option>Network Administrator</option>
                  </select>
                </div>
                <div><label class="field-label">Employee ID</label><input class="field-input" id="pi-empid" value="CTU-2024-001"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Gender</label>
                  <select class="field-input" id="pi-gender"><option>Male</option><option>Female</option><option>Prefer not to say</option></select>
                </div>
                <div><label class="field-label">Civil Status</label>
                  <select class="field-input" id="pi-civil"><option>Single</option><option>Married</option><option>Widowed</option><option>Separated</option></select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Date of Birth</label><input class="field-input" id="pi-dob" type="date" value="1990-01-01"></div>
                <div><label class="field-label">Nationality</label><input class="field-input" id="pi-nationality" value="Filipino"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Contact & Office Details</div><div class="card-sub">How others can reach you</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Email Address</label><input class="field-input" id="pi-email" type="email" value="admin@ctu.edu.ph"></div>
                <div><label class="field-label">Phone Number</label><input class="field-input" id="pi-phone" value="(032) 401-0000"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Office Location</label><input class="field-input" id="pi-office" value="ICT Building, Room 100"></div>
                <div><label class="field-label">Department</label><input class="field-input bg-slate-100 cursor-not-allowed" id="pi-dept" value="CCICT" readonly></div>
              </div>
              <div class="mb-4"><label class="field-label">Bio / About</label><textarea class="field-input resize-y" id="pi-bio" rows="3" placeholder="Brief description about yourself...">Technical Administrator of the CCICT, Cebu Technological University.</textarea></div>
              <div class="flex justify-end">
                <button class="btn btn-primary" onclick="showToast('Contact details saved successfully!')">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- GENERAL --}}
          <div id="settings-general" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Institution Details</div><div class="card-sub">Basic information about your school</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Institution Name</label><input class="field-input" value="Cebu Technological University"></div>
                <div><label class="field-label">Campus</label><input class="field-input" value="Main Campus"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">College / Unit</label><input class="field-input" value="College of Computing, Information and Communications Technology"></div>
                <div><label class="field-label">Abbreviation</label><input class="field-input" value="CCICT"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Contact Email</label><input class="field-input" type="email" value="ccict@ctu.edu.ph"></div>
                <div><label class="field-label">Phone</label><input class="field-input" value="(032) 401-7777"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Institution details saved!')">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><div><div class="card-title">Appearance</div><div class="card-sub">Customize the look of SKEDYUL</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">Theme</label>
                  <select class="field-input" id="theme-select" onchange="applyTheme(this.value)">
                    <option value="light" selected>Light</option>
                    <option value="dark">Dark</option>
                  </select>
                </div>
                <div>
                  <label class="field-label">Language</label>
                  <select class="field-input">
                    <option selected>English</option>
                    <option>Filipino</option>
                    <option>Cebuano</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">Date Format</label>
                  <select class="field-input">
                    <option>MM/DD/YYYY</option>
                    <option selected>DD/MM/YYYY</option>
                    <option>YYYY-MM-DD</option>
                  </select>
                </div>
                <div>
                  <label class="field-label">Time Format</label>
                  <select class="field-input">
                    <option selected>12-hour (AM/PM)</option>
                    <option>24-hour</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Appearance settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- ACADEMIC YEAR --}}
          <div id="settings-academic" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Current Academic Year</div><div class="card-sub">Active semester configuration</div></div><span class="badge badge-green">Active</span></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Academic Year</label><input class="field-input" value="2025–2026"></div>
                <div>
                  <label class="field-label">Semester</label>
                  <select class="field-input"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Start Date</label><input class="field-input" type="date" value="2025-08-11"></div>
                <div><label class="field-label">End Date</label><input class="field-input" type="date" value="2025-12-20"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Academic year settings saved!')">Save Changes</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Schedule Constraints</div><div class="card-sub">Define scheduling rules for this semester</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Max Faculty Load (hrs/week)</label><input class="field-input" type="number" value="30"></div>
                <div><label class="field-label">Min Faculty Load (hrs/week)</label><input class="field-input" type="number" value="12"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Class Start Time</label><input class="field-input" type="time" value="07:00"></div>
                <div><label class="field-label">Class End Time</label><input class="field-input" type="time" value="21:00"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">School Days</label>
                  <select class="field-input"><option selected>Monday – Saturday</option><option>Monday – Friday</option></select>
                </div>
                <div>
                  <label class="field-label">Conflict Detection</label>
                  <select class="field-input"><option selected>Enabled (strict)</option><option>Enabled (warnings only)</option><option>Disabled</option></select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Schedule constraints saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- NOTIFICATIONS --}}
          <div id="settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive</div></div></div>
              <div class="flex flex-col gap-4 mt-1">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Schedule Conflicts</div><div class="text-xs text-slate-400 mt-0.5">Get notified when a scheduling conflict is detected</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">New User Registration</div><div class="text-xs text-slate-400 mt-0.5">Alert when a new account is created or pending approval</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Faculty Overload</div><div class="text-xs text-slate-400 mt-0.5">Notify when a faculty member exceeds their max load</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Room Double-Booking</div><div class="text-xs text-slate-400 mt-0.5">Alert when a room is assigned to two classes at the same time</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">System Backups</div><div class="text-xs text-slate-400 mt-0.5">Receive confirmation after each automatic backup</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Login Activity</div><div class="text-xs text-slate-400 mt-0.5">Notify on new logins from unrecognized devices</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div class="flex justify-end mt-4">
                <button class="btn btn-primary" onclick="showToast('Notification settings saved!')">Save Preferences</button>
              </div>
            </div>
          </div>

          {{-- SECURITY --}}
          <div id="settings-security" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your admin account password</div></div></div>
              <div class="mb-3.5"><label class="field-label">Current Password</label><input class="field-input" type="password" placeholder="••••••••"></div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">New Password</label><input class="field-input" type="password" placeholder="Min. 8 characters"></div>
                <div><label class="field-label">Confirm New Password</label><input class="field-input" type="password" placeholder="Re-enter new password"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Session & Access</div><div class="card-sub">Manage login security settings</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">Session Timeout</label>
                  <select class="field-input"><option>15 minutes</option><option selected>30 minutes</option><option>1 hour</option><option>Never</option></select>
                </div>
                <div>
                  <label class="field-label">Max Login Attempts</label>
                  <select class="field-input"><option>3</option><option selected>5</option><option>10</option></select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Security settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- SYSTEM INFO --}}
          <div id="settings-system" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">System Information</div><div class="card-sub">Current environment details</div></div></div>
              <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Application</div><div class="text-[13px] font-semibold text-slate-900">SKEDYUL v1.0.0</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Host</div><div class="text-[13px] font-semibold text-slate-900">Vercel (Production)</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Database</div><div class="text-[13px] font-semibold text-slate-900">Supabase PostgreSQL</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Auth</div><div class="text-[13px] font-semibold text-slate-900">JWT + Laravel Sanctum</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Frontend</div><div class="text-[13px] font-semibold text-slate-900">Tailwind CSS + Vanilla JS</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Mobile App</div><div class="text-[13px] font-semibold text-slate-900">React Native</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Last Backup</div><div class="text-[13px] font-semibold text-green-600">Today, 06:00 AM ✓</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Uptime</div><div class="text-[13px] font-semibold text-slate-900">99.98% (last 30 days)</div></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">Danger Zone</div><div class="card-sub">Irreversible actions — proceed with caution</div></div></div>
              <div class="flex flex-col gap-2.5">
                <div class="flex items-center justify-between p-3.5 border border-red-100 rounded-lg bg-red-50">
                  <div><div class="text-[13px] font-semibold text-slate-900">Clear All Schedules</div><div class="text-xs text-slate-400">Removes all schedule assignments for the current semester</div></div>
                  <button class="btn btn-danger text-xs px-3.5 py-1.5" onclick="showToast('Action cancelled — confirmation required.')">Clear</button>
                </div>
                <div class="flex items-center justify-between p-3.5 border border-red-100 rounded-lg bg-red-50">
                  <div><div class="text-[13px] font-semibold text-slate-900">Reset System Data</div><div class="text-xs text-slate-400">Wipes all records and resets to factory state</div></div>
                  <button class="btn btn-danger text-xs px-3.5 py-1.5" onclick="showToast('Action cancelled — confirmation required.')">Reset</button>
                </div>
              </div>
            </div>
          </div>

        </div>{{-- end settings-content --}}
      </div>
    </div>

  </div>
</div>

<div class="toast" id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── NOTIFICATION BELL ────────────────────────────────────────────────────────
const ADMIN_NOTIFS = [
  { dot:'#dc2626', text:'<b>Conflict Detected</b> — GE002 Room 205 double-booked Wed 1PM.', time:'Today, 08:30 AM', unread:true },
  { dot:'#d97706', text:'<b>Faculty Overload</b> — Carlo Mendoza at 31h/30h max load.', time:'Today, 08:00 AM', unread:true },
  { dot:'#2563eb', text:'<b>New User Pending</b> — Ana Reyes account awaiting verification.', time:'Yesterday, 4:00 PM', unread:true },
  { dot:'#16a34a', text:'<b>Backup Complete</b> — System backup successful at 06:00 AM.', time:'Today, 06:00 AM', unread:false },
  { dot:'#2563eb', text:'<b>User Created</b> — New faculty account created for Liza Cruz.', time:'Yesterday, 7:55 AM', unread:false },
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