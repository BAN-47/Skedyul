<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>SKEDYUL — {{ __('settings.settings') }}</title>
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
          <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide px-5 pb-1">{{ __('settings.settings') }}</div>
          <div class="settings-nav-item active" onclick="showSettingsSection('profile',this)">{{ __('settings.nav_personal_info') }}</div>
          <div class="settings-nav-item" onclick="showSettingsSection('general',this)">{{ __('settings.nav_general') }}</div>
          <div class="settings-nav-item" onclick="showSettingsSection('academic',this)">{{ __('settings.nav_academic') }}</div>
          <div class="settings-nav-item" onclick="showSettingsSection('notifications',this)">{{ __('settings.nav_notifications') }}</div>
          <div class="settings-nav-item" onclick="showSettingsSection('security',this)">{{ __('settings.nav_security') }}</div>
          <div class="settings-nav-item" onclick="showSettingsSection('system',this)">{{ __('settings.nav_system') }}</div>
        </div>

        {{-- Right content --}}
        <div id="settings-content">

          {{-- PERSONAL INFO --}}
          <div id="settings-profile">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.profile_picture') }}</div><div class="card-sub">{{ __('settings.profile_picture_sub') }}</div></div></div>
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
                    <button class="btn btn-primary text-xs px-3.5 py-1.5" onclick="document.getElementById('pic-upload').click()">{{ __('settings.upload_photo') }}</button>
                    <button class="btn btn-secondary text-xs px-3.5 py-1.5" onclick="resetProfilePic()">{{ __('settings.remove') }}</button>
                  </div>
                </div>
              </div>
            </div>
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.personal_info') }}</div><div class="card-sub">{{ __('settings.personal_info_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">{{ __('settings.first_name') }}</label><input class="field-input" id="pi-firstname" value="{{ auth()->user()->usr_first_name }}"></div>
              <div><label class="field-label">{{ __('settings.last_name') }}</label><input class="field-input" id="pi-lastname" value="{{ auth()->user()->usr_last_name }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">{{ __('settings.middle_name') }}</label><input class="field-input" id="pi-middlename" placeholder="{{ __('settings.optional') }}" value="{{ auth()->user()->usr_middle_name }}"></div>
              <div><label class="field-label">{{ __('settings.suffix') }}</label>
                <select class="field-input" id="pi-suffix">
                  <option value="" {{ !auth()->user()->usr_suffix ? 'selected' : '' }}>{{ __('settings.none') }}</option>
                  <option {{ auth()->user()->usr_suffix === 'Jr.' ? 'selected' : '' }}>Jr.</option>
                  <option {{ auth()->user()->usr_suffix === 'Sr.' ? 'selected' : '' }}>Sr.</option>
                  <option {{ auth()->user()->usr_suffix === 'II' ? 'selected' : '' }}>II</option>
                  <option {{ auth()->user()->usr_suffix === 'III' ? 'selected' : '' }}>III</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">{{ __('settings.rank_title') }}</label><input class="field-input" id="rank-title" value="{{ auth()->user()->usr_rank_title }}"></div>
              <div><label class="field-label">{{ __('settings.employee_id') }}</label><input class="field-input" id="pi-empid" value="{{ auth()->user()->usr_employee_id }}"></div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-3">
              <div><label class="field-label">{{ __('settings.gender') }}</label>
                <select class="field-input" id="pi-gender">
                  <option {{ auth()->user()->usr_gender === 'Male' ? 'selected' : '' }}>{{ __('settings.male') }}</option>
                  <option {{ auth()->user()->usr_gender === 'Female' ? 'selected' : '' }}>{{ __('settings.female') }}</option>
                  <option {{ auth()->user()->usr_gender === 'Prefer not to say' ? 'selected' : '' }}>{{ __('settings.prefer_not_to_say') }}</option>
                </select>
              </div>
              <div><label class="field-label">{{ __('settings.civil_status') }}</label>
                <select class="field-input" id="pi-civil">
                  <option {{ auth()->user()->usr_civil_status === 'Single' ? 'selected' : '' }}>{{ __('settings.single') }}</option>
                  <option {{ auth()->user()->usr_civil_status === 'Married' ? 'selected' : '' }}>{{ __('settings.married') }}</option>
                  <option {{ auth()->user()->usr_civil_status === 'Widowed' ? 'selected' : '' }}>{{ __('settings.widowed') }}</option>
                  <option {{ auth()->user()->usr_civil_status === 'Separated' ? 'selected' : '' }}>{{ __('settings.separated') }}</option>
                </select>
              </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mb-1">
              <div><label class="field-label">{{ __('settings.date_of_birth') }}</label><input class="field-input" id="pi-dob" type="date" value="{{ auth()->user()->usr_dob }}"></div>
              <div><label class="field-label">{{ __('settings.nationality') }}</label><input class="field-input" id="pi-nationality" value="{{ auth()->user()->usr_nationality }}"></div>
            </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="savePersonalInfo()">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.contact_office') }}</div><div class="card-sub">{{ __('settings.contact_office_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.email_address') }}</label><input class="field-input" id="pi-email" type="email" value="admin@ctu.edu.ph"></div>
                <div><label class="field-label">{{ __('settings.phone_number') }}</label><input class="field-input" id="pi-phone" value="(032) 401-0000"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.office_location') }}</label><input class="field-input" id="pi-office" value="ICT Building, Room 100"></div>
                <div><label class="field-label">{{ __('settings.department') }}</label><input class="field-input bg-slate-100 cursor-not-allowed" id="pi-dept" value="CCICT" readonly></div>
              </div>
              <div class="mb-4"><label class="field-label">{{ __('settings.bio_about') }}</label><textarea class="field-input resize-y" id="pi-bio" rows="3" placeholder="{{ __('settings.bio_placeholder') }}">Technical Administrator of the CCICT, Cebu Technological University.</textarea></div>
              <div class="flex justify-end">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_contact_saved') }}')">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
          </div>

          {{-- GENERAL --}}
          <div id="settings-general" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.institution_details') }}</div><div class="card-sub">{{ __('settings.institution_details_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.institution_name') }}</label><input class="field-input" value="Cebu Technological University"></div>
                <div><label class="field-label">{{ __('settings.campus') }}</label><input class="field-input" value="Main Campus"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.college_unit') }}</label><input class="field-input" value="College of Computing, Information and Communications Technology"></div>
                <div><label class="field-label">{{ __('settings.abbreviation') }}</label><input class="field-input" value="CCICT"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">{{ __('settings.contact_email') }}</label><input class="field-input" type="email" value="ccict@ctu.edu.ph"></div>
                <div><label class="field-label">{{ __('settings.phone') }}</label><input class="field-input" value="(032) 401-7777"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_institution_saved') }}')">{{ __('settings.save_changes') }}</button>
              </div>
            </div>

            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.appearance') }}</div><div class="card-sub">{{ __('settings.appearance_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div>
                  <label class="field-label">{{ __('settings.theme') }}</label>
                  <select class="field-input" id="theme-select" onchange="applyTheme(this.value)">
                    <option value="light" selected>{{ __('settings.light') }}</option>
                    <option value="dark">{{ __('settings.dark') }}</option>
                  </select>
                </div>
                <div>
                  <label class="field-label">{{ __('settings.language') }}</label>
                  <select name="language" class="field-input" id="pi-language">
                      @php
                          $languages = ['en' => 'English', 'fil' => 'Filipino', 'ceb' => 'Cebuano'];
                          $selectedLang = auth()->user()->language ?? 'en';
                      @endphp
                      @foreach ($languages as $value => $label)
                          <option value="{{ $value }}" @selected($selectedLang === $value)>
                              {{ $label }}
                          </option>
                      @endforeach
                  </select>
              </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">{{ __('settings.date_format') }}</label>
                  <select class="field-input" id="pi-dateformat">
                    <option>MM/DD/YYYY</option>
                    <option selected>DD/MM/YYYY</option>
                    <option>YYYY-MM-DD</option>
                  </select>
                </div>
                <div>
                  <label class="field-label">{{ __('settings.time_format') }}</label>
                  <select class="field-input" id="pi-timeformat">
                    <option selected>{{ __('settings.hour_12') }}</option>
                    <option>{{ __('settings.hour_24') }}</option>
                  </select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="saveAppearance()">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
          </div>

          {{-- ACADEMIC YEAR --}}
          <div id="settings-academic" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.current_academic_year') }}</div><div class="card-sub">{{ __('settings.current_academic_year_sub') }}</div></div><span class="badge badge-green">{{ __('settings.active') }}</span></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.academic_year') }}</label><input class="field-input" value="2025–2026"></div>
                <div>
                  <label class="field-label">{{ __('settings.semester') }}</label>
                  <select class="field-input"><option selected>{{ __('settings.sem_1') }}</option><option>{{ __('settings.sem_2') }}</option><option>{{ __('settings.summer') }}</option></select>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">{{ __('settings.start_date') }}</label><input class="field-input" type="date" value="2025-08-11"></div>
                <div><label class="field-label">{{ __('settings.end_date') }}</label><input class="field-input" type="date" value="2025-12-20"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_academic_saved') }}')">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.schedule_constraints') }}</div><div class="card-sub">{{ __('settings.schedule_constraints_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.max_faculty_load') }}</label><input class="field-input" type="number" value="30"></div>
                <div><label class="field-label">{{ __('settings.min_faculty_load') }}</label><input class="field-input" type="number" value="12"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-3">
                <div><label class="field-label">{{ __('settings.class_start_time') }}</label><input class="field-input" type="time" value="07:00"></div>
                <div><label class="field-label">{{ __('settings.class_end_time') }}</label><input class="field-input" type="time" value="21:00"></div>
              </div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">{{ __('settings.school_days') }}</label>
                  <select class="field-input"><option selected>{{ __('settings.mon_sat') }}</option><option>{{ __('settings.mon_fri') }}</option></select>
                </div>
                <div>
                  <label class="field-label">{{ __('settings.conflict_detection') }}</label>
                  <select class="field-input"><option selected>{{ __('settings.conflict_strict') }}</option><option>{{ __('settings.conflict_warn') }}</option><option>{{ __('settings.conflict_disabled') }}</option></select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_constraints_saved') }}')">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
          </div>

          {{-- NOTIFICATIONS --}}
          <div id="settings-notifications" style="display:none;">
            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.notification_prefs') }}</div><div class="card-sub">{{ __('settings.notification_prefs_sub') }}</div></div></div>
              <div class="flex flex-col gap-4 mt-1">
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_schedule_conflicts') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_schedule_conflicts_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_new_user') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_new_user_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_faculty_overload') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_faculty_overload_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_room_double_booking') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_room_double_booking_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" checked onchange="toggleSwitch(this)"><span class="toggle-track on"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_backups') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_backups_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
                <div class="flex items-center justify-between p-3.5 bg-slate-50 rounded-lg">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.notif_login_activity') }}</div><div class="text-xs text-slate-400 mt-0.5">{{ __('settings.notif_login_activity_sub') }}</div></div>
                  <label class="toggle-switch"><input type="checkbox" onchange="toggleSwitch(this)"><span class="toggle-track"><span class="toggle-thumb"></span></span></label>
                </div>
              </div>
              <div class="flex justify-end mt-4">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_notifications_saved') }}')">{{ __('settings.save_preferences') }}</button>
              </div>
            </div>
          </div>

          {{-- SECURITY --}}
          <div id="settings-security" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.change_password') }}</div><div class="card-sub">{{ __('settings.change_password_sub') }}</div></div></div>
              <div class="mb-3.5"><label class="field-label">{{ __('settings.current_password') }}</label><input class="field-input" type="password" placeholder="••••••••"></div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div><label class="field-label">{{ __('settings.new_password') }}</label><input class="field-input" type="password" placeholder="{{ __('settings.min_8_chars') }}"></div>
                <div><label class="field-label">{{ __('settings.confirm_new_password') }}</label><input class="field-input" type="password" placeholder="{{ __('settings.re_enter_password') }}"></div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_password_updated') }}')">{{ __('settings.update_password') }}</button>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.session_access') }}</div><div class="card-sub">{{ __('settings.session_access_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3 mb-1">
                <div>
                  <label class="field-label">{{ __('settings.session_timeout') }}</label>
                  <select class="field-input"><option>{{ __('settings.minutes_15') }}</option><option selected>{{ __('settings.minutes_30') }}</option><option>{{ __('settings.hour_1') }}</option><option>{{ __('settings.never') }}</option></select>
                </div>
                <div>
                  <label class="field-label">{{ __('settings.max_login_attempts') }}</label>
                  <select class="field-input"><option>3</option><option selected>5</option><option>10</option></select>
                </div>
              </div>
              <div class="flex justify-end mt-1">
                <button class="btn btn-primary" onclick="showToast('{{ __('settings.toast_security_saved') }}')">{{ __('settings.save_changes') }}</button>
              </div>
            </div>
          </div>

          {{-- SYSTEM INFO --}}
          <div id="settings-system" style="display:none;">
            <div class="card mb-4">
              <div class="card-header"><div><div class="card-title">{{ __('settings.system_info') }}</div><div class="card-sub">{{ __('settings.system_info_sub') }}</div></div></div>
              <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.application') }}</div><div class="text-[13px] font-semibold text-slate-900">SKEDYUL v1.0.0</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.host') }}</div><div class="text-[13px] font-semibold text-slate-900">Vercel (Production)</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.database') }}</div><div class="text-[13px] font-semibold text-slate-900">Supabase PostgreSQL</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.auth') }}</div><div class="text-[13px] font-semibold text-slate-900">JWT + Laravel Sanctum</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.frontend') }}</div><div class="text-[13px] font-semibold text-slate-900">Tailwind CSS + Vanilla JS</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.mobile_app') }}</div><div class="text-[13px] font-semibold text-slate-900">React Native</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.last_backup') }}</div><div class="text-[13px] font-semibold text-green-600">Today, 06:00 AM ✓</div></div>
                <div class="bg-slate-50 rounded-lg p-3.5"><div class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mb-1">{{ __('settings.uptime') }}</div><div class="text-[13px] font-semibold text-slate-900">99.98% (last 30 days)</div></div>
              </div>
            </div>
            <div class="card">
              <div class="card-header"><div><div class="card-title">{{ __('settings.danger_zone') }}</div><div class="card-sub">{{ __('settings.danger_zone_sub') }}</div></div></div>
              <div class="flex flex-col gap-2.5">
                <div class="flex items-center justify-between p-3.5 border border-red-100 rounded-lg bg-red-50">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.clear_all_schedules') }}</div><div class="text-xs text-slate-400">{{ __('settings.clear_all_schedules_sub') }}</div></div>
                  <button class="btn btn-danger text-xs px-3.5 py-1.5" onclick="showToast('{{ __('settings.toast_action_cancelled') }}')">{{ __('settings.clear') }}</button>
                </div>
                <div class="flex items-center justify-between p-3.5 border border-red-100 rounded-lg bg-red-50">
                  <div><div class="text-[13px] font-semibold text-slate-900">{{ __('settings.reset_system_data') }}</div><div class="text-xs text-slate-400">{{ __('settings.reset_system_data_sub') }}</div></div>
                  <button class="btn btn-danger text-xs px-3.5 py-1.5" onclick="showToast('{{ __('settings.toast_action_cancelled') }}')">{{ __('settings.reset') }}</button>
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
  document.body.classList.toggle('dark', theme === 'dark');
  localStorage.setItem('skedyul-theme', theme);
}

document.addEventListener('DOMContentLoaded', () => {
  const saved = localStorage.getItem('skedyul-theme') || 'light';
  const themeSelect = document.getElementById('theme-select');
  if (themeSelect) themeSelect.value = saved;
  applyTheme(saved);
});

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
        showToast('{{ __('settings.toast_upload_failed') }}');
        return;
      }
      preview.innerHTML = `<img src="${data.url}" style="width:100%;height:100%;object-fit:cover;">`;
      showToast('{{ __('settings.toast_profile_saved') }}');
    })
    .catch(() => showToast('{{ __('settings.toast_generic_error') }}'));
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
      showToast('{{ __('settings.toast_photo_removed') }}');
    })
    .catch(() => showToast('{{ __('settings.toast_generic_error') }}'));
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
        showToast('{{ __('settings.toast_personal_failed') }}');
        return;
      }
      const sbName = document.getElementById('sb-name');
      const sbRole = document.getElementById('sb-role');
      if (sbName) sbName.textContent = data.usr_name;
      if (sbRole) sbRole.textContent = document.getElementById('rank-title').value;
      showToast('{{ __('settings.toast_personal_saved') }}');
    })
    .catch(() => showToast('{{ __('settings.toast_generic_error') }}'));
}

// ── APPEARANCE (theme / language / date & time format) ──────────────────────────
function saveAppearance() {
  const payload = {
    language: document.getElementById('pi-language').value,
    date_format: document.getElementById('pi-dateformat').value,
    time_format: document.getElementById('pi-timeformat').value,
  };

  fetch('{{ route("admin.profile.appearance.update") }}', {
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
        showToast('{{ __('settings.toast_appearance_failed') }}');
        return;
      }
      showToast('{{ __('settings.toast_appearance_saved') }}');
      // Reload so the SetLocale middleware re-applies the new language
      // and every __() string on the page re-renders translated.
      setTimeout(() => window.location.reload(), 600);
    })
    .catch(() => showToast('{{ __('settings.toast_generic_error') }}'));
}

// ── NOTIFICATION TOGGLE SWITCHES ────────────────────────────────────────────────
function toggleSwitch(input) {
  const track = input.nextElementSibling;
  if (input.checked) { track.classList.add('on'); }
  else { track.classList.remove('on'); }
  showToast('{{ __('settings.toast_notification_updated') }}');
}
</script>
</body>
</html>