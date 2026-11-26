<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Faculty Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #faculty-load-table tbody tr[data-load-row],
        #faculty-load-table tbody tr[data-load-row]>td {
            background-color: #fff !important;
        }
    </style>
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    <div class="app-shell">

        @include('partials.chair_sidebar')

        <div class="app-main relative">

            <div class="topbar">
                <div class="topbar-title">Faculty Management</div>
                <div class="flex items-center gap-2.5">
                    <span class="badge badge-blue text-[11px]">
                        {{ $program->prog_code ?? ($department->dept_code ?? 'DEPT') }}
                        @if ($academicYear)
                            &middot; {{ $academicYear->ay_academic_year }}
                        @endif
                        @if ($semester)
                            &middot; {{ $semester->sem_name }}
                        @endif
                    </span>
                    <div class="relative flex items-center gap-1.5 h-9 px-3.5 rounded-lg bg-slate-100 text-slate-600 text-[13px] font-semibold cursor-pointer hover:bg-slate-200 transition"
                        id="notif-btn" onclick="toggleNotifPanel()">
                        Notifications
                    </div>
                </div>
            </div>

            <div class="hidden absolute top-[60px] right-7 w-[340px] max-h-[420px] overflow-y-auto bg-white rounded-2xl border border-slate-200 shadow-[0_20px_60px_rgba(0,0,0,.15)] z-50"
                id="notif-panel">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                    <span class="text-[14px] font-bold text-slate-900">Notifications</span>
                </div>
                <div class="px-4 py-6 text-center text-[13px] text-slate-400">No notifications yet.</div>
            </div>

            <div class="page-content">

                @if (session('success'))
                    <div role="status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
                        {{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="mb-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">
                        {{ $errors->first() }}</div>
                @endif

                <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex flex-col items-start gap-3">
                        <p class="text-sm text-slate-500">Manage faculty accounts, employment types, and teaching loads
                            for your department.</p>
                        <button type="button" onclick="openAddFacultyModal()"
                            class="inline-flex shrink-0 items-center rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            + Add Faculty
                        </button>
                    </div>
                    <form method="GET" action="{{ route('chair.faculty_load') }}" class="flex flex-col gap-1">
                        <label for="faculty-load-semester" class="text-xs font-semibold text-slate-600">
                            Teaching load term
                        </label>
                        <select id="faculty-load-semester" name="semester" onchange="this.form.submit()"
                            class="min-w-[220px] rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800">
                            @foreach ($semesters as $option)
                                <option value="{{ $option->sem_id }}" @selected(($semester?->sem_id ?? '') === $option->sem_id)>
                                    {{ $option->label }}
                                </option>
                            @endforeach
                        </select>
                        <span class="max-w-[260px] text-[11px] text-slate-400">Loads change by semester; account status
                            and employment do not.</span>
                    </form>
                </div>

                <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="stat-card">
                        <div class="stat-card-bar bg-blue-600"></div>
                        <div class="stat-icon">👥</div>
                        <div class="stat-label">Department Faculty</div>
                        <div class="stat-value">{{ $accountStats['total'] }}</div>
                        <div class="stat-sub">Accounts in your department</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-bar bg-green-600"></div>
                        <div class="stat-icon">✅</div>
                        <div class="stat-label">Active Accounts</div>
                        <div class="stat-value">{{ $accountStats['active'] }}</div>
                        <div class="stat-sub">Available to sign in</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-bar bg-amber-500"></div>
                        <div class="stat-icon">⏸️</div>
                        <div class="stat-label">Inactive Accounts</div>
                        <div class="stat-value">{{ $accountStats['inactive'] }}</div>
                        <div class="stat-sub">Disabled or not linked</div>
                    </article>
                    <article class="stat-card">
                        <div class="stat-card-bar bg-cyan-600"></div>
                        <div class="stat-icon">🕒</div>
                        <div class="stat-label">Scheduled Hours</div>
                        <div class="stat-value">{{ number_format($facultyStats['scheduled_hours'], 1) }}h</div>
                        <div class="stat-sub">Selected teaching term</div>
                    </article>
                </div>

                <div class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex flex-wrap items-center gap-2">
                        <input id="faculty-load-search" type="search" placeholder="Search name, email, ID, or subject"
                            aria-label="Search faculty" class="field-input w-full sm:max-w-xs">
                        <div class="flex gap-1" role="group" aria-label="Filter faculty by account status">
                            <button type="button" data-account-status="all"
                                class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white">All</button>
                            <button type="button" data-account-status="active"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600">Active</button>
                            <button type="button" data-account-status="inactive"
                                class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-600">Inactive</button>
                        </div>
                        <select id="faculty-load-employment-filter" class="field-input w-full sm:w-auto"
                            aria-label="Filter by employment">
                            <option value="all">All employment types</option>
                            <option value="full_time">Full-time</option>
                            <option value="part_time">Part-time</option>
                        </select>
                        <select id="faculty-load-designation-filter" class="field-input w-full sm:w-auto"
                            aria-label="Filter by designation">
                            <option value="all">All designations</option>
                            <option value="designated">Has designation</option>
                            <option value="none">No designation</option>
                        </select>
                        <select id="faculty-load-status-filter" class="field-input w-full sm:w-auto"
                            aria-label="Filter by workload status">
                            <option value="all">All workload statuses</option>
                            <option value="attention">Near or over limit</option>
                            <option value="available">Within limit</option>
                            <option value="no_load">No load</option>
                        </select>
                        <div class="ml-auto flex flex-wrap items-center gap-2 text-xs text-slate-500">
                            <label for="faculty-load-page-size">Rows</label>
                            <select id="faculty-load-page-size"
                                class="rounded-lg border border-slate-200 bg-white px-2 py-1.5">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            <span id="faculty-load-page-info" aria-live="polite"></span>
                            <button type="button" id="faculty-load-prev"
                                class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Previous</button>
                            <button type="button" id="faculty-load-next"
                                class="rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 disabled:cursor-not-allowed disabled:opacity-40">Next</button>
                        </div>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="w-full min-w-[1100px] table-fixed text-left text-sm"
                                id="faculty-load-table">
                                <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                                    <tr>
                                        <th class="w-[20%] p-3">Faculty</th>
                                        <th class="w-[18%] p-3">Rank / designation</th>
                                        <th class="w-[12%] p-3">Employment</th>
                                        <th class="w-[9%] p-3">Day</th>
                                        <th class="w-[9%] p-3">Evening</th>
                                        <th class="w-[11%] p-3">Workload</th>
                                        <th class="w-[10%] whitespace-normal p-3">Override analytics</th>
                                        <th class="w-[11%] p-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($facultyLoad as $fl)
                                        <tr class="bg-white" data-load-row
                                            data-employment="{{ $fl['employment'] ?? 'full_time' }}"
                                            data-account-status="{{ $fl['is_active'] ? 'active' : 'inactive' }}"
                                            data-designated="{{ $fl['special_position'] ? '1' : '0' }}"
                                            data-load-status="{{ $fl['status_color'] === 'rose' ? 'attention' : ($fl['status_color'] === 'amber' ? 'attention' : ($fl['status'] === 'Within Limit' ? 'available' : 'no_load')) }}"
                                            data-search="{{ strtolower($fl['name'] . ' ' . $fl['email'] . ' ' . $fl['employee_id'] . ' ' . $fl['rank'] . ' ' . $fl['employment'] . ' ' . ($fl['special_position'] ?? '') . ' ' . $fl['subjects'] . ' ' . $fl['status'] . ' ' . collect($fl['workload_overrides'])->pluck('reason')->implode(' ')) }}">
                                            <td>
                                                <div class="flex min-w-[200px] items-center gap-3">
                                                    <span
                                                        class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 text-sm font-bold text-blue-700">
                                                        @if ($fl['profile_image'])
                                                            <img src="{{ \App\Services\ProfileImageStorage::url($fl['profile_image'], 'faculty_profile') }}"
                                                                alt="{{ $fl['name'] }} profile photo"
                                                                class="h-full w-full object-cover">@else{{ strtoupper(mb_substr($fl['name'], 0, 1)) }}
                                                        @endif
                                                    </span>
                                                    <span class="min-w-0"><span
                                                            class="block truncate font-semibold">{{ $fl['name'] }}</span><span
                                                            class="block truncate text-xs text-slate-500">{{ $fl['email'] ?? 'No linked account' }}</span></span>
                                                </div>
                                            </td>
                                            <td class="p-3">
                                                <div>{{ $fl['rank'] ?: 'Faculty' }}</div>
                                                <div class="text-xs text-slate-500">
                                                    {{ $fl['special_position'] ?: 'No special designation' }}</div>
                                            </td>
                                            <td class="p-3">
                                                {{ $fl['employment'] === 'part_time' ? 'Part-time' : 'Full-time' }}<div
                                                    class="mt-1"><span
                                                        class="badge {{ $fl['is_active'] ? 'badge-green' : 'badge-amber' }}">{{ $fl['is_active'] ? 'Active' : 'Inactive' }}</span>
                                                </div>
                                            </td>
                                            <td class="p-3"><strong
                                                    class="text-emerald-700">{{ number_format($fl['day']['total_hours'], 1) }}h</strong><span
                                                    class="text-xs text-slate-400"> /
                                                    {{ $fl['day']['max_hours'] }}h</span>
                                                @if ($fl['employment'] === 'part_time')
                                                    <div class="text-xs text-slate-400">Part of shared cap</div>
                                                @endif
                                            </td>
                                            <td class="p-3"><strong
                                                    class="text-indigo-700">{{ number_format($fl['evening']['total_hours'], 1) }}h</strong><span
                                                    class="text-xs text-slate-400"> /
                                                    {{ $fl['evening']['max_hours'] }}h</span>
                                                @if ($fl['employment'] === 'part_time')
                                                    <div class="text-xs text-slate-400">Part of shared cap</div>
                                                @endif
                                            </td>
                                            <td class="p-3"><span
                                                    class="badge {{ $fl['status_color'] === 'rose' ? 'badge-red' : ($fl['status_color'] === 'amber' ? 'badge-amber' : ($fl['status_color'] === 'emerald' ? 'badge-green' : 'badge-grey')) }}">{{ $fl['status'] }}</span>
                                            </td>
                                            <td class="w-[10%] p-3 break-words">
                                                @if ($fl['workload_override_count'] > 0)
                                                    <details>
                                                        <summary class="cursor-pointer font-semibold text-rose-700">
                                                            {{ $fl['workload_override_count'] }} recorded · View
                                                            reasons</summary>
                                                        <div class="mt-2 space-y-2">
                                                            @foreach ($fl['workload_overrides']->take(3) as $override)
                                                                <div
                                                                    class="rounded-lg border border-rose-100 bg-rose-50 p-2 text-xs">
                                                                    <div class="font-semibold">
                                                                        {{ $override['hours_after'] }} /
                                                                        {{ $override['cap_hours'] }}h ·
                                                                        {{ $override['designation'] }}</div>
                                                                    <p class="mt-1 text-slate-700">
                                                                        {{ $override['reason'] }}</p>
                                                                    <div class="mt-1 text-slate-500">By
                                                                        {{ $override['approved_by'] }} ·
                                                                        {{ $override['created_at']?->format('M j, Y') }}
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </details>
                                                @else
                                                    <span class="text-xs text-slate-400">None</span>
                                                @endif
                                            </td>
                                            <td class="w-[11%] min-w-[145px] p-3">
                                                @if ($fl['can_manage'])
                                                    <div class="flex justify-end gap-1.5">
                                                        @php($designationAction = $fl['special_position'] ? 'Edit designation' : 'Assign designation')
                                                        <button type="button" data-designation-edit
                                                            data-id="{{ $fl['id'] }}"
                                                            data-name="{{ $fl['name'] }}"
                                                            data-position="{{ $fl['special_position'] ?? '' }}"
                                                            data-employment="{{ $fl['employment'] }}"
                                                            title="{{ $designationAction }}"
                                                            aria-label="{{ $designationAction }}"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-blue-200 bg-blue-50 text-blue-700 transition hover:bg-blue-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                            <svg aria-hidden="true" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor"
                                                                stroke-width="1.8" class="h-4 w-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M12 3.5 14.6 9l6.1.8-4.4 4.3 1 6.1-5.3-2.9-5.3 2.9 1-6.1-4.4-4.3L9.4 9 12 3.5Z" />
                                                            </svg>
                                                        </button>
                                                        <button type="button" data-edit-faculty
                                                            data-id="{{ $fl['id'] }}"
                                                            data-first-name="{{ $fl['first_name'] }}"
                                                            data-middle-name="{{ $fl['middle_name'] }}"
                                                            data-last-name="{{ $fl['last_name'] }}"
                                                            data-suffix="{{ $fl['suffix'] }}"
                                                            data-email="{{ $fl['email'] }}"
                                                            data-employee-id="{{ $fl['employee_id'] }}"
                                                            data-phone="{{ $fl['phone_number'] }}"
                                                            data-address="{{ $fl['address'] }}"
                                                            data-rank="{{ $fl['rank'] }}"
                                                            data-employment="{{ $fl['employment'] ?? 'full_time' }}"
                                                            data-active="{{ $fl['is_active'] ? '1' : '0' }}"
                                                            data-position="{{ $fl['special_position'] ?? '' }}"
                                                            title="Edit faculty" aria-label="Edit faculty"
                                                            class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 transition hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                                            <svg aria-hidden="true" viewBox="0 0 24 24"
                                                                fill="none" stroke="currentColor"
                                                                stroke-width="1.8" class="h-4 w-4">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="m15.2 5.2 3.6 3.6M4 20l4.2-.9L19.2 8.1a2.5 2.5 0 0 0-3.5-3.5L4.7 15.6 4 20Z" />
                                                            </svg>
                                                        </button>
                                                        <form method="POST"
                                                            action="{{ route('chair.faculty_load.faculty.destroy', ['faculty' => $fl['id']]) }}"
                                                            onsubmit="return confirm('Delete this faculty account?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <input type="hidden" name="semester"
                                                                value="{{ $semester?->sem_id }}">
                                                            <button type="submit" title="Delete faculty account"
                                                                aria-label="Delete faculty account"
                                                                class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 text-rose-600 transition hover:bg-rose-100 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2">
                                                                <svg aria-hidden="true" viewBox="0 0 24 24"
                                                                    fill="none" stroke="currentColor"
                                                                    stroke-width="1.8" class="h-4 w-4">
                                                                    <path stroke-linecap="round"
                                                                        stroke-linejoin="round"
                                                                        d="M4 7h16M10 11v6m4-6v6M6 7l1 13h10l1-13M9 7V4h6v3" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                @else
                                                    <span class="text-xs text-slate-400">Managed by another
                                                        department</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="p-8 text-center text-slate-500">No faculty in
                                                this
                                                department yet.</td>
                                        </tr>
                                    @endforelse
                                    <tr id="faculty-load-no-results" class="hidden">
                                        <td colspan="8" class="p-8 text-center text-slate-500">No faculty match
                                            these filters.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div id="faculty-add-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4"
            role="dialog" aria-modal="true" aria-labelledby="faculty-add-title">
            <form id="faculty-add-form" method="POST" action="{{ route('chair.faculty_load.faculty.store') }}"
                class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
                @csrf
                <input type="hidden" name="semester" value="{{ $semester?->sem_id }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="faculty-add-title" class="text-lg font-bold text-slate-900">Add User Account</h2>
                        <p class="mt-1 text-sm text-slate-500">New faculty will be assigned to
                            {{ $program->dept_code ?? 'your department' }}{{ $department?->college_code ? ' · ' . $department->college_code : '' }}.
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary shrink-0"
                        onclick="closeFacultyModal('faculty-add-modal')">Close</button>
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                    <label class="text-sm font-medium text-slate-700">First Name
                        <input name="first_name" required maxlength="150" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Middle Name
                        <input name="middle_name" maxlength="150" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Last Name
                        <input name="last_name" required maxlength="150" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Suffix
                        <input name="suffix" maxlength="20" placeholder="Jr., Sr., III"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Email
                        <input name="email" type="email" required maxlength="255"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Temporary Password
                        <div class="mt-1 flex gap-2">
                            <input id="add-password" name="password" type="password" required minlength="8"
                                autocomplete="new-password" class="field-input min-w-0 flex-1">
                            <button type="button" class="btn btn-secondary shrink-0"
                                onclick="toggleFacultyPassword('add-password', this)">Show</button>
                        </div>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Confirm Password
                        <input name="password_confirmation" type="password" required minlength="8"
                            autocomplete="new-password" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Employee ID
                        <input name="employee_id" maxlength="255" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Rank / Title
                        <select name="rank" class="field-input mt-1 w-full">
                            <option value="">Select rank</option>
                            @foreach (['Instructor I', 'Instructor II', 'Instructor III', 'Assistant Professor I', 'Assistant Professor II', 'Assistant Professor III', 'Assistant Professor IV', 'Associate Professor I', 'Associate Professor II', 'Associate Professor III', 'Associate Professor IV', 'Associate Professor V', 'Professor I', 'Professor II', 'Professor III', 'Professor IV', 'Professor V', 'Professor VI', 'University Professor'] as $rank)
                                <option value="{{ $rank }}">{{ $rank }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Employment
                        <select name="employment_type" required class="field-input mt-1 w-full">
                            <option value="full_time">Full-time</option>
                            <option value="part_time">Part-time</option>
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Designation
                        <select name="special_position" class="field-input mt-1 w-full">
                            <option value="">None</option>
                            @foreach ($designations as $designation)
                                <option value="{{ $designation->name }}">{{ $designation->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="sm:col-span-2">
                        <label class="text-sm font-medium text-slate-700" for="add-phone-local">Phone number</label>
                        <div class="mt-1 flex gap-2">
                            <select id="add-phone-code" class="field-input w-32 shrink-0"
                                aria-label="Phone country code">
                                <option value="+63" selected>PH +63</option>
                                <option value="+1">US +1</option>
                                <option value="+44">UK +44</option>
                                <option value="+81">JP +81</option>
                                <option value="+82">KR +82</option>
                                <option value="+65">SG +65</option>
                                <option value="+60">MY +60</option>
                                <option value="+62">ID +62</option>
                            </select>
                            <input id="add-phone-local" type="tel" inputmode="numeric" maxlength="10" required
                                placeholder="9123456789" oninput="sanitizeFacultyPhone(this)"
                                class="field-input min-w-0 flex-1">
                            <input id="add-phone-full" name="phone_number" type="hidden">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Country code replaces the leading 0. Enter digits only.
                        </p>
                    </div>
                    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Address
                        <input name="address" maxlength="255" placeholder="Cebu City"
                            class="field-input mt-1 w-full">
                    </label>
                    <div class="sm:col-span-2 flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" class="btn btn-secondary"
                            onclick="closeFacultyModal('faculty-add-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Create faculty account</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="faculty-edit-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4"
            role="dialog" aria-modal="true" aria-labelledby="faculty-edit-title">
            <form id="faculty-edit-form" method="POST"
                class="max-h-[90vh] w-full max-w-4xl overflow-y-auto rounded-2xl bg-white p-6 shadow-2xl">
                @csrf
                @method('PUT')
                <input type="hidden" name="semester" value="{{ $semester?->sem_id }}">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <h2 id="faculty-edit-title" class="text-lg font-bold text-slate-900">Edit faculty details</h2>
                        <p class="mt-1 text-sm text-slate-500">Update profile information and account access.</p>
                    </div>
                    <button type="button" class="btn btn-secondary shrink-0"
                        onclick="closeFacultyModal('faculty-edit-modal')">Close</button>
                </div>
                <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <label class="text-sm font-medium text-slate-700">First Name
                        <input id="edit-first-name" name="first_name" required maxlength="150"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Middle Name
                        <input id="edit-middle-name" name="middle_name" maxlength="150"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Last Name
                        <input id="edit-last-name" name="last_name" required maxlength="150"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Suffix
                        <input id="edit-suffix" name="suffix" maxlength="20" class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700 sm:col-span-2">Email
                        <input id="edit-email" type="email" name="email" required maxlength="255"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Employee ID
                        <input id="edit-employee-id" name="employee_id" maxlength="255"
                            class="field-input mt-1 w-full">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Rank / Title
                        <select id="edit-rank" name="rank" class="field-input mt-1 w-full">
                            <option value="">Select rank</option>
                            @foreach (['Instructor I', 'Instructor II', 'Instructor III', 'Assistant Professor I', 'Assistant Professor II', 'Assistant Professor III', 'Assistant Professor IV', 'Associate Professor I', 'Associate Professor II', 'Associate Professor III', 'Associate Professor IV', 'Associate Professor V', 'Professor I', 'Professor II', 'Professor III', 'Professor IV', 'Professor V', 'Professor VI', 'University Professor'] as $rank)
                                <option value="{{ $rank }}">{{ $rank }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Employment
                        <select id="edit-employment" name="employment_type" required class="field-input mt-1 w-full">
                            <option value="full_time">Full-time</option>
                            <option value="part_time">Part-time</option>
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Account Status
                        <select id="edit-active" name="usr_is_active" required class="field-input mt-1 w-full">
                            <option value="1">Active</option>
                            <option value="0">Deactivated</option>
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">Designation
                        <select id="edit-special-position" name="special_position" class="field-input mt-1 w-full">
                            <option value="">None</option>
                            @foreach ($designations as $designation)
                                <option value="{{ $designation->name }}">{{ $designation->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label class="text-sm font-medium text-slate-700">College
                        <input value="{{ $department?->college_name }}" readonly
                            class="field-input mt-1 w-full bg-slate-50 text-slate-500">
                    </label>
                    <label class="text-sm font-medium text-slate-700">Department
                        <input value="{{ $program?->dept_name }}" readonly
                            class="field-input mt-1 w-full bg-slate-50 text-slate-500">
                    </label>
                    <div class="sm:col-span-2 lg:col-span-3">
                        <label class="text-sm font-medium text-slate-700" for="edit-phone-local">Phone Number</label>
                        <div class="mt-1 flex gap-2">
                            <select id="edit-phone-code" class="field-input w-32 shrink-0"
                                aria-label="Phone country code">
                                <option value="+63">PH +63</option>
                                <option value="+1">US +1</option>
                                <option value="+44">UK +44</option>
                                <option value="+81">JP +81</option>
                                <option value="+82">KR +82</option>
                                <option value="+65">SG +65</option>
                                <option value="+60">MY +60</option>
                                <option value="+62">ID +62</option>
                            </select>
                            <input id="edit-phone-local" type="tel" inputmode="numeric" maxlength="10" required
                                oninput="sanitizeFacultyPhone(this)" class="field-input min-w-0 flex-1">
                            <input id="edit-phone-full" name="phone_number" type="hidden">
                        </div>
                        <p class="mt-1 text-xs text-slate-400">Country code replaces the leading 0. Enter digits only.
                        </p>
                    </div>
                    <label class="text-sm font-medium text-slate-700 sm:col-span-2 lg:col-span-3">Address
                        <input id="edit-address" name="address" maxlength="255" class="field-input mt-1 w-full">
                    </label>
                    <div class="sm:col-span-2 lg:col-span-3 grid gap-3 sm:grid-cols-2">
                        <label class="text-sm font-medium text-slate-700">New Password <span
                                class="font-normal text-slate-400">(optional)</span>
                            <div class="mt-1 flex gap-2">
                                <input id="edit-password" name="password" type="password" minlength="8"
                                    autocomplete="new-password" placeholder="Leave blank to keep current password"
                                    class="field-input min-w-0 flex-1">
                                <button type="button" title="Show password" aria-label="Show password"
                                    class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 hover:bg-slate-50"
                                    onclick="toggleFacultyPassword('edit-password', this)">
                                    <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="1.8" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.5 12s3.2-6 9.5-6 9.5 6 9.5 6-3.2 6-9.5 6-9.5-6-9.5-6Z" />
                                        <circle cx="12" cy="12" r="2.5" />
                                    </svg>
                                </button>
                            </div>
                            <span class="mt-1 block text-xs text-slate-400">Enter a new password only if it needs to be
                                changed.</span>
                        </label>
                        <label class="text-sm font-medium text-slate-700">Confirm New Password
                            <input id="edit-password-confirmation" name="password_confirmation" type="password"
                                minlength="8" autocomplete="new-password" placeholder="Re-enter the new password"
                                class="field-input mt-1 w-full">
                        </label>
                    </div>
                    <div class="col-span-full flex justify-end gap-2 border-t border-slate-100 pt-4">
                        <button type="button" class="btn btn-secondary"
                            onclick="closeFacultyModal('faculty-edit-modal')">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save faculty details</button>
                    </div>
                </div>
            </form>
        </div>

        <div id="designation-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4"
            role="dialog" aria-modal="true" aria-labelledby="designation-modal-title"
            onclick="if(event.target===this)closeDesignationModal()">
            <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h2 id="designation-modal-title" class="text-lg font-bold text-slate-900">Assign special
                            designation</h2>
                        <p id="designation-modal-person" class="mt-1 text-sm text-slate-500"></p>
                    </div><button type="button"
                        class="rounded-lg bg-slate-100 px-3 py-2 text-sm font-semibold text-slate-600"
                        onclick="closeDesignationModal()">Close</button>
                </div>
                <form id="designation-form" method="POST" action="" class="mt-5">
                    @csrf
                    @method('PUT')
                    <label for="designation-select"
                        class="text-xs font-semibold uppercase tracking-wide text-slate-500">Special
                        designation</label>
                    <select id="designation-select" name="special_position" class="field-input mt-1 w-full">
                        <option value="">No special designation</option>
                        @foreach ($designations as $d)
                            <option value="{{ $d->name }}" data-max-hours="{{ $d->day_cap }}">
                                {{ $d->name }}</option>
                        @endforeach
                    </select>
                    <div id="designation-cap-note" class="mt-3 rounded-lg bg-blue-50 p-3 text-sm text-blue-800"></div>
                    <div class="mt-5 flex justify-end gap-2"><button type="button"
                            class="rounded-lg bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-600"
                            onclick="closeDesignationModal()">Cancel</button><button type="submit"
                            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save
                            designation</button></div>
                </form>
            </div>
        </div>

        <div class="toast" id="toast"><span id="toast-msg"></span></div>

        <script>
            const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

            function toggleNotifPanel() {
                document.getElementById('notif-panel').classList.toggle('hidden');
            }
            document.addEventListener('click', function(e) {
                const panel = document.getElementById('notif-panel');
                const btn = document.getElementById('notif-btn');
                if (panel && btn && !panel.contains(e.target) && !btn.contains(e.target)) {
                    panel.classList.add('hidden');
                }
            });

            (() => {
                const search = document.getElementById('faculty-load-search');
                const rows = [...document.querySelectorAll('[data-load-row]')];
                const noResults = document.getElementById('faculty-load-no-results');
                const info = document.getElementById('faculty-load-page-info');
                const previous = document.getElementById('faculty-load-prev');
                const next = document.getElementById('faculty-load-next');
                const pageSizeSelect = document.getElementById('faculty-load-page-size');
                const employmentFilter = document.getElementById('faculty-load-employment-filter');
                const designationFilter = document.getElementById('faculty-load-designation-filter');
                const statusFilter = document.getElementById('faculty-load-status-filter');
                const accountStatusButtons = [...document.querySelectorAll('[data-account-status]')];
                let pageSize = Number(pageSizeSelect.value);
                let page = 0;
                let accountStatus = 'all';

                function render() {
                    const query = search.value.trim().toLowerCase();
                    const employment = employmentFilter.value;
                    const designation = designationFilter.value;
                    const status = statusFilter.value;
                    const matches = rows.filter(row => row.dataset.search.includes(query) &&
                        (employment === 'all' || row.dataset.employment === employment) &&
                        (accountStatus === 'all' || row.dataset.accountStatus === accountStatus) &&
                        (designation === 'all' || (designation === 'designated' ? row.dataset.designated === '1' : row
                            .dataset.designated === '0')) &&
                        (status === 'all' || row.dataset.loadStatus === status));
                    const pageCount = Math.max(1, Math.ceil(matches.length / pageSize));
                    page = Math.min(page, pageCount - 1);
                    const start = page * pageSize;
                    rows.forEach(row => {
                        row.style.display = 'none';
                    });
                    matches.slice(start, start + pageSize).forEach(row => {
                        row.style.display = '';
                    });
                    noResults.classList.toggle('hidden', matches.length > 0 || rows.length === 0);
                    info.textContent = matches.length ?
                        `Showing ${start + 1}–${Math.min(start + pageSize, matches.length)} of ${matches.length}` :
                        'Showing 0 of 0';
                    previous.disabled = page === 0;
                    next.disabled = page >= pageCount - 1;
                }

                search.addEventListener('input', () => {
                    page = 0;
                    render();
                });
                employmentFilter.addEventListener('change', () => {
                    page = 0;
                    render();
                });
                designationFilter.addEventListener('change', () => {
                    page = 0;
                    render();
                });
                statusFilter.addEventListener('change', () => {
                    page = 0;
                    render();
                });
                accountStatusButtons.forEach(button => button.addEventListener('click', () => {
                    accountStatus = button.dataset.accountStatus;
                    accountStatusButtons.forEach(option => {
                        option.classList.toggle('bg-blue-600', option === button);
                        option.classList.toggle('text-white', option === button);
                        option.classList.toggle('border-slate-200', option !== button);
                        option.classList.toggle('bg-white', option !== button);
                        option.classList.toggle('text-slate-600', option !== button);
                    });
                    page = 0;
                    render();
                }));
                pageSizeSelect.addEventListener('change', () => {
                    pageSize = Number(pageSizeSelect.value);
                    page = 0;
                    render();
                });
                previous.addEventListener('click', () => {
                    page--;
                    render();
                });
                next.addEventListener('click', () => {
                    page++;
                    render();
                });
                render();
            })();

            const facultyEditForm = document.getElementById('faculty-edit-form');

            function openAddFacultyModal() {
                document.getElementById('faculty-add-modal').classList.remove('hidden');
                document.getElementById('faculty-add-modal').classList.add('flex');
            }

            function closeFacultyModal(id) {
                const modal = document.getElementById(id);
                modal.classList.add('hidden');
                modal.classList.remove('flex');
            }

            function toggleFacultyPassword(inputId, button) {
                const input = document.getElementById(inputId);
                const visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                const label = visible ? 'Hide password' : 'Show password';
                const text = button.querySelector('[data-password-toggle-label]');
                if (text) {
                    text.textContent = label;
                } else {
                    button.textContent = visible ? 'Hide' : 'Show';
                }
                button.title = label;
                button.setAttribute('aria-label', label);
                button.setAttribute('aria-pressed', visible ? 'true' : 'false');
            }

            function sanitizeFacultyPhone(input) {
                input.value = input.value.replace(/\D/g, '').slice(0, 10);
            }

            function setFacultyPhone(countryCodeId, localNumberId, storedNumber) {
                const countryCode = document.getElementById(countryCodeId);
                const localNumber = document.getElementById(localNumberId);
                const phone = storedNumber || '';
                const matchedCode = [...countryCode.options]
                    .map(option => option.value)
                    .sort((a, b) => b.length - a.length)
                    .find(code => phone.startsWith(code));
                countryCode.value = matchedCode || '+63';
                localNumber.value = matchedCode ?
                    phone.slice(matchedCode.length).replace(/^0/, '').replace(/\D/g, '') :
                    phone.replace(/\D/g, '').slice(-10);
            }

            function syncFacultyPhone(formId, countryCodeId, localNumberId, fullNumberId) {
                const form = document.getElementById(formId);
                const password = form.querySelector('[name="password"]');
                const confirmation = form.querySelector('[name="password_confirmation"]');
                if (password && confirmation) {
                    const updatePasswordConfirmation = () => {
                        confirmation.required = password.required || password.value.length > 0;
                        password.setCustomValidity(confirmation.value && confirmation.value !== password.value ?
                            'Passwords do not match.' :
                            '');
                        confirmation.setCustomValidity(password.value && confirmation.value !== password.value ?
                            'Passwords do not match.' :
                            '');
                    };
                    password.addEventListener('input', updatePasswordConfirmation);
                    confirmation.addEventListener('input', updatePasswordConfirmation);
                }
                form.addEventListener('submit', event => {
                    const countryCode = document.getElementById(countryCodeId).value;
                    const localNumber = document.getElementById(localNumberId).value.replace(/\D/g, '').replace(/^0/,
                        '');
                    document.getElementById(fullNumberId).value = `${countryCode}${localNumber}`;

                    if (password && confirmation) {
                        const hasPassword = password.value.length > 0;
                        const passwordsMismatch = (hasPassword || confirmation.value.length > 0) &&
                            password.value !== confirmation.value;
                        confirmation.required = password.required || hasPassword;
                        confirmation.setCustomValidity(passwordsMismatch ?
                            'Passwords do not match.' :
                            '');
                        password.setCustomValidity(passwordsMismatch ? 'Passwords do not match.' : '');
                        if (!form.reportValidity()) {
                            event.preventDefault();
                        }
                    }
                });
            }

            function setFacultySelectValue(select, value) {
                if (value && ![...select.options].some(option => option.value === value)) {
                    select.add(new Option(value, value), 1);
                }
                select.value = value || '';
            }
            syncFacultyPhone('faculty-add-form', 'add-phone-code', 'add-phone-local', 'add-phone-full');
            syncFacultyPhone('faculty-edit-form', 'edit-phone-code', 'edit-phone-local', 'edit-phone-full');
            document.querySelectorAll('[data-edit-faculty]').forEach(button => {
                button.addEventListener('click', () => {
                    facultyEditForm.action =
                        `{{ url('/chair/faculty-load/faculty') }}/${encodeURIComponent(button.dataset.id)}`;
                    document.getElementById('edit-first-name').value = button.dataset.firstName || '';
                    document.getElementById('edit-middle-name').value = button.dataset.middleName || '';
                    document.getElementById('edit-last-name').value = button.dataset.lastName || '';
                    document.getElementById('edit-suffix').value = button.dataset.suffix || '';
                    document.getElementById('edit-email').value = button.dataset.email || '';
                    document.getElementById('edit-employee-id').value = button.dataset.employeeId || '';
                    document.getElementById('edit-address').value = button.dataset.address || '';
                    setFacultyPhone('edit-phone-code', 'edit-phone-local', button.dataset.phone || '');
                    setFacultySelectValue(document.getElementById('edit-rank'), button.dataset.rank || '');
                    setFacultySelectValue(document.getElementById('edit-special-position'), button.dataset
                        .position || '');
                    document.getElementById('edit-employment').value = button.dataset.employment || 'full_time';
                    document.getElementById('edit-active').value = button.dataset.active || '0';
                    document.getElementById('edit-password').value = '';
                    document.getElementById('edit-password-confirmation').value = '';
                    document.getElementById('edit-password').type = 'password';
                    document.getElementById('edit-password').nextElementSibling.setAttribute('aria-label',
                        'Show password');
                    document.getElementById('edit-password').nextElementSibling.setAttribute('title',
                        'Show password');
                    document.getElementById('edit-password').nextElementSibling.setAttribute('aria-pressed',
                        'false');
                    document.getElementById('faculty-edit-modal').classList.remove('hidden');
                    document.getElementById('faculty-edit-modal').classList.add('flex');
                });
            });

            const designationModal = document.getElementById('designation-modal');
            const designationForm = document.getElementById('designation-form');
            const designationSelect = document.getElementById('designation-select');
            const designationCapNote = document.getElementById('designation-cap-note');
            let designationEmployment = 'full_time';

            function syncDesignationCap() {
                const option = designationSelect.selectedOptions[0];
                const positionCap = option?.dataset.maxHours || {{ \App\Services\ScheduleAssignmentService::TOTAL_MAX_HOURS }};
                designationCapNote.textContent = designationEmployment === 'part_time' ?
                    `Part-time faculty have a shared {{ \App\Services\ScheduleAssignmentService::PART_TIME_MAX_HOURS }}-hour limit across Day and Evening. The selected designation does not replace this shared limit.` :
                    `This designation sets the Day teaching limit to ${positionCap} hours. The Evening limit remains {{ \App\Services\ScheduleAssignmentService::EVENING_MAX_HOURS }} hours.`;
            }

            function openDesignationModal(button) {
                designationForm.action = `{{ url('/chair/faculty-load') }}/${button.dataset.id}/special-position`;
                document.getElementById('designation-modal-person').textContent = button.dataset.name;
                designationEmployment = button.dataset.employment;
                designationSelect.value = button.dataset.position || '';
                syncDesignationCap();
                designationModal.classList.remove('hidden');
                designationModal.classList.add('flex');
                designationSelect.focus();
            }

            function closeDesignationModal() {
                designationModal.classList.add('hidden');
                designationModal.classList.remove('flex');
            }
            document.querySelectorAll('[data-designation-edit]').forEach(button => button.addEventListener('click', () =>
                openDesignationModal(button)));
            designationSelect.addEventListener('change', syncDesignationCap);
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape') {
                    closeDesignationModal();
                    closeFacultyModal('faculty-add-modal');
                    closeFacultyModal('faculty-edit-modal');
                }
            });

            function showToast(msg) {
                const t = document.getElementById('toast');
                document.getElementById('toast-msg').textContent = msg;
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 3200);
            }
        </script>

</body>

</html>
