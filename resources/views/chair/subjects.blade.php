<!DOCTYPE html>
<html lang="en">

<head>
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
                'title' => 'Course Management',
                'badgeText' => ($programName ?? 'BSIS') . ' Department',
            ])

            <div id="page-subjects" class="page-content min-h-0 flex-1 overflow-y-auto">
                <div class="mb-5">
                    <div class="text-[20px] font-extrabold text-slate-900">Course Management</div>
                    <div class="mt-1 text-[13px] text-slate-500">
                        {{ $programName ?? 'BSIS' }} Department — filter by year &amp; semester
                    </div>
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
                                </tr>
                            </thead>
                            <tbody id="subjects-table">
                                <tr id="subjects-empty-row">
                                    <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-400">
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

    <script>
        const ALL_SUBJECTS = @json($subjectsForJs ?? []);

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
                        <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-400">
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
                        <td colspan="5" class="px-3 py-10 text-center text-sm text-slate-400">
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
                </tr>
            `).join('') + `
                <tr class="bg-slate-50/80">
                    <td colspan="2" class="px-3 py-2.5 text-[12px] font-bold text-slate-600">Total</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalUnits}</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalLec}</td>
                    <td class="px-3 py-2.5 text-[12px] font-bold text-slate-700">${totalLab}</td>
                </tr>`;
        }
    </script>
</body>

</html>
