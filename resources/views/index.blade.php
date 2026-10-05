<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@25.14.1/build/css/intlTelInput.css">
  <script defer src="https://cdn.jsdelivr.net/npm/intl-tel-input@25.14.1/build/js/intlTelInput.min.js"></script>
</head>

<body class="{{ ($showRegister ?? false) ? 'min-h-screen overflow-y-auto' : 'h-screen overflow-hidden' }} font-sans"
  data-login-url="{{ route('login') }}"
  data-register-url="{{ route('register') }}">

  {{-- ═══════════════════════════════════════════════════
       WRAPPER — full screen, side by side
  ════════════════════════════════════════════════════ --}}
  <div id="screen-login" class="flex min-h-screen">

    {{-- ═══════════════════════════════════════════════
         LEFT PANEL
    ════════════════════════════════════════════════ --}}
    <div class="relative flex flex-1 flex-col justify-center overflow-hidden px-14 py-16"
      style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 60%, #1a2d5a 100%);">

      {{-- Background glow --}}
      <div class="pointer-events-none absolute inset-0"
        style="background: radial-gradient(ellipse at 20% 50%, rgba(37,99,235,.3) 0%, transparent 60%),
                              radial-gradient(ellipse at 80% 10%, rgba(8,145,178,.2) 0%, transparent 50%);">
      </div>

      {{-- Grid overlay --}}
      <div class="pointer-events-none absolute inset-0"
        style="background-image: linear-gradient(rgba(255,255,255,.03) 1px, transparent 1px),
                                    linear-gradient(90deg, rgba(255,255,255,.03) 1px, transparent 1px);
                  background-size: 40px 40px;">
      </div>

      {{-- Decorative circles --}}
      <div class="pointer-events-none absolute -top-20 -right-16 h-72 w-72 rounded-full border border-white/[.07] bg-white/[.04]"></div>
      <div class="pointer-events-none absolute bottom-16 right-20 h-44 w-44 rounded-full border border-white/[.07] bg-white/[.04]"></div>
      <div class="pointer-events-none absolute bottom-48 left-8 h-20 w-20 rounded-full border border-white/[.07] bg-white/[.04]"></div>

      {{-- Content --}}
      <div class="relative z-10 flex flex-col items-center text-center">

        {{-- Logo --}}
        <div class="mb-1 text-5xl font-extrabold tracking-tight text-white leading-none">
          SKED<span class="text-blue-400">YUL</span>
        </div>

        {{-- Tagline --}}
        <div class="mb-10 text-xs font-bold uppercase tracking-widest text-white/40">
          Faculty Scheduling System
        </div>

        {{-- Description --}}
        <p class="mb-11 max-w-sm text-center text-[17px] leading-relaxed text-white/70">
          A <b class="font-bold text-white">smart, centralized platform</b> built for CCICT faculty —
          effortlessly manage class schedules, plot subjects,
          detect conflicts, and balance workloads all in one place.
        </p>

        {{-- Feature list --}}
        <div class="flex w-full max-w-sm flex-col gap-3.5">

          <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg" style="background:#3C3489;">
              <i class="ti ti-calendar text-xl text-white"></i>
            </div>
            <div class="text-left">
              <div class="text-[13px] font-bold text-white">Automated Schedule Plotting</div>
              <div class="mt-0.5 text-[12px] text-white/40">Assign subjects to time slots with instant conflict detection</div>
            </div>
          </div>

          <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg" style="background:#3C3489;">
              <i class="ti ti-school text-xl text-white"></i>
            </div>
            <div class="text-left">
              <div class="text-[13px] font-bold text-white">Faculty Workload Monitoring</div>
              <div class="mt-0.5 text-[12px] text-white/40">Track teaching loads and prevent overloading in real time</div>
            </div>
          </div>

          <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg" style="background:#3C3489;">
              <i class="ti ti-building text-xl text-white"></i>
            </div>
            <div class="text-left">
              <div class="text-[13px] font-bold text-white">Room & Section Management</div>
              <div class="mt-0.5 text-[12px] text-white/40">Manage classrooms and sections across all programs</div>
            </div>
          </div>

          <div class="flex items-center gap-3.5">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg" style="background:#3C3489;">
              <i class="ti ti-chart-bar text-xl text-white"></i>
            </div>
            <div class="text-left">
              <div class="text-[13px] font-bold text-white">Reports & Export</div>
              <div class="mt-0.5 text-[12px] text-white/40">Generate and download schedule reports in PDF or Excel</div>
            </div>
          </div>

        </div>

        {{-- Department badge --}}
        <div class="mt-12 w-full max-w-sm rounded-2xl border border-white/10 bg-white/[.06] px-5 py-3.5 text-center">
          <div class="text-[12px] font-extrabold leading-snug text-white">
            College of Computing, Information and<br>Communications Technology
          </div>
          <div class="mt-1 text-[10px] text-white/30">
            Cebu Technological University · Main Campus
          </div>
        </div>

      </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         RIGHT PANEL — login form
    ════════════════════════════════════════════════ --}}
    <div id="auth-panel" class="flex {{ ($showRegister ?? false) ? 'w-[760px]' : 'w-[600px]' }} shrink-0 items-center justify-center bg-slate-50 px-10 py-12 transition-[width] duration-200">

      {{-- Card --}}
      <div id="login-view" class="w-full animate-[fadeUp_.6s_ease]" @if(($showRegister ?? false) || session('registration_submitted')) hidden @endif>

        {{-- Logo text --}}
        <div class="mb-4 text-5xl font-extrabold tracking-tight text-gray-400 leading-none">
          LOG<span class="text-blue-400">IN</span>
        </div>

        <div class="mb-1.5 text-[26px] font-extrabold tracking-tight text-slate-900">
          Welcome back!
        </div>
        <div class="mb-9 text-sm text-slate-500">
          Sign in to your SKEDYUL account to continue.
        </div>

        {{-- ── FORM ── --}}
        <form method="POST" action="{{ route('login.authenticate') }}">
          @csrf

          {{-- Error --}}
          @if(session('error'))
          <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ session('error') }}
          </div>
          @endif

          {{-- Email --}}
          <div class="mb-4">
            <label class="mb-2 block text-[11px] font-bold uppercase tracking-[.8px] text-slate-500">
              Email Address
            </label>
            <input
              type="email"
              name="email"
              value="{{ old('email') }}"
              placeholder="Enter your email address"
              required
              class="w-full rounded-[10px] border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:border-blue-600 focus:ring-[3px] focus:ring-blue-600/10">
            @error('email')
            <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
            @enderror
          </div>

          {{-- Password --}}
          <div class="mb-8">
            <label class="mb-2 block text-[11px] font-bold uppercase tracking-[.8px] text-slate-500">
              Password
            </label>
            <input
              type="password"
              name="password"
              placeholder="Enter your password"
              required
              class="w-full rounded-[10px] border border-slate-200 bg-white px-3.5 py-3 text-sm text-slate-900 placeholder-slate-400 outline-none transition focus:border-blue-600 focus:ring-[3px] focus:ring-blue-600/10">
            @error('password')
            <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
            @enderror
          </div>

          {{-- Submit --}}
          <button
            type="submit"
            class="flex w-full items-center justify-center gap-2 rounded-[10px] bg-blue-600 py-3.5 text-[15px] font-bold text-white transition hover:-translate-y-px hover:bg-blue-700 hover:shadow-[0_8px_24px_rgba(37,99,235,.3)] active:translate-y-0">
            <i class="ti ti-login"></i>
            Sign In
          </button>

        </form>

        {{-- ═══════════════════════════════════════════════
         Register Form
    ════════════════════════════════════════════════ --}}
        <div class="mt-4 text-center text-sm text-slate-500">
          Don’t have an account?
          <a href="{{ route('register') }}" data-auth-view="register" class="font-bold text-blue-600 hover:text-blue-700">Create Account</a>
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
          CCICT · Cebu Technological University — Main Campus
        </div>

      </div>


        <!-- Register Submitted Show Message -->
      @if(session('registration_submitted'))
      <div id="registration-success" class="w-full max-w-md animate-[fadeUp_.6s_ease] text-center" role="status" aria-live="polite">
        <div class="mb-3 text-5xl font-extrabold tracking-tight text-gray-400 leading-none">
          THANK YOU <span class="text-blue-500">FOR REGISTERING!</span>
        </div>
        <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-2xl bg-blue-50 text-5xl" aria-hidden="true">🎉</div>
        <p class="mx-auto mb-2 max-w-sm text-sm font-semibold leading-relaxed text-slate-700">
          Your faculty account request and ID photo have been received for review.
        </p>
        <p class="mx-auto mb-6 max-w-sm text-sm leading-relaxed text-slate-500">
          An administrator will review your details and contact you about verification. 
          Thank you for your patience and using SKEDYUL as your daily tool for class scheduling.
        </p>
        <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-6 py-3 text-sm font-bold text-white transition hover:bg-blue-700">
          Back to Log In
        </a>
      </div>
      @endif

      <div id="register-view" class="w-full max-w-[680px] animate-[fadeUp_.6s_ease]" @unless($showRegister ?? false) hidden @endunless>
        <div class="mb-2 text-5xl font-extrabold tracking-tight text-gray-400 leading-none">REGIS<span class="text-blue-500">TER</span></div>
        <p class="mb-5 text-xs font-semibold leading-relaxed text-slate-600">Create a faculty account. Complete the same faculty information used in Admin User Accounts.</p>

        @if($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-xs text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" id="register-form" enctype="multipart/form-data">
          @csrf
          <div class="mb-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Account Information</div>
          <div class="grid grid-cols-2 gap-x-3 gap-y-2.5">
            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">First Name</label>
              <input name="usr_first_name" value="{{ old('usr_first_name') }}" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600" autocomplete="given-name">@error('usr_first_name')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Middle Name</label>
              <input name="usr_middle_name" value="{{ old('usr_middle_name') }}" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600" autocomplete="additional-name">
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Last Name</label>
              <input name="usr_last_name" value="{{ old('usr_last_name') }}" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600" autocomplete="family-name">@error('usr_last_name')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Employee ID No.</label>
              <input name="usr_employee_id" value="{{ old('usr_employee_id') }}" placeholder="e.g. 2026-00123" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600">@error('usr_employee_id')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="col-span-2">
              <div class="mb-1 flex items-center justify-between">
                <label class="text-[10px] font-bold uppercase text-slate-700">Email Address</label>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-indigo-700">Faculty Role</span>
              </div>
              <input type="email" name="usr_email" value="{{ old('usr_email') }}" required autocomplete="email" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600">@error('usr_email')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Password</label>
              <input id="register-password" type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600">@error('password')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Confirm Password</label>
              <input id="confirm-password" type="password" name="password_confirmation" required minlength="8" autocomplete="new-password" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600">
              <p id="password-match" class="mt-1 min-h-3 text-[10px]" aria-live="polite">
              </p>
            </div>

            <div class="col-span-2 mt-1 border-t border-slate-200 pt-2 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">Personal and Faculty Information</div>
            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Suffix</label>
              <input name="usr_suffix" value="{{ old('usr_suffix') }}" placeholder="Jr., Sr., III" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs outline-none focus:border-blue-600">
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Gender</label>
              <select name="usr_gender" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="">Select</option>@foreach(['Male','Female','Other'] as $value)<option value="{{ $value }}" @selected(old('usr_gender')===$value)>{{ $value }}</option>@endforeach
              </select>
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Civil Status</label>
              <select name="usr_civil_status" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="">Select</option>@foreach(['Single','Married','Widowed','Separated'] as $value)<option value="{{ $value }}" @selected(old('usr_civil_status')===$value)>{{ $value }}</option>@endforeach
              </select>
            </div>

            <div>
              <label for="register-dob" class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Date of Birth</label>
              <input id="register-dob" type="date" name="usr_dob" value="{{ old('usr_dob') }}" min="1950-01-01" max="{{ now()->subYears(6)->format('Y-m-d') }}" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
              @error('usr_dob')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Nationality</label>
              <input name="usr_nationality" value="{{ old('usr_nationality', 'Filipino') }}" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Rank / Title</label>
              <select name="usr_rank_title" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="">Select rank</option>@foreach(['Instructor I','Instructor II','Instructor III','Assistant Professor I','Assistant Professor II','Assistant Professor III','Assistant Professor IV','Associate Professor I','Associate Professor II','Associate Professor III','Associate Professor IV','Associate Professor V','Professor I','Professor II','Professor III','Professor IV','Professor V','Professor IV','College/University Professor'] as $value)<option value="{{ $value }}" @selected(old('usr_rank_title')===$value)>{{ $value }}</option>@endforeach
              </select>
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Employment Type</label>
              <select name="employment_type" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="full_time" @selected(old('employment_type','full_time')==='full_time' )>Full-time</option>
                <option value="part_time" @selected(old('employment_type')==='part_time' )>Part-time</option>
              </select>
            </div>

            <div class="col-span-2">
              <label for="faculty-id-photo" class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Faculty ID Photo (for verification)</label>
              <input
                id="faculty-id-photo"
                type="file"
                name="faculty_id_photo"
                accept="image/jpeg,image/png,image/webp"
                required
                class="block w-full cursor-pointer rounded-md border border-slate-200 bg-white text-xs text-slate-600 file:mr-3 file:cursor-pointer file:border-0 file:bg-indigo-600 file:px-4 file:py-3 file:text-xs file:font-bold file:text-white hover:file:bg-indigo-700">
              <p class="mt-1 text-[10px] text-slate-500">Upload a clear JPG, PNG, or WebP image. Maximum file size: 5 MB.</p>
              @error('faculty_id_photo')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="col-span-2">
              <div class="mb-1 flex items-center justify-between">
                <label for="register-phone" class="text-[10px] font-bold uppercase text-slate-700">Phone Number</label>
                <span class="rounded-full bg-indigo-50 px-2 py-0.5 text-[9px] font-extrabold uppercase tracking-wide text-indigo-700">Faculty Role</span>
              </div>
              <input id="register-phone" type="tel" inputmode="tel" autocomplete="tel" placeholder="Phone Number" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
              <input type="hidden" name="role_phone_number" id="register-phone-full" value="{{ old('role_phone_number') }}">
              @error('role_phone_number')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Address</label>
              <input name="role_address" value="{{ old('role_address') }}" placeholder="Cebu City" class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">College</label>
              <select id="register-college" name="college_id" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="">Select college</option>@foreach(($colleges ?? []) as $college)<option value="{{ $college->college_id }}" @selected(old('college_id')===$college->college_id)>{{ $college->college_name }} ({{ $college->college_code }})</option>@endforeach
              </select>@error('college_id')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
              <label class="mb-1 block text-[10px] font-bold uppercase text-slate-700">Department / Program</label>
              <select id="register-department" name="dept_id" required class="w-full rounded-md border border-slate-200 bg-white px-2.5 py-2 text-xs">
                <option value="">Select program</option>@foreach(($departments ?? []) as $department)<option value="{{ $department->dept_id }}" data-college="{{ $department->dept_college_id }}" @selected(old('dept_id')===$department->dept_id)>{{ $department->dept_name }} ({{ $department->dept_code }})</option>@endforeach
              </select>@error('dept_id')<p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="col-span-2 rounded-md bg-indigo-50 px-3 py-2 text-[10px] leading-relaxed text-indigo-800">Your account will remain pending until an administrator reviews your information and faculty ID photo.</div>
          </div>
          <button id="register-submit" type="submit" class="mx-auto mt-5 block rounded-lg bg-blue-600 px-7 py-3 text-xs font-bold text-white transition hover:bg-blue-700">Create Account</button>
        </form>

        <div class="mt-4 text-center text-xs text-slate-500">Already have an account? <a href="{{ route('login') }}" data-auth-view="login" class="font-bold text-blue-600 hover:text-blue-700">Sign In</a></div>
        <div class="mt-4 text-center text-[10px] text-slate-400">CCICT · Cebu Technological University — Main Campus</div>
      </div>
    </div>

  </div>

  {{-- Fade-up animation --}}
  <style>
    @keyframes fadeUp {
      from {
        opacity: 0;
        transform: translateY(16px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @media (max-width: 900px) {
      #auth-panel {
        width: min(100%, 760px);
        padding-left: 1.25rem;
        padding-right: 1.25rem;
      }

      #screen-login {
        flex-direction: column;
      }

      #screen-login>div:first-child {
        min-height: 210px;
        padding: 2rem;
      }

      #login-view,
      #register-view {
        max-width: 100%;
      }
    }

    @media (max-width: 520px) {
      #register-view .grid-cols-2 {
        grid-template-columns: minmax(0, 1fr);
      }

      #register-view .col-span-2 {
        grid-column: auto;
      }
    }

    #register-view .iti {
      width: 100%;
    }

    #register-view .iti input {
      width: 100%;
    }

    #register-view .iti__country-list {
      z-index: 70;
    }

    #register-view .register-phone-fallback {
      display: flex;
      width: 100%;
    }

    #register-view .register-phone-fallback select {
      max-width: 48%;
      border: 1px solid #e2e8f0;
      border-radius: .375rem 0 0 .375rem;
      background: white;
      padding: .5rem .35rem;
      font-size: .75rem;
    }

    #register-view .register-phone-fallback input {
      min-width: 0;
      flex: 1;
      border-radius: 0 .375rem .375rem 0;
    }
  </style>
  <script src="{{ asset('js/index.js') }}" defer></script>

</body>

</html>
