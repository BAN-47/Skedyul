<!DOCTYPE html>
<html lang="en">

<head>
  @include('partials.favicon')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Courses Department</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        #page-subjects {
            padding-bottom: 32px;
            scrollbar-gutter: stable;
        }
    </style>
</head>

<body class="overflow-hidden bg-slate-50 font-sans text-slate-900 antialiased">

    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'Courses',
                'badgeText' => ($programName ?? 'BSIS') . ' Department',
            ])

            <div id="page-subjects" class="page-content min-h-0 flex-1 overflow-y-auto">
                @if(session('success'))<div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">{{ session('success') }}</div>@endif
                @if(session('error'))<div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ session('error') }}</div>@endif
                @if($errors->any())<div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $errors->first() }}</div>@endif
                <div class="mb-5 font-bold">
                    <div class="mt-1 text-[13px] text-slate-500">
                        {{ $programName ?? 'BSIS' }} Department — filter by year &amp; semester
                    </div>
                </div>

                <div class="mb-4 flex justify-end">
                    <button type="button" onclick="openChairCourseModal()" class="btn btn-primary">+ Add Course</button>
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
                                        Lec Units</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Lab Units</th>
                                    <th
                                        class="border-b-2 border-slate-200 px-3 py-2 text-[11px] font-bold uppercase tracking-[0.6px] text-slate-400">
                                        Total (Lec + Lab)</th>
                                </tr>
                            </thead>
                            <tbody id="subjects-table">
                                <tr id="subjects-empty-row">
                                    <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">
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

    <div id="chair-course-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 p-4" role="dialog" aria-modal="true" aria-labelledby="chair-course-modal-title" onclick="if(event.target===this)closeChairCourseModal()">
        <form method="POST" action="{{ route('chair.subject.store') }}" class="max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-5 shadow-xl" onclick="event.stopPropagation()">
            @csrf
            <div class="flex items-start justify-between gap-3">
                <div><h2 id="chair-course-modal-title" class="text-lg font-bold">Add Course</h2><p class="mt-1 text-sm text-slate-500">This course will be added to {{ $programName ?? 'your department' }}.</p></div>
                <button type="button" class="btn btn-secondary" onclick="closeChairCourseModal()">Close</button>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <label class="text-sm font-medium">Course code<input name="subj_code" value="{{ old('subj_code') }}" maxlength="50" required class="field-input mt-1" placeholder="e.g. CC 111"></label>
                <label class="text-sm font-medium">Course title<input name="subj_name" value="{{ old('subj_name') }}" maxlength="255" required class="field-input mt-1" placeholder="Course descriptive title"></label>
                <label class="text-sm font-medium">Year level<select name="subj_year_level" class="field-input mt-1"><option value="">Select year</option>@foreach($yearLevels as $value => $label)<option value="{{ $value }}" @selected(old('subj_year_level') == $value)>{{ $label }}</option>@endforeach</select></label>
                <label class="text-sm font-medium">Semester<select name="subj_semester" class="field-input mt-1"><option value="">Select semester</option><option value="1" @selected(old('subj_semester') == 1)>1st Semester</option><option value="2" @selected(old('subj_semester') == 2)>2nd Semester</option></select></label>
                <label class="text-sm font-medium">Course units<input name="subj_units" value="{{ old('subj_units', 0) }}" type="number" min="0" step="0.5" required class="field-input mt-1"></label>
                <label class="text-sm font-medium">Lecture units<input id="chair-course-lec" name="subj_lecture_units" value="{{ old('subj_lecture_units', 0) }}" type="number" min="0" max="12" step="0.5" required oninput="updateChairCourseTotals()" class="field-input mt-1"><span class="mt-1 block text-xs text-slate-400">1 lecture unit = 1 teaching hour</span></label>
                <label class="text-sm font-medium">Lab units<input id="chair-course-lab" name="subj_lab_units" value="{{ old('subj_lab_units', 0) }}" type="number" min="0" max="4" step="0.5" required oninput="updateChairCourseTotals()" class="field-input mt-1"><span class="mt-1 block text-xs text-slate-400">Workload counts 1 lab unit as 3 teaching hours</span></label>
                <label class="text-sm font-medium">Total (lecture + lab units)<input id="chair-course-total" type="number" value="0" readonly class="field-input mt-1 bg-slate-50"></label>
            </div>
            <div class="mt-5 flex justify-end gap-2"><button type="button" onclick="closeChairCourseModal()" class="btn btn-secondary">Cancel</button><button type="submit" class="btn btn-primary">Save Course</button></div>
        </form>
    </div>

    <script>
        const ALL_SUBJECTS = @json($subjectsForJs ?? []);

        function updateChairCourseTotals() {
            const lecture = Number(document.getElementById('chair-course-lec')?.value) || 0;
            const lab = Number(document.getElementById('chair-course-lab')?.value) || 0;
            document.getElementById('chair-course-total').value = lecture + lab;
        }

        function openChairCourseModal() {
            const modal = document.getElementById('chair-course-modal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            updateChairCourseTotals();
        }

        function closeChairCourseModal() {
            const modal = document.getElementById('chair-course-modal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        updateChairCourseTotals();
        @if($errors->any()) openChairCourseModal(); @endif

        function escapeHtml(str) {
            if (str == null) return '';
            return String(str)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        function applySubjectFilters() {
            const year = document.getElementById('filter-year').value;
            const sem = document.getElementById('filter-semester').value;
            const tbody = document.getElementById('subjects-table');
            const hint = document.getElementById('filter-hint');
            const count = document.getElementById('filter-count');

            if (!year || !sem) {
                tbody.innerHTML = `
                    <tr id="subjects-empty-row">
                        <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">
                            Select a year level and semester above to load subjects.
                        </td>
                    </tr>`;
                hint.classList.remove('hidden');
                hint.textContent = 'Select year and semester to view subjects';
                count.classList.add('hidden');
                return;
            }

            const list = ALL_SUBJECTS.filter(s =>
                String(s.year_level) === String(year) && String(s.semester) === String(sem)
            );

            const yearLabel = document.getElementById('filter-year').selectedOptions[0]?.text || year;
            const semLabel = document.getElementById('filter-semester').selectedOptions[0]?.text || sem;

            if (list.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="px-3 py-10 text-center text-sm text-slate-400">
                            No subjects for this year and semester.
                        </td>
                    </tr>`;
                hint.classList.add('hidden');
                count.classList.remove('hidden');
                count.textContent = '0 subjects';
                return;
            }

            const totalUnits = list.reduce((a, s) => a + Number(s.units || 0), 0);
            const totalLec = list.reduce((a, s) => a + Number(s.lec || 0), 0);
            const totalLab = list.reduce((a, s) => a + Number(s.lab || 0), 0);
            const totalHours = list.reduce((a, s) => a + Number(s.total || 0), 0);

            hint.classList.remove('hidden');
            hint.textContent = `${yearLabel} · ${semLabel}`;
            count.classList.remove('hidden');
            count.textContent = `${list.length} subject${list.length === 1 ? '' : 's'}`;

            tbody.innerHTML = list.map(s => `
                <tr class="hover:bg-slate-50">
                    <td class="border-b border-slate-100 px-3 py-3 text-sm font-mono font-bold text-slate-900">${escapeHtml(s.code)}</td>
                    <td class="border-b border-slate-100 px-3 py-3 text-sm font-bold text-slate-900">${escapeHtml(s.name)}</td>
                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.units}</td>
                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.lec}</td>
                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.lab}</td>
                    <td class="border-b border-slate-100 px-3 py-3 text-sm text-slate-600">${s.total}</td>
                </tr>
            `).join('') + `
                <tr class="bg-slate-50/80">
                    <td colspan="2" class="px-3 py-2.5 text-[12px] font-bold text-slate-600">Total</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalUnits}</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalLec}</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalLab}</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalHours}</td>
                </tr>`;
        }
    </script>
</body>

</html>
