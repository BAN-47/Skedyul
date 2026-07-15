 <!-- ══════════ SIDEBAR ══════════ -->
  <div class="sidebar">
    <div class="sidebar-logo">
      <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>

    <div class="sidebar-user">
      <div class="sidebar-avatar">MV</div>
      <div class="overflow-hidden">
        <div class="sidebar-user-name">Ma. Emie Villaceran</div>
        <div class="sidebar-user-role">Dean, BSIS</div>
      </div>
    </div>

    <div class="sidebar-nav">
      <div class="nav-section-label">Main</div>
      <a href="{{ route('dean.dashboard') }}" class="nav-item {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
        <span class="nav-icon"></span> Dashboard
      </a>
      <div class="nav-item"><span class="nav-icon"></span> Faculty Workload</div>
      <div class="nav-item"><span class="nav-icon"></span> Departments</div>

      <div class="nav-section-label">Approvals</div>
      <div class="nav-item"><span class="nav-icon"></span> Pending Approvals</div>

      <div class="nav-section-label">System</div>
      <div class="nav-item"><span class="nav-icon"></span> Schedule Reports</div>
      <div class="nav-item"><span class="nav-icon"></span> Faculty Deployment</div>
      <div class="nav-item"><span class="nav-icon"></span> Settings</div>
    </div>

    <div class="sidebar-bottom">
      <button class="btn-logout">⬅ Sign Out</button>
    </div>
  </div>
  <!-- ══ END SIDEBAR ══ -->