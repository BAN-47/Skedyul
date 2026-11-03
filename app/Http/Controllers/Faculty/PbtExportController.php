<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PbtExportController extends Controller
{
    private const COLORS = [
        'DCE8F5',
        'D9EAD3',
        'FCE4D6',
        'E4DFEC',
        'FFF2CC',
        'DDEBF7',
        'E2F0D9',
        'F4CCCC',
    ];

    public function download()
    {
        $faculty = Faculty::with(['user', 'program'])
            ->where('fac_usr_id', Auth::id())
            ->firstOrFail();

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        if (!$semester) {
            throw ValidationException::withMessages([
                'faculty_id' => 'There is no active semester to export.',
            ]);
        }

        $schedules = Schedule::with(['subject', 'section.program', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        if ($schedules->isEmpty()) {
            throw ValidationException::withMessages([
                'faculty_id' => 'You have no active scheduled classes for the current semester.',
            ]);
        }

        $templatePath = storage_path('app/templates/PBT_Template.xlsx');

        if (!is_file($templatePath)) {
            throw ValidationException::withMessages([
                'faculty_id' => 'The PBT template is missing from storage/app/templates.',
            ]);
        }

        $spreadsheet = IOFactory::load($templatePath);
        $daySheet = $spreadsheet->getSheetByName('Day');
        $eveningSheet = $spreadsheet->getSheetByName('Evening');

        if (!$daySheet || !$eveningSheet) {
            throw ValidationException::withMessages([
                'faculty_id' => 'The PBT template must contain sheets named Day and Evening.',
            ]);
        }

        $facultyName = $faculty->user->usr_name
            ?? trim(implode(' ', array_filter([
                $faculty->fac_first_name,
                $faculty->fac_middle_name,
                $faculty->fac_last_name,
                $faculty->fac_suffix,
            ])));

        $chair = Dept_Chair::with('user')
            ->where('dc_dept_id', $faculty->fac_dept_id)
            ->first();

        $chairName = $chair?->full_name
            ?: ($chair?->user?->usr_name ?? 'Department Chair');

        $normalizedChairName = mb_strtoupper(
            preg_replace('/[^A-Z]/i', '', $chairName)
        );

        $chairSignature = $normalizedChairName === 'NOREENBFUENTES'
            ? 'NOREEN B. FUENTES, Ph.D., DIT'
            : mb_strtoupper($chairName);

        $academicYear = $semester->academicYear?->ay_academic_year
            ?? $semester->academicYear?->ay_year_label
            ?? '';

        $semesterLabel = trim(
            $semester->sem_name
                . ($academicYear !== ''
                    ? ', AY ' . str_replace('-', ' - ', $academicYear)
                    : '')
        );

        $this->fillSheet(
            $daySheet,
            $schedules->filter(fn($schedule) => $this->isDay($schedule))->values(),
            $facultyName,
            $chairSignature,
            $semesterLabel,
            false
        );

        $this->fillSheet(
            $eveningSheet,
            $schedules->filter(fn($schedule) => $this->isEvening($schedule))->values(),
            $facultyName,
            $chairSignature,
            $semesterLabel,
            true
        );

        $spreadsheet->setActiveSheetIndex(0);

        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $facultyName ?: 'Faculty');
        $filename = 'PBT_' . trim($safeName, '_') . '_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function fillSheet(
        $sheet,
        $schedules,
        string $facultyName,
        string $chairSignature,
        string $semesterLabel,
        bool $evening
    ): void {
        $firstCourseRow = $evening ? 20 : 18;
        $lastCourseRow = $evening ? 37 : 35;
        $preparationsRow = $evening ? 38 : 36;
        $unitsRow = $evening ? 39 : 37;
        $hoursRow = $evening ? 40 : 38;
        $designationRow = $evening ? 41 : 39;

        if ($evening) {
            $sheet->setCellValue('A9', 'Evening Program');
            $sheet->setCellValue('A10', $semesterLabel);
            $sheet->setCellValue('B12', $facultyName);
            $sheet->setCellValue('C13', '');
            $sheet->setCellValue('C14', '');
            $sheet->setCellValue('A15', 'Doctorate Degree:');
            $sheet->setCellValue('A52', $chairSignature);
        } else {
            $sheet->setCellValue('A7', 'Day Program');
            $sheet->setCellValue('A8', $semesterLabel);
            $sheet->setCellValue('B10', $facultyName);
            $sheet->setCellValue('C11', '');
            $sheet->setCellValue('C12', '');
            $sheet->setCellValue('A13', 'Doctorate Degree:');
            $sheet->setCellValue('A51', $chairSignature);
        }

        if ($evening) {
            $this->clearEveningCalendar($sheet);
        } else {
            $this->clearDayCalendar($sheet);
        }
        for ($row = $firstCourseRow; $row <= $lastCourseRow; $row++) {
            foreach (['A', 'B', 'D', 'E'] as $column) {
                $sheet->setCellValue($column . $row, null);
            }
        }

        $courseSchedules = $schedules
            ->filter(fn($schedule) => $schedule->subject && $schedule->section)
            ->unique(fn($schedule) => $schedule->sch_course_id . ':' . $schedule->sch_sec_id)
            ->values();

        if ($courseSchedules->count() > ($lastCourseRow - $firstCourseRow + 1)) {
            throw ValidationException::withMessages([
                'faculty_id' => 'Your PBT has more course-section rows than the template can display.',
            ]);
        }

        foreach ($courseSchedules as $index => $schedule) {
            $row = $firstCourseRow + $index;

            $sheet->setCellValue('A' . $row, $schedule->subject->course_code ?? '');
            $sheet->setCellValue('B' . $row, $schedule->subject->course_name ?? '');
            $sheet->setCellValue('D' . $row, $schedule->section->sec_name ?? '');
            $sheet->setCellValue('E' . $row, (int) ($schedule->section->sec_no_of_student ?? 0));
        }

        $units = $courseSchedules->sum(
            fn($schedule) => (float) ($schedule->subject->course_units ?? 0)
        );

        $hours = $schedules->sum(function ($schedule) {
            $start = strtotime((string) $schedule->sch_start_time);
            $end = strtotime((string) $schedule->sch_end_time);

            return $start && $end && $end > $start
                ? ($end - $start) / 3600
                : 0;
        });

        $sheet->setCellValue('C' . $preparationsRow, $courseSchedules->count());
        $sheet->setCellValue('C' . $unitsRow, $units);
        $sheet->setCellValue('C' . $hoursRow, $hours);
        $sheet->setCellValue(
            'C' . $designationRow,
            $this->facultyDesignation()
        );

        foreach ($schedules as $schedule) {
            $this->writeSchedule($sheet, $schedule, $evening);
        }
    }

    private function facultyDesignation(): string
    {
        $faculty = Faculty::with('user')
            ->where('fac_usr_id', Auth::id())
            ->first();

        return $faculty?->fac_special_position
            ?: ($faculty?->fac_rank ?? '');
    }

    private function clearDayCalendar($sheet): void
    {
        $protectedMerges = [
            'K30:P32',
            'O15:O29',
            'O33:O42',
        ];

        foreach ($sheet->getMergeCells() as $merge) {
            [$start, $end] = explode(':', $merge);
            $startColumn = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $start));
            $endColumn = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $end));
            $startRow = (int) preg_replace('/\D+/', '', $start);
            $endRow = (int) preg_replace('/\D+/', '', $end);

            if (
                $startColumn >= 11
                && $endColumn <= 16
                && $startRow >= 15
                && $endRow <= 45
                && !in_array($merge, $protectedMerges, true)
            ) {
                $sheet->unmergeCells($merge);
            }
        }

        for ($row = 15; $row <= 45; $row++) {
            for ($column = 11; $column <= 16; $column++) {
                $coordinate = Coordinate::stringFromColumnIndex($column) . $row;

                if ($this->isProtectedDayCell($coordinate)) {
                    continue;
                }

                $sheet->setCellValue($coordinate, null);
                $sheet->getStyle($coordinate)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('FFFFFF');
            }
        }
    }

    private function isProtectedDayCell(string $coordinate): bool
    {
        if (preg_match('/^([A-Z]+)(\d+)$/', $coordinate, $matches) !== 1) {
            return false;
        }

        $column = $matches[1];
        $row = (int) $matches[2];

        return ($row >= 30 && $row <= 32)
            || ($column === 'O' && $row >= 15 && $row <= 42);
    }

    private function clearEveningCalendar($sheet): void
    {
        foreach ($sheet->getMergeCells() as $merge) {
            [$start, $end] = explode(':', $merge);
            $startColumn = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $start));
            $endColumn = Coordinate::columnIndexFromString(preg_replace('/\d+/', '', $end));
            $startRow = (int) preg_replace('/\D+/', '', $start);
            $endRow = (int) preg_replace('/\D+/', '', $end);

            $isWeekdayScheduleMerge = $startColumn >= 11 && $endColumn <= 15;
            $isWeekendScheduleMerge = $startColumn >= 17 && $endColumn <= 18;

            if (
                ($isWeekdayScheduleMerge || $isWeekendScheduleMerge)
                && $startRow >= 17
                && $endRow <= 46
            ) {
                $sheet->unmergeCells($merge);
            }
        }

        foreach ([[11, 15], [17, 18]] as [$firstColumn, $lastColumn]) {
            for ($row = 17; $row <= 46; $row++) {
                for ($column = $firstColumn; $column <= $lastColumn; $column++) {
                    $coordinate = Coordinate::stringFromColumnIndex($column) . $row;
                    $sheet->setCellValue($coordinate, null);
                    $sheet->getStyle($coordinate)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFFFFF');
                }
            }
        }
    }
    private function writeSchedule($sheet, Schedule $schedule, bool $evening): void
    {
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

        if (!$evening) {
            $columns = [
                'monday' => 11,
                'tuesday' => 12,
                'wednesday' => 13,
                'thursday' => 14,
                'friday' => 15,
                'saturday' => 16,
            ];

            if (!isset($columns[$day])) {
                return;
            }

            $baseMinutes = $start >= 13 * 60 ? 13 * 60 : 7 * 60;
            $baseRow = $start >= 13 * 60 ? 33 : 15;
            $firstRow = $baseRow + (int) floor(($start - $baseMinutes) / 20);
            $lastRow = $baseRow + (int) ceil(($end - $baseMinutes) / 20) - 1;
            $column = $columns[$day];
        } else {
            if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true)) {
                $column = [
                    'monday' => 11,
                    'tuesday' => 12,
                    'wednesday' => 13,
                    'thursday' => 14,
                    'friday' => 15,
                ][$day];

                $baseMinutes = 16 * 60;
                $rowsPerHour = 5;
            } elseif ($day === 'saturday') {
                $column = 17;
                $baseMinutes = 7 * 60;
                $rowsPerHour = 2;
            } elseif ($day === 'sunday') {
                $column = 18;
                $baseMinutes = 7 * 60;
                $rowsPerHour = 2;
            } else {
                return;
            }

            $firstRow = 17 + (int) floor((($start - $baseMinutes) * $rowsPerHour) / 60);
            $lastRow = 17 + (int) ceil((($end - $baseMinutes) * $rowsPerHour) / 60) - 1;
        }

        $firstRow = max(15, $firstRow);
        $lastRow = max($firstRow, $lastRow);

        $cell = Coordinate::stringFromColumnIndex($column) . $firstRow;
        $lastCell = Coordinate::stringFromColumnIndex($column) . $lastRow;

        if ($firstRow !== $lastRow) {
            $sheet->mergeCells($cell . ':' . $lastCell);
        }

        $text = ($schedule->subject->course_code ?? 'Course')
            . "\n" . ($schedule->section->sec_name ?? 'Section')
            . "\n" . ($schedule->room->room_name ?? 'Room not assigned');

        $sheet->setCellValue($cell, $text);

        $colorIndex = crc32((string) $schedule->sch_course_id) % count(self::COLORS);

        $style = $sheet->getStyle($cell . ':' . $lastCell);
        $style->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::COLORS[$colorIndex]);

        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
    }

    private function isDay(Schedule $schedule): bool
    {
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

        return in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], true)
            && $start >= 7 * 60
            && $end <= 17 * 60
            && !($start < 13 * 60 && $end > 12 * 60);
    }

    private function isEvening(Schedule $schedule): bool
    {
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

        if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true)) {
            return $start >= 16 * 60 && $end <= 22 * 60;
        }

        if ($day === 'saturday') {
            return $start >= 17 * 60 && $end <= 22 * 60;
        }

        return $day === 'sunday'
            && $start >= 7 * 60
            && $end <= 22 * 60;
    }

    private function minutes($time): int
    {
        $timestamp = strtotime((string) $time);

        if ($timestamp === false) {
            return 0;
        }

        return ((int) date('G', $timestamp) * 60)
            + (int) date('i', $timestamp);
    }
}
