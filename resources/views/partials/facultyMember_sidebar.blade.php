 <!-- ══════════ SIDEBAR ══════════ -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>

    <div class="sidebar-user">
      <div class="sidebar-avatar">EU</div>
      <div class="overflow-hidden">
        <div class="sidebar-user-name">Emmanuel D. Ugalde</div>
        <div class="sidebar-user-role">Faculty Member, BSIS</div>
      </div>
    </div>

    <div class="sidebar-nav">
      <div class="nav-section-label">Main</div>
        <a href="{{ route('faculty.faculty_dashboard') }}" class="nav-item {{ request()->routeIs('faculty.faculty_dashboard') ? 'active' : '' }}">
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
      <button class="btn-logout">⬅ Sign Out</button>
    </div>
  </div>