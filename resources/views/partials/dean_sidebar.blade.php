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
            <div class="sidebar-user-role">Dean</div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('dean.dashboard') }}"
            class="nav-item {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"></span> Dashboard
        </a>
        <a href="{{ route('dean.faculty_workload') }}"
            class="nav-item {{ request()->routeIs('dean.faculty_workload') ? 'active' : '' }}">
            <span class="nav-icon"></span> Faculty Workload
        </a>
        <a href="{{ route('dean.departments') }}"
            class="nav-item {{ request()->routeIs('dean.departments') ? 'active' : '' }}">
            <span class="nav-icon"></span> Departments
        </a>

        <div class="nav-section-label">Approvals</div>
        <a href="{{ route('dean.pending_approvals') }}"
            class="nav-item {{ request()->routeIs('dean.pending_approvals') ? 'active' : '' }}">
            <span class="nav-icon"></span> Pending Approvals
            @if(($pendingApprovalsCount ?? 0) > 0)
                <span class="nav-badge">{{ $pendingApprovalsCount }}</span>
            @endif
        </a>

        <div class="nav-section-label">Reports</div>
        <a href="{{ route('dean.schedule_reports') }}"
            class="nav-item {{ request()->routeIs('dean.schedule_reports') ? 'active' : '' }}">
            <span class="nav-icon"></span> Schedule Reports
        </a>
        <a href="{{ route('dean.faculty_deployment') }}"
            class="nav-item {{ request()->routeIs('dean.faculty_deployment') ? 'active' : '' }}">
            <span class="nav-icon"></span> Faculty Deployment
        </a>

        <div class="nav-section-label">System</div>
        <a href="{{ route('dean.settings') }}"
            class="nav-item {{ request()->routeIs('dean.settings') ? 'active' : '' }}">
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