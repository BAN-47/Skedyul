<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Sections</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">
    <div class="app-shell">
        @include($isDean ? 'partials.dean_sidebar' : 'partials.admin_sidebar')

        <div class="app-main">
            @if ($isDean)
                <div class="topbar">
                    <div class="topbar-title">Section Management</div>
                    <a href="{{ route('dean.pending_approvals') }}" class="btn btn-secondary">Pending Approvals</a>
                </div>
            @else
                @include('partials.admin_header', ['title' => 'Section Management'])
            @endif

            <div class="page-content">
                @if (session('success'))
                    <div class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700">
                        {{ session('error') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm text-red-700">
                        <div class="font-semibold">Please correct the following:</div>
                        <ul class="mt-1 list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <div class="text-[20px] font-extrabold text-slate-900">Sections</div>
                        <div class="mt-1 text-[13px] text-slate-500">
                            {{ $isDean ? 'Manage sections in your college.' : 'Manage sections across all programs.' }}
                        </div>
                    </div>
                    <button type="button" onclick="openNewSection()" class="btn btn-primary">+ Add Section</button>
                </div>

                <form method="GET" action="{{ route($routePrefix) }}" class="card mb-4" style="padding: 16px;">
                    <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                        <div>
                            <div class="text-sm font-bold text-slate-800">Filter sections</div>
                            <div class="text-xs text-slate-500">Filter by department, year level, or shift.</div>
                        </div>
                        <a href="{{ route($routePrefix) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">
                            Clear
                        </a>
                    </div>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="field-label" for="filter-department">Department</label>
                            <select class="field-input" id="filter-department" name="department">
                                <option value="">All departments</option>
                                @foreach ($programs as $program)
                                    <option value="{{ $program->dept_id }}" @selected($filters['department'] === $program->dept_id)>
                                        {{ $program->dept_name }} ({{ $program->dept_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="filter-year-level">Year level</label>
                            <select class="field-input" id="filter-year-level" name="year_level">
                                <option value="">All year levels</option>
                                @foreach (range(1, 5) as $level)
                                    <option value="{{ $level }}" @selected($filters['year_level'] === (string) $level)>
                                        Year {{ $level }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="filter-shift">Shift</label>
                            <select class="field-input" id="filter-shift" name="shift">
                                <option value="">All shifts</option>
                                <option value="day" @selected($filters['shift'] === 'day')>Day</option>
                                <option value="evening" @selected($filters['shift'] === 'evening')>Evening</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="btn btn-primary">Apply filters</button>
                    </div>
                </form>

                <div class="card">
                    <div class="flex flex-wrap items-end justify-between gap-3 border-b border-slate-100 p-4">
                        <div>
                            <label class="field-label" for="section-search">Search sections</label>
                            <input class="field-input w-full sm:w-80" id="section-search" type="search"
                                placeholder="Search section, program, year, shift, or status" autocomplete="off">
                        </div>
                        <p class="text-sm text-slate-500" id="section-search-count" role="status" aria-live="polite"></p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Section</th>
                                    <th>Program</th>
                                    <th>Academic Year / Semester</th>
                                    <th>Year</th>
                                    <th>Students / Capacity</th>
                                    <th>Shift</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($sections as $section)
                                    <tr data-section-row>
                                        <td class="font-semibold">{{ $section->sec_name }}</td>
                                        <td>
                                            {{ $section->program?->dept_code ?? '—' }}
                                            <span class="text-slate-500">{{ $section->program?->dept_name }}</span>
                                        </td>
                                        <td>
                                            {{ $section->academicYear?->ay_academic_year ?? '—' }}
                                            / {{ $section->semester?->sem_name ?? '—' }}
                                        </td>
                                        <td>{{ $section->sec_year_level }}</td>
                                        <td>{{ $section->sec_no_of_student }} / {{ $section->sec_max_capacity }}</td>
                                        <td>{{ $section->sec_is_evening ? 'Evening' : 'Day' }}</td>
                                        <td>
                                            <span class="badge {{ $section->sec_status === 'active' ? 'badge-green' : 'badge-amber' }}">
                                                {{ ucfirst($section->sec_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="flex gap-1.5">
                                                <button type="button"
                                                    class="btn btn-secondary text-[11px] px-3 py-1.5 edit-section"
                                                    data-id="{{ $section->sec_id }}"
                                                    data-name="{{ $section->sec_name }}"
                                                    data-program="{{ $section->sec_dept_id }}"
                                                    data-year="{{ $section->sec_ay_id }}"
                                                    data-semester="{{ $section->sec_sem_id }}"
                                                    data-level="{{ $section->sec_year_level }}"
                                                    data-capacity="{{ $section->sec_max_capacity }}"
                                                    data-status="{{ $section->sec_status }}"
                                                    data-evening="{{ $section->sec_is_evening ? '1' : '0' }}">
                                                    Edit
                                                </button>
                                                <button type="button"
                                                    class="btn btn-secondary text-[11px] px-3 py-1.5 view-section"
                                                    data-name="{{ $section->sec_name }}"
                                                    data-program="{{ $section->program?->dept_code ?? '—' }} — {{ $section->program?->dept_name ?? '—' }}"
                                                    data-year="{{ $section->academicYear?->ay_academic_year ?? '—' }}"
                                                    data-semester="{{ $section->semester?->sem_name ?? '—' }}"
                                                    data-level="{{ $section->sec_year_level }}"
                                                    data-enrollment="{{ $section->sec_no_of_student }} / {{ $section->sec_max_capacity }}"
                                                    data-shift="{{ $section->sec_is_evening ? 'Evening' : 'Day' }}"
                                                    data-status="{{ ucfirst($section->sec_status) }}">
                                                    View
                                                </button>
                                                <form method="POST" action="{{ route($routePrefix . '.destroy', $section->sec_id) }}"
                                                    onsubmit="return confirm('Delete section {{ $section->sec_name }}?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr id="section-empty-row">
                                        <td colspan="8" class="py-10 text-center text-slate-400" id="section-empty-message">
                                            No sections match these filters. Try changing or clearing your filters.
                                        </td>
                                    </tr>
                                @endforelse
                                @if ($sections->isNotEmpty())
                                    <tr id="section-empty-row" style="display:none">
                                        <td colspan="8" class="py-10 text-center text-slate-400">
                                            No sections match your search.
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 px-4 py-3">
                        <p class="text-sm text-slate-500" id="section-page-info" role="status" aria-live="polite"></p>
                        <div id="section-pagination" class="flex items-center gap-2">
                            <button type="button" id="section-previous" class="btn btn-secondary" aria-label="Previous sections page">Previous</button>
                            <button type="button" id="section-next" class="btn btn-secondary" aria-label="Next sections page">Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal-overlay" id="section-modal">
        <div class="modal-box w-[560px]">
            <form id="section-form" method="POST" action="{{ route($routePrefix . '.store') }}">
                @csrf
                <input type="hidden" name="_method" id="section-method" value="POST">
                <input type="hidden" name="section_id" id="section-id" value="{{ old('section_id') }}">

                <div class="modal-header">
                    <div class="modal-title" id="section-modal-title">Add Section</div>
                    <button type="button" class="modal-close" onclick="closeModal('section-modal')">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div class="col-span-2">
                        <label class="field-label">Program</label>
                        <select class="field-input" name="sec_dept_id" id="section-program" required>
                            <option value="">Select a program</option>
                            @foreach ($programs as $program)
                                <option value="{{ $program->dept_id }}" @selected(old('sec_dept_id') === $program->dept_id)>
                                    {{ $program->dept_name }} ({{ $program->dept_code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Academic Year</label>
                        <select class="field-input" name="sec_ay_id" id="section-year" required>
                            <option value="">Select an academic year</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->ay_id }}"
                                    @selected(old('sec_ay_id', $activeAcademicYear?->ay_id) === $academicYear->ay_id)>
                                    {{ $academicYear->ay_academic_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Semester</label>
                        <select class="field-input" name="sec_sem_id" id="section-semester" required>
                            <option value="">Select a semester</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->sem_id }}"
                                    @selected(old('sec_sem_id', $activeSemester?->sem_id) === $semester->sem_id)>
                                    {{ $semester->sem_name }} — {{ $semester->academicYear?->ay_academic_year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Section Name</label>
                        <input class="field-input" name="sec_name" id="section-name" maxlength="50"
                            value="{{ old('sec_name') }}" placeholder="e.g. BSIT-1A" required>
                    </div>
                    <div>
                        <label class="field-label">Year Level</label>
                        <select class="field-input" name="sec_year_level" id="section-level" required>
                            <option value="">Select year level</option>
                            @foreach (range(1, 5) as $level)
                                <option value="{{ $level }}" @selected((string) old('sec_year_level') === (string) $level)>
                                    Year {{ $level }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Maximum Capacity</label>
                        <input class="field-input" type="number" name="sec_max_capacity" id="section-capacity"
                            min="1" value="{{ old('sec_max_capacity', 40) }}" required>
                    </div>
                    <div>
                        <label class="field-label">Status</label>
                        <select class="field-input" name="sec_status" id="section-status" required>
                            <option value="active" @selected(old('sec_status', 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('sec_status') === 'inactive')>Inactive</option>
                        </select>
                    </div>
                    <div class="col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="sec_is_evening" id="section-evening" value="1"
                                @checked(old('sec_is_evening'))>
                            Evening section
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" onclick="closeModal('section-modal')" class="btn btn-secondary">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="section-submit">Add Section</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal-overlay" id="section-view-modal" role="dialog" aria-modal="true" aria-labelledby="section-view-title">
        <div class="modal-box w-[560px]">
            <div class="modal-header">
                <div class="modal-title" id="section-view-title">Section Details</div>
                <button type="button" class="modal-close" onclick="closeModal('section-view-modal')" aria-label="Close">✕</button>
            </div>
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div><dt class="field-label">Program</dt><dd id="section-view-program" class="font-semibold text-slate-800"></dd></div>
                <div><dt class="field-label">Section</dt><dd id="section-view-name" class="font-semibold text-slate-800"></dd></div>
                <div><dt class="field-label">Academic year</dt><dd id="section-view-year" class="text-slate-700"></dd></div>
                <div><dt class="field-label">Semester</dt><dd id="section-view-semester" class="text-slate-700"></dd></div>
                <div><dt class="field-label">Year level</dt><dd id="section-view-level" class="text-slate-700"></dd></div>
                <div><dt class="field-label">Students / capacity</dt><dd id="section-view-enrollment" class="text-slate-700"></dd></div>
                <div><dt class="field-label">Shift</dt><dd id="section-view-shift" class="text-slate-700"></dd></div>
                <div><dt class="field-label">Status</dt><dd id="section-view-status" class="text-slate-700"></dd></div>
            </dl>
            <div class="modal-footer">
                <button type="button" onclick="closeModal('section-view-modal')" class="btn btn-secondary">Close</button>
            </div>
        </div>
    </div>

    <script>
        const sectionForm = document.getElementById('section-form');
        const sectionUpdateUrl = @json(route($routePrefix . '.update', '__SECTION_ID__'));
        const sectionStoreUrl = @json(route($routePrefix . '.store'));

        function openModal(id) {
            document.getElementById(id)?.classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id)?.classList.remove('open');
        }

        document.querySelectorAll('.modal-overlay').forEach((modal) => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) closeModal(modal.id);
            });
        });

        document.querySelectorAll('.view-section').forEach((button) => {
            button.addEventListener('click', () => {
                const data = button.dataset;
                document.getElementById('section-view-title').textContent = data.name;
                document.getElementById('section-view-name').textContent = data.name;
                document.getElementById('section-view-program').textContent = data.program;
                document.getElementById('section-view-year').textContent = data.year;
                document.getElementById('section-view-semester').textContent = data.semester;
                document.getElementById('section-view-level').textContent = `Year ${data.level}`;
                document.getElementById('section-view-enrollment').textContent = data.enrollment;
                document.getElementById('section-view-shift').textContent = data.shift;
                document.getElementById('section-view-status').textContent = data.status;
                openModal('section-view-modal');
            });
        });

        function setupSectionPagination() {
            const rows = Array.from(document.querySelectorAll('[data-section-row]'));
            const emptyRow = document.getElementById('section-empty-row');
            const searchInput = document.getElementById('section-search');
            const searchCount = document.getElementById('section-search-count');
            const pageSize = 10;
            const pageInfo = document.getElementById('section-page-info');
            const pagination = document.getElementById('section-pagination');
            const previousButton = document.getElementById('section-previous');
            const nextButton = document.getElementById('section-next');
            let currentPage = 0;
            let filteredRows = rows;

            function renderPage() {
                const pageCount = Math.max(1, Math.ceil(filteredRows.length / pageSize));
                const start = currentPage * pageSize;
                const end = Math.min(start + pageSize, filteredRows.length);
                const visibleRows = new Set(filteredRows.slice(start, end));

                rows.forEach((row) => {
                    row.style.display = visibleRows.has(row) ? '' : 'none';
                });

                if (emptyRow) emptyRow.style.display = filteredRows.length === 0 ? '' : 'none';
                pageInfo.textContent = filteredRows.length === 0
                    ? 'Showing 0 of 0 sections'
                    : `Showing ${start + 1}–${end} of ${filteredRows.length} sections`;
                searchCount.textContent = searchInput.value.trim()
                    ? `${filteredRows.length} matching section${filteredRows.length === 1 ? '' : 's'}`
                    : `${filteredRows.length} section${filteredRows.length === 1 ? '' : 's'}`;
                previousButton.disabled = currentPage === 0;
                nextButton.disabled = currentPage >= pageCount - 1;
                pagination.style.display = filteredRows.length > pageSize ? 'flex' : 'none';
            }

            function applySearch() {
                const query = searchInput.value.trim().toLocaleLowerCase();
                filteredRows = rows.filter((row) => {
                    const searchableText = Array.from(row.cells)
                        .slice(0, -1)
                        .map((cell) => cell.textContent)
                        .join(' ')
                        .toLocaleLowerCase();
                    return searchableText.includes(query);
                });
                currentPage = 0;
                renderPage();
            }

            previousButton.addEventListener('click', () => {
                if (currentPage > 0) {
                    currentPage -= 1;
                    renderPage();
                }
            });
            nextButton.addEventListener('click', () => {
                if (currentPage < pageCount - 1) {
                    currentPage += 1;
                    renderPage();
                }
            });
            searchInput.addEventListener('input', applySearch);

            renderPage();
        }

        setupSectionPagination();

        function openNewSection() {
            sectionForm.reset();
            sectionForm.action = sectionStoreUrl;
            document.getElementById('section-method').value = 'POST';
            document.getElementById('section-id').value = '';
            document.getElementById('section-modal-title').textContent = 'Add Section';
            document.getElementById('section-submit').textContent = 'Add Section';
            document.getElementById('section-capacity').value = '40';
            document.getElementById('section-status').value = 'active';
            document.getElementById('section-year').value = @json($activeAcademicYear?->ay_id ?? '');
            document.getElementById('section-semester').value = @json($activeSemester?->sem_id ?? '');
            openModal('section-modal');
        }

        document.querySelectorAll('.edit-section').forEach((button) => {
            button.addEventListener('click', () => {
                const data = button.dataset;
                sectionForm.action = sectionUpdateUrl.replace('__SECTION_ID__', encodeURIComponent(data.id));
                document.getElementById('section-method').value = 'PUT';
                document.getElementById('section-id').value = data.id;
                document.getElementById('section-modal-title').textContent = 'Edit Section';
                document.getElementById('section-submit').textContent = 'Save Changes';
                document.getElementById('section-program').value = data.program;
                document.getElementById('section-year').value = data.year;
                document.getElementById('section-semester').value = data.semester;
                document.getElementById('section-name').value = data.name;
                document.getElementById('section-level').value = data.level;
                document.getElementById('section-capacity').value = data.capacity;
                document.getElementById('section-status').value = data.status;
                document.getElementById('section-evening').checked = data.evening === '1';
                openModal('section-modal');
            });
        });

        @if ($errors->any())
            const oldSectionId = document.getElementById('section-id').value;
            if (oldSectionId) {
                sectionForm.action = sectionUpdateUrl.replace('__SECTION_ID__', encodeURIComponent(oldSectionId));
                document.getElementById('section-method').value = 'PUT';
                document.getElementById('section-modal-title').textContent = 'Edit Section';
                document.getElementById('section-submit').textContent = 'Save Changes';
            }
            openModal('section-modal');
        @endif
    </script>
</body>

</html>
