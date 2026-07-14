<!-- ══════════ SIDEBAR ══════════ -->
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar">TA</div>
        <div class="overflow-hidden">
            <div class="sidebar-user-name">Tech Admin</div>
            <div class="sidebar-user-role">Technical Administrator</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"></span> Dashboard
        </a>
        <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
            <span class="nav-icon"></span> User Accounts
        </a>
        <a href="{{ route('subject.index') }}" class="nav-item {{ request()->routeIs('subject.index') ? 'active' : '' }}">
            <span class="nav-icon"></span> Subjects
        </a>
        <a href="{{ route('admin.rooms') }}" class="nav-item {{ request()->routeIs('admin.rooms') ? 'active' : '' }}">
            <span class="nav-icon"></span> Rooms
        </a>

        <div class="nav-section-label">System</div>
        <a href="{{ route('admin.reports') }}" class="nav-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
            <span class="nav-icon"></span> Reports
        </a>
        <a href="{{ route('admin.admin_settings') }}" class="nav-item {{ request()->routeIs('admin.admin_settings') ? 'active' : '' }}">
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