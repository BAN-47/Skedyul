<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Settings</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

<div class="app-shell">

  @include('partials.admin_sidebar')

  <div class="app-main">
        @include('partials.admin_header', ['title' => 'Technical Admin settings'])


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
                  <div id="profile-pic-preview"
                      class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center text-3xl font-extrabold text-white border-4 border-slate-200 overflow-hidden"
                      @if(auth()->user()->profile_picture)
                        style="background-image: url('{{ auth()->user()->profile_picture }}'); background-size: cover; background-position: center;"
                      @endif>
                    @unless(auth()->user()->profile_picture)
                      {{ strtoupper(substr(auth()->user()->usr_name, 0, 1)) }}
                    @endunless
                  </div>
                  <div onclick="document.getElementById('pic-upload').click()"
                    class="absolute bottom-0 right-0 w-[30px] h-[30px] bg-blue-600 rounded-full flex items-center justify-center cursor-pointer border-2 border-white text-sm">✏️</div>
                  <input type="file" id="pic-upload" accept="image/*" class="hidden" onchange="previewProfilePic(this)">
                </div>
                <div>
                  <div class="text-sm font-bold text-slate-900">{{ auth()->user()->usr_name }}</div>
                  <div class="text-xs text-slate-400 mt-0.5">{{ ucfirst(auth()->user()->usr_role) }} · CCICT</div>
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
              <div><label class="field-label">First Name</label><input class="field-input" id="pi-firstname" value="{{ auth()->user()->usr_first_name }}"></div>
              <div><label class="field-label">Last Name</label><input class="field-input" id="pi-lastname" value="{{ auth()->user()->usr_last_name }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">Middle Name</label><input class="field-input" id="pi-middlename" placeholder="Optional" value="{{ auth()->user()->usr_middle_name }}"></div>
              <div><label class="field-label">Suffix</label>
                <select class="field-input" id="pi-suffix">
                  <option value="" {{ !auth()->user()->usr_suffix ? 'selected' : '' }}>None</option>
                  <option {{ auth()->user()->usr_suffix === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                  <option {{ auth()->user()->usr_suffix === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                  <option {{ auth()->user()->usr_suffix === 'II' ? 'selected' : '' }}>II</option>
                  <option {{ auth()->user()->usr_suffix === 'III' ? 'selected' : '' }}>III</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">Rank / Title</label><input class="field-input" id="rank-title" value="{{ auth()->user()->usr_rank_title }}"></div>
              <div><label class="field-label">Employee ID</label><input class="field-input" id="pi-empid" value="{{ auth()->user()->usr_employee_id }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">Gender</label>
                <select class="field-input" id="pi-gender">
                  <option {{ auth()->user()->usr_gender === 'Male' ? 'selected' : '' }}>Male</option>
                  <option {{ auth()->user()->usr_gender === 'Female' ? 'selected' : '' }}>Female</option>
                  <option {{ auth()->user()->usr_gender === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                </select>
              </div>
              <div><label class="field-label">Civil Status</label>
                <select class="field-input" id="pi-civil">
                  <option {{ auth()->user()->usr_civil_status === 'Single' ? 'selected' : '' }}>Single</option>
                  <option {{ auth()->user()->usr_civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                  <option {{ auth()->user()->usr_civil_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                  <option {{ auth()->user()->usr_civil_status === 'Separated' ? 'selected' : '' }}>Separated</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-1">
              <div><label class="field-label">Date of Birth</label><input class="field-input" id="pi-dob" type="date" value="{{ auth()->user()->usr_dob }}"></div>
              <div><label class="field-label">Nationality</label><input class="field-input" id="pi-nationality" value="{{ auth()->user()->usr_nationality }}"></div>
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
  const file = input.files[0];
  const preview = document.getElementById('profile-pic-preview');

  // instant local preview while it uploads
  const reader = new FileReader();
  reader.onload = e => {
    preview.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;">`;
  };
  reader.readAsDataURL(file);

  // real upload to Cloudinary
  const formData = new FormData();
  formData.append('profile_picture', file);

  fetch('{{ route("admin.profile.picture.update") }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
    body: formData,
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        showToast('❌ Upload failed');
        return;
      }
      preview.innerHTML = `<img src="${data.url}" style="width:100%;height:100%;object-fit:cover;">`;
      showToast('Profile photo updated!');
    })
    .catch(() => showToast('❌ Something went wrong uploading the photo.'));
}

function resetProfilePic() {
  if (!confirm('Remove your profile picture?')) return;

  fetch('{{ route("admin.profile.picture.remove") }}', {
    method: 'DELETE',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
  })
    .then(res => res.json())
    .then(() => {
      document.getElementById('profile-pic-preview').innerHTML = '{{ strtoupper(substr(auth()->user()->usr_name, 0, 1)) }}';
      document.getElementById('pic-upload').value = '';
      showToast('Profile photo removed.');
    })
    .catch(() => showToast('❌ Something went wrong removing the photo.'));
}

// ── PERSONAL INFO ──────────────────────────────────────────────────────────────
function savePersonalInfo() {
  const first = document.getElementById('pi-firstname').value.trim();
  const last  = document.getElementById('pi-lastname').value.trim();
  if (!first || !last) { alert('First and Last name are required.'); return; }

  const payload = {
    usr_first_name: first,
    usr_last_name: last,
    usr_middle_name: document.getElementById('pi-middlename').value.trim(),
    usr_suffix: document.getElementById('pi-suffix').value,
    usr_rank_title: document.getElementById('rank-title').value.trim(),
    usr_employee_id: document.getElementById('pi-empid').value.trim(),
    usr_gender: document.getElementById('pi-gender').value,
    usr_civil_status: document.getElementById('pi-civil').value,
    usr_dob: document.getElementById('pi-dob').value,
    usr_nationality: document.getElementById('pi-nationality').value.trim(),
  };

  fetch('{{ route("admin.profile.personal-info.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        showToast('❌ Failed to save changes');
        return;
      }
      const sbName = document.getElementById('sb-name');
      const sbRole = document.getElementById('sb-role');
      if (sbName) sbName.textContent = data.usr_name;
      if (sbRole) sbRole.textContent = document.getElementById('rank-title').value;
      showToast('Personal info saved successfully!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
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