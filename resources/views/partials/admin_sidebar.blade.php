{{-- ══ ADMIN SIDEBAR ══ --}}
<div class="sidebar admin-sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="admin-brand">
            <span class="admin-brand-mark">S</span>
            <div class="min-w-0 text-left">
                <div class="sidebar-logo-text">SKED<span>YUL</span></div>
                <div class="admin-brand-caption">Academic Core</div>
            </div>
            <span class="admin-role-badge">ADMIN</span>
        </div>
    </div>

    {{-- NAV --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Academic Operations</div>

        @php
            $navMain = [
                ['admin.dashboard', route('admin.dashboard'), '▦', 'Dashboard'],

                ['admin.users', route('admin.users'), '♙', 'User Accounts'],

                ['admin.pending-accounts', route('admin.pending-accounts'), '◷', 'Pending Account Approval'],

                ['admin.sections', route('admin.sections'), '▤', 'Sections'],

                ['admin.schedule-archives', route('admin.schedule-archives'), '▣', 'Schedule Archives'],

                ['subject.index', route('subject.index'), '▱', 'Courses'],

                ['admin.rooms', route('admin.rooms'), '⌂', 'Rooms'],
            ];

            $navSystem = [
                ['admin.activity-logs', route('admin.activity-logs'), '◴', 'Activity Logs'],

                ['admin.reports', route('admin.reports'), '▥', 'Reports'],

                ['admin.settings', route('admin.settings'), '⚙', 'Settings'],
            ];
        @endphp

        {{-- MAIN NAVIGATION --}}
        @foreach ($navMain as [$route, $url, $icon, $label])
            <a href="{{ $url }}" class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}">
                <span class="nav-icon">{{ $icon }}</span>
                {{ $label }}
            </a>
        @endforeach
        <div class="nav-section-label">System Administration</div>
        {{-- SYSTEM NAVIGATION --}}
        @foreach ($navSystem as [$route, $url, $icon, $label])
            <a href="{{ $url }}" class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}">
                <span class="nav-icon">{{ $icon }}</span>
                {{ $label }}
            </a>
        @endforeach

    </nav>
    {{-- SIGN OUT --}}
    <div class="sidebar-bottom">
        <div class="admin-sidebar-version">
            <span>Environment</span>
            <strong>{{ strtoupper(app()->environment()) }}</strong>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                ⬅ Sign Out
            </button>
        </form>
    </div>
</div>
{{-- ══ END SIDEBAR ══ --}}
