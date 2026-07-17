<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>SKEDYUL</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>

<body>
  <div id="screen-login">
    <!-- ================= LEFT PANEL ================= -->
    <div class="left-panel">
      <div class="left-grid"></div>
      <div class="deco-circle deco-circle-1"></div>
      <div class="deco-circle deco-circle-2"></div>
      <div class="deco-circle deco-circle-3"></div>
      <div class="left-content">
        <div class="left-logo-text">
          SKED<span>YUL</span>
        </div>
        <div class="left-tagline">
          Faculty Scheduling System
        </div>
        <p class="left-desc">
          A <b>smart, centralized platform</b> built for CCICT faculty —
          effortlessly manage class schedules, plot subjects,
          detect conflicts, and balance workloads all in one place.
        </p>
        <div class="feature-list">
          <div class="feature-item">
            <div class="feature-icon">
              <i class="ti ti-calendar"></i>
            </div>
            <div>
              <div class="feature-text-title">
                Automated Schedule Plotting
              </div>
              <div class="feature-text-sub">
                Assign subjects to time slots with instant conflict detection
              </div>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="ti ti-school"></i>
            </div>
            <div>
              <div class="feature-text-title">
                Faculty Workload Monitoring
              </div>
              <div class="feature-text-sub">
                Track teaching loads and prevent overloading in real time
              </div>
            </div>
          </div>
          <div class="feature-item">

            <div class="feature-icon">
              <i class="ti ti-building"></i>
            </div>
            <div>
              <div class="feature-text-title">
                Room & Section Management
              </div>
              <div class="feature-text-sub">
                Manage classrooms and sections across all programs
              </div>
            </div>
          </div>
          <div class="feature-item">
            <div class="feature-icon">
              <i class="ti ti-chart-bar"></i>
            </div>
            <div>
              <div class="feature-text-title">
                Reports & Export
              </div>
              <div class="feature-text-sub">
                Generate and download schedule reports in PDF or Excel
              </div>
            </div>
          </div>
        </div>
        <div class="dept-badge" style="margin-top:15px;">
          <div class="dept-badge-name">
            College of Computing, Information and
            <br>
            Communications Technology
          </div>
          <div class="dept-badge-uni">
            Cebu Technological University · Main Campus
          </div>
        </div>
      </div>
    </div>
    <!-- ================= RIGHT LOGIN PANEL ================= -->
    <div class="right-panel">
      <div class="login-card">
        <div class="left-logo-text mb-4"
          style="color:#808080;">
          LOG<span>IN</span>
        </div>
        <div class="login-card-title">
          Welcome back!
        </div>
        <div class="login-card-sub">
          Sign in to your SKEDYUL account to continue.
        </div>
        <!-- ================= LOGIN FORM ================= -->
        <form method="POST" action="{{ route('login.authenticate') }}">
          @csrf
          <!-- ERROR MESSAGE -->
          @if(session('error'))
          <div class="alert alert-danger">
            {{ session('error') }}
          </div>
          @endif
          <!-- EMAIL -->
          <div class="mb-3">
            <label class="login-label mb-2 d-block">
              Email Address
            </label>
            <input
              type="email"
              name="email"
              class="form-control login-input"
              placeholder="Enter your email address"
              value="{{ old('email') }}"
              required>
            @error('email')
            <div class="text-danger">
              {{ $message }}
            </div>
            @enderror
          </div>
          <!-- PASSWORD -->
          <div class="mb-4">
            <label class="login-label mb-2 d-block">
              Password
            </label>
            <input
              type="password"
              name="password"
              class="form-control login-input"
              placeholder="Enter your password"
              required>
            @error('password')
            <div class="text-danger">
              {{ $message }}
            </div>
            @enderror
          </div>
          <!-- BUTTON -->
          <button
            type="submit"
            class="btn btn-login w-100">
            <i class="ti ti-login"></i>
            Sign In
          </button>
        </form>
        <div class="login-footer text-center mt-4">
          CCICT · Cebu Technological University — Main Campus
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>