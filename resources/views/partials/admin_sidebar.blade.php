{{-- ══ ADMIN SIDEBAR ══ --}}
<div class="sidebar">

    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-text">SKED<span>YUL</span></div>
    </div>

    {{-- USER --}}
    <div class="sidebar-user">
        <div class="sidebar-avatar">
            {{ collect(explode(' ', Auth::user()->usr_name))
                ->map(fn($n) => strtoupper($n[0]))
                ->take(2)
                ->implode('') }}
        </div>

        <div class="overflow-hidden">
            <div class="sidebar-user-name">
                {{ Auth::user()->usr_name }}
            </div>

            <div class="sidebar-user-role">
                @php
                $roleLabels = [
                'system_admin' => 'Technical Administrator',
                'faculty' => 'Faculty',
                'department_chair' => 'Department Chair',
                'dean' => 'Dean',
                ];
                @endphp

                {{ $roleLabels[Auth::user()->usr_role] ?? ucfirst(Auth::user()->usr_role) }}
            </div>
        </div>
    </div>

    {{-- NAV --}}
    <nav class="sidebar-nav">

        <div class="nav-section-label">Main</div>

        @php
        $navMain = [
        [
        'admin.dashboard',
        route('admin.dashboard'),
        '🏠',
        'Dashboard'
        ],

        [
        'admin.users',
        route('admin.users'),
        '👥',
        'User Accounts'
        ],

        [
        'subject.index',
        route('subject.index'),
        '📚',
        'Subjects'
        ],

        [
        'admin.rooms',
        route('admin.rooms'),
        '🚪',
        'Rooms'
        ],
        ];

        $navSystem = [
        [
        'admin.reports',
        route('admin.reports'),
        '📊',
        'Reports'
        ],

        [
        'admin.settings',
        route('admin.settings'),
        '⚙️',
        'Settings'
        ],
        ];
        @endphp

        {{-- MAIN NAVIGATION --}}
        @foreach($navMain as [$route, $url, $icon, $label])
        <a
            href="{{ $url }}"
            class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}">
            <span class="nav-icon">{{ $icon }}</span>
            {{ $label }}
        </a>
        @endforeach
        <div class="nav-section-label">System</div>
        {{-- SYSTEM NAVIGATION --}}
        @foreach($navSystem as [$route, $url, $icon, $label])
        <a
            href="{{ $url }}"
            class="nav-item {{ request()->routeIs($route) ? 'active' : '' }}">
            <span class="nav-icon">{{ $icon }}</span>
            {{ $label }}
        </a>
        @endforeach

    </nav>
    {{-- SIGN OUT --}}
    <div class="sidebar-bottom">
        <form
            method="POST"
            action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="btn-logout">
                ⬅ Sign Out
            </button>
        </form>
    </div>
</div>
{{-- ══ END SIDEBAR ══ --}}