<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Course Management</title>
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
    </style>
</head>

<body class="font-sans bg-slate-50 text-slate-900 overflow-hidden h-screen">

    @php
        $programs = $programs ?? collect([]);
        $departments = $departments ?? collect([]);
        $yearLevels = $yearLevels ?? [1 => '1st Year', 2 => '2nd Year', 3 => '3rd Year', 4 => '4th Year'];
    @endphp

    <div class="app-shell">
        @include('partials.admin_sidebar')

        <div class="app-main">
            @include('partials.admin_header', ['title' => 'Course Management'])

            <div class="page-content" id="page-subjects">
                @if (session('success'))
                    <div
                        class="mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div
                        class="mb-3 rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-700">
                        {{ session('error') }}
                    </div>
                @endif

                <div class="mb-4 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <div class="text-[20px] font-extrabold text-slate-900">Courses</div>
                        <div class="mt-1 text-[13px] text-slate-500">BIT-CT · BSIS · BSIT — filter by program, year
                            &amp; semester</div>
                    </div>
                    <button type="button" onclick="openModal('modal-add-subject')" class="btn btn-primary">+ Add
                        Course</button>
                </div>

                {{-- Filters --}}
                <div class="card mb-4" style="padding: 16px;">
                    <div class="flex flex-wrap items-end gap-3">
                        <div class="min-w-[180px]">
                            <label class="field-label">Program</label>
                            <select id="filter-program" class="field-input" onchange="applyFilters()">
                                <option value="">— Select program —</option>
                                @foreach ($programs as $prog)
                                    <option value="{{ $prog->dept_id ?? $prog->prog_id }}">
                                        {{ $prog->dept_name ?? $prog->prog_name }}
                                        @if (!empty($prog->dept_code ?? $prog->prog_code))
                                            ({{ $prog->dept_code ?? $prog->prog_code }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-[140px]">
                            <label class="field-label">Year Level</label>
                            <select id="filter-year" class="field-input" onchange="applyFilters()">
                                <option value="">— Select year —</option>
                                @foreach ($yearLevels as $val => $label)
                                    <option value="{{ $val }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-[140px]">
                            <label class="field-label">Semester</label>
                            <select id="filter-semester" class="field-input" onchange="applyFilters()">
                                <option value="">— Select semester —</option>
                                <option value="1">1st Semester</option>
                                <option value="2">2nd Semester</option>
                            </select>
                        </div>
                        <div class="pb-1">
                            <span id="filter-hint" class="text-[12px] text-slate-400">Select program, year and
                                semester</span>
                            <span id="filter-count"
                                class="hidden rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600"></span>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="overflow-x-auto">
                        <table class="data-table" id="subjects-table">
                            <thead>
                                <tr>
                                    <th>Course Code</th>
                                    <th>Descriptive Title</th>
                                    <th>Units</th>
                                    <th>Lec Hrs</th>
                                    <th>Lab Hrs</th>
                                    <th>Total Hrs</th>
                                    <th>Program</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="subjects-tbody">
                                <tr id="subjects-empty-row">
                                    <td colspan="8" class="text-center text-slate-400 py-10">
                                        Select a program, year level and semester to view subjects.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ADD SUBJECT MODAL --}}
    <div class="modal-overlay" id="modal-add-subject">
        <div class="modal-box w-[520px]">
            <form action="{{ route('subject.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <div class="modal-title">Add New Subject</div>
                    <button class="modal-close" type="button" onclick="closeModal('modal-add-subject')">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Course Code</label>
                        <input class="field-input" name="subj_code" value="{{ old('subj_code') }}"
                            placeholder="e.g. CC 111" required>
                        @error('subj_code')
                            <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="field-label">College / Department</label>
                        <select class="field-input" name="subj_dept_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->college_id ?? $dept->dept_id }}" @selected(old('subj_dept_id') == ($dept->college_id ?? $dept->dept_id))>
                                    {{ $dept->college_name ?? $dept->dept_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('subj_dept_id')
                            <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Program</label>
                    <select class="field-input" name="subj_prog_id" id="add-subj-prog" required>
                        <option value="">-- Select Program --</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->dept_id ?? $prog->prog_id }}" @selected(old('subj_prog_id') == ($prog->dept_id ?? $prog->prog_id))>
                                {{ $prog->dept_name ?? $prog->prog_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('subj_prog_id')
                        <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="field-label">Descriptive Title</label>
                    <input class="field-input" name="subj_name" value="{{ old('subj_name') }}"
                        placeholder="e.g. Introduction to Computing" required>
                    @error('subj_name')
                        <div class="text-red-600 text-[12px] mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Year Level</label>
                        <select class="field-input" name="subj_year_level">
                            <option value="">— Optional —</option>
                            @foreach ($yearLevels as $val => $label)
                                <option value="{{ $val }}" @selected(old('subj_year_level') == $val)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Semester</label>
                        <select class="field-input" name="subj_semester">
                            <option value="">— Optional —</option>
                            <option value="1" @selected(old('subj_semester') == 1)>1st Semester</option>
                            <option value="2" @selected(old('subj_semester') == 2)>2nd Semester</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-3 mb-3">
                    <div>
                        <label class="field-label">Units</label>
                        <input class="field-input" id="add-subj-units" name="subj_units" type="number" min="0" step="0.5" value="{{ old('subj_units', 0) }}" required>
                    </div>
                    <div>
                        <label class="field-label">Lecture Hrs</label>
                        <input class="field-input" id="add-subj-lec" name="subj_lecture_hours" type="number"
                            min="0" max="12" step="0.5" value="{{ old('subj_lecture_hours', 0) }}"
                            oninput="updateSubjectTotalHours('add')" required>
                    </div>
                    <div>
                        <label class="field-label">Lab Hrs</label>
                        <input class="field-input" id="add-subj-lab" name="subj_lab_hours" type="number"
                            min="0" max="12" step="0.5" value="{{ old('subj_lab_hours', 0) }}"
                            oninput="updateSubjectTotalHours('add')" required>
                    </div>
                    <div>
                        <label class="field-label">Total Hrs</label>
                        <input class="field-input bg-slate-50" id="add-subj-total" type="number" value="0" readonly>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button"
                        onclick="closeModal('modal-add-subject')">Cancel</button>
                    <button class="btn btn-primary" type="submit">Add Course</button>
                </div>
            </form>
        </div>
    </div>

    {{-- EDIT SUBJECT MODAL --}}
    <div class="modal-overlay" id="modal-edit-subject">
        <div class="modal-box w-[520px]">
            <form id="edit-subj-form" action="" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-header">
                    <div class="modal-title">Edit Subject</div>
                    <button class="modal-close" type="button" onclick="closeModal('modal-edit-subject')">✕</button>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Course Code</label>
                        <input class="field-input" id="edit-subj-code" name="subj_code" required>
                    </div>
                    <div>
                        <label class="field-label">College / Department</label>
                        <select class="field-input" id="edit-subj-dept" name="subj_dept_id" required>
                            <option value="">-- Select --</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->college_id ?? $dept->dept_id }}">
                                    {{ $dept->college_name ?? $dept->dept_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="field-label">Program</label>
                    <select class="field-input" id="edit-subj-prog" name="subj_prog_id" required>
                        <option value="">-- Select Program --</option>
                        @foreach ($programs as $prog)
                            <option value="{{ $prog->dept_id ?? $prog->prog_id }}">
                                {{ $prog->dept_name ?? $prog->prog_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="field-label">Descriptive Title</label>
                    <input class="field-input" id="edit-subj-name" name="subj_name" required>
                </div>

                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div>
                        <label class="field-label">Year Level</label>
                        <select class="field-input" id="edit-subj-year" name="subj_year_level">
                            <option value="">— Optional —</option>
                            @foreach ($yearLevels as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="field-label">Semester</label>
                        <select class="field-input" id="edit-subj-sem" name="subj_semester">
                            <option value="">— Optional —</option>
                            <option value="1">1st Semester</option>
                            <option value="2">2nd Semester</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-4 gap-3 mb-3">
                    <div>
                        <label class="field-label">Units</label>
                        <input class="field-input" id="edit-subj-units" name="subj_units" type="number" min="0" step="0.5" required>
                    </div>
                    <div>
                        <label class="field-label">Lecture Hrs</label>
                        <input class="field-input" id="edit-subj-lec" name="subj_lecture_hours" type="number"
                            min="0" max="12" step="0.5" oninput="updateSubjectTotalHours('edit')"
                            required>
                    </div>
                    <div>
                        <label class="field-label">Lab Hrs</label>
                        <input class="field-input" id="edit-subj-lab" name="subj_lab_hours" type="number"
                            min="0" max="12" step="0.5" oninput="updateSubjectTotalHours('edit')"
                            required>
                    </div>
                    <div>
                        <label class="field-label">Total Hrs</label>
                        <input class="field-input bg-slate-50" id="edit-subj-total" type="number" value="0" readonly>
                    </div>
                </div>

                <div
                    class="bg-amber-100 border border-amber-300 rounded-lg px-3.5 py-2.5 text-[12px] text-amber-800 mb-1">
                    Editing a subject may affect existing schedule assignments.
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button"
                        onclick="closeModal('modal-edit-subject')">Cancel</button>
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div class="toast" id="toast"><span id="toast-msg"></span></div>

    <script>
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;
        const DESTROY_URL = "{{ url('/subject') }}"; // + /{id}
        const UPDATE_URL = "{{ url('/subject') }}";

        const ALL_SUBJECTS = @json($subjectsForJs ?? []);

        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }
        document.querySelectorAll('.modal-overlay').forEach(m => {
            m.addEventListener('click', e => {
                if (e.target === m) m.classList.remove('open');
            });
        });

        function showToast(msg) {
            const t = document.getElementById('toast');
            document.getElementById('toast-msg').textContent = msg;
            t.classList.add('show');
            setTimeout(() => t.classList.remove('show'), 3000);
        }

        function updateSubjectTotalHours(prefix) {
            const lecture = Number(document.getElementById(`${prefix}-subj-lec`).value) || 0;
            const lab = Number(document.getElementById(`${prefix}-subj-lab`).value) || 0;
            document.getElementById(`${prefix}-subj-total`).value = lecture + lab;
        }

        updateSubjectTotalHours('add');

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
            return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        }

        function applyFilters() {
            const prog = document.getElementById('filter-program').value;
            const year = document.getElementById('filter-year').value;
            const sem = document.getElementById('filter-semester').value;
            const tbody = document.getElementById('subjects-tbody');
            const hint = document.getElementById('filter-hint');
            const count = document.getElementById('filter-count');

            if (!prog || !year || !sem) {
                tbody.innerHTML = `
      <tr id="subjects-empty-row">
        <td colspan="8" class="text-center text-slate-400 py-10">
          Select a program, year level and semester to view subjects.
        </td>
      </tr>`;
                hint.classList.remove('hidden');
                hint.textContent = 'Select program, year and semester';
                count.classList.add('hidden');
                return;
            }

            const list = ALL_SUBJECTS.filter(s =>
                String(s.prog_id) === String(prog) &&
                String(s.year_level) === String(year) &&
                String(s.semester) === String(sem)
            );

            const progLabel = document.getElementById('filter-program').selectedOptions[0]?.text?.trim() || prog;
            const yearLabel = document.getElementById('filter-year').selectedOptions[0]?.text || year;
            const semLabel = document.getElementById('filter-semester').selectedOptions[0]?.text || sem;

            if (list.length === 0) {
                tbody.innerHTML = `
      <tr>
        <td colspan="8" class="text-center text-slate-400 py-10">
          No subjects for ${escapeHtml(progLabel)} · ${escapeHtml(yearLabel)} · ${escapeHtml(semLabel)}.
        </td>
      </tr>`;
                hint.classList.add('hidden');
                count.classList.remove('hidden');
                count.textContent = '0 subjects';
                return;
            }

            hint.classList.remove('hidden');
            hint.textContent = `${progLabel} · ${yearLabel} · ${semLabel}`;
            count.classList.remove('hidden');
            count.textContent = `${list.length} subject${list.length === 1 ? '' : 's'}`;

            tbody.innerHTML = list.map(s => `
    <tr>
      <td><span class="font-mono font-bold">${escapeHtml(s.code)}</span></td>
      <td class="font-semibold">${escapeHtml(s.name)}</td>
      <td>${s.units}</td>
      <td>${s.lec}</td>
      <td>${s.lab}</td>
      <td>${s.total}</td>
      <td>${escapeHtml(s.prog_name)}</td>
      <td>
        <div class="flex gap-1.5">
          <button type="button" class="btn btn-secondary text-[11px] px-3 py-1.5"
            onclick="openEditSubject(this)"
            data-id="${s.id}"
            data-code="${escapeAttr(s.code)}"
            data-name="${escapeAttr(s.name)}"
            data-lec="${s.lec}"
            data-lab="${s.lab}"
            data-units="${s.units}"
            data-total="${s.total}"
            data-dept="${s.dept_id || ''}"
            data-prog="${s.prog_id || ''}"
            data-year="${s.year_level ?? ''}"
            data-sem="${s.semester ?? ''}"
            data-action="${UPDATE_URL}/${s.id}"
          >Edit</button>
          <form action="${DESTROY_URL}/${s.id}" method="POST" class="inline"
            onsubmit="return confirm('Delete: ${escapeAttr(s.code)}?\\nThis will deactivate the subject.');">
            <input type="hidden" name="_token" value="${CSRF_TOKEN}">
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="btn btn-danger text-[11px] px-3 py-1.5">Delete</button>
          </form>
        </div>
      </td>
    </tr>
  `).join('');
        }

        function openEditSubject(btn) {
            const form = document.getElementById('edit-subj-form');
            form.action = btn.dataset.action;

            document.getElementById('edit-subj-code').value = btn.dataset.code || '';
            document.getElementById('edit-subj-name').value = btn.dataset.name || '';
            document.getElementById('edit-subj-lec').value = btn.dataset.lec || 0;
            document.getElementById('edit-subj-lab').value = btn.dataset.lab || 0;
            document.getElementById('edit-subj-dept').value = btn.dataset.dept || '';
            document.getElementById('edit-subj-prog').value = btn.dataset.prog || '';
            document.getElementById('edit-subj-year').value = btn.dataset.year || '';
            document.getElementById('edit-subj-sem').value = btn.dataset.sem || '';
            document.getElementById('edit-subj-units').value = btn.dataset.units || 0;
            updateSubjectTotalHours('edit');

            openModal('modal-edit-subject');
        }

        // Pre-select program on Add modal from page filter
        document.querySelector('[onclick="openModal(\'modal-add-subject\')"]')?.addEventListener('click', () => {
            const prog = document.getElementById('filter-program').value;
            const year = document.getElementById('filter-year').value;
            const sem = document.getElementById('filter-semester').value;
            if (prog) document.getElementById('add-subj-prog').value = prog;
            // year/sem selects in add form by name
            const yearSel = document.querySelector('#modal-add-subject select[name="subj_year_level"]');
            const semSel = document.querySelector('#modal-add-subject select[name="subj_semester"]');
            if (yearSel && year) yearSel.value = year;
            if (semSel && sem) semSel.value = sem;
        });
    </script>
</body>

</html>
