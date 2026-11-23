<!-- ══════════ SIDEBAR ══════════ -->
<div class="sidebar dean-sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-text">SKED<span>YUL</span></div>
        <div class="dean-sidebar-caption">College Scheduling Portal</div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar">
            @if (Auth::user()->dean?->dean_profile_image)
                <img src="{{ \App\Services\ProfileImageStorage::url(Auth::user()->dean->dean_profile_image, 'dean_profile') }}"
                    alt="Profile photo" class="h-full w-full rounded-full object-cover">
            @else
                {{ strtoupper(substr(Auth::user()->dean->dean_first_name ?? '', 0, 1) . substr(Auth::user()->dean->dean_middle_name ?? '', 0, 1) . substr(Auth::user()->dean->dean_last_name ?? '', 0, 1)) }}
            @endif
        </div>
        <div class="overflow-hidden">
            <div class="sidebar-user-name">{{ Auth::user()->dean->dean_first_name ?? '' }}
                {{ Auth::user()->dean->dean_middle_name ?? '' }} {{ Auth::user()->dean->dean_last_name ?? '' }}</div>
            <div class="sidebar-user-role">Dean ·
                {{ Auth::user()->dean?->department?->college_code ?? (Auth::user()->dean?->department?->college_name ?? 'College') }}
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Overview</div>
        <a href="{{ route('dean.dashboard') }}"
            class="nav-item {{ request()->routeIs('dean.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-layout-dashboard" aria-hidden="true"></i></span> Dashboard
        </a>

        <a href="{{ route('dean.departments') }}"
            class="nav-item {{ request()->routeIs('dean.departments') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-building-community" aria-hidden="true"></i></span> Departments
        </a>
        <a href="{{ route('dean.courses') }}"
            class="nav-item {{ request()->routeIs('dean.courses') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-book-2" aria-hidden="true"></i></span> Courses
        </a>
        <a href="{{ route('dean.sections') }}"
            class="nav-item {{ request()->routeIs('dean.sections*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-users-group" aria-hidden="true"></i></span> Sections
        </a>

        <div class="nav-section-label">Approvals</div>
        <a href="{{ route('dean.pending_approvals') }}"
            class="nav-item {{ request()->routeIs('dean.pending_approvals') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-inbox" aria-hidden="true"></i></span> Pending Approvals
            @if (($pendingApprovalsCount ?? 0) > 0)
                <span class="nav-badge">{{ $pendingApprovalsCount }}</span>
            @endif
        </a>

        <div class="nav-section-label">Schedule Views</div>
        <a href="{{ route('dean.pbs') }}" class="nav-item {{ request()->routeIs('dean.pbs*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-calendar-event" aria-hidden="true"></i></span> Program by Section
            (PBS)
        </a>
        <a href="{{ route('dean.pbt') }}" class="nav-item {{ request()->routeIs('dean.pbt') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-users" aria-hidden="true"></i></span> Program by Teacher (PBT)
        </a>

        <div class="nav-section-label">System</div>
        <a href="{{ route('dean.settings') }}"
            class="nav-item {{ request()->routeIs('dean.settings') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-settings" aria-hidden="true"></i></span> Settings
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
