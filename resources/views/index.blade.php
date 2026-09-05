<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden font-sans">

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
        <div class="mt-12 w-full max-w-sm rounded-2xl border border-white/10 bg-white/[.06] px-5 py-3.5 text-left">
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
    <div class="flex w-[600px] shrink-0 items-center justify-center bg-slate-50 px-10 py-12">

      {{-- Card --}}
      <div class="w-full animate-[fadeUp_.6s_ease]">

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

        <div class="mt-6 text-center text-xs text-slate-400">
          CCICT · Cebu Technological University — Main Campus
        </div>

      </div>
    </div>

  </div>

  {{-- Fade-up animation --}}
  <style>
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(16px); }
      to   { opacity:1; transform:translateY(0); }
    }
  </style>

</body>
</html>