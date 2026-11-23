<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Dean Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

    <div id="screen-app" class="screen active" style="flex-direction:row;">

        <!-- Main -->
        <div class="main">

            @include('partials.dean_header', ['title' => 'Dean Departments'])

            <!-- DEPARTMENTS PAGE (now: straight to Programs, no department step) -->
            <div id="page-departments" class="page active">
                @if(session('success'))<div role="status" class="mx-5 mt-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">{{ session('success') }}</div>@endif
                @if(session('error'))<div role="alert" class="mx-5 mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">{{ session('error') }}</div>@endif
                @if($errors->any())<div role="alert" class="mx-5 mt-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>@endif
                <div id="selected-program-tools" class="hidden">
                @if($selectedDepartment)
                    <div class="mx-5 mt-5">
                        <h2 class="text-lg font-bold">{{ $selectedDepartment->dept_code }} · {{ $selectedDepartment->dept_name }}</h2>
                        <p class="mt-1 text-sm text-slate-500">Faculty roster and department chair · {{ $assignedCollege?->college_name }}</p>
                    </div>
                    <div class="mx-5 mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Sections</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $sectionCount }}</p><p class="mt-1 text-xs text-slate-500">In this department, across academic years</p></article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Assigned faculty</p><p class="mt-2 text-2xl font-bold text-slate-900">{{ $facultyTotal }}</p><p class="mt-1 text-xs text-slate-500">Faculty rostered in this department</p></article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Pending dean review</p><p class="mt-2 text-2xl font-bold text-amber-600">{{ $pendingReviewCount }}</p><p class="mt-1 text-xs text-slate-500">PBS/PBT schedules awaiting your review this semester</p></article>
                        <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Faculty workload alerts</p><p class="mt-2 text-2xl font-bold {{ $workloadAttentionCount ? 'text-rose-600' : 'text-emerald-700' }}">{{ $workloadAttentionCount }}</p><p class="mt-1 text-xs text-slate-500">Faculty near their limit or over the workload cap</p></article>
                    </div>
                    <section class="mx-5 mt-5 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mt-5 grid gap-4">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex flex-wrap items-center justify-between gap-3">
                                    <div>
                                        <h3 class="font-bold">Current department chair</h3>
                                        <p class="mt-1 text-sm text-slate-600">{{ $currentChair?->user?->usr_name ?? 'No chair assigned' }}</p>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                      <button type="button" class="topbar-btn btn-primary" onclick="showUtilityModal('replace-chair-modal')">{{ $currentChair ? 'Replace chair' : 'Assign chair' }}</button>
                                      @if($currentChair)
                                        <form method="POST" action="{{ route('dean.departments.chair.unassign', $selectedDepartment->dept_id) }}" onsubmit="return confirm('Unassign {{ addslashes($currentChair->user?->usr_name ?? 'this chair') }}? Their faculty account and schedules will stay.');">@csrf @method('DELETE')
                                            <button class="topbar-btn btn-secondary" type="submit">Unassign chair</button>
                                        </form>
                                      @endif
                                    </div>
                                </div>
                                @if($facultyMembers->isEmpty())<p class="mt-2 text-xs text-amber-700">There are no faculty members assigned to this department yet.</p>@endif
                            </div>
                        </div>

                        <div class="mt-5 flex flex-wrap items-center justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <h3 class="font-bold">Faculty members</h3>
                                <p class="text-sm text-slate-500">{{ $facultyTotal }} faculty assigned to this department’s sections</p>
                                <input id="faculty-roster-search" type="search" class="field-input mt-3 w-full max-w-sm" placeholder="Search faculty..." aria-label="Search faculty">
                            </div>
                            <div class="flex flex-wrap gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="showUtilityModal('replace-faculty-modal')">Assign / replace faculty</button><button type="button" class="topbar-btn btn-primary" onclick="showUtilityModal('add-faculty-modal')">Add user account</button></div>
                        </div>
                        <div class="mt-3 overflow-x-auto rounded-xl border border-slate-200">
                            <table class="w-full min-w-[900px] text-left text-sm">
                                <thead class="bg-slate-50 text-xs uppercase text-slate-500"><tr><th class="p-3">Name</th><th class="p-3">Rank / department</th><th class="p-3">Email</th><th class="p-3">Employment</th><th class="p-3">Day load</th><th class="p-3">Evening load</th><th class="p-3">Account status</th><th class="p-3">Action</th></tr></thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($facultyMembers as $member)
                                        <tr class="faculty-roster-row" data-faculty-search="{{ strtolower($member->full_name.' '.$member->fac_rank.' '.$member->user?->usr_email.' '.$member->program?->dept_code) }}">
                                            <td class="p-3"><button type="button" class="flex items-center gap-3 text-left" onclick="showFacultyDetails(this)" data-name="{{ $member->full_name }}" data-rank="{{ $member->fac_rank ?? '—' }}" data-employment="{{ ucfirst(str_replace('_', '-', $member->fac_employment_type ?? '—')) }}" data-email="{{ $member->user?->usr_email ?? '—' }}" data-employee="{{ $member->fac_employee_id ?? '—' }}" data-phone="{{ $member->fac_phone_number ?? '—' }}" data-position="{{ $member->fac_special_position ?? '—' }}" data-day="{{ number_format($member->day_load_hours, 1) }}h / {{ $member->day_load_cap }}h" data-evening="{{ number_format($member->evening_load_hours, 1) }}h / {{ $member->evening_load_cap }}h" data-load="{{ number_format($member->current_load_hours, 1) }}h"><span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 font-bold text-blue-700">@if($member->fac_profile_image)<img class="h-full w-full object-cover" src="{{ asset('storage/'.$member->fac_profile_image) }}" alt="">@else{{ strtoupper(mb_substr($member->full_name, 0, 1)) }}@endif</span><span><span class="block font-semibold text-slate-900">{{ $member->full_name }}</span><span class="block text-xs text-slate-500">{{ $member->user?->usr_email ?? 'No email' }}</span></span></button></td>
                                            <td class="p-3"><div>{{ $member->fac_rank ?? 'Faculty' }}</div><div class="text-xs text-slate-400">{{ $member->program?->dept_code ?? 'Other department' }}</div></td>
                                            <td class="p-3 text-slate-600">{{ $member->user?->usr_email ?? '—' }}</td>
                                            <td class="p-3">{{ ucfirst(str_replace('_', '-', $member->fac_employment_type ?? '—')) }}</td>
                                            <td class="p-3 font-semibold text-emerald-700">{{ number_format($member->day_load_hours, 1) }}h <span class="text-xs font-normal text-slate-400">/ {{ $member->day_load_cap }}h</span></td>
                                            <td class="p-3 font-semibold text-indigo-700">{{ number_format($member->evening_load_hours, 1) }}h <span class="text-xs font-normal text-slate-400">/ {{ $member->evening_load_cap }}h</span></td>
                                            <td class="p-3"><span class="badge {{ $member->user?->usr_is_active ? 'badge-green' : 'badge-grey' }}">{{ $member->user?->usr_is_active ? 'Active' : 'Inactive' }}</span></td>
                                            <td class="p-3">
                                                @if($member->fac_college_id === $assignedCollege->college_id && $member->fac_dept_id === $selectedDepartment->dept_id && $member->user)
                                                    <button type="button" class="topbar-btn btn-secondary" onclick="showFacultyEdit(this)" data-faculty-id="{{ $member->fac_id }}" data-dept-id="{{ $member->fac_dept_id }}" data-first="{{ $member->fac_first_name }}" data-middle="{{ $member->fac_middle_name }}" data-last="{{ $member->fac_last_name }}" data-suffix="{{ $member->fac_suffix }}" data-email="{{ $member->user->usr_email }}" data-active="{{ $member->user->usr_is_active ? '1' : '0' }}" data-employee="{{ $member->fac_employee_id }}" data-rank="{{ $member->fac_rank }}" data-employment="{{ $member->fac_employment_type }}" data-position="{{ $member->fac_special_position }}" data-phone="{{ $member->fac_phone_number }}" data-address="{{ $member->fac_address }}">Edit</button>
                                                @else<span class="text-xs text-slate-400">View only</span>@endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="8" class="p-5 text-center text-slate-500">No faculty assigned to this department’s sections.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($facultyMembers->hasPages())<div class="mt-4">{{ $facultyMembers->links() }}</div>@endif
                        <div class="mt-5 flex justify-end border-t border-slate-100 pt-4"><button type="button" class="topbar-btn btn-secondary" onclick="showUtilityModal('program-history-modal')">History from past positions</button></div>
                    </section>
                    <div id="faculty-details-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="faculty-details-title" onclick="if(event.target===this)this.classList.add('hidden')">
                        <div class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-xl">
                            <div class="flex items-center justify-between"><h2 id="faculty-details-title" class="text-lg font-bold">Faculty details</h2><button type="button" class="topbar-btn btn-secondary" onclick="document.getElementById('faculty-details-modal').classList.add('hidden')" aria-label="Close">Close</button></div>
                            <dl id="faculty-details-list" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2"></dl>
                        </div>
                    </div>
                    <div id="edit-department-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="edit-department-title" onclick="if(event.target===this)this.classList.add('hidden')">
                        <div class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-between"><div><h2 id="edit-department-title" class="text-lg font-bold">Edit department</h2><p class="text-sm text-slate-500">Update this program’s name or code.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('edit-department-modal')">Close</button></div>
                            <form method="POST" action="{{ route('dean.departments.update', $selectedDepartment->dept_id) }}" class="mt-4 grid gap-3 sm:grid-cols-[1fr_160px]">@csrf @method('PUT')
                                <input name="dept_name" required maxlength="255" value="{{ $selectedDepartment->dept_name }}" class="field-input" aria-label="Department name">
                                <input name="dept_code" required maxlength="50" value="{{ $selectedDepartment->dept_code }}" class="field-input" aria-label="Department code">
                                <div class="flex justify-end gap-2 sm:col-span-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('edit-department-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">Save changes</button></div>
                            </form>
                            <form method="POST" action="{{ route('dean.departments.destroy', $selectedDepartment->dept_id) }}" class="mt-4 border-t border-slate-100 pt-4" onsubmit="return confirm('Delete {{ addslashes($selectedDepartment->dept_code) }} permanently? This works only if the program has no linked faculty, courses, sections, Chair, or submissions.');">@csrf @method('DELETE')<button type="submit" class="topbar-btn btn-secondary text-rose-700">Delete department</button><p class="mt-1 text-xs text-slate-500">Deletion is blocked if records still depend on this department.</p></form>
                        </div>
                    </div>
                    <div id="replace-chair-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="replace-chair-title" onclick="if(event.target===this)this.classList.add('hidden')">
                        <form method="POST" action="{{ route('dean.departments.chair.assign', $selectedDepartment->dept_id) }}" class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()" onsubmit="return confirmChairAssignment(this, {{ $currentChair ? 'true' : 'false' }})">@csrf
                            <div class="flex items-start justify-between gap-3"><div><h2 id="replace-chair-title" class="text-lg font-bold">{{ $currentChair ? 'Replace department chair' : 'Assign department chair' }}</h2><p class="text-sm text-slate-500">Choose a faculty member in {{ $selectedDepartment->dept_code }}.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('replace-chair-modal')">Close</button></div>
                            <label class="mt-4 block text-sm font-semibold text-slate-600" for="chair-faculty-id">Faculty member</label>
                            <select id="chair-faculty-id" name="faculty_id" required class="field-input mt-1 w-full"><option value="">Choose faculty</option>@foreach($collegeFaculty->where('fac_dept_id', $selectedDepartment->dept_id) as $member)<option value="{{ $member->fac_id }}">{{ $member->full_name }}{{ $member->fac_rank ? ' · '.$member->fac_rank : '' }}</option>@endforeach</select>
                            <input type="hidden" name="confirm_replace" value="0">
                            <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('replace-chair-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">{{ $currentChair ? 'Replace chair' : 'Assign chair' }}</button></div>
                        </form>
                    </div>
                    <div id="edit-faculty-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="edit-faculty-title" onclick="if(event.target===this)hideUtilityModal('edit-faculty-modal')">
                        <form id="edit-faculty-form" method="POST" data-update-base="{{ url('/dean/departments/'.$selectedDepartment->dept_id.'/faculty') }}" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">@csrf @method('PUT')
                            <div class="flex items-start justify-between gap-3"><div><h2 id="edit-faculty-title" class="text-lg font-bold">Edit faculty details</h2><p class="text-sm text-slate-500">Update profile details. Account status and access are not changed here.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('edit-faculty-modal')">Close</button></div>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                                <label class="text-sm font-medium">First name<input name="usr_first_name" required maxlength="150" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Middle name<input name="usr_middle_name" maxlength="150" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Last name<input name="usr_last_name" required maxlength="150" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Suffix<input name="usr_suffix" maxlength="20" class="field-input mt-1"></label>
                                <label class="text-sm font-medium sm:col-span-2">Email<input name="usr_email" type="email" required maxlength="255" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Employee ID<input name="fac_employee_id" maxlength="255" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Rank / title<select name="fac_rank" class="field-input mt-1"><option value="">Select rank</option>@foreach(['Instructor I','Instructor II','Instructor III','Assistant Professor I','Assistant Professor II','Assistant Professor III','Assistant Professor IV','Associate Professor I','Associate Professor II','Associate Professor III','Associate Professor IV','Associate Professor V','Professor I','Professor II','Professor III','Professor IV','Professor V','Professor VI','University Professor'] as $rank)<option value="{{ $rank }}">{{ $rank }}</option>@endforeach</select></label>
                                <label class="text-sm font-medium">Employment<select name="fac_employment_type" required class="field-input mt-1"><option value="full_time">Full-time</option><option value="part_time">Part-time</option></select></label>
                                <label class="text-sm font-medium">Account status<select name="usr_is_active" class="field-input mt-1" required><option value="1">Active</option><option value="0">Deactivated</option></select></label>
                                <label class="text-sm font-medium">Special position<select name="fac_special_position" class="field-input mt-1"><option value="">None</option>@foreach(array_keys(\App\Services\ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS) as $position)<option value="{{ $position }}">{{ $position }}</option>@endforeach</select></label>
                                <label class="text-sm font-medium">College<input class="field-input mt-1" value="{{ $assignedCollege?->college_name }}" readonly></label>
                                <label class="text-sm font-medium">Department<select name="dept_id" class="field-input mt-1" required>@foreach($managedDepartments as $departmentOption)<option value="{{ $departmentOption->dept_id }}">{{ $departmentOption->dept_code }} · {{ $departmentOption->dept_name }}</option>@endforeach</select></label>
                                <div class="sm:col-span-2 lg:col-span-3"><label class="text-sm font-medium">Phone number</label><div class="mt-1 flex gap-2"><select id="dean-edit-phone-code" class="field-input w-32"><option value="+63">PH +63</option><option value="+1">US +1</option><option value="+44">UK +44</option><option value="+81">JP +81</option><option value="+82">KR +82</option><option value="+65">SG +65</option><option value="+60">MY +60</option><option value="+62">ID +62</option></select><input id="dean-edit-phone-local" type="tel" inputmode="numeric" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)" class="field-input flex-1" placeholder="9123456789"><input id="dean-edit-phone-full" name="fac_phone_number" type="hidden"></div><p class="text-xs text-slate-400">Country code replaces the leading 0. Enter digits only.</p></div>
                                <label class="text-sm font-medium sm:col-span-2 lg:col-span-3">Address<input name="fac_address" maxlength="255" class="field-input mt-1"></label>
                            </div>
                            <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('edit-faculty-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">Save faculty details</button></div>
                        </form>
                    </div>
                    <div id="replace-faculty-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="replace-faculty-title" onclick="if(event.target===this)this.classList.add('hidden')">
                        <form method="POST" action="{{ route('dean.departments.faculty.assign', $selectedDepartment->dept_id) }}" class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">@csrf
                            <div class="flex items-start justify-between gap-3"><div><h2 id="replace-faculty-title" class="text-lg font-bold">Assign / replace faculty</h2><p class="text-sm text-slate-500">Choose a faculty member from your college to move into {{ $selectedDepartment->dept_code }}.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('replace-faculty-modal')">Close</button></div>
                            <label class="mt-4 block text-sm font-semibold text-slate-600" for="replacement-faculty-search">Search faculty</label>
                            <input id="replacement-faculty-search" type="search" class="field-input mt-1 w-full" placeholder="Type a name or department..." oninput="filterFacultyOptions(this, 'replacement-faculty-id')">
                            <label class="mt-3 block text-sm font-semibold text-slate-600" for="replacement-faculty-id">Faculty member</label>
                            <select id="replacement-faculty-id" name="faculty_id" required class="field-input mt-1 w-full">
                                <option value="">Select faculty</option>
                                @foreach($collegeFaculty->where('fac_dept_id', '!=', $selectedDepartment->dept_id) as $member)
                                    <option value="{{ $member->fac_id }}">{{ $member->full_name }} · {{ $managedDepartments->firstWhere('dept_id', $member->fac_dept_id)?->dept_code ?? 'Unassigned' }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-slate-500">This changes their home department and keeps existing schedules intact.</p>
                            <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('replace-faculty-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">Assign to this program</button></div>
                        </form>
                    </div>
                    <div id="add-faculty-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="add-faculty-title" onclick="if(event.target===this)hideUtilityModal('add-faculty-modal')">
                        <form method="POST" action="{{ route('dean.departments.faculty.store', $selectedDepartment->dept_id) }}" class="max-h-[90vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">@csrf
                            <div class="flex items-center justify-between"><div><h2 id="add-faculty-title" class="text-lg font-bold">Add user account</h2><p class="text-sm text-slate-500">New faculty will be assigned to {{ $selectedDepartment->dept_code }} · {{ $assignedCollege?->college_code }}.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('add-faculty-modal')">Close</button></div>
                            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                <label class="text-sm font-medium">First name<input name="usr_first_name" required maxlength="150" class="field-input mt-1"></label><label class="text-sm font-medium">Middle name<input name="usr_middle_name" maxlength="150" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Last name<input name="usr_last_name" required maxlength="150" class="field-input mt-1"></label><label class="text-sm font-medium">Suffix<input name="usr_suffix" maxlength="20" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Email<input name="usr_email" type="email" required maxlength="255" class="field-input mt-1"></label><label class="text-sm font-medium">Temporary password<div class="mt-1 flex gap-2"><input id="dean-add-password" name="password" type="password" required minlength="8" class="field-input flex-1"><button type="button" class="topbar-btn btn-secondary" onclick="toggleDeanPassword()" id="dean-add-password-toggle">Show</button></div></label>
                                <label class="text-sm font-medium">Employee ID<input name="usr_employee_id" maxlength="255" class="field-input mt-1"></label>
                                <label class="text-sm font-medium">Rank / title<select name="usr_rank_title" class="field-input mt-1"><option value="">Select rank</option>@foreach(['Instructor I','Instructor II','Instructor III','Assistant Professor I','Assistant Professor II','Assistant Professor III','Assistant Professor IV','Associate Professor I','Associate Professor II','Associate Professor III','Associate Professor IV','Associate Professor V','Professor I','Professor II','Professor III','Professor IV','Professor V','Professor VI','University Professor'] as $rank)<option value="{{ $rank }}">{{ $rank }}</option>@endforeach</select></label>
                                <label class="text-sm font-medium">Employment<select name="employment_type" required class="field-input mt-1"><option value="full_time">Full-time</option><option value="part_time">Part-time</option></select></label>
                                <label class="text-sm font-medium">Special position<select name="special_position" class="field-input mt-1"><option value="">None</option>@foreach(array_keys(\App\Services\ScheduleAssignmentService::SPECIAL_POSITION_MAX_HOURS) as $position)<option value="{{ $position }}">{{ $position }}</option>@endforeach</select></label>
                                <div class="sm:col-span-2"><label class="text-sm font-medium">Phone number</label><div class="mt-1 flex gap-2"><select id="dean-add-phone-code" class="field-input w-32"><option value="+63" selected>PH +63</option><option value="+1">US +1</option><option value="+44">UK +44</option><option value="+81">JP +81</option><option value="+82">KR +82</option><option value="+65">SG +65</option><option value="+60">MY +60</option><option value="+62">ID +62</option></select><input id="dean-add-phone-local" type="tel" inputmode="numeric" maxlength="10" required oninput="this.value=this.value.replace(/\D/g,'').slice(0,10)" class="field-input flex-1" placeholder="9123456789"><input id="dean-add-phone-full" name="role_phone_number" type="hidden"></div><p class="text-xs text-slate-400">Country code replaces the leading 0. Enter digits only.</p></div>
                                <label class="text-sm font-medium">Address<input name="role_address" maxlength="255" class="field-input mt-1"></label>
                            </div>
                            <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('add-faculty-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">Create faculty account</button></div>
                        </form>
                    </div>
                    <div id="program-history-modal" class="fixed inset-0 z-50 {{ request()->has('chair_history_page') || request()->has('dean_history_page') || request()->has('activity_page') ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="program-history-title" onclick="if(event.target===this)this.classList.add('hidden')">
                        <div class="max-h-[85vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-between"><div><h2 id="program-history-title" class="text-lg font-bold">History from past positions</h2><p class="text-sm text-slate-500">{{ $selectedDepartment->dept_code }} · {{ $assignedCollege?->college_name }}</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('program-history-modal')">Close</button></div>
                            <section class="mt-4 rounded-xl border border-slate-200 p-4"><h3 class="font-bold">Past department chair positions</h3>
                                <div class="mt-2 space-y-2">@forelse($chairHistory as $history)<div class="border-t border-slate-100 pt-2"><div class="font-semibold">{{ $chairHistoryAvailable ? ($history->pph_former_name ?? $history->pph_new_name ?? 'Chair assignment') : ($history->user?->usr_name ?? 'Chair assignment') }}</div><div class="text-sm text-slate-500">{{ $chairHistoryAvailable ? ucfirst($history->pph_action) : $history->al_action }} · {{ \Illuminate\Support\Carbon::parse($chairHistoryAvailable ? $history->pph_created_at : $history->al_created_at)->format('M j, Y g:i A') }} · {{ $chairHistoryAvailable ? 'changed by '.$history->pph_changed_by : ($history->user?->usr_name ?? 'System') }}</div><div class="text-sm text-slate-500">{{ $chairHistoryAvailable ? (($history->pph_action === 'replaced' && $history->pph_new_name) ? 'Replaced by '.$history->pph_new_name : '') : $history->al_description }}</div></div>@empty<p class="py-2 text-sm text-slate-500">No past Chair positions recorded yet.</p>@endforelse</div>
                                @if($chairHistory->hasPages())<div class="mt-3">{{ $chairHistory->links() }}</div>@endif
                            </section>
                            <section class="mt-4 rounded-xl border border-slate-200 p-4"><h3 class="font-bold">Past Dean positions</h3><p class="mt-1 text-sm text-slate-700">Current Dean: {{ $currentDean?->user?->usr_name ?? 'No Dean account assigned' }}</p><p class="text-xs text-slate-500">Dean for {{ $assignedCollege?->college_code }} · {{ $assignedCollege?->college_name }}</p>
                                <div class="mt-2 space-y-2">@forelse($deanHistory as $entry)<div class="border-t border-slate-100 pt-2 text-sm"><b>{{ $entry->user?->usr_name ?? 'Admin' }}</b> · {{ $entry->al_action }}<div class="text-xs text-slate-500">{{ $entry->al_description }} · {{ \Illuminate\Support\Carbon::parse($entry->al_created_at)->format('M j, Y g:i A') }}</div></div>@empty<p class="py-2 text-sm text-slate-500">No Dean assignment changes have been logged yet.</p>@endforelse</div>
                                @if($deanHistory->hasPages())<div class="mt-3">{{ $deanHistory->links() }}</div>@endif
                            </section>
                            <section class="mt-4 rounded-xl border border-slate-200 p-4"><h3 class="font-bold">Department activity</h3><div class="mt-2 space-y-2">@forelse($departmentActivity as $activity)<div class="border-t border-slate-100 pt-2 text-sm"><b>{{ $activity->user?->usr_name ?? 'System' }}</b> · {{ $activity->al_action }}<div class="text-xs text-slate-500">{{ $activity->al_description }} · {{ \Illuminate\Support\Carbon::parse($activity->al_created_at)->format('M j, Y g:i A') }}</div></div>@empty<p class="py-2 text-sm text-slate-500">No activity recorded yet.</p>@endforelse</div></section>
                            @if($departmentActivity->hasPages())<div class="mt-3">{{ $departmentActivity->links() }}</div>@endif
                        </div>
                    </div>
                    <script>
                        function confirmChairAssignment(form, replacing) {
                            if (replacing && !window.confirm('This department already has a chair. Replace the current chair with the selected faculty member? The former chair will return to a faculty account and be saved in chair history.')) return false;
                            form.querySelector('[name="confirm_replace"]').value = replacing ? '1' : '0';
                            return true;
                        }

                        document.getElementById('faculty-roster-search')?.addEventListener('input', event => {
                            const term = event.target.value.trim().toLowerCase();
                            document.querySelectorAll('.faculty-roster-row').forEach(row => row.classList.toggle('hidden', !row.dataset.facultySearch.includes(term)));
                        });
                        function filterFacultyOptions(input, selectId) {
                            const term = input.value.trim().toLowerCase();
                            document.querySelectorAll(`#${selectId} option`).forEach((option, index) => {
                                if (index === 0) return;
                                option.hidden = !option.textContent.toLowerCase().includes(term);
                            });
                        }

                        function showFacultyEdit(button) {
                            const form = document.getElementById('edit-faculty-form');
                            form.action = `${form.dataset.updateBase}/${encodeURIComponent(button.dataset.facultyId)}`;
                            const fields = {usr_first_name:'first',usr_middle_name:'middle',usr_last_name:'last',usr_suffix:'suffix',usr_email:'email',fac_employee_id:'employee',fac_rank:'rank',fac_employment_type:'employment',fac_special_position:'position',fac_phone_number:'phone',fac_address:'address',usr_is_active:'active'};
                            Object.entries(fields).forEach(([name,key]) => { form.elements[name].value = button.dataset[key] || ''; });
                            form.elements.dept_id.value = button.dataset.deptId || '';
                            const phone = button.dataset.phone || '';
                            const code = document.getElementById('dean-edit-phone-code');
                            const matchedCode = [...code.options].map(option => option.value).sort((a,b) => b.length-a.length).find(value => phone.startsWith(value));
                            code.value = matchedCode || '+63';
                            document.getElementById('dean-edit-phone-local').value = phone.startsWith(code.value) ? phone.slice(code.value.length).replace(/^0/, '') : phone;
                            const modal = document.getElementById('edit-faculty-modal'); modal.classList.remove('hidden'); modal.classList.add('flex');
                        }
                        function toggleDeanPassword() {
                            const field = document.getElementById('dean-add-password');
                            const button = document.getElementById('dean-add-password-toggle');
                            field.type = field.type === 'password' ? 'text' : 'password';
                            button.textContent = field.type === 'password' ? 'Show' : 'Hide';
                        }
                        document.getElementById('add-faculty-modal')?.querySelector('form')?.addEventListener('submit', () => {
                            const code = document.getElementById('dean-add-phone-code').value;
                            const local = document.getElementById('dean-add-phone-local').value.replace(/\D/g, '').replace(/^0/, '');
                            document.getElementById('dean-add-phone-full').value = `${code}${local}`;
                        });                        document.getElementById('edit-faculty-form')?.addEventListener('submit', () => {
                            const code = document.getElementById('dean-edit-phone-code').value;
                            const local = document.getElementById('dean-edit-phone-local').value.replace(/\D/g, '').replace(/^0/, '');
                            document.getElementById('dean-edit-phone-full').value = local ? `${code}${local}` : '';
                        });
                        function showFacultyDetails(button) {
                            const labels = {name:'Name',rank:'Rank',employment:'Employment',email:'Email',employee:'Employee ID',phone:'Phone',position:'Special position',day:'Day load',evening:'Evening load',load:'Total semester load'};
                            const list = document.getElementById('faculty-details-list');
                            list.replaceChildren();
                            Object.entries(labels).forEach(([key,label]) => {
                                const wrap = document.createElement('div');
                                const term = document.createElement('dt'); term.className='text-xs font-semibold uppercase text-slate-400'; term.textContent=label;
                                const value = document.createElement('dd'); value.className='mt-1 font-medium text-slate-800'; value.textContent=button.dataset[key] || '—';
                                wrap.append(term,value); list.append(wrap);
                            });
                            const modal = document.getElementById('faculty-details-modal'); modal.classList.remove('hidden'); modal.classList.add('flex');
                        }
                    </script>
                @endif
                </div>
                <div id="program-list-view" style="{{ $selectedDepartment ? 'display:none;' : '' }}">
                    <div class="mx-5 mt-5 flex flex-wrap items-center justify-between gap-3">
                      <div>
                        <div style="font-size:20px;font-weight:800;">Program Overview</div>
                        <div style="font-size:13px;color:var(--text3);margin-top:3px;">Select a program to view
                            details and faculty</div>
                      </div>
                      @if($assignedCollege)<button type="button" class="topbar-btn btn-primary" onclick="showUtilityModal('add-department-modal')">+ Add Department</button>@endif
                    </div>
                    <div class="three-col mx-5 mt-4" id="program-list-grid"></div>
                </div>
                <div id="program-detail-view" style="{{ $selectedDepartment ? 'display:block;' : 'display:none;' }}">
                    <button class="topbar-btn btn-secondary" onclick="closeProgram()" style="margin-bottom:20px;">Back
                        to Programs</button>
                    <div id="program-detail-content"></div>
                </div>

            </div><!-- end .main -->
        </div><!-- end #screen-app -->

        <div id="add-department-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="add-department-title" onclick="if(event.target===this)hideUtilityModal('add-department-modal')">
            <form method="POST" action="{{ route('dean.departments.store') }}" class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">@csrf
                <div class="flex items-center justify-between"><div><h2 id="add-department-title" class="text-lg font-bold">Add department</h2><p class="text-sm text-slate-500">This department will be added to {{ $assignedCollege?->college_name ?? 'your assigned college' }}.</p></div><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('add-department-modal')">Close</button></div>
                <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_180px]"><input name="dept_name" required maxlength="255" class="field-input" placeholder="Department name"><input name="dept_code" required maxlength="50" class="field-input" placeholder="Code (e.g. BSIS)"></div>
                <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary" onclick="hideUtilityModal('add-department-modal')">Cancel</button><button type="submit" class="topbar-btn btn-primary">Add Department</button></div>
            </form>
        </div>

        <!-- MODAL: EXPORT -->
        <div class="modal-overlay" id="modal-export">
            <div class="modal" style="width:440px;">
                <div class="modal-header">
                    <div class="modal-title">Export Report</div><button class="modal-close"
                        onclick="closeModal('modal-export')">✕</button>
                </div>
                <div class="modal-body">
                    <div class="field-group" style="margin-bottom:14px;"><label class="field-label">Report
                            Type</label><select class="field-select">
                            <option>Master Schedule</option>
                            <option>Faculty Workload Report</option>
                            <option>Faculty Deployment Report</option>
                            <option>Department Summary</option>
                        </select></div>
                    <div class="field-group" style="margin-bottom:14px;"><label
                            class="field-label">Department</label><select class="field-select">
                            <option>All Departments</option>
                            @foreach ($deptData as $d)
                                <option>{{ $d['code'] }}</option>
                            @endforeach
                        </select></div>
                    <div class="field-group"><label class="field-label">Format</label><select class="field-select">
                            <option>PDF</option>
                            <option>Excel (.xlsx)</option>
                            <option>Word (.docx)</option>
                        </select></div>
                </div>
                <div class="modal-footer"><button class="topbar-btn btn-secondary"
                        onclick="closeModal('modal-export')">Cancel</button><button class="topbar-btn btn-primary"
                        onclick="closeModal('modal-export');showToast('Report exported!')">Download</button></div>
            </div>
        </div>
        <!-- MODAL: NOTIFY CHAIRS -->
        <div class="modal-overlay" id="modal-notify">
            <div class="modal" style="width:520px;">
                <div class="modal-header">
                    <div class="modal-title">Send Notification to Chairs</div>
                    <button class="modal-close" onclick="closeModal('modal-notify')">✕</button>
                </div>
                <div class="modal-body">

                    <!-- Recipients -->
                    <div style="margin-bottom:16px;">
                        <div class="field-label" style="margin-bottom:8px;">Recipients</div>
                        <div style="display:flex;flex-direction:column;gap:8px;">
                            <label
                                style="display:flex;align-items:center;gap:10px;padding:10px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
                                <input type="checkbox" id="notif-all" checked onchange="toggleAllChairs(this)"
                                    style="width:15px;height:15px;accent-color:var(--blue);">
                                <span style="font-size:13px;font-weight:700;color:var(--text);">All Department
                                    Chairs</span>
                            </label>
                            <div style="display:flex;flex-direction:column;gap:6px;padding-left:12px;">
                                {{-- Chairs are assigned per-program now (BSIS / BSIT / BIT-CT each have their
                                     own chair), so this list loops over every program across all departments
                                     the dean oversees, rather than one row per department. --}}
                                @foreach ($deptData as $d)
                                    @foreach ($d['programs'] as $p)
                                        <label
                                            style="display:flex;align-items:center;gap:10px;padding:8px 14px;background:var(--grey);border-radius:8px;cursor:pointer;border:1px solid var(--border);">
                                            <input type="checkbox" class="chair-check" checked
                                                onchange="syncAllChairs()"
                                                style="width:14px;height:14px;accent-color:var(--blue);">
                                            <div style="display:flex;align-items:center;gap:8px;">
                                                <div
                                                    style="width:28px;height:28px;border-radius:50%;background:#d97706;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;">
                                                    {{ strtoupper(substr($p['chair'], 0, 2)) }}</div>
                                                <div>
                                                    <div style="font-size:13px;font-weight:600;color:var(--text);">
                                                        {{ $p['chair'] }}</div>
                                                    <div style="font-size:11px;color:var(--text3);">Chair ·
                                                        {{ $p['code'] }}</div>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Type -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Notification Type</label>
                        <select class="field-select" id="notif-type">
                            <option value="info">General Info</option>
                            <option value="reminder">Reminder</option>
                            <option value="urgent">Urgent</option>
                            <option value="deadline">Deadline Notice</option>
                        </select>
                    </div>

                    <!-- Title -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Title</label>
                        <input class="field-input" id="notif-title" placeholder="e.g. Schedule Submission Reminder">
                    </div>

                    <!-- Message -->
                    <div class="field-group" style="margin-bottom:14px;">
                        <label class="field-label">Message</label>
                        <textarea class="field-input" id="notif-message" rows="4" style="resize:vertical;"
                            placeholder="Write your message to the chairs..."></textarea>
                    </div>

                    <!-- Sent History -->
                    <div>
                        <div
                            style="font-size:12px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px;">
                            Recently Sent</div>
                        <div id="notif-history" style="display:flex;flex-direction:column;gap:6px;">
                            <div style="font-size:12px;color:var(--text3);padding:8px;text-align:center;">No
                                notifications sent yet this session.</div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button class="topbar-btn btn-secondary" onclick="closeModal('modal-notify')">Cancel</button>
                    <button class="topbar-btn btn-primary" onclick="sendNotifToChairs()">Send Notification</button>
                </div>
            </div>
        </div>
        <div class="modal-overlay" id="modal-review">
            <div class="modal" style="width:560px;">
                <div class="modal-header">
                    <div class="modal-title">Schedule Review — BSIT</div><button class="modal-close"
                        onclick="closeModal('modal-review')">✕</button>
                </div>
                <div class="modal-body">
                    <div
                        style="background:var(--red-light);border:1px solid #fecaca;border-left:4px solid var(--red);border-radius:8px;padding:12px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;">
                        <strong>1 Conflict Detected:</strong> Carlo Mendoza is scheduled for CC 311 and IT 201 at the
                        same time on Monday 8:30–10:00 AM.
                    </div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Faculty</th>
                                <th>Subject</th>
                                <th>Day & Time</th>
                                <th>Room</th>
                                <th>Issue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><b>Carlo Mendoza</b></td>
                                <td>IT 201</td>
                                <td>Mon 8:30–10:00</td>
                                <td>Lab 1</td>
                                <td><span class="badge badge-red">Conflict</span></td>
                            </tr>
                            <tr>
                                <td><b>Carlo Mendoza</b></td>
                                <td>CC 311</td>
                                <td>Mon 8:30–10:00</td>
                                <td>Room 205</td>
                                <td><span class="badge badge-red">Conflict</span></td>
                            </tr>
                            <tr>
                                <td><b>Ana Reyes</b></td>
                                <td>IT 401</td>
                                <td>Tue 7:00–8:30</td>
                                <td>Room 206</td>
                                <td><span class="badge badge-green">OK</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="field-group" style="margin-top:16px;"><label class="field-label">Return Note</label>
                        <textarea class="field-input" rows="3">Please resolve the scheduling conflict for Carlo Mendoza before resubmitting.</textarea>
                    </div>
                </div>
                <div class="modal-footer"><button class="topbar-btn btn-secondary"
                        onclick="closeModal('modal-review')">Close</button><button class="topbar-btn"
                        style="background:var(--red-light);color:var(--red);"
                        onclick="closeModal('modal-review');showToast('Schedule returned to Chair Cruz.')">Return to
                        Chair</button></div>
            </div>
        </div>

        <div class="toast" id="toast"><span id="toast-msg"></span></div>

        <script>
            function doLogin() {
                document.getElementById('screen-login').classList.remove('active');
                const app = document.getElementById('screen-app');
                app.classList.add('active');
                app.style.display = 'flex';
            }

            function logout() {
                document.getElementById('screen-app').classList.remove('active');
                document.getElementById('screen-app').style.display = 'none';
                document.getElementById('screen-login').classList.add('active');
            }

            function goToPage(id) {
                document.querySelectorAll('.page').forEach(p => {
                    p.classList.remove('active');
                    p.style.display = 'none';
                });
                const page = document.getElementById(id);
                if (!page) return;
                page.style.display = 'block';
                page.classList.add('active');
                const titles = {
                    'page-dashboard': 'Dean Dashboard',
                    'page-faculty': 'Faculty Workload Overview',
                    'page-departments': 'Program Overview',
                    'page-approvals': 'Schedule Approvals',
                    'page-overload': 'Overload Alerts',
                    'page-reports': 'Schedule Reports',
                    'page-deployment': 'Faculty Deployment Report',
                    'page-settings': 'Settings'
                };
                document.getElementById('topbar-title').textContent = titles[id] || 'SKEDYUL';
            }

            function setActiveNav(el) {
                document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
                el.classList.add('active');
            }

            function openModal(id) {
                document.getElementById(id).classList.add('open');
            }

            function closeModal(id) {
                document.getElementById(id).classList.remove('open');
            }
            function showUtilityModal(id) {
                const modal = document.getElementById(id);
                if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
            }
            function hideUtilityModal(id) {
                const modal = document.getElementById(id);
                if (modal) modal.classList.add('hidden');
            }
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.modal-overlay').forEach(m => {
                    m.addEventListener('click', e => {
                        if (e.target === m) m.classList.remove('open');
                    });
                });
            });

            function showSettingsTab(tab, el) {
                ['general', 'academic', 'notifications', 'security', 'system'].forEach(s => {
                    const p = document.getElementById('stab-' + s);
                    if (p) p.style.display = 'none';
                });
                const t = document.getElementById('stab-' + tab);
                if (t) t.style.display = 'block';
                document.querySelectorAll('.settings-tab').forEach(b => b.classList.remove('active'));
                el.classList.add('active');
            }

            function showDeanSettingsSection(section, el) {
                ['profile', 'general', 'academic', 'notifications', 'security', 'system'].forEach(s => {
                    const p = document.getElementById('dsec-' + s);
                    if (p) p.style.display = 'none';
                });
                const t = document.getElementById('dsec-' + section);
                if (t) t.style.display = 'block';
                document.querySelectorAll('#page-settings .settings-nav-item').forEach(i => i.classList.remove('active'));
                el.classList.add('active');
            }

            function toggleAllChairs(master) {
                document.querySelectorAll('.chair-check').forEach(c => c.checked = master.checked);
            }

            function syncAllChairs() {
                const checks = document.querySelectorAll('.chair-check');
                const allChecked = Array.from(checks).every(c => c.checked);
                document.getElementById('notif-all').checked = allChecked;
            }

            function sendNotifToChairs() {
                const title = document.getElementById('notif-title').value.trim();
                const message = document.getElementById('notif-message').value.trim();
                const type = document.getElementById('notif-type').value;
                const checks = document.querySelectorAll('.chair-check');
                const selected = [];
                // Chairs are per-program now, so flatten every department's
                // programs into one list of "Chair Name (PROG_CODE)" strings —
                // this must line up 1:1 with the recipient rows rendered above.
                const names = @json(collect($deptData)->flatMap(fn($d) => collect($d['programs'])->map(fn($p) => $p['chair'] . ' (' . $p['code'] . ')'))->values());
                checks.forEach((c, i) => {
                    if (c.checked) selected.push(names[i]);
                });

                if (!title) {
                    showToast('Please enter a notification title.');
                    return;
                }
                if (!message) {
                    showToast('Please enter a message.');
                    return;
                }
                if (selected.length === 0) {
                    showToast('Please select at least one recipient.');
                    return;
                }

                const typeColors = {
                    info: 'badge-blue',
                    reminder: 'badge-amber',
                    urgent: 'badge-red',
                    deadline: 'badge-purple'
                };
                const typeLabels = {
                    info: 'Info',
                    reminder: 'Reminder',
                    urgent: 'Urgent',
                    deadline: 'Deadline'
                };
                const now = new Date();
                const timeStr = now.toLocaleTimeString('en-US', {
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                });

                const historyEl = document.getElementById('notif-history');
                // Remove empty state message
                const empty = historyEl.querySelector('div[style*="text-align:center"]');
                if (empty) empty.remove();

                const item = document.createElement('div');
                item.style.cssText =
                    'background:var(--grey);border-radius:8px;padding:10px 12px;border:1px solid var(--border);';
                item.innerHTML = `
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:4px;">
      <div style="display:flex;align-items:center;gap:7px;">
        <span class="badge ${typeColors[type]}">${typeLabels[type]}</span>
        <span style="font-size:13px;font-weight:700;color:var(--text);">${title}</span>
      </div>
      <span style="font-size:11px;color:var(--text3);">Sent ${timeStr}</span>
    </div>
    <div style="font-size:12px;color:var(--text2);margin-bottom:4px;">${message}</div>
    <div style="font-size:11px;color:var(--text3);">To: ${selected.join(', ')}</div>`;

                historyEl.prepend(item);

                // Clear fields
                document.getElementById('notif-title').value = '';
                document.getElementById('notif-message').value = '';
                document.getElementById('notif-type').value = 'info';

                showToast('Notification sent to ' + selected.length + ' chair' + (selected.length > 1 ? 's' : '') + '!');
            }

            function toggleSwitch(input) {
                const track = input.nextElementSibling;
                input.checked ? track.classList.add('on') : track.classList.remove('on');
                showToast('Preference updated!');
            }

            function applyTheme(theme) {
                theme === 'dark' ? document.body.classList.add('dark') : document.body.classList.remove('dark');
                showToast('Theme switched to ' + (theme === 'dark' ? 'Dark' : 'Light') + ' mode!');
            }

            function checkPwStrength(val) {
                const bar = document.getElementById('pw-bar');
                if (!bar) return;
                let score = 0;
                if (val.length >= 8) score++;
                if (/[A-Z]/.test(val)) score++;
                if (/[0-9]/.test(val)) score++;
                if (/[^A-Za-z0-9]/.test(val)) score++;
                const colors = ['#dc2626', '#d97706', '#16a34a', '#0891b2'];
                const widths = ['25%', '50%', '75%', '100%'];
                bar.style.width = val.length ? (widths[score - 1] || '10%') : '0';
                bar.style.background = val.length ? (colors[score - 1] || '#dc2626') : 'transparent';
            }

            function showToast(msg) {
                const t = document.getElementById('toast');
                document.getElementById('toast-msg').textContent = msg;
                t.classList.add('show');
                setTimeout(() => t.classList.remove('show'), 3000);
            }

            function switchTab(barId, panelId, btn) {
                const bar = document.getElementById(barId);
                if (!bar) return;
                bar.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                ['tab-by-dept', 'tab-by-faculty', 'tab-by-section'].forEach(p => {
                    const el = document.getElementById(p);
                    if (el) {
                        el.classList.remove('active');
                        el.style.display = 'none';
                    }
                });
                const target = document.getElementById(panelId);
                if (target) {
                    target.style.display = 'block';
                    target.classList.add('active');
                }
            }

            // Real department + faculty data from the database (built in DeanDepartmentController)
            const deptData = @json($deptData);

            // Flatten every department's programs into one list, keeping the
            // originating deptKey on each program so openProgram() still works.
            function flattenPrograms() {
                const list = [];
                Object.keys(deptData).forEach(deptKey => {
                    const d = deptData[deptKey];
                    Object.keys(d.programs).forEach(progKey => {
                        list.push({
                            deptKey,
                            progKey,
                            ...d.programs[progKey]
                        });
                    });
                });
                return list;
            }

            function renderProgramList() {
                const programs = flattenPrograms();
                const grid = document.getElementById('program-list-grid');

                if (!programs.length) {
                    grid.innerHTML = `<div style="color:var(--text3);font-size:13px;">No programs found.</div>`;
                    return;
                }

                grid.innerHTML = programs.map(p => `
    <div class="dept-card" onclick="openProgram('${p.deptKey}','${p.progKey}')">
      <div class="dept-card-header"><div><div class="dept-card-code" style="color:${p.color}">${p.code}</div><div class="dept-card-name">${p.name}</div></div><span class="dept-card-arrow">›</span></div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:14px;">
        <div style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:800;">${p.facultyCount}</div><div style="font-size:11px;color:var(--text3)">Faculty</div></div>
        <div style="background:var(--grey);border-radius:8px;padding:10px;text-align:center;"><div style="font-size:20px;font-weight:800;">${p.sections}</div><div style="font-size:11px;color:var(--text3)">Sections</div></div>
      </div>
      <div style="margin-bottom:8px;font-size:12px;font-weight:600;color:var(--text2)">${p.activeFaculty} active accounts · ${p.plottedCourses} courses plotted</div>
      <div style="margin-top:12px;"><span class="badge badge-grey">Chair: ${p.chair}</span></div>
    </div>`).join('');
            }

            // Put the selected program's management tools inside its detail view.
            const selectedProgramId = @json($selectedDepartment?->dept_id);
            document.addEventListener('DOMContentLoaded', () => {
                renderProgramList();
                const tools = document.getElementById('selected-program-tools');
                document.getElementById('program-detail-view').appendChild(tools);
                if (selectedProgramId) {
                    tools.classList.remove('hidden');
                    for (const [deptKey, dept] of Object.entries(deptData)) {
                        const selectedProgram = Object.keys(dept.programs).find(id => String(id) === String(selectedProgramId));
                        if (selectedProgram) {
                            openProgram(deptKey, selectedProgram);
                            break;
                        }
                    }
                }
            });

            function openProgram(deptKey, progKey) {
                const d = deptData[deptKey];
                if (!d) return;
                const p = d.programs[progKey];
                if (!p) return;

                // Load this program from the server so its forms and history are scoped to the selected department.
                if (String(progKey) !== String(selectedProgramId)) {
                    window.location.href = `${@json(route('dean.departments'))}?department_id=${encodeURIComponent(progKey)}#program-detail-view`;
                    return;
                }

                document.getElementById('program-detail-content').replaceChildren();

                document.getElementById('program-list-view').style.display = 'none';
                document.getElementById('program-detail-view').style.display = 'block';
                document.getElementById('topbar-title').textContent = p.code + ' — Faculty';
            }

            function closeProgram() {
                document.getElementById('program-detail-view').style.display = 'none';
                document.getElementById('program-list-view').style.display = 'block';
                document.getElementById('topbar-title').textContent = 'Program Overview';
                history.replaceState(null, '', @json(route('dean.departments')));
            }
        </script>
</body>

</html>
