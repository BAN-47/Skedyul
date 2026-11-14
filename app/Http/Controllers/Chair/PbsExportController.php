<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Semester;
use App\Models\SystemSetting;
use App\Services\CampusDirectorSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PbsExportController extends Controller
{
    private const COLORS = ['DCE8F5', 'D9EAD3', 'FCE4D6', 'E4DFEC', 'FFF2CC', 'DDEBF7', 'E2F0D9', 'F4CCCC'];

    public function download(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'section' => ['required', 'uuid', 'exists:section,sec_id'],
            'semester' => ['required', 'uuid', 'exists:semester,sem_id'],
        ]);

        $chair = Dept_Chair::where('dc_usr_id', Auth::user()->usr_id)->first();
        abort_unless($chair, 403, 'Your account is not assigned to a department.');

        // PBS allows the Chair to choose departments within their own college.
        $allowedDepartmentIds = $chair->dc_college_id
            ? \App\Models\Departments::where('dept_college_id', $chair->dc_college_id)->pluck('dept_id')
            : collect([$chair->dc_dept_id])->filter()->values();

        $section = Section::with('program.department')
            ->whereIn('sec_dept_id', $allowedDepartmentIds)
            ->where('sec_id', $validated['section'])
            ->firstOrFail();

        $semester = Semester::with('academicYear')->findOrFail($validated['semester']);
        $schedules = Schedule::query()
            ->where('sch_sec_id', $section->sec_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->with(['subject', 'faculty', 'room'])
            ->orderByRaw("CASE UPPER(sch_day)
                WHEN 'MONDAY' THEN 1 WHEN 'MON' THEN 1
                WHEN 'TUESDAY' THEN 2 WHEN 'TUE' THEN 2
                WHEN 'WEDNESDAY' THEN 3 WHEN 'WED' THEN 3
                WHEN 'THURSDAY' THEN 4 WHEN 'THU' THEN 4
                WHEN 'FRIDAY' THEN 5 WHEN 'FRI' THEN 5
                WHEN 'SATURDAY' THEN 6 WHEN 'SAT' THEN 6
                WHEN 'SUNDAY' THEN 7 WHEN 'SUN' THEN 7
                ELSE 8 END")
            ->orderBy('sch_start_time')
            ->get();

        $college = $section->program?->department;
        $collegeDean = $college
            ? Dean::where('dean_college_id', $college->college_id)->with('user')->first()
            : null;
        $casCollege = College::whereRaw('UPPER(TRIM(college_code)) = ?', ['CAS'])->first();
        $casDean = $casCollege
            ? Dean::where('dean_college_id', $casCollege->college_id)->with('user')->first()
            : null;
        $chairName = trim(implode(' ', array_filter([
            $chair->dc_first_name,
            $chair->dc_middle_name,
            $chair->dc_last_name,
            $chair->dc_suffix,
        ])));
        $collegeDeanName = $this->personName($collegeDean, 'dean');
        $casDeanName = $this->personName($casDean, 'dean');
        $campusDirectorName = trim((string) SystemSetting::get('campus_director_name', ''));

        $isEvening = (bool) $section->sec_is_evening;
        $templateName = $isEvening ? 'PBS_Evening_Template.xlsx' : 'PBS_Day_Template.xlsx';
        $templatePath = storage_path('app/templates/' . $templateName);
        abort_unless(is_file($templatePath), 500, 'The PBS export template is missing: ' . $templateName);

        $spreadsheet = IOFactory::load($templatePath);
        $sourceSheetName = $isEvening ? 'BSIS 1-1' : 'BSIS 1-A';
        $sheet = $spreadsheet->getSheetByName($sourceSheetName);
        abort_unless($sheet, 500, 'The PBS template is missing the expected sheet: ' . $sourceSheetName);

        // Keep the supplied workbook's original worksheet formatting, dimensions,
        // logos, merged cells, and print setup. Export only the selected template tab.
        for ($index = $spreadsheet->getSheetCount() - 1; $index >= 0; $index--) {
            if ($spreadsheet->getSheet($index) !== $sheet) {
                $spreadsheet->removeSheetByIndex($index);
            }
        }
        $spreadsheet->setActiveSheetIndex(0);
        $sheet->setTitle(mb_substr(trim((string) $section->sec_name) ?: 'PBS', 0, 31));

        // The source workbook styles the entire D column with a right border,
        // which Excel renders as a long line below the actual course table.
        // Clear only the column-level style; the table's individual cell borders remain.
        $sheet->getColumnDimension('D')->setXfIndex(0);
        $this->clearSampleSchedule($sheet, $isEvening);
        $this->addFormMetadata($sheet);

        $semesterName = trim((string) $semester->sem_name);
        $academicYear = $semester->academicYear?->ay_academic_year
            ?? $semester->academicYear?->ay_year_label
            ?? '';
        $academicYear = str_replace('-', ' - ', $academicYear);
        $semesterLabel = trim($semesterName . ($academicYear !== '' ? ', AY ' . $academicYear : ''));

        $sheet->setCellValue('A5', 'PROGRAM BY SECTION');
        $sheet->setCellValue('A6', $isEvening ? 'Evening Program' : 'Day Program');
        $sheet->setCellValue('A7', $semesterLabel);
        $sheet->setCellValue('A9', 'Degree Year & Section: ' . $section->sec_name);
        $sheet->setCellValue('A10', 'Adviser: _____________________________________');
        $sheet->setCellValue('D15', (int) ($section->sec_no_of_student ?? 0));

        $this->clearCourseSummary($sheet);
        $this->writeCourseSummary($sheet, $schedules, (int) ($section->sec_no_of_student ?? 0));
        $this->writeSignatures(
            $sheet,
            $chairName,
            $collegeDeanName,
            $college?->college_code ?: $college?->college_name,
            $campusDirectorName,
            $this->shouldIncludeCasDean($section, $semester) ? $casDeanName : '',
            $isEvening
        );

        $this->drawSchedule($sheet, $schedules, $isEvening);

        $sheet->setSelectedCell('A1');
        $spreadsheet->setActiveSheetIndex(0);

        $safeSection = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string) $section->sec_name);
        $safeSemester = preg_replace('/[^A-Za-z0-9_-]+/', '-', $semesterName);
        $filename = sprintf('PBS-%s-%s.xlsx', $safeSection ?: 'Section', $safeSemester ?: 'Semester');

        return response()->streamDownload(
            static function () use ($spreadsheet): void {
                (new Xlsx($spreadsheet))->save('php://output');
            },
            $filename,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        );
    }

    private function clearSampleSchedule($sheet, bool $isEvening): void
    {
        $scheduleColumns = $isEvening
            ? [8, 9, 10, 11, 12, 14, 15] // Monday-Friday, Saturday, Sunday; M is the divider.
            : [8, 9, 10, 11, 12, 13]; // Monday-Saturday.
        $scheduleColumnLookup = array_fill_keys($scheduleColumns, true);

        foreach (array_keys($sheet->getMergeCells()) as $range) {
            [$start, $end] = Coordinate::rangeBoundaries($range);
            [$startColumn, $startRow] = $start;
            [$endColumn, $endRow] = $end;

            if ($startRow < 13 || $endRow > 42) {
                continue;
            }

            // The Day template's noon-break merge is part of the form, not sample data.
            if (!$isEvening && strtoupper($range) === 'H28:M30') {
                continue;
            }

            $touchesSchedule = false;
            for ($column = $startColumn; $column <= $endColumn; $column++) {
                if (isset($scheduleColumnLookup[$column])) {
                    $touchesSchedule = true;
                    break;
                }
            }

            if ($touchesSchedule) {
                $sheet->unmergeCells($range);
            }
        }

        foreach ($scheduleColumns as $column) {
            foreach (range(13, 42) as $row) {
                // Preserve the Day template's merged NOON BREAK band.
                if (!$isEvening && $column >= 8 && $column <= 13 && $row >= 28 && $row <= 30) {
                    continue;
                }

                $cell = Coordinate::stringFromColumnIndex($column) . $row;
                $sheet->setCellValue($cell, null);
                $sheet->getStyle($cell)->getFill()->setFillType(Fill::FILL_NONE);
            }
        }
    }

    private function addFormMetadata($sheet): void
    {
        $imagePath = storage_path('app/templates/PBS_Form_Metadata.png');
        abort_unless(is_file($imagePath), 500, 'The PBS form metadata image is missing.');

        // The supplied workbook stores this label as a floating text box.
        // PhpSpreadsheet drops text boxes while saving, so re-add it as a
        // transparent image at the same header anchor and size.
        $metadata = new Drawing();
        $metadata->setName('PBS Form Metadata');
        $metadata->setDescription('INS Form 5B-1, February 15, 2021, Revision 0');
        $metadata->setPath($imagePath);
        $metadata->setCoordinates('L2');
        $metadata->setOffsetX(71);
        $metadata->setOffsetY(3);
        $metadata->setHeight(73);
        $metadata->setWorksheet($sheet);
    }

    private function clearCourseSummary($sheet): void
    {
        foreach (range(16, 23) as $row) {
            $descriptionRange = 'B' . $row . ':C' . $row;
            // The template merges B:C only for the taller PATHFit row.
            if ($row !== 22 && isset($sheet->getMergeCells()[$descriptionRange])) {
                $sheet->unmergeCells($descriptionRange);
            }

            $sheet->setCellValue('A' . $row, null);
            $sheet->setCellValue('B' . $row, null);
            if ($row !== 22) {
                $sheet->setCellValue('C' . $row, null);
            }
            $sheet->setCellValue('D' . $row, null);
        }
        $sheet->setCellValue('D15', null);
    }

    private function writeCourseSummary($sheet, $schedules, int $students): void
    {
        // Follow the curriculum order used by the supplied PBS workbook.
        // PATHFit1 belongs on row 22, which is the template's taller row for
        // its long course title.
        $courseOrder = [
            'GECRPH' => 0,
            'GECMMW' => 1,
            'GEETEM' => 2,
            'CC111' => 3,
            'CC112' => 4,
            'CC112L' => 5,
            'PATHFIT1' => 6,
            'NSTP1' => 7,
        ];

        $courses = $schedules
            ->filter(fn($schedule) => $schedule->subject)
            ->unique(fn($schedule) => $schedule->subject->course_id)
            ->sortBy(function ($schedule) use ($courseOrder) {
                $code = $schedule->subject->course_code ?? $schedule->subject->subj_code ?? '';
                $normalizedCode = preg_replace('/[^A-Z0-9]/', '', mb_strtoupper((string) $code));

                return sprintf('%03d-%s', $courseOrder[$normalizedCode] ?? 999, $normalizedCode);
            })
            ->values();

        $sheet->setCellValue('D15', $students);
        foreach ($courses->take(8) as $index => $schedule) {
            $row = 16 + $index;
            $course = $schedule->subject;

            $descriptionRange = 'B' . $row . ':C' . $row;
            if ($row === 22) {
                if (!isset($sheet->getMergeCells()[$descriptionRange])) {
                    $sheet->mergeCells($descriptionRange);
                }
                $sheet->getStyle($descriptionRange)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true)
                    ->setShrinkToFit(false);
            } else {
                if (isset($sheet->getMergeCells()[$descriptionRange])) {
                    $sheet->unmergeCells($descriptionRange);
                }
                $sheet->getStyle('B' . $row)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(false)
                    ->setShrinkToFit(false);
            }

            $sheet->setCellValue('A' . $row, $course->course_code ?? $course->subj_code ?? '');
            $sheet->setCellValue('B' . $row, $course->course_name ?? $course->subj_name ?? '');
        }
    }

    private function writeSignatures($sheet, string $chairName, string $collegeDeanName, ?string $collegeLabel, string $directorName, string $casDeanName, bool $isEvening): void
    {
        $sheet->setCellValue('A48', $chairName !== '' ? mb_strtoupper($chairName) : 'DEPARTMENT CHAIR');
        $sheet->setCellValue('A49', 'Program Chairperson');

        $sheet->setCellValue('G48', $collegeDeanName !== '' ? mb_strtoupper($collegeDeanName) : '');
        $sheet->setCellValue('G49', 'Dean' . ($collegeLabel ? ', ' . $collegeLabel : ''));

        $directorCell = $isEvening ? 'J48' : 'K48';
        $directorRange = $isEvening ? 'J48:M48' : 'K48:M48';
        // Write to the anchor of the merged signature range. The shared helper
        // follows the K49 label, which is not the anchor in the evening template.
        $sheet->setCellValue($directorCell, mb_strtoupper(trim($directorName)));
        $sheet->getStyle($directorRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setIndent(1);
        $sheet->setCellValue('K49', 'Campus Director');
        $sheet->getStyle('K49:M49')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->setCellValue('G53', $casDeanName !== '' ? mb_strtoupper($casDeanName) : '');
        $sheet->setCellValue('G54', $casDeanName !== '' ? 'Dean, CAS' : '');
    }

    private function shouldIncludeCasDean(Section $section, Semester $semester): bool
    {
        $year = (int) $section->sec_year_level;
        $semesterNumber = $this->semesterNumber((string) $semester->sem_name);

        return ($year === 1 && in_array($semesterNumber, [1, 2], true))
            || ($year === 2 && $semesterNumber === 1);
    }

    private function semesterNumber(string $semesterName): ?int
    {
        $name = mb_strtolower($semesterName);
        if (preg_match('/(?:1st|first|semester\s*1)/', $name)) {
            return 1;
        }
        if (preg_match('/(?:2nd|second|semester\s*2)/', $name)) {
            return 2;
        }
        return null;
    }

    private function drawSchedule($sheet, $schedules, bool $isEvening): void
    {
        $columnByDay = $isEvening
            ? ['MON' => 8, 'TUE' => 9, 'WED' => 10, 'THU' => 11, 'FRI' => 12, 'SAT' => 14, 'SUN' => 15]
            : ['MON' => 8, 'TUE' => 9, 'WED' => 10, 'THU' => 11, 'FRI' => 12, 'SAT' => 13];

        $groups = [];

        foreach ($schedules as $schedule) {
            $day = $this->dayKey((string) $schedule->sch_day);
            if (!isset($columnByDay[$day])) {
                continue;
            }

            $start = $this->minutes((string) $schedule->sch_start_time);
            $end = $this->minutes((string) $schedule->sch_end_time);
            if ($end <= $start) {
                continue;
            }

            $column = $columnByDay[$day];
            if (!$isEvening) {
                $rowRange = $this->dayRows($start, $end);
            } else {
                $rowRange = $this->eveningRows($day, $start, $end);
            }
            if (!$rowRange) {
                continue;
            }

            [$firstRow, $lastRow] = $rowRange;
            $courseCode = $schedule->subject?->course_code ?? $schedule->subject?->subj_code ?? 'Course';
            $roomName = $schedule->room?->room_name ?? 'Room not assigned';
            $facultyLabel = $this->facultyLabel($schedule->faculty);
            if (strtoupper(trim((string) $courseCode)) === 'REC') {
                $display = "REC\nResearch, Extension and\nConsultation Time";
            } else {
                $display = $courseCode . ($facultyLabel !== '' ? "\n" . $facultyLabel : '') . "\n" . $roomName;
            }

            // Group the same class, time, faculty, and room across its days.
            // This lets adjacent weekday cells become one merged block.
            $groupKey = json_encode([
                (string) ($schedule->sch_course_id ?? $schedule->subject?->course_id ?? $courseCode),
                (string) ($schedule->sch_fac_id ?? $schedule->faculty?->fac_id ?? $facultyLabel),
                (string) ($schedule->sch_room_id ?? $schedule->room?->room_id ?? $roomName),
                $start,
                $end,
                $firstRow,
                $lastRow,
                $display,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            $color = self::COLORS[abs(crc32((string) ($schedule->sch_course_id ?? $courseCode))) % count(self::COLORS)];

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'first_row' => $firstRow,
                    'last_row' => $lastRow,
                    'display' => $display,
                    'color' => $color,
                    'columns' => [],
                ];
            }

            // A set avoids duplicate blocks if duplicate schedule rows exist for a day.
            $groups[$groupKey]['columns'][$column] = true;
        }

        foreach ($groups as $group) {
            $columns = array_map('intval', array_keys($group['columns']));
            sort($columns, SORT_NUMERIC);

            // Merge consecutive day columns only. In the evening template,
            // this keeps Friday separate from Saturday/Sunday around the divider.
            $runs = [];
            foreach ($columns as $column) {
                $lastRunIndex = count($runs) - 1;
                if ($lastRunIndex < 0 || $column !== end($runs[$lastRunIndex]) + 1) {
                    $runs[] = [$column];
                } else {
                    $runs[$lastRunIndex][] = $column;
                }
            }

            foreach ($runs as $run) {
                $firstCell = Coordinate::stringFromColumnIndex($run[0]) . $group['first_row'];
                $lastCell = Coordinate::stringFromColumnIndex(end($run)) . $group['last_row'];
                $range = $firstCell . ':' . $lastCell;

                if ($firstCell !== $lastCell) {
                    $sheet->mergeCells($range);
                }

                $sheet->setCellValue($firstCell, $group['display']);
                $style = $sheet->getStyle($range);
                $style->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($group['color']);
                $style->getFont()->getColor()->setRGB('000000');
                $style->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(true);
                $style->getBorders()->getOutline()->setBorderStyle(Border::BORDER_THIN)->getColor()->setRGB('1F2937');
            }
        }
    }

    private function dayRows(int $start, int $end): ?array
    {
        $morningStart = 7 * 60;
        $noon = 12 * 60;
        $afternoonStart = 13 * 60;
        $dayEnd = 17 * 60;

        if ($start >= $morningStart && $end <= $noon) {
            $first = 13 + (int) floor(($start - $morningStart) / 20);
            $last = 13 + (int) ceil(($end - $morningStart) / 20) - 1;
            return [$first, min(27, max($first, $last))];
        }

        if ($start >= $afternoonStart && $end <= $dayEnd) {
            $first = 31 + (int) floor(($start - $afternoonStart) / 20);
            $last = 31 + (int) ceil(($end - $afternoonStart) / 20) - 1;
            return [$first, min(42, max($first, $last))];
        }

        return null;
    }

    private function eveningRows(string $day, int $start, int $end): ?array
    {
        $weekend = in_array($day, ['SAT', 'SUN'], true);
        $gridStart = $weekend ? 7 * 60 : 16 * 60;
        $gridEnd = 22 * 60;
        $minutesPerRow = $weekend ? 30 : 12;

        // The evening template has two independent time scales:
        // weekdays use 4:00 PM-10:00 PM across 30 worksheet rows (12 minutes per row),
        // while Saturday and Sunday use 7:00 AM-10:00 PM across 30 rows (30 minutes per row).
        if ($start < $gridStart || $end > $gridEnd || $end <= $start) {
            return null;
        }

        // Round a non-aligned start forward. Otherwise a class starting at
        // 6:30 PM shares a worksheet row with the preceding class ending at
        // 6:30 PM, causing overlapping merged cells in the Excel export.
        $first = 13 + (int) ceil(($start - $gridStart) / $minutesPerRow);
        $last = 13 + (int) ceil(($end - $gridStart) / $minutesPerRow) - 1;

        return [$first, min(42, max($first, $last))];
    }

    private function facultyLabel($faculty): string
    {
        if (!$faculty) {
            return '';
        }

        $lastName = trim((string) $faculty->fac_last_name);
        if ($lastName === '') {
            return trim((string) ($faculty->full_name ?? ''));
        }

        $hasDoctorate = trim((string) ($faculty->fac_doctorate_degree ?? '')) !== '';
        return ($hasDoctorate ? 'Dr. ' : 'Prof. ') . $lastName;
    }

    private function personName($person, string $prefix): string
    {
        if (!$person) {
            return '';
        }

        $fields = [
            $person->{$prefix . '_first_name'} ?? null,
            $person->{$prefix . '_middle_name'} ?? null,
            $person->{$prefix . '_last_name'} ?? null,
            $person->{$prefix . '_suffix'} ?? null,
        ];
        return trim(implode(' ', array_filter($fields, fn($value) => trim((string) $value) !== '')));
    }

    private function dayKey(string $day): string
    {
        $day = strtoupper(trim($day));
        return match (substr($day, 0, 3)) {
            'MON' => 'MON',
            'TUE' => 'TUE',
            'WED' => 'WED',
            'THU' => 'THU',
            'FRI' => 'FRI',
            'SAT' => 'SAT',
            'SUN' => 'SUN',
            default => '',
        };
    }

    private function minutes(string $time): int
    {
        try {
            $parsed = Carbon::parse($time);
            return ((int) $parsed->format('H')) * 60 + (int) $parsed->format('i');
        } catch (\Throwable) {
            return 0;
        }
    }
}
