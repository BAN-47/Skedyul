<!-- ══════════ SIDEBAR ══════════ -->
<div class="sidebar">
  <div class="sidebar-logo">
    <div class="sidebar-logo-text">SKED<span>YUL</span></div>
  </div>

<div class="sidebar-user">
  <div class="sidebar-avatar">
    {{ collect(explode(' ', Auth::user()->usr_name))->map(fn($n) => strtoupper($n[0]))->take(2)->implode('') }}
  </div>
  <div class="overflow-hidden">
    <div class="sidebar-user-name">{{ Auth::user()->usr_name }}</div>
    <div class="sidebar-user-role">Department Chair</div>
  </div>
</div>

  <div class="sidebar-nav">
    <div class="nav-section-label">Main</div>
    <a href="{{ route('chair.dashboard') }}" class="nav-item {{ request()->routeIs('chair.dashboard') ? 'active' : '' }}">
      <span class="nav-icon"></span> Dashboard
    </a>

    <a href="{{ route('chair.pbs') }}" class="nav-item {{ request()->routeIs('chair.pbs') ? 'active' : '' }}">
      <span class="nav-icon"></span> Program by Section (PBS)
    </a>

        <a href="{{ route('chair.pbt') }}" class="nav-item {{ request()->routeIs('chair.pbt') ? 'active' : '' }}">
      <span class="nav-icon"></span> Program by Teacher (PBT)
    </a>

    {{--
      PBT and MIS Code are on the roadmap but not built yet. Uncomment
      these once chair.pbt and chair.mis_code routes + controllers exist.
      Keep this a real Blade comment block, not an HTML comment, or the
      route() calls below still execute even though the links stay
      visually hidden, which throws RouteNotFoundException.

    <a href="{{ route('chair.mis_code') }}" class="nav-item {{ request()->routeIs('chair.mis_code') ? 'active' : '' }}">
      <span class="nav-icon"></span> MIS Code
    </a>
    --}}

    <a href="{{ route('chair.faculty_load') }}" class="nav-item {{ request()->routeIs('chair.faculty_load') ? 'active' : '' }}">
      <span class="nav-icon"></span> Faculty Load
    </a>
    <a href="{{ route('chair.subjects') }}" class="nav-item {{ request()->routeIs('chair.subjects') ? 'active' : '' }}">
      <span class="nav-icon"></span> Subjects
    </a>
    <a href="{{ route('chair.rooms') }}" class="nav-item {{ request()->routeIs('chair.rooms') ? 'active' : '' }}">
      <span class="nav-icon"></span> Rooms
    </a>

    <div class="nav-section-label">Submission</div>
    <a href="{{ route('chair.submit_dean') }}" class="nav-item {{ request()->routeIs('chair.submit_dean') ? 'active' : '' }}">
      <span class="nav-icon"></span> Submit to Dean
    </a>

    <div class="nav-section-label">System</div>
    <a href="{{ route('chair.export_reports') }}" class="nav-item {{ request()->routeIs('chair.export_reports') ? 'active' : '' }}">
      <span class="nav-icon"></span> Export Reports
    </a>
    <a href="{{ route('chair.settings') }}" class="nav-item {{ request()->routeIs('chair.settings') ? 'active' : '' }}">
      <span class="nav-icon"></span> Settings
    </a>
  </div>

  <div class="sidebar-bottom">
    <form method="POST" action="{{ route('logout') }}">
      @csrf
      <button type="submit" class="btn-logout">⬅ Sign Out</button>
    </form>
  </div>
</div>
<!-- ══ END SIDEBAR ══ -->