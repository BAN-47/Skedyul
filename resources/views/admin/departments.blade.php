<!doctype html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SKEDYUL — Colleges & Departments</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-screen overflow-hidden bg-slate-50 font-sans text-slate-900">
    <div class="app-shell">
        @include('partials.admin_sidebar')
        <div class="app-main">
            @include('partials.admin_header', ['title' => 'Colleges & Departments'])
            <main class="page-content space-y-4">
                @if (session('success'))
                    <div role="status"
                        class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800">
                        {{ session('success') }}</div>
                @endif
                @if (session('error'))
                    <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                        {{ session('error') }}</div>
                @endif
                @if ($errors->any())
                    <div role="alert" class="rounded-xl border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">
                        {{ $errors->first() }}</div>
                @endif

            @if(!$selectedCollege)
                <section class="card">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div><h1 class="text-lg font-bold">Colleges</h1><p class="mt-1 text-sm text-slate-500">Choose a college to see its departments.</p></div>
                        <div class="flex flex-wrap gap-2"><a class="btn btn-secondary" href="{{ route('admin.departments', ['modal' => 'activity', 'history_page' => 1]) }}">Department and role activity</a><a class="btn btn-primary" href="{{ route('admin.departments', ['modal' => 'add-college']) }}">+ Add college</a></div>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($colleges as $college)
                            <a href="{{ route('admin.departments', ['college_id' => $college->college_id]) }}" class="group flex min-h-[88px] items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="min-w-0"><span class="flex items-center gap-2"><span class="text-base font-bold text-slate-900">{{ $college->college_code }}</span><span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $college->programs_count }} {{ \Illuminate\Support\Str::plural('department', $college->programs_count) }}</span></span><span class="mt-1 block truncate text-sm text-slate-600">{{ $college->college_name }}</span></span>
                                <span class="shrink-0 text-sm font-semibold text-blue-700 group-hover:text-blue-900">Open →</span>
                            </a>
                        @empty
                            <p class="text-sm text-slate-500">No colleges have been added yet.</p>
                        @endforelse
                    </div>
                </section>
            @elseif($selectedDepartment)
                <section class="card">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3">
                            <a class="btn btn-secondary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">← Departments</a>
                            <div><h1 class="text-lg font-bold">{{ $selectedDepartment->dept_code }} · {{ $selectedDepartment->dept_name }}</h1><p class="text-sm text-slate-500">Faculty roster · {{ $selectedCollege->college_name }}</p></div>
                        </div>
                    </div>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Faculty in this department</p><p class="mt-2 text-2xl font-bold">{{ $selectedDepartment->faculty_count }}</p></article>
                        <article class="rounded-xl border border-slate-200 bg-slate-50 p-4"><p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Department chair</p><p class="mt-2 text-lg font-semibold">{{ $selectedDepartment->chairs->first()?->user?->usr_name ?? 'Not assigned' }}</p></article>
                    </div>
                    @php($departmentDean = $selectedDepartment->department?->deans?->first())
                    <div class="mt-4 flex flex-wrap items-start gap-2">
                        <form method="POST" action="{{ route('admin.departments.update', $selectedDepartment->dept_id) }}" class="grid min-w-[min(100%,32rem)] flex-1 grid-cols-[minmax(0,1fr)_82px_auto] gap-2" data-angle-bracket-guard>@csrf @method('PUT')
                            <div class="col-span-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" data-angle-bracket-alert role="alert" aria-live="assertive" hidden></div>
                            <input type="hidden" name="dept_college_id" value="{{ $selectedCollege->college_id }}">
                            <input name="dept_name" value="{{ $selectedDepartment->dept_name }}" class="field-input min-w-0 px-2 py-2 text-sm" required maxlength="255" aria-label="Department name">
                            <input name="dept_code" value="{{ $selectedDepartment->dept_code }}" class="field-input min-w-0 px-2 py-2 text-sm" required maxlength="50" aria-label="Department code">
                            <button class="btn btn-secondary shrink-0 px-3 py-2 text-sm" type="submit">Save department</button>
                        </form>
                        <form method="POST" action="{{ route('admin.departments.destroy', $selectedDepartment->dept_id) }}" onsubmit="return confirm('Permanently delete {{ addslashes($selectedDepartment->dept_code) }}? This is allowed only when no faculty, courses, sections, or chair uses it.');">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete department</button></form>
                    </div>
                    <p class="mt-3 text-sm text-slate-500">Dean: {{ $departmentDean?->user?->usr_name ?? 'Not assigned' }}</p>
                    <div class="mt-5 overflow-x-auto rounded-xl border border-slate-200">
                        <table class="data-table min-w-[760px]">
                            <thead><tr><th>Faculty</th><th>Rank</th><th>Employment</th><th>Account status</th><th>Action</th></tr></thead>
                            <tbody>
                                @forelse($departmentFaculty as $member)
                                    <tr>
                                        <td>
                                            @if($member->user)
                                                <a class="flex items-center gap-3 font-semibold text-slate-800 hover:text-blue-700" href="{{ route('admin.users', ['search' => $member->user->usr_email]) }}">
                                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 text-sm font-bold text-blue-700">@if($member->fac_profile_image)<img src="{{ \App\Services\ProfileImageStorage::url($member->fac_profile_image, 'faculty_profile') }}" alt="" class="h-full w-full object-cover">@else{{ strtoupper(mb_substr($member->full_name, 0, 1)) }}@endif</span>
                                                    <span>{{ $member->full_name }}<span class="block text-xs font-normal text-slate-500">{{ $member->user->usr_email }}</span></span>
                                                </a>
                                            @else
                                                <span class="flex items-center gap-3 font-semibold text-slate-700"><span class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-bold">{{ strtoupper(mb_substr($member->full_name, 0, 1)) }}</span>{{ $member->full_name }}<span class="text-xs font-normal text-slate-400">No linked account</span></span>
                                            @endif
                                        </td>
                                        <td>{{ $member->fac_rank ?: 'Faculty' }}@if($member->fac_special_position)<span class="mt-1 block text-xs text-slate-500">{{ $member->fac_special_position }}</span>@endif</td>
                                        <td>{{ ucfirst(str_replace('_', '-', $member->fac_employment_type ?? '—')) }}</td>
                                        <td><span class="badge {{ $member->user?->usr_is_active ? 'badge-green' : 'badge-grey' }}">{{ $member->user ? ($member->user->usr_is_active ? 'Active' : 'Deactivated') : 'No account' }}</span></td>
                                        <td>@if($member->user)<a class="btn btn-secondary !px-3 !py-1.5 !text-xs" href="{{ route('admin.users', ['search' => $member->user->usr_email]) }}">Open account</a>@else<span class="text-xs text-slate-400">—</span>@endif</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="5" class="py-8 text-center text-slate-500">No faculty are assigned to this department.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($departmentFaculty->hasPages())<div class="mt-4">{{ $departmentFaculty->links() }}</div>@endif
                </section>
            @else
                <section class="card">
                    <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-4">
                        <div class="flex items-center gap-3"><a class="btn btn-secondary" href="{{ route('admin.departments') }}">← Colleges</a><div><h1 class="text-lg font-bold">{{ $selectedCollege->college_code }}</h1><p class="text-sm text-slate-500">{{ $selectedCollege->college_name }}</p></div></div>
                        <div class="flex flex-wrap gap-2"><a class="btn btn-secondary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id, 'modal' => 'activity', 'history_page' => 1]) }}">Department and role activity</a><a class="btn btn-primary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id, 'modal' => 'add-department']) }}">+ Add department</a></div>
                    </div>
                    <div class="mt-4 flex flex-wrap items-start gap-2">
                        <form method="POST" action="{{ route('admin.colleges.update', $selectedCollege->college_id) }}" class="grid min-w-[min(100%,32rem)] flex-1 grid-cols-[minmax(0,1fr)_82px_auto] gap-2" data-angle-bracket-guard>@csrf @method('PUT')
                            <div class="col-span-full rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" data-angle-bracket-alert role="alert" aria-live="assertive" hidden></div>
                            <input class="field-input min-w-0 px-2 py-2 text-sm" name="college_name" value="{{ $selectedCollege->college_name }}" required maxlength="255" aria-label="College name">
                            <input class="field-input min-w-0 px-2 py-2 text-sm" name="college_code" value="{{ $selectedCollege->college_code }}" required maxlength="50" aria-label="College code">
                            <button class="btn btn-secondary shrink-0 px-3 py-2 text-sm" type="submit">Save college</button>
                        </form>
                        <button class="btn btn-danger" type="button" id="open-college-delete-warning">Delete college</button>
                    </div>
                    <form method="GET" action="{{ route('admin.departments') }}" class="mt-4 grid gap-3">
                        <input type="hidden" name="college_id" value="{{ $selectedCollege->college_id }}">
                        <input name="search" value="{{ $filters['search'] ?? '' }}" class="field-input" placeholder="Search departments">
                        <div class="flex gap-2"><button class="btn btn-primary" type="submit">Search departments</button><a class="btn btn-secondary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">Clear search</a></div>
                    </form>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                        @forelse($departmentRows as $department)
                            <a href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id, 'department_id' => $department->dept_id]) }}" class="group flex min-h-[84px] items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition hover:border-blue-300 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <span class="min-w-0"><span class="flex items-center gap-2"><span class="text-base font-bold text-blue-700">{{ $department->dept_code }}</span><span class="rounded-full bg-blue-50 px-2 py-0.5 text-xs text-blue-700">{{ $department->faculty_count }} faculty</span></span><span class="mt-1 block truncate text-sm text-slate-600">{{ $department->dept_name }}</span></span>
                                <span class="shrink-0 text-sm font-semibold text-blue-700 group-hover:text-blue-900">Open →</span>
                            </a>
                        @empty
                            <p class="rounded-xl border border-dashed border-slate-300 p-6 text-center text-sm text-slate-500">No departments match these filters.</p>
                        @endforelse
                    </div>
                    <div class="mt-4">{{ $departmentRows->links() }}</div>
                </section>
            @endif
        </main>
    </div>
</div>

@if($selectedCollege && !$selectedDepartment)
    <div id="college-delete-warning" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/60 p-4" role="dialog" aria-modal="true" aria-labelledby="college-delete-title" aria-describedby="college-delete-description">
        <section class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-amber-100 text-xl text-amber-700" aria-hidden="true">!</span>
                <div>
                    <h2 id="college-delete-title" class="text-lg font-bold text-slate-900">Delete {{ $selectedCollege->college_name }}?</h2>
                    <p id="college-delete-description" class="mt-2 text-sm leading-6 text-slate-600">This action cannot be undone. It will permanently delete this college, all {{ $selectedCollege->programs_count }} {{ \Illuminate\Support\Str::plural('department', $selectedCollege->programs_count) }}, and the records linked to them, including faculty assignments, courses, sections, rooms, Chair and Dean assignments, and schedule records.</p>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <button class="btn btn-secondary" type="button" id="cancel-college-delete">Cancel</button>
                <form method="POST" action="{{ route('admin.colleges.destroy', $selectedCollege->college_id) }}">@csrf @method('DELETE')<button class="btn btn-danger" type="submit">Delete college and departments</button></form>
            </div>
        </section>
    </div>
    <script>
        (() => {
            const modal = document.getElementById('college-delete-warning');
            const openButton = document.getElementById('open-college-delete-warning');
            const cancelButton = document.getElementById('cancel-college-delete');
            const closeModal = () => {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                openButton.focus();
            };
            openButton.addEventListener('click', () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                cancelButton.focus();
            });
            cancelButton.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal();
            });
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
        })();
    </script>
@endif

@if(request('modal') === 'add-college')
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="college-add-title">
    <form method="POST" action="{{ route('admin.colleges.store') }}" class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" data-angle-bracket-guard>@csrf
        <div class="flex items-center justify-between"><div><h2 id="college-add-title" class="text-lg font-bold">Add college</h2><p class="text-sm text-slate-500">Create a college for departments and faculty.</p></div><a class="btn btn-secondary" href="{{ route('admin.departments') }}">Close</a></div>
        @php($collegeValidationError = $errors->first('college_name') ?: $errors->first('college_code'))
        <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" data-angle-bracket-alert role="alert" aria-live="assertive" @if(!$collegeValidationError) hidden @endif>{{ $collegeValidationError }}</div>
        <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_180px]"><input class="field-input" name="college_name" value="{{ old('college_name') }}" required maxlength="255" placeholder="College name"><input class="field-input" name="college_code" value="{{ old('college_code') }}" required maxlength="50" placeholder="Code (e.g. CCICT)"></div>
        <div class="mt-5 flex justify-end gap-2"><a class="btn btn-secondary" href="{{ route('admin.departments') }}">Cancel</a><button class="btn btn-primary" type="submit">Add college</button></div>
    </form>
</div>
@endif

@if(request('modal') === 'add-department' && $selectedCollege)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="department-add-title">
    <form method="POST" action="{{ route('admin.departments.store') }}" class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" data-angle-bracket-guard>@csrf
        <div class="flex items-center justify-between"><div><h2 id="department-add-title" class="text-lg font-bold">Add department</h2><p class="text-sm text-slate-500">Adding to {{ $selectedCollege->college_code }} · {{ $selectedCollege->college_name }}</p></div><a class="btn btn-secondary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">Close</a></div>
        @php($departmentValidationError = $errors->first('dept_college_id') ?: ($errors->first('dept_name') ?: $errors->first('dept_code')))
        <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700" data-angle-bracket-alert role="alert" aria-live="assertive" @if(!$departmentValidationError) hidden @endif>{{ $departmentValidationError }}</div>
        <input type="hidden" name="dept_college_id" value="{{ old('dept_college_id', $selectedCollege->college_id) }}"><div class="mt-4 grid gap-3 sm:grid-cols-[1fr_180px]"><input name="dept_name" value="{{ old('dept_name') }}" class="field-input" required maxlength="255" placeholder="Department name"><input name="dept_code" value="{{ old('dept_code') }}" class="field-input" required maxlength="50" placeholder="Code (e.g. BSIS)"></div>
        <div class="mt-5 flex justify-end gap-2"><a class="btn btn-secondary" href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">Cancel</a><button class="btn btn-primary" type="submit">Add department</button></div>
    </form>
</div>
@endif

    @if (request('modal') === 'add-college')
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" role="dialog"
            aria-modal="true" aria-labelledby="college-add-title">
            <form method="POST" action="{{ route('admin.colleges.store') }}"
                class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" data-angle-bracket-guard>@csrf
                <div class="flex items-center justify-between">
                    <div>
                        <h2 id="college-add-title" class="text-lg font-bold">Add college</h2>
                        <p class="text-sm text-slate-500">Create a college for departments and faculty.</p>
                    </div><a class="btn btn-secondary" href="{{ route('admin.departments') }}">Close</a>
                </div>
                @php($collegeValidationError = $errors->first('college_name') ?: $errors->first('college_code'))
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                    data-angle-bracket-alert role="alert" aria-live="assertive"
                    @if (!$collegeValidationError) hidden @endif>{{ $collegeValidationError }}</div>
                <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_180px]"><input class="field-input" name="college_name"
                        value="{{ old('college_name') }}" required maxlength="255" placeholder="College name"><input
                        class="field-input" name="college_code" value="{{ old('college_code') }}" required
                        maxlength="50" placeholder="Code (e.g. CCICT)"></div>
                <div class="mt-5 flex justify-end gap-2"><a class="btn btn-secondary"
                        href="{{ route('admin.departments') }}">Cancel</a><button class="btn btn-primary"
                        type="submit">Add college</button></div>
            </form>
        </div>
    @endif

    @if (request('modal') === 'add-department' && $selectedCollege)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" role="dialog"
            aria-modal="true" aria-labelledby="department-add-title">
            <form method="POST" action="{{ route('admin.departments.store') }}"
                class="w-full max-w-xl rounded-2xl bg-white p-5 shadow-xl" data-angle-bracket-guard>@csrf
                <div class="flex items-center justify-between">
                    <div>
                        <h2 id="department-add-title" class="text-lg font-bold">Add department</h2>
                        <p class="text-sm text-slate-500">Adding to {{ $selectedCollege->college_code }} ·
                            {{ $selectedCollege->college_name }}</p>
                    </div><a class="btn btn-secondary"
                        href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">Close</a>
                </div>
                @php($departmentValidationError = $errors->first('dept_college_id') ?: ($errors->first('dept_name') ?: $errors->first('dept_code')))
                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                    data-angle-bracket-alert role="alert" aria-live="assertive"
                    @if (!$departmentValidationError) hidden @endif>{{ $departmentValidationError }}</div>
                <input type="hidden" name="dept_college_id"
                    value="{{ old('dept_college_id', $selectedCollege->college_id) }}">
                <div class="mt-4 grid gap-3 sm:grid-cols-[1fr_180px]"><input name="dept_name"
                        value="{{ old('dept_name') }}" class="field-input" required maxlength="255"
                        placeholder="Department name"><input name="dept_code" value="{{ old('dept_code') }}"
                        class="field-input" required maxlength="50" placeholder="Code (e.g. BSIS)"></div>
                <div class="mt-5 flex justify-end gap-2"><a class="btn btn-secondary"
                        href="{{ route('admin.departments', ['college_id' => $selectedCollege->college_id]) }}">Cancel</a><button
                        class="btn btn-primary" type="submit">Add department</button></div>
            </form>
        </div>
    @endif

    @if (request('modal') === 'activity' || request()->has('history_page'))
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50 p-4" role="dialog"
            aria-modal="true" aria-labelledby="department-activity-title">
            <section class="max-h-[85vh] w-full max-w-3xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 id="department-activity-title" class="text-lg font-bold">Department and role activity</h2>
                        <p class="text-sm text-slate-500">Recent actions by Admins, Deans, Chairs, and Faculty.</p>
                    </div><a class="btn btn-secondary"
                        href="{{ $selectedCollege ? route('admin.departments', ['college_id' => $selectedCollege->college_id]) : route('admin.departments') }}">Close</a>
                </div>
                <div class="mt-3 divide-y divide-slate-100">
                    @forelse($departmentHistory as $activity)
                        <article class="py-3">
                            <div class="font-semibold">{{ $activity->user?->usr_name ?? 'System' }} <span
                                    class="font-normal text-slate-500">·
                                    {{ $activity->user?->usr_role ?? 'system' }}</span></div>
                            <div class="text-sm">{{ $activity->al_action }} · {{ $activity->al_description }}</div>
                            <div class="text-xs text-slate-500">{{ $activity->al_target_table }} ·
                                {{ $activity->al_created_at }}</div>
                        </article>
                    @empty
                        <p class="py-4 text-sm text-slate-500">No department or role activity recorded yet.</p>
                    @endforelse
                </div>
                <div class="mt-4">{{ $departmentHistory->links() }}</div>
            </section>
        </div>
    @endif
    <script>
        document.querySelectorAll('form[data-angle-bracket-guard]').forEach((form) => {
            const alert = form.querySelector('[data-angle-bracket-alert]');
            const findInvalidField = () => Array.from(form.elements).find((field) => {
                if (field.disabled || ['hidden', 'submit', 'button', 'checkbox'].includes(field.type))
                    return false;
                return typeof field.value === 'string' && /[<>]/.test(field.value);
            });
            const updateAlert = () => {
                const invalidField = findInvalidField();
                alert.textContent = invalidField ? 'Angle brackets (< and >) are not allowed.' : '';
                alert.hidden = !invalidField;
            };

            form.addEventListener('input', updateAlert);
            form.addEventListener('change', updateAlert);
            form.addEventListener('invalid', (event) => {
                if (typeof event.target.value === 'string' && /[<>]/.test(event.target.value)) {
                    updateAlert();
                }
            }, true);
            form.addEventListener('submit', (event) => {
                const invalidField = findInvalidField();
                if (!invalidField) return;
                event.preventDefault();
                updateAlert();
                invalidField.focus();
            });
        });
    </script>
</body>

</html>
