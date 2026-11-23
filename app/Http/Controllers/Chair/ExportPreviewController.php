<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Services\FacultyNameFormatter;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ExportPreviewController extends Controller
{
    public function pbt(Request $request)
    {
        $routeName = (string) $request->route()?->getName();
        $forDean = str_starts_with($routeName, 'dean.');
        $forFaculty = str_starts_with($routeName, 'faculty.');
        $dean = $forDean ? $this->dean() : null;
        $chair = (!$forDean && !$forFaculty) ? $this->chair() : null;

        $rules = [
            'semester' => ['required', 'uuid', 'exists:semester,sem_id'],
        ];
        if (!$forFaculty) {
            $rules['faculty_id'] = ['required', 'uuid', 'exists:faculty,fac_id'];
        }
        $data = $request->validate($rules);

        $facultyQuery = Faculty::with(['user', 'program'])
            ->where('fac_is_placeholder', false)
            ->whereHas('user');
        if ($forFaculty) {
            $facultyQuery->where('fac_usr_id', Auth::id());
        } else {
            $facultyQuery->where('fac_id', $data['faculty_id']);
        }
        $faculty = $facultyQuery->firstOrFail();

        $collegeId = $dean?->dean_college_id ?? $chair?->program?->dept_college_id ?? $chair?->dc_college_id;
        if (!$forFaculty) {
            abort_unless(
                $collegeId && (
                    (string) $faculty->fac_college_id === (string) $collegeId
                    || (string) $faculty->program?->dept_college_id === (string) $collegeId
                ),
                403,
                'You can only preview faculty schedules within your college.'
            );
        }

        $semester = Semester::with('academicYear')->findOrFail($data['semester']);
        $schedules = Schedule::with(['subject', 'section', 'room', 'studyLoad'])
            ->where(function ($query) use ($faculty, $forFaculty) {
                $query->where('sch_fac_id', $faculty->fac_id);
                if ($forFaculty) {
                    $query->orWhereHas('studyLoad', fn($loadQuery) => $loadQuery->where('sl_fac_id', $faculty->fac_id));
                }
            })
            ->where(function ($query) use ($semester, $forFaculty) {
                $query->where('sch_sem_id', $semester->sem_id);
                if ($forFaculty) {
                    $query->orWhereHas('studyLoad', fn($loadQuery) => $loadQuery->where('sl_sem_id', $semester->sem_id));
                }
            })
            ->where('sch_is_active', true)
            ->when($forDean, fn($query) => $query->whereHas(
                'section.program',
                fn($section) => $section->where('dept_college_id', $collegeId)
            ))
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        abort_if($schedules->isEmpty(), 422, 'This faculty member has no active scheduled classes for the selected semester.');

        $facultyName = FacultyNameFormatter::format($faculty) ?: 'Faculty';
        $rows = $schedules->map(fn($schedule) => [
            'section' => $schedule->section?->sec_name ?? '—',
            'faculty' => $facultyName,
            'course_code' => $schedule->subject?->course_code ?? '—',
            'course_name' => $schedule->subject?->course_name ?? '—',
            'mis_code' => $schedule->sch_mis_code ?? '',
            'day' => $schedule->sch_day ?? '—',
            'start_time' => $schedule->sch_start_time,
            'end_time' => $schedule->sch_end_time,
            'room' => $schedule->room?->room_name ?? '—',
            'shift' => $this->sectionShiftKey($schedule),
        ])->values();
        $rowsByShift = $this->groupRowsByShift($rows);
        $displayShifts = ['day' => 'Day', 'evening' => 'Evening'];

        $coursesByShift = $this->courseSummaryByShift($schedules, null, true);
        $workloadByShift = [
            'day' => $this->pbtWorkloadSummary($faculty, $schedules, 'day'),
            'evening' => $this->pbtWorkloadSummary($faculty, $schedules, 'evening'),
        ];

        $semesterLabel = $this->semesterLabel($semester);
        $downloadUrl = $forFaculty
            ? route('faculty.pbt_export', ['semester' => $semester->sem_id])
            : ($forDean
                ? route('dean.pbt_export.download', ['faculty_id' => $faculty->fac_id])
                : route('chair.export_reports.pbt', [
                    'faculty_id' => $faculty->fac_id,
                    'semester' => $semester->sem_id,
                ]));

        return $this->previewResponse($request, [
            'kind' => 'PBT',
            'title' => 'Program by Teacher',
            'subtitle' => $facultyName . ' · ' . $semesterLabel,
            'semesterLabel' => $semesterLabel,
            'rows' => $rows,
            'rowsByShift' => $rowsByShift,
            'displayShifts' => $displayShifts,
            'coursesByShift' => $coursesByShift,
            'workloadByShift' => $workloadByShift,
            'downloadUrl' => $downloadUrl,
            'backUrl' => $forFaculty
                ? route('faculty.schedule', ['semester' => $semester->sem_id])
                : ($forDean
                    ? route('dean.pbt', ['faculty_id' => $faculty->fac_id, 'program_id' => $faculty->fac_dept_id])
                    : route('chair.export_reports', ['semester' => $semester->sem_id])),
            'studentCount' => null,
            'sectionName' => null,
            'studentCountEditable' => false,
        ]);
    }
    public function pbs(Request $request)
    {
        $forDean = str_starts_with((string) $request->route()?->getName(), 'dean.');
        $dean = $forDean ? $this->dean() : null;
        $chair = $forDean ? null : $this->chair();
        $data = $request->validate([
            'section' => ['required', 'uuid', 'exists:section,sec_id'],
            'semester' => ['required', 'uuid', 'exists:semester,sem_id'],
            'shift' => ['nullable', 'in:day,night,evening'],
            'student_count' => ['nullable', 'integer', 'min:0', 'max:500'],
        ]);

        $collegeId = $dean?->dean_college_id ?? $chair?->program?->dept_college_id ?? $chair?->dc_college_id;
        $allowedDepartmentIds = $collegeId
            ? Departments::where('dept_college_id', $collegeId)->pluck('dept_id')
            : collect([$chair->dc_dept_id])->filter()->values();
        $section = Section::with('program')
            ->whereIn('sec_dept_id', $allowedDepartmentIds)
            ->where('sec_id', $data['section'])
            ->firstOrFail();
        $semester = Semester::with('academicYear')->findOrFail($data['semester']);

        $shift = in_array($data['shift'] ?? '', ['night', 'evening'], true)
            ? 'night'
            : (($data['shift'] ?? null) === 'day' ? 'day' : ((bool) $section->sec_is_evening ? 'night' : 'day'));
        $isEvening = $shift === 'night';

        $allSchedules = Schedule::with(['subject', 'faculty', 'room', 'section.program'])
            ->where('sch_sec_id', $section->sec_id)
            ->where('sch_sem_id', $semester->sem_id)
            ->where('sch_is_active', true)
            ->when($forDean, fn($query) => $query->whereHas(
                'section.program',
                fn($section) => $section->where('dept_college_id', $collegeId)
            ))
            ->orderBy('sch_day')
            ->orderBy('sch_start_time')
            ->get();

        $shiftSchedules = $forDean
            ? $allSchedules->values()
            : $allSchedules
            ->filter(fn($schedule) => $this->scheduleOverlapsShift($schedule, $isEvening))
            ->values();

        $rows = $shiftSchedules
            ->map(fn($schedule) => [
                'section' => $section->sec_name,
                'faculty' => FacultyNameFormatter::format($schedule->faculty) ?: '—',
                'course_code' => $schedule->subject?->course_code ?? '—',
                'course_name' => $schedule->subject?->course_name ?? '—',
                'mis_code' => $schedule->sch_mis_code ?? '',
                'day' => $schedule->sch_day ?? '—',
                'start_time' => $schedule->sch_start_time,
                'end_time' => $schedule->sch_end_time,
                'room' => $schedule->room?->room_name ?? '—',
                'shift' => $forDean ? $this->scheduleShiftKey($schedule) : ($isEvening ? 'evening' : 'day'),
            ])->values();
        $activeShiftKey = $isEvening ? 'evening' : 'day';
        $rowsByShift = $this->groupRowsByShift($rows);
        $displayShifts = $forDean
            ? ['day' => 'Day', 'evening' => 'Evening']
            : [$activeShiftKey => ucfirst($activeShiftKey) . ' Program'];

        abort_if($rows->isEmpty(), 422, 'There are no active plotted classes for this section and semester.');

        $coursesByShift = $this->courseSummaryByShift($shiftSchedules, $forDean ? null : $activeShiftKey);

        $semesterLabel = $this->semesterLabel($semester);
        $studentCount = $data['student_count'] ?? $section->sec_no_of_student ?? 0;

        return $this->previewResponse($request, [
            'kind' => 'PBS',
            'title' => 'Program by Section',
            'subtitle' => $section->sec_name . ' · ' . ($isEvening ? 'Evening' : 'Day') . ' · ' . $semesterLabel,
            'semesterLabel' => $semesterLabel,
            'rows' => $rows,
            'rowsByShift' => $rowsByShift,
            'displayShifts' => $displayShifts,
            'coursesByShift' => $coursesByShift,
            'downloadUrl' => $forDean
                ? route('dean.pbs.export', ['section_id' => $section->sec_id])
                : route('chair.pbs.export', [
                    'section' => $section->sec_id,
                    'semester' => $semester->sem_id,
                    'shift' => $shift,
                ]),
            'backUrl' => $forDean
                ? route('dean.pbs', ['program_id' => $section->sec_dept_id, 'section_id' => $section->sec_id, 'shift' => $shift])
                : route('chair.pbs', [
                    'department' => $chair->dc_dept_id,
                    'section' => $section->sec_id,
                    'semester' => $semester->sem_id,
                    'shift' => $shift,
                ]),
            'studentCount' => (int) $studentCount,
            'sectionName' => $section->sec_name,
            'studentCountEditable' => !$forDean,
        ]);
    }

    public function mis(Request $request)
    {
        $page = app(MisController::class)->index($request);
        $data = $page->getData();
        $groupedRows = $data['groupedRows'] ?? collect();
        $rows = collect();

        foreach ($groupedRows as $sectionName => $schedules) {
            foreach ($schedules as $schedule) {
                $rows->push([
                    'section' => $sectionName,
                    'faculty' => FacultyNameFormatter::format($schedule->faculty) ?: '—',
                    'course_code' => $schedule->subject?->course_code ?? '—',
                    'course_name' => $schedule->subject?->course_name ?? '—',
                    'mis_code' => $schedule->sch_mis_code ?? '—',
                    'day' => $schedule->sch_day ?? '—',
                    'start_time' => $schedule->sch_start_time,
                    'end_time' => $schedule->sch_end_time,
                    'room' => $schedule->room?->room_name ?? '—',
                    'shift' => $this->scheduleShiftKey($schedule),
                ]);
            }
        }

        $semester = $data['activeSemester'] ?? null;
        $semesterLabel = $semester?->label ?? 'Active semester';
        $filters = $data['filters'] ?? ['section' => null, 'shift' => 'day'];
        $activeShiftKey = in_array(($filters['shift'] ?? 'day'), ['night', 'evening'], true) ? 'evening' : 'day';
        $rowsByShift = $this->groupRowsByShift($rows->values());
        $displayShifts = [$activeShiftKey => ucfirst($activeShiftKey) . ' Program'];

        return $this->previewResponse($request, [
            'kind' => 'MIS',
            'title' => 'MIS Class Program',
            'subtitle' => $semesterLabel . ' · ' . ucfirst(($filters['shift'] ?? 'day') === 'night' ? 'evening' : ($filters['shift'] ?? 'day')),
            'semesterLabel' => $semesterLabel,
            'rows' => $rows->values(),
            'rowsByShift' => $rowsByShift,
            'displayShifts' => $displayShifts,
            'coursesByShift' => ['day' => collect(), 'evening' => collect()],
            'downloadUrl' => route('chair.mis.word', $request->query()),
            'backUrl' => route('chair.mis', $request->query()),
            'studentCount' => null,
            'sectionName' => null,
        ]);
    }

    private function previewResponse(Request $request, array $data)
    {
        $view = $request->ajax()
            ? 'chair.export_preview_modal'
            : 'chair.export_preview';

        return view($view, $data);
    }

    private function chair(): Dept_Chair
    {
        $chair = Dept_Chair::with('program')
            ->where('dc_usr_id', Auth::id())
            ->first();

        abort_if(!$chair || !$chair->dc_dept_id, 403, 'Your account is not assigned to a program department.');

        return $chair;
    }

    private function dean(): Dean
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->first();
        abort_if(!$dean || !$dean->dean_college_id, 403, 'Your account is not assigned to a college.');

        return $dean;
    }

    private function semesterLabel(Semester $semester): string
    {
        $year = $semester->academicYear?->ay_year_label
            ?? $semester->academicYear?->ay_academic_year
            ?? '';
        $year = $year !== '' ? ' · AY ' . str_replace('-', ' - ', $year) : '';

        return trim($semester->sem_name . $year);
    }

    private function scheduleOverlapsShift(Schedule $schedule, bool $evening): bool
    {
        try {
            $start = Carbon::parse($schedule->sch_start_time);
            $end = Carbon::parse($schedule->sch_end_time);
            $startMinutes = $start->hour * 60 + $start->minute;
            $endMinutes = $end->hour * 60 + $end->minute;
            $day = strtoupper(substr(trim((string) $schedule->sch_day), 0, 3));

            if ($endMinutes <= $startMinutes) {
                return false;
            }

            if ($evening) {
                // Evening template weekends run from 7 AM to 10 PM.
                if (in_array($day, ['SAT', 'SUN'], true)) {
                    return $startMinutes >= 7 * 60 && $endMinutes <= 22 * 60;
                }

                return in_array($day, ['MON', 'TUE', 'WED', 'THU', 'FRI'], true)
                    && $startMinutes >= 16 * 60 && $endMinutes <= 22 * 60;
            }

            if (!in_array($day, ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT'], true)) {
                return false;
            }

            // Day template has a noon break and ends at 5 PM.
            return ($startMinutes >= 7 * 60 && $endMinutes <= 12 * 60)
                || ($startMinutes >= 13 * 60 && $endMinutes <= 17 * 60);
        } catch (\Throwable $exception) {
            return false;
        }
    }

    private function durationHours(Schedule $schedule): float
    {
        try {
            return Carbon::parse($schedule->sch_start_time)
                ->diffInMinutes(Carbon::parse($schedule->sch_end_time)) / 60;
        } catch (\Throwable $exception) {
            return 0;
        }
    }

    private function courseSummaryByShift($schedules, ?string $shiftOverride = null, bool $useSectionType = false): array
    {
        $shiftForSchedule = fn(Schedule $schedule): string => $shiftOverride
            ?? ($useSectionType ? $this->sectionShiftKey($schedule) : $this->scheduleShiftKey($schedule));

        $rows = $schedules
            ->filter(fn($schedule) => $schedule->subject && $schedule->section)
            ->groupBy(function ($schedule) use ($shiftForSchedule) {
                $shift = $shiftForSchedule($schedule);
                $courseId = $schedule->subject->course_id ?? $schedule->sch_course_id ?? '';
                $sectionId = $schedule->section->sec_id ?? '';

                return $shift . '|' . $courseId . '|' . $sectionId;
            })
            ->map(function ($meetings) use ($shiftForSchedule) {
                $schedule = $meetings->first();
                $course = $schedule->subject;
                $section = $schedule->section;

                return [
                    'shift' => $shiftForSchedule($schedule),
                    'course_code' => $course->course_code ?? '—',
                    'course_name' => $course->course_name ?? '—',
                    'degree_section' => $section->sec_name ?? '—',
                    'student_count' => (int) ($section->sec_no_of_student ?? 0),
                ];
            })
            ->sortBy(fn($row) => ($row['course_code'] ?? '') . '|' . ($row['degree_section'] ?? ''))
            ->values();

        return [
            'day' => $rows->where('shift', 'day')->values(),
            'evening' => $rows->where('shift', 'evening')->values(),
        ];
    }

    /** Keep preview totals consistent with the values written to the PBT workbook. */
    private function pbtWorkloadSummary(Faculty $faculty, $allSchedules, string $shift): array
    {
        $shiftSchedules = $allSchedules->filter(fn(Schedule $schedule) => $this->pbtScheduleMatchesShift($schedule, $shift));

        // The workbook also includes an OJT study load in its selected shift,
        // even when its shared schedule is plotted in the other shift.
        $ojtScheduleIds = $allSchedules
            ->filter(fn(Schedule $schedule) => ScheduleAssignmentService::isOjt($schedule->subject)
                && (($schedule->studyLoad?->sl_ojt_is_evening ? 'evening' : 'day') === $shift))
            ->pluck('sch_id');
        $scheduleIds = $shiftSchedules->pluck('sch_id')->merge($ojtScheduleIds)->filter()->unique()->all();

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

        $countedOjtUnits = [];
        $units = $studyLoads->sum(function ($load) use (&$countedOjtUnits) {
            $course = $load->subject;
            if (ScheduleAssignmentService::isOjt($course)) {
                $courseId = (string) $course->course_id;
                if (isset($countedOjtUnits[$courseId])) return 0;
                $countedOjtUnits[$courseId] = true;
            }
            return ScheduleAssignmentService::courseUnits($course);
        });

        $countedOjtHours = [];
        $hours = $studyLoads->sum(function ($load) use (&$countedOjtHours) {
            $course = $load->subject;
            if (ScheduleAssignmentService::isOjt($course)) {
                $courseId = (string) $course->course_id;
                if (isset($countedOjtHours[$courseId])) return 0;
                $countedOjtHours[$courseId] = true;
            }
            return ScheduleAssignmentService::courseHours($course);
        });

        return [
            'preparations' => $preparations,
            'units' => $this->formatWorkloadNumber($units),
            'hours' => $this->formatWorkloadNumber($hours),
        ];
    }

    private function pbtScheduleMatchesShift(Schedule $schedule, string $shift): bool
    {
        if ($shift === 'day') {
            if (($schedule->section?->sec_is_evening ?? false) === true) return false;
            $day = strtolower((string) $schedule->sch_day);
            $start = $this->timeInMinutes($schedule->sch_start_time);
            $end = $this->timeInMinutes($schedule->sch_end_time);

            return in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], true)
                && $start >= 7 * 60
                && $end <= 17 * 60
                && !($start < 13 * 60 && $end > 12 * 60);
        }

        if (($schedule->section?->sec_is_evening ?? false) === true) return true;
        $day = strtolower((string) $schedule->sch_day);
        $start = $this->timeInMinutes($schedule->sch_start_time);
        $end = $this->timeInMinutes($schedule->sch_end_time);

        if (in_array($day, ['monday', 'tuesday', 'wednesday', 'thursday', 'friday'], true)) {
            return $start >= 16 * 60 && $end <= 22 * 60;
        }

        return in_array($day, ['saturday', 'sunday'], true) && $start >= 7 * 60 && $end <= 22 * 60;
    }

    private function timeInMinutes($time): int
    {
        try {
            $parsed = Carbon::parse($time);
            return $parsed->hour * 60 + $parsed->minute;
        } catch (\Throwable $exception) {
            return -1;
        }
    }

    private function formatWorkloadNumber(float|int $value): int|float
    {
        return floor($value) === $value ? (int) $value : round($value, 2);
    }

    private function sectionShiftKey(Schedule $schedule): string
    {
        return (bool) $schedule->section?->sec_is_evening ? 'evening' : 'day';
    }

    private function scheduleShiftKey(Schedule $schedule): string
    {
        try {
            $start = Carbon::parse($schedule->sch_start_time);
            $startMinutes = $start->hour * 60 + $start->minute;

            return $startMinutes >= 16 * 60 ? 'evening' : 'day';
        } catch (\Throwable $exception) {
            $storedShift = strtolower(trim((string) ($schedule->sch_shift ?? '')));
            if (in_array($storedShift, ['evening', 'night'], true)) {
                return 'evening';
            }

            if ($storedShift === 'day') {
                return 'day';
            }

            return (bool) $schedule->section?->sec_is_evening ? 'evening' : 'day';
        }
    }

    private function groupRowsByShift($rows): array
    {
        return [
            'day' => $rows->where('shift', 'day')->values(),
            'evening' => $rows->where('shift', 'evening')->values(),
        ];
    }
}
