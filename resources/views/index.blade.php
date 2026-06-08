<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SKEDYUL — Faculty Portal</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/login.css') }}">

</head>
<body>

<div id="screen-login">

  <!-- ══ LEFT: BRANDING PANEL ══ -->
  <div class="left-panel">
    <div class="left-grid"></div>
    <div class="deco-circle deco-circle-1"></div>
    <div class="deco-circle deco-circle-2"></div>
    <div class="deco-circle deco-circle-3"></div>

    <div class="left-content" style="margin-top: -10px;">
      <!-- Logo -->
      <div class="left-logo-text">SKED<span>YUL</span></div>
      <div class="left-tagline">Faculty Scheduling System</div>

      <!-- Description -->
      <p class="left-desc" style="margin-top: -20px;">
        A <b>smart, centralized platform</b> built for CCICT faculty — effortlessly manage class schedules, plot subjects, detect conflicts, and balance workloads all in one place.
      </p>

        <!-- Features -->
        <div class="feature-list" style="margin-top: -10px;">
        <div class="feature-item">
            <div class="feature-icon"><i class="ti ti-calendar" aria-hidden="true"></i></div>
            <div>
            <div class="feature-text-title">Automated Schedule Plotting</div>
            <div class="feature-text-sub">Assign subjects to time slots with instant conflict detection</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="ti ti-school" aria-hidden="true"></i></div>
            <div>
            <div class="feature-text-title">Faculty Workload Monitoring</div>
            <div class="feature-text-sub">Track teaching loads and prevent overloading in real time</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="ti ti-building" aria-hidden="true"></i></div>
            <div>
            <div class="feature-text-title">Room & Section Management</div>
            <div class="feature-text-sub">Manage classrooms and sections across all programs</div>
            </div>
        </div>
        <div class="feature-item">
            <div class="feature-icon"><i class="ti ti-chart-bar" aria-hidden="true"></i></div>
            <div>
            <div class="feature-text-title">Reports & Export</div>
            <div class="feature-text-sub">Generate and download schedule reports in PDF or Excel</div>
            </div>
        </div>
        </div>

      <!-- Dept badge -->
      <div class="dept-badge" style="margin-top: 40px;">
        <div class="dept-badge-name">College of Computing, Information and<br>Communications Technology</div>
        <div class="dept-badge-uni">Cebu Technological University · Main Campus</div>
      </div>
    </div>
  </div>

  <!-- ══ RIGHT: LOGIN PANEL ══ -->
  <div class="right-panel">
    <div class="login-card">

      <div class="left-logo-text mb-4" style="color: #808080; letter-spacing: -.10px;">LOG<span>IN</span></div>
      <div class="login-card-title">Welcome back!</div>
      <div class="login-card-sub">Sign in to your SKEDYUL account to continue.</div>

      <!-- Email -->
      <div class="mb-3">
        <label class="login-label mb-2 d-block">Email Address</label>
        <input type="email" id="login-email"
               class="form-control login-input"
               placeholder="Enter your email address">
      </div>

      <!-- Password -->
      <div class="mb-4">
        <label class="login-label mb-2 d-block">Password</label>
        <input type="password" id="login-password"
               class="form-control login-input"
               placeholder="Enter your password">
      </div>

      <!-- Sign In -->
      <button class="btn btn-login w-100" onclick="doLogin()">Sign In</button>

      <!-- Footer -->
      <div class="login-footer text-center mt-4">
        CCICT · Cebu Technological University — Main Campus
      </div>

    </div>
  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function doLogin() {
  const email    = document.getElementById('login-email').value.trim();
  const password = document.getElementById('login-password').value.trim();
  if (!email || !password) {
    alert('Please enter your email and password.');
    return;
  }
  // Replace with your actual login logic / form submit / redirect
  alert('Logging in as: ' + email);
}
</script>
</body>
</html>
