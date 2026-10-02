<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Subject Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            background: #0f172a;
            color: #fff;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            opacity: 0;
            transform: translateY(12px);
            transition: all .25s ease;
            pointer-events: none;
            max-width: 320px;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Scroll fix: the content area scrolls, not the body */
        #page-subjects {
            padding-bottom: 32px;
            scrollbar-gutter: stable;
        }
    </style>
</head>

<body class="overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">

    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')

        {{-- main = flex column; header keeps natural height, content fills the rest and scrolls --}}
        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'Subject Management',
                'badgeText' => ($programName ?? 'BSIS') . ' Department',
            ])

            <div id="page-subjects" class="page-content min-h-0 flex-1 overflow-y-auto">
                <div class="mb-5 flex items-center justify-between gap-3">
                    <div>
                        <div class="text-[20px] font-extrabold text-slate-900">Subject Management</div>
                        <div class="mt-1 text-[13px] text-slate-500">{{ $programName ?? 'BSIS' }} Department — filter by
                            year &amp; semester</div>
                    </div>
                    <button
                        class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white shadow-sm transition hover:bg-blue-700"
                        onclick="openModal('modal-add-subject')">+ Add Subject</button>
                </div>

                {{-- Year + Semester filters --}}
                <div
                    class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="min-w-[160px]">
                        <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Year
                            Level</label>
                        <select id="filter-year" onchange="applySubjectFilters()"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500">
                            <option value="">— Select year —</option>
                            @foreach ($yearLevels ?? [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'] as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="min-w-[160px]">
                        <label
                            class="mb-1.5 block text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">Semester</label>
                        <select id="filter-semester" onchange="applySubjectFilters()"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500">
                            <option value="">— Select semester —</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-2 pb-0.5">
                        <span id="filter-hint" class="text-[12px] text-slate-400">Select year and semester to view
                            subjects</span>
                        <span id="filter-count"
                            class="hidden rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600"></span>
                    </div>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse text-left">
                            <thead>
                                <tr>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Course Code</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Descriptive Title</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Units</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Lec</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Lab</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Assigned Faculty</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Status</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody id="subjects-table">
                                <tr id="subjects-empty-row">
                                    <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">
                                        Select a year level and semester above to load subjects.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    {{-- ADD SUBJECT MODAL --}}
    <div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-add-subject-backdrop"></div>
    <div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto p-4" id="modal-add-subject">
        <div
            class="my-auto w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
            <form action="{{ route('chair.subject.store') }}" method="POST">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div class="text-lg font-bold text-slate-900">Add New Subject</div>
                    <button type="button" class="text-2xl text-slate-500 hover:text-slate-700"
                        onclick="closeModal('modal-add-subject')">×</button>
                </div>
                <div class="space-y-4 p-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Course Code</label>
                            <input
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                name="subj_code" value="{{ old('subj_code') }}" placeholder="e.g. CC 314">
                            @error('subj_code')
                                <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Department</label>
                            <select
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                name="subj_dept_id">
                                <option value="">-- Select --</option>
                                @foreach ($departments as $dept)
                                    <option value="{{ $dept->college_id ?? $dept->dept_id }}"
                                        @selected(old('subj_dept_id') == ($dept->college_id ?? $dept->dept_id))>{{ $dept->college_name ?? $dept->dept_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('subj_dept_id')
                                <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Program</label>
                        <select
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            name="subj_prog_id">
                            <option value="">-- Select --</option>
                            @foreach ($programs as $prog)
                                <option value="{{ $prog->dept_id ?? $prog->prog_id }}" @selected(old('subj_prog_id') == ($prog->dept_id ?? $prog->prog_id))>
                                    {{ $prog->dept_name ?? $prog->prog_name }}</option>
                            @endforeach
                        </select>
                        @error('subj_prog_id')
                            <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Descriptive Title</label>
                        <input
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            name="subj_name" value="{{ old('subj_name') }}"
                            placeholder="e.g. Web Systems and Technologies">
                        @error('subj_name')
                            <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lecture Hrs</label>
                            <input
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                name="subj_lecture_hours" type="number" min="0" max="6"
                                value="{{ old('subj_lecture_hours', 0) }}">
                            @error('subj_lecture_hours')
                                <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lab Hrs</label>
                            <input
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                name="subj_lab_hours" type="number" min="0" max="6"
                                value="{{ old('subj_lab_hours', 0) }}">
                            @error('subj_lab_hours')
                                <div class="mt-1 text-[12px] text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <button type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700"
                        onclick="closeModal('modal-add-subject')">Cancel</button>
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700">Add
                        Subject</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT SUBJECT MODAL --}}
    <div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-edit-subject-backdrop"></div>
    <div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto p-4" id="modal-edit-subject">
        <div
            class="my-auto w-full max-w-2xl rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
            <form id="edit-subj-form" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit-subj-dept-id" name="subj_dept_id">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div class="text-lg font-bold text-slate-900">Edit Subject</div>
                    <button type="button" class="text-2xl text-slate-500 hover:text-slate-700"
                        onclick="closeModal('modal-edit-subject')">×</button>
                </div>
                <div class="space-y-4 p-5">
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Course Code</label>
                        <input
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            id="edit-subj-code" name="subj_code">
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Descriptive Title</label>
                        <input
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            id="edit-subj-name" name="subj_name">
                    </div>
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lecture Hrs</label>
                            <input
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                id="edit-subj-lec" name="subj_lecture_hours" type="number" min="0"
                                max="6">
                        </div>
                        <div>
                            <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Lab Hrs</label>
                            <input
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                                id="edit-subj-lab" name="subj_lab_hours" type="number" min="0"
                                max="6">
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                    <button type="button"
                        class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700"
                        onclick="closeModal('modal-edit-subject')">Cancel</button>
                    <button type="submit"
                        class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700">Save
                        Changes</button>
                </div>
            </form>
        </div>
    </div>

    {{--
    ASSIGN FACULTY MODAL
    Subject + Faculty + Section + Semester → creates Study_Load.
    Day/Time/Room stay on PBS.
--}}
    <div class="fixed inset-0 z-40 hidden bg-slate-900/40" id="modal-assign-backdrop"></div>
    <div class="fixed inset-0 z-50 hidden items-center justify-center overflow-y-auto p-4" id="modal-assign">
        <div
            class="my-auto w-full max-w-lg rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_rgba(15,23,42,0.25)]">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <div class="text-lg font-bold text-slate-900">Assign Faculty</div>
                    <div id="assign-subj-title" class="text-[12px] text-slate-500 mt-0.5"></div>
                </div>
                <button type="button" class="text-2xl text-slate-500 hover:text-slate-700"
                    onclick="closeModal('modal-assign')">×</button>
            </div>
            <div class="space-y-4 p-5">
                <input type="hidden" id="assign-subj-id">
                <div>
                    <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Select Faculty</label>
                    <select
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                        id="assign-faculty">
                        <option value="">— Choose Faculty —</option>
                        @foreach ($faculty as $f)
                            <option value="{{ $f['id'] }}">{{ $f['name'] }} ({{ $f['units'] }}u/30u)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Semester</label>
                        <select
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            id="assign-semester">
                            <option value="">— Select semester —</option>
                            @foreach ($semesters as $sem)
                                <option value="{{ $sem->sem_id }}" data-ordinal="{{ $sem->sem_ordinal ?? '' }}">
                                    {{ $sem->sem_label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="mb-1.5 block text-[12px] font-semibold text-slate-700">Section</label>
                        <select
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-blue-500"
                            id="assign-section">
                            <option value="">— Select section —</option>
                            @foreach ($section as $sec)
                                <option value="{{ $sec->sec_id }}" data-year="{{ $sec->sec_year_level }}">
                                    {{ $sec->sec_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div id="assign-conflict"
                    class="hidden bg-red-50 border border-red-200 rounded-lg px-3.5 py-2.5 text-[12.5px] text-red-700">
                    <div class="font-bold mb-0.5">Already Assigned!</div>
                    <div id="assign-conflict-message"></div>
                </div>
            </div>
            <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-5 py-4">
                <button type="button"
                    class="rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-[12px] font-semibold text-slate-700"
                    onclick="closeModal('modal-assign')">Cancel</button>
                <button type="button"
                    class="rounded-xl bg-blue-600 px-3.5 py-2 text-[12px] font-semibold text-white hover:bg-blue-700"
                    id="assign-save-btn" onclick="saveAssign()">Assign Faculty</button>
            </div>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const SUBJECT_UPDATE_URL_TEMPLATE = "{{ route('chair.subject.update', ['id' => '__ID__']) }}";
        const SUBJECT_DESTROY_URL_TEMPLATE = "{{ route('chair.subject.destroy', ['id' => '__ID__']) }}";
        const ASSIGN_URL = "{{ route('chair.faculty_load.assign') }}";

        // All BSIS (program-scoped) subjects from the server
        const ALL_SUBJECTS = @json($subjectsForJs ?? []);

        // ── MODALS ───────────────────────────────────────────────────────────────
        function openModal(id) {
            const node = document.getElementById(id);
            const backdrop = document.getElementById(id + '-backdrop');
            if (node) {
                node.classList.remove('hidden');
                node.classList.add('flex');
            }
            if (backdrop) backdrop.classList.remove('hidden');
        }

        function closeModal(id) {
            const node = document.getElementById(id);
            const backdrop = document.getElementById(id + '-backdrop');
            if (node) {
                node.classList.add('hidden');
                node.classList.remove('flex');
            }
            if (backdrop) backdrop.classList.add('hidden');
        }

        // ── FILTERS → render table ───────────────────────────────────────────────
        function applySubjectFilters() {
            const year = document.getElementById('filter-year').value;
            const sem = document.getElementById('filter-semester').value;
            const tbody = document.getElementById('subjects-table');
            const hint = document.getElementById('filter-hint');
            const count = document.getElementById('filter-count');

            if (!year || !sem) {
                tbody.innerHTML = `
      <tr id="subjects-empty-row">
        <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">
          Select a year level and semester above to load subjects.
        </td>
      </tr>`;
                hint.classList.remove('hidden');
                hint.textContent = 'Select year and semester to view subjects';
                count.classList.add('hidden');
                return;
            }

            // Strict filter: only subjects that match BOTH year level and semester
            const list = ALL_SUBJECTS.filter(s =>
                String(s.year_level) === String(year) && String(s.semester) === String(sem)
            );

            if (list.length === 0) {
                tbody.innerHTML = `
      <tr>
        <td colspan="8" class="px-3 py-10 text-center text-sm text-slate-400">
          No subjects for this year and semester.
        </td>
      </tr>`;
                hint.classList.add('hidden');
                count.classList.remove('hidden');
                count.textContent = '0 subjects';
                return;
            }

            const yearLabel = document.getElementById('filter-year').selectedOptions[0]?.text || year;
            const semLabel = document.getElementById('filter-semester').selectedOptions[0]?.text || sem;
            hint.classList.remove('hidden');
            hint.textContent = `${yearLabel} · ${semLabel}`;
            count.classList.remove('hidden');
            count.textContent = `${list.length} subject${list.length === 1 ? '' : 's'}`;

            tbody.innerHTML = list.map(s => {
                const facultyCell = s.faculty ?
                    escapeHtml(s.faculty) :
                    '<span class="text-red-600">Unassigned</span>';
                const statusBadge = s.faculty ?
                    '<span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700">Assigned</span>' :
                    '<span class="inline-flex whitespace-nowrap rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-600">No Faculty</span>';
                const actionBtn = s.faculty ?
                    `<button class="rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-[11px] font-semibold text-slate-700 transition hover:bg-slate-50"
           onclick="openEditSubject('${s.id}','${escapeAttr(s.code)}','${escapeAttr(s.name)}','${s.lec}','${s.lab}','${s.dept_id || ''}')">Edit</button>` :
                    `<button class="rounded-lg bg-blue-600 px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-blue-700"
           onclick="openAssignModal('${s.id}','${escapeAttr(s.code)}','${escapeAttr(s.name)}','${s.units}')">Assign</button>`;

                return `
      <tr class="hover:bg-slate-50">
        <td class="border-b border-slate-100 px-3 py-3 text-sm font-mono font-bold text-slate-900">${escapeHtml(s.code)}</td>
        <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">${escapeHtml(s.name)}</td>
        <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.units}</td>
        <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.lec}</td>
        <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.lab}</td>
        <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${facultyCell}</td>
        <td class="border-b border-slate-100 px-3 py-3">${statusBadge}</td>
        <td class="whitespace-nowrap border-b border-slate-100 px-3 py-3">
          ${actionBtn}
          <form action="${SUBJECT_DESTROY_URL_TEMPLATE.replace('__ID__', s.id)}" method="POST" class="inline" onsubmit="return confirm('Deactivate ${escapeAttr(s.code)}? It will be hidden from active lists but kept for historical records.');">
            <input type="hidden" name="_token" value="${CSRF_TOKEN}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="ml-2 rounded-lg bg-red-50 px-3 py-1.5 text-[11px] font-semibold text-red-600 transition hover:bg-red-100">Delete</button>
          </form>
        </td>
      </tr>`;
            }).join('');
        }

        function escapeHtml(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function escapeAttr(str) {
            if (str == null) return '';
            return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'").replace(/"/g, '&quot;');
        }

        // ── EDIT SUBJECT ─────────────────────────────────────────────────────────
        function openEditSubject(id, code, name, lec, lab, deptId) {
            document.getElementById('edit-subj-form').action = SUBJECT_UPDATE_URL_TEMPLATE.replace('__ID__', id);
            document.getElementById('edit-subj-code').value = code;
            document.getElementById('edit-subj-name').value = name;
            document.getElementById('edit-subj-lec').value = lec;
            document.getElementById('edit-subj-lab').value = lab;
            document.getElementById('edit-subj-dept-id').value = deptId;
            openModal('modal-edit-subject');
        }

        // ── ASSIGN FACULTY ───────────────────────────────────────────────────────
        function openAssignModal(subjId, code, name, units) {
            document.getElementById('assign-subj-id').value = subjId;
            document.getElementById('assign-subj-title').textContent = `${code} — ${name} (${units}u)`;
            document.getElementById('assign-faculty').value = '';
            document.getElementById('assign-conflict').classList.add('hidden');

            // Pre-select semester from page filter (1st / 2nd)
            const filterSem = document.getElementById('filter-semester').value;
            const semSelect = document.getElementById('assign-semester');
            semSelect.value = '';
            if (filterSem) {
                const matchOrdinal = filterSem === '1' ? '1st Sem' : '2nd Sem';
                for (const opt of semSelect.options) {
                    if (opt.dataset.ordinal === matchOrdinal) {
                        opt.selected = true;
                        break;
                    }
                }
            }

            // Prefer sections matching the selected year level; if none match, show all.
            const filterYear = document.getElementById('filter-year').value;
            const secSelect = document.getElementById('assign-section');
            secSelect.value = '';

            let matchCount = 0;
            for (const opt of secSelect.options) {
                if (!opt.value) continue;
                const y = opt.dataset.year;
                const matches = !filterYear || !y || String(y) === String(filterYear);
                opt.hidden = !matches;
                if (matches) matchCount++;
            }
            // No year-matched sections (or year_level missing on rows) → show every section
            if (matchCount === 0) {
                for (const opt of secSelect.options) {
                    if (opt.value) opt.hidden = false;
                }
            }

            openModal('modal-assign');
        }

        async function saveAssign() {
            const subjId = document.getElementById('assign-subj-id').value;
            const facId = document.getElementById('assign-faculty').value;
            const semId = document.getElementById('assign-semester').value;
            const secId = document.getElementById('assign-section').value;

            document.getElementById('assign-conflict').classList.add('hidden');

            if (!facId || !semId || !secId) {
                showToast('Please fill in all fields.');
                return;
            }

            const btn = document.getElementById('assign-save-btn');
            btn.disabled = true;
            btn.textContent = 'Saving...';

            try {
                const res = await fetch(ASSIGN_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        subj_id: subjId,
                        fac_id: facId,
                        sec_id: secId,
                        sem_id: semId
                    })
                });

                const data = await res.json();

                if (!res.ok) {
                    if (data.conflict) {
                        document.getElementById('assign-conflict-message').textContent = data.message;
                        document.getElementById('assign-conflict').classList.remove('hidden');
                    } else {
                        showToast(data.message || 'Failed to assign faculty.');
                    }
                    return;
                }

                showToast(data.message || 'Faculty assigned successfully!');
                closeModal('modal-assign');
                setTimeout(() => window.location.reload(), 600);
            } catch (err) {
                showToast('Failed to assign faculty. Please try again.');
                console.error(err);
            } finally {
                btn.disabled = false;
                btn.textContent = 'Assign Faculty';
            }
        }

        // ── TOAST ────────────────────────────────────────────────────────────────
        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3200);
        }
    </script>
</body>

</html>
