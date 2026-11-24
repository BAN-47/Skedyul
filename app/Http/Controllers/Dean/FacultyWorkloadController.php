<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Departments;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Services\AuditActivityLogger;
use App\Models\Designation;
use App\Services\ScheduleAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class FacultyWorkloadController extends Controller
{
    public function updateDesignation(Request $request, string $facultyId)
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->firstOrFail();
        abort_unless($dean->dean_college_id, 403, 'Your account is not assigned to a college.');
        $departmentIds = Departments::where('dept_college_id', $dean->dean_college_id)->pluck('dept_id');
        $faculty = Faculty::where('fac_id', $facultyId)
            ->whereIn('fac_dept_id', $departmentIds)
            ->where('fac_is_placeholder', false)
            ->firstOrFail();

        $data = $request->validate([
            'special_position' => ['nullable', 'string', Rule::in(Designation::pluck('name')->toArray())],
        ]);
        $position = $data['special_position'] ?? null;
        if ($position && Designation::isLimitReached($position, $faculty->fac_id)) {
            return back()->withErrors(['special_position' => "The designation '{$position}' has reached its maximum faculty limit."]);
        }
        $cap = $position ? Designation::where('name', $position)->value('day_cap') : null;
        $faculty->update([
            'fac_special_position' => $position,
            'fac_special_position_max_hours' => $cap,
        ]);

        AuditActivityLogger::record(
            $request->user(),
            'Updated faculty designation',
            'faculty',
            ($position ? 'Set ' . $faculty->full_name . ' as ' . $position . ' (' . $cap . '-hour Day cap).' : 'Removed the special designation from ' . $faculty->full_name . '.') ,
            $request->ip(),
            $faculty->fac_id
        );

        return back()->with('success', $position
            ? $faculty->full_name . ' is now designated as ' . $position . '.'
            : 'Special designation removed from ' . $faculty->full_name . '.');
    }

    public function facultyWorkload(Request $request)
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->firstOrFail();
        abort_unless($dean->dean_college_id, 403, 'Your account is not assigned to a college.');

        $data = $request->validate([
            'semester' => ['nullable', 'uuid', 'exists:semester,sem_id'],
            'department' => ['nullable', 'string', 'max:80'],
            'employment' => ['nullable', 'in:all,full_time,part_time'],
            'status' => ['nullable', 'in:all,attention,available'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $departments = Departments::where('dept_college_id', $dean->dean_college_id)
            ->orderBy('dept_code')->get();
        $departmentIds = $departments->pluck('dept_id');
        $selectedDepartment = $data['department'] ?? 'all';
        abort_if($selectedDepartment !== 'all' && !$departmentIds->contains($selectedDepartment), 404);

        $semesters = Semester::with('academicYear')->orderByDesc('sem_start_date')->get();
        $semester = !empty($data['semester'])
            ? $semesters->firstWhere('sem_id', $data['semester'])
            : $semesters->firstWhere('sem_is_active', true);
        $semester ??= $semesters->first();

        $sectionIds = \App\Models\Section::whereIn('sec_dept_id', $departmentIds)->pluck('sec_id');
        $loadsByFaculty = $semester
            ? Study_Load::with(['subject', 'section', 'schedules'])
                ->where('sl_sem_id', $semester->sem_id)
                ->whereIn('sl_sec_id', $sectionIds)
                ->get()->groupBy('sl_fac_id')
            : collect();

        // Include home faculty in this college and any outside faculty teaching
        // one of its sections, so the Dean can see the full teaching picture.
        $facultyIdsWithLoads = $loadsByFaculty->keys();
        $faculty = Faculty::with(['user', 'program'])
            ->where('fac_is_placeholder', false)
            ->where(function ($query) use ($departmentIds, $facultyIdsWithLoads) {
                $query->whereIn('fac_dept_id', $departmentIds);
                if ($facultyIdsWithLoads->isNotEmpty()) {
                    $query->orWhereIn('fac_id', $facultyIdsWithLoads);
                }
            })
            ->orderBy('fac_last_name')->orderBy('fac_first_name')->get();

        $rows = $faculty->map(function (Faculty $member) use ($loadsByFaculty) {
            $loads = $loadsByFaculty->get($member->fac_id, collect());
            $isEvening = fn ($load) => (bool) $load->section?->sec_is_evening
                || strtolower((string) ($load->sl_shift ?? 'day')) === 'evening';
            $dayLoads = $loads->filter(fn ($load) => !$isEvening($load));
            $eveningLoads = $loads->filter($isEvening);
            $dayHours = ScheduleAssignmentService::scheduledHoursForLoads($dayLoads);
            $eveningHours = ScheduleAssignmentService::scheduledHoursForLoads($eveningLoads);
            $totalHours = $dayHours + $eveningHours;
            $isPartTime = $member->fac_employment_type === 'part_time';
            $dayCap = ScheduleAssignmentService::facultyMaxHours($member, 'day');
            $eveningCap = ScheduleAssignmentService::facultyMaxHours($member, 'evening');
            $totalCap = $isPartTime
                ? ScheduleAssignmentService::PART_TIME_MAX_HOURS
                : $dayCap + $eveningCap;
            $hoursAgainstCap = $isPartTime ? $totalHours : null;
            $attention = $isPartTime
                ? $totalHours >= $totalCap - 3
                : ($dayHours >= $dayCap - 3 || $eveningHours >= $eveningCap - 3);
            $overLimit = $isPartTime
                ? $totalHours > $totalCap
                : ($dayHours > $dayCap || $eveningHours > $eveningCap);
            $status = $overLimit ? 'Over Limit' : ($attention ? 'Near Limit' : ($totalHours > 0 ? 'Within Limit' : 'No Load'));
            $progress = $isPartTime
                ? ($totalCap > 0 ? min(100, (int) round($totalHours / $totalCap * 100)) : 0)
                : (int) round(max(
                    $dayCap > 0 ? $dayHours / $dayCap : 0,
                    $eveningCap > 0 ? $eveningHours / $eveningCap : 0
                ) * 100);
            $codes = $loads->map(fn ($load) => $load->subject?->course_code)->filter()->unique()->implode(', ');

            return [
                'id' => $member->fac_id,
                'name' => $member->full_name,
                'email' => $member->user?->usr_email,
                'photo' => $member->fac_profile_image,
                'rank' => $member->fac_rank,
                'position' => $member->fac_special_position,
                'department' => $member->program?->dept_code ?? 'Other department',
                'department_id' => $member->fac_dept_id,
                'employment' => $member->fac_employment_type ?? 'full_time',
                'active' => (bool) ($member->user?->usr_is_active ?? false),
                'subjects' => $codes ?: '—',
                'day_hours' => $dayHours,
                'day_cap' => $dayCap,
                'evening_hours' => $eveningHours,
                'evening_cap' => $eveningCap,
                'total_hours' => $totalHours,
                'total_cap' => $totalCap,
                'status' => $status,
                'status_color' => $overLimit ? 'rose' : ($attention ? 'amber' : ($totalHours > 0 ? 'emerald' : 'slate')),
                'progress' => min(100, $progress),
                'search' => strtolower(implode(' ', array_filter([
                    $member->full_name, $member->user?->usr_email, $member->fac_rank,
                    $member->fac_special_position, $member->program?->dept_code, $codes,
                ]))),
            ];
        });

        $employmentFilter = $data['employment'] ?? 'all';
        $statusFilter = $data['status'] ?? 'all';
        $search = trim($data['search'] ?? '');
        $filtered = $rows->filter(function (array $row) use ($selectedDepartment, $employmentFilter, $statusFilter, $search) {
            if ($selectedDepartment !== 'all' && $row['department_id'] !== $selectedDepartment) return false;
            if ($employmentFilter !== 'all' && $row['employment'] !== $employmentFilter) return false;
            if ($statusFilter === 'attention' && !in_array($row['status'], ['Near Limit', 'Over Limit'], true)) return false;
            if ($statusFilter === 'available' && $row['status'] !== 'Within Limit') return false;
            return $search === '' || str_contains($row['search'], strtolower($search));
        })->values();

        $stats = [
            'faculty' => $rows->count(),
            'total_hours' => $rows->sum('total_hours'),
            'attention' => $rows->whereIn('status', ['Near Limit', 'Over Limit'])->count(),
            'over_limit' => $rows->where('status', 'Over Limit')->count(),
        ];
        $page = LengthAwarePaginator::resolveCurrentPage();
        $facultyRows = new LengthAwarePaginator(
            $filtered->forPage($page, 15)->values(), $filtered->count(), 15, $page,
            ['path' => LengthAwarePaginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        return view('dean.faculty_workload', compact(
            'dean', 'departments', 'selectedDepartment', 'semesters', 'semester',
            'facultyRows', 'stats', 'search', 'employmentFilter', 'statusFilter'
        ));
    }
}
