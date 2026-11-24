<div class="sidebar faculty-sidebar" id="faculty-sidebar">
    <div class="sidebar-logo faculty-sidebar-logo">
        <div class="sidebar-logo-text">SKED<span>YUL</span></div>
        <span class="faculty-portal-badge">PORTAL</span>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar">
            @if (auth()->user()->faculty?->fac_profile_image)
                <img src="{{ \App\Services\ProfileImageStorage::url(auth()->user()->faculty->fac_profile_image, 'faculty_profile') }}"
                    alt="Profile photo" class="h-full w-full rounded-full object-cover">
            @else
                {{ strtoupper(substr(auth()->user()->faculty->fac_first_name ?? 'U', 0, 1) . substr(auth()->user()->faculty->fac_last_name ?? '', 0, 1)) }}
            @endif
        </div>
        <div class="overflow-hidden">
            <div class="sidebar-user-name">{{ auth()->user()->faculty->full_name ?? auth()->user()->usr_name }}</div>
            <div class="sidebar-user-role">Faculty Member, {{ auth()->user()->faculty->department->dept_code ?? 'N/A' }}
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>
        <a href="{{ route('faculty.dashboard') }}"
            class="nav-item {{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}"
            style="text-decoration:none !important;">
            <span class="nav-icon"><i class="ti ti-layout-dashboard" aria-hidden="true"></i></span> My Dashboard
        </a>
        <a href="{{ route('faculty.subjects') }}"
            class="nav-item {{ request()->routeIs('faculty.subjects') ? 'active' : '' }}"
            style="text-decoration:none !important;">
            <span class="nav-icon"><i class="ti ti-book-2" aria-hidden="true"></i></span> My Subjects
        </a>
        <a href="{{ route('faculty.schedule') }}"
            class="nav-item {{ request()->routeIs('faculty.schedule') ? 'active' : '' }}"
            style="text-decoration:none !important;">
            <span class="nav-icon"><i class="ti ti-calendar-week" aria-hidden="true"></i></span> My Schedule
        </a>
        <a href="{{ route('faculty.faculty_settings') }}"
            class="nav-item {{ request()->routeIs('faculty.faculty_settings') ? 'active' : '' }}"
            style="text-decoration:none !important;">
            <span class="nav-icon"><i class="ti ti-settings" aria-hidden="true"></i></span> Settings
        </a>
    </div>

    <div class="sidebar-bottom">
        @include('partials.sidebar_mobile_app')
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">⬅ Sign Out</button>
        </form>
    </div>
</div>
