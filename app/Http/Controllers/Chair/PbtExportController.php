<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Study_Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PbtExportController extends Controller
{
    private const DAY_TEMPLATE_COURSE_FIRST_ROW = 18;
    private const DAY_TEMPLATE_COURSE_LAST_ROW = 35;
    private const EVENING_TEMPLATE_COURSE_FIRST_ROW = 20;
    private const EVENING_TEMPLATE_COURSE_LAST_ROW = 37;

    private const CLASS_COLORS = [
        'DCE8F5',
        'D9EAD3',
        'FCE4D6',
        'E4DFEC',
        'FFF2CC',
        'DDEBF7',
        'E2F0D9',
        'F4CCCC',
    ];

    public function index()
    {
        $chair = $this->chair();

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        $facultyMembers = Faculty::with('user')
            ->where('fac_dept_id', $chair->dc_dept_id)
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return view('chair.export_reports', compact(
            'semester',
            'facultyMembers'
        ));
    }

    public function download(Request $request)
    {
        $chair = $this->chair();

        $validated = $request->validate([
            'faculty_id' => ['required', 'uuid'],
        ]);

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        if (!$semester) {
            throw ValidationException::withMessages([
                'faculty_id' => 'There is no active semester to export.',
            ]);
        }

        $faculty = Faculty::with(['user', 'program'])
            ->where('fac_id', $validated['faculty_id'])
            ->where('fac_dept_id', $chair->dc_dept_id)
            ->firstOrFail();

        $schedules = Schedule::with([
            'subject',
            'section.program',
            'room',
        ])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        if ($schedules->isEmpty()) {
            throw ValidationException::withMessages([
                'faculty_id' => 'This faculty member has no active scheduled classes for the current semester.',
            ]);
        }

        $templatePath = storage_path('app/templates/PBT_Template.xlsx');

        if (!is_file($templatePath)) {
            throw ValidationException::withMessages([
                'faculty_id' => 'The PBT Excel template is missing from storage/app/templates.',
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

        $chairName = $chair->full_name
            ?: ($chair->user?->usr_name ?? 'Department Chair');

        $academicYear = $semester->academicYear?->ay_academic_year
            ?? $semester->academicYear?->ay_year_label
            ?? '';

        $semesterLabel = trim(
            $semester->sem_name
                . ($academicYear !== '' ? ', AY ' . str_replace('-', ' - ', $academicYear) : '')
        );

        $this->fillDaySheet(
            $daySheet,
            $faculty,
            $facultyName,
            $chairName,
            $semesterLabel,
            $schedules
        );

        $this->fillEveningSheet(
            $eveningSheet,
            $faculty,
            $facultyName,
            $chairName,
            $semesterLabel,
            $schedules
        );

        $spreadsheet->setActiveSheetIndex(0);

        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $facultyName ?: 'Faculty');
        $filename = 'PBT_' . trim($safeName, '_') . '_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function chair(): Dept_Chair
    {
        $chair = Dept_Chair::with('user')
            ->where('dc_usr_id', Auth::id())
            ->first();

        abort_if(
            !$chair || !$chair->dc_dept_id,
            403,
            'Your account is not assigned to a program department.'
        );

        return $chair;
    }

    private function fillDaySheet(
        $sheet,
        Faculty $faculty,
        string $facultyName,
        string $chairName,
        string $semesterLabel,
        $schedules
    ): void {
        $sheet->setCellValue('A7', 'Day Program');
        $sheet->setCellValue('A8', $semesterLabel);
        $sheet->setCellValue('B10', $facultyName);
        $sheet->setCellValue('C11', '');
        $sheet->setCellValue('C12', '');
        $sheet->setCellValue('A13', 'Doctorate Degree:');
        $sheet->setCellValue('K11', '___ Permanent');
        $sheet->setCellValue('L11', '___ Temporary');
        $sheet->setCellValue('M11', '___ Contract of Service');
        $sheet->setCellValue('K12', '');
        $sheet->setCellValue('K13', '');
        $sheet->setCellValue('A51', mb_strtoupper($chairName));

        $daySchedules = $schedules
            ->filter(fn($schedule) => $this->isDaySchedule($schedule))
            ->values();

        $this->clearCourseRows(
            $sheet,
            self::DAY_TEMPLATE_COURSE_FIRST_ROW,
            self::DAY_TEMPLATE_COURSE_LAST_ROW
        );

        $this->clearDayCalendar($sheet);

        $this->fillCourseRows(
            $sheet,
            $daySchedules,
            self::DAY_TEMPLATE_COURSE_FIRST_ROW,
            self::DAY_TEMPLATE_COURSE_LAST_ROW
        );

        $this->fillLoadTotals(
            $sheet,
            $faculty,
            $daySchedules,
            36,
            37,
            38,
            39
        );

        foreach ($daySchedules as $schedule) {
            $this->writeDaySchedule($sheet, $schedule);
        }
    }

    private function fillEveningSheet(
        $sheet,
        Faculty $faculty,
        string $facultyName,
        string $chairName,
        string $semesterLabel,
        $schedules
    ): void {
        $sheet->setCellValue('A9', 'Evening Program');
        $sheet->setCellValue('A10', $semesterLabel);
        $sheet->setCellValue('B12', $facultyName);
        $sheet->setCellValue('C13', '');
        $sheet->setCellValue('C14', '');
        $sheet->setCellValue('A15', 'Doctorate Degree:');
        $sheet->setCellValue('K13', '___ Permanent');
        $sheet->setCellValue('L13', '___ Temporary');
        $sheet->setCellValue('M13', '___ Contract of Service');
        $sheet->setCellValue('K14', '');
        $sheet->setCellValue('K15', '');
        $sheet->setCellValue('A52', mb_strtoupper($chairName));

        $eveningSchedules = $schedules
            ->filter(fn($schedule) => $this->isEveningSchedule($schedule))
            ->values();

        $this->clearCourseRows(
            $sheet,
            self::EVENING_TEMPLATE_COURSE_FIRST_ROW,
            self::EVENING_TEMPLATE_COURSE_LAST_ROW
        );

        $this->clearEveningCalendar($sheet);

        $this->fillCourseRows(
            $sheet,
            $eveningSchedules,
            self::EVENING_TEMPLATE_COURSE_FIRST_ROW,
            self::EVENING_TEMPLATE_COURSE_LAST_ROW
        );

        $this->fillLoadTotals(
            $sheet,
            $faculty,
            $eveningSchedules,
            38,
            39,
            40,
            41
        );

        foreach ($eveningSchedules as $schedule) {
            $this->writeEveningSchedule($sheet, $schedule);
        }
    }

    private function clearCourseRows($sheet, int $firstRow, int $lastRow): void
    {
        for ($row = $firstRow; $row <= $lastRow; $row++) {
            foreach (['A', 'B', 'D', 'E'] as $column) {
                $sheet->setCellValue($column . $row, null);
            }
        }
    }

    private function fillCourseRows($sheet, $schedules, int $firstRow, int $lastRow): void
    {
        $courses = $schedules
            ->filter(fn($schedule) => $schedule->subject && $schedule->section)
            ->unique(fn($schedule) => $schedule->sch_course_id . ':' . $schedule->sch_sec_id)
            ->values();

        $availableRows = $lastRow - $firstRow + 1;

        if ($courses->count() > $availableRows) {
            throw ValidationException::withMessages([
                'faculty_id' => 'This PBT has more course-section rows than the provided template can display.',
            ]);
        }

        foreach ($courses as $index => $schedule) {
            $row = $firstRow + $index;
            $section = $schedule->section;

            $sheet->setCellValue('A' . $row, $schedule->subject->course_code ?? '');
            $sheet->setCellValue('B' . $row, $schedule->subject->course_name ?? '');
            $sheet->setCellValue('D' . $row, $section->sec_name ?? '');
            $sheet->setCellValue('E' . $row, (int) ($section->sec_no_of_student ?? 0));
        }
    }

    private function fillLoadTotals(
        $sheet,
        Faculty $faculty,
        $schedules,
        int $preparationsRow,
        int $unitsRow,
        int $hoursRow,
        int $designationRow
    ): void {
        $studyLoads = Study_Load::with('subject')
            ->where('sl_fac_id', $faculty->fac_id)
            ->whereHas('schedules', function ($query) use ($schedules) {
                $scheduleIds = $schedules->pluck('sch_id')->all();

                if ($scheduleIds) {
                    $query->whereIn('sch_id', $scheduleIds);
                } else {
                    $query->whereRaw('1 = 0');
                }
            })
            ->get();

        $preparations = $schedules
            ->pluck('sch_course_id')
            ->filter()
            ->unique()
            ->count();

        $units = $studyLoads
            ->map(fn($load) => (float) ($load->subject?->course_units ?? 0))
            ->sum();

        $hours = $schedules->sum(function ($schedule) {
            $start = strtotime((string) $schedule->sch_start_time);
            $end = strtotime((string) $schedule->sch_end_time);

            return $start && $end && $end > $start
                ? ($end - $start) / 3600
                : 0;
        });

        $sheet->setCellValue('C' . $preparationsRow, $preparations);
        $sheet->setCellValue('C' . $unitsRow, $units);
        $sheet->setCellValue('C' . $hoursRow, $hours);
        $sheet->setCellValue(
            'C' . $designationRow,
            $faculty->fac_special_position ?: ($faculty->fac_rank ?? '')
        );
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

    private function isDaySchedule(Schedule $schedule): bool
    {
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

        return in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], true)
            && $start >= 7 * 60
            && $end <= 17 * 60
            && !($start < 13 * 60 && $end > 12 * 60);
    }

    private function isEveningSchedule(Schedule $schedule): bool
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

        return ((int) date('G', $timestamp) * 60) + (int) date('i', $timestamp);
    }

    private function writeDaySchedule($sheet, Schedule $schedule): void
    {
        $dayColumns = [
            'monday' => 11,
            'tuesday' => 12,
            'wednesday' => 13,
            'thursday' => 14,
            'friday' => 15,
            'saturday' => 16,
        ];

        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

        $baseRow = $start >= 13 * 60 ? 33 : 15;
        $baseMinutes = $start >= 13 * 60 ? 13 * 60 : 7 * 60;

        $this->writeScheduleBlock(
            $sheet,
            $schedule,
            $dayColumns[$day],
            $baseRow + (int) floor(($start - $baseMinutes) / 20),
            $baseRow + (int) ceil(($end - $baseMinutes) / 20) - 1
        );
    }

    private function writeEveningSchedule($sheet, Schedule $schedule): void
    {
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->minutes($schedule->sch_start_time);
        $end = $this->minutes($schedule->sch_end_time);

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
        } else {
            $column = 18;
            $baseMinutes = 7 * 60;
            $rowsPerHour = 2;
        }

        $baseRow = 17;

        $this->writeScheduleBlock(
            $sheet,
            $schedule,
            $column,
            $baseRow + (int) floor((($start - $baseMinutes) * $rowsPerHour) / 60),
            $baseRow + (int) ceil((($end - $baseMinutes) * $rowsPerHour) / 60) - 1
        );
    }

    private function writeScheduleBlock($sheet, Schedule $schedule, int $column, int $firstRow, int $lastRow): void
    {
        $firstRow = max(15, $firstRow);
        $lastRow = max($firstRow, $lastRow);
        $coordinate = Coordinate::stringFromColumnIndex($column) . $firstRow;
        $lastCoordinate = Coordinate::stringFromColumnIndex($column) . $lastRow;

        if ($firstRow !== $lastRow) {
            $sheet->mergeCells($coordinate . ':' . $lastCoordinate);
        }

        $courseCode = $schedule->subject->course_code ?? 'Course';
        $sectionName = $schedule->section->sec_name ?? 'Section';
        $roomName = $schedule->room->room_name ?? 'Room not assigned';

        $sheet->setCellValue(
            $coordinate,
            $courseCode . "\n" . $sectionName . "\n" . $roomName
        );

        $paletteIndex = crc32((string) $schedule->sch_course_id) % count(self::CLASS_COLORS);
        $style = $sheet->getStyle($coordinate . ':' . $lastCoordinate);
        $style->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB(self::CLASS_COLORS[$paletteIndex]);

        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
    }
}
