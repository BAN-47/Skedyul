<div class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-text">SKED<span>YUL</span></div>
  </div>

  <div class="sidebar-user">
    <div class="sidebar-avatar">
      {{ strtoupper(substr(auth()->user()->faculty->fac_first_name ?? 'U', 0, 1) . substr(auth()->user()->faculty->fac_last_name ?? '', 0, 1)) }}
    </div>
    <div class="overflow-hidden">
      <div class="sidebar-user-name">{{ auth()->user()->faculty->full_name ?? auth()->user()->usr_name }}</div>
      <div class="sidebar-user-role">Faculty Member, {{ auth()->user()->faculty->department->dept_code ?? 'N/A' }}</div>
    </div>
  </div>

  <div class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a href="{{ route('faculty.dashboard') }}" class="nav-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
      <span class="nav-icon"></span> My Dashboard
    </a>
    <a href="{{ route('faculty.subjects') }}" class="nav-item {{ request()->routeIs('faculty.subjects') ? 'active' : '' }}">
      <span class="nav-icon"></span> My Subjects
    </a>
    <a href="{{ route('faculty.schedule') }}" class="nav-item {{ request()->routeIs('faculty.schedule') ? 'active' : '' }}">
      <span class="nav-icon"></span> My Schedule
    </a>
    <a href="{{ route('faculty.faculty_settings') }}" class="nav-item {{ request()->routeIs('faculty.faculty_settings') ? 'active' : '' }}">
      <span class="nav-icon"></span> Settings
    </a>
  </div>

  <div class="sidebar-bottom">
    <form action="{{ route('logout') }}" method="POST">
      @csrf
      <button type="submit" class="btn-logout">⬅ Sign Out</button>
    </form>
  </div>
</div>