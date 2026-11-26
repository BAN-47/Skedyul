<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SKEDYUL — Faculty Workload</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
    @include('partials.dean_header', ['title' => 'Faculty Workload'])
    <main class="page-content">
        @php
            $filterValidationMessage = '';
            foreach (['semester', 'department', 'employment', 'status', 'search'] as $filterField) {
                if ($errors->has($filterField)) {
                    $filterValidationMessage = $errors->first($filterField);
                    break;
                }
            }
            $designationValidationMessage = $errors->first('special_position');
        @endphp
        @if (session('success'))
            <div role="status" class="mb-4 rounded-lg bg-emerald-50 p-3 text-sm text-emerald-800">
                {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-4 rounded-lg bg-rose-50 p-3 text-sm text-rose-800">{{ $errors->first() }}</div>
        @endif
        <div class="mb-5 flex flex-wrap items-end justify-between gap-4">
            <div>
                <h1 class="text-xl font-bold">Faculty Workload</h1>
                <p class="mt-1 text-sm text-slate-500">Teaching hours for faculty in
                    {{ $dean->department?->college_name ?? 'Your college' }}.</p>
            </div>
            <span
                class="badge badge-blue">{{ $semester?->sem_name ?? 'No semester' }}{{ $semester?->academicYear?->ay_academic_year ? ' · AY ' . $semester->academicYear->ay_academic_year : '' }}</span>
        </div>

        <div class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Faculty</p>
                <p class="mt-1 text-2xl font-bold">{{ $stats['faculty'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Across your college</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Scheduled Hours</p>
                <p class="mt-1 text-2xl font-bold text-blue-700">{{ number_format($stats['total_hours'], 1) }}h</p>
                <p class="mt-1 text-xs text-slate-500">This semester’s college sections</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Needs attention</p>
                <p class="mt-1 text-2xl font-bold {{ $stats['attention'] ? 'text-amber-600' : 'text-emerald-700' }}">
                    {{ $stats['attention'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Near or over a workload limit</p>
            </article>
            <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">Over limit</p>
                <p class="mt-1 text-2xl font-bold {{ $stats['over_limit'] ? 'text-rose-600' : 'text-emerald-700' }}">
                    {{ $stats['over_limit'] }}</p>
                <p class="mt-1 text-xs text-slate-500">Day, Evening, or shared Part-time cap</p>
            </article>
        </div>

        <form method="GET" action="{{ route('dean.faculty_workload') }}" data-workload-filter-form
            class="mb-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="{{ $filterValidationMessage ? '' : 'hidden' }} mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                data-workload-filter-alert role="alert" aria-live="assertive">{{ $filterValidationMessage }}</div>
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                <label class="text-xs font-semibold text-slate-500">Academic Year / Semester
                    <select name="semester" class="field-input mt-1 w-full" onchange="this.form.submit()">
                        @forelse($semesters as $option)
                            <option value="{{ $option->sem_id }}" @selected(old('semester', $semester?->sem_id) === $option->sem_id)>
                                {{ $option->academicYear?->ay_academic_year ?? 'Academic year' }} ·
                                {{ $option->sem_name }}{{ $option->sem_is_active ? ' (Current)' : '' }}</option>
                        @empty<option value="">No semesters found</option>
                        @endforelse
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-500">Department
                    <select name="department" class="field-input mt-1 w-full">
                        <option value="all" @selected(old('department', $selectedDepartment) === 'all')>All departments</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->dept_id }}" @selected(old('department', $selectedDepartment) === $department->dept_id)>
                                {{ $department->dept_code }} · {{ $department->dept_name }}</option>
                        @endforeach
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-500">Employment
                    <select name="employment" class="field-input mt-1 w-full">
                        <option value="all" @selected(old('employment', $employmentFilter) === 'all')>All employment types</option>
                        <option value="full_time" @selected(old('employment', $employmentFilter) === 'full_time')>Full-time</option>
                        <option value="part_time" @selected(old('employment', $employmentFilter) === 'part_time')>Part-time</option>
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-500">Workload Status
                    <select name="status" class="field-input mt-1 w-full">
                        <option value="all" @selected(old('status', $statusFilter) === 'all')>All statuses</option>
                        <option value="attention" @selected(old('status', $statusFilter) === 'attention')>Near / Over Limit</option>
                        <option value="available" @selected(old('status', $statusFilter) === 'available')>Within limit</option>
                    </select>
                </label>
                <label class="text-xs font-semibold text-slate-500">Search Faculty
                    <input name="search" value="{{ old('search', $search) }}" class="field-input mt-1 w-full"
                        placeholder="Name, email, subject...">
                </label>
            </div>
            <div class="mt-3 flex justify-end gap-2"><a href="{{ route('dean.faculty_workload') }}"
                    class="topbar-btn btn-secondary">Clear</a><button class="topbar-btn btn-primary"
                    type="submit">Apply filters</button></div>
        </form>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500">
                        <tr>
                            <th class="p-3">Faculty</th>
                            <th class="p-3">Department / Designation</th>
                            <th class="p-3">Employment</th>
                            <th class="p-3">Subjects</th>
                            <th class="p-3">Day</th>
                            <th class="p-3">Evening</th>
                            <th class="p-3">Total load</th>
                            <th class="p-3">Status</th>
                            <th class="p-3">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($facultyRows as $row)
                            <tr>
                                <td class="p-3">
                                    <div class="flex min-w-[200px] items-center gap-3"><span
                                            class="flex h-10 w-10 shrink-0 items-center justify-center overflow-hidden rounded-full bg-blue-100 font-bold text-blue-700"><span
                                                data-photo-fallback
                                                class="{{ $row['photo'] ? 'hidden' : '' }}">{{ strtoupper(mb_substr($row['name'], 0, 1)) }}</span>
                                            @if ($row['photo'])
                                                <img class="h-full w-full object-cover" src="{{ $row['photo'] }}"
                                                    alt=""
                                                    onerror="this.onerror=null; this.hidden=true; this.previousElementSibling.classList.remove('hidden')">
                                            @endif
                                        </span>
                                        <span class="min-w-0"><span
                                                class="block truncate font-semibold">{{ $row['name'] }}</span><span
                                                class="block truncate text-xs text-slate-500">{{ $row['email'] ?? 'No linked account' }}</span></span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div>{{ $row['department'] }}{{ $row['rank'] ? ' · ' . $row['rank'] : '' }}</div>
                                    <div class="text-xs text-slate-500">
                                        {{ $row['position'] ?: 'No special designation' }}</div>
                                </td>
                                <td class="p-3">{{ $row['employment'] === 'part_time' ? 'Part-time' : 'Full-time' }}
                                    <div class="text-xs {{ $row['active'] ? 'text-emerald-700' : 'text-slate-400' }}">
                                        {{ $row['active'] ? 'Active account' : 'Inactive / No Account' }}</div>
                                </td>
                                <td class="max-w-[220px] p-3 text-slate-600">{{ $row['subjects'] }}</td>
                                <td class="p-3"><strong
                                        class="text-emerald-700">{{ number_format($row['day_hours'], 1) }}h</strong><span
                                        class="text-xs text-slate-400"> / {{ $row['day_cap'] }}h</span>
                                    @if ($row['employment'] === 'part_time')
                                        <div class="text-xs text-slate-400">Part of shared cap</div>
                                    @endif
                                </td>
                                <td class="p-3"><strong
                                        class="text-indigo-700">{{ number_format($row['evening_hours'], 1) }}h</strong><span
                                        class="text-xs text-slate-400"> / {{ $row['evening_cap'] }}h</span>
                                    @if ($row['employment'] === 'part_time')
                                        <div class="text-xs text-slate-400">Part of shared cap</div>
                                    @endif
                                </td>
                                <td class="min-w-[170px] p-3">
                                    <div class="font-semibold">{{ number_format($row['total_hours'], 1) }}h <span
                                            class="text-xs font-normal text-slate-400">/
                                            {{ $row['total_cap'] }}h{{ $row['employment'] === 'part_time' ? ' shared' : ' combined caps' }}</span>
                                    </div>
                                    <div class="mt-1.5 h-2 overflow-hidden rounded-full bg-slate-100">
                                        <div class="h-full rounded-full {{ $row['status_color'] === 'rose' ? 'bg-rose-500' : ($row['status_color'] === 'amber' ? 'bg-amber-500' : ($row['status_color'] === 'emerald' ? 'bg-emerald-500' : 'bg-slate-300')) }}"
                                            style="width: {{ $row['progress'] }}%"></div>
                                    </div>
                                </td>
                                <td class="p-3"><span
                                        class="badge {{ $row['status_color'] === 'rose' ? 'badge-red' : ($row['status_color'] === 'amber' ? 'badge-amber' : ($row['status_color'] === 'emerald' ? 'badge-green' : 'badge-grey')) }}">{{ $row['status'] }}</span>
                                </td>
                                <td class="p-3">
                                    @if ($departments->contains('dept_id', $row['department_id']))
                                        <button type="button" class="topbar-btn btn-secondary" data-edit-designation
                                            data-id="{{ $row['id'] }}" data-name="{{ $row['name'] }}"
                                            data-position="{{ $row['position'] ?? '' }}"
                                            data-employment="{{ $row['employment'] }}">Edit
                                        Designation</button>@else<span class="text-xs text-slate-400">View
                                            Only</span>
                                    @endif
                                </td>
                            </tr>
                        @empty<tr>
                                <td colspan="9" class="p-8 text-center text-slate-500">No faculty match these
                                    filters for this semester.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($facultyRows->total())
                <div
                    class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4 text-sm text-slate-500">
                    <span>Showing {{ $facultyRows->firstItem() }}–{{ $facultyRows->lastItem() }} of
                        {{ $facultyRows->total() }} faculty</span>{{ $facultyRows->links() }}</div>
            @endif
        </section>
    </main>
    <div id="workload-designation-modal"
        class="fixed inset-0 z-[120] hidden items-center justify-center bg-slate-950/50 p-4" role="dialog"
        aria-modal="true" aria-labelledby="workload-designation-title"
        onclick="if(event.target===this)closeWorkloadDesignation()">
        <form id="workload-designation-form" method="POST" action=""
            class="w-full max-w-lg rounded-2xl bg-white p-5 shadow-2xl" onclick="event.stopPropagation()">
            @csrf @method('PUT')
            <input type="hidden" name="_workload_form" value="designation">
            <input type="hidden" name="_workload_faculty_id"
                value="{{ old('_workload_form') === 'designation' ? old('_workload_faculty_id') : '' }}">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <h2 id="workload-designation-title" class="text-lg font-bold">Edit faculty designation</h2>
                    <p id="workload-designation-name" class="mt-1 text-sm text-slate-500"></p>
                </div><button type="button" class="topbar-btn btn-secondary"
                    onclick="closeWorkloadDesignation()">Close</button>
            </div>
            <div class="{{ old('_workload_form') === 'designation' && $designationValidationMessage ? '' : 'hidden' }} mt-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700"
                data-workload-designation-alert role="alert" aria-live="assertive">
                {{ old('_workload_form') === 'designation' ? $designationValidationMessage : '' }}</div>
            <label for="workload-designation-select"
                class="mt-5 block text-xs font-semibold uppercase tracking-wide text-slate-500">Special
                designation</label>
            <select id="workload-designation-select" name="special_position" class="field-input mt-1 w-full">
                <option value="" @selected(old('_workload_form') === 'designation' && old('special_position') === null)>No special designation</option>
                @foreach (\App\Models\Designation::orderBy('name')->get() as $d)
                    <option value="{{ $d->name }}" data-cap="{{ $d->day_cap }}"
                        @selected(old('_workload_form') === 'designation' && old('special_position') === $d->name)>{{ $d->name }} ({{ $d->day_cap }}h Day cap)</option>
                @endforeach
            </select>
            <p id="workload-designation-note" class="mt-3 rounded-lg bg-blue-50 p-3 text-sm text-blue-800"></p>
            <div class="mt-5 flex justify-end gap-2"><button type="button" class="topbar-btn btn-secondary"
                    onclick="closeWorkloadDesignation()">Cancel</button><button type="submit"
                    class="topbar-btn btn-primary">Save Designation</button></div>
        </form>
    </div>
    <script>
        const workloadDesignationModal = document.getElementById('workload-designation-modal');
        const workloadDesignationForm = document.getElementById('workload-designation-form');
        const workloadDesignationSelect = document.getElementById('workload-designation-select');
        let workloadDesignationEmployment = 'full_time';

        function syncWorkloadDesignationNote() {
            const cap = workloadDesignationSelect.selectedOptions[0]?.dataset.cap ||
                {{ \App\Services\ScheduleAssignmentService::TOTAL_MAX_HOURS }};
            document.getElementById('workload-designation-note').textContent = workloadDesignationEmployment ===
                'part_time' ?
                'Part-time faculty have one shared {{ \App\Services\ScheduleAssignmentService::PART_TIME_MAX_HOURS }}-hour limit across Day and Evening. The designation does not change this shared limit.' :
                `This designation sets the Day limit to ${cap} hours. Evening remains capped at {{ \App\Services\ScheduleAssignmentService::EVENING_MAX_HOURS }} hours.`;
        }

        function openWorkloadDesignation(button) {
            workloadDesignationForm.action = `{{ url('/dean/faculty-workload') }}/${button.dataset.id}/designation`;
            workloadDesignationForm.elements._workload_faculty_id.value = button.dataset.id;
            document.getElementById('workload-designation-name').textContent = button.dataset.name;
            workloadDesignationEmployment = button.dataset.employment;
            workloadDesignationSelect.value = button.dataset.position || '';
            showWorkloadDesignationAlert('');
            syncWorkloadDesignationNote();
            workloadDesignationModal.classList.remove('hidden');
            workloadDesignationModal.classList.add('flex');
        }

        function closeWorkloadDesignation() {
            workloadDesignationModal.classList.add('hidden');
            workloadDesignationModal.classList.remove('flex');
        }

        function showWorkloadDesignationAlert(message) {
            const alert = workloadDesignationForm.querySelector('[data-workload-designation-alert]');
            alert.textContent = message;
            alert.classList.toggle('hidden', !message);
        }
        document.querySelectorAll('[data-edit-designation]').forEach(button => button.addEventListener('click', () =>
            openWorkloadDesignation(button)));
        workloadDesignationSelect.addEventListener('change', syncWorkloadDesignationNote);
        workloadDesignationSelect.addEventListener('change', () => {
            showWorkloadDesignationAlert(/[<>]/.test(workloadDesignationSelect.value) ?
                'Angle brackets (< and >) are not allowed.' : '');
        });
        workloadDesignationForm.addEventListener('submit', event => {
            if (!/[<>]/.test(workloadDesignationSelect.value)) return;
            event.preventDefault();
            showWorkloadDesignationAlert('Angle brackets (< and >) are not allowed.');
            workloadDesignationSelect.focus();
        });
        document.querySelector('form[data-workload-filter-form]')?.addEventListener('submit', event => {
            const form = event.currentTarget;
            const invalidField = [...form.querySelectorAll('input, select')].find(field => /[<>]/.test(field
            .value));
            const alert = form.querySelector('[data-workload-filter-alert]');
            if (!invalidField) return;
            event.preventDefault();
            alert.textContent = 'Angle brackets (< and >) are not allowed.';
            alert.classList.remove('hidden');
            invalidField.focus();
        });
        document.querySelector('form[data-workload-filter-form]')?.querySelectorAll('input, select').forEach(field => {
            ['input', 'change'].forEach(eventName => field.addEventListener(eventName, () => {
                const form = field.form;
                const invalidField = [...form.querySelectorAll('input, select')].find(candidate =>
                    /[<>]/.test(candidate.value));
                const alert = form.querySelector('[data-workload-filter-alert]');
                alert.textContent = invalidField ? 'Angle brackets (< and >) are not allowed.' : '';
                alert.classList.toggle('hidden', !invalidField);
            }));
        });
        @if ($designationValidationMessage && old('_workload_form') === 'designation' && old('_workload_faculty_id'))
            const failedWorkloadFacultyId = @json(old('_workload_faculty_id'));
            workloadDesignationForm.action =
                `{{ url('/dean/faculty-workload') }}/${encodeURIComponent(failedWorkloadFacultyId)}/designation`;
            workloadDesignationForm.elements._workload_faculty_id.value = failedWorkloadFacultyId;
            const failedWorkloadButton = [...document.querySelectorAll('[data-edit-designation]')].find(button => button
                .dataset.id === failedWorkloadFacultyId);
            if (failedWorkloadButton) {
                document.getElementById('workload-designation-name').textContent = failedWorkloadButton.dataset.name;
                workloadDesignationEmployment = failedWorkloadButton.dataset.employment;
            }
            syncWorkloadDesignationNote();
            workloadDesignationModal.classList.remove('hidden');
            workloadDesignationModal.classList.add('flex');
        @endif
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') closeWorkloadDesignation();
        });
    </script>
    </div><!-- app-main opened by dean_header -->
    </div><!-- app-shell opened by dean_header -->
</body>

</html>
