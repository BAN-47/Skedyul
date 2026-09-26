<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Dean Settings</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 overflow-hidden h-screen">

<div class="app-shell">

  <div class="app-main">
    @include('partials.dean_header', ['title' => 'Dean Settings'])

    <div class="page-content">
      <div class="grid grid-cols-[220px_1fr] gap-6 items-start">

        {{-- Left nav --}}
        <div class="card py-3 px-0">
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">Settings</div>
          <div class="settings-nav-item active" onclick="showDeanSettingsSection('profile',this)">Personal Info</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('general',this)">General</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('academic',this)">Academic Year</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('notifications',this)">Notifications</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('security',this)">Security</div>
          <div class="settings-nav-item" onclick="showDeanSettingsSection('system',this)">System Info</div>
        </div>

        {{-- Right content --}}
        <div id="dean-settings-content">

          {{-- PERSONAL INFO --}}
          <div id="dsec-profile">
            <div class="card mb-4">
            <div class="card-header">
              <div><div class="card-title">Profile Picture</div><div class="card-sub">Upload a new profile photo</div></div>
            </div>
            <div class="flex items-center gap-6 py-2">
              <div class="relative shrink-0">
                <div id="avatar-display"
                    class="w-24 h-24 rounded-full bg-[--navy] flex items-center justify-center text-2xl font-extrabold text-white border-4 border-white shadow-sm overflow-hidden bg-cover bg-center"
                    @if($dean->dean_profile_image) style="background-image:url('{{ $dean->dean_profile_image }}')" @endif>
                  @unless($dean->dean_profile_image){{ strtoupper(substr($dean->dean_first_name, 0, 1)) }}@endunless
                </div>
                <div class="absolute bottom-0 right-0 w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer border-2 border-white text-sm font-bold text-white"
                    onclick="document.getElementById('avatar-input').click()">+</div>
                <input type="file" id="avatar-input" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
              </div>
              <div>
                <div class="text-[15px] font-bold text-slate-900">{{ $dean->dean_first_name }} {{ $dean->dean_middle_name }} {{ $dean->dean_last_name }}</div>
                <div class="text-xs text-slate-400 mt-0.5">Dean · CCICT</div>
                <div class="flex gap-2 mt-3">
                  <button class="btn btn-primary text-xs px-3.5 py-1.5" onclick="document.getElementById('avatar-input').click()">Upload Photo</button>
                  <button class="btn btn-secondary text-xs px-3.5 py-1.5" onclick="removeAvatar()">Remove</button>
                </div>
              </div>
            </div>
          </div>

            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Personal Information</div><div class="card-sub">Update your personal details</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">First Name</label><input class="field-input" id="pi-first" value="{{ $dean->dean_first_name }}"></div>
                <div><label class="field-label">Last Name</label><input class="field-input" id="pi-last" value="{{ $dean->dean_last_name }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Middle Name</label><input class="field-input" id="pi-middle" placeholder="Optional" value="{{ $dean->dean_middle_name }}"></div>
                <div>
                  <label class="field-label">Suffix</label>
                  <select class="field-input" id="pi-suffix">
                    <option value="" {{ !$dean->dean_suffix ? 'selected' : '' }}>None</option>
                    <option {{ $dean->dean_suffix === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                    <option {{ $dean->dean_suffix === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                    <option {{ $dean->dean_suffix === 'II' ? 'selected' : '' }}>II</option>
                    <option {{ $dean->dean_suffix === 'III' ? 'selected' : '' }}>III</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Employee ID</label><input class="field-input" id="pi-empid" value="{{ $dean->dean_employee_id }}"></div>
                <div>
                  <label class="field-label">Gender</label>
                  <select class="field-input" id="pi-gender">
                    <option {{ $dean->dean_gender === 'Male' ? 'selected' : '' }}>Male</option>
                    <option {{ $dean->dean_gender === 'Female' ? 'selected' : '' }}>Female</option>
                    <option {{ $dean->dean_gender === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">Civil Status</label>
                  <select class="field-input" id="pi-civil">
                    <option {{ $dean->dean_civil_status === 'Single' ? 'selected' : '' }}>Single</option>
                    <option {{ $dean->dean_civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                    <option {{ $dean->dean_civil_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                    <option {{ $dean->dean_civil_status === 'Separated' ? 'selected' : '' }}>Separated</option>
                  </select>
                </div>
                <div><label class="field-label">Nationality</label><input class="field-input" id="pi-nat" value="{{ $dean->dean_nationality }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Date of Birth</label><input class="field-input" type="date" id="pi-dob" value="{{ $dean->dean_dob?->format('Y-m-d') }}"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><div><div class="card-title">Contact & Office Details</div><div class="card-sub">How others can reach you</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Email Address</label><input class="field-input" type="email" id="pi-gmail" value="{{ $dean->dean_gmail }}"></div>
                <div><label class="field-label">Phone Number</label><input class="field-input" id="pi-phone" value="{{ $dean->dean_phone_number }}"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Office Location</label><input class="field-input" id="pi-address" value="{{ $dean->dean_office_address }}"></div>
                <div><label class="field-label">College / Unit</label><input class="field-input bg-slate-100 cursor-not-allowed" value="CCICT" readonly></div>
              </div>
              <div class="mb-4">
                <label class="field-label">Bio / About</label>
                <textarea class="field-input resize-y" id="pi-bio" rows="3">{{ $dean->dean_bio }}</textarea>
              </div>
              <div class="flex justify-end">
                <button class="btn btn-primary" onclick="saveContactInfo()">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- GENERAL --}}
          <div id="dsec-general" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Institution Details</div><div class="card-sub">Basic information about your school and college</div></div></div>
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
          </div>

          {{-- ACADEMIC YEAR --}}
          <div id="dsec-academic" style="display:none;">
            <div class="card mb-4">
              <div class="card-header">
                <div><div class="card-title">Current Academic Year</div><div class="card-sub">Active semester configuration</div></div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-600">Active</span>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Academic Year</label><input class="field-input" value="2025-2026"></div>
                <div>
                  <label class="field-label">Semester</label>
                  <select class="field-input"><option selected>1st Semester</option><option>2nd Semester</option><option>Summer</option></select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Start Date</label><input class="field-input" type="date" value="2025-08-11"></div>
                <div><label class="field-label">End Date</label><input class="field-input" type="date" value="2025-12-20"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Schedule Submission Deadline</label><input class="field-input" type="date" value="2025-07-25"></div>
                <div>
                  <label class="field-label">School Days</label>
                  <select class="field-input"><option selected>Monday - Saturday</option><option>Monday - Friday</option></select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Academic year settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          {{-- NOTIFICATIONS --}}
          <div id="dsec-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">Notification Preferences</div><div class="card-sub">Choose what alerts you receive as Dean</div></div></div>
              <div class="flex flex-col gap-3 mt-1">

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Schedule Submitted for Approval</div><div class="text-xs text-slate-400 mt-0.5">Notify when a Chair submits a department schedule for review</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_schedule_submitted" {{ (bool) \App\Models\SystemSetting::get('dean_notif_schedule_submitted', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_schedule_submitted', true) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Faculty Overload Alert</div><div class="text-xs text-slate-400 mt-0.5">Notify when any faculty member exceeds their maximum unit load</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_faculty_overload" {{ (bool) \App\Models\SystemSetting::get('dean_notif_faculty_overload', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_faculty_overload', true) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Submission Deadline Reminder</div><div class="text-xs text-slate-400 mt-0.5">Remind 3 days before the schedule submission deadline</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_deadline_reminder" {{ (bool) \App\Models\SystemSetting::get('dean_notif_deadline_reminder', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_deadline_reminder', true) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Weekly Workload Digest</div><div class="text-xs text-slate-400 mt-0.5">Receive a weekly email summary of faculty loads across all departments</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_weekly_digest" {{ (bool) \App\Models\SystemSetting::get('dean_notif_weekly_digest', false) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_weekly_digest', false) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Schedule Approval Reminders</div><div class="text-xs text-slate-400 mt-0.5">Remind when pending schedules have not been acted on for 48 hours</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_approval_reminders" {{ (bool) \App\Models\SystemSetting::get('dean_notif_approval_reminders', true) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_approval_reminders', true) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">Login Activity</div><div class="text-xs text-slate-400 mt-0.5">Notify on new logins from unrecognized devices or browsers</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_login_activity" {{ (bool) \App\Models\SystemSetting::get('dean_notif_login_activity', false) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_login_activity', false) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">System Backups</div><div class="text-xs text-slate-400 mt-0.5">Receive confirmation after each automatic system backup</div></div>
                  <label class="toggle-switch">
                    <input type="checkbox" data-field="dean_notif_system_backups" {{ (bool) \App\Models\SystemSetting::get('dean_notif_system_backups', false) ? 'checked' : '' }} onchange="toggleSwitch(this)">
                    <span class="toggle-track {{ (bool) \App\Models\SystemSetting::get('dean_notif_system_backups', false) ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                  </label>
                </div>

              </div>
            </div>
          </div>

          {{-- SECURITY --}}
          <div id="dsec-security" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">Change Password</div><div class="card-sub">Update your Dean portal account password</div></div></div>
              <div class="mb-3.5">
                <label class="field-label">Current Password</label>
                <input class="field-input" type="password" placeholder="Enter your current password">
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">New Password</label>
                  <input class="field-input" type="password" placeholder="Min. 8 characters" id="pw-new" oninput="checkPwStrength(this.value)">
                  <div class="h-1 rounded bg-slate-200 mt-1.5 overflow-hidden">
                    <div class="h-full rounded transition-all" id="pw-bar" style="width:0"></div>
                  </div>
                </div>
                <div><label class="field-label">Confirm New Password</label><input class="field-input" type="password" placeholder="Re-enter new password"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><div><div class="card-title">Session & Access</div><div class="card-sub">Manage login security and session behaviour</div></div></div>
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
          <div id="dsec-system" style="display:none;">
            <div class="card mb-4">
              <div class="card-header">
                <div><div class="card-title">System Information</div><div class="card-sub">Current environment and infrastructure details</div></div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-600">All Systems Normal</span>
              </div>
              <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Application</div><div class="text-[13px] font-semibold text-slate-900">SKEDYUL v1.0.0</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Host / Deploy</div><div class="text-[13px] font-semibold text-slate-900">Vercel (Production)</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Database</div><div class="text-[13px] font-semibold text-slate-900">Supabase PostgreSQL</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Authentication</div><div class="text-[13px] font-semibold text-slate-900">JWT + Laravel Sanctum</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Frontend Stack</div><div class="text-[13px] font-semibold text-slate-900">Tailwind CSS + Vanilla JS</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Mobile App</div><div class="text-[13px] font-semibold text-slate-900">React Native</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Last Backup</div><div class="text-[13px] font-semibold text-green-600">Today, 06:00 AM</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Uptime (30 days)</div><div class="text-[13px] font-semibold text-green-600">99.98%</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Environment</div><div class="text-[13px] font-semibold text-slate-900">Production</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">API Version</div><div class="text-[13px] font-semibold text-slate-900">v2.3.1</div></div>
              </div>
            </div>
          </div>

        </div>{{-- /dean-settings-content --}}
      </div>
    </div>
  </div>
</div>

{{-- MODAL: EXPORT --}}
<div class="fixed inset-0 bg-black/40 items-center justify-center p-5 z-[1000] hidden [&.open]:flex" id="modal-export">
  <div class="bg-white rounded-2xl w-[440px] max-w-full max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 sticky top-0 bg-white">
      <div class="text-base font-bold text-slate-900">Export Report</div>
      <button class="bg-transparent border-none text-lg text-slate-400 cursor-pointer p-1" onclick="closeModal('modal-export')">✕</button>
    </div>
    <div class="p-6">
      <div class="mb-3.5"><label class="field-label">Report Type</label>
        <select class="field-input"><option>Master Schedule</option><option>Faculty Workload Report</option><option>Faculty Deployment Report</option><option>Department Summary</option></select>
      </div>
      <div class="mb-3.5"><label class="field-label">Department</label>
        <select class="field-input"><option>All Departments</option><option>BSIS</option><option>BSIT</option><option>BIT-CT</option></select>
      </div>
      <div><label class="field-label">Format</label>
        <select class="field-input"><option>PDF</option><option>Excel (.xlsx)</option><option>Word (.docx)</option></select>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-2.5">
      <button class="btn btn-secondary" onclick="closeModal('modal-export')">Cancel</button>
      <button class="btn btn-primary" onclick="closeModal('modal-export');showToast('Report exported!')">Download</button>
    </div>
  </div>
</div>

{{-- MODAL: NOTIFY CHAIRS --}}
<div class="fixed inset-0 bg-black/40 items-center justify-center p-5 z-[1000] hidden [&.open]:flex" id="modal-notify">
  <div class="bg-white rounded-2xl w-[520px] max-w-full max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 sticky top-0 bg-white">
      <div class="text-base font-bold text-slate-900">Send Notification to Chairs</div>
      <button class="bg-transparent border-none text-lg text-slate-400 cursor-pointer p-1" onclick="closeModal('modal-notify')">✕</button>
    </div>
    <div class="p-6">

      <div class="mb-4">
        <div class="field-label mb-2">Recipients</div>
        <div class="flex flex-col gap-2">
          <label class="flex items-center gap-2.5 px-3.5 py-2.5 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
            <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)" class="w-[15px] h-[15px] accent-blue-600">
            <span class="text-[13px] font-bold text-slate-900">All Department Chairs</span>
          </label>
          <div class="flex flex-col gap-1.5 pl-3">
            <label class="flex items-center gap-2.5 px-3.5 py-2 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
              <input type="checkbox" class="chair-check w-3.5 h-3.5 accent-blue-600" checked onchange="syncAllChairs()">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-amber-600 flex items-center justify-center text-[11px] font-bold text-white shrink-0">RT</div>
                <div><div class="text-[13px] font-semibold text-slate-900">Rodrigo Tan</div><div class="text-[11px] text-slate-400">Chair · BSIS</div></div>
              </div>
            </label>
            <label class="flex items-center gap-2.5 px-3.5 py-2 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
              <input type="checkbox" class="chair-check w-3.5 h-3.5 accent-blue-600" checked onchange="syncAllChairs()">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-violet-600 flex items-center justify-center text-[11px] font-bold text-white shrink-0">MC</div>
                <div><div class="text-[13px] font-semibold text-slate-900">Maria Cruz</div><div class="text-[11px] text-slate-400">Chair · BSIT</div></div>
              </div>
            </label>
            <label class="flex items-center gap-2.5 px-3.5 py-2 bg-slate-50 rounded-lg cursor-pointer border border-slate-200">
              <input type="checkbox" class="chair-check w-3.5 h-3.5 accent-blue-600" checked onchange="syncAllChairs()">
              <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-cyan-600 flex items-center justify-center text-[11px] font-bold text-white shrink-0">JL</div>
                <div><div class="text-[13px] font-semibold text-slate-900">Jose Lim</div><div class="text-[11px] text-slate-400">Chair · BIT-CT</div></div>
              </div>
            </label>
          </div>
        </div>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Notification Type</label>
        <select class="field-input" id="notif-type">
          <option value="info">General Info</option>
          <option value="reminder">Reminder</option>
          <option value="urgent">Urgent</option>
          <option value="deadline">Deadline Notice</option>
        </select>
      </div>

      <div class="mb-3.5">
        <label class="field-label">Title</label>
        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
      </div>

      <div class="mb-3.5">
        <label class="field-label">Message</label>
        <textarea class="field-input resize-y" id="notif-message" rows="4" placeholder="Write your message to the chairs..."></textarea>
      </div>

      <div>
        <div class="text-xs font-bold text-slate-400 uppercase tracking-wide mb-2">Recently Sent</div>
        <div id="notif-history" class="flex flex-col gap-1.5">
          <div class="text-xs text-slate-400 p-2 text-center">No notifications sent yet this session.</div>
        </div>
      </div>

    </div>
    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-2.5">
      <button class="btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
      <button class="btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
    </div>
  </div>
</div>

{{-- MODAL: SCHEDULE REVIEW --}}
<div class="fixed inset-0 bg-black/40 items-center justify-center p-5 z-[1000] hidden [&.open]:flex" id="modal-review">
  <div class="bg-white rounded-2xl w-[560px] max-w-full max-h-[90vh] overflow-y-auto shadow-2xl">
    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-200 sticky top-0 bg-white">
      <div class="text-base font-bold text-slate-900">Schedule Review — BSIT</div>
      <button class="bg-transparent border-none text-lg text-slate-400 cursor-pointer p-1" onclick="closeModal('modal-review')">✕</button>
    </div>
    <div class="p-6">
      <div class="bg-red-50 border border-red-200 border-l-4 border-l-red-600 rounded-lg px-3.5 py-3 mb-4 text-[13px] text-red-800">
        <strong>1 Conflict Detected:</strong> Carlo Mendoza is scheduled for CC 311 and IT 201 at the same time on Monday 8:30–10:00 AM.
      </div>
      <div class="overflow-x-auto">
        <table class="w-full border-collapse">
          <thead>
            <tr>
              <th class="text-[11px] font-bold text-slate-400 uppercase tracking-wide px-3.5 py-2.5 text-left border-b-2 border-slate-200">Faculty</th>
              <th class="text-[11px] font-bold text-slate-400 uppercase tracking-wide px-3.5 py-2.5 text-left border-b-2 border-slate-200">Subject</th>
              <th class="text-[11px] font-bold text-slate-400 uppercase tracking-wide px-3.5 py-2.5 text-left border-b-2 border-slate-200">Day & Time</th>
              <th class="text-[11px] font-bold text-slate-400 uppercase tracking-wide px-3.5 py-2.5 text-left border-b-2 border-slate-200">Room</th>
              <th class="text-[11px] font-bold text-slate-400 uppercase tracking-wide px-3.5 py-2.5 text-left border-b-2 border-slate-200">Issue</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200"><b>Carlo Mendoza</b></td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">IT 201</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">Mon 8:30–10:00</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">Lab 1</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-600">Conflict</span></td>
            </tr>
            <tr>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200"><b>Carlo Mendoza</b></td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">CC 311</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">Mon 8:30–10:00</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200">Room 205</td>
              <td class="text-[13px] px-3.5 py-3 border-b border-slate-200"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-600">Conflict</span></td>
            </tr>
            <tr>
              <td class="text-[13px] px-3.5 py-3"><b>Ana Reyes</b></td>
              <td class="text-[13px] px-3.5 py-3">IT 401</td>
              <td class="text-[13px] px-3.5 py-3">Tue 7:00–8:30</td>
              <td class="text-[13px] px-3.5 py-3">Room 206</td>
              <td class="text-[13px] px-3.5 py-3"><span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-600">OK</span></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-4">
        <label class="field-label">Return Note</label>
        <textarea class="field-input resize-y" rows="3">Please resolve the scheduling conflict for Carlo Mendoza before resubmitting.</textarea>
      </div>
    </div>
    <div class="px-6 py-4 border-t border-slate-200 flex justify-end gap-2.5">
      <button class="btn btn-secondary" onclick="closeModal('modal-review')">Close</button>
      <button class="bg-red-100 text-red-600 px-4 py-2 rounded-lg text-[13px] font-semibold hover:bg-red-600 hover:text-white transition-colors" onclick="closeModal('modal-review');showToast('Schedule returned to Chair Cruz.')">Return to Chair</button>
    </div>
  </div>
</div>

<div class="toast" id="toast"><span id="toast-msg"></span></div>

<script>
function goToPage(id) {
  document.querySelectorAll('.page').forEach(p => { p.classList.remove('active'); p.style.display = 'none'; });
  const page = document.getElementById(id);
  if (!page) return;
  page.style.display = 'block'; page.classList.add('active');
  const titles = {'page-dashboard':'Dean Dashboard','page-faculty':'Faculty Workload Overview','page-departments':'Department Overview','page-approvals':'Schedule Approvals','page-overload':'Overload Alerts','page-reports':'Schedule Reports','page-deployment':'Faculty Deployment Report','page-settings':'Settings'};
  const title = document.getElementById('topbar-title');
  if (title) title.textContent = titles[id] || 'SKEDYUL';
}

function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.fixed.inset-0.bg-black\\/40').forEach(m => {
    m.addEventListener('click', e => { if (e.target === m) m.classList.remove('open'); });
  });
});

function showDeanSettingsSection(section, el) {
  ['profile','general','academic','notifications','security','system'].forEach(s => {
    const p = document.getElementById('dsec-' + s); if (p) p.style.display = 'none';
  });
  const t = document.getElementById('dsec-' + section); if (t) t.style.display = 'block';
  document.querySelectorAll('#dean-settings-content ~ .settings-nav-item, .settings-nav-item').forEach(i => i.classList.remove('active'));
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

  const typeColors = { info: 'bg-blue-50 text-blue-600', reminder: 'bg-amber-50 text-amber-600', urgent: 'bg-red-50 text-red-600', deadline: 'bg-violet-50 text-violet-600' };
  const typeLabels = { info: 'Info', reminder: 'Reminder', urgent: 'Urgent', deadline: 'Deadline' };
  const now = new Date();
  const timeStr = now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });

  const historyEl = document.getElementById('notif-history');
  const empty = historyEl.querySelector('.text-center');
  if (empty) empty.remove();

  const item = document.createElement('div');
  item.className = 'bg-slate-50 rounded-lg px-3 py-2.5 border border-slate-200';
  item.innerHTML = `
    <div class="flex items-center justify-between mb-1">
      <div class="flex items-center gap-1.5">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold ${typeColors[type]}">${typeLabels[type]}</span>
        <span class="text-[13px] font-bold text-slate-900">${title}</span>
      </div>
      <span class="text-[11px] text-slate-400">Sent ${timeStr}</span>
    </div>
    <div class="text-xs text-slate-600 mb-1">${message}</div>
    <div class="text-[11px] text-slate-400">To: ${selected.join(', ')}</div>`;

  historyEl.prepend(item);

  document.getElementById('notif-title').value = '';
  document.getElementById('notif-message').value = '';
  document.getElementById('notif-type').value = 'info';

  showToast('Notification sent to ' + selected.length + ' chair' + (selected.length > 1 ? 's' : '') + '!');
}

function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (input.checked) { track.classList.add('on'); }
  else { track.classList.remove('on'); }
  showToast('Preference updated!');
}

function checkPwStrength(val) {
  const bar = document.getElementById('pw-bar'); if (!bar) return;
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
  t.classList.add('show'); setTimeout(() => t.classList.remove('show'), 3000);
}

function uploadAvatar(input) {
  const file = input.files[0];
  if (!file) return;

  const formData = new FormData();
  formData.append('avatar', file);

  fetch("{{ route('dean.profile.avatar.update') }}", {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: formData,
  })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
      console.log('Avatar upload response:', status, data);
      if (data.success) {
        const display = document.getElementById('avatar-display');
        display.style.backgroundImage = `url('${data.url}')`;
        display.textContent = '';
        showToast('Profile photo updated!');
      } else {
        showToast(data.message || 'Upload failed. Please try again.');
      }
    })
    .catch(err => {
      console.error('Avatar upload error:', err);
      showToast('Upload failed. Please try again.');
    });
}

function removeAvatar() {
  fetch("{{ route('dean.profile.avatar.remove') }}", {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
  })
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        const display = document.getElementById('avatar-display');
        display.style.backgroundImage = '';
        display.textContent = '{{ strtoupper(substr($dean->dean_first_name, 0, 1)) }}';
        showToast('Profile photo removed.');
      } else {
        showToast('Remove failed. Please try again.');
      }
    })
    .catch(() => showToast('Remove failed. Please try again.'));
}

function savePersonalInfo() {
  const payload = {
    dean_first_name: document.getElementById('pi-first').value,
    dean_last_name: document.getElementById('pi-last').value,
    dean_middle_name: document.getElementById('pi-middle').value,
    dean_suffix: document.getElementById('pi-suffix').value,
    dean_employee_id: document.getElementById('pi-empid').value,
    dean_gender: document.getElementById('pi-gender').value,
    dean_civil_status: document.getElementById('pi-civil').value,
    dean_dob: document.getElementById('pi-dob').value || null,
    dean_nationality: document.getElementById('pi-nat').value,
  };

  fetch("{{ route('dean.profile.personal-info.update') }}", {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
      if (data.success) {
        showToast('Personal information saved!');
      } else {
        const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Save failed. Please check your inputs.';
        showToast(firstError);
      }
    })
    .catch(() => showToast('Save failed. Please try again.'));
}

function saveContactInfo() {
  const payload = {
    dean_gmail: document.getElementById('pi-gmail').value,
    dean_phone_number: document.getElementById('pi-phone').value,
    dean_office_address: document.getElementById('pi-address').value,
    dean_bio: document.getElementById('pi-bio').value,
  };

  fetch("{{ route('dean.profile.contact.update') }}", {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      'Accept': 'application/json',
    },
    body: JSON.stringify(payload),
  })
    .then(res => res.json().then(data => ({ status: res.status, data })))
    .then(({ status, data }) => {
      if (data.success) {
        showToast('Contact details saved successfully!');
      } else {
        const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Save failed. Please check your inputs.';
        showToast(firstError);
      }
    })
    .catch(() => showToast('Save failed. Please try again.'));
}
</script>
</body>
</html>