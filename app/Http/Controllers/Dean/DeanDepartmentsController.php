<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Workload;
use App\Models\Section;
use App\Models\Dept_Chair;
use App\Models\Schedule_Submission;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\User;

class DeanDepartmentController extends Controller
{
    // Keep these in sync with DeanFacultyWorkloadController / DeanDashboardController
    const MAX_LOAD_HOURS     = 30;
    const NEAR_MAX_THRESHOLD = 27;
    const OK_THRESHOLD       = 20;

    // Cycles through brand colors per department card, in department order.
    // Add more if you add more departments.
    const DEPT_COLORS = ['blue', 'purple', 'teal', 'amber', 'red', 'green'];

    public function index()
    {
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $departments = Department::orderBy('dept_code')->get();

        // Latest workload (total hours) per faculty for the active semester
        $workloads = Workload::when($semester, fn ($q) => $q->where('wl_sem_id', $semester->sem_id))
            ->when($academicYear, fn ($q) => $q->where('wl_ay_id', $academicYear->ay_id))
            ->get()
            ->keyBy('wl_fac_id');

        $deptData = [];

        foreach ($departments as $index => $dept) {
            $facultyList = Faculty::where('fac_dept_id', $dept->dept_id)->get();

            $facultyRows = $facultyList->map(function ($f) use ($workloads) {
                $hours = optional($workloads->get($f->fac_id))->wl_total_hours ?? 0;
                [$statusLabel, $statusColor] = $this->resolveStatus($hours, $f->fac_employment_type);

                return [
                    'name'       => $f->full_name ?: 'Unnamed Faculty',
                    'rank'       => $f->fac_rank ?? '—',
                    'employment' => $this->formatEmployment($f->fac_employment_type),
                    'load'       => $hours . 'h',
                    'status'     => $statusLabel,
                    'badge'      => 'badge-' . $statusColor,
                ];
            })->values();

            $facultyCount = $facultyList->count();
            $avgLoad = $facultyCount > 0
                ? round($facultyList->sum(fn ($f) => optional($workloads->get($f->fac_id))->wl_total_hours ?? 0) / $facultyCount)
                : 0;

            $loadPct = min(100, round(($avgLoad / self::MAX_LOAD_HOURS) * 100));

            $sectionCount = Section::whereHas('program', fn ($q) => $q->where('prog_dept_id', $dept->dept_id))
                ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
                ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
                ->count();

            $chairName = $this->resolveChairName($dept->dept_id);

            $submission = Schedule_Submission::where('schsub_dept_id', $dept->dept_id)
                ->when($semester, fn ($q) => $q->where('schsub_sem_id', $semester->sem_id))
                ->orderByDesc('schsub_submitted_at')
                ->first();

            [$scheduleStatus, $statusBadge] = $this->resolveScheduleStatus($submission);

            $deptData[$dept->dept_id] = [
                'code'           => $dept->dept_code,
                'color'          => 'var(--' . self::DEPT_COLORS[$index % count(self::DEPT_COLORS)] . ')',
                'name'           => $dept->dept_name,
                'chair'          => $chairName,
                'sections'       => $sectionCount,
                'avgLoad'        => $avgLoad . 'h',
                'maxLoad'        => self::MAX_LOAD_HOURS . 'h',
                'loadPct'        => $loadPct,
                'loadColor'      => $this->resolveLoadColor($avgLoad),
                'scheduleStatus' => $scheduleStatus,
                'statusBadge'    => $statusBadge,
                'faculty'        => $facultyRows,
            ];
        }

        return view('dean.departments', compact('deptData', 'academicYear', 'semester'));
    }

    /**
     * Resolves the department chair's display name via department_chair -> users.
     * Falls back to '—' if no chair record exists for this department.
     */
    private function resolveChairName(string $deptId): string
    {
        $chairRecord = Dept_Chair::where('dc_dept_id', $deptId)->first();
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
     */
    private function resolveLoadColor(float $avgLoad): string
    {
        if ($avgLoad > self::MAX_LOAD_HOURS) {
            return 'var(--red)';
        }
        if ($avgLoad >= self::NEAR_MAX_THRESHOLD) {
            return 'var(--amber)';
        }
        return 'var(--blue)';
    }

    /**
     * Determines the badge label + color for a faculty member,
     * mirroring DeanFacultyWorkloadController's business rule.
     */
    private function resolveStatus(float $hours, ?string $employmentType): array
    {
        if ($hours > self::MAX_LOAD_HOURS) {
            return ['Overload', 'red'];
        }
        if ($hours >= self::NEAR_MAX_THRESHOLD) {
            return ['Near Max', 'amber'];
        }
        if ($hours >= self::OK_THRESHOLD) {
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