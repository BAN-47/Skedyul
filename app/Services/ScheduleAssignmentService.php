<?php

namespace App\Services;

use App\Models\Schedule;
use App\Models\Study_Load;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\AcademicYear;
use App\Models\Workload;
use App\Models\Course;
use App\Models\Room;
use App\Models\Section;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Single place where PBS and PBT both create/update/delete schedule entries.
 * Neither controller talks to the `schedule` table directly — this keeps the
 * conflict rules (faculty / room / section double-booking) defined exactly
 * once, so a class added from either page is checked the same way.
 *
 * Every method returns an array shaped like:
 *   ['success' => true,  'schedule' => Schedule]
 *   ['success' => false, 'conflict' => true,  'message' => '...']   <- don't save, show as a toast
 *   ['success' => false, 'conflict' => false, 'message' => '...']   <- some other failure
 */
class ScheduleAssignmentService
{
    // These are the weekly teaching-hour limits used by the schedule checks.
    public const TOTAL_MAX_HOURS = 30;
    public const EVENING_MAX_HOURS = 30;
    public const PART_TIME_MAX_HOURS = 30;
    private const FULL_TIME_MAX_HOURS = 30;

    /** Day-hour caps for staff with special positions; other full-time faculty use 30 hours. */
    public const SPECIAL_POSITION_MAX_HOURS = [
        'Vice-President' => 3,
        'University Director' => 6,
        'Campus Director' => 6,
        'UD Data Protection Officer' => 6,
        'Assistant Campus Director' => 9,
        'Dean of Instruction' => 9,
        'College Dean' => 9,
        'Associate College Dean' => 12,
        'Department Chairperson' => 16,
        'PERG Chair' => 16,
        'Campus Secretary' => 16,
        'College Secretary' => 16,
        'Research Chair' => 16,
        'MIS Officer' => 30,
    ];

    public static function facultyMaxHours(Faculty $faculty, string $shift = 'day'): float
    {
        // Evening workload has a fixed 30-hour cap for all designations.
        if ($shift === 'evening') {
            return (float) self::EVENING_MAX_HOURS;
        }

        // Part-time staff share one 30-hour cap between Day and Evening.
        if ($faculty->fac_employment_type === 'part_time') {
            return (float) self::PART_TIME_MAX_HOURS;
        }

        if (
            $faculty->fac_special_position
            && isset(self::SPECIAL_POSITION_MAX_HOURS[$faculty->fac_special_position])
        ) {
            return (float) self::SPECIAL_POSITION_MAX_HOURS[$faculty->fac_special_position];
        }

        return (float) self::FULL_TIME_MAX_HOURS;
    }

    public static function workloadShiftForAssignment(
        bool $sectionIsEvening,
        string $scheduleShift,
        ?string $existingShift = null,
        bool $hasScheduledMeetings = false
    ): string {
        if ($sectionIsEvening) {
            return 'evening';
        }

        if ($hasScheduledMeetings && in_array($existingShift, ['day', 'evening'], true)) {
            return $existingShift;
        }

        return $scheduleShift;
    }

    /** Weekday schedule tabs have a fixed 4:00 PM boundary. */
    public static function scheduleTimeMessage(array $data, bool $sectionIsEvening = false): ?string
    {
        $day = $data['day'] ?? '';
        $shift = $sectionIsEvening ? 'evening' : ($data['shift'] ?? 'day');
        $start = $data['start_time'] ?? '';
        $end = $data['end_time'] ?? '';

        if (!in_array($day, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'], true)) {
            return null;
        }

        if ($shift === 'evening' && $start < '16:00') {
            return 'For evening sections, 7:00 AM–4:00 PM schedules are allowed on Saturday or Sunday only. Monday–Friday evening schedules must start at or after 4:00 PM.';
        }

        if ($shift === 'day' && $end > '16:00') {
            return 'Day schedules on weekdays must end by 4:00 PM.';
        }

        return null;
    }

    /** Assign one course/session to several weekdays as one all-or-nothing action. */
    public function assignMultiple(array $data): array
    {
        $days = array_values(array_unique($data['days'] ?? []));
        if (!$days) {
            return ['success' => false, 'conflict' => false, 'message' => 'Choose at least one day.'];
        }

        try {
            return DB::transaction(function () use ($data, $days) {
                $created = [];
                foreach ($days as $day) {
                    $dayData = $data;
                    unset($dayData['days']);
                    $dayData['day'] = $day;
                    // The workload is unchanged between days in this one
                    // request, so recalculate it once after all meetings save.
                    $result = $this->assign($dayData, syncWorkload: false, refreshResponse: false);
                    if (empty($result['success'])) {
                        throw new RuntimeException('__MULTI_DAY_FAIL__' . json_encode($result));
                    }
                    $created[] = $result['schedule'];
                }

                if (!app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'])) {
                    $this->syncWorkload($data['fac_id'], $data['sem_id']);
                }

                return [
                    'success' => true,
                    'message' => 'Schedule added for ' . implode(', ', $days) . '.',
                    'schedules' => $created,
                ];
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), '__MULTI_DAY_FAIL__')) {
                return json_decode(substr($e->getMessage(), strlen('__MULTI_DAY_FAIL__')), true)
                    ?: ['success' => false, 'conflict' => false, 'message' => 'Unable to add the selected days.'];
            }
            throw $e;
        }
    }

    /** Add a shared course across selected sections and days as one transaction. */
    public function assignMultipleSections(array $data): array
    {
        $sectionIds = array_values(array_unique($data['sec_ids'] ?? []));
        $days = array_values(array_unique($data['days'] ?? []));
        if (!$sectionIds || !$days) {
            return ['success' => false, 'conflict' => false, 'message' => 'Choose at least one Year 4 section and one day.'];
        }

        try {
            return DB::transaction(function () use ($data, $sectionIds, $days) {
                $created = [];
                $course = Course::findOrFail($data['subj_id']);
                if (self::isOjt($course)) {
                    Study_Load::where('sl_fac_id', $data['fac_id'])
                        ->where('sl_course_id', $course->course_id)
                        ->where('sl_sem_id', $data['sem_id'])
                        ->update(['sl_ojt_is_evening' => (bool) ($data['ojt_is_evening'] ?? false)]);
                }
                foreach ($sectionIds as $sectionId) {
                    foreach ($days as $day) {
                        $dayData = $data;
                        unset($dayData['days'], $dayData['sec_ids']);
                        $dayData['sec_id'] = $sectionId;
                        $dayData['day'] = $day;
                        $result = $this->assign($dayData, syncWorkload: false, refreshResponse: false);
                        if (empty($result['success'])) {
                            throw new RuntimeException('__MULTI_SECTION_FAIL__' . json_encode($result));
                        }
                        $created[] = $result['schedule'];
                    }
                }

                if (!app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'])) {
                    $this->syncWorkload($data['fac_id'], $data['sem_id']);
                }
                return ['success' => true, 'message' => 'Shared OJT schedule added for the selected Year 4 sections.', 'schedules' => $created];
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), '__MULTI_SECTION_FAIL__')) {
                return json_decode(substr($e->getMessage(), strlen('__MULTI_SECTION_FAIL__')), true)
                    ?: ['success' => false, 'conflict' => false, 'message' => 'Unable to add the shared OJT schedule.'];
            }
            throw $e;
        }
    }

    public function assign(array $data, bool $syncWorkload = true, bool $refreshResponse = true): array
    {
        $placeholder = app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'] ?? null);
        if (!$placeholder && !$this->facultyAccountIsActive($data['fac_id'])) {
            return ['success' => false, 'conflict' => false, 'message' => 'This faculty account is pending approval or inactive and cannot be assigned a schedule.'];
        }

        $course = Course::findOrFail($data['subj_id']);
        $isRec = $course->course_code === 'REC';

        if ($message = $this->sectionRequirementMessage($course, $data['sec_id'] ?? null)) {
            return ['success' => false, 'conflict' => false, 'status' => 422, 'message' => $message];
        }

        $sectionIsEvening = !empty($data['sec_id'])
            && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');
        if ($message = self::scheduleTimeMessage($data, $sectionIsEvening)) {
            return ['success' => false, 'conflict' => false, 'status' => 422, 'message' => $message];
        }

        if ($conflict = $this->findConflict($data, $isRec)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        try {
            $result = DB::transaction(function () use ($data, $syncWorkload, $placeholder) {
                $course = Course::findOrFail($data['subj_id']);
                $isRec = $course->course_code === 'REC';
                $isOjt = self::isOjt($course);
                $faculty = $this->lockConflictResources($data, $isRec);

                if ($conflict = $this->findConflict($data, $isRec)) {
                    throw new RuntimeException('SCHEDULE_CONFLICT:' . $conflict);
                }

                $scheduleShift = $data['shift'] ?? 'day';
                if (!$isRec) {
                    // Placeholder classes still count toward the section's course-hour cap.
                    $this->assertCourseHoursAvailable($data, $course);
                }
                if (!$placeholder) {
                $loadQuery = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'],
                    'sl_course_id' => $data['subj_id'],
                    'sl_sem_id' => $data['sem_id'],
                ]);
                if (!$isRec) {
                    $loadQuery->where('sl_sec_id', $data['sec_id']);
                }
                $studyLoad = $loadQuery->withExists('schedules')->first();
                $courseHours = self::courseHours($course);
                $sectionIsEvening = !empty($data['sec_id'])
                    && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');
                $workloadShift = self::workloadShiftForAssignment(
                    $sectionIsEvening,
                    $scheduleShift,
                    $studyLoad?->sl_shift,
                    (bool) ($studyLoad?->schedules_exists ?? false)
                );
                $currentHours = $this->facultyHoursForLimit(
                    $faculty,
                    $data['fac_id'],
                    $data['sem_id'],
                    $workloadShift
                );
                $hasExistingSharedOjt = self::isOjt($course) && Study_Load::where('sl_fac_id', $data['fac_id'])
                    ->where('sl_course_id', $course->course_id)
                    ->where('sl_sem_id', $data['sem_id'])
                    ->exists();
                $additionalHours = (!$studyLoad || (
                    $studyLoad->sl_shift !== $workloadShift
                    && $faculty->fac_employment_type !== 'part_time'
                )) && !$hasExistingSharedOjt ? $courseHours : 0;
                $hoursAfterAssignment = $currentHours + $additionalHours;
                $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                if ($hoursAfterAssignment > $maxHours) {
                    $workloadName = $faculty->fac_employment_type === 'part_time'
                        ? 'combined Day and Evening'
                        : $workloadShift;
                    throw new RuntimeException("LOAD_LIMIT:Cannot add {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadName} workload; this course adds {$additionalHours} hours, totaling {$hoursAfterAssignment} against the {$maxHours}-hour limit.");
                }

                if (!$studyLoad) {
                    $studyLoad = Study_Load::create([
                        'sl_fac_id' => $data['fac_id'],
                        'sl_course_id' => $data['subj_id'],
                        'sl_shift' => $workloadShift,
                        'sl_ojt_is_evening' => $isOjt ? (bool) ($data['ojt_is_evening'] ?? false) : false,
                        'sl_sec_id' => $data['sec_id'] ?? null,
                        'sl_sem_id' => $data['sem_id'],
                        'sl_assigned_by' => Auth::id(),
                        'sl_status'      => 'approved',
                        'sl_assigned_at' => now(),
                    ]);
                } else {
                    $needsSave = false;
                    if ($studyLoad->sl_shift !== $workloadShift) {
                        $studyLoad->sl_shift = $workloadShift;
                        $needsSave = true;
                    }
                    if ($isOjt && (bool) $studyLoad->sl_ojt_is_evening !== (bool) ($data['ojt_is_evening'] ?? false)) {
                        $studyLoad->sl_ojt_is_evening = (bool) ($data['ojt_is_evening'] ?? false);
                        $needsSave = true;
                    }
                    if ($needsSave) $studyLoad->save();
                }
                } else {
                    $studyLoad = null;
                    $courseHours = 0;
                    $hoursAfterAssignment = null;
                    $maxHours = 0;
                    $workloadShift = null;
                }

                $scheduleData = [
                    'sch_load_id'    => $studyLoad?->sl_id,
                    'sch_fac_id'     => $data['fac_id'],
                    // Professor X has no real faculty workload, so remember which department must assign this class later.
                    // The course's department ID is then used to route it to that department's chair.
                    'sch_fac_assignment_dept_id' => $placeholder ? $course->course_dept_id : null,
                    'sch_course_id'  => $data['subj_id'],
                    'sch_sec_id'     => $data['sec_id'],
                    'sch_room_id'    => $data['room_id'],
                    'sch_sem_id'     => $data['sem_id'],
                    'sch_day'        => $data['day'],
                    'sch_shift'      => $scheduleShift,
                    'sch_start_time' => $data['start_time'],
                    'sch_end_time'   => $data['end_time'],
                    'sch_status'     => 'draft',
                    'sch_is_active'  => true,
                    'sch_created_by' => Auth::id(),
                ];
                $schedule = Schedule::create($scheduleData);

                $updatedHours = $syncWorkload && !$placeholder
                    ? $this->syncWorkload($data['fac_id'], $data['sem_id'])
                    : null;

                return [
                    'schedule' => $schedule,
                    'hours' => $updatedHours,
                    'max_hours' => $maxHours,
                    'hours_after' => $hoursAfterAssignment,
                    'workload_shift' => $workloadShift,
                ];
            });
            $schedule = $result['schedule'];
            if ($placeholder && $schedule->sch_fac_assignment_dept_id) {
                // Refresh the notice for the department that now owns this unassigned class.
                app(UnassignedFacultyService::class)->syncDepartmentNotification(
                    (string) $schedule->sch_fac_assignment_dept_id,
                    (string) $schedule->sch_sem_id
                );
            }
            $notice = '';
            if ($syncWorkload && $result['hours_after'] !== null) {
                $hours = $result['hours_after'];
                $max = $result['max_hours'];
                $shift = $result['workload_shift'];
                $notice = $hours >= $max
                    ? " Faculty load is now {$hours} of {$max} teaching hours in the {$shift} workload; further course assignments in this shift are blocked."
                    : '';
            }
            if ($refreshResponse) {
                $schedule->load(['subject', 'faculty', 'section', 'room']);
            }
            return [
                'success' => true,
                'message' => 'Schedule added.' . $notice,
                'schedule' => $schedule,
                'hours_after' => $result['hours_after'],
            ];
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'SCHEDULE_CONFLICT:')) {
                return ['success' => false, 'conflict' => true, 'message' => substr($e->getMessage(), strlen('SCHEDULE_CONFLICT:'))];
            }
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) {
                return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            }
            if (str_starts_with($e->getMessage(), 'COURSE_HOURS:')) {
                return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 13)];
            }
            throw $e;
        } catch (QueryException $e) {
            if (($e->errorInfo[0] ?? $e->getCode()) === '23505') {
                return ['success' => false, 'conflict' => true, 'message' => 'Conflict: that schedule already exists.'];
            }
            throw $e;
        }
    }

    public function update(string $scheduleId, array $data): array
    {
        $placeholder = app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'] ?? null);
        if (!$placeholder && !$this->facultyAccountIsActive($data['fac_id'])) {
            return ['success' => false, 'conflict' => false, 'message' => 'This faculty account is pending approval or inactive and cannot be assigned a schedule.'];
        }

        $course = Course::findOrFail($data['subj_id']);
        $isRec = $course->course_code === 'REC';
        if ($message = $this->sectionRequirementMessage($course, $data['sec_id'] ?? null)) {
            return ['success' => false, 'conflict' => false, 'status' => 422, 'message' => $message];
        }
        $sectionIsEvening = !empty($data['sec_id'])
            && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');
        if ($message = self::scheduleTimeMessage($data, $sectionIsEvening)) {
            return ['success' => false, 'conflict' => false, 'status' => 422, 'message' => $message];
        }
        if ($conflict = $this->findConflict($data, $isRec, $scheduleId)) {
            return ['success' => false, 'conflict' => true, 'message' => $conflict];
        }

        $oldFacultyId = null;
        $oldSemesterId = null;
        $oldAssignmentDepartmentId = null;
        try {
            $schedule = DB::transaction(function () use ($scheduleId, $data, $isRec, $placeholder, &$oldFacultyId, &$oldSemesterId, &$oldAssignmentDepartmentId) {
                $faculty = $this->lockConflictResources($data, $isRec);
                $schedule = Schedule::whereKey($scheduleId)->lockForUpdate()->firstOrFail();
                $oldFacultyId = $schedule->sch_fac_id;
                $oldSemesterId = $schedule->sch_sem_id;
                $oldAssignmentDepartmentId = $schedule->sch_fac_assignment_dept_id;

                if ($conflict = $this->findConflict($data, $isRec, $scheduleId)) {
                    throw new RuntimeException('SCHEDULE_CONFLICT:' . $conflict);
                }

                $course = Course::findOrFail($data['subj_id']);
                if (!$isRec) {
                    // Moving an existing class must obey the section cap for all teachers.
                    $this->assertCourseHoursAvailable($data, $course, $scheduleId);
                }

                if ($placeholder) {
                    $oldLoadId = $schedule->sch_load_id;
                    $placeholderCourse = Course::findOrFail($data['subj_id']);
                    $schedule->update([
                        'sch_load_id' => null,
                        'sch_fac_id' => $data['fac_id'],
                        // Keep the destination department in sync if an edit changes the course while Professor X is selected.
                        'sch_fac_assignment_dept_id' => $placeholderCourse->course_dept_id,
                        'sch_course_id' => $data['subj_id'],
                        'sch_sec_id' => $data['sec_id'],
                        'sch_room_id' => $data['room_id'],
                        'sch_sem_id' => $data['sem_id'],
                        'sch_day' => $data['day'],
                        'sch_shift' => $data['shift'] ?? $schedule->sch_shift ?? 'day',
                        'sch_start_time' => $data['start_time'],
                        'sch_end_time' => $data['end_time'],
                    ]);
                    if ($oldLoadId) {
                        Study_Load::whereKey($oldLoadId)->whereDoesntHave('schedules')->delete();
                    }
                    return $schedule;
                }

                $course = Course::findOrFail($data['subj_id']);
                $courseHours = self::courseHours($course);
                $scheduleShift = $data['shift'] ?? $schedule->sch_shift ?? 'day';
                $sectionIsEvening = !empty($data['sec_id'])
                    && (bool) Section::whereKey($data['sec_id'])->value('sec_is_evening');

                $sourceLoad = Study_Load::whereKey($schedule->sch_load_id)
                    ->lockForUpdate()
                    ->first();
                $isSameFacultySectionChange = $sourceLoad
                    && $schedule->sch_fac_id === $data['fac_id']
                    && $schedule->sch_course_id === $data['subj_id']
                    && $schedule->sch_sem_id === $data['sem_id']
                    && $schedule->sch_sec_id !== $data['sec_id'];
                $sourceLoadHasOtherSchedules = $isSameFacultySectionChange
                    && Schedule::where('sch_load_id', $sourceLoad->sl_id)
                    ->where('sch_id', '!=', $schedule->sch_id)
                    ->exists();

                $targetLoad = Study_Load::where([
                    'sl_fac_id' => $data['fac_id'],
                    'sl_course_id' => $data['subj_id'],
                    'sl_sec_id' => $data['sec_id'],
                    'sl_sem_id' => $data['sem_id'],
                ])->withExists('schedules')->first();
                $workloadShift = self::workloadShiftForAssignment(
                    $sectionIsEvening,
                    $scheduleShift,
                    $targetLoad?->sl_shift,
                    (bool) ($targetLoad?->schedules_exists ?? false)
                );
                if ($isSameFacultySectionChange) {
                    $workloadShift = $sectionIsEvening ? 'evening' : 'day';
                }
                $isSameLoad = $targetLoad && $targetLoad->sl_id === $schedule->sch_load_id;
                $targetLoadNeedsShiftMove = $targetLoad && $targetLoad->sl_shift !== $workloadShift;
                $sourceLoadToDelete = null;
                if ($isSameFacultySectionChange && !$sourceLoadHasOtherSchedules) {
                    $sourceWorkloadShift = (bool) Section::whereKey($sourceLoad->sl_sec_id)->value('sec_is_evening')
                        ? 'evening'
                        : ($sourceLoad->sl_shift ?? 'day');
                    if ($sourceWorkloadShift !== $workloadShift && !$targetLoad) {
                        $currentHours = $this->facultyHoursForLimit(
                            $faculty,
                            $data['fac_id'],
                            $data['sem_id'],
                            $workloadShift
                        );
                        $hoursToAdd = $faculty->fac_employment_type === 'part_time' ? 0 : $courseHours;
                        $projectedHours = $currentHours + $hoursToAdd;
                        $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                        if ($projectedHours > $maxHours) {
                            $workloadName = $faculty->fac_employment_type === 'part_time'
                                ? 'combined Day and Evening'
                                : $workloadShift;
                            throw new RuntimeException("LOAD_LIMIT:Cannot move {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadName} workload; this course adds {$hoursToAdd} hours, totaling {$projectedHours} against the {$maxHours}-hour limit.");
                        }
                    }

                    if ($targetLoad && $targetLoad->sl_id !== $sourceLoad->sl_id) {
                        $schedule->sch_load_id = $targetLoad->sl_id;
                        $sourceLoadToDelete = $sourceLoad->sl_id;
                    } else {
                        $sourceLoad->sl_sec_id = $data['sec_id'];
                        $sourceLoad->sl_shift = $workloadShift;
                        $sourceLoad->save();
                        $schedule->sch_load_id = $sourceLoad->sl_id;
                    }
                } elseif (!$isSameLoad || $targetLoadNeedsShiftMove) {
                    $currentHours = $this->facultyHoursForLimit(
                        $faculty,
                        $data['fac_id'],
                        $data['sem_id'],
                        $workloadShift
                    );
                    $replacingSameFacultyLoad = $schedule->sch_fac_id === $data['fac_id'] && $schedule->sch_sem_id === $data['sem_id'];
                    $sourceLoad = Study_Load::with('section')->find($schedule->sch_load_id);
                    $sourceWorkloadShift = $sourceLoad?->section?->sec_is_evening
                        ? 'evening'
                        : ($sourceLoad?->sl_shift ?? 'day');
                    $oldLoadHasOtherSchedules = Schedule::where('sch_load_id', $schedule->sch_load_id)
                        ->where('sch_id', '!=', $schedule->sch_id)
                        ->exists();
                    $willRemoveOldLoad = !$oldLoadHasOtherSchedules;
                    if (
                        $replacingSameFacultyLoad
                        && $willRemoveOldLoad
                        && (
                            $sourceWorkloadShift === $workloadShift
                            || $faculty->fac_employment_type === 'part_time'
                        )
                    ) {
                        $oldCourse = Course::find($schedule->sch_course_id);
                        $currentHours -= $oldCourse ? self::courseHours($oldCourse) : 0;
                    }
                    $hoursToAdd = 0;
                    if (!$targetLoad || $targetLoadNeedsShiftMove) {
                        $hoursToAdd = $courseHours;
                        if (
                            $faculty->fac_employment_type === 'part_time'
                            && $targetLoad
                            && !$isSameLoad
                        ) {
                            $hoursToAdd = 0;
                        }
                    }
                    $projectedHours = $currentHours + $hoursToAdd;
                    $maxHours = self::facultyMaxHours($faculty, $workloadShift);
                    if ($projectedHours > $maxHours) {
                        $workloadName = $faculty->fac_employment_type === 'part_time'
                            ? 'combined Day and Evening'
                            : $workloadShift;
                        throw new RuntimeException("LOAD_LIMIT:Cannot assign {$course->course_code}. {$faculty->fac_first_name} {$faculty->fac_last_name} has {$currentHours} teaching hours in the {$workloadName} workload; this course adds {$hoursToAdd} hours, totaling {$projectedHours} against the {$maxHours}-hour limit.");
                    }
                    if (!$targetLoad) {
                        $targetLoad = Study_Load::create([
                            'sl_fac_id' => $data['fac_id'],
                            'sl_course_id' => $data['subj_id'],
                            'sl_shift' => $workloadShift,
                            'sl_sec_id' => $data['sec_id'],
                            'sl_sem_id' => $data['sem_id'],
                            'sl_assigned_by' => Auth::id(),
                            'sl_assigned_at' => now(),
                            'sl_status' => 'approved',
                        ]);
                    } elseif ($targetLoadNeedsShiftMove) {
                        $targetLoad->sl_shift = $workloadShift;
                        $targetLoad->save();
                    }
                    if (!$isSameLoad) {
                        $oldLoadId = $schedule->sch_load_id;
                        $schedule->sch_load_id = $targetLoad->sl_id;
                        $schedule->save();
                        if ($oldLoadId !== $targetLoad->sl_id) {
                            Study_Load::whereKey($oldLoadId)->whereDoesntHave('schedules')->delete();
                        }
                    }
                }
                $scheduleData = [
                    'sch_fac_id' => $data['fac_id'],
                    'sch_fac_assignment_dept_id' => null,
                    'sch_course_id' => $data['subj_id'],
                    'sch_sec_id' => $data['sec_id'],
                    'sch_room_id' => $data['room_id'],
                    'sch_sem_id' => $data['sem_id'],
                    'sch_day' => $data['day'],
                    'sch_shift' => $scheduleShift,
                    'sch_start_time' => $data['start_time'],
                    'sch_end_time' => $data['end_time'],
                ];
                $schedule->update($scheduleData);
                if ($sourceLoadToDelete) {
                    Study_Load::whereKey($sourceLoadToDelete)
                        ->whereDoesntHave('schedules')
                        ->delete();
                }
                if (!app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'])) {
                    $this->syncWorkload($data['fac_id'], $data['sem_id']);
                }
                return $schedule;
            });
        } catch (RuntimeException $e) {
            if (str_starts_with($e->getMessage(), 'SCHEDULE_CONFLICT:')) {
                return ['success' => false, 'conflict' => true, 'message' => substr($e->getMessage(), strlen('SCHEDULE_CONFLICT:'))];
            }
            if (str_starts_with($e->getMessage(), 'LOAD_LIMIT:')) return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 11)];
            if (str_starts_with($e->getMessage(), 'COURSE_HOURS:')) return ['success' => false, 'conflict' => false, 'message' => substr($e->getMessage(), 13)];
            throw $e;
        }
        if (($oldFacultyId !== $data['fac_id'] || $oldSemesterId !== $data['sem_id'])
            && $oldFacultyId
            && !app(UnassignedFacultyService::class)->isPlaceholder($oldFacultyId)) {
            $this->syncWorkload($oldFacultyId, $oldSemesterId);
        }
        if ($placeholder && $schedule->sch_fac_assignment_dept_id) {
            // Update the new department's notice after an edit involving Professor X.
            app(UnassignedFacultyService::class)->syncDepartmentNotification(
                (string) $schedule->sch_fac_assignment_dept_id,
                (string) $schedule->sch_sem_id
            );
        } elseif (app(UnassignedFacultyService::class)->isPlaceholder($oldFacultyId)
            && $oldAssignmentDepartmentId
            && $oldSemesterId) {
            app(UnassignedFacultyService::class)->syncDepartmentNotification(
                (string) $oldAssignmentDepartmentId,
                (string) $oldSemesterId
            );
        }
        return ['success' => true, 'schedule' => $schedule->load(['subject', 'faculty', 'section', 'room'])];
    }

    public function delete(string $scheduleId): array
    {
        $unassignedNoticeScopes = collect();
        $result = DB::transaction(function () use ($scheduleId, &$unassignedNoticeScopes) {
            $schedule = Schedule::with('subject')->findOrFail($scheduleId);
            $facultyId = $schedule->sch_fac_id;
            $semesterId = $schedule->sch_sem_id;

            $schedulesToDelete = self::isOjt($schedule->subject)
                ? Schedule::query()
                ->where('sch_fac_id', $facultyId)
                ->where('sch_sem_id', $semesterId)
                ->where('sch_is_active', $schedule->sch_is_active)
                ->where('sch_course_id', $schedule->sch_course_id)
                ->where('sch_day', $schedule->sch_day)
                ->where('sch_room_id', $schedule->sch_room_id)
                ->where('sch_start_time', $schedule->sch_start_time)
                ->where('sch_end_time', $schedule->sch_end_time)
                ->get()
                : collect([$schedule]);

            $unassignedNoticeScopes = $schedulesToDelete
                ->filter(fn ($item) => app(UnassignedFacultyService::class)->isPlaceholder((string) $item->sch_fac_id))
                ->map(fn ($item) => [
                    'department_id' => $item->sch_fac_assignment_dept_id,
                    'semester_id' => $item->sch_sem_id,
                ])
                ->filter(fn ($scope) => $scope['department_id'] && $scope['semester_id'])
                ->unique(fn ($scope) => $scope['department_id'] . '|' . $scope['semester_id'])
                ->values();

            $loadIds = $schedulesToDelete->pluck('sch_load_id')->filter()->unique();
            Schedule::whereIn('sch_id', $schedulesToDelete->pluck('sch_id'))->delete();

            // Keep a study load while it still has schedules on another day.
            Study_Load::whereIn('sl_id', $loadIds)->whereDoesntHave('schedules')->delete();
            if (!app(UnassignedFacultyService::class)->isPlaceholder($facultyId)) {
                $this->syncWorkload($facultyId, $semesterId);
            }

            return [
                'success' => true,
                'message' => self::isOjt($schedule->subject)
                    ? 'Shared OJT schedule deleted for all selected sections.'
                    : 'Schedule deleted.',
            ];
        });

        foreach ($unassignedNoticeScopes as $scope) {
            app(UnassignedFacultyService::class)->syncDepartmentNotification(
                (string) $scope['department_id'],
                (string) $scope['semester_id']
            );
        }

        return $result;
    }

    public function facultyHours(string $facultyId, string $semesterId): float
    {
        $loads = Study_Load::query()
            ->where('sl_fac_id', $facultyId)
            ->where('sl_sem_id', $semesterId)
            ->with('subject')
            ->get();
        return self::sumLoadCourseHours($loads);
    }

    public function facultyHoursForShift(string $facultyId, string $semesterId, string $shift): float
    {
        $loads = Study_Load::query()
            ->where('sl_fac_id', $facultyId)
            ->where('sl_sem_id', $semesterId)
            ->where('sl_shift', $shift)
            ->with('subject')
            ->get();

        return self::sumLoadCourseHours($loads);
    }

    private static function sumLoadCourseHours($loads): float
    {
        $countedOjt = [];
        return (float) $loads->sum(function ($load) use (&$countedOjt) {
            $course = $load->subject;
            if (self::isOjt($course)) {
                $id = (string) $course->course_id;
                if (isset($countedOjt[$id])) return 0;
                $countedOjt[$id] = true;
            }
            return self::courseHours($course);
        });
    }

    public function facultyHoursForLimit(
        Faculty $faculty,
        string $facultyId,
        string $semesterId,
        string $shift
    ): float {
        return $faculty->fac_employment_type === 'part_time'
            ? $this->facultyHours($facultyId, $semesterId)
            : $this->facultyHoursForShift($facultyId, $semesterId, $shift);
    }

    public static function courseHours(?Course $course): float
    {
        return $course
            ? (float) ($course->course_lecture_hours ?? 0) + (float) ($course->course_lab_hours ?? 0)
            : 0.0;
    }

    /** Sum active scheduled meeting duration for the given faculty loads. */
    public static function scheduledHoursForLoads(iterable $loads): float
    {
        $totalMinutes = 0;

        foreach ($loads as $load) {
            foreach ($load->schedules ?? [] as $schedule) {
                if (!$schedule->sch_is_active) {
                    continue;
                }

                $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                $totalMinutes += $start->diffInMinutes($end);
            }
        }

        return $totalMinutes / 60;
    }

    public static function isOjt(?Course $course): bool
    {
        if (!$course) return false;
        $code = mb_strtolower((string) $course->course_code);
        $name = mb_strtolower((string) $course->course_name);
        return str_contains($code, 'ojt') || str_contains($name, 'on-the-job training');
    }

    public static function courseUnits(?Course $course): float
    {
        if (!$course) return 0.0;
        return self::isOjt($course) ? 5.0 : (float) ($course->course_units ?? 0);
    }

    private function facultyAccountIsActive(string $facultyId): bool
    {
        return Faculty::query()
            ->whereKey($facultyId)
            ->whereHas('user', fn($query) => $query->where('usr_is_active', true))
            ->exists();
    }

    /** REC is the only course that may be scheduled without a section. */
    private function sectionRequirementMessage(Course $course, ?string $sectionId): ?string
    {
        if (!$sectionId) {
            return $course->course_code === 'REC' ? null : 'A section is required for this course.';
        }

        return null;
    }

    private function assertCourseHoursAvailable(
        array $data,
        Course $course,
        ?string $excludeScheduleId = null
    ): void {
        // A course's weekly allowance is its saved lecture hours plus lab hours.
        $allowedHours = self::courseHours($course);
        if ($allowedHours <= 0) {
            return;
        }

        $query = Schedule::query()
            ->where('sch_course_id', $data['subj_id'])
            ->where('sch_sec_id', $data['sec_id'])
            ->where('sch_sem_id', $data['sem_id'])
            ->where('sch_is_active', true);
        if ($excludeScheduleId) {
            $query->where('sch_id', '!=', $excludeScheduleId);
        }

        // Add the length of this course's active meetings for this section and semester.
        $scheduledHours = (float) $query->get(['sch_start_time', 'sch_end_time'])
            ->sum(function ($schedule) {
                $start = \Illuminate\Support\Carbon::parse($schedule->sch_start_time);
                $end = \Illuminate\Support\Carbon::parse($schedule->sch_end_time);
                return $start->diffInMinutes($end) / 60;
            });
        $newHours = \Illuminate\Support\Carbon::parse($data['start_time'])
            ->diffInMinutes(\Illuminate\Support\Carbon::parse($data['end_time'])) / 60;
        // Check the total after adding the proposed meeting (or moving the edited one).
        $projectedHours = $scheduledHours + $newHours;

        if ($projectedHours > $allowedHours) {
            $section = \App\Models\Section::find($data['sec_id']);
            $sectionName = $section?->sec_name ?? 'the selected section';
            throw new RuntimeException(
                "COURSE_HOURS:Overextension: {$course->course_code} for {$sectionName} allows {$allowedHours} total hours per week. "
                    . "Already scheduled: {$scheduledHours} hours; this meeting adds {$newHours}, totaling {$projectedHours} hours."
            );
        }
    }

    public function syncWorkload(string $facultyId, string $semesterId, ?float $knownTotalHours = null): float
    {
        if ($facultyId === UnassignedFacultyService::FACULTY_ID
            || app(UnassignedFacultyService::class)->isPlaceholder($facultyId)) {
            return 0.0;
        }
        $semester = Semester::find($semesterId);
        $workload = Workload::firstOrNew(['wl_fac_id' => $facultyId, 'wl_sem_id' => $semesterId]);
        if (!$workload->exists) $workload->wl_id = (string) \Illuminate\Support\Str::uuid();
        $workload->wl_ay_id = $semester?->sem_ay_id ?? AcademicYear::where('ay_is_active', true)->value('ay_id');
        $totalHours = $knownTotalHours ?? $this->facultyHours($facultyId, $semesterId);
        $workload->wl_total_hours = $totalHours;
        $workload->wl_type = $workload->wl_type ?? 'regular';
        $workload->save();
        return $totalHours;
    }

    /**
     * Checks faculty / room / section double-booking: same day, same
     * semester, overlapping time range. Returns a human-readable conflict
     * message, or null when the slot is free.
     */
    private function lockConflictResources(array $data, bool $isRec): Faculty
    {
        $faculty = Faculty::whereKey($data['fac_id'])->lockForUpdate()->firstOrFail();

        if (!$isRec && !empty($data['room_id'])) {
            Room::whereKey($data['room_id'])->lockForUpdate()->firstOrFail();
        }

        if (!$isRec && !empty($data['sec_id'])) {
            Section::whereKey($data['sec_id'])->lockForUpdate()->firstOrFail();
        }

        return $faculty;
    }

    private function findConflict(array $data, bool $isRec = false, ?string $excludeScheduleId = null): ?string
    {
        $course = Course::find($data['subj_id'] ?? null);
        $isSharedOjt = self::isOjt($course);
        // Find active classes in the same semester/day whose time overlaps the new class.
        // The < and > checks allow classes to touch end-to-start without a conflict.
        $base = Schedule::query()
            ->where('sch_sem_id', $data['sem_id'])
            ->where('sch_day', $data['day'])
            ->where('sch_is_active', true)
            ->where('sch_start_time', '<', $data['end_time'])
            ->where('sch_end_time', '>', $data['start_time']);

        if ($excludeScheduleId) {
            $base->where('sch_id', '!=', $excludeScheduleId);
        }

        // Check the teacher first. Placeholder Unassigned Faculty do not conflict with themselves.
        $facultyClash = app(UnassignedFacultyService::class)->isPlaceholder($data['fac_id'] ?? null)
            ? null
            : (clone $base)->where('sch_fac_id', $data['fac_id'])
                ->with(['subject', 'section', 'faculty'])
                ->first();
        $sameSharedOjtMeeting = $isSharedOjt && $facultyClash
            && self::isOjt($facultyClash->subject)
            && $facultyClash->sch_course_id === $course?->course_id
            && (string) $facultyClash->sch_sec_id !== (string) ($data['sec_id'] ?? '')
            && $facultyClash->sch_room_id === ($data['room_id'] ?? null)
            && substr((string) $facultyClash->sch_start_time, 0, 5) === substr((string) $data['start_time'], 0, 5)
            && substr((string) $facultyClash->sch_end_time, 0, 5) === substr((string) $data['end_time'], 0, 5);
        if ($facultyClash && !$sameSharedOjtMeeting) {
            $professor = $this->professorName($facultyClash);
            $section = $this->sectionName($facultyClash);
            return "Professor {$professor} already has {$this->codeOf($facultyClash)} with {$section} on {$data['day']} ({$facultyClash->sch_start_time}–{$facultyClash->sch_end_time}), which overlaps this time.";
        }

        // A room cannot host two overlapping classes; REC has no room/section requirement.
        if (!$isRec && !empty($data['room_id'])) {
            $roomClash = (clone $base)->where('sch_room_id', $data['room_id'])
                ->with(['subject', 'section', 'faculty', 'room'])
                ->first();
            if ($roomClash && !$sameSharedOjtMeeting) {
                $room = $roomClash->room->room_name ?? 'the selected room';
                return "Room {$room} is already used by {$this->professorName($roomClash)} for {$this->codeOf($roomClash)} with {$this->sectionName($roomClash)} on {$data['day']} ({$roomClash->sch_start_time}–{$roomClash->sch_end_time}).";
            }
        }

        // A section cannot attend two overlapping classes at the same time.
        if (!$isRec && !empty($data['sec_id'])) {
            $sectionClash = (clone $base)->where('sch_sec_id', $data['sec_id'])
                ->with(['subject', 'section', 'faculty'])
                ->first();
            if ($sectionClash) {
                $professor = $this->professorName($sectionClash);
                $section = $this->sectionName($sectionClash);
                return "Section {$section} already has {$this->codeOf($sectionClash)} with {$professor} on {$data['day']} ({$sectionClash->sch_start_time}–{$sectionClash->sch_end_time}).";
            }
        }

        return null;
    }

    /**
     * Pairwise version of findConflict(), used when a PBT is submitted.
     * Blank rooms and blank sections are not a match. REC skips room and
     * section checks. One OJT meeting shared by several sections is allowed.
     */
    public function schedulesConflict(Schedule $first, Schedule $second): bool
    {
        if ($first->sch_id !== null && $first->sch_id === $second->sch_id) {
            return false;
        }

        if (!$this->sameDay($first, $second) || !$this->timesOverlap($first, $second)) {
            return false;
        }

        $sharedOjt = $this->isSharedOjtMeeting($first, $second);
        $sameFaculty = (string) $first->sch_fac_id === (string) $second->sch_fac_id;
        $placeholderFaculty = app(UnassignedFacultyService::class)->isPlaceholder((string) $first->sch_fac_id)
            || app(UnassignedFacultyService::class)->isPlaceholder((string) $second->sch_fac_id);

        if ($sameFaculty && !$sharedOjt && !$placeholderFaculty) {
            return true;
        }

        $firstIsRec = $this->isRecSchedule($first);
        $secondIsRec = $this->isRecSchedule($second);

        if (
            !$sharedOjt
            && !$firstIsRec
            && !$secondIsRec
            && $this->sameFilledId($first->sch_room_id, $second->sch_room_id)
        ) {
            return true;
        }

        if (
            !$firstIsRec
            && !$secondIsRec
            && $this->sameFilledId($first->sch_sec_id, $second->sch_sec_id)
        ) {
            return true;
        }

        return false;
    }

    public function submissionConflictCount($schedules, string $facultyId): int
    {
        $count = 0;
        $items = collect($schedules)->values();

        for ($left = 0; $left < $items->count(); $left++) {
            for ($right = $left + 1; $right < $items->count(); $right++) {
                $first = $items[$left];
                $second = $items[$right];

                if ($first->sch_fac_id !== $facultyId && $second->sch_fac_id !== $facultyId) {
                    continue;
                }

                if ($this->schedulesConflict($first, $second)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    public function scheduleDetailsAreComplete($schedules): bool
    {
        $items = collect($schedules);

        return $items->isNotEmpty() && $items->every(function (Schedule $schedule) {
            if (!$schedule->faculty || !$schedule->subject) {
                return false;
            }

            if ($this->isRecSchedule($schedule)) {
                return true;
            }

            return (bool) $schedule->section;
        });
    }

    private function isRecSchedule(Schedule $schedule): bool
    {
        return ($schedule->subject->course_code ?? '') === 'REC';
    }

    private function isSharedOjtMeeting(Schedule $first, Schedule $second): bool
    {
        $firstCourse = $first->subject;
        $secondCourse = $second->subject;

        return self::isOjt($firstCourse)
            && self::isOjt($secondCourse)
            && (string) $first->sch_course_id === (string) $second->sch_course_id
            && (string) $first->sch_fac_id === (string) $second->sch_fac_id
            && (string) $first->sch_sec_id !== (string) $second->sch_sec_id
            && (string) ($first->sch_room_id ?? '') === (string) ($second->sch_room_id ?? '')
            && $this->clock($first->sch_start_time) === $this->clock($second->sch_start_time)
            && $this->clock($first->sch_end_time) === $this->clock($second->sch_end_time);
    }

    private function sameDay(Schedule $first, Schedule $second): bool
    {
        return strcasecmp(trim((string) $first->sch_day), trim((string) $second->sch_day)) === 0;
    }

    private function timesOverlap(Schedule $first, Schedule $second): bool
    {
        $firstStart = $this->clock($first->sch_start_time);
        $firstEnd = $this->clock($first->sch_end_time);
        $secondStart = $this->clock($second->sch_start_time);
        $secondEnd = $this->clock($second->sch_end_time);

        return $firstStart < $secondEnd && $secondStart < $firstEnd;
    }

    private function sameFilledId(mixed $left, mixed $right): bool
    {
        if ($left === null || $left === '' || $right === null || $right === '') {
            return false;
        }

        return (string) $left === (string) $right;
    }

    private function clock(mixed $time): string
    {
        $value = substr(trim((string) $time), 0, 8);

        if (preg_match('/^\d{2}:\d{2}$/', $value)) {
            return $value . ':00';
        }

        return $value;
    }

    private function codeOf(Schedule $schedule): string
    {
        return $schedule->subject->subj_code ?? 'another class';
    }

    private function sectionName(Schedule $schedule): string
    {
        return $schedule->section->sec_name ?? 'an unnamed section';
    }

    private function professorName(Schedule $schedule): string
    {
        $faculty = $schedule->faculty;
        return $faculty
            ? (FacultyNameFormatter::format($faculty) ?: 'Unassigned')
            : 'the selected professor';
    }
}
