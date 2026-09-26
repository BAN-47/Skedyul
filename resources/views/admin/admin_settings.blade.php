<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Settings</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 overflow-hidden h-screen">

<div class="app-shell">

  @include('partials.admin_sidebar')

  <div class="app-main">
        @include('partials.admin_header', ['title' => 'Technical Admin settings'])


    <div class="page-content">

      <div class="grid grid-cols-[220px_1fr] gap-6 items-start">

        {{-- Left nav --}}
        <div class="card py-3 px-0">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">Settings</div>
          <div class="settings-nav-item active" onclick="showSettingsSection('general',this)">General</div>
          <div class="settings-nav-item" onclick="showSettingsSection('academic',this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showSettingsSection('notifications',this)">Notifications</div>
          <div class="settings-nav-item" onclick="showSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showSettingsSection('system',this)">System Info</div>
        </div>

        {{-- Right content --}}
          {{-- GENERAL --}}
          <div id="settings-general">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your name, rank, and contact details</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">First Name</label><input class="field-input" id="pi-firstname" value="{{ auth()->user()->usr_first_name }}"></div>
                <div><label class="field-label">Last Name</label><input class="field-input" id="pi-lastname" value="{{ auth()->user()->usr_last_name }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
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
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
              </div>
            </div>
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Institution Details</div><div class="card-sub">Basic information about your school</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Institution Name</label><input class="field-input" id="inst-name" value="{{ optional(\App\Models\Institution::first())->inst_name ?? 'Cebu Technological University' }}"></div>
                <div><label class="field-label">Branch / Campus</label><input class="field-input" id="inst-campus" value="{{ optional(\App\Models\Institution::first())->inst_branch_campus ?? 'Main Campus' }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">College / Unit</label><input class="field-input" id="inst-college" value="{{ optional(\App\Models\Institution::first())->inst_college ?? 'College of Computing, Information and Communications Technology' }}"></div>
                <div><label class="field-label">Abbreviation</label><input class="field-input" id="inst-abbr" value="{{ optional(\App\Models\Institution::first())->inst_abbreviation ?? 'CCICT' }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Contact Email</label><input class="field-input" id="inst-email" type="email" value="{{ optional(\App\Models\Institution::first())->inst_contact_email ?? 'ccict@ctu.edu.ph' }}"></div>
                <div><label class="field-label">Phone</label><input class="field-input" id="inst-phone" value="{{ optional(\App\Models\Institution::first())->inst_phone ?? '(032) 401-7777' }}"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="saveInstitution()">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- ACADEMIC YEAR --}}
          @php
              $activeSemester = \App\Models\Semester::where('sem_is_active', true)->with('academicYear')->first();
              $activeAY = $activeSemester?->academicYear;
          @endphp

          <div id="settings-academic" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Current Academic Year</div><div class="card-sub">Active semester configuration</div></div><span class="badge badge-green">Active</span></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Academic Year</label><input class="field-input" id="ay-year" value="{{ $activeAY->ay_academic_year ?? '2025–2026' }}"></div>
                <div>
                  <label class="field-label">Semester</label>
                  <select class="field-input" id="ay-semester">
                    <option {{ ($activeSemester->sem_name ?? '') === '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                    <option {{ ($activeSemester->sem_name ?? '') === '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    <option {{ ($activeSemester->sem_name ?? '') === 'Summer' ? 'selected' : '' }}>Summer</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Start Date</label><input class="field-input" id="ay-start" type="date" value="{{ $activeSemester->sem_start_date?->format('Y-m-d') ?? '2025-08-11' }}"></div>
                <div><label class="field-label">End Date</label><input class="field-input" id="ay-end" type="date" value="{{ $activeSemester->sem_end_date?->format('Y-m-d') ?? '2025-12-20' }}"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="saveAcademicYear()">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- NOTIFICATIONS --}}
          <div id="settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive</div></div></div>
              <div class="flex flex-col gap-4 mt-1">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">New User Registration</div><div class="text-xs text-slate-400 mt-0.5">Alert when a new account is created or pending approval</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="notif_new_user_registration" {{ (bool) \App\Models\SystemSetting::get('notif_new_user_registration', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ auth()->user()->notif_new_user_registration ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Faculty Overload</div><div class="text-xs text-slate-400 mt-0.5">Notify when a faculty member exceeds their max load</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="notif_faculty_overload" {{ (bool) \App\Models\SystemSetting::get('notif_faculty_overload', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ auth()->user()->notif_faculty_overload ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">System Backups</div><div class="text-xs text-slate-400 mt-0.5">Receive confirmation after each automatic backup</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="notif_system_backups" {{ (bool) \App\Models\SystemSetting::get('notif_system_backups', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ auth()->user()->notif_system_backups ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Login Activity</div><div class="text-xs text-slate-400 mt-0.5">Notify on new logins from unrecognized devices</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="notif_login_activity" {{ (bool) \App\Models\SystemSetting::get('notif_login_activity', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ auth()->user()->notif_login_activity ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          {{-- SECURITY --}}
          <div id="settings-security" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your admin account password</div></div></div>
              <div class="mb-3.5">
              <label class="field-label">Current Password</label>
              <div class="relative">
                <input class="field-input pr-10" type="password" id="pwd-current" placeholder="••••••••">
                <button type="button" onclick="togglePasswordVisibility('pwd-current', this)"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 bg-transparent border-none cursor-pointer">
                  <i class="ti ti-eye"></i>
                </button>
              </div>
            </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">New Password</label>
                <div class="relative">
                  <input class="field-input pr-10" type="password" id="pwd-new" placeholder="Min. 8 characters">
                  <button type="button" onclick="togglePasswordVisibility('pwd-new', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 bg-transparent border-none cursor-pointer">
                    <i class="ti ti-eye"></i>
                  </button>
                </div>
              </div>
              <div><label class="field-label">Confirm New Password</label>
                <div class="relative">
                  <input class="field-input pr-10" type="password" id="pwd-confirm" placeholder="Re-enter new password">
                  <button type="button" onclick="togglePasswordVisibility('pwd-confirm', this)"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 bg-transparent border-none cursor-pointer">
                    <i class="ti ti-eye"></i>
                  </button>
                </div>
              </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="updatePassword()">Update Password</button>
              </div>
            </div>
            <div class="card">
            <div class="card-header"><div><div class="card-title">Session & Access</div><div class="card-sub">Manage login security settings</div></div></div>
            <div class="grid grid-cols-2 gap-3 mb-1">
              <div>
                <label class="field-label">Session Timeout</label>
                <select class="field-input" id="sec-session-timeout">
                  <option value="15" {{ \App\Models\SystemSetting::get('usr_session_timeout_minutes', 30) == 15 ? 'selected' : '' }}>15 minutes</option>
                  <option value="30" {{ \App\Models\SystemSetting::get('usr_session_timeout_minutes', 30) == 30 ? 'selected' : '' }}>30 minutes</option>
                  <option value="60" {{ \App\Models\SystemSetting::get('usr_session_timeout_minutes', 30) == 60 ? 'selected' : '' }}>1 hour</option>
                  <option value="999999" {{ \App\Models\SystemSetting::get('usr_session_timeout_minutes', 30) == 999999 ? 'selected' : '' }}>Never</option>
                </select>
              </div>
              <div>
                <label class="field-label">Max Login Attempts</label>
                <select class="field-input" id="sec-max-attempts">
                  <option value="3" {{ \App\Models\SystemSetting::get('usr_max_login_attempts', 5) == 3 ? 'selected' : '' }}>3</option>
                  <option value="5" {{ \App\Models\SystemSetting::get('usr_max_login_attempts', 5) == 5 ? 'selected' : '' }}>5</option>
                  <option value="10" {{ \App\Models\SystemSetting::get('usr_max_login_attempts', 5) == 10 ? 'selected' : '' }}>10</option>
                </select>
              </div>
            </div>
            <div class="flex justify-end mt-1">
              <button class="btn btn-primary" onclick="saveSecuritySettings()">Save Changes</button>
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
                @php
                    $latestBackup = \App\Models\SystemBackup::orderByDesc('bkp_ran_at')->first();
                @endphp
                <div class="bg-slate-50 rounded-lg p-3.5">
                  <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Last Backup</div>
                  @if($latestBackup)
                    <div class="text-[13px] font-semibold {{ $latestBackup->bkp_status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                      {{ $latestBackup->bkp_ran_at->format('M j, Y, h:i A') }}
                      {{ $latestBackup->bkp_status === 'success' ? '✓' : '✗' }}
                    </div>
                  @else
                    <div class="text-[13px] font-semibold text-slate-400">No backups yet</div>
                  @endif
                </div>
                @php
                  $uptimeText = 'Unknown';
                  try {
                      $output = shell_exec('wmic process where "name=\'httpd.exe\'" get CreationDate /value');
                      preg_match('/CreationDate=(\d{14})/', $output, $matches);
                      if (isset($matches[1])) {
                          $startTime = \Carbon\Carbon::createFromFormat('YmdHis', substr($matches[1], 0, 14));
                          $uptimeText = $startTime->diffForHumans(now(), true) . ' (since ' . $startTime->format('M j, g:i A') . ')';
                      }
                  } catch (\Exception $e) {
                      $uptimeText = 'Unable to determine';
                  }
              @endphp
              <div class="bg-slate-50 rounded-lg p-3.5">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Server Uptime</div>
                <div class="text-[13px] font-semibold text-slate-900">{{ $uptimeText }}</div>
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

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
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
      if (sbName) sbName.textContent = data.usr_name;
      showToast('Personal info saved successfully!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

function saveInstitution() {
  const payload = {
    inst_name: document.getElementById('inst-name').value.trim(),
    inst_branch_campus: document.getElementById('inst-campus').value.trim(),
    inst_college: document.getElementById('inst-college').value.trim(),
    inst_abbreviation: document.getElementById('inst-abbr').value.trim(),
    inst_contact_email: document.getElementById('inst-email').value.trim(),
    inst_phone: document.getElementById('inst-phone').value.trim(),
  };

  fetch('{{ route("admin.institution.update") }}', {
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
        showToast('❌ Failed to save institution details');
        return;
      }
      showToast('Institution details saved!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

function saveAcademicYear() {
  const payload = {
    ay_academic_year: document.getElementById('ay-year').value.trim(),
    sem_name: document.getElementById('ay-semester').value,
    sem_start_date: document.getElementById('ay-start').value,
    sem_end_date: document.getElementById('ay-end').value,
  };

  fetch('{{ route("admin.academic-year.update") }}', {
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
        showToast('❌ Failed to save academic year settings');
        return;
      }
      showToast('Academic year settings saved!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

function saveNotificationPreferences() {
  const payload = {
    notif_new_user_registration: document.getElementById('notif-new-user').checked,
    notif_faculty_overload: document.getElementById('notif-faculty-overload').checked,
    notif_system_backups: document.getElementById('notif-system-backups').checked,
    notif_login_activity: document.getElementById('notif-login-activity').checked,
  };

  fetch('{{ route("admin.profile.notification-preferences.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        showToast('❌ Failed to save notification preferences');
        return;
      }
      showToast('Notification settings saved!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

// ── NOTIFICATION TOGGLE SWITCHES ────────────────────────────────────────────────
function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (input.checked) { track.classList.add('on'); }
  else { track.classList.remove('on'); }

  const field = input.dataset.field;
  const payload = { [field]: input.checked };

  fetch('{{ route("admin.profile.notification-preferences.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        showToast('❌ Failed to update preference');
        return;
      }
      showToast('Notification preference updated!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

function saveSecuritySettings() {
  const payload = {
    usr_session_timeout_minutes: parseInt(document.getElementById('sec-session-timeout').value),
    usr_max_login_attempts: parseInt(document.getElementById('sec-max-attempts').value),
  };

  fetch('{{ route("admin.profile.security-settings.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json())
    .then(data => {
      if (!data.success) {
        showToast('❌ Failed to save security settings');
        return;
      }
      showToast('Security settings saved!');
    })
    .catch(() => showToast('❌ Something went wrong saving.'));
}

function togglePasswordVisibility(inputId, btn) {
  const input = document.getElementById(inputId);
  const icon = btn.querySelector('i');
  if (input.type === 'password') {
    input.type = 'text';
    icon.classList.remove('ti-eye');
    icon.classList.add('ti-eye-off');
  } else {
    input.type = 'password';
    icon.classList.remove('ti-eye-off');
    icon.classList.add('ti-eye');
  }
}

// -------PASSWORD CHANGES -----------------------------------------
function updatePassword() {
  const current = document.getElementById('pwd-current').value;
  const newPwd = document.getElementById('pwd-new').value;
  const confirmPwd = document.getElementById('pwd-confirm').value;

  if (!current || !newPwd || !confirmPwd) {
    showToast('❌ Please fill in all password fields');
    return;
  }

  fetch('{{ route("admin.profile.password.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
    },
    body: JSON.stringify({
      current_password: current,
      new_password: newPwd,
      new_password_confirmation: confirmPwd,
    }),
  })
    .then(res => res.json().then(data => ({ status: res.status, body: data })))
    .then(({ status, body }) => {
      if (status === 200 && body.success) {
        showToast('Password updated successfully!');
        document.getElementById('pwd-current').value = '';
        document.getElementById('pwd-new').value = '';
        document.getElementById('pwd-confirm').value = '';
      } else if (status === 422 && body.errors) {
        const firstError = Object.values(body.errors)[0][0];
        showToast('❌ ' + firstError);
      } else {
        showToast('❌ Failed to update password');
      }
    })
    .catch(() => showToast('❌ Something went wrong.'));
}
</script>
</body>
</html>