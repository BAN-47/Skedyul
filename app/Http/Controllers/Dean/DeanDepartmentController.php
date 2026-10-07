<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\College;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Study_Load;
use App\Models\Dept_Chair;
use App\Models\Dean;
use App\Models\Schedule_Submission;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DeanDepartmentController extends Controller
{
    // Keep these in sync with DeanFacultyWorkloadController / DeanDashboardController
    const FULL_TIME_MAX_HOURS = 30;
    const PART_TIME_MAX_HOURS = 22;
    const NEAR_MAX_BUFFER     = 3;
    const OK_THRESHOLD       = 20;

    // Cycles through brand colors per department card, in department order.
    const DEPT_COLORS = ['blue', 'purple', 'teal', 'amber', 'red', 'green'];

    public function index()
    {
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // Each Dean is assigned to exactly one Department via department_dean.
        // Only show that department here — not the whole university.
        $deanAssignment = Dean::where('dean_usr_id', Auth::id())->first();

        $departments = $deanAssignment
            ? College::where('college_id', $deanAssignment->dean_college_id)->get()
            : collect(); // Dean has no department assigned yet — show nothing rather than everything

        // Calculate from course assignments so legacy workload rows that
        // stored academic units are not shown as teaching hours.
        $loadsByFaculty = Study_Load::with('subject')
            ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');
        $hoursByFaculty = $loadsByFaculty->map(fn ($loads) => $loads->sum(
            fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject)
        ));

        $deptData = [];

        foreach ($departments as $index => $dept) {
            $facultyList = Faculty::where('fac_college_id', $dept->college_id)->get();

            $facultyCount = $facultyList->count();

            // Only faculty with a course assignment count toward the average.
            $assignedFaculty = $facultyList->filter(fn($f) => $loadsByFaculty->has($f->fac_id));
            $avgLoad = $assignedFaculty->count() > 0
                ? round($assignedFaculty->sum(fn($f) => $hoursByFaculty->get($f->fac_id, 0)) / $assignedFaculty->count())
                : 0;

            $loadPct = min(100, round(($avgLoad / self::FULL_TIME_MAX_HOURS) * 100));

            // ---------- PROGRAM BREAKDOWN ----------
            // A faculty member "belongs" to whichever program(s) they're
            // currently assigned subjects for this semester (via Study_Load),
            // since Faculty itself is only tagged at the Department level.
            $programs = Departments::where('dept_college_id', $dept->college_id)->orderBy('dept_code')->get();

            $programsData = [];

            foreach ($programs as $progIndex => $prog) {
                $facultyIds = Study_Load::whereHas('subject', fn($q) => $q->where('course_dept_id', $prog->dept_id))
                    ->when($semester, fn($q) => $q->where('sl_sem_id', $semester->sem_id))
                    ->pluck('sl_fac_id')
                    ->unique();

                // Faculty statically assigned to this program (fac_prog_id), even
                // with no study_load record yet this semester
                $staticFacultyIds = Faculty::where('fac_dept_id', $prog->dept_id)->pluck('fac_id');

                $facultyIds = $facultyIds->merge($staticFacultyIds)->unique();

                $progFaculty = Faculty::whereIn('fac_id', $facultyIds)->get();

                $progFacultyRows = $progFaculty->map(function ($f) use ($loadsByFaculty, $hoursByFaculty) {
                    $hasWorkload = $loadsByFaculty->has($f->fac_id);
                    $hours = $hasWorkload ? $hoursByFaculty->get($f->fac_id, 0) : 0;
                    [$statusLabel, $statusColor] = $this->resolveStatus($hours, $f->fac_employment_type, $hasWorkload);

                    return [
                        'name'       => $f->full_name ?: 'Unnamed Faculty',
                        'rank'       => $f->fac_rank ?? '—',
                        'employment' => $this->formatEmployment($f->fac_employment_type),
                        'load'       => $hasWorkload ? $hours . 'h' : '—',
                        'status'     => $statusLabel,
                        'badge'      => 'badge-' . $statusColor,
                    ];
                })->values();

                $progFacultyCount = $progFaculty->count();

                $progAssignedFaculty = $progFaculty->filter(fn($f) => $loadsByFaculty->has($f->fac_id));
                $progAvgLoad = $progAssignedFaculty->count() > 0
                    ? round($progAssignedFaculty->sum(fn($f) => $hoursByFaculty->get($f->fac_id, 0)) / $progAssignedFaculty->count())
                    : 0;
                $progLoadPct = min(100, round(($progAvgLoad / self::FULL_TIME_MAX_HOURS) * 100));

                $progSectionCount = Section::where('sec_dept_id', $prog->dept_id)
                    ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
                    ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
                    ->count();

                $programsData[$prog->prog_id] = [
                    'code'         => $prog->prog_code,
                    'name'         => $prog->prog_name,
                    'color'        => 'var(--' . self::DEPT_COLORS[$progIndex % count(self::DEPT_COLORS)] . ')',
                    'chair'        => $this->resolveProgramChairName($prog->prog_id),
                    'facultyCount' => $progFacultyCount,
                    'sections'     => $progSectionCount,
                    'avgLoad'      => $progAssignedFaculty->count() > 0 ? $progAvgLoad . 'h' : '—',
                    'maxLoad'      => 'FT ' . self::FULL_TIME_MAX_HOURS . 'h / PT ' . self::PART_TIME_MAX_HOURS . 'h',
                    'loadPct'      => $progLoadPct,
                    'loadColor'    => $this->resolveLoadColor($progAvgLoad),
                    'faculty'      => $progFacultyRows,
                ];
            }

            $sectionCount = Section::whereHas('program', fn($q) => $q->where('dept_college_id', $dept->college_id))
                ->when($semester, fn($q) => $q->where('sec_sem_id', $semester->sem_id))
                ->when($academicYear, fn($q) => $q->where('sec_ay_id', $academicYear->ay_id))
                ->count();

            $submission = Schedule_Submission::where('schsub_dept_id', $dept->college_id)
                ->when($semester, fn($q) => $q->where('schsub_sem_id', $semester->sem_id))
                ->orderByDesc('schsub_submitted_at')
                ->first();

            [$scheduleStatus, $statusBadge] = $this->resolveScheduleStatus($submission);

            $deptData[$dept->dept_id] = [
                'code'           => $dept->dept_code,
                'color'          => 'var(--' . self::DEPT_COLORS[$index % count(self::DEPT_COLORS)] . ')',
                'name'           => $dept->dept_name,
                'facultyCount'   => $facultyCount,
                'sections'       => $sectionCount,
                'avgLoad'        => $assignedFaculty->count() > 0 ? $avgLoad . 'h' : '—',
                'maxLoad'        => 'FT ' . self::FULL_TIME_MAX_HOURS . 'h / PT ' . self::PART_TIME_MAX_HOURS . 'h',
                'loadPct'        => $loadPct,
                'loadColor'      => $this->resolveLoadColor($avgLoad),
                'scheduleStatus' => $scheduleStatus,
                'statusBadge'    => $statusBadge,
                'programs'       => $programsData,
            ];
        }

        return view('dean.departments', compact('deptData', 'academicYear', 'semester'));
    }

    /**
     * Resolves a program's chair display name via department_chair -> users,
     * matching on dc_prog_id since chairs are now assigned per-program
     * (a department can have several programs, each with its own chair).
     */
    private function resolveProgramChairName(string $progId): string
    {
        $chairRecord = Dept_Chair::where('dc_dept_id', $progId)->first();
        if (!$chairRecord) {
            return '—';
        }

        $user = User::find($chairRecord->dc_usr_id);
        return $user->usr_name ?? '—';
    }

    /**
     * Maps the latest schedule_submission status to a display label + badge color.
     */
    private function resolveScheduleStatus(?Schedule_Submission $submission): array
    {
        if (!$submission) {
            return ['No Submission', 'grey'];
        }

        return match ($submission->schsub_status) {
            'submitted' => ['Submitted', 'green'],
            'pending'   => ['Pending Review', 'amber'],
            'returned'  => ['Returned', 'red'],
            default     => [ucfirst($submission->schsub_status), 'grey'],
        };
    }

    /**
     * Same OK/Near Max/Overload thresholds as individual faculty status,
     * applied to a department's average load for the summary bar color.
     * Untouched by the "not yet assigned" change — an avgLoad of 0 here
     * already reads as neutral/blue, which is fine for a summary bar.
     */
    private function resolveLoadColor(float $avgLoad): string
    {
        if ($avgLoad > self::FULL_TIME_MAX_HOURS) {
            return 'var(--red)';
        }
        if ($avgLoad >= self::FULL_TIME_MAX_HOURS - self::NEAR_MAX_BUFFER) {
            return 'var(--amber)';
        }
        return 'var(--blue)';
    }

    /**
     * Determines the badge label + color for a faculty member,
     * mirroring DeanFacultyWorkloadController's business rule.
     *
     * $hasWorkload = false means no Workload row exists for them this
     * semester at all — i.e. they haven't been given a schedule yet.
     * That's distinct from a genuine 0-hour workload record, so it's
     * checked FIRST and short-circuits the hour-based thresholds below.
     */
    private function resolveStatus(float $hours, ?string $employmentType, bool $hasWorkload = true): array
    {
        if (!$hasWorkload) {
            return ['Not Yet Assigned', 'grey'];
        }

        $maxHours = $employmentType === 'part_time'
            ? self::PART_TIME_MAX_HOURS
            : self::FULL_TIME_MAX_HOURS;
        if ($hours > $maxHours) {
            return ['Overload', 'red'];
        }
        if ($hours >= $maxHours - self::NEAR_MAX_BUFFER) {
            return ['Near Max', 'amber'];
        }
        if ($employmentType !== 'part_time' && $hours >= self::OK_THRESHOLD) {
            return ['OK', 'green'];
        }

        if ($employmentType === 'part_time') {
            return ['Part-time', 'teal'];
        }

        return ['Available', 'blue'];
    }

    /**
     * Converts raw enum values like "full_time" / "part_time"
     * into readable labels like "Full-time" / "Part-time".
     */
    private function formatEmployment(?string $type): string
    {
        return match ($type) {
            'full_time' => 'Full-time',
            'part_time' => 'Part-time',
            default     => $type ? ucfirst(str_replace('_', ' ', $type)) : '—',
        };
    }
}
