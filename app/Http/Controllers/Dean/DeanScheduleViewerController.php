<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DeanScheduleViewerController extends Controller
{
    public function pbs(Request $request)
    {
        $dean = $this->dean();
        $semester = $this->activeSemester();

        $departments = Departments::with('department')
            ->where('dept_college_id', $dean->dean_college_id)
            ->get()
            ->sortBy(fn($department) => $this->departmentSortKey($department))
            ->values();

        $sections = Section::with('program')
            ->whereHas('program', fn($query) => $query->where('dept_college_id', $dean->dean_college_id))
            ->orderBy('sec_name')
            ->get();

        $selectedSection = null;
        $schedules = collect();
        $sectionId = $request->query('section_id');

        if ($sectionId) {
            $selectedSection = $this->sectionForDean($dean, $sectionId)->firstOrFail();
            if ($semester) {
                $schedules = $this->sectionSchedules($selectedSection->sec_id, $semester->sem_id);
            }
        }

        return view('dean.pbs', compact(
            'semester',
            'departments',
            'sections',
            'selectedSection',
            'schedules'
        ));
    }

    public function exportPbs(Request $request)
    {
        $dean = $this->dean();
        $validated = $request->validate([
            'section_id' => ['required', 'uuid'],
        ]);
        $section = $this->sectionForDean($dean, $validated['section_id'])->firstOrFail();
        $semester = $this->activeSemester();

        if (!$semester) {
            throw ValidationException::withMessages([
                'section_id' => 'There is no active semester to export.',
            ]);
        }

        $schedules = $this->sectionSchedules($section->sec_id, $semester->sem_id);
        if ($schedules->isEmpty()) {
            throw ValidationException::withMessages([
                'section_id' => 'This section has no scheduled classes for the active semester.',
            ]);
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('PBS Schedule');
        $semesterLabel = $this->semesterLabel($semester);
        $departmentCode = $section->program?->dept_code ?? 'Department';

        $sheet->mergeCells('A1:H1');
        $sheet->setCellValue('A1', 'SKEDYUL — PROGRAM BY SECTION (PBS)');
        $sheet->mergeCells('A2:H2');
        $sheet->setCellValue('A2', 'Semester: ' . $semesterLabel);
        $sheet->mergeCells('A3:H3');
        $sheet->setCellValue('A3', 'College: ' . ($section->program?->department?->college_code ?? 'N/A')
            . '    Department: ' . $departmentCode
            . '    Section: ' . $section->sec_name);

        $headers = ['Course Code', 'Subject', 'Faculty', 'Day', 'Start Time', 'End Time', 'Room', 'Shift'];
        $sheet->fromArray($headers, null, 'A5');
        $row = 6;

        foreach ($schedules as $schedule) {
            $facultyName = $schedule->faculty?->user?->usr_name
                ?? $schedule->faculty?->full_name
                ?? 'Unassigned';

            $sheet->fromArray([
                $schedule->subject?->course_code ?? '—',
                $schedule->subject?->course_name ?? '—',
                $facultyName,
                ucfirst(strtolower((string) $schedule->sch_day)),
                $this->timeLabel($schedule->sch_start_time),
                $this->timeLabel($schedule->sch_end_time),
                $schedule->room?->room_name ?? '—',
                ucfirst(strtolower((string) $schedule->sch_shift)),
            ], null, 'A' . $row);
            $row++;
        }

        $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A5:H5')->getFont()->setBold(true);
        $sheet->getStyle('A5:H5')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('DCE8F5');
        $sheet->getStyle('A5:H' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $sheet->freezePane('A6');
        $sheet->setAutoFilter('A5:H' . ($row - 1));

        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $safeSection = preg_replace('/[^A-Za-z0-9_-]+/', '_', (string) $section->sec_name);
        $filename = 'PBS_' . trim($safeSection, '_') . '_' . now()->format('Ymd') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
            $spreadsheet->disconnectWorksheets();
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function pbt(Request $request)
    {
        $dean = $this->dean();
        $semester = $this->activeSemester();

        $facultyMembers = Faculty::with(['user', 'program'])
            ->where('fac_college_id', $dean->dean_college_id)
            ->get()
            ->sortBy(fn($faculty) => implode('|', [
                $this->departmentSortKey($faculty->program),
                strtolower((string) ($faculty->fac_last_name ?? '')),
                strtolower((string) ($faculty->fac_first_name ?? '')),
            ]))
            ->values();

        $selectedFaculty = null;
        $schedules = collect();
        $facultyId = $request->query('faculty_id');

        if ($facultyId) {
            $selectedFaculty = $facultyMembers->firstWhere('fac_id', $facultyId);
            abort_unless($selectedFaculty, 404, 'Faculty member not found in your college.');

            if ($semester) {
                $schedules = $this->facultySchedules($dean, $selectedFaculty->fac_id, $semester->sem_id);
            }
        }

        return view('dean.pbt', compact(
            'semester',
            'facultyMembers',
            'selectedFaculty',
            'schedules'
        ));
    }

    private function dean(): Dean
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->first();
        abort_if(!$dean || !$dean->dean_college_id, 403, 'Your account is not assigned to a college.');

        return $dean;
    }

    private function activeSemester(): ?Semester
    {
        return Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();
    }

    private function sectionForDean(Dean $dean, string $sectionId)
    {
        return Section::with(['program.department'])
            ->where('sec_id', $sectionId)
            ->whereHas('program', fn($query) => $query->where('dept_college_id', $dean->dean_college_id));
    }

    private function sectionSchedules(string $sectionId, string $semesterId)
    {
        return Schedule::with(['subject', 'faculty.user', 'room', 'section.program'])
            ->where('sch_sec_id', $sectionId)
            ->where('sch_sem_id', $semesterId)
            ->where('sch_is_active', true)
            ->get()
            ->sortBy(fn($schedule) => $this->scheduleSortKey($schedule))
            ->values();
    }

    private function facultySchedules(Dean $dean, string $facultyId, string $semesterId)
    {
        return Schedule::with(['subject', 'faculty.user', 'room', 'section.program'])
            ->where('sch_fac_id', $facultyId)
            ->where('sch_sem_id', $semesterId)
            ->where('sch_is_active', true)
            ->whereHas('section.program', fn($query) => $query->where('dept_college_id', $dean->dean_college_id))
            ->get()
            ->sortBy(fn($schedule) => $this->scheduleSortKey($schedule))
            ->values();
    }

    private function scheduleSortKey(Schedule $schedule): string
    {
        $dayOrder = [
            'MONDAY' => 1,
            'TUESDAY' => 2,
            'WEDNESDAY' => 3,
            'THURSDAY' => 4,
            'FRIDAY' => 5,
            'SATURDAY' => 6,
            'SUNDAY' => 7,
        ];
        $day = strtoupper((string) $schedule->sch_day);

        return str_pad((string) ($dayOrder[$day] ?? 99), 2, '0', STR_PAD_LEFT)
            . '|' . (string) $schedule->sch_start_time
            . '|' . strtolower((string) ($schedule->subject?->course_code ?? ''));
    }

    private function departmentSortKey($department): string
    {
        $order = [
            'IT' => 1,
            'BSIT' => 1,
            'IS' => 2,
            'BSIS' => 2,
            'BIT-CT' => 3,
        ];
        $code = strtoupper(trim((string) ($department?->dept_code ?? '')));

        return str_pad((string) ($order[$code] ?? 99), 2, '0', STR_PAD_LEFT) . '|' . $code;
    }

    private function semesterLabel(Semester $semester): string
    {
        $year = $semester->academicYear?->ay_academic_year
            ?? $semester->academicYear?->ay_year_label
            ?? '';
        $label = $semester->sem_name ?? '';

        return trim($label . ($year !== '' ? ', AY ' . $year : ''));
    }

    private function timeLabel($time): string
    {
        if (!$time) {
            return '—';
        }

        try {
            return Carbon::parse($time)->format('g:i A');
        } catch (\Throwable) {
            return (string) $time;
        }
    }
}
