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

@php
  $authUser = auth()->user();
  $activeSemester = $activeSemester ?? null;
  $activeYear = $activeYear ?? null;
  $academicYears = $academicYears ?? collect();
  $ayStart = $ayStart ?? '2026-08-01';
  $ayEnd = $ayEnd ?? '2026-12-20';
  $periodLabel = $periodLabel ?? null;
  $periodNotice = $periodNotice ?? null;
  $institution = \App\Models\Institution::query()->first();
@endphp

<div class="app-shell">
  @include('partials.admin_sidebar')

  <div class="app-main">
    @include('partials.admin_header', ['title' => 'Technical Admin settings'])

    <div class="page-content">
      <div class="grid grid-cols-[220px_1fr] gap-6 items-start">

        {{-- Left nav --}}
        <div class="card py-3 px-0">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">Settings</div>
          <div class="settings-nav-item active" onclick="showSettingsSection('general', this)">General</div>
          <div class="settings-nav-item" onclick="showSettingsSection('academic', this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showSettingsSection('notifications', this)">Notifications</div>
          <div class="settings-nav-item" onclick="showSettingsSection('security', this)">Security</div>
          <div class="settings-nav-item" onclick="showSettingsSection('system', this)">System Info</div>
        </div>

        {{-- Right content --}}
        <div>
          {{-- GENERAL --}}
          <div id="settings-general">
            <div class="card mb-4">
              <div class="card-header">
                <div>
                  <div class="card-title">Personal Information</div>
                  <div class="card-sub">Update your name, rank, and contact details</div>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">First Name</label>
                  <input class="field-input" id="pi-firstname" value="{{ $authUser->usr_first_name ?? '' }}">
                </div>
                <div>
                  <label class="field-label">Last Name</label>
                  <input class="field-input" id="pi-lastname" value="{{ $authUser->usr_last_name ?? '' }}">
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">Middle Name</label>
                  <input class="field-input" id="pi-middlename" placeholder="Optional" value="{{ $authUser->usr_middle_name ?? '' }}">
                </div>
                <div>
                  <label class="field-label">Suffix</label>
                  <select class="field-input" id="pi-suffix">
                    <option value="" @selected(empty($authUser->usr_suffix ?? null))>None</option>
                    <option value="Jr." @selected(($authUser->usr_suffix ?? '') === 'Jr.')>Jr.</option>
                    <option value="Sr." @selected(($authUser->usr_suffix ?? '') === 'Sr.')>Sr.</option>
                    <option value="II" @selected(($authUser->usr_suffix ?? '') === 'II')>II</option>
                    <option value="III" @selected(($authUser->usr_suffix ?? '') === 'III')>III</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button type="button" class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header">
                <div>
                  <div class="card-title">Institution Details</div>
                  <div class="card-sub">Basic information about your school</div>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">Institution Name</label>
                  <input class="field-input" id="inst-name" value="{{ optional($institution)->inst_name ?? 'Cebu Technological University' }}">
                </div>
                <div>
                  <label class="field-label">Branch / Campus</label>
                  <input class="field-input" id="inst-campus" value="{{ optional($institution)->inst_branch_campus ?? 'Main Campus' }}">
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">College / Unit</label>
                  <input class="field-input" id="inst-college" value="{{ optional($institution)->inst_college ?? 'College of Computing, Information and Communications Technology' }}">
                </div>
                <div>
                  <label class="field-label">Abbreviation</label>
                  <input class="field-input" id="inst-abbr" value="{{ optional($institution)->inst_abbreviation ?? 'CCICT' }}">
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">Contact Email</label>
                  <input class="field-input" id="inst-email" type="email" value="{{ optional($institution)->inst_contact_email ?? 'ccict@ctu.edu.ph' }}">
                </div>
                <div>
                  <label class="field-label">Phone</label>
                  <input class="field-input" id="inst-phone" value="{{ optional($institution)->inst_phone ?? '(032) 401-7777' }}">
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button type="button" class="btn btn-primary" onclick="saveInstitution()">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- ACADEMIC YEAR --}}
          <div id="settings-academic" style="display:none;">
            <div class="card mb-4">
              <div class="card-header">
                <div>
                  <div class="card-title">Current Academic Year</div>
                  <div class="card-sub">Controls the fixed period on PBS, PBT, and Faculty Load for all roles</div>
                </div>
                @if(!empty($activeSemester) && !empty($activeYear))
                  <span class="badge badge-green">{{ $periodLabel }}</span>
                @else
                  <span class="badge" style="background:#fef3c7;color:#92400e;">Not set</span>
                @endif
              </div>

              @if(!empty($periodNotice))
                <div class="mb-3 p-3 rounded-lg text-[12px]" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1e40af;">
                  {{ $periodNotice }}
                </div>
              @endif

              @if(empty($activeSemester) || empty($activeYear))
                <div class="mb-3 p-3 rounded-lg text-[12px]" style="background:#fff7ed;border:1px solid #fed7aa;color:#9a3412;">
                  No active period yet. Type a year, choose 1st or 2nd semester, set dates, then Save.
                </div>
              @endif

              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">Academic Year</label>
                  <input class="field-input" id="ay-year" list="ay-year-list"
                         value="{{ optional($activeYear)->ay_academic_year ?? '' }}"
                         placeholder="2027-2028">
                  <datalist id="ay-year-list">
                    @foreach($academicYears as $ay)
                      <option value="{{ $ay->ay_academic_year }}"></option>
                    @endforeach
                  </datalist>
                  <p class="text-[11px] text-slate-400 mt-1">Type a new year (e.g. 2027-2028) to register it.</p>
                </div>
                <div>
                  <label class="field-label">Semester</label>
                  <select class="field-input" id="ay-semester">
                    <option value="1st Semester" @selected((optional($activeSemester)->sem_name ?? '') === '1st Semester')>1st Semester</option>
                    <option value="2nd Semester" @selected((optional($activeSemester)->sem_name ?? '') === '2nd Semester')>2nd Semester</option>
                  </select>
                  <p class="text-[11px] text-slate-400 mt-1">Only two semesters exist system-wide.</p>
                </div>
              </div>

              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">Start Date</label>
                  <input class="field-input" id="ay-start" type="date" value="{{ $ayStart }}">
                </div>
                <div>
                  <label class="field-label">End Date</label>
                  <input class="field-input" id="ay-end" type="date" value="{{ $ayEnd }}">
                </div>
              </div>
              <p class="text-[11px] text-slate-400 mt-1 mb-2">
                When 1st semester end date passes, the system switches to 2nd semester.
                When 2nd ends, you will be prompted to set the next academic year.
              </p>

              <div class="flex justify-end mt-1">
                <button type="button" class="btn btn-primary" onclick="saveAcademicYear()">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- NOTIFICATIONS --}}
          <div id="settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">Notification Preferences</div>
                  <div class="card-sub">Choose what alerts you receive</div>
                </div>
              </div>
              <div class="flex flex-col gap-4 mt-1">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">New User Registration</div>
                    <div class="text-xs text-slate-400 mt-0.5">Alert when a new account is created</div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="notif-new-user" data-field="notif_new_user_registration"
                           {{ (bool) \App\Models\SystemSetting::get('notif_new_user_registration', true) ? 'checked' : '' }}
                           onchange="toggleSwitch(this)">
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">Faculty Overload</div>
                    <div class="text-xs text-slate-400 mt-0.5">Notify when faculty exceeds max load</div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="notif-faculty-overload" data-field="notif_faculty_overload"
                           {{ (bool) \App\Models\SystemSetting::get('notif_faculty_overload', true) ? 'checked' : '' }}
                           onchange="toggleSwitch(this)">
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">System Backups</div>
                    <div class="text-xs text-slate-400 mt-0.5">Confirmation after automatic backups</div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="notif-system-backups" data-field="notif_system_backups"
                           {{ (bool) \App\Models\SystemSetting::get('notif_system_backups', true) ? 'checked' : '' }}
                           onchange="toggleSwitch(this)">
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div>
                    <div class="text-[13px] font-semibold text-slate-900">Login Activity</div>
                    <div class="text-xs text-slate-400 mt-0.5">Notify on new unrecognized logins</div>
                  </div>
                  <label class="toggle-switch">
                    <input type="checkbox" id="notif-login-activity" data-field="notif_login_activity"
                           {{ (bool) \App\Models\SystemSetting::get('notif_login_activity', true) ? 'checked' : '' }}
                           onchange="toggleSwitch(this)">
                    <span class="toggle-track"><span class="toggle-thumb"></span></span>
                  </label>
                </div>
              </div>
            </div>
          </div>

          {{-- SECURITY --}}
          <div id="settings-security" style="display:none;">
            <div class="card mb-4">
              <div class="card-header">
                <div>
                  <div class="card-title">Change Password</div>
                  <div class="card-sub">Update your admin account password</div>
                </div>
              </div>
              <div class="mb-3">
                <label class="field-label">Current Password</label>
                <input class="field-input" type="password" id="pwd-current" placeholder="Current password">
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">New Password</label>
                  <input class="field-input" type="password" id="pwd-new" placeholder="Min. 8 characters">
                </div>
                <div>
                  <label class="field-label">Confirm Password</label>
                  <input class="field-input" type="password" id="pwd-confirm" placeholder="Repeat new password">
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button type="button" class="btn btn-primary" onclick="savePassword()">Update Password</button>
              </div>
            </div>
          </div>

          {{-- SYSTEM --}}
          <div id="settings-system" style="display:none;">
            <div class="card">
              <div class="card-header">
                <div>
                  <div class="card-title">System Info</div>
                  <div class="card-sub">Application environment</div>
                </div>
              </div>
              <div class="text-[13px] text-slate-600 space-y-2">
                <div><strong>App:</strong> SKEDYUL</div>
                <div><strong>Laravel:</strong> {{ app()->version() }}</div>
                <div><strong>PHP:</strong> {{ PHP_VERSION }}</div>
                <div><strong>Environment:</strong> {{ config('app.env') }}</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function showSettingsSection(name, el) {
  ['general', 'academic', 'notifications', 'security', 'system'].forEach(function (s) {
    var node = document.getElementById('settings-' + s);
    if (node) node.style.display = (s === name) ? '' : 'none';
  });
  document.querySelectorAll('.settings-nav-item').forEach(function (item) {
    item.classList.remove('active');
  });
  if (el) el.classList.add('active');
}

function showToast(msg) {
  if (typeof window.showToast === 'function' && window.showToast !== showToast) {
    return window.showToast(msg);
  }
  alert(msg);
}

function csrfToken() {
  var m = document.querySelector('meta[name="csrf-token"]');
  return m ? m.content : '';
}

function savePersonalInfo() {
  var payload = {
    usr_first_name: document.getElementById('pi-firstname').value.trim(),
    usr_last_name: document.getElementById('pi-lastname').value.trim(),
    usr_middle_name: document.getElementById('pi-middlename').value.trim(),
    usr_suffix: document.getElementById('pi-suffix').value,
  };
  fetch('{{ route("admin.profile.personal-info.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    credentials: 'same-origin',
    body: JSON.stringify(payload),
  })
    .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })
    .then(function (r) {
      if (!r.res.ok || !r.data.success) {
        showToast(r.data.message || 'Failed to save personal info');
        return;
      }
      showToast('Personal info saved!');
    })
    .catch(function () { showToast('Network error saving personal info'); });
}

function saveInstitution() {
  var payload = {
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
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    credentials: 'same-origin',
    body: JSON.stringify(payload),
  })
    .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })
    .then(function (r) {
      if (!r.res.ok || !r.data.success) {
        showToast(r.data.message || 'Failed to save institution');
        return;
      }
      showToast('Institution details saved!');
    })
    .catch(function () { showToast('Network error saving institution'); });
}

function saveAcademicYear() {
  var payload = {
    ay_academic_year: (document.getElementById('ay-year').value || '').trim(),
    sem_name: (document.getElementById('ay-semester').value || '').trim(),
    sem_start_date: document.getElementById('ay-start').value || '',
    sem_end_date: document.getElementById('ay-end').value || '',
  };
  if (!payload.ay_academic_year || !payload.sem_name || !payload.sem_start_date || !payload.sem_end_date) {
    showToast('Please fill academic year, semester, start date, and end date.');
    return;
  }
  fetch('{{ route("admin.academic-year.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    credentials: 'same-origin',
    body: JSON.stringify(payload),
  })
    .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })
    .then(function (r) {
      if (r.res.status === 401 || r.res.status === 419) {
        showToast('Session expired. Please log in again.');
        return;
      }
      if (!r.res.ok || !r.data.success) {
        showToast(r.data.message || 'Failed to save academic year');
        return;
      }
      showToast(r.data.message || 'Academic year saved!');
      setTimeout(function () { window.location.reload(); }, 700);
    })
    .catch(function () { showToast('Network error saving academic year'); });
}

function savePassword() {
  var payload = {
    current_password: document.getElementById('pwd-current').value,
    password: document.getElementById('pwd-new').value,
    password_confirmation: document.getElementById('pwd-confirm').value,
  };
  fetch('{{ route("admin.profile.password.update") }}', {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
    },
    credentials: 'same-origin',
    body: JSON.stringify(payload),
  })
    .then(function (res) { return res.json().then(function (data) { return { res: res, data: data }; }); })
    .then(function (r) {
      if (!r.res.ok || !r.data.success) {
        showToast(r.data.message || 'Failed to update password');
        return;
      }
      showToast('Password updated!');
      document.getElementById('pwd-current').value = '';
      document.getElementById('pwd-new').value = '';
      document.getElementById('pwd-confirm').value = '';
    })
    .catch(function () { showToast('Network error updating password'); });
}

function toggleSwitch(el) {
  // Optional: wire to notification preferences API if needed
}
</script>
</body>
</html>
