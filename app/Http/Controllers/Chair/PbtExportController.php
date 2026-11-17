<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\SystemSetting;
use App\Services\ScheduleSignatoryFormatter;
use App\Services\FacultyNameFormatter;
use App\Services\CampusDirectorSignature;
use App\Models\Study_Load;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
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

    public function index(Request $request)
    {
        $chair = $this->chair();
        $collegeId = $chair->program?->dept_college_id ?? $chair->dc_college_id;

        $semester = $request->filled('semester')
            ? Semester::with('academicYear')->find($request->query('semester'))
            : null;
        $semester ??= Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();
        $semester ??= Semester::with('academicYear')
            ->orderByDesc('sem_start_date')
            ->first();

        $semesters = Semester::with('academicYear')
            ->orderByDesc('sem_start_date')
            ->get()
            ->map(function (Semester $option) {
                $year = $option->academicYear?->ay_academic_year
                    ?? $option->academicYear?->ay_year_label
                    ?? '';
                $yearLabel = $year !== '' ? str_replace('-', ' - ', $year) : '';

                $option->label = trim(
                    ($option->sem_name ?? '')
                        . ($yearLabel !== '' ? ', AY ' . $yearLabel : '')
                        . ($option->sem_is_active ? ' (Current)' : '')
                );

                return $option;
            });

        $facultyMembers = Faculty::with('user', 'program')
            ->where(function ($query) use ($collegeId) {
                $query->where('fac_college_id', $collegeId)
                    ->orWhereHas('program', fn ($program) => $program->where('dept_college_id', $collegeId));
                if (!$collegeId) {
                    $query->whereRaw('1 = 0');
                }
            })
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->orderBy('fac_last_name')
            ->orderBy('fac_first_name')
            ->get();

        return view('chair.export_reports', compact(
            'semester',
            'semesters',
            'facultyMembers'
        ));
    }

    public function download(Request $request)
    {
        $chair = $this->chair();

        $validated = $request->validate([
            'faculty_id' => ['required', 'uuid', 'exists:faculty,fac_id'],
            'semester' => ['nullable', 'uuid', 'exists:semester,sem_id'],
        ]);

        $semester = !empty($validated['semester'])
            ? Semester::with('academicYear')->findOrFail($validated['semester'])
            : Semester::with('academicYear')->where('sem_is_active', true)->first();

        if (!$semester) {
            throw ValidationException::withMessages([
                'faculty_id' => 'There is no active semester to export.',
            ]);
        }

        $faculty = Faculty::with(['user', 'program'])
            ->where('fac_id', $validated['faculty_id'])
            ->whereHas('user', fn($query) => $query->where('usr_is_active', true))
            ->firstOrFail();

        $collegeId = $chair->program?->dept_college_id ?? $chair->dc_college_id;
        $facultyInCollege = $collegeId && (
            (string) $faculty->fac_college_id === (string) $collegeId
            || (string) $faculty->program?->dept_college_id === (string) $collegeId
        );
        abort_unless($facultyInCollege, 403, 'You can only export faculty schedules within your college.');

        $schedules = Schedule::with([
            'subject',
            'section.program',
            'room',
            'studyLoad.section',
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

        $templatePath = app(\App\Services\ExportTemplateStorage::class)->localPath('PBT_Template.xlsx');

        if (!$templatePath) {
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

        $facultyName = FacultyNameFormatter::format($faculty);
        $doctorateAbbreviation = trim((string) $faculty->fac_doctorate_abbr);
        if ($doctorateAbbreviation !== '') {
            $facultyName = str_replace(
                '(' . FacultyNameFormatter::highestPostNominal($faculty) . ')',
                '(' . mb_strtoupper($doctorateAbbreviation) . ')',
                $facultyName
            );
        }

        $chairSignature = app(ScheduleSignatoryFormatter::class)->formatRoleName($chair, 'dc_')
            ?: ($chair->user?->usr_name ?? 'Department Chair');
        $campusDirector = trim((string) SystemSetting::get('campus_director_name', ''));

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
            $chairSignature,
            $campusDirector,
            $semesterLabel,
            $schedules
        );

        $this->fillEveningSheet(
            $eveningSheet,
            $faculty,
            $facultyName,
            $chairSignature,
            $campusDirector,
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
        $chair = Dept_Chair::with('user.faculty', 'program')
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
        string $chairSignature,
        string $campusDirector,
        string $semesterLabel,
        $schedules
    ): void {
        $sheet->setCellValue('A7', 'Day Program');
        $sheet->setCellValue('A8', $semesterLabel);
        $sheet->setCellValue('B10', $facultyName);
        $sheet->setCellValue('C11', '');
        $sheet->setCellValue('C12', '');
        $sheet->setCellValue('A13', $this->doctorateDegreeLabel($faculty));
        $sheet->setCellValue('K11', '___ Permanent');
        $sheet->setCellValue('L11', '___ Temporary');
        $sheet->setCellValue('M11', '___ Contract of Service');
        $sheet->setCellValue('K12', '');
        $sheet->setCellValue('K13', '');
        $sheet->setCellValue('A51', $chairSignature);
        CampusDirectorSignature::write($sheet, $campusDirector, 'N51');

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
            self::DAY_TEMPLATE_COURSE_LAST_ROW,
            $schedules,
            'day'
        );
        $this->styleCourseSummaryGrid(
            $sheet,
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
            39,
            $schedules,
            'day'
        );

        $sheet->getStyle('A36:F44')->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000');

        $this->writeGroupedSchedules($sheet, $daySchedules, $schedules, false);
    }

    private function fillEveningSheet(
        $sheet,
        Faculty $faculty,
        string $facultyName,
        string $chairSignature,
        string $campusDirector,
        string $semesterLabel,
        $schedules
    ): void {
        $sheet->setCellValue('A9', 'Evening Program');
        $sheet->setCellValue('A10', $semesterLabel);
        $sheet->setCellValue('B12', $facultyName);
        $sheet->setCellValue('C13', '');
        $sheet->setCellValue('C14', '');
        $sheet->setCellValue('A15', $this->doctorateDegreeLabel($faculty));
        $sheet->setCellValue('K13', '___ Permanent');
        $sheet->setCellValue('L13', '___ Temporary');
        $sheet->setCellValue('M13', '___ Contract of Service');
        $sheet->setCellValue('K14', '');
        $sheet->setCellValue('K15', '');
        $sheet->setCellValue('A52', $chairSignature);
        CampusDirectorSignature::write($sheet, $campusDirector, 'N52');
        $this->centerEveningCampusDirectorSignature($sheet);

        $eveningSchedules = $schedules
            ->filter(fn($schedule) => $this->isEveningSchedule($schedule))
            ->values();
        $eveningColumnWidths = [
            'A' => 8.543,
            'B' => 10.363,
            'C' => 14.906,
            'D' => 12.453,
            'E' => 10.363,
            'F' => 1.363,
            'G' => 10.453,
            'H' => 10.453,
            'I' => 5.543,
        ];
        foreach ($eveningColumnWidths as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }

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
            self::EVENING_TEMPLATE_COURSE_LAST_ROW,
            $schedules,
            'evening'
        );
        $this->styleCourseSummaryGrid(
            $sheet,
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
            41,
            $schedules,
            'evening'
        );

        $this->writeGroupedSchedules($sheet, $eveningSchedules, $schedules, true);
    }

    private function centerEveningCampusDirectorSignature($sheet): void
    {
        $nameRange = 'N52:R52';
        if (!in_array($nameRange, $sheet->getMergeCells(), true)) {
            $sheet->mergeCells($nameRange);
        }

        $sheet->getStyle($nameRange)->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(false);
        $sheet->getStyle($nameRange)->getFont()->setBold(true);
        $sheet->getStyle($nameRange)->getBorders()->getBottom()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000');
        $sheet->getStyle('N53:R53')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);
    }

    private function doctorateDegreeLabel(Faculty $faculty): string
    {
        $abbreviation = trim((string) $faculty->fac_doctorate_abbr);
        $degree = $abbreviation !== ''
            ? mb_strtoupper($abbreviation)
            : trim((string) $faculty->fac_doctorate_degree);

        return 'Doctorate Degree:' . ($degree !== '' ? ' ' . $degree : '');
    }
    private function styleCourseSummaryGrid($sheet, int $firstRow, int $lastRow): void
    {
        for ($row = $firstRow; $row <= $lastRow; $row++) {
            $hasCourseData = false;
            foreach (['A', 'B', 'D', 'E'] as $column) {
                $value = $sheet->getCell($column . $row)->getValue();
                if ($value !== null && trim((string) $value) !== '') {
                    $hasCourseData = true;
                    break;
                }
            }

            if (!$hasCourseData) {
                continue;
            }

            foreach (["B{$row}:C{$row}", "E{$row}:F{$row}"] as $mergeRange) {
                if (!in_array($mergeRange, $sheet->getMergeCells(), true)) {
                    $sheet->mergeCells($mergeRange);
                }
            }

            foreach (["A{$row}", "B{$row}:C{$row}", "D{$row}", "E{$row}:F{$row}"] as $range) {
                $style = $sheet->getStyle($range);
                $style->getBorders()->getOutline()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()->setRGB('000000');
                $style->getFont()->setName('Arial Narrow')->setSize(9)->setBold(false);
                $style->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            }

            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
            $sheet->getStyle("B{$row}:C{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setWrapText(false);
            $sheet->getStyle("D{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}:F{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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

    private function fillCourseRows($sheet, $schedules, int $firstRow, int $lastRow, $allSchedules, string $summaryShift): void
    {
        $courseRows = $schedules
            ->filter(fn($schedule) => $schedule->subject && $schedule->section
                && !\App\Services\ScheduleAssignmentService::isOjt($schedule->subject))
            ->unique(fn($schedule) => $schedule->sch_course_id . ':' . $schedule->sch_sec_id);
        $ojtRows = $allSchedules
            ->filter(fn($schedule) => $schedule->subject && $schedule->section
                && \App\Services\ScheduleAssignmentService::isOjt($schedule->subject)
                && (($schedule->studyLoad?->sl_ojt_is_evening ? 'evening' : 'day') === $summaryShift))
            ->unique('sch_course_id');
        $courses = $courseRows->concat($ojtRows)->values();

        $availableRows = $lastRow - $firstRow + 1;

        if ($courses->count() > $availableRows) {
            throw ValidationException::withMessages([
                'faculty_id' => 'This PBT has more course-section rows than the provided template can display.',
            ]);
        }

        foreach ($courses as $index => $schedule) {
            $row = $firstRow + $index;
            $course = $schedule->subject;
            $isOjt = \App\Services\ScheduleAssignmentService::isOjt($course);
            $courseSections = $isOjt
                ? $allSchedules->where('sch_course_id', $schedule->sch_course_id)->map(fn($item) => $item->section)->filter()->unique('sec_id')
                : collect([$schedule->section])->filter();
            $sectionNames = $courseSections->pluck('sec_name')->filter()->implode(', ');
            $studentCount = $courseSections->sum(fn($section) => (int) ($section->sec_no_of_student ?? 0));

            $sheet->setCellValue('A' . $row, $course->course_code ?? '');
            $courseTitle = trim((string) ($course->course_name ?? ''));
            $sheet->setCellValue('B' . $row, $courseTitle);
            if ($summaryShift === 'evening') {
                $sheet->getStyle('B' . $row . ':C' . $row)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setWrapText(false);
            }
            $sheet->setCellValue('D' . $row, $sectionNames);
            $sheet->setCellValue('E' . $row, $studentCount);
        }
    }

    private function fillLoadTotals(
        $sheet,
        Faculty $faculty,
        $schedules,
        int $preparationsRow,
        int $unitsRow,
        int $hoursRow,
        int $designationRow,
        $allSchedules,
        string $summaryShift
    ): void {
        $ojtScheduleIds = $allSchedules
            ->filter(fn($schedule) => \App\Services\ScheduleAssignmentService::isOjt($schedule->subject)
                && (($schedule->studyLoad?->sl_ojt_is_evening ? 'evening' : 'day') === $summaryShift))
            ->pluck('sch_id');
        $scheduleIds = $schedules->pluck('sch_id')->merge($ojtScheduleIds)->filter()->unique()->all();
        $studyLoads = $scheduleIds
            ? Study_Load::with('subject')
            ->where('sl_fac_id', $faculty->fac_id)
            ->whereHas('schedules', fn($query) => $query->whereIn('sch_id', $scheduleIds))
            ->get()
            ->unique('sl_id')
            : collect();

        $preparations = $studyLoads
            ->filter(fn($load) => ($load->subject?->course_code ?? '') !== 'REC')
            ->pluck('sl_course_id')
            ->filter()
            ->unique()
            ->count();

        $countedOjt = [];
        $units = $studyLoads->sum(function ($load) use (&$countedOjt) {
            $course = $load->subject;
            if (\App\Services\ScheduleAssignmentService::isOjt($course)) {
                $courseId = (string) $course->course_id;
                if (isset($countedOjt[$courseId])) return 0;
                $countedOjt[$courseId] = true;
            }
            return \App\Services\ScheduleAssignmentService::courseUnits($course);
        });

        $countedOjtHours = [];
        $hours = $studyLoads->sum(function ($load) use (&$countedOjtHours) {
            $course = $load->subject;
            if (\App\Services\ScheduleAssignmentService::isOjt($course)) {
                $courseId = (string) $course->course_id;
                if (isset($countedOjtHours[$courseId])) return 0;
                $countedOjtHours[$courseId] = true;
            }
            return \App\Services\ScheduleAssignmentService::courseHours($course);
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
        if ($this->isEveningSection($schedule)) {
            return false;
        }

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
        if ($this->hasSection($schedule)) {
            if (!$this->isEveningSection($schedule)) {
                return false;
            }

            if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true)) {
                return $start >= 16 * 60 && $end <= 22 * 60;
            }

            return in_array($day, ['saturday', 'sunday'], true)
                && $start >= 7 * 60
                && $end <= 22 * 60;
        }

        if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true)) {
            return $start >= (16 * 60) && $end <= 22 * 60;
        }

        if (in_array($day, ['saturday', 'sunday'], true)) {
            return $start >= 7 * 60 && $end <= 22 * 60;
        }

        return false;
    }

    private function isEveningSection(Schedule $schedule): bool
    {
        return $this->isTruthyDatabaseBoolean($schedule->section?->sec_is_evening)
            || $this->isTruthyDatabaseBoolean($schedule->studyLoad?->section?->sec_is_evening);
    }

    private function hasSection(Schedule $schedule): bool
    {
        return (bool) ($schedule->sch_sec_id || $schedule->section?->sec_id || $schedule->studyLoad?->sl_sec_id);
    }

    private function isTruthyDatabaseBoolean(mixed $value): bool
    {
        return is_bool($value)
            ? $value
            : in_array(strtolower(trim((string) $value)), ['1', 'true', 't', 'yes', 'on'], true);
    }

    private function minutes($time): int
    {
        $timestamp = strtotime((string) $time);

        if ($timestamp === false) {
            return 0;
        }

        return ((int) date('G', $timestamp) * 60) + (int) date('i', $timestamp);
    }

    private function writeGroupedSchedules($sheet, $schedules, $allSchedules, bool $evening): void
    {
        $dayColumns = $evening
            ? [
                'monday' => 11,
                'tuesday' => 12,
                'wednesday' => 13,
                'thursday' => 14,
                'friday' => 15,
                'saturday' => 17,
                'sunday' => 18,
            ]
            : [
                'monday' => 11,
                'tuesday' => 12,
                'wednesday' => 13,
                'thursday' => 14,
                'friday' => 15,
                'saturday' => 16,
            ];
        $groups = [];

        foreach ($schedules as $schedule) {
            $day = strtolower(trim((string) $schedule->sch_day));
            if (!isset($dayColumns[$day])) {
                continue;
            }

            $start = $this->minutes($schedule->sch_start_time);
            $end = $this->minutes($schedule->sch_end_time);
            if ($end <= $start) {
                continue;
            }

            if (!$evening) {
                $baseRow = $start >= 13 * 60 ? 33 : 15;
                $baseMinutes = $start >= 13 * 60 ? 13 * 60 : 7 * 60;
                $firstRow = $baseRow + (int) ceil(($start - $baseMinutes) / 20);
                $lastRow = $baseRow + (int) ceil(($end - $baseMinutes) / 20) - 1;
            } else {
                $isWeekday = in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true);
                $baseMinutes = $isWeekday ? 16 * 60 : 7 * 60;
                $rowsPerHour = $isWeekday ? 5 : 2;
                $firstRow = 17 + (int) ceil((($start - $baseMinutes) * $rowsPerHour) / 60);
                $lastRow = 17 + (int) ceil((($end - $baseMinutes) * $rowsPerHour) / 60) - 1;
            }

            $sectionNames = $this->sectionNamesForSchedule($schedule, $allSchedules);
            $groupKey = json_encode([
                (string) ($schedule->sch_course_id ?? $schedule->subject?->course_id ?? ''),
                (string) ($schedule->sch_sec_id ?? $schedule->section?->sec_id ?? ''),
                (string) ($schedule->sch_fac_id ?? ''),
                (string) ($schedule->sch_room_id ?? $schedule->room?->room_id ?? ''),
                $start,
                $end,
                $firstRow,
                $lastRow,
                $sectionNames,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'schedule' => $schedule,
                    'section_names' => $sectionNames,
                    'first_row' => $firstRow,
                    'last_row' => $lastRow,
                    'columns' => [],
                ];
            }

            $groups[$groupKey]['columns'][$dayColumns[$day]] = true;
        }

        foreach ($groups as $group) {
            $columns = array_map('intval', array_keys($group['columns']));
            sort($columns, SORT_NUMERIC);
            $runs = [];

            foreach ($columns as $column) {
                $lastRun = count($runs) - 1;
                if ($lastRun < 0 || $column !== $runs[$lastRun][1] + 1) {
                    $runs[] = [$column, $column];
                } else {
                    $runs[$lastRun][1] = $column;
                }
            }

            foreach ($runs as [$firstColumn, $lastColumn]) {
                $this->writeScheduleBlock(
                    $sheet,
                    $group['schedule'],
                    $firstColumn,
                    $group['first_row'],
                    $group['last_row'],
                    $group['section_names'],
                    $lastColumn
                );
            }
        }
    }
    private function writeDaySchedule($sheet, Schedule $schedule, $allSchedules): void
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
            $baseRow + (int) ceil(($start - $baseMinutes) / 20),
            $baseRow + (int) ceil(($end - $baseMinutes) / 20) - 1,
            $this->sectionNamesForSchedule($schedule, $allSchedules)
        );
    }

    private function writeEveningSchedule($sheet, Schedule $schedule, $allSchedules): void
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
            $baseRow + (int) ceil((($start - $baseMinutes) * $rowsPerHour) / 60),
            $baseRow + (int) ceil((($end - $baseMinutes) * $rowsPerHour) / 60) - 1,
            $this->sectionNamesForSchedule($schedule, $allSchedules)
        );
    }

    private function sectionNamesForSchedule(Schedule $schedule, $allSchedules): string
    {
        if (!\App\Services\ScheduleAssignmentService::isOjt($schedule->subject)) {
            return $schedule->section->sec_name ?? 'Section';
        }

        return $allSchedules->where('sch_course_id', $schedule->sch_course_id)
            ->where('sch_day', $schedule->sch_day)
            ->filter(fn($item) => substr((string) $item->sch_start_time, 0, 5) === substr((string) $schedule->sch_start_time, 0, 5)
                && substr((string) $item->sch_end_time, 0, 5) === substr((string) $schedule->sch_end_time, 0, 5))
            ->map(fn($item) => $item->section?->sec_name)
            ->filter()
            ->unique()
            ->implode(', ');
    }

    private function writeScheduleBlock($sheet, Schedule $schedule, int $column, int $firstRow, int $lastRow, ?string $sectionNames = null, ?int $lastColumn = null): void
    {
        $firstRow = max(15, $firstRow);
        $lastRow = max($firstRow, $lastRow);
        $lastColumn ??= $column;
        $coordinate = Coordinate::stringFromColumnIndex($column) . $firstRow;
        $lastCoordinate = Coordinate::stringFromColumnIndex($lastColumn) . $lastRow;

        if ($firstRow !== $lastRow || $column !== $lastColumn) {
            $sheet->mergeCells($coordinate . ':' . $lastCoordinate);
        }

        $courseCode = $schedule->subject->course_code ?? 'Course';
        $sectionName = $sectionNames ?: ($schedule->section->sec_name ?? 'Section');
        $roomName = $schedule->room->room_name ?? 'Room not assigned';

        if ($courseCode === 'REC') {
            $displayText = "Research,\nExtension\nand\nConsultation\ntime";
        } else {
            $displayText = $courseCode . "\n" . $sectionName . "\n" . $roomName;
        }

        $sheet->setCellValue($coordinate, $displayText);

        $paletteIndex = crc32((string) $schedule->sch_course_id) % count(self::CLASS_COLORS);
        $style = $sheet->getStyle($coordinate . ':' . $lastCoordinate);

        if ($courseCode === 'REC') {
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB('FFFFFF');
        } else {
            $style->getFill()
                ->setFillType(Fill::FILL_SOLID)
                ->getStartColor()->setRGB(self::CLASS_COLORS[$paletteIndex]);
        }

        $style->getFont()->getColor()->setRGB('000000'); // Ensure text color is black
        $style->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER)
            ->setWrapText(true);
        $style->getBorders()->getOutline()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setRGB('000000');
    }
}
