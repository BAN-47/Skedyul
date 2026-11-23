<?php

namespace App\Services;

use App\Models\Faculty;
use App\Models\Dept_Chair;
use App\Models\Departments;
use App\Models\Notification;
use App\Models\Schedule;
use App\Models\Semester;

/** Keeps the reserved Professor X faculty account out of real faculty lists. */
class UnassignedFacultyService
{
    public const FACULTY_ID = '01a1e95e-aa6f-79eb-ba98-48d3d1533d3f';
    public const LABEL = 'Professor X (Unassigned)';

    public function faculty(): ?Faculty
    {
        return Faculty::query()->where('fac_is_placeholder', true)->first();
    }

    public function isPlaceholder(?string $facultyId): bool
    {
        if ($facultyId === null) {
            return false;
        }

        return $facultyId === self::FACULTY_ID;
    }

    public function label(?Faculty $faculty): string
    {
        return $faculty?->fac_is_placeholder ? self::LABEL : FacultyNameFormatter::format($faculty);
    }

    public function syncDepartmentNotification(string $departmentId, string $semesterId): void
    {
        // The schedule stores the department that must take over Professor X's classes.
        // Use that ID together with the semester so only the right department's work is counted.
        $department = Departments::find($departmentId);
        $semester = Semester::find($semesterId);
        if (!$department || !$semester) {
            return;
        }

        $pendingCount = Schedule::query()
            ->where('sch_fac_id', self::FACULTY_ID)
            ->where('sch_fac_assignment_dept_id', $departmentId)
            ->where('sch_sem_id', $semesterId)
            ->where('sch_is_active', true)
            ->count();
        $title = $this->notificationTitle($department, $semester);
        $chairUserIds = Dept_Chair::query()
            // Find the active chair account(s) linked to this department ID.
            ->where('dc_dept_id', $departmentId)
            ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
            ->pluck('dc_usr_id')
            ->filter();

        foreach ($chairUserIds as $userId) {
            $notificationQuery = Notification::query()
                ->where('notif_usr_id', $userId)
                ->where('notif_type', 'unassigned_faculty')
                ->where('notif_title', $title);

            if ($pendingCount === 0) {
                (clone $notificationQuery)->where('notif_is_read', false)
                    ->update(['notif_is_read' => true]);
                continue;
            }

            $message = $this->notificationMessage($department, $semester, $pendingCount);
            $unreadNotification = (clone $notificationQuery)
                ->where('notif_is_read', false)
                ->orderByDesc('notif_created_at')
                ->first();

            if ($unreadNotification) {
                $unreadNotification->update(['notif_message' => $message]);
            } else {
                Notification::create([
                    'notif_usr_id' => $userId,
                    'notif_title' => $title,
                    'notif_message' => $message,
                    'notif_type' => 'unassigned_faculty',
                    'notif_is_read' => false,
                ]);
            }
        }
    }

    public function ensureCurrentDepartmentNotification(Dept_Chair $chair): void
    {
        // A chair's dc_dept_id is the boundary: rebuild a missing notice only for their department.
        if (!$chair->dc_dept_id) {
            return;
        }

        $semester = Semester::where('sem_is_active', true)->first();
        if (!$semester) {
            return;
        }
        $query = Schedule::query()
            ->where('sch_fac_id', self::FACULTY_ID)
            // Match Professor X schedules assigned to this chair's department and active semester.
            ->where('sch_fac_assignment_dept_id', $chair->dc_dept_id)
            ->where('sch_is_active', true)
            ->when($semester, fn ($scheduleQuery) => $scheduleQuery->where('sch_sem_id', $semester->sem_id));
        if (!$query->exists()) {
            return;
        }

        $department = Departments::find($chair->dc_dept_id);
        if (!$department) {
            return;
        }

        $title = $this->notificationTitle($department, $semester);
        $hasExistingNotification = Notification::query()
            ->where('notif_usr_id', $chair->dc_usr_id)
            ->where('notif_type', 'unassigned_faculty')
            ->where('notif_title', $title)
            ->exists();
        if ($hasExistingNotification) {
            return;
        }

        $this->syncDepartmentNotification(
            (string) $chair->dc_dept_id,
            (string) $semester->sem_id
        );
    }

    private function notificationTitle(Departments $department, ?Semester $semester): string
    {
        $departmentLabel = $department->dept_code ?: $department->dept_name;
        $semesterLabel = $semester?->sem_name ?: 'current schedules';
        return "Unassigned faculty: {$departmentLabel} · {$semesterLabel}";
    }

    private function notificationMessage(Departments $department, ?Semester $semester, int $count): string
    {
        $departmentLabel = $department->dept_code ?: $department->dept_name;
        $semesterLabel = $semester?->sem_name ?: 'current schedules';
        $scheduleWord = $count === 1 ? 'schedule is' : 'schedules are';
        return "{$count} active {$scheduleWord} assigned to " . self::LABEL
            . " in {$departmentLabel} for {$semesterLabel}. Open Unassigned Faculty in the chair sidebar to assign them.";
    }
}
