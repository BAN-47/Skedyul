<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Settings</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex h-screen overflow-hidden">
  @include('partials.chair_sidebar')

  <main class="flex-1 overflow-hidden">
    @include('partials.chair_header', ['title' => 'Settings', 'badgeText' => 'BSIS · AY 2025–26 · 1st Sem'])

    <div class="page-content">
      <div class="mb-5">
        <div class="text-[20px] font-extrabold text-slate-900">Settings</div>
        <div class="mt-1 text-[13px] text-slate-500">Configure SKEDYUL for your department</div>
      </div>

      <div class="grid grid-cols-1 gap-5 xl:grid-cols-[220px_minmax(0,1fr)]">
        <aside class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm">
          <div class="mb-2 px-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Settings</div>
          <button class="w-full rounded-xl bg-blue-50 px-3 py-2 text-left text-sm font-semibold text-blue-700" onclick="showSection('general', this)">General</button>
          <button class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="showSection('academic', this)">Academic Year</button>
          <button class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="showSection('workload', this)">Workload Rules</button>
          <button class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="showSection('notifications', this)">Notifications</button>
          <button class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="showSection('security', this)">Security</button>
          <button class="mt-2 w-full rounded-xl px-3 py-2 text-left text-sm font-semibold text-slate-600 hover:bg-slate-50" onclick="showSection('system', this)">System Info</button>
        </aside>

        <div id="settings-content" class="space-y-5">
          <div id="settings-general" class="space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-4">
                <div class="text-[15px] font-bold text-slate-900">Institution Details</div>
                <div class="mt-1 text-[12px] text-slate-500">Basic information about your school</div>
              </div>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Institution Name</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="Cebu Technological University">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Campus</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="Main Campus">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">College / Unit</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="College of Computing, Information and Communications Technology">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Abbreviation</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="CCICT">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Contact Email</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="email" value="ccict@ctu.edu.ph">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Phone</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="(032) 401-7777">
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700" onclick="showToast('Institution details saved!')">Save Changes</button>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-4">
                <div class="text-[15px] font-bold text-slate-900">Appearance</div>
                <div class="mt-1 text-[12px] text-slate-500">Theme, language, and display format preferences</div>
              </div>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Theme</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>Light</option><option>Dark</option></select>
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Language</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>English</option><option>Filipino</option><option>Cebuano</option></select>
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Time Format</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>12-hour (AM/PM)</option><option>24-hour</option></select>
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Date Format</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option>MM/DD/YYYY</option><option selected>DD/MM/YYYY</option><option>YYYY-MM-DD</option></select>
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700" onclick="showToast('Appearance settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <div id="settings-academic" class="hidden space-y-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-4 flex items-center justify-between gap-3">
                <div>
                  <div class="text-[15px] font-bold text-slate-900">Current Academic Year</div>
                  <div class="mt-1 text-[12px] text-slate-500">Active semester configuration</div>
                </div>
                <span class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Active</span>
              </div>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Academic Year</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" value="2025–2026">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Semester</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select>
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Start Date</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="date" value="2025-08-11">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">End Date</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="date" value="2025-12-20">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Schedule Submission Deadline</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="date" value="2025-07-25">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">School Days</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>Monday – Saturday</option><option>Monday – Friday</option></select>
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700" onclick="showToast('Academic year settings saved!')">Save Changes</button>
              </div>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
              <div class="mb-4">
                <div class="text-[15px] font-bold text-slate-900">Schedule Constraints</div>
                <div class="mt-1 text-[12px] text-slate-500">Define scheduling rules for this semester</div>
              </div>
              <div class="grid gap-4 md:grid-cols-2">
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Max Faculty Load (hrs / week)</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="number" value="30" min="1" max="60">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Min Faculty Load (hrs / week)</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="number" value="12" min="1" max="60">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Class Start Time</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="time" value="07:00">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Class End Time</label>
                  <input class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500" type="time" value="21:00">
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Class Duration</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>90 minutes (1.5 hrs)</option><option>60 minutes</option><option>120 minutes</option></select>
                </div>
                <div>
                  <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Conflict Detection</label>
                  <select class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"><option selected>Enabled (strict)</option><option>Enabled (warnings only)</option><option>Disabled</option></select>
                </div>
              </div>
              <div class="mt-4 flex justify-end">
                <button class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700" onclick="showToast('Schedule constraints saved!')">Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
const CHAIR_NOTIFS = [
  { dot: 'var(--red)', text: '<b>Conflict Detected</b> — Maria Santos: GE 102 & IT 101 overlap Tue 7:00–8:30 AM.', time: 'Today, 08:30 AM', unread: true },
  { dot: 'var(--amber)', text: '<b>Near Max Load</b> — Felicitas Lagman is at 27u/30u (3u remaining).', time: 'Today, 08:00 AM', unread: true },
  { dot: 'var(--blue)', text: '<b>Reminder</b> — Schedule submission deadline is Friday.', time: 'Yesterday, 4:00 PM', unread: false },
];

function renderNotifList() {
  const list = document.getElementById('notif-list');
  if (!list) return;
  list.innerHTML = CHAIR_NOTIFS.map(n => `
    <div class="flex items-start gap-3 border-b border-slate-100 px-4 py-3 ${n.unread ? 'bg-slate-50' : ''}" onclick="markRead(this)">
      <div class="mt-1.5 h-2.5 w-2.5 rounded-full" style="background:${n.dot};"></div>
      <div class="min-w-0 flex-1">
        <div class="text-[12.5px] leading-relaxed text-slate-600">${n.text}</div>
        <div class="mt-1 text-[11px] text-slate-400">${n.time}</div>
      </div>
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
function markRead(el) { el.classList.remove('bg-slate-50'); updateNotifCount(); }
function markAllRead() { document.querySelectorAll('#notif-list > div').forEach(el => el.classList.remove('bg-slate-50')); updateNotifCount(); }
function updateNotifCount() {
  const unread = document.querySelectorAll('#notif-list > div.bg-slate-50').length;
  const badge = document.getElementById('notif-dot');
  if (badge) badge.style.display = unread > 0 ? 'inline-flex' : 'none';
}
function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3200);
}
function showSection(id, button) {
  document.querySelectorAll('[id^="settings-"]')
    .forEach(el => {
      if (el.id === 'settings-content') return;
      el.classList.add('hidden');
    });
  const target = document.getElementById('settings-' + id);
  if (target) target.classList.remove('hidden');
  document.querySelectorAll('aside button').forEach(btn => {
    btn.classList.remove('bg-blue-50', 'text-blue-700');
    btn.classList.add('text-slate-600');
  });
  button.classList.add('bg-blue-50', 'text-blue-700');
  button.classList.remove('text-slate-600');
}
renderNotifList();
</script>
</body>
</html>
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