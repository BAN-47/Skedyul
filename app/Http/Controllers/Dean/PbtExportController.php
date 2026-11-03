<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use ZipArchive;

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

    public function index()
    {
        $dean = $this->dean();

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        $facultyMembers = Faculty::with('user')
            ->where('fac_college_id', $dean->dean_college_id)
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return view('dean.pbt_export', compact('semester', 'facultyMembers'));
    }

    public function download(Request $request)
    {
        $dean = $this->dean();

        $validated = $request->validate([
            'faculty_id' => ['required', 'uuid'],
        ]);

        $faculty = Faculty::with(['user', 'program'])
            ->where('fac_id', $validated['faculty_id'])
            ->where('fac_college_id', $dean->dean_college_id)
            ->firstOrFail();

        $semester = $this->activeSemester();

        $spreadsheet = $this->makeWorkbook($faculty, $semester);
        $filename = $this->filenameFor($faculty);

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function downloadAll()
    {
        set_time_limit(300);

        $dean = $this->dean();
        $semester = $this->activeSemester();

        $facultyMembers = Faculty::with(['user', 'program'])
            ->where('fac_college_id', $dean->dean_college_id)
            ->whereHas('schedules', function ($query) use ($semester) {
                $query->where('sch_sem_id', $semester->sem_id)
                    ->where('sch_is_active', true);
            })
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        if ($facultyMembers->isEmpty()) {
            throw ValidationException::withMessages([
                'faculty_id' => 'There are no faculty PBTs to export for the active semester.',
            ]);
        }

        if (!class_exists(ZipArchive::class)) {
            throw ValidationException::withMessages([
                'faculty_id' => 'The PHP ZIP extension is required to download all PBT files.',
            ]);
        }

        $batchId = now()->format('Ymd_His') . '_' . bin2hex(random_bytes(4));
        $tempDirectory = storage_path('app/temp/pbt-' . $batchId);
        $zipDirectory = storage_path('app/temp');
        $zipPath = $zipDirectory . '/Faculty_PBTs_' . $batchId . '.zip';

        File::ensureDirectoryExists($zipDirectory);
        File::ensureDirectoryExists($tempDirectory);

        $zip = new ZipArchive();
        $zipOpened = false;

        try {
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw ValidationException::withMessages([
                    'faculty_id' => 'The ZIP file could not be created.',
                ]);
            }

            $zipOpened = true;

            foreach ($facultyMembers as $faculty) {
                $spreadsheet = $this->makeWorkbook($faculty, $semester);
                $xlsxPath = $tempDirectory . '/' . $this->filenameFor($faculty);

                (new Xlsx($spreadsheet))->save($xlsxPath);

                if (!$zip->addFile($xlsxPath, basename($xlsxPath))) {
                    throw ValidationException::withMessages([
                        'faculty_id' => 'A faculty PBT workbook could not be added to the ZIP file.',
                    ]);
                }

                $spreadsheet->disconnectWorksheets();
                unset($spreadsheet);
            }

            if (!$zip->close()) {
                $zipOpened = false;

                throw ValidationException::withMessages([
                    'faculty_id' => 'The ZIP file could not be finalized.',
                ]);
            }

            $zipOpened = false;
        } catch (\Throwable $exception) {
            if ($zipOpened) {
                $zip->close();
            }

            File::delete($zipPath);

            throw $exception;
        } finally {
            File::deleteDirectory($tempDirectory);
        }

        return response()->download($zipPath, basename($zipPath), [
            'Content-Type' => 'application/zip',
        ])->deleteFileAfterSend(true);
    }

    private function dean(): Dean
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->first();

        abort_if(
            !$dean || !$dean->dean_college_id,
            403,
            'Your account is not assigned to a college.'
        );

        return $dean;
    }

    private function activeSemester(): Semester
    {
        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        if (!$semester) {
            throw ValidationException::withMessages([
                'faculty_id' => 'There is no active semester to export.',
            ]);
        }

        return $semester;
    }

    private function makeWorkbook(Faculty $faculty, Semester $semester)
    {
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

        $schedules = Schedule::with(['subject', 'section.program', 'room'])
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

        return $spreadsheet;
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
                'faculty_id' => 'This PBT has more course-section rows than the template can display.',
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

        $sheet->setCellValue($this->valueColumn($evening, $preparationsRow), $courseSchedules->count());
        $sheet->setCellValue($this->valueColumn($evening, $unitsRow), $units);
        $sheet->setCellValue($this->valueColumn($evening, $hoursRow), $hours);

        foreach ($schedules as $schedule) {
            $this->writeSchedule($sheet, $schedule, $evening);
        }
    }

    private function valueColumn(bool $evening, int $row): string
    {
        return 'C' . $row;
    }

    private function clearRange($sheet, string $range): void
    {
        [$startCell, $endCell] = explode(':', $range, 2);

        [$startColumn, $startRow] = Coordinate::coordinateFromString($startCell);
        [$endColumn, $endRow] = Coordinate::coordinateFromString($endCell);

        $startColumnIndex = Coordinate::columnIndexFromString($startColumn);
        $endColumnIndex = Coordinate::columnIndexFromString($endColumn);

        $rowCount = $endRow - $startRow + 1;
        $columnCount = $endColumnIndex - $startColumnIndex + 1;

        $blankValues = array_fill(
            0,
            $rowCount,
            array_fill(0, $columnCount, null)
        );

        $sheet->fromArray($blankValues, null, $startCell);
        $sheet->getStyle($range)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'FFFFFF',
                ],
            ],
        ]);
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

        foreach (
            [
                'K15:N29',
                'P15:P29',
                'K33:N42',
                'P33:P42',
                'K43:P45',
            ] as $range
        ) {
            $this->clearRange($sheet, $range);
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

        foreach (
            [
                'K17:O46',
                'Q17:R46',
            ] as $range
        ) {
            $this->clearRange($sheet, $range);
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

        return $day === 'sunday' && $start >= 7 * 60 && $end <= 22 * 60;
    }

    private function minutes($time): int
    {
        $timestamp = strtotime((string) $time);

        if ($timestamp === false) {
            return 0;
        }

        return ((int) date('G', $timestamp) * 60) + (int) date('i', $timestamp);
    }

    private function filenameFor(Faculty $faculty): string
    {
        $name = $faculty->user->usr_name
            ?? trim(implode(' ', array_filter([
                $faculty->fac_first_name,
                $faculty->fac_middle_name,
                $faculty->fac_last_name,
                $faculty->fac_suffix,
            ])));

        $safeName = preg_replace('/[^A-Za-z0-9_-]+/', '_', $name ?: 'Faculty');

        return 'PBT_' . trim($safeName, '_') . '_' . now()->format('Ymd') . '.xlsx';
    }
}
