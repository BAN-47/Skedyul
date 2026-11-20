<!-- ══════════ SIDEBAR ══════════ -->
@php($chairSidebarProfile = Auth::user()->deptChair()->with('program')->first())
<div class="sidebar chair-sidebar">
    <div class="sidebar-logo">
        <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>

    <div class="sidebar-user">
        <div class="sidebar-avatar">
            @if ($chairSidebarProfile?->dc_profile_image)
                <img src="{{ \App\Services\ProfileImageStorage::url($chairSidebarProfile->dc_profile_image, 'chair_profile') }}"
                    alt="Profile photo" class="h-full w-full rounded-full object-cover">
            @else
                {{ collect(explode(' ', Auth::user()->usr_name))->map(fn($n) => strtoupper($n[0]))->take(2)->implode('') }}
            @endif
        </div>
        <div class="overflow-hidden">
            <div class="sidebar-user-name">{{ Auth::user()->usr_name }}</div>
            <div class="sidebar-user-role">Department Chair</div>
            <div class="truncate text-[11px] text-slate-400"
                title="{{ $chairSidebarProfile?->program?->dept_name ?? 'Department not assigned' }}">
                Handling:
                {{ $chairSidebarProfile?->program?->dept_code ?? ($chairSidebarProfile?->program?->dept_name ?? 'Department not assigned') }}
            </div>
        </div>
    </div>

    <div class="sidebar-nav">
        <div class="nav-section-label">Main</div>

        <a href="{{ route('chair.dashboard') }}"
            class="nav-item {{ request()->routeIs('chair.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-layout-dashboard" aria-hidden="true"></i></span> Dashboard
        </a>

        <a href="{{ route('chair.pbs') }}" class="nav-item {{ request()->routeIs('chair.pbs') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-calendar-event" aria-hidden="true"></i></span><span
                class="nav-label">Program by Section (PBS)</span>
        </a>

        <a href="{{ route('chair.pbt') }}" class="nav-item {{ request()->routeIs('chair.pbt') ? 'active' : '' }}">
            <span class="nav-icon" aria-hidden="true">
                <svg class="mx-auto h-[17px] w-[17px]" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="13" rx="1.5"></rect>
                    <circle cx="9" cy="9" r="1.5"></circle>
                    <path d="M6.5 14c.5-1.5 1.4-2.2 2.5-2.2s2 .7 2.5 2.2M14 9h4m-4 3h4M8 21l4-4m4 4-4-4"></path>
                </svg>
            </span><span class="nav-label">Program by Teacher (PBT)</span>
        </a>


        <a href="{{ route('chair.mis') }}" class="nav-item {{ request()->routeIs('chair.mis') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-id-badge-2" aria-hidden="true"></i></span> MIS Code
        </a>


        <a href="{{ route('chair.faculty_load') }}"
            class="nav-item {{ request()->routeIs('chair.faculty_load') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-chart-bar" aria-hidden="true"></i></span> Faculty Load
        </a>

        <a href="{{ route('chair.subjects') }}"
            class="nav-item {{ request()->routeIs('chair.subjects') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-book-2" aria-hidden="true"></i></span> Courses
        </a>

        <a href="{{ route('chair.rooms') }}" class="nav-item {{ request()->routeIs('chair.rooms') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-door" aria-hidden="true"></i></span> Rooms
        </a>

        <div class="nav-section-label">Submission</div>
        <a href="{{ route('chair.unassigned_faculty.index') }}"
            class="nav-item {{ request()->routeIs('chair.unassigned_faculty.*') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-user-question" aria-hidden="true"></i></span> Unassigned Faculty
        </a>
        <a href="{{ route('chair.submit_dean') }}"
            class="nav-item {{ request()->routeIs('chair.submit_dean') ? 'active' : '' }}">
            <span class="nav-icon"><i class="ti ti-send" aria-hidden="true"></i></span> Schedule Submission
        </a>

        <div class="nav-section-label">System</div>
        <a href="{{ route('chair.settings') }}"
            class="nav-item {{ request()->routeIs('chair.settings') ? 'active' : '' }}">
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
