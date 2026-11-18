<!DOCTYPE html>
<html lang="en">
<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Admin Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

{{-- ══ APP WRAPPER ══ --}}
<div class="app-shell">

    {{-- ══ SIDEBAR ══ --}}
    @include('partials.admin_sidebar')

    {{-- ══ MAIN ══ --}}
    <div class="app-main">

        {{-- ══ TOPBAR ══ --}}
        @include('partials.admin_header', ['title' => 'Technical Admin Dashboard'])

        {{-- ══ PAGE CONTENT ══ --}}
        <div class="page-content">

            {{-- ══ QUOTE BANNER ══ --}}
            <div class="quote-banner">
                <div class="quote-banner-grid"></div>
                <div class="relative z-10 flex items-start justify-between gap-4">
                    <div class="flex-1">
                        <div class="text-[11px] font-bold uppercase tracking-[1.5px] text-white/40 mb-1.5">
                            Welcome back, {{ Auth::user()->usr_name }}
                        </div>
                        <div id="quote-text" class="text-[22px] leading-snug font-bold text-white mb-2.5 italic">
                            "Education is the most powerful weapon which you can use to change the world."
                        </div>
                        <div id="quote-author" class="text-[12px] font-semibold text-white/40">
                            — Nelson Mandela
                        </div>
                        <div class="flex items-center gap-2 mt-3">
                            <button type="button" id="quote-prev"
                                class="w-7 h-7 rounded-full bg-white/10 border-none text-white text-[13px] cursor-pointer flex items-center justify-center hover:bg-white/20 transition">‹</button>
                            <div class="flex gap-2" id="quote-dots"></div>
                            <button type="button" id="quote-next"
                                class="w-7 h-7 rounded-full bg-white/10 border-none text-white text-[13px] cursor-pointer flex items-center justify-center hover:bg-white/20 transition">›</button>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="text-[44px] opacity-[.12] leading-none mb-2.5">"</div>
                        <div class="text-[11px] text-white/60">Current academic term</div>
                        <div class="text-[12px] font-bold text-white mt-1">
                            {{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label ?? 'No active academic year' }}
                            · {{ $semester->sem_name ?? 'No active semester' }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SYSTEM SUMMARY ══ --}}
            <div class="grid grid-cols-2 gap-3 mb-4 sm:grid-cols-3 2xl:grid-cols-7">
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-blue-600"></div>
                    <span class="admin-metric-icon bg-blue-50 text-blue-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM20 8v6m3-3h-6"/></svg></span>
                    <div class="stat-label">Total Users</div><div class="stat-value">{{ number_format($totalUsers) }}</div><div class="stat-sub">All registered roles</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-amber-500"></div>
                    <span class="admin-metric-icon bg-amber-50 text-amber-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="4"/><path d="M2 21v-2a7 7 0 0 1 14 0v2m3-13v6m3-3h-6"/></svg></span>
                    <div class="stat-label">Total Faculty</div><div class="stat-value">{{ number_format($totalFaculty) }}</div><div class="stat-sub">Across all colleges</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar {{ $dbStatus === 'Online' ? 'bg-green-600' : 'bg-red-500' }}"></div>
                    <span class="admin-metric-icon {{ $dbStatus === 'Online' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 3 4 6v5c0 5 3.4 8.5 8 10 4.6-1.5 8-5 8-10V6l-8-3Z"/><path d="m8.5 12 2.3 2.3 4.8-5"/></svg></span>
                    <div class="stat-label">System Status</div><div class="stat-value {{ $dbStatus === 'Online' ? 'text-green-700' : 'text-red-600' }}">{{ $dbStatus }}</div><div class="stat-sub">{{ $platformName }} · {{ $databaseDriver }}</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-cyan-600"></div>
                    <span class="admin-metric-icon bg-cyan-50 text-cyan-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 1.7 3.6 3 8 3s8-1.3 8-3V5M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/></svg></span>
                    <div class="stat-label">DB Records</div><div class="stat-value">{{ number_format($dbRecords) }}</div><div class="stat-sub">Users, sections, courses, rooms</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-violet-600"></div>
                    <span class="admin-metric-icon bg-violet-50 text-violet-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h5"/></svg></span>
                    <div class="stat-label">Total Sections</div><div class="stat-value">{{ number_format($totalSections) }}</div><div class="stat-sub">All academic years and semesters</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-indigo-600"></div>
                    <span class="admin-metric-icon bg-indigo-50 text-indigo-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 4h16v16H4zM8 8h8M8 12h8M8 16h4"/><path d="m16 16 2 2 4-4"/></svg></span>
                    <div class="stat-label">Total Courses</div><div class="stat-value">{{ number_format($subjectsOffered) }}</div><div class="stat-sub">Across all colleges</div>
                </article>
                <article class="stat-card admin-metric-card group">
                    <div class="stat-card-bar bg-emerald-600"></div>
                    <span class="admin-metric-icon bg-emerald-50 text-emerald-700"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 9h.01M15 9h.01"/></svg></span>
                    <div class="stat-label">Total Rooms</div><div class="stat-value">{{ number_format($totalRooms) }}</div><div class="stat-sub">{{ number_format($roomsAvailable) }} available · {{ number_format($roomsOccupied) }} in use</div>
                </article>
            </div>

            {{-- ══ DEPARTMENT FACULTY DISTRIBUTION ══ --}}
            <section class="card mb-4 admin-faculty-distribution" aria-labelledby="faculty-distribution-title">
                <div class="card-header flex-wrap gap-3">
                    <div>
                        <div class="card-title" id="faculty-distribution-title">Department Faculty Distribution</div>
                        <div class="card-sub">Active teaching rosters categorized by college</div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5" role="tablist" aria-label="Filter departments by college">
                        <button type="button" class="faculty-college-filter active" data-college-filter="all" role="tab" aria-selected="true">All Colleges</button>
                        @foreach($facultyDistributionGroups as $collegeGroup)
                            <button type="button" class="faculty-college-filter" data-college-filter="{{ $collegeGroup['id'] }}" role="tab" aria-selected="false">{{ $collegeGroup['code'] }}</button>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-5 xl:grid-cols-10" id="faculty-distribution-grid">
                    @forelse($facultyDistributionGroups as $collegeGroup)
                        @foreach($collegeGroup['departments'] as $dept)
                            <article class="faculty-distribution-dept" data-college="{{ $collegeGroup['id'] }}" title="{{ $dept->dept_name }} · {{ $collegeGroup['name'] }}">
                                <div class="text-[9px] font-bold uppercase tracking-wide text-slate-500">{{ $dept->dept_code ?: $dept->dept_name }}</div>
                                <div class="mt-1 text-2xl font-extrabold leading-none {{ $dept->faculty_count > 0 ? 'text-blue-700' : 'text-slate-400' }}">{{ $dept->faculty_count }}</div>
                                <div class="mt-1 text-[9px] font-medium text-slate-500">{{ Str::plural('Faculty', $dept->faculty_count) }}</div>
                            </article>
                        @endforeach
                    @empty
                        <div class="col-span-full rounded-lg bg-slate-50 p-5 text-center text-xs text-slate-500">No departments are registered yet.</div>
                    @endforelse
                </div>

                <div class="mt-4 border-t border-slate-100 pt-3" id="college-faculty-quota">
                    <div class="mb-2 text-[9px] font-bold uppercase tracking-[.1em] text-slate-500">College faculty distribution</div>
                    <div class="grid grid-cols-1 gap-2 md:grid-cols-3">
                        @foreach($facultyDistributionGroups as $collegeGroup)
                            @php
                                $collegePercent = $totalActiveFacultyInColleges > 0
                                    ? round(($collegeGroup['faculty_count'] / $totalActiveFacultyInColleges) * 100, 1)
                                    : 0;
                            @endphp
                            <div class="faculty-college-quota" data-college="{{ $collegeGroup['id'] }}">
                                <div class="mb-1 flex items-center justify-between gap-2 text-[10px]">
                                    <span class="truncate font-semibold text-slate-600" title="{{ $collegeGroup['name'] }}">{{ $collegeGroup['name'] }}</span>
                                    <span class="shrink-0 text-slate-500">{{ $collegeGroup['faculty_count'] }} · {{ $collegePercent }}%</span>
                                </div>
                                <div class="h-1.5 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full bg-blue-600 transition-all" style="width:{{ $collegePercent }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="card mb-4">
                <div class="card-header">
                    <div>
                        <div class="card-title">Platform Record Mix</div>
                        <div class="card-sub">A quick view of the main data groups managed by SKEDYUL</div>
                    </div>
                    <span class="badge badge-blue">{{ number_format($dbRecords) }} tracked records</span>
                </div>
                @php
                    $recordGroups = [
                        ['Users', $totalUsers, 'bg-blue-600'],
                        ['Sections', $totalSections, 'bg-violet-600'],
                        ['Courses', $subjectsOffered, 'bg-cyan-600'],
                        ['Rooms', $totalRooms, 'bg-amber-500'],
                    ];
                @endphp
                <div class="flex h-3 overflow-hidden rounded-full bg-slate-100" aria-label="Record groups distribution">
                    @foreach($recordGroups as [$label, $count, $color])
                        <div class="{{ $color }} transition-all" title="{{ $label }}: {{ $count }}" style="width:{{ $dbRecords > 0 ? ($count / $dbRecords * 100) : 0 }}%"></div>
                    @endforeach
                </div>
                <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-4">
                    @foreach($recordGroups as [$label, $count, $color])
                        <div class="flex items-center gap-2 rounded-xl bg-slate-50 px-3 py-2.5">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $color }}"></span>
                            <div class="min-w-0"><div class="text-[11px] text-slate-500">{{ $label }}</div><div class="text-sm font-extrabold text-slate-800">{{ number_format($count) }}</div></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- ══ GLOBAL TEACHING ANALYTICS ══ --}}
            <div class="card mb-4 overflow-hidden">
                <div class="card-header">
                    <div>
                        <div class="card-title">Top 3 Most Plotted Faculty — All Colleges</div>
                        <div class="card-sub">
                            Ranked by scheduled weekly hours across every college
                            @if($semester)
                                · {{ $semester->sem_name }}
                                @if($academicYear)
                                    · AY {{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label }}
                                @endif
                            @endif
                        </div>
                    </div>
                    <span class="badge badge-blue">{{ number_format($totalScheduledHours, 1) }} scheduled hours</span>
                </div>

                @if($topFaculty->isNotEmpty())
                    @php $leaderMaxHours = max(1, (float) $topFaculty->max('total_hours')); @endphp
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                        @foreach($topFaculty as $index => $leader)
                            @php
                                $rankStyles = [
                                    ['border-amber-400', 'bg-amber-50', 'text-amber-700', 'bg-amber-500'],
                                    ['border-slate-300', 'bg-slate-100', 'text-slate-600', 'bg-slate-400'],
                                    ['border-orange-300', 'bg-orange-50', 'text-orange-700', 'bg-orange-400'],
                                ][$index] ?? ['border-slate-200', 'bg-slate-50', 'text-slate-600', 'bg-slate-400'];
                                $loadPercent = min(100, (int) round(($leader['total_hours'] / $leaderMaxHours) * 100));
                            @endphp
                            <article class="rounded-2xl border-2 {{ $rankStyles[0] }} bg-white p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                <div class="mb-4 flex items-start justify-between gap-3">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $rankStyles[1] }} text-sm font-extrabold {{ $rankStyles[2] }}">#{{ $index + 1 }}</span>
                                        <div class="min-w-0">
                                            <h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $leader['name'] }}">{{ $leader['name'] }}</h3>
                                            <p class="truncate text-[11px] text-slate-500" title="{{ $leader['college'] }} · {{ $leader['department'] }}">{{ $leader['college'] }} · {{ $leader['department'] }}</p>
                                        </div>
                                    </div>
                                    <span class="shrink-0 rounded-full {{ $rankStyles[1] }} px-2 py-1 text-[9px] font-bold uppercase tracking-wide {{ $rankStyles[2] }}">#{{ $index + 1 }}</span>
                                </div>

                                <div class="flex items-baseline justify-between gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Weekly scheduled load</span>
                                    <span class="text-lg font-extrabold text-slate-900">{{ number_format($leader['total_hours'], 1) }}<span class="ml-1 text-xs font-semibold text-slate-400">hrs</span></span>
                                </div>
                                <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-100">
                                    <div class="h-full rounded-full {{ $rankStyles[3] }} transition-all" style="width:{{ $loadPercent }}%"></div>
                                </div>

                                <div class="mt-3 grid grid-cols-3 divide-x divide-slate-200 rounded-xl bg-slate-50 py-2.5 text-center">
                                    <div><div class="text-[9px] font-bold uppercase text-slate-400">Day</div><div class="text-xs font-extrabold text-emerald-600">{{ number_format($leader['day_hours'], 1) }}h</div></div>
                                    <div><div class="text-[9px] font-bold uppercase text-slate-400">Evening</div><div class="text-xs font-extrabold text-indigo-600">{{ number_format($leader['evening_hours'], 1) }}h</div></div>
                                    <div><div class="text-[9px] font-bold uppercase text-slate-400">Classes</div><div class="text-xs font-extrabold text-slate-700">{{ $leader['classes'] }}</div></div>
                                </div>
                                @if($leader['courses']->isNotEmpty())
                                    <p class="mt-3 truncate text-[10px] text-slate-500" title="{{ $leader['courses']->implode(', ') }}">Courses: {{ $leader['courses']->implode(', ') }}</p>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50 px-4 py-8 text-center">
                        <div class="text-sm font-semibold text-slate-600">No active schedules found for this semester.</div>
                        <div class="mt-1 text-xs text-slate-400">The faculty ranking will appear when active schedules have been plotted.</div>
                    </div>
                @endif
            </div>

            @php
                $weeklyPeakDay = $weeklyPattern->sortByDesc('sections')->first();
                $weeklyMaxSections = max(1, (int) $weeklyPattern->max('sections'));
                $weeklyPeakRoomSections = (int) $weeklyPattern
                    ->map(fn ($day) => $day['most_used_room']['sections_count'] ?? 0)
                    ->max();
            @endphp
            <section class="card mb-4 admin-weekly-pattern" id="admin-weekly-pattern">
                <div class="card-header">
                    <div>
                        <div class="card-title">Sections Scheduled by Day</div>
                        <div class="card-sub">Distinct sections with active schedules across all colleges</div>
                    </div>
                    <div class="flex items-center gap-1 rounded-xl bg-slate-100 p-1" role="group" aria-label="Weekly pattern display">
                        <button type="button" class="weekly-pattern-mode active" data-mode="sections" aria-pressed="true">Sections</button>
                        <button type="button" class="weekly-pattern-mode" data-mode="rooms" aria-pressed="false">Room Util.</button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4 lg:grid-cols-7">
                    @foreach($weeklyPattern as $day)
                        @php
                            $dayPercent = min(100, (int) round(($day['sections'] / $weeklyMaxSections) * 100));
                            $roomSections = (int) ($day['most_used_room']['sections_count'] ?? 0);
                            $roomPercent = $weeklyPeakRoomSections > 0 ? min(100, (int) round(($roomSections / $weeklyPeakRoomSections) * 100)) : 0;
                            $isPeakDay = $day['sections'] > 0 && $weeklyPeakDay && $day['sections'] === $weeklyPeakDay['sections'];
                            $isPeakRoom = $roomSections > 0 && $roomSections === $weeklyPeakRoomSections;
                            $isClosedDay = $day['sections'] <= 0;
                            $mostUsedRoom = $day['most_used_room'];
                        @endphp
                        <article class="weekly-pattern-day rounded-xl border {{ $isPeakDay ? 'border-blue-300 bg-blue-50' : ($day['sections'] > 0 ? 'border-blue-100 bg-slate-50' : 'border-slate-100 bg-slate-50') }} p-2.5 transition hover:border-blue-200"
                            data-classes="{{ $day['classes'] }}"
                            data-sections="{{ $day['sections'] }}"
                            data-room-name="{{ $mostUsedRoom['name'] ?? 'No room used' }}"
                            data-room-sections="{{ $roomSections }}"
                            data-room-classes="{{ $mostUsedRoom['class_count'] ?? 0 }}"
                            data-room-section-list="{{ isset($mostUsedRoom['sections']) ? $mostUsedRoom['sections']->take(4)->implode(', ') : '' }}"
                            data-section-width="{{ $dayPercent }}"
                            data-room-width="{{ $roomPercent }}"
                            data-section-closed-label="{{ $isClosedDay ? 'No active sections' : $day['classes'] . ' scheduled classes' }}"
                            data-closed="{{ $isClosedDay ? 'true' : 'false' }}">
                            <div class="flex items-center justify-between gap-1">
                                <span class="text-[10px] font-extrabold tracking-wide text-slate-500">{{ $day['short'] }}</span>
                                @if($isPeakDay)<span class="weekly-pattern-section-peak rounded bg-blue-600 px-1.5 py-0.5 text-[8px] font-extrabold text-white">PEAK</span>@endif
                                @if($isPeakRoom)<span class="weekly-pattern-room-peak rounded bg-emerald-600 px-1.5 py-0.5 text-[8px] font-extrabold text-white">ROOM PEAK</span>@endif
                                @if(!$isPeakDay && !$isPeakRoom)<span class="text-[9px] text-slate-400">{{ $day['sections'] }} {{ Str::plural('section', $day['sections']) }}</span>@endif
                            </div>
                            <div class="weekly-pattern-value mt-2 truncate text-xl font-extrabold {{ $day['sections'] > 0 ? 'text-slate-900' : 'text-slate-300' }}">{{ $day['sections'] }} {{ Str::plural('section', $day['sections']) }}</div>
                            <div class="weekly-pattern-track mt-2 h-5 overflow-hidden rounded bg-white">
                                <div class="weekly-pattern-bar h-full rounded bg-blue-600 transition-all" style="width:{{ $dayPercent }}%"></div>
                            </div>
                            <div class="weekly-pattern-caption mt-1 truncate text-[9px] text-slate-500">{{ $isClosedDay ? 'No active sections' : $day['classes'] . ' scheduled classes' }}</div>
                            <div class="weekly-pattern-room-caption mt-1">
                                <div class="weekly-pattern-room-name truncate text-[10px] font-bold text-emerald-700">{{ $mostUsedRoom['name'] ?? 'No room used' }}</div>
                                <div class="weekly-pattern-room-stats text-[9px] text-slate-500">{{ $roomSections }} {{ Str::plural('section', $roomSections) }} · {{ $mostUsedRoom['class_count'] ?? 0 }} classes</div>
                                <div class="weekly-pattern-room-sections mt-0.5 truncate text-[8px] text-slate-400" title="{{ isset($mostUsedRoom['sections']) ? $mostUsedRoom['sections']->implode(', ') : '' }}">{{ isset($mostUsedRoom['sections']) ? $mostUsedRoom['sections']->take(3)->implode(', ') : '' }}</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            {{-- ══ USER ACCOUNTS + SYSTEM INFO ══ --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 mb-4">

                {{-- User Accounts Table --}}
                <div class="col-span-1 xl:col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">User Accounts</div>
                            <div class="card-sub">All registered system users</div>
                        </div>
                        <span class="badge badge-blue">{{ $totalUsers }} Total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $roleLabels = [
                                        'faculty'          => ['Faculty',     'badge-grey'],
                                        'department_chair' => ['Dept. Chair', 'badge-amber'],
                                        'dean'             => ['Dean',        'badge-blue'],
                                        'system_admin'     => ['System Admin','badge-navy'],
                                    ];
                                @endphp
                                @forelse($users as $user)
                                    @php
                                        [$roleLabel, $roleBadgeClass] = $roleLabels[$user->usr_role] ?? [ucfirst($user->usr_role), 'badge-grey'];
                                    @endphp
                                    <tr>
                                        <td class="font-semibold">{{ $user->usr_name }}</td>
                                        <td><span class="badge {{ $roleBadgeClass }}">{{ $roleLabel }}</span></td>
                                        <td>
                                            <span class="badge {{ $user->usr_is_active ? 'badge-green' : 'badge-amber' }}">
                                                {{ $user->usr_is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.users') }}"
                                               class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-[11px] font-semibold no-underline hover:bg-slate-200 transition">
                                                Edit
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-6 text-slate-400">No user accounts found yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($users, 'total'))
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 px-1">
                        <div class="text-[12px] text-slate-400">
                            Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                            of {{ $users->total() }}
                        </div>
                        <div class="flex items-center gap-1.5">
                            <a href="{{ $users->previousPageUrl() ?? '#' }}"
                               class="btn btn-secondary text-[12px] {{ $users->onFirstPage() ? 'opacity-40 pointer-events-none' : '' }}">
                                ← Prev
                            </a>
                            <span class="text-[12px] text-slate-500 px-1">
                                {{ $users->currentPage() }} / {{ max(1, $users->lastPage()) }}
                            </span>
                            <a href="{{ $users->nextPageUrl() ?? '#' }}"
                               class="btn btn-secondary text-[12px] {{ !$users->hasMorePages() ? 'opacity-40 pointer-events-none' : '' }}">
                                Next →
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- System Info + Role Distribution --}}
                <div class="col-span-1 xl:col-span-4 flex flex-col gap-3">

                    <div class="card">
                        <div class="card-title mb-3">System Info</div>
                        <div class="text-[12px] text-slate-500 leading-[1.8]">
                            <div><b class="text-slate-700">App environment:</b> {{ ucfirst(app()->environment()) }}</div>
                            <div><b class="text-slate-700">Database driver:</b> {{ strtoupper(config('database.default')) }}</div>
                            <div><b class="text-slate-700">Current term:</b> {{ $semester->sem_name ?? 'Not configured' }}</div>
                            <div><b class="text-slate-700">Active schedule entries:</b> {{ number_format($scheduleRowsCount) }}</div>
                            <div><b class="text-slate-700">Weekly plotted hours:</b> {{ number_format($totalScheduledHours, 1) }}</div>
                        </div>
                    </div>

                    <div class="card flex-1">
                        <div class="card-title mb-3">Role Distribution</div>
                        @php $pct = fn($count) => $totalUsers > 0 ? round(($count / $totalUsers) * 100) : 0; @endphp

                        @foreach([
                            ['Faculty Members',  'faculty',          'bg-blue-600'],
                            ['Dept. Chairs',     'department_chair', 'bg-amber-500'],
                            ['Dean',             'dean',             'bg-cyan-600'],
                            ['Tech Admin',       'system_admin',     'bg-slate-800'],
                        ] as [$label, $key, $color])
                        <div class="workload-item">
                            <div class="workload-header">
                                <span class="workload-name">{{ $label }}</span>
                                <span class="workload-val text-slate-500">{{ $roleCounts[$key] ?? 0 }}</span>
                            </div>
                            <div class="workload-bar">
                                <div class="workload-fill {{ $color }}" style="width:{{ $pct($roleCounts[$key] ?? 0) }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- ══ SECTIONS + SCHEDULE COMPLETION ══ --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 mb-4">

                <div class="col-span-1 xl:col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Current Sections</div>
                            <div class="card-sub">
                                AY {{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label ?? 'N/A' }} · {{ $semester->sem_name ?? 'N/A' }}
                            </div>
                        </div>
                        <span class="badge badge-blue">{{ $currentTermSectionCount }} This Term</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table" id="dash-sections-table">
                            <thead>
                                <tr>
                                    @foreach(['Section','Program','Year','Students','Department Assigned','Status'] as $h)
                                    <th>{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($section as $sec)
                                <tr class="dash-sec-row">
                                    <td class="font-semibold">{{ $sec->sec_name }}</td>
                                    <td>{{ $sec->program->dept_code ?? $sec->program->prog_code ?? 'N/A' }}</td>
                                    <td>{{ $sec->sec_year_level }}</td>
                                    <td>{{ $sec->sec_no_of_student }}</td>
                                    <td>{{ $sec->program->dept_name ?? $sec->program->prog_name ?? 'Not assigned' }}</td>
                                    <td>
                                        @php
                                            $scheduleStatusBadge = match($sec->schedule_progress_status) {
                                                'Fully Scheduled' => 'badge-green',
                                                'In Progress' => 'badge-amber',
                                                default => 'badge-red',
                                            };
                                        @endphp
                                        <span class="badge {{ $scheduleStatusBadge }}" title="{{ $sec->schedule_load_plotted }} of {{ $sec->schedule_load_total }} assigned courses scheduled">
                                            {{ $sec->schedule_progress_status }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="dash-sec-empty"><td colspan="6" class="text-center py-6 text-slate-400">No sections found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 px-1">
                        <div id="dash-sec-page-info" class="text-[12px] text-slate-400"></div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" id="dash-sec-prev" onclick="dashPage('sec', -1)" class="btn btn-secondary text-[12px]">← Prev</button>
                            <div id="dash-sec-nums" class="flex gap-1"></div>
                            <button type="button" id="dash-sec-next" onclick="dashPage('sec', 1)" class="btn btn-secondary text-[12px]">Next →</button>
                        </div>
                    </div>
                </div>

                {{-- Schedule Completion --}}
                <div class="col-span-1 xl:col-span-4 card">
                    <div class="card-title mb-0.5">Schedule Completion</div>
                    <div class="card-sub mb-4">Percent of assigned courses with an active schedule</div>

                    @foreach($program as $programs)
                    @php
                        $pcolor = match($programs['color']) {
                            'blue'  => 'bg-blue-600',
                            'amber' => 'bg-amber-500',
                            'teal'  => 'bg-cyan-600',
                            'red'   => 'bg-red-600',
                            'green' => 'bg-green-600',
                            default => 'bg-slate-500',
                        };
                        $tcolor = match($programs['color']) {
                            'blue'  => 'text-blue-600',
                            'amber' => 'text-amber-500',
                            'teal'  => 'text-cyan-600',
                            'red'   => 'text-red-600',
                            'green' => 'text-green-600',
                            default => 'text-slate-500',
                        };
                    @endphp
                    <div class="workload-item">
                        <div class="workload-header">
                            <span class="workload-name">{{ $programs['name'] }}</span>
                            <span class="workload-val {{ $tcolor }}">{{ $programs['percent'] }}%</span>
                        </div>
                        <div class="workload-bar">
                            <div class="workload-fill {{ $pcolor }}" style="width:{{ $programs['percent'] }}%"></div>
                        </div>
                    </div>
                    @endforeach

                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <div class="text-[12px] font-bold text-slate-500 mb-2">Sections Summary</div>
                        <div class="flex gap-2">
                            <div class="flex-1 text-center p-2 rounded-xl bg-green-50">
                                <div class="text-[20px] font-extrabold text-green-600">{{ $scheduledCount }}</div>
                                <div class="text-[10px] font-semibold text-green-600">Fully Scheduled</div>
                            </div>
                            <div class="flex-1 text-center p-2 rounded-xl bg-amber-50">
                                <div class="text-[20px] font-extrabold text-amber-500">{{ $inProgressCount }}</div>
                                <div class="text-[10px] font-semibold text-amber-500">In Progress</div>
                            </div>
                            <div class="flex-1 text-center p-2 rounded-xl bg-red-50">
                                <div class="text-[20px] font-extrabold text-red-600">{{ $unscheduledCount }}</div>
                                <div class="text-[10px] font-semibold text-red-600">Unscheduled</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ══ SUBJECTS + ROOM UTILIZATION ══ --}}
            <div class="grid grid-cols-1 xl:grid-cols-12 gap-3 mb-4">

                <div class="col-span-1 xl:col-span-8 card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Subjects Offered</div>
                            <div class="card-sub">Current semester — all programs</div>
                        </div>
                        <span class="badge badge-blue">{{ $subjectsOffered }} Total</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    @foreach(['Code','Subject','Units','Program','Status'] as $h)
                                    <th>{{ $h }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($subject as $subjects)
                                <tr class="dash-subj-row">
                                    <td><span class="font-mono text-[12px] text-slate-600">{{ $subjects->course_code ?? $subjects->subj_code }}</span></td>
                                    <td class="font-semibold">{{ $subjects->course_name ?? $subjects->subj_name }}</td>
                                    <td>{{ $subjects->course_units ?? $subjects->subj_units ?? 0 }}</td>
                                    <td>{{ $subjects->program->dept_name ?? $subjects->program->prog_name ?? $subjects->department->dept_name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge {{ ($subjects->course_is_active ?? $subjects->subj_is_active ?? true) ? 'badge-green' : 'badge-red' }}">
                                            {{ ($subjects->course_is_active ?? $subjects->subj_is_active ?? true) ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr class="dash-subj-empty"><td colspan="5" class="text-center py-6 text-slate-400">No subjects found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 px-1">
                        <div id="dash-subj-page-info" class="text-[12px] text-slate-400"></div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" id="dash-subj-prev" onclick="dashPage('subj', -1)" class="btn btn-secondary text-[12px]">← Prev</button>
                            <div id="dash-subj-nums" class="flex gap-1"></div>
                            <button type="button" id="dash-subj-next" onclick="dashPage('subj', 1)" class="btn btn-secondary text-[12px]">Next →</button>
                        </div>
                    </div>
                </div>

                {{-- Room Utilization --}}
                <div class="col-span-1 xl:col-span-4 card">
                    <div class="card-title mb-0.5">Room Utilization</div>
                    <div class="card-sub mb-3">This week</div>

                    <div id="room-util-track" class="overflow-hidden relative">
                        <div id="room-util-list" class="flex flex-col transition-transform duration-400">
                            @foreach($room as $rooms)
                            @php
                                $rc = match($rooms['color']) {
                                    'blue'  => ['bg-blue-600',  'text-blue-600'],
                                    'amber' => ['bg-amber-500', 'text-amber-500'],
                                    'green' => ['bg-green-600', 'text-green-600'],
                                    'red'   => ['bg-red-600',   'text-red-600'],
                                    default => ['bg-slate-500', 'text-slate-500'],
                                };
                            @endphp
                            <div class="workload-item">
                                <div class="workload-header">
                                    <span class="workload-name">{{ $rooms['name'] }}</span>
                                    <span class="workload-val {{ $rc[1] }}">
                                        {{ $rooms['count'] }} {{ Str::plural('class', $rooms['count']) }}
                                    </span>
                                </div>
                                <div class="workload-bar">
                                    <div class="workload-fill {{ $rc[0] }}" style="width:{{ $rooms['percent'] }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-center items-center gap-2.5 mt-2.5">
                        <button id="room-util-prev"
                            class="px-3 py-1 text-[12px] border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
                            onclick="roomUtilPage(-1)">‹ Prev</button>
                        <span id="room-util-indicator" class="text-[12px] text-slate-400"></span>
                        <button id="room-util-next"
                            class="px-3 py-1 text-[12px] border border-slate-200 rounded-lg bg-slate-50 hover:bg-slate-100 transition"
                            onclick="roomUtilPage(1)">Next ›</button>
                    </div>

                    <div class="flex justify-between mt-4 pt-4 border-t border-slate-100 text-[12px]">
                        <div>
                            <div class="text-slate-400">Total Rooms</div>
                            <div class="text-[18px] font-extrabold text-slate-900">{{ $totalRooms }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400">In Use</div>
                            <div class="text-[18px] font-extrabold text-blue-600">{{ $roomsInUse }}</div>
                        </div>
                        <div>
                            <div class="text-slate-400">Available</div>
                            <div class="text-[18px] font-extrabold text-green-600">{{ $roomsAvailable }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <footer class="admin-dashboard-footer">
                <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                    <span class="admin-footer-brand">SKEDYUL</span>
                    <span class="text-slate-300">·</span>
                    <span>Academic Scheduling & Faculty Workload System</span>
                </div>
                <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                    <span>{{ $academicYear->ay_academic_year ?? $academicYear->ay_year_label ?? 'Academic year not configured' }} · {{ $semester->sem_name ?? 'Semester not configured' }}</span>
                    <span class="admin-footer-status"><span></span> Admin workspace</span>
                </div>
            </footer>
        </div>{{-- end page-content --}}
    </div>{{-- end app-main --}}
</div>{{-- end app-shell --}}

<script>
(function() {
    const perPage = 5;
    const list = document.getElementById('room-util-list');
    if (!list) return;
    const items = Array.from(list.children);
    const total = items.length;
    if (total === 0) return;
    const pages = Math.ceil(total / perPage);
    let current = 0;
    function getItemHeight() {
        const first = items[0];
        const style = window.getComputedStyle(first);
        return first.offsetHeight + (parseFloat(style.marginBottom) || 0);
    }
    function render() {
        const itemHeight = getItemHeight();
        const track = document.getElementById('room-util-track');
        track.style.height = (perPage * itemHeight) + 'px';
        list.style.transform = `translateY(-${current * perPage * itemHeight}px)`;
        document.getElementById('room-util-indicator').textContent = `Page ${current + 1} of ${pages}`;
        document.getElementById('room-util-prev').disabled = current === 0;
        document.getElementById('room-util-next').disabled = current === pages - 1;
    }
    window.roomUtilPage = function(dir) {
        const next = current + dir;
        if (next < 0 || next >= pages) return;
        current = next;
        render();
    };
    requestAnimationFrame(render);
    window.addEventListener('resize', render);
})();

(function() {
    const quotes = [
        { text: "Education is the most powerful weapon which you can use to change the world.", author: "Nelson Mandela" },
        { text: "The beautiful thing about learning is that no one can take it away from you.", author: "B.B. King" },
        { text: "An investment in knowledge pays the best interest.", author: "Benjamin Franklin" }
    ];
    const textEl   = document.getElementById('quote-text');
    const authorEl = document.getElementById('quote-author');
    const dotsEl   = document.getElementById('quote-dots');
    const prevBtn  = document.getElementById('quote-prev');
    const nextBtn  = document.getElementById('quote-next');
    if (!textEl || !dotsEl) return;
    let current = 0;
    function renderDots() {
        dotsEl.innerHTML = quotes.map((_, i) =>
            `<span style="width:6px;height:6px;border-radius:50%;display:inline-block;background:rgba(255,255,255,${i === current ? '.9' : '.25'});"></span>`
        ).join('');
    }
    function renderQuote() {
        textEl.textContent = `"${quotes[current].text}"`;
        authorEl.textContent = `— ${quotes[current].author}`;
        renderDots();
    }
    prevBtn.addEventListener('click', () => { current = (current - 1 + quotes.length) % quotes.length; renderQuote(); });
    nextBtn.addEventListener('click', () => { current = (current + 1) % quotes.length; renderQuote(); });
    renderQuote();
})();
</script>

<script>
(function () {
  const PER = 8;
  const state = { sec: 1, subj: 1 };

  function rows(kind) {
    const sel = kind === 'sec' ? 'tr.dash-sec-row' : 'tr.dash-subj-row';
    return Array.from(document.querySelectorAll(sel));
  }

  function render(kind) {
    const list = rows(kind);
    const total = list.length;
    const pages = Math.max(1, Math.ceil(total / PER));
    if (state[kind] > pages) state[kind] = pages;
    if (state[kind] < 1) state[kind] = 1;
    const start = (state[kind] - 1) * PER;
    const end = start + PER;
    list.forEach((row, i) => {
      row.style.display = (i >= start && i < end) ? '' : 'none';
    });
    const from = total === 0 ? 0 : start + 1;
    const to = Math.min(end, total);
    const info = document.getElementById(kind === 'sec' ? 'dash-sec-page-info' : 'dash-subj-page-info');
    if (info) {
      info.textContent = total === 0
        ? 'No records'
        : ('Showing ' + from + '–' + to + ' of ' + total + ' (page ' + state[kind] + '/' + pages + ')');
    }
    const prev = document.getElementById(kind === 'sec' ? 'dash-sec-prev' : 'dash-subj-prev');
    const next = document.getElementById(kind === 'sec' ? 'dash-sec-next' : 'dash-subj-next');
    if (prev) {
      prev.disabled = state[kind] <= 1;
      prev.classList.toggle('opacity-40', state[kind] <= 1);
      prev.classList.toggle('pointer-events-none', state[kind] <= 1);
    }
    if (next) {
      next.disabled = state[kind] >= pages;
      next.classList.toggle('opacity-40', state[kind] >= pages);
      next.classList.toggle('pointer-events-none', state[kind] >= pages);
    }
    const nums = document.getElementById(kind === 'sec' ? 'dash-sec-nums' : 'dash-subj-nums');
    if (nums) {
      nums.innerHTML = '';
      const a = Math.max(1, state[kind] - 2);
      const b = Math.min(pages, state[kind] + 2);
      for (let p = a; p <= b; p++) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.textContent = String(p);
        btn.className = 'btn text-[12px] min-w-[32px] ' + (p === state[kind] ? 'btn-primary' : 'btn-secondary');
        btn.addEventListener('click', function () { state[kind] = p; render(kind); });
        nums.appendChild(btn);
      }
    }
  }

  window.dashPage = function (kind, dir) {
    state[kind] += dir;
    render(kind);
  };

  document.addEventListener('DOMContentLoaded', function () {
    render('sec');
    render('subj');
  });
})();
</script>

<script>
(function () {
    document.querySelectorAll('[data-college-filter]').forEach((button) => {
        button.addEventListener('click', () => {
            const selectedCollege = button.dataset.collegeFilter;
            document.querySelectorAll('[data-college-filter]').forEach((tab) => {
                const selected = tab === button;
                tab.classList.toggle('active', selected);
                tab.setAttribute('aria-selected', selected ? 'true' : 'false');
            });
            document.querySelectorAll('.faculty-distribution-dept, .faculty-college-quota').forEach((item) => {
                item.hidden = selectedCollege !== 'all' && item.dataset.college !== selectedCollege;
            });
        });
    });

    const weeklyCard = document.getElementById('admin-weekly-pattern');
    if (!weeklyCard) return;

    weeklyCard.querySelectorAll('[data-mode]').forEach((button) => {
        button.addEventListener('click', () => {
            const roomMode = button.dataset.mode === 'rooms';
            weeklyCard.classList.toggle('room-util-active', roomMode);
            weeklyCard.querySelectorAll('[data-mode]').forEach((modeButton) => {
                const selected = modeButton === button;
                modeButton.classList.toggle('active', selected);
                modeButton.setAttribute('aria-pressed', selected ? 'true' : 'false');
            });

            weeklyCard.querySelectorAll('.weekly-pattern-day').forEach((day) => {
                const value = roomMode ? Number(day.dataset.roomSections || 0) : Number(day.dataset.sections || 0);
                const width = roomMode ? day.dataset.roomWidth : day.dataset.sectionWidth;
                const valueElement = day.querySelector('.weekly-pattern-value');
                const bar = day.querySelector('.weekly-pattern-bar');
                if (valueElement) {
                    valueElement.textContent = roomMode
                        ? (value > 0 ? day.dataset.roomName : 'No room used')
                        : `${value} ${value === 1 ? 'section' : 'sections'}`;
                    valueElement.classList.toggle('text-sm', roomMode);
                    valueElement.classList.toggle('text-xl', !roomMode);
                    valueElement.classList.toggle('text-slate-300', value === 0);
                    valueElement.classList.toggle('text-slate-900', value > 0 && !roomMode);
                    valueElement.classList.toggle('text-emerald-700', value > 0 && roomMode);
                }
                if (bar) bar.style.width = `${width || 0}%`;
                const sectionCaption = day.querySelector('.weekly-pattern-caption');
                if (sectionCaption) sectionCaption.textContent = day.dataset.sectionClosedLabel || '';
                const roomName = day.querySelector('.weekly-pattern-room-name');
                const roomStats = day.querySelector('.weekly-pattern-room-stats');
                const roomSections = day.querySelector('.weekly-pattern-room-sections');
                const sectionCount = Number(day.dataset.roomSections || 0);
                const classCount = Number(day.dataset.roomClasses || 0);
                if (roomName) roomName.textContent = sectionCount > 0 ? (day.dataset.roomName || 'No room used') : 'No room used';
                if (roomStats) roomStats.textContent = sectionCount > 0
                    ? `${sectionCount} ${sectionCount === 1 ? 'section' : 'sections'} · ${classCount} classes`
                    : 'No scheduled rooms';
                if (roomSections) {
                    roomSections.textContent = sectionCount > 0 ? (day.dataset.roomSectionList || '') : '';
                    roomSections.title = roomSections.textContent;
                }
                day.classList.toggle('opacity-60', roomMode && value === 0);
            });
        });
    });
})();
</script>

</body>
</html>
