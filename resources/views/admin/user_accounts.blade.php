<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL — User Accounts</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

  <div class="app-shell">
    @include('partials.admin_sidebar')

    <div class="app-main">
      @include('partials.admin_header', ['title' => 'User Accounts'])

      <div class="page-content" id="page-user-accounts">

        {{-- STAT CARDS --}}
        <div class="grid grid-cols-4 gap-3 mb-4">
          <div class="stat-card">
            <div class="stat-card-bar bg-blue-600"></div>
            <div class="stat-icon">👥</div>
            <div class="stat-label">Total Users</div>
            <div class="stat-value">{{ $stats['total'] ?? $users->total() }}</div>
            <div class="stat-sub">All roles combined</div>
          </div>

          <div class="stat-card">
            <div class="stat-card-bar bg-green-600"></div>
            <div class="stat-icon">👨‍🏫</div>
            <div class="stat-label">Faculty</div>
            <div class="stat-value">{{ $stats['faculty'] ?? 0 }}</div>
            <div class="stat-sub">BSIS · BSIT · BIT-CT</div>
          </div>

          <div class="stat-card">
            <div class="stat-card-bar bg-amber-500"></div>
            <div class="stat-icon">📋</div>
            <div class="stat-label">Dept. Chairs</div>
            <div class="stat-value">{{ $stats['chairs'] ?? 0 }}</div>
            <div class="stat-sub">Active this semester</div>
          </div>

          <div class="stat-card">
            <div class="stat-card-bar bg-cyan-600"></div>
            <div class="stat-icon">✅</div>
            <div class="stat-label">Active Accounts</div>
            <div class="stat-value">{{ $stats['active'] ?? 0 }}</div>
            <div class="stat-sub">{{ $stats['inactive'] ?? 0 }} inactive</div>
          </div>
        </div>

        {{-- USER TABLE --}}
        <div class="card">
          <div class="card-header">
            <div>
              <div class="card-title">All User Accounts</div>
              <div class="card-sub">Click a name to view full profile</div>
            </div>

            <div class="flex gap-2 items-center">
              <input id="user-search" placeholder="Search users..."
                oninput="filterUsers(this.value)" class="field-input w-[200px]">

              <button type="button" onclick="openAddModal()" class="btn btn-primary">
                + Add User
              </button>
            </div>
          </div>

          <div class="overflow-x-auto">
            <table class="data-table" id="users-table">
              <thead>
                <tr>
                  @foreach(['Name (A–Z)','Role','Email','Status','Action'] as $h)
                  <th>{{ $h }}</th>
                  @endforeach
                </tr>
              </thead>

              <tbody>
                @forelse($users as $user)

                @php
                $displayName = trim(implode(' ', array_filter([
                $user->usr_first_name,
                $user->usr_middle_name,
                $user->usr_last_name,
                $user->usr_suffix,
                ])));

                $displayName = $displayName ?: $user->usr_name;

                $colors = ['#2563eb','#16a34a','#d97706','#0891b2','#7c3aed'];
                $colorIndex = crc32($displayName) % count($colors);
                $avatarColor = $colors[$colorIndex];
                $avatarUrl = match ($user->usr_role) {
                  'faculty' => $user->faculty?->fac_profile_image
                    ? asset('images/faculty_profile/' . $user->faculty->fac_profile_image)
                    : null,
                  'dean' => $user->dean?->dean_profile_image
                    ? asset('images/dean_profile/' . $user->dean->dean_profile_image)
                    : ($user->faculty?->fac_profile_image ? asset('images/faculty_profile/' . $user->faculty->fac_profile_image) : null),
                  'department_chair' => $user->deptChair?->dc_profile_image
                    ? asset('images/chair_profile/' . $user->deptChair->dc_profile_image)
                    : ($user->faculty?->fac_profile_image ? asset('images/faculty_profile/' . $user->faculty->fac_profile_image) : null),
                  default => null,
                };

                $roleLabels = [
                'faculty' => 'Faculty',
                'department_chair' => 'Dept. Chair',
                'dean' => 'Dean',
                'system_admin' => 'System Admin',
                ];
                @endphp

                <tr class="user-row"
                  data-user-id="{{ $user->usr_id }}"
                  data-name="{{ $displayName }}"
                  data-first-name="{{ $user->usr_first_name }}"
                  data-last-name="{{ $user->usr_last_name }}"
                  data-role="{{ $user->usr_role }}"
                  data-special-position="{{ $user->faculty?->fac_special_position ?? '' }}"
                  data-email="{{ $user->usr_email }}"
                  data-active="{{ $user->usr_is_active ? '1' : '0' }}"
                  data-bio="{{ $user->usr_bio }}"
                  data-room-location="{{ $user->room_location }}">

                  <td>
                    <div class="flex items-center gap-2.5">

                      <div
                        class="w-[34px] h-[34px] rounded-full flex items-center justify-center text-[13px] font-extrabold text-white flex-shrink-0 overflow-hidden"
                        style="background:{{ $avatarColor }};">
                        @if($avatarUrl)
                          <img src="{{ $avatarUrl }}" alt="{{ $displayName }} profile photo" class="h-full w-full object-cover">
                        @else
                          {{ strtoupper(substr($displayName, 0, 1)) }}
                        @endif
                      </div>

                      <button
                        type="button"
                        onclick="openProfileModal('{{ $user->usr_id }}')"
                        class="font-semibold text-[13px] text-slate-900 hover:text-blue-600 text-left">
                        {{ $displayName }}
                      </button>

                    </div>
                  </td>

                  <td>
                    <div class="flex flex-col items-start gap-1">
                      <span class="badge badge-grey">
                        {{ $roleLabels[$user->usr_role] ?? ucfirst($user->usr_role) }}
                      </span>
                      @if ($user->faculty?->fac_special_position)
                        <span class="text-[11px] text-slate-500">{{ $user->faculty->fac_special_position }} · {{ rtrim(rtrim(number_format((float) $user->faculty->fac_special_position_max_hours, 2), '0'), '.') }}h/week</span>
                      @endif
                    </div>
                  </td>

                  <td class="text-slate-500">
                    {{ $user->usr_email }}
                  </td>

                  <td>
                    <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                      {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                    </span>
                  </td>

                  <td>
                    <div class="flex gap-1.5">

                      <button
                        type="button"
                        class="btn btn-secondary !px-3 !py-1.5 !text-[12px]"
                        onclick="openEditModal('{{ $user->usr_id }}')">
                        Edit
                      </button>

                      <form
                        method="POST"
                        action="{{ route('admin.users.destroy', ['id' => $user->usr_id]) }}"
                        onsubmit="return confirm('Delete this user?')"
                        class="inline">

                        @csrf
                        @method('DELETE')

                        <button
                          type="submit"
                          class="btn btn-danger !px-3 !py-1.5 !text-[12px]">
                          Delete
                        </button>

                      </form>

                    </div>
                  </td>

                </tr>

                @empty

                <tr>
                  <td colspan="5"
                    class="text-center py-10 text-[14px] text-slate-400">
                    No user accounts found. Click
                    <strong>+ Add User</strong> to get started.
                  </td>
                </tr>

                @endforelse
              </tbody>
            </table>
          </div>

          <div class="flex items-center justify-between mt-4 pt-3.5 border-t border-slate-200">
            <div id="page-info" class="text-[13px] text-slate-400">Showing 0–0 of 0 users</div>

            <div class="flex items-center gap-1.5">
              <button id="btn-prev" type="button"
                onclick="changePage(-1)" class="btn btn-secondary">
                ← Prev
              </button>

              <div id="page-numbers" class="flex gap-1"></div>

              <button id="btn-next" type="button"
                onclick="changePage(1)" class="btn btn-secondary">
                Next →
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>


  {{-- ========================================================= --}}
  {{-- ADD USER MODAL --}}
  {{-- ========================================================= --}}

  <div class="modal-overlay" id="modal-add-user">
    <div class="modal-box w-[720px]">

      <div class="modal-header">
        <div class="modal-title">Add New User</div>
        <button type="button" onclick="closeModal('modal-add-user')" class="modal-close">✕</button>
      </div>

      <form method="POST" action="{{ route('admin.users.store') }}" id="form-add-user">
        @csrf

        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Email</label>
            <input name="usr_email" type="email" placeholder="user@ctu.edu.ph"
              required class="field-input">
          </div>

          <div>
            <label class="field-label">Role</label>
            <select name="usr_role" class="field-input" required
              onchange="toggleRoleFields(this)">
              <option value="">— Select role —</option>
              <option value="faculty">Faculty Member</option>
              <option value="department_chair">Department Chair</option>
              <option value="dean">Dean</option>
              <option value="system_admin">Technical Administrator</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Password</label>
            <input name="password" type="password"
              placeholder="Temporary password" required minlength="8"
              class="field-input">
          </div>

          <div class="role-field" data-roles="faculty,dean,department_chair" style="display:none;">
            <label class="field-label">Employee ID</label>
            <input name="usr_employee_id"
              placeholder="e.g. 2026-00123" class="field-input">
          </div>
        </div>

        <div class="border-t border-slate-200 my-3"></div>
        <div class="text-[12px] font-bold text-slate-500 uppercase mb-2">
          Personal Information
        </div>

        <div class="grid grid-cols-4 gap-3 mb-3">
          <div>
            <label class="field-label">First Name</label>
            <input name="usr_first_name" placeholder="Juan"
              required class="field-input">
          </div>

          <div>
            <label class="field-label">Middle Name</label>
            <input name="usr_middle_name" placeholder="A."
              class="field-input">
          </div>

          <div>
            <label class="field-label">Last Name</label>
            <input name="usr_last_name" placeholder="Dela Cruz"
              required class="field-input">
          </div>

          <div>
            <label class="field-label">Suffix</label>
            <input name="usr_suffix" placeholder="Jr., Sr., III"
              class="field-input">
          </div>
        </div>

        <div class="grid grid-cols-4 gap-3 mb-3">
          <div>
            <label class="field-label">Gender</label>
            <select name="usr_gender" class="field-input">
              <option value="">— Select —</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div>
            <label class="field-label">Civil Status</label>
            <select name="usr_civil_status" class="field-input">
              <option value="">— Select —</option>
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>

          <div>
            <label class="field-label">Date of Birth</label>
            <input name="usr_dob" type="date" class="field-input" min="1950-01-01" max="{{ \Illuminate\Support\Carbon::now()->subYears(6)->format('Y-m-d') }}">
          </div>

          <div>
            <label class="field-label">Nationality</label>
            <input name="usr_nationality"
              placeholder="Filipino" class="field-input">
          </div>
        </div>

        <div class="role-field" data-roles="faculty,dean,department_chair" style="display:none;">
          <div class="grid grid-cols-2 gap-3 mb-3">
            <div class="role-field" data-roles="faculty" style="display:none;">
              <label class="field-label">Rank / Title</label>
              <select name="usr_rank_title" class="field-input">
                <option value="">-- Select Rank --</option>
                <option value="Instructor I">Instructor I</option>
                <option value="Instructor II">Instructor II</option>
                <option value="Instructor III">Instructor III</option>
                <option value="Assistant Professor I">Assistant Professor I</option>
                <option value="Assistant Professor II">Assistant Professor II</option>
                <option value="Assistant Professor III">Assistant Professor III</option>
                <option value="Assistant Professor IV">Assistant Professor IV</option>
                <option value="Associate Professor I">Associate Professor I</option>
                <option value="Associate Professor II">Associate Professor II</option>
                <option value="Associate Professor III">Associate Professor III</option>
                <option value="Associate Professor IV">Associate Professor IV</option>
                <option value="Associate Professor V">Associate Professor V</option>
              </select>
            </div>

            <div>
              <label class="field-label">Employment Type</label>
              <select name="employment_type" class="field-input">
                <option value="full_time">Full-time</option>
                <option value="part_time">Part-time</option>
              </select>
            </div>
          </div>
          <div class="col-span-2">
            <label class="field-label">Special Position (Optional)</label>
            <select id="add-special-position" name="special_position" class="field-input" onchange="updateAdminSpecialPositionCap('add')">
              <option value="">None</option>
              @foreach (\App\Services\ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS as $position => $maxHours)
                <option value="{{ $position }}" data-max-hours="{{ $maxHours }}">{{ $position }}</option>
              @endforeach
            </select>
            <p id="add-special-position-cap" class="mt-1 text-[11px] text-slate-500">No special-position limit.</p>
          </div>
        </div>

        <div class="border-t border-slate-200 my-3"></div>

        <div class="role-field" data-roles="faculty,dean,department_chair" style="display:none;">
          <div class="text-[12px] font-bold text-slate-500 uppercase mb-2">
            Role Details
          </div>

          <div class="mb-3">
            <label class="field-label">Phone Number</label>
            <div class="flex gap-2">
              <select id="add-phone-cc" class="field-input w-[120px] shrink-0" onchange="syncPhoneField('add')">
                <option value="+63" selected>PH +63</option>
                <option value="+1">US +1</option>
                <option value="+44">UK +44</option>
                <option value="+81">JP +81</option>
                <option value="+82">KR +82</option>
                <option value="+65">SG +65</option>
                <option value="+60">MY +60</option>
                <option value="+62">ID +62</option>
              </select>
              <input id="add-phone-local" type="tel" inputmode="numeric" maxlength="10"
                placeholder="9123456789" class="field-input flex-1"
                oninput="onPhoneLocalInput(this, 'add')">
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Country code replaces the leading 0. Enter up to 10 digits (e.g. 9123456789).</p>
            <input type="hidden" name="role_phone_number" id="add-phone-full" value="">
          </div>

          <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
              <label class="field-label">Address</label>
              <input name="role_address"
                placeholder="Cebu City" class="field-input">
            </div>

            <div>
              <label class="field-label">College</label>
              <select id="add-college" name="college_id" class="field-input">
                <option value="">— Select college —</option>
                @foreach(($colleges ?? []) as $college)
                <option value="{{ $college->college_id }}">
                  {{ $college->college_name }} ({{ $college->college_code }})
                </option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <label class="field-label">Department</label>
            <select id="add-department" data-department-select name="dept_id" class="field-input">
              <option value="">— Select department —</option>
              @foreach(($departments ?? []) as $dept)
                <option value="{{ $dept->dept_id }}" data-college="{{ $dept->dept_college_id }}">
                {{ $dept->dept_name }} ({{ $dept->dept_code }})
              </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button"
            onclick="closeModal('modal-add-user')"
            class="btn btn-secondary">
            Cancel
          </button>

          <button type="submit" class="btn btn-primary">
            Create Account
          </button>
        </div>
      </form>
    </div>
  </div>


  {{-- ========================================================= --}}
  {{-- EDIT USER MODAL --}}
  {{-- ========================================================= --}}

  <div class="modal-overlay" id="modal-edit-user">
    <div class="modal-box w-[720px]">

      <div class="modal-header">
        <div class="modal-title">Edit User</div>
        <button type="button"
          onclick="closeModal('modal-edit-user')"
          class="modal-close">
          ✕
        </button>
      </div>

      <div class="flex items-center gap-3 p-3.5 rounded-xl mb-4"
        style="background:linear-gradient(135deg,#0f172a,#1e3a8a);">

        <div id="edit-avatar"
          class="w-10 h-10 rounded-full flex items-center justify-center text-base font-extrabold text-white shrink-0"
          style="border:2px solid rgba(255,255,255,.2);">
        </div>

        <div>
          <div id="edit-avatar-name"
            class="text-[13px] font-extrabold text-white"></div>
          <div id="edit-avatar-role"
            class="text-[11px] text-white/50 mt-0.5"></div>
        </div>
      </div>

      <form id="form-edit-user" method="POST" action="">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Email</label>
            <input id="edit-email" name="usr_email"
              type="email" required class="field-input">
          </div>

          <div>
            <label class="field-label">Role</label>
            <select id="edit-role" name="usr_role"
              class="field-input" required onchange="toggleRoleFields(this)">
              <option value="faculty">Faculty</option>
              <option value="department_chair">Dept. Chair</option>
              <option value="dean">Dean</option>
            </select>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-3">
          <div>
            <label class="field-label">Status</label>
            <select id="edit-status" name="usr_is_active"
              class="field-input">
              <option value="1">Active</option>
              <option value="0">Inactive</option>
            </select>
          </div>

          <div class="role-field"
            data-roles="faculty,dean,department_chair"
            style="display:none;">
            <label class="field-label">Employee ID</label>
            <input id="edit-employee-id"
              name="usr_employee_id" class="field-input">
          </div>
        </div>

        <div class="border-t border-slate-200 my-3"></div>

        <div class="text-[12px] font-bold text-slate-500 uppercase mb-2">
          Personal Information
        </div>

        <div class="grid grid-cols-4 gap-3 mb-3">
          <div>
            <label class="field-label">First Name</label>
            <input id="edit-first-name"
              name="usr_first_name" required class="field-input">
          </div>

          <div>
            <label class="field-label">Middle Name</label>
            <input id="edit-middle-name"
              name="usr_middle_name" class="field-input">
          </div>

          <div>
            <label class="field-label">Last Name</label>
            <input id="edit-last-name"
              name="usr_last_name" required class="field-input">
          </div>

          <div>
            <label class="field-label">Suffix</label>
            <input id="edit-suffix"
              name="usr_suffix" class="field-input">
          </div>
        </div>

        <div class="grid grid-cols-4 gap-3 mb-3">
          <div>
            <label class="field-label">Gender</label>
            <select id="edit-gender"
              name="usr_gender" class="field-input">
              <option value="">— Select —</option>
              <option value="Male">Male</option>
              <option value="Female">Female</option>
              <option value="Other">Other</option>
            </select>
          </div>

          <div>
            <label class="field-label">Civil Status</label>
            <select id="edit-civil-status"
              name="usr_civil_status" class="field-input">
              <option value="">— Select —</option>
              <option value="Single">Single</option>
              <option value="Married">Married</option>
              <option value="Widowed">Widowed</option>
              <option value="Separated">Separated</option>
            </select>
          </div>

          <div>
            <label class="field-label">Date of Birth</label>
            <input id="edit-dob" name="usr_dob" type="date" class="field-input" min="1950-01-01" max="{{ now()->subYears(6)->format('Y-m-d') }}">
          </div>

          <div>
            <label class="field-label">Nationality</label>
            <input id="edit-nationality"
              name="usr_nationality" class="field-input">
          </div>
        </div>

        <div class="role-field" data-roles="faculty,dean,department_chair" style="display:none;">
          <div class="grid grid-cols-2 gap-3 mb-3">
            <div class="role-field" data-roles="faculty" style="display:none;">
              <label class="field-label">Rank / Title</label>
              <select id="edit-rank-title" name="usr_rank_title" class="field-input">
                <option value="">-- Select Rank --</option>
                <option value="Instructor I">Instructor I</option>
                <option value="Instructor II">Instructor II</option>
                <option value="Instructor III">Instructor III</option>
                <option value="Assistant Professor I">Assistant Professor I</option>
                <option value="Assistant Professor II">Assistant Professor II</option>
                <option value="Assistant Professor III">Assistant Professor III</option>
                <option value="Assistant Professor IV">Assistant Professor IV</option>
                <option value="Associate Professor I">Associate Professor I</option>
                <option value="Associate Professor II">Associate Professor II</option>
                <option value="Associate Professor III">Associate Professor III</option>
                <option value="Associate Professor IV">Associate Professor IV</option>
                <option value="Associate Professor V">Associate Professor V</option>
                <option value="Associate Professor V">Professor I</option>
                <option value="Associate Professor V">Professor II</option>
                <option value="Associate Professor V">Professor III</option>
                <option value="Associate Professor V">Professor IV</option>
                <option value="Associate Professor V">Professor V</option>
                <option value="Associate Professor V">Professor VI</option>
                <option value="Associate Professor V">University Professor</option>
              </select>
            </div>

            <div>
              <label class="field-label">Employment Type</label>
              <select id="edit-employment"
                name="employment_type" class="field-input">
                <option value="full_time">Full-time</option>
                <option value="part_time">Part-time</option>
              </select>
            </div>
          </div>
          <div class="col-span-2">
            <label class="field-label">Special Position (Optional)</label>
            <select id="edit-special-position" name="special_position" class="field-input" onchange="updateAdminSpecialPositionCap('edit')">
              <option value="">None</option>
              @foreach (\App\Services\ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS as $position => $maxHours)
                <option value="{{ $position }}" data-max-hours="{{ $maxHours }}">{{ $position }}</option>
              @endforeach
            </select>
            <p id="edit-special-position-cap" class="mt-1 text-[11px] text-slate-500">No special-position limit.</p>
          </div>
        </div>

        <div class="border-t border-slate-200 my-3"></div>

        <div class="role-field"
          data-roles="faculty,dean,department_chair"
          style="display:none;">

          <div class="text-[12px] font-bold text-slate-500 uppercase mb-2">
            Role Details
          </div>

          <div class="mb-3">
            <label class="field-label">Phone Number</label>
            <div class="flex gap-2">
              <select id="edit-phone-cc" class="field-input w-[120px] shrink-0" onchange="syncPhoneField('edit')">
                <option value="+63" selected>PH +63</option>
                <option value="+1">US +1</option>
                <option value="+44">UK +44</option>
                <option value="+81">JP +81</option>
                <option value="+82">KR +82</option>
                <option value="+65">SG +65</option>
                <option value="+60">MY +60</option>
                <option value="+62">ID +62</option>
              </select>
              <input id="edit-phone-local" type="tel" inputmode="numeric" maxlength="10"
                placeholder="9123456789" class="field-input flex-1"
                oninput="onPhoneLocalInput(this, 'edit')">
            </div>
            <p class="text-[11px] text-slate-400 mt-1">Country code replaces the leading 0. Enter up to 10 digits (e.g. 9123456789).</p>
            <input type="hidden" name="role_phone_number" id="edit-phone-full" value="">
          </div>

          <div class="grid grid-cols-2 gap-3 mb-3">
            <div>
              <label class="field-label">Address</label>
              <input id="edit-address"
                name="role_address" class="field-input">
            </div>

            <div>
              <label class="field-label">College</label>
              <select id="edit-college"
                name="college_id" class="field-input">
                <option value="">— Select college —</option>
                @foreach(($colleges ?? []) as $college)
                <option value="{{ $college->college_id }}">
                  {{ $college->college_name }} ({{ $college->college_code }})
                </option>
                @endforeach
              </select>
            </div>
          </div>

          <div>
            <label class="field-label">Department</label>
              <select id="edit-department" data-department-select
              name="dept_id" class="field-input">
              <option value="">— Select department —</option>
              @foreach(($departments ?? []) as $dept)
                <option value="{{ $dept->dept_id }}" data-college="{{ $dept->dept_college_id }}">
                {{ $dept->dept_name }} ({{ $dept->dept_code }})
              </option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="mb-3 mt-3">
          <label class="field-label">About / Bio</label>
          <textarea id="edit-about" name="usr_bio"
            class="field-input min-h-12 resize-y"
            rows="2"></textarea>
        </div>

        <div class="bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12px] text-amber-800 mb-1">
          ⚠️ Changes will be reflected immediately across the system.
        </div>

        <div class="modal-footer">
          <button type="button"
            onclick="closeModal('modal-edit-user')"
            class="btn btn-secondary">
            Cancel
          </button>

          <button type="button"
            onclick="confirmDeleteFromEdit()"
            class="btn btn-danger">
            Delete
          </button>

          <button type="submit" class="btn btn-primary">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>


  {{-- ========================================================= --}}
  {{-- PROFILE MODAL --}}
  {{-- ========================================================= --}}

  <div class="modal-overlay" id="modal-user-profile">
    <div class="modal-box w-[520px] max-w-[calc(100vw-32px)]">

      <div class="modal-header">
        <div class="modal-title">User Profile</div>
        <button type="button"
          onclick="closeModal('modal-user-profile')"
          class="modal-close">
          ✕
        </button>
      </div>

      <div class="profile-modal-banner">
        <div id="profile-avatar" class="profile-modal-avatar"></div>

        <div class="min-w-0">
          <div id="profile-name" class="profile-modal-name"></div>
          <div id="profile-role" class="profile-modal-role"></div>
          <span id="profile-status" class="profile-modal-status"></span>
        </div>
      </div>

      <div class="profile-modal-grid">

        <div class="profile-modal-field">
          <div class="profile-modal-label">Email</div>
          <div id="profile-email" class="profile-modal-value"></div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Employee ID</div>
          <div id="profile-employee-id" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Department</div>
          <div id="profile-department" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Program</div>
          <div id="profile-program" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Personal Info</div>
          <div id="profile-personal" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Contact</div>
          <div id="profile-contact" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Employment</div>
          <div id="profile-employment" class="profile-modal-value">Not applicable</div>
        </div>

        <div class="profile-modal-field">
          <div class="profile-modal-label">Address</div>
          <div id="profile-address" class="profile-modal-value">Not provided</div>
        </div>

        <div class="profile-modal-field profile-modal-field-wide">
          <div class="profile-modal-label">About</div>
          <div id="profile-about" class="profile-modal-value">
            No biography provided.
          </div>
        </div>

        <div class="profile-modal-field profile-modal-field-wide">
          <div class="profile-modal-label">Office Location</div>
          <div id="profile-office" class="profile-modal-value">
            No room assigned
          </div>
        </div>

      </div>

      <div class="profile-modal-actions">
        <button type="button"
          onclick="closeModal('modal-user-profile')"
          class="btn btn-secondary">
          Close
        </button>

        <button type="button"
          onclick="editProfileUser()"
          class="btn btn-primary">
          Edit Profile
        </button>
      </div>

    </div>
  </div>


  {{-- TOAST --}}
  <div class="toast" id="toast">
    <span id="toast-msg"></span>
  </div>

  @if(session('error') || session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      showToast(@json(session('error') ?? session('success')));
    });
  </script>
  @endif


  <script>
    const AVATAR_COLORS = ['#2563eb', '#16a34a', '#d97706', '#0891b2', '#7c3aed'];

    const ROLE_LABELS = {
      faculty: 'Faculty',
      department_chair: 'Dept. Chair',
      dean: 'Dean',
      system_admin: 'System Admin'
    };

    const EDIT_URL_TEMPLATE = @json(route('admin.users.edit', ['id' => '__ID__']));
    const UPDATE_URL_TEMPLATE = @json(url('admin/users').
      '/__ID__');

    let profileUserId = null;

    function updateAdminSpecialPositionCap(which) {
      const select = document.getElementById(`${which}-special-position`);
      const note = document.getElementById(`${which}-special-position-cap`);
      if (!select || !note) return;
      const maxHours = select.selectedOptions[0]?.dataset.maxHours;
      note.textContent = maxHours
        ? `Fixed teaching-load limit: ${maxHours} hours per week.`
        : 'No special-position limit.';
    }


    function openModal(id) {
      document.getElementById(id)?.classList.add('active');
    }


    function closeModal(id) {
      document.getElementById(id)?.classList.remove('active');
    }


    function updateCollegeDepartment(form, preferredDepartmentId) {
      const college = form?.querySelector('select[name="college_id"]');
      const department = form?.querySelector('[data-department-select]');
      if (!college || !department) return;

      if (!department._allDepartmentOptions) {
        department._allDepartmentOptions = [...department.options]
          .filter(option => option.value)
          .map(option => option.cloneNode(true));
      }

      const selectedId = preferredDepartmentId === undefined
        ? department.value
        : String(preferredDepartmentId || '');
      const matching = department._allDepartmentOptions
        .filter(option => option.dataset.college === college.value);
      const placeholder = document.createElement('option');
      placeholder.value = '';
      placeholder.textContent = !college.value
        ? 'Select a college first'
        : matching.length
          ? 'Select department'
          : 'No departments available for this college';
      placeholder.selected = true;

      department.replaceChildren(placeholder, ...matching.map(option => option.cloneNode(true)));
      if (selectedId && matching.some(option => option.value === selectedId)) {
        department.value = selectedId;
      }

      const roleField = department.closest('.role-field');
      const roleIsVisible = !roleField || roleField.style.display !== 'none';
      department.disabled = !roleIsVisible || !college.value || matching.length === 0;
    }


    function setupCollegeDepartmentSelectors() {
      ['form-add-user', 'form-edit-user'].forEach(formId => {
        const form = document.getElementById(formId);
        const college = form?.querySelector('select[name="college_id"]');
        if (!form || !college) return;

        college.addEventListener('change', () => updateCollegeDepartment(form, ''));
        updateCollegeDepartment(form);
      });
    }


    document.querySelectorAll('.modal-overlay').forEach(overlay => {
      overlay.addEventListener('click', e => {
        if (e.target === overlay) overlay.classList.remove('active');
      });
    });


    function simpleHash(str) {
      let hash = 0;

      for (let i = 0; i < str.length; i++) {
        hash = (hash * 31 + str.charCodeAt(i)) >>> 0;
      }

      return hash;
    }


    function getInitials(name) {
      return (name || '?')
        .trim()
        .split(/\s+/)
        .map(part => part.charAt(0))
        .join('')
        .slice(0, 2)
        .toUpperCase();
    }


    function toggleRoleFields(selectEl) {
      const role = selectEl.value;
      const form = selectEl.closest('form');

      if (!form) return;

      form.querySelectorAll('.role-field').forEach(field => {
        const roles = (field.dataset.roles || '')
          .split(',')
          .map(role => role.trim());

        const show = roles.includes(role);

        field.style.display = show ? '' : 'none';

        field.querySelectorAll('input, select, textarea').forEach(input => {
          input.disabled = !show;
        });
      });

      updateCollegeDepartment(form);
    }


    function openAddModal() {
      const form = document.getElementById('form-add-user');

      form.reset();
      updateAdminSpecialPositionCap('add');

      form.querySelectorAll('.role-field').forEach(field => {
        field.style.display = 'none';

        field.querySelectorAll('input, select, textarea').forEach(input => {
          input.disabled = true;
        });
      });

      updateCollegeDepartment(form, '');

      openModal('modal-add-user');
    }



    // ── Phone: country code + up to 10 local digits ───────────────────────
    function onPhoneLocalInput(el, which) {
      // digits only, max 10; drop a single leading 0 (0 → country code)
      let v = (el.value || '').replace(/\D/g, '');
      if (v.startsWith('0')) v = v.replace(/^0+/, '');
      if (v.length > 10) v = v.slice(0, 10);
      el.value = v;
      syncPhoneField(which);
    }

    function syncPhoneField(which) {
      const cc = document.getElementById(which + '-phone-cc');
      const local = document.getElementById(which + '-phone-local');
      const full = document.getElementById(which + '-phone-full');
      if (!cc || !local || !full) return;
      const digits = (local.value || '').replace(/\D/g, '').slice(0, 10);
      full.value = digits ? (cc.value + digits) : '';
    }

    function setPhoneFromStored(which, stored) {
      const ccEl = document.getElementById(which + '-phone-cc');
      const localEl = document.getElementById(which + '-phone-local');
      const fullEl = document.getElementById(which + '-phone-full');
      if (!ccEl || !localEl || !fullEl) return;

      stored = (stored || '').trim();
      let cc = '+63';
      let local = '';

      if (stored.startsWith('+')) {
        // Match longest known country code from the select options
        const codes = Array.from(ccEl.options).map(o => o.value).sort((a, b) => b.length - a.length);
        const match = codes.find(c => stored.startsWith(c));
        if (match) {
          cc = match;
          local = stored.slice(match.length).replace(/\D/g, '').slice(0, 10);
        } else {
          local = stored.replace(/\D/g, '').slice(0, 10);
        }
      } else {
        // Legacy local formats like 09123456789
        local = stored.replace(/\D/g, '');
        if (local.startsWith('0')) local = local.replace(/^0+/, '');
        local = local.slice(0, 10);
      }

      ccEl.value = cc;
      localEl.value = local;
      fullEl.value = local ? (cc + local) : '';
    }

    // Keep hidden full number in sync before submit
    document.addEventListener('DOMContentLoaded', function() {
      const addForm = document.getElementById('form-add-user');
      if (addForm) {
        addForm.addEventListener('submit', function() {
          syncPhoneField('add');
        });
      }
      const editForm = document.getElementById('form-edit-user');
      if (editForm) {
        editForm.addEventListener('submit', function() {
          syncPhoneField('edit');
        });
      }
    });

    async function openEditModal(userId) {
      try {
        const url = EDIT_URL_TEMPLATE.replace('__ID__', encodeURIComponent(userId));
        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) {
          showToast('Could not load user information.');
          return;
        }

        const data = await response.json();

        document.getElementById('edit-email').value = data.usr_email || '';
        document.getElementById('edit-role').value = data.usr_role || 'faculty';
        document.getElementById('edit-status').value = data.usr_is_active ? '1' : '0';

        document.getElementById('edit-employee-id').value =
          data.usr_employee_id || '';

        document.getElementById('edit-first-name').value =
          data.usr_first_name || '';

        document.getElementById('edit-middle-name').value =
          data.usr_middle_name || '';

        document.getElementById('edit-last-name').value =
          data.usr_last_name || '';

        document.getElementById('edit-suffix').value =
          data.usr_suffix || '';

        document.getElementById('edit-gender').value =
          data.usr_gender || '';

        document.getElementById('edit-civil-status').value =
          data.usr_civil_status || '';

        document.getElementById('edit-dob').value =
          data.usr_dob ? String(data.usr_dob).substring(0, 10) : '';

        document.getElementById('edit-nationality').value =
          data.usr_nationality || '';

        document.getElementById('edit-rank-title').value =
          data.usr_rank_title || '';

        document.getElementById('edit-employment').value =
          data.employment_type || 'full_time';

        document.getElementById('edit-special-position').value =
          data.special_position || '';
        updateAdminSpecialPositionCap('edit');

        setPhoneFromStored('edit', data.role_phone_number || '');

        document.getElementById('edit-address').value =
          data.role_address || '';

        document.getElementById('edit-college').value =
          data.college_id || '';

        document.getElementById('edit-department').value =
          data.dept_id || '';

        document.getElementById('edit-about').value =
          data.usr_bio || '';

        toggleRoleFields(document.getElementById('edit-role'));

        const fullName = [
          data.usr_first_name,
          data.usr_middle_name,
          data.usr_last_name,
          data.usr_suffix
        ].filter(Boolean).join(' ') || data.usr_name || '';

        const colorIndex =
          simpleHash(fullName) % AVATAR_COLORS.length;

        document.getElementById('edit-avatar').style.background =
          AVATAR_COLORS[colorIndex];

        const editAvatar = document.getElementById('edit-avatar');
        editAvatar.style.backgroundImage = data.profile_image_url ? `url("${data.profile_image_url}")` : '';
        editAvatar.style.backgroundSize = data.profile_image_url ? 'cover' : '';
        editAvatar.style.backgroundPosition = data.profile_image_url ? 'center' : '';
        editAvatar.textContent = data.profile_image_url ? '' : getInitials(fullName);

        document.getElementById('edit-avatar-name').textContent =
          fullName;

        document.getElementById('edit-avatar-role').textContent =
          ROLE_LABELS[data.usr_role] || data.usr_role || '';

        const form = document.getElementById('form-edit-user');

        form.action = UPDATE_URL_TEMPLATE.replace('__ID__', userId);
        form.dataset.userId = userId;

        openModal('modal-edit-user');

      } catch (error) {
        console.error(error);
        showToast('Something went wrong while loading the user.');
      }
    }


    async function openProfileModal(userId) {
      try {
        const url = EDIT_URL_TEMPLATE.replace('__ID__', encodeURIComponent(userId));

        const response = await fetch(url, {
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        if (!response.ok) {
          showToast('Could not load profile.');
          return;
        }

        const data = await response.json();

        profileUserId = userId;

        const fullName = [
          data.usr_first_name,
          data.usr_middle_name,
          data.usr_last_name,
          data.usr_suffix
        ].filter(Boolean).join(' ') || data.usr_name || 'Unknown User';

        const colorIndex =
          simpleHash(fullName) % AVATAR_COLORS.length;

        const profileAvatar = document.getElementById('profile-avatar');
        profileAvatar.style.background = AVATAR_COLORS[colorIndex];
        profileAvatar.style.backgroundImage = data.profile_image_url ? `url("${data.profile_image_url}")` : '';
        profileAvatar.style.backgroundSize = data.profile_image_url ? 'cover' : '';
        profileAvatar.style.backgroundPosition = data.profile_image_url ? 'center' : '';
        profileAvatar.textContent = data.profile_image_url ? '' : getInitials(fullName);

        document.getElementById('profile-name').textContent =
          fullName;

        document.getElementById('profile-role').textContent =
          ROLE_LABELS[data.usr_role] || data.usr_role || '';

        const status = document.getElementById('profile-status');

        status.textContent =
          data.usr_is_active ? 'Active' : 'Inactive';

        status.className =
          `profile-modal-status ${
        data.usr_is_active ? 'is-active' : 'is-inactive'
      }`;

        document.getElementById('profile-email').textContent =
          data.usr_email || 'Not provided';

        document.getElementById('profile-employee-id').textContent =
          data.usr_employee_id || 'Not provided';

        document.getElementById('profile-department').textContent =
          data.college_id ? getCollegeName(data.college_id) : 'Not provided';

        document.getElementById('profile-program').textContent =
          data.dept_id ? getDepartmentName(data.dept_id) : 'Not provided';

        const personal = [
          data.usr_gender,
          data.usr_civil_status,
          data.usr_dob ?
          formatDate(data.usr_dob) :
          null,
          data.usr_nationality
        ].filter(Boolean);

        document.getElementById('profile-personal').textContent =
          personal.length ? personal.join(' · ') : 'Not provided';

        const contact = [
          data.role_phone_number,
          data.usr_email
        ].filter(Boolean);

        document.getElementById('profile-contact').textContent =
          contact.length ? contact.join(' · ') : 'Not provided';

        document.getElementById('profile-address').textContent =
          data.role_address || 'Not provided';

        document.getElementById('profile-employment').textContent =
          data.usr_role === 'faculty' ?
          formatEmployment(data.employment_type) :
          'Not applicable';

        document.getElementById('profile-about').textContent =
          data.usr_bio || 'No biography provided.';

        const row = document.querySelector(
          `tr[data-user-id="${CSS.escape(String(userId))}"]`
        );

        document.getElementById('profile-office').textContent =
          row?.dataset.roomLocation || 'No room assigned';

        openModal('modal-user-profile');

      } catch (error) {
        console.error(error);
        showToast('Something went wrong while loading the profile.');
      }
    }


    function getCollegeName(id) {
      const option = document.querySelector(
        `#edit-college option[value="${CSS.escape(String(id))}"]`
      );
      return option ? option.textContent.trim() : 'Not provided';
    }

    function getDepartmentName(id) {
      const departmentSelect = document.getElementById('edit-department');
      const options = departmentSelect?._allDepartmentOptions || [...(departmentSelect?.options || [])];
      const option = options.find(item => item.value === String(id));
      return option ? option.textContent.trim() : 'Not provided';
    }


    function formatDate(date) {
      const d = new Date(date);

      if (Number.isNaN(d.getTime())) return date;

      return d.toLocaleDateString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric'
      });
    }


    function formatEmployment(value) {
      if (value === 'full_time') return 'Full-time';
      if (value === 'part_time') return 'Part-time';

      return value || 'Not provided';
    }


    function editProfileUser() {
      if (!profileUserId) return;

      closeModal('modal-user-profile');
      openEditModal(profileUserId);
    }


    function confirmDeleteFromEdit() {
      const form = document.getElementById('form-edit-user');
      const userId = form.dataset.userId;

      if (!userId) return;

      if (!confirm('Delete this user? This cannot be undone.')) {
        return;
      }

      const deleteForm = document.createElement('form');

      deleteForm.method = 'POST';
      deleteForm.action = "{{ url('admin/users') }}/" + userId;

      const csrf = document.createElement('input');
      csrf.type = 'hidden';
      csrf.name = '_token';
      csrf.value = "{{ csrf_token() }}";

      const method = document.createElement('input');
      method.type = 'hidden';
      method.name = '_method';
      method.value = 'DELETE';

      deleteForm.appendChild(csrf);
      deleteForm.appendChild(method);

      document.body.appendChild(deleteForm);
      deleteForm.submit();
    }


    function showToast(msg) {
      const toast = document.getElementById('toast');
      const toastMsg = document.getElementById('toast-msg');

      if (!toast || !toastMsg) return;

      const message = String(msg || '');

      toastMsg.textContent =
        message.replace(/^✅\s?|^❌\s?/, '');

      toast.classList.add('show');

      setTimeout(() => {
        toast.classList.remove('show');
      }, 3000);
    }


    function toggleNotifDropdown() {
      const dd = document.getElementById('notif-dropdown');

      if (!dd) return;

      dd.style.display =
        dd.style.display === 'block' ? 'none' : 'block';
    }


    function markAllRead() {
      const count = document.getElementById('notif-count');

      if (count) count.style.display = 'none';
    }

    // ── Client-side pagination (10 per page) ──
    const USERS_PER_PAGE = 10;
    let currentPage = 1;
    let filterQuery = '';

    function getUserRows() {
      return Array.from(document.querySelectorAll('#users-table tbody tr.user-row'));
    }

    function rowMatchesFilter(row, q) {
      if (!q) return true;
      const name = (row.dataset.name || '').toLowerCase();
      const firstName = (row.dataset.firstName || '').toLowerCase();
      const lastName = (row.dataset.lastName || '').toLowerCase();
      const email = (row.dataset.email || '').toLowerCase();
      const role = (row.dataset.role || '').toLowerCase();
      const specialPosition = (row.dataset.specialPosition || '').toLowerCase();
      return name.includes(q) || firstName.includes(q) || lastName.includes(q)
        || email.includes(q) || role.includes(q) || specialPosition.includes(q);
    }

    function getFilteredRows() {
      return getUserRows().filter(row => rowMatchesFilter(row, filterQuery));
    }

    function renderUserPage() {
      const rows = getUserRows();
      const filtered = getFilteredRows();
      const total = filtered.length;
      const totalPages = Math.max(1, Math.ceil(total / USERS_PER_PAGE));
      if (currentPage > totalPages) currentPage = totalPages;
      if (currentPage < 1) currentPage = 1;

      const start = (currentPage - 1) * USERS_PER_PAGE;
      const end = start + USERS_PER_PAGE;

      rows.forEach(row => { row.style.display = 'none'; });
      filtered.forEach((row, i) => {
        row.style.display = (i >= start && i < end) ? '' : 'none';
      });

      const from = total === 0 ? 0 : start + 1;
      const to = Math.min(end, total);
      const info = document.getElementById('page-info');
      if (info) {
        info.textContent = total === 0
          ? 'No users found'
          : 'Showing ' + from + '–' + to + ' of ' + total + ' users (page ' + currentPage + ' of ' + totalPages + ')';
      }

      const btnPrev = document.getElementById('btn-prev');
      const btnNext = document.getElementById('btn-next');
      if (btnPrev) {
        btnPrev.disabled = currentPage <= 1;
        btnPrev.classList.toggle('opacity-40', currentPage <= 1);
        btnPrev.classList.toggle('pointer-events-none', currentPage <= 1);
      }
      if (btnNext) {
        btnNext.disabled = currentPage >= totalPages;
        btnNext.classList.toggle('opacity-40', currentPage >= totalPages);
        btnNext.classList.toggle('pointer-events-none', currentPage >= totalPages);
      }

      const nums = document.getElementById('page-numbers');
      if (nums) {
        nums.innerHTML = '';
        const fromP = Math.max(1, currentPage - 2);
        const toP = Math.min(totalPages, currentPage + 2);
        for (let p = fromP; p <= toP; p++) {
          const b = document.createElement('button');
          b.type = 'button';
          b.textContent = String(p);
          b.className = 'btn min-w-[36px] text-center ' + (p === currentPage ? 'btn-primary' : 'btn-secondary');
          b.addEventListener('click', function () { currentPage = p; renderUserPage(); });
          nums.appendChild(b);
        }
      }
    }

    function filterUsers(query) {
      filterQuery = (query || '').trim().toLowerCase();
      currentPage = 1;
      renderUserPage();
    }

    function changePage(direction) {
      currentPage += direction;
      renderUserPage();
    }

    document.addEventListener('DOMContentLoaded', () => {
      setupCollegeDepartmentSelectors();

      const addRole = document.querySelector(
        '#form-add-user select[name="usr_role"]'
      );
      if (addRole) toggleRoleFields(addRole);

      const editRole = document.getElementById('edit-role');
      if (editRole) toggleRoleFields(editRole);

      renderUserPage();
    });
  </script>

</body>

</html>
