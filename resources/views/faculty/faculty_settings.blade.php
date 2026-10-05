<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — Faculty Settings</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>

<div id="screen-app" class="screen active" style="flex-direction:row;">

  @include('partials.facultyMember_sidebar')

  <!-- Main -->
  <div class="main">

    @include('partials.faculty_header', [
        'title' => 'Settings',
        'announcements' => $announcements ?? null,
    ])

    <!-- FACULTY SETTINGS PAGE -->
    <div id="page-faculty-settings" class="page active">
      <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-6 items-start">

        <!-- Settings side nav -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm py-3">
          <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wide px-5 py-2">Settings</div>
          <button type="button"
                  class="settings-nav-item w-full text-left px-5 py-2.5 text-sm font-semibold text-blue-600 bg-blue-50 border-r-2 border-blue-600"
                  onclick="showFacSettingsSection('profile', this)">Personal Info</button>
          <button type="button"
                  class="settings-nav-item w-full text-left px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                  onclick="showFacSettingsSection('security', this)">Security</button>
          <button type="button"
                  class="settings-nav-item w-full text-left px-5 py-2.5 text-sm font-semibold text-gray-600 hover:bg-gray-50"
                  onclick="showFacSettingsSection('notifications', this)">Notifications</button>
        </div>

        <div id="fac-settings-content">

          <!-- PERSONAL INFO -->
          <div id="fac-settings-profile">

            <!-- Profile picture -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Profile Picture</div>
                <div class="text-xs text-gray-500 mt-0.5">Upload a new profile photo</div>
              </div>
              <div class="flex items-center gap-6">
                <div class="relative shrink-0">
                  <div id="fac-pic-preview"
                       class="w-24 h-24 rounded-full bg-gray-100 flex items-center justify-center text-2xl font-extrabold text-green-600 border-[3px] border-gray-200 overflow-hidden">
                    {{ strtoupper(substr($faculty->fac_first_name ?? 'J', 0, 1) . substr($faculty->fac_last_name ?? 'B', 0, 1)) }}
                  </div>
                  <button type="button"
                          onclick="document.getElementById('fac-pic-upload').click()"
                          class="absolute bottom-0 right-0 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center text-sm border-2 border-white cursor-pointer">+</button>
                  <input type="file" id="fac-pic-upload" accept="image/*" class="hidden" onchange="facPreviewPic(this)">
                </div>
                <div>
                  <div class="text-[15px] font-bold text-gray-900">{{ $faculty->full_name ?? 'Faculty Member' }}</div>
                  <div class="text-xs text-gray-500 mt-0.5">Faculty · {{ $faculty->department->dept_code ?? 'N/A' }} Department</div>
                  <div class="flex gap-2 mt-3">
                    <button type="button"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-blue-600 text-white hover:bg-blue-700"
                            onclick="document.getElementById('fac-pic-upload').click()">Upload Photo</button>
                    <button type="button"
                            class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 text-gray-600 hover:bg-gray-200"
                            onclick="facResetPic()">Remove</button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Personal information -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Personal Information</div>
                <div class="text-xs text-gray-500 mt-0.5">Update your personal details</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">First Name</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_first_name ?? '' }}">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Last Name</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_last_name ?? '' }}">
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Middle Name</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         placeholder="Optional" value="{{ $faculty->fac_middle_name ?? '' }}">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Employee ID</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_employee_id ?? $faculty->fac_id ?? '' }}">
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Gender</label>
                  <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
                    <option {{ ($faculty->fac_gender ?? '') === 'Male' ? 'selected' : '' }}>Male</option>
                    <option {{ ($faculty->fac_gender ?? '') === 'Female' ? 'selected' : '' }}>Female</option>
                    <option>Prefer not to say</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Civil Status</label>
                  <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
                    <option>Single</option>
                    <option>Married</option>
                    <option>Widowed</option>
                  </select>
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Date of Birth</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900" type="date"
                         value="{{ $faculty->fac_birthdate ?? '' }}">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Nationality</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_nationality ?? 'Filipino' }}">
                </div>
              </div>
              <div class="flex justify-end">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                        onclick="showToast('Personal info saved successfully!')">Save Changes</button>
              </div>
            </div>

            <!-- Contact & office -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Contact & Office</div>
                <div class="text-xs text-gray-500 mt-0.5">How others can reach you</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Email Address</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900" type="email"
                         value="{{ $faculty->fac_email ?? '' }}">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Phone Number</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_phone ?? '' }}">
                </div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Office Location</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900"
                         value="{{ $faculty->fac_office ?? '' }}">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Department</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-500 bg-gray-50 cursor-not-allowed"
                         value="{{ $faculty->department->dept_code ?? 'N/A' }}" readonly>
                </div>
              </div>
              <div class="mb-4">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Bio / About</label>
                <textarea class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900 resize-y" rows="3">{{ $faculty->fac_bio ?? '' }}</textarea>
              </div>
              <div class="flex justify-end">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                        onclick="showToast('Contact details saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- SECURITY -->
          <div id="fac-settings-security" class="hidden">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-4">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Change Password</div>
                <div class="text-xs text-gray-500 mt-0.5">Update your account password</div>
              </div>
              <div class="mb-3">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Current Password</label>
                <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900" type="password" placeholder="••••••••">
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">New Password</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900" type="password" placeholder="Min. 8 characters">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Confirm New Password</label>
                  <input class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900" type="password" placeholder="Re-enter new password">
                </div>
              </div>
              <div class="flex justify-end">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                        onclick="showToast('Password updated successfully!')">Update Password</button>
              </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Session Settings</div>
                <div class="text-xs text-gray-500 mt-0.5">Manage your login preferences</div>
              </div>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Session Timeout</label>
                  <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
                    <option>15 minutes</option>
                    <option selected>30 minutes</option>
                    <option>1 hour</option>
                    <option>Never</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 mb-1">Max Login Attempts</label>
                  <select class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-900">
                    <option>3</option>
                    <option selected>5</option>
                    <option>10</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                        onclick="showToast('Security settings saved!')">Save Changes</button>
              </div>
            </div>
          </div>

          <!-- NOTIFICATION PREFERENCES -->
          <div id="fac-settings-notifications" class="hidden">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5">
              <div class="mb-4">
                <div class="text-base font-bold text-gray-900">Notification Preferences</div>
                <div class="text-xs text-gray-500 mt-0.5">Choose what alerts you receive</div>
              </div>
              <div class="flex flex-col gap-3">
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-[10px]">
                  <div>
                    <div class="text-[13px] font-semibold text-gray-900">Schedule Updates</div>
                    <div class="text-xs text-gray-500 mt-0.5">When your schedule is modified</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked onchange="toggleSwitch(this)">
                    <div class="w-10 h-5 bg-gray-300 peer-checked:bg-blue-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-[10px]">
                  <div>
                    <div class="text-[13px] font-semibold text-gray-900">New Assignments</div>
                    <div class="text-xs text-gray-500 mt-0.5">When a new subject is assigned to you</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked onchange="toggleSwitch(this)">
                    <div class="w-10 h-5 bg-gray-300 peer-checked:bg-blue-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-[10px]">
                  <div>
                    <div class="text-[13px] font-semibold text-gray-900">Reminders</div>
                    <div class="text-xs text-gray-500 mt-0.5">Deadlines and important announcements</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" checked onchange="toggleSwitch(this)">
                    <div class="w-10 h-5 bg-gray-300 peer-checked:bg-blue-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                  </label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-gray-50 rounded-[10px]">
                  <div>
                    <div class="text-[13px] font-semibold text-gray-900">System Announcements</div>
                    <div class="text-xs text-gray-500 mt-0.5">General system updates and messages</div>
                  </div>
                  <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" class="sr-only peer" onchange="toggleSwitch(this)">
                    <div class="w-10 h-5 bg-gray-300 peer-checked:bg-blue-600 rounded-full after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                  </label>
                </div>
              </div>
              <div class="flex justify-end mt-4">
                <button type="button"
                        class="px-4 py-2 rounded-lg text-sm font-semibold bg-blue-600 text-white hover:bg-blue-700"
                        onclick="showToast('Notification preferences saved!')">Save Preferences</button>
              </div>
            </div>
          </div>

        </div>
      </div>
    </div>

  </div><!-- end .main -->
</div><!-- end #screen-app -->

<!-- TOAST -->
<div class="toast fixed bottom-6 right-6 z-[300] bg-gray-900 text-white text-sm font-semibold px-5 py-3 rounded-xl shadow-lg opacity-0 translate-y-2 pointer-events-none transition-all duration-300 [&.show]:opacity-100 [&.show]:translate-y-0 [&.show]:pointer-events-auto"
     id="toast">✅ <span id="toast-msg"></span></div>

<script>
// ── SETTINGS SECTIONS ────────────────────────────────────────────────────
function showFacSettingsSection(section, el) {
  ['profile', 'security', 'notifications'].forEach(s => {
    const elem = document.getElementById('fac-settings-' + s);
    if (elem) elem.classList.toggle('hidden', s !== section);
  });
  document.querySelectorAll('#page-faculty-settings .settings-nav-item').forEach(i => {
    i.classList.remove('text-blue-600', 'bg-blue-50', 'border-r-2', 'border-blue-600');
    i.classList.add('text-gray-600');
  });
  if (el) {
    el.classList.remove('text-gray-600');
    el.classList.add('text-blue-600', 'bg-blue-50', 'border-r-2', 'border-blue-600');
  }
}

function facPreviewPic(input) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const p = document.getElementById('fac-pic-preview');
    p.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
    showToast('Profile photo updated!');
  };
  reader.readAsDataURL(input.files[0]);
}

function facResetPic() {
  const p = document.getElementById('fac-pic-preview');
  if (p) {
    p.innerHTML = '{{ strtoupper(substr($faculty->fac_first_name ?? "J", 0, 1) . substr($faculty->fac_last_name ?? "B", 0, 1)) }}';
  }
  const upload = document.getElementById('fac-pic-upload');
  if (upload) upload.value = '';
  showToast('Profile photo removed.');
}

function toggleSwitch(/* checkbox */) {
  // Visual state handled by peer classes; hook API save here later
}

function showToast(msg) {
  const t = document.getElementById('toast');
  document.getElementById('toast-msg').textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 3000);
}

</script>
</body>
</html>