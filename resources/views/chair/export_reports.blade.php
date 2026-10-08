<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SKEDYUL — Export Reports</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-slate-50 font-sans text-slate-900 antialiased">
    <div class="flex h-screen overflow-hidden">
        @include('partials.chair_sidebar')

        <main class="flex min-w-0 flex-1 flex-col overflow-hidden">
            @include('partials.chair_header', [
                'title' => 'Export Reports',
                'badgeText' => 'Department Chair',
            ])

            <div class="flex-1 overflow-y-auto p-6 lg:p-8">
                <div class="mx-auto max-w-7xl">
                    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="mb-1 text-sm font-semibold uppercase tracking-wide text-blue-600">
                                Schedule documents
                            </p>
                            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">
                                Export Reports
                            </h1>
                            <p class="mt-1 max-w-2xl text-sm text-slate-500">
                                Generate an individual Program by Teacher workbook from your department’s active
                                schedule.
                            </p>
                        </div>

                        @if ($semester)
                            <div class="rounded-xl border border-blue-100 bg-blue-50 px-4 py-3">
                                <div class="text-xs font-semibold uppercase tracking-wide text-blue-600">
                                    Active semester
                                </div>
                                <div class="mt-1 text-sm font-bold text-slate-800">
                                    {{ $semester->sem_name }}
                                    @if ($semester->academicYear?->ay_academic_year)
                                        · AY {{ $semester->academicYear->ay_academic_year }}
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                            role="alert">
                            <ul class="list-inside list-disc space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Faculty in
                                department</div>
                            <div class="mt-2 text-2xl font-extrabold text-slate-900">{{ $facultyMembers->count() }}
                            </div>
                            <div class="mt-1 text-xs text-slate-500">Available for individual PBT export</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Workbook sheets
                            </div>
                            <div class="mt-2 text-2xl font-extrabold text-slate-900">2</div>
                            <div class="mt-1 text-xs text-slate-500">Day and Evening schedules</div>
                        </div>

                        <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                            <div class="text-xs font-semibold uppercase tracking-wide text-slate-400">Download format
                            </div>
                            <div class="mt-2 text-2xl font-extrabold text-slate-900">Excel</div>
                            <div class="mt-1 text-xs text-slate-500">Uses the provided PBT template</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-5 xl:grid-cols-5">
                        <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm xl:col-span-3">
                            <div class="mb-6">
                                <div
                                    class="mb-2 inline-flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
                                    <span class="text-sm font-extrabold">PBT</span>
                                </div>
                                <h2 class="text-lg font-bold text-slate-900">Faculty Program by Teacher</h2>
                                <p class="mt-1 max-w-xl text-sm leading-6 text-slate-500">
                                    Select a faculty member to generate a workbook containing their course summary
                                    and scheduled classes.
                                </p>
                            </div>

                            @if (!$semester)
                                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                    No active semester is set. Set an active semester before exporting a PBT workbook.
                                </div>
                            @elseif ($facultyMembers->isEmpty())
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600">
                                    No faculty members are assigned to your department.
                                </div>
                            @else
                                <form method="GET" action="{{ route('chair.export_reports.pbt') }}" class="space-y-5">
                                    <div>
                                        <label for="faculty_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                            Faculty member
                                        </label>
                                        <select id="faculty_id" name="faculty_id" required
                                            class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-100">
                                            <option value="">Choose a faculty member</option>
                                            @foreach ($facultyMembers as $faculty)
                                                @php
                                                    $facultyName =
                                                        $faculty->user->usr_name ??
                                                        trim(
                                                            implode(
                                                                ' ',
                                                                array_filter([
                                                                    $faculty->fac_first_name,
                                                                    $faculty->fac_middle_name,
                                                                    $faculty->fac_last_name,
                                                                    $faculty->fac_suffix,
                                                                ]),
                                                            ),
                                                        );
                                                @endphp
                                                <option value="{{ $faculty->fac_id }}" @selected(old('faculty_id') === $faculty->fac_id)>
                                                    {{ $facultyName ?: 'Unnamed Faculty' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <p class="mt-2 text-xs text-slate-500">
                                            The selected faculty member appears in the PBT name field. The logged-in
                                            Chair remains in the Prepared by signature.
                                        </p>
                                    </div>

                                    <div
                                        class="flex flex-wrap items-center justify-between gap-4 border-t border-slate-100 pt-5">
                                        <div>
                                            <div class="text-sm font-semibold text-slate-700">Ready to export?</div>
                                            <div class="mt-1 text-xs text-slate-500">
                                                The workbook uses the current active semester.
                                            </div>
                                        </div>

                                        <button type="submit"
                                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                            Download PBT Excel
                                        </button>
                                    </div>
                                </form>
                            @endif
                        </section>

                        <aside
                            class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
                            <div class="bg-gradient-to-br from-blue-700 to-indigo-900 p-6 text-white">
                                <div class="text-xs font-semibold uppercase tracking-[0.16em] text-blue-100">
                                    Workbook preview
                                </div>
                                <h2 class="mt-2 text-lg font-bold">Program by Teacher</h2>
                                <p class="mt-1 text-sm leading-6 text-blue-100">
                                    Your download keeps the layout and branding of the supplied form.
                                </p>

                                <div class="mt-5 rounded-xl bg-white p-4 text-slate-800 shadow-lg">
                                    <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-3">
                                        <div>
                                            <div class="text-xs font-bold uppercase tracking-wide text-blue-700">SKEDYUL
                                            </div>
                                            <div class="mt-1 text-sm font-bold">Faculty Schedule</div>
                                        </div>
                                        <div
                                            class="rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            {{ $semester?->sem_name ?? 'Semester' }}
                                        </div>
                                    </div>

                                    <div class="mt-3 flex gap-2 text-xs font-semibold">
                                        <span class="rounded-md bg-blue-600 px-3 py-1.5 text-white">Day</span>
                                        <span class="rounded-md bg-slate-100 px-3 py-1.5 text-slate-500">Evening</span>
                                    </div>

                                    <div class="mt-3 grid grid-cols-5 gap-1.5" aria-hidden="true">
                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-blue-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>

                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-emerald-100"></span>
                                        <span class="h-7 rounded bg-blue-100"></span>
                                        <span class="h-7 rounded bg-amber-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>

                                        <span class="h-7 rounded bg-blue-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-slate-100"></span>
                                        <span class="h-7 rounded bg-amber-100"></span>
                                        <span class="h-7 rounded bg-emerald-100"></span>
                                    </div>

                                    <div
                                        class="mt-3 flex items-center justify-between border-t border-slate-100 pt-3 text-xs">
                                        <span class="font-semibold text-slate-600">Course summary and signatures</span>
                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 font-semibold text-emerald-700">Excel
                                            workbook</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4 p-6">
                                <div>
                                    <h3 class="text-sm font-bold text-slate-800">Included in the workbook</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Faculty details, course and section summary, weekly timetable, workload totals,
                                        and signature areas.
                                    </p>
                                </div>

                                <div class="flex items-start gap-3 border-t border-slate-100 pt-4">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-700">1</span>
                                    <p class="text-sm text-slate-600">Choose a faculty member from your department.</p>
                                </div>

                                <div class="flex items-start gap-3">
                                    <span
                                        class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-blue-50 text-xs font-bold text-blue-700">2</span>
                                    <p class="text-sm text-slate-600">Download and review the generated PBT workbook.
                                    </p>
                                </div>
                            </div>
                        </aside>
                    </div>

                    <div class="mt-5 rounded-xl border border-slate-200 bg-white px-5 py-4 text-sm text-slate-600">
                        <span class="font-semibold text-slate-800">Note:</span>
                        PDF export is not available yet. Download the Excel workbook first, then use Excel’s
                        print or export-to-PDF option if you need a PDF copy.
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>

</html>
