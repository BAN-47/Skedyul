<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <style>
        @page {
            margin: 24px 28px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #172033;
            font-size: 9px;
        }

        h1 {
            margin: 0;
            font-size: 16px;
            text-align: center;
        }

        .subtitle {
            margin: 4px 0 16px;
            text-align: center;
            color: #4b5563;
            font-size: 10px;
        }

        .dept {
            margin-bottom: 8px;
            font-size: 10px;
            font-weight: bold;
        }

        .section {
            margin: 12px 0 5px;
            font-size: 11px;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 1px solid #9ca3af;
            padding: 5px 4px;
            vertical-align: middle;
            word-wrap: break-word;
        }

        th {
            background: #dceaf5;
            text-align: left;
            font-size: 8px;
        }

        tr {
            page-break-inside: avoid;
        }

        .center {
            text-align: center;
        }

        .signatures {
            margin-top: 30px;
        }

        .signatures td {
            height: 44px;
            vertical-align: bottom;
            text-align: center;
            border: 0;
            border-top: 1px solid #6b7280;
        }

        .sig-name {
            font-weight: bold;
        }

        .sig-title {
            color: #4b5563;
            margin-top: 3px;
        }

        .empty {
            padding: 18px;
            text-align: center;
            color: #6b7280;
        }
    </style>
</head>

<body>
    @php
        $sections = $sections ?? collect();
        $groupedRows = $groupedRows ?? collect();
        $activeSemester = $activeSemester ?? null;
        $chairDepartment = $chairDepartment ?? null;
        $signatories = $signatories ?? [];
        $filters = $filters ?? ['shift' => 'day', 'section' => null];
        $shiftLabel = ($filters['shift'] ?? 'day') === 'night' ? 'Evening Program' : 'Day Program';
        $selectedSection = $sections->firstWhere('sec_id', $filters['section'] ?? null);
    @endphp
    <h1>CLASS PROGRAM FOR MIS</h1>
    <div class="subtitle">
        {{ $chairDepartment->dept_name ?? 'Department' }}
        @if ($activeSemester)
            · {{ $activeSemester->label }}
        @endif
        · {{ $shiftLabel }}
        @if ($selectedSection)
            · {{ $selectedSection->sec_name }}
        @else
            · All Sections
        @endif
    </div>
    @if ($error ?? null)
        <p>{{ $error }}</p>
    @endif
    @forelse($groupedRows as $sectionName => $rows)
        <div class="section">{{ $sectionName }}</div>
        <table>
            <thead>
                <tr>
                    <th style="width:7%">MIS Code</th>
                    <th style="width:8%">Course No.</th>
                    <th style="width:22%">Descriptive Title</th>
                    <th style="width:13%">Time</th>
                    <th style="width:8%">Day</th>
                    <th style="width:5%" class="center">Lec</th>
                    <th style="width:5%" class="center">Lab</th>
                    <th style="width:7%" class="center">Total Hrs</th>
                    <th style="width:5%" class="center">Unit</th>
                    <th style="width:10%">Room</th>
                    <th style="width:10%">Instructor</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $s)
                    @php
                        $course = $s->course ?? $s->subject;
                        $code = $course->course_code ?? ($course->subj_code ?? '—');
                        $title = $course->course_name ?? ($course->subj_name ?? '—');
                        $lec = $course->course_lecture_hours ?? ($course->subj_lecture_hours ?? 0);
                        $lab = $course->course_lab_hours ?? ($course->subj_lab_hours ?? 0);
                        $hours = $course->course_total_hours ?? (float) $lec + (float) $lab;
                        $unit = (float) ($course->course_units ?? 0);
                        try {
                            $time =
                                \Illuminate\Support\Carbon::parse($s->sch_start_time)->format('g:i A') .
                                ' – ' .
                                \Illuminate\Support\Carbon::parse($s->sch_end_time)->format('g:i A');
                        } catch (\Throwable $e) {
                            $time = trim(($s->sch_start_time ?? '') . ' – ' . ($s->sch_end_time ?? ''));
                        }
                        $inst = $s->faculty
                            ? trim(($s->faculty->fac_first_name ?? '') . ' ' . ($s->faculty->fac_last_name ?? ''))
                            : '—';
                    @endphp
                    <tr>
                        <td>{{ $s->sch_mis_code ?: '—' }}</td>
                        <td>{{ $code }}</td>
                        <td>{{ $title }}</td>
                        <td>{{ $time }}</td>
                        <td>{{ $s->sch_day ?? '—' }}</td>
                        <td class="center">{{ $lec }}</td>
                        <td class="center">{{ $lab }}</td>
                        <td class="center">{{ number_format((float) $hours, 2) }}</td>
                        <td class="center">{{ $unit }}</td>
                        <td>{{ $s->room->room_name ?? '—' }}</td>
                        <td>{{ $inst }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @empty
        <div class="empty">No schedules found for this selection.</div>
    @endforelse
    <table class="signatures">
        <tr>
            <td>
                <div class="sig-name">{{ $signatories['chair_name'] ?? '—' }}</div>
                <div class="sig-title">{{ $signatories['chair_title'] ?? 'Department Chair' }}</div>
            </td>
            <td>
                <div class="sig-name">{{ $signatories['dean_name'] ?? '—' }}</div>
                <div class="sig-title">{{ $signatories['dean_title'] ?? 'Dean' }}</div>
            </td>
            <td>
                <div class="sig-name">{{ $signatories['campus_director'] ?: '—' }}</div>
                <div class="sig-title">Campus Director</div>
            </td>
        </tr>
    </table>
</body>

</html>
