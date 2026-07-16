<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Workload;
use App\Models\Study_Load;
use App\Models\AcademicYear;
use App\Models\Semester;

class DeanFacultyWorkloadController extends Controller
{
    // Same hardcoded max as DeanDashboardController — keep these two in sync
    // if your team ever moves this to a config value.
    const MAX_LOAD_HOURS = 30;
    const NEAR_MAX_THRESHOLD = 27;
    const OK_THRESHOLD = 20;

    public function index()
    {
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        $faculty = Faculty::with('department')->get();

        // Latest workload (total hours) per faculty for the active semester
        $workloads = Workload::when($semester, fn ($q) => $q->where('wl_sem_id', $semester->sem_id))
            ->when($academicYear, fn ($q) => $q->where('wl_ay_id', $academicYear->ay_id))
            ->get()
            ->keyBy('wl_fac_id');

        // Subject codes assigned to each faculty this semester, for the "Subjects" column
        $studyLoads = Study_Load::with('subject')
            ->when($semester, fn ($q) => $q->where('sl_sem_id', $semester->sem_id))
            ->get()
            ->groupBy('sl_fac_id');

        $facultyRows = $faculty->map(function ($f) use ($workloads, $studyLoads) {
            $hours = optional($workloads->get($f->fac_id))->wl_total_hours ?? 0;

            $subjectCodes = optional($studyLoads->get($f->fac_id))
                ->pluck('subject.subj_code')
                ->filter()
                ->implode(', ');

            [$statusLabel, $statusColor] = $this->resolveStatus($hours, $f->fac_employment_type);

            return [
                'name'        => $f->full_name ?: 'Unnamed Faculty',
                'department'  => optional($f->department)->dept_name ?? '—',
                'employment'  => $this->formatEmployment($f->fac_employment_type),
                'subjects'    => $subjectCodes ?: '—',
                'hours'       => $hours,
                'percent'     => min(100, round(($hours / self::MAX_LOAD_HOURS) * 100)),
                'status'      => $statusLabel,
                'statusColor' => $statusColor,
            ];
        })->values();

        // ---------- STAT CARDS ----------
        $totalFaculty  = $facultyRows->count();
        $okCount       = $facultyRows->where('status', 'OK')->count();
        $nearMaxCount  = $facultyRows->where('status', 'Near Max')->count();
        $overloadCount = $facultyRows->where('status', 'Overload')->count();

        // ---------- OVERLOAD ALERT BANNER ----------
        $overloadedFaculty = $facultyRows->where('status', 'Overload')->values();

        return view('dean.faculty_workload', compact(
            'facultyRows', 'totalFaculty', 'okCount', 'nearMaxCount', 'overloadCount',
            'overloadedFaculty', 'academicYear', 'semester'
        ));
    }

    /**
     * Determines the badge label + color for a faculty member,
     * matching the business rule where low-hour part-time faculty
     * show "Part-time" instead of "Available".
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

        // Below OK_THRESHOLD: part-time faculty get labeled by employment type
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