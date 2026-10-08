<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>SKEDYUL — Department Chair Settings</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 overflow-hidden h-screen">

  <div class="app-shell">

    @include('partials.chair_sidebar')

    <div class="app-main">
      @include('partials.chair_header', ['title' => 'Department Chair Settings'])

      <div class="page-content">
        <div class="grid grid-cols-[220px_1fr] gap-6 items-start">

          {{-- Left nav --}}
          <div class="card py-3 px-0">
            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">Settings</div>
            <div class="settings-nav-item active" onclick="showChairSettingsSection('profile',this)">Personal Info</div>
            <div class="settings-nav-item" onclick="showChairSettingsSection('general',this)">General</div>
            <div class="settings-nav-item" onclick="showChairSettingsSection('academic',this)">Academic Year</div>
            <div class="settings-nav-item" onclick="showChairSettingsSection('notifications',this)">Notifications</div>
            <div class="settings-nav-item" onclick="showChairSettingsSection('security',this)">Security</div>
            <div class="settings-nav-item" onclick="showChairSettingsSection('system',this)">System Info</div>
          </div>

          {{-- Right content --}}
          <div id="chair-settings-content">

            {{-- PERSONAL INFO --}}
            <div id="csec-profile">
              <div class="card mb-4">
                <div class="card-header">
                  <div>
                    <div class="card-title">Profile Picture</div>
                    <div class="card-sub">Upload a new profile photo</div>
                  </div>
                </div>
                <div class="flex items-center gap-6 py-2">
                  <div class="relative shrink-0">
                    <div id="avatar-display"
                      class="w-24 h-24 rounded-full bg-[--navy] flex items-center justify-center text-2xl font-extrabold text-white border-4 border-white shadow-sm overflow-hidden bg-cover bg-center"
                      @if($chair->dc_profile_image) style="background-image:url('{{ asset('images/chair_profile/' . $chair->dc_profile_image) }}')" @endif>
                      @unless($chair->dc_profile_image){{ strtoupper(substr($chair->dc_first_name, 0, 1)) }}@endunless
                    </div>
                    <div class="absolute bottom-0 right-0 w-7 h-7 bg-blue-600 rounded-full flex items-center justify-center cursor-pointer border-2 border-white text-sm font-bold text-white"
                      onclick="document.getElementById('avatar-input').click()">+</div>
                    <input type="file" id="avatar-input" accept="image/*" class="hidden" onchange="uploadAvatar(this)">
                  </div>
                  <div>
                    <div class="text-[15px] font-bold text-slate-900">{{ $chair->dc_first_name }} {{ $chair->dc_middle_name }} {{ $chair->dc_last_name }}</div>
                    <div class="text-xs text-slate-400 mt-0.5">Department Chair · {{ $departmentName }}</div>
                    <div class="flex gap-2 mt-3">
                      <button class="btn btn-primary text-xs px-3.5 py-1.5" onclick="document.getElementById('avatar-input').click()">Upload Photo</button>
                      <button class="btn btn-secondary text-xs px-3.5 py-1.5" onclick="removeAvatar()">Remove</button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card mb-4">
                <div class="card-header">
                  <div>
                    <div class="card-title">Personal Information</div>
                    <div class="card-sub">Update your personal details</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">First Name</label><input class="field-input" id="pi-first" value="{{ $chair->dc_first_name }}"></div>
                  <div><label class="field-label">Last Name</label><input class="field-input" id="pi-last" value="{{ $chair->dc_last_name }}"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Middle Name</label><input class="field-input" id="pi-middle" placeholder="Optional" value="{{ $chair->dc_middle_name }}"></div>
                  <div>
                    <label class="field-label">Suffix</label>
                    <select class="field-input" id="pi-suffix">
                      <option value="" {{ !$chair->dc_suffix ? 'selected' : '' }}>None</option>
                      <option {{ $chair->dc_suffix === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                      <option {{ $chair->dc_suffix === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                      <option {{ $chair->dc_suffix === 'II' ? 'selected' : '' }}>II</option>
                      <option {{ $chair->dc_suffix === 'III' ? 'selected' : '' }}>III</option>
                    </select>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Employee ID</label><input class="field-input" id="pi-empid" value="{{ $chair->dc_employee_id }}"></div>
                  <div>
                    <label class="field-label">Gender</label>
                    <select class="field-input" id="pi-gender">
                      <option {{ $chair->dc_gender === 'Male' ? 'selected' : '' }}>Male</option>
                      <option {{ $chair->dc_gender === 'Female' ? 'selected' : '' }}>Female</option>
                      <option {{ $chair->dc_gender === 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                    </select>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div>
                    <label class="field-label">Civil Status</label>
                    <select class="field-input" id="pi-civil">
                      <option {{ $chair->dc_civil_status === 'Single' ? 'selected' : '' }}>Single</option>
                      <option {{ $chair->dc_civil_status === 'Married' ? 'selected' : '' }}>Married</option>
                      <option {{ $chair->dc_civil_status === 'Widowed' ? 'selected' : '' }}>Widowed</option>
                      <option {{ $chair->dc_civil_status === 'Separated' ? 'selected' : '' }}>Separated</option>
                    </select>
                  </div>
                  <div><label class="field-label">Nationality</label><input class="field-input" id="pi-nat" value="{{ $chair->dc_nationality }}"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-1">
                  <div><label class="field-label">Date of Birth</label><input class="field-input" type="date" id="pi-dob" value="{{ $chair->dc_dob?->format('Y-m-d') }}"></div>
                </div>
                <div class="flex justify-end mt-1">
                  <button class="btn btn-primary" onclick="savePersonalInfo()">Save Changes</button>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <div>
                    <div class="card-title">Contact & Office Details</div>
                    <div class="card-sub">How others can reach you</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Email Address</label><input class="field-input" type="email" id="pi-gmail" value="{{ auth()->user()->usr_email }}"></div>
                  <div><label class="field-label">Phone Number</label><input class="field-input" id="pi-phone" value="{{ $chair->dc_phone_number }}"></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Office Address</label><input class="field-input" id="pi-address" value="{{ $chair->dc_address }}"></div>
                  <div><label class="field-label">Department</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $departmentName }}" readonly></div>
                </div>
                <div class="mb-4">
                  <label class="field-label">Bio / About</label>
                  <textarea class="field-input resize-y" id="pi-bio" rows="3">{{ $chair->dc_bio }}</textarea>
                </div>
                <div class="flex justify-end">
                  <button class="btn btn-primary" onclick="saveContactInfo()">Save Changes</button>
                </div>
              </div>
            </div>

            {{-- GENERAL --}}
            <div id="csec-general" style="display:none;">
              @php
              $institution = \App\Models\Institution::first();
              @endphp
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">Institution Name</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $institution->inst_name ?? 'Cebu Technological University' }}" readonly></div>
                <div><label class="field-label">Campus</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $institution->inst_branch_campus ?? 'Main Campus' }}" readonly></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">College / Unit</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $institution->inst_college ?? 'College of Computing, Information and Communications Technology' }}" readonly></div>
                <div><label class="field-label">Abbreviation</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $institution->inst_abbreviation ?? 'CCICT' }}" readonly></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">Contact Email</label><input class="field-input bg-slate-100 cursor-not-allowed" type="email" value="{{ $institution->inst_contact_email ?? 'ccict@ctu.edu.ph' }}" readonly></div>
                <div><label class="field-label">Phone</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $institution->inst_phone ?? '(032) 401-7777' }}" readonly></div>
              </div>
            </div>

            {{-- ACADEMIC YEAR --}}
            <div id="csec-academic" style="display:none;">
              <div class="card mb-4">
                <div class="card-header">
                  <div>
                    <div class="card-title">Current Academic Year</div>
                    <div class="card-sub">Managed by the Technical Administrator</div>
                  </div>
                  @if($academicYear)
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-600">Active</span>
                  @else
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-500">Not Set</span>
                  @endif
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Academic Year</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $academicYear->ay_academic_year ?? 'Not configured' }}" readonly></div>
                  <div><label class="field-label">Semester</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $activeSemester->sem_name ?? 'Not configured' }}" readonly></div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-3">
                  <div><label class="field-label">Start Date</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $activeSemester?->sem_start_date?->format('Y-m-d') ?? '—' }}" readonly></div>
                  <div><label class="field-label">End Date</label><input class="field-input bg-slate-100 cursor-not-allowed" value="{{ $activeSemester?->sem_end_date?->format('Y-m-d') ?? '—' }}" readonly></div>
                </div>
              </div>
            </div>

            {{-- NOTIFICATIONS --}}
            <div id="csec-notifications" style="display:none;">
              <div class="card">
                <div class="card-header">
                  <div>
                    <div class="card-title">Notification Preferences</div>
                    <div class="card-sub">Choose what alerts you receive as Department Chair</div>
                  </div>
                </div>
                <div class="flex flex-col gap-3 mt-1">
                  <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                    <div>
                      <div class="text-[13px] font-semibold text-slate-900">Faculty Overload Alert</div>
                      <div class="text-xs text-slate-400 mt-0.5">Notify when any faculty member exceeds their maximum unit load</div>
                    </div>
                    <label class="toggle-switch">
                      <input type="checkbox" data-field="chair_notif_faculty_overload" {{ $notificationPreferences['chair_notif_faculty_overload'] ? 'checked' : '' }} onchange="toggleSwitch(this)">
                      <span class="toggle-track {{ $notificationPreferences['chair_notif_faculty_overload'] ? 'on' : '' }}"><span class="toggle-thumb"></span></span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            {{-- SECURITY --}}
            <div id="csec-security" style="display:none;">
              <div class="card mb-4">
                <div class="card-header">
                  <div>
                    <div class="card-title">Change Password</div>
                    <div class="card-sub">Update your Department Chair portal account password</div>
                  </div>
                </div>
                <div class="mb-3.5">
                  <label class="field-label">Current Password</label>
                  <div class="relative">
                    <input class="field-input pr-10" type="password" placeholder="Enter your current password" id="pw-current">
                    <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePasswordVisibility('pw-current', this)">
                      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" />
                        <circle cx="12" cy="12" r="3" />
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-1">
                  <div>
                    <label class="field-label">New Password</label>
                    <div class="relative">
                      <input class="field-input pr-10" type="password" placeholder="Min. 8 characters" id="pw-new" oninput="checkPwStrength(this.value)">
                      <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePasswordVisibility('pw-new', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </div>
                    <div class="h-1 rounded bg-slate-200 mt-1.5 overflow-hidden">
                      <div class="h-full rounded transition-all" id="pw-bar" style="width:0"></div>
                    </div>
                  </div>
                  <div>
                    <label class="field-label">Confirm New Password</label>
                    <div class="relative">
                      <input class="field-input pr-10" type="password" placeholder="Re-enter new password" id="pw-confirm">
                      <button type="button" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" onclick="togglePasswordVisibility('pw-confirm', this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z" />
                          <circle cx="12" cy="12" r="3" />
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
                <div class="flex justify-end mt-1">
                  <button class="btn btn-primary" onclick="savePassword()">Update Password</button>
                </div>
              </div>

              <div class="card">
                <div class="card-header">
                  <div>
                    <div class="card-title">Session & Access</div>
                    <div class="card-sub">Manage login security and session behaviour</div>
                  </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-1">
                  <div>
                    <label class="field-label">Session Timeout</label>
                    @php $timeout = (int) \App\Models\SystemSetting::get('usr_session_timeout_minutes', 30); @endphp
                    <select class="field-input" id="sec-timeout">
                      <option value="15" {{ $timeout === 15 ? 'selected' : '' }}>15 minutes</option>
                      <option value="30" {{ $timeout === 30 ? 'selected' : '' }}>30 minutes</option>
                      <option value="60" {{ $timeout === 60 ? 'selected' : '' }}>1 hour</option>
                      <option value="999999" {{ $timeout === 999999 ? 'selected' : '' }}>Never</option>
                    </select>
                  </div>
                  <div>
                    <label class="field-label">Max Login Attempts</label>
                    @php $attempts = (int) \App\Models\SystemSetting::get('usr_max_login_attempts', 5); @endphp
                    <select class="field-input" id="sec-attempts">
                      <option value="3" {{ $attempts === 3 ? 'selected' : '' }}>3</option>
                      <option value="5" {{ $attempts === 5 ? 'selected' : '' }}>5</option>
                      <option value="10" {{ $attempts === 10 ? 'selected' : '' }}>10</option>
                    </select>
                  </div>
                </div>
                <div class="flex justify-end mt-1">
                  <button class="btn btn-primary" onclick="saveSecuritySettings()">Save Changes</button>
                </div>
              </div>
            </div>

            {{-- SYSTEM INFO --}}
            <div id="csec-system" style="display:none;">
              <div class="card mb-4">
                <div class="card-header">
                  <div>
                    <div class="card-title">System Information</div>
                    <div class="card-sub">Current environment and infrastructure details</div>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-600">All Systems Normal</span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Application</div>
                    <div class="text-[13px] font-semibold text-slate-900">SKEDYUL v1.0.0</div>
                  </div>
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Host / Deploy</div>
                    <div class="text-[13px] font-semibold text-slate-900">Vercel (Production)</div>
                  </div>
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Database</div>
                    <div class="text-[13px] font-semibold text-slate-900">Supabase PostgreSQL</div>
                  </div>
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Authentication</div>
                    <div class="text-[13px] font-semibold text-slate-900">JWT + Laravel Sanctum</div>
                  </div>
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Frontend Stack</div>
                    <div class="text-[13px] font-semibold text-slate-900">Tailwind CSS + Vanilla JS</div>
                  </div>
                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Mobile App</div>
                    <div class="text-[13px] font-semibold text-slate-900">React Native</div>
                  </div>
                  @php
                  $latestBackup = \App\Models\SystemBackup::orderByDesc('bkp_ran_at')->first();

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

                  <div class="bg-slate-50 rounded-lg p-3.5">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">Server Uptime</div>
                    <div class="text-[13px] font-semibold text-slate-900">{{ $uptimeText }}</div>
                  </div>
                </div>
              </div>
            </div>

          </div>{{-- /chair-settings-content --}}
        </div>
      </div>
    </div>
  </div>

  <div class="toast" id="toast"><span id="toast-msg"></span></div>

  <script>
    function showChairSettingsSection(section, el) {
      ['profile', 'general', 'academic', 'notifications', 'security', 'system'].forEach(s => {
        const p = document.getElementById('csec-' + s);
        if (p) p.style.display = 'none';
      });
      const t = document.getElementById('csec-' + section);
      if (t) t.style.display = 'block';
      document.querySelectorAll('.settings-nav-item').forEach(i => i.classList.remove('active'));
      el.classList.add('active');
    }

    function toggleSwitch(input) {
      const track = input.nextElementSibling;
      if (input.checked) {
        track.classList.add('on');
      } else {
        track.classList.remove('on');
      }

      const field = input.dataset.field;
      if (!field) return;

      fetch('{{ route("chair.profile.notification-preferences.update") }}', {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          },
          body: JSON.stringify({
            [field]: input.checked
          }),
        })
        .then(res => res.json())
        .then(data => {
          if (!data.success) {
            showToast('❌ Failed to update preference');
            return;
          }
          showToast('Preference updated!');
        })
        .catch(() => showToast('❌ Something went wrong saving.'));
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

    function uploadAvatar(input) {
      const file = input.files[0];
      if (!file) return;

      const formData = new FormData();
      formData.append('avatar', file);

      fetch("{{ route('chair.profile.avatar.update') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: formData,
        })
        .then(res => res.json().then(data => ({
          status: res.status,
          data
        })))
        .then(({
          data
        }) => {
          if (data.success) {
            const display = document.getElementById('avatar-display');
            display.style.backgroundImage = `url('${data.url}')`;
            display.textContent = '';
            showToast('Profile photo updated!');
          } else {
            showToast(data.message || 'Upload failed. Please try again.');
          }
        })
        .catch(() => showToast('Upload failed. Please try again.'));
    }

    function removeAvatar() {
      fetch("{{ route('chair.profile.avatar.remove') }}", {
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
            display.textContent = '{{ strtoupper(substr($chair->dc_first_name, 0, 1)) }}';
            showToast('Profile photo removed.');
          } else {
            showToast('Remove failed. Please try again.');
          }
        })
        .catch(() => showToast('Remove failed. Please try again.'));
    }

    function savePersonalInfo() {
      const payload = {
        dc_first_name: document.getElementById('pi-first').value,
        dc_last_name: document.getElementById('pi-last').value,
        dc_middle_name: document.getElementById('pi-middle').value,
        dc_suffix: document.getElementById('pi-suffix').value,
        dc_employee_id: document.getElementById('pi-empid').value,
        dc_gender: document.getElementById('pi-gender').value,
        dc_civil_status: document.getElementById('pi-civil').value,
        dc_dob: document.getElementById('pi-dob').value || null,
        dc_nationality: document.getElementById('pi-nat').value,
      };

      fetch("{{ route('chair.profile.personal-info.update') }}", {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload),
        })
        .then(res => res.json().then(data => ({
          status: res.status,
          data
        })))
        .then(({
          data
        }) => {
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
        usr_email: document.getElementById('pi-gmail').value,
        dc_phone_number: document.getElementById('pi-phone').value,
        dc_address: document.getElementById('pi-address').value,
        dc_bio: document.getElementById('pi-bio').value,
      };

      fetch("{{ route('chair.profile.contact.update') }}", {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload),
        })
        .then(res => res.json().then(data => ({
          status: res.status,
          data
        })))
        .then(({
          data
        }) => {
          if (data.success) {
            showToast('Contact details saved successfully!');
          } else {
            const firstError = data.errors ? Object.values(data.errors)[0][0] : 'Save failed. Please check your inputs.';
            showToast(firstError);
          }
        })
        .catch(() => showToast('Save failed. Please try again.'));
    }

    function savePassword() {
      const current = document.getElementById('pw-current').value;
      const newPw = document.getElementById('pw-new').value;
      const confirm = document.getElementById('pw-confirm').value;

      if (!current) {
        showToast('Please enter your current password.');
        return;
      }
      if (newPw.length < 8) {
        showToast('New password must be at least 8 characters.');
        return;
      }
      if (newPw !== confirm) {
        showToast('New passwords do not match.');
        return;
      }

      fetch("{{ route('chair.profile.password.update') }}", {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            current_password: current,
            new_password: newPw,
            new_password_confirmation: confirm,
          }),
        })
        .then(res => res.json().then(data => ({
          status: res.status,
          data
        })))
        .then(({
          data
        }) => {
          if (data.success) {
            document.getElementById('pw-current').value = '';
            document.getElementById('pw-new').value = '';
            document.getElementById('pw-confirm').value = '';
            document.getElementById('pw-bar').style.width = '0';
            showToast('Password updated successfully!');
          } else {
            showToast(data.message || 'Update failed. Please check your inputs.');
          }
        })
        .catch(() => showToast('Update failed. Please try again.'));
    }

    function togglePasswordVisibility(inputId, btn) {
      const input = document.getElementById(inputId);
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      btn.innerHTML = isHidden ?
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a21.6 21.6 0 0 1 5.06-6.06M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a21.6 21.6 0 0 1-2.16 3.19M14.12 14.12a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>' :
        '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8Z"/><circle cx="12" cy="12" r="3"/></svg>';
    }

    function saveSecuritySettings() {
      const payload = {
        usr_session_timeout_minutes: parseInt(document.getElementById('sec-timeout').value, 10),
        usr_max_login_attempts: parseInt(document.getElementById('sec-attempts').value, 10),
      };

      fetch("{{ route('chair.security.update') }}", {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
          },
          body: JSON.stringify(payload),
        })
        .then(res => res.json().then(data => ({
          status: res.status,
          data
        })))
        .then(({
          data
        }) => {
          if (data.success) {
            showToast('Security settings saved!');
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
