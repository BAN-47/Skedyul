<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Room;
use App\Models\Section;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class ScheduleReuseService
{
    public function __construct(private ScheduleAssignmentService $scheduler)
    {
    }

    public function destinationSemesterForSource(?string $sourceSemesterId): ?object
    {
        if (!$sourceSemesterId) {
            return null;
        }

        $source = $this->semester($sourceSemesterId);
        $sourceTerm = $source ? self::termNumber((string) $source->sem_name) : null;
        if (!$source || !$sourceTerm || (bool) ($source->ay_is_active ?? false)) {
            return null;
        }

        return DB::table('semester as sem')
            ->join('academic_year as ay', 'ay.ay_id', '=', 'sem.sem_ay_id')
            ->where('ay.ay_is_active', true)
            ->orderByDesc('sem.sem_is_active')
            ->orderByDesc('sem.sem_start_date')
            ->get([
                'sem.sem_id',
                'sem.sem_name',
                'sem.sem_ay_id',
                'sem.sem_is_active',
                'sem.sem_is_plotting_enabled',
                'sem.sem_start_date',
                'ay.ay_academic_year',
                'ay.ay_year_label',
                'ay.ay_is_active',
            ])
            ->first(fn ($semester) =>
                self::termNumber((string) $semester->sem_name) === $sourceTerm
            );
    }

    public function preview(
        string $scope,
        string $targetId,
        string $destinationSemesterId,
        string $sourceSemesterId,
        array $mappings = [],
        array $excluded = []
    ): array {
        $connection = DB::connection();
        $transactionLevel = $connection->transactionLevel();
        $connection->beginTransaction();

        try {
            $plan = $this->prepare($scope, $targetId, $destinationSemesterId, $sourceSemesterId, $mappings, $excluded);
            if ($plan['error']) {
                return $this->errorPreview($plan['error'], $plan);
            }
            if (!$plan['rows']) {
                return $this->errorPreview(
                    'No active schedules were found in the selected archived semester for this section or faculty.',
                    $plan
                );
            }

            $this->simulate($plan);
            $rows = array_values($plan['rows']);
            $summary = $this->summary($rows);
            $hasAssignments = count($plan['assignments']) > 0;

            return [
                'success' => true,
                'can_copy' => $summary['blocked'] === 0 && $hasAssignments,
                'message' => $this->previewMessage($summary, $hasAssignments),
                'rows' => $rows,
                'summary' => $summary,
                'source_label' => $plan['source_label'],
                'destination_label' => $plan['destination_label'],
            ];
        } finally {
            $this->rollbackToTransactionLevel($transactionLevel);
        }
    }

    public function apply(
        string $scope,
        string $targetId,
        string $destinationSemesterId,
        string $sourceSemesterId,
        array $mappings = [],
        array $excluded = [],
        ?string $overrideReason = null
    ): array {
        $preview = $this->preview(
            $scope,
            $targetId,
            $destinationSemesterId,
            $sourceSemesterId,
            $mappings,
            $excluded
        );

        if (!$preview['success'] || !$preview['can_copy']) {
            return [
                'success' => false,
                'message' => $preview['message'],
                'preview' => $preview,
            ];
        }

        $overrideReason = trim((string) $overrideReason);
        $hasWorkloadOverrides = ($preview['summary']['workload_overrides'] ?? 0) > 0;
        if ($hasWorkloadOverrides && $overrideReason === '') {
            return [
                'success' => false,
                'requires_reason' => true,
                'message' => 'Enter a reason for the faculty workload limit exception.',
                'preview' => $preview,
            ];
        }

        try {
            $copied = DB::transaction(function () use (
                $scope,
                $targetId,
                $destinationSemesterId,
                $sourceSemesterId,
                $mappings,
                $excluded,
                $overrideReason,
                $hasWorkloadOverrides,
                $preview
            ) {
                $plan = $this->prepare(
                    $scope,
                    $targetId,
                    $destinationSemesterId,
                    $sourceSemesterId,
                    $mappings,
                    $excluded
                );
                if ($plan['error']) {
                    throw new ScheduleReuseAbort([
                        'success' => false,
                        'message' => $plan['error'],
                    ]);
                }

                $this->simulate($plan);
                $rows = array_values($plan['rows']);
                $summary = $this->summary($rows);
                if ($summary['blocked'] > 0 || !$plan['assignments']) {
                    throw new ScheduleReuseAbort([
                        'success' => false,
                        'message' => 'Schedules changed or have unresolved issues. Review the refreshed preview before copying.',
                        'preview' => $this->previewPayload($rows, $summary, false, $plan),
                    ]);
                }

                $count = 0;
                foreach ($plan['assignments'] as $sourceKey => $assignment) {
                    if ($hasWorkloadOverrides) {
                        $assignment['workload_override'] = true;
                        $assignment['workload_override_reason'] = $overrideReason;
                    }

                    $result = $this->scheduler->assign($assignment);
                    if (empty($result['success'])) {
                        throw new ScheduleReuseAbort([
                            'success' => false,
                            'message' => 'A schedule changed during reuse. No schedules were copied.',
                            'error' => [
                                'source_key' => $sourceKey,
                                'message' => (string) ($result['message'] ?? 'The schedule could not be reused.'),
                            ],
                        ]);
                    }
                    $count++;
                }

                return $count;
            });
        } catch (ScheduleReuseAbort $exception) {
            return $exception->response;
        }

        return [
            'success' => true,
            'message' => "{$copied} schedule assignments were reused successfully.",
            'copied_count' => $copied,
        ];
    }

    private function prepare(
        string $scope,
        string $targetId,
        string $destinationSemesterId,
        string $sourceSemesterId,
        array $mappings,
        array $excluded
    ): array {
        if (!in_array($scope, ['pbs', 'pbt'], true)) {
            abort(404);
        }

        $departmentId = $this->chairDepartmentId();
        $collegeId = $this->chairCollegeId();
        abort_unless($departmentId && $collegeId, 403, 'Your chair account is not linked to a department and college.');

        $source = $this->semester($sourceSemesterId);
        $destination = $this->semester($destinationSemesterId);
        if (!$source || !$destination) {
            return $this->emptyPlan('The selected source or destination semester could not be found.');
        }
        if ((bool) ($source->ay_is_active ?? false)) {
            return $this->emptyPlan('Choose an archived academic year as the source.');
        }
        if (!$this->semesterIsWritable($destinationSemesterId)) {
            $message = $this->semesterLabel($destination)
                . ' is the matching semester in the current academic year, but schedule plotting is not enabled for it.';
            $message .= $destination->sem_name === '2nd Semester'
                ? ' Ask an administrator to enable Advance Plotting in Admin Settings.'
                : ' Ask an administrator to make this semester available for plotting.';

            return $this->emptyPlan($message, $source, $destination);
        }

        $sourceTerm = self::termNumber((string) $source->sem_name);
        $destinationTerm = self::termNumber((string) $destination->sem_name);
        if (!$sourceTerm || $sourceTerm !== $destinationTerm) {
            return $this->emptyPlan('Choose a destination semester with the same semester type as the archive.');
        }

        $sourceSection = null;
        if ($scope === 'pbs') {
            $sourceSection = Section::query()
                ->whereKey($targetId)
                ->where('sec_dept_id', $departmentId)
                ->where(function ($query) use ($source) {
                    $query->where('sec_sem_id', $source->sem_id)
                        ->orWhere(function ($fallback) use ($source) {
                            $fallback->whereNull('sec_sem_id')->where('sec_ay_id', $source->sem_ay_id);
                        });
                })
                ->first();
            if (!$sourceSection) {
                abort(403, 'You can only reuse archived sections assigned to your department.');
            }
        } else {
            $faculty = Faculty::query()
                ->whereKey($targetId)
                ->where('fac_is_placeholder', false)
                ->whereHas('user', fn ($query) => $query->where('usr_is_active', true))
                ->whereHas('program', fn ($query) => $query->where('dept_college_id', $collegeId))
                ->first();
            if (!$faculty) {
                abort(403, 'You can only reuse schedules for active faculty within your college.');
            }
        }

        $sourceRows = $this->sourceScheduleRows($sourceSemesterId, $scope, $targetId, $sourceSection);
        if (!$sourceRows) {
            return $this->emptyPlan(
                'No active schedules were found in the selected archived semester for this section or faculty.',
                $source,
                $destination
            );
        }

        $sections = $this->destinationSections($destination, $departmentId, $collegeId);
        if ($scope === 'pbs' && $sourceSection) {
            $exactMatches = $sections->filter(fn ($section) =>
                $this->sameSectionIdentity($sourceSection, $section)
            );
            if ($exactMatches->isEmpty()) {
                $sameName = $sections->first(fn ($section) =>
                    (string) $section->sec_dept_id === (string) $sourceSection->sec_dept_id
                    &&
                    mb_strtolower(trim((string) $section->sec_name))
                    === mb_strtolower(trim((string) $sourceSection->sec_name))
                );
                if ($sameName) {
                    return $this->emptyPlan(
                        'A current section has the archived section name but a different year level. Update the current section before reusing.',
                        $source,
                        $destination
                    );
                }

                $sections->push($this->createDestinationSection($sourceSection, $destination));
            }
        }

        $sourceSections = Section::with('program')
            ->whereIn('sec_id', collect($sourceRows)->pluck('section_id')->filter()->unique())
            ->get()->keyBy('sec_id');
        $sourceCourses = Course::query()
            ->whereIn('course_id', collect($sourceRows)->pluck('course_id')->filter()->unique())
            ->get()->keyBy('course_id');
        $sourceFaculty = Faculty::query()
            ->whereIn('fac_id', collect($sourceRows)->pluck('faculty_id')->filter()->unique())
            ->get()->keyBy('fac_id');
        if ($scope === 'pbt') {
            foreach ($sourceRows as $sourceRow) {
                $historicalSection = $sourceSections->get($sourceRow['section_id']);
                $course = $sourceCourses->get($sourceRow['course_id']);
                $ownerDepartment = $historicalSection?->program ?? $course?->program;

                if (!$historicalSection
                    || !$ownerDepartment
                    || (string) $ownerDepartment->dept_id !== $departmentId
                    || (string) $ownerDepartment->dept_college_id !== $collegeId) {
                    continue;
                }

                $exactMatches = $sections->filter(fn ($section) =>
                    $this->sameSectionIdentity($historicalSection, $section)
                );
                if ($exactMatches->isNotEmpty()) {
                    continue;
                }

                $sameName = $sections->first(fn ($section) =>
                    (string) $section->sec_dept_id === (string) $historicalSection->sec_dept_id
                    && mb_strtolower(trim((string) $section->sec_name))
                        === mb_strtolower(trim((string) $historicalSection->sec_name))
                );
                if (!$sameName) {
                    $sections->push($this->createDestinationSection($historicalSection, $destination));
                }
            }
        }

        $courses = Course::query()->where('course_is_active', true)->get();
        $rooms = Room::query()->where('room_is_available', true)->orderBy('room_name')->get();
        $excludedKeys = array_fill_keys($excluded, true);
        $rows = [];
        $assignments = [];

        foreach ($sourceRows as $sourceRow) {
            $key = $sourceRow['source_key'];
            $historicalSection = $sourceSections->get($sourceRow['section_id']);
            $course = $sourceCourses->get($sourceRow['course_id']);
            $faculty = $sourceFaculty->get($sourceRow['faculty_id']);
            $ownerDepartment = $historicalSection?->program ?? $course?->program;

            if ($scope === 'pbt' && (
                !$ownerDepartment
                || (string) $ownerDepartment->dept_id !== $departmentId
                || (string) $ownerDepartment->dept_college_id !== $collegeId
            )) {
                $rows[$key] = $this->makeRow(
                    $key,
                    $sourceRow,
                    $course,
                    $historicalSection,
                    null,
                    'excluded',
                    'This archived assignment belongs to another department. Only that department’s chair can reuse it.'
                );
                $rows[$key]['included'] = false;
                $rows[$key]['user_excludable'] = false;
                continue;
            }

            if (isset($excludedKeys[$key])) {
                $rows[$key] = $this->makeRow(
                    $key,
                    $sourceRow,
                    $course,
                    $historicalSection,
                    null,
                    'excluded',
                    'You chose not to include this assignment.'
                );
                $rows[$key]['included'] = false;
                continue;
            }

            $sectionResolution = $this->resolveSection(
                $historicalSection,
                $course,
                $sections,
                $mappings['sections'][$key] ?? null
            );
            $section = $sectionResolution['section'];
            $courseResolution = $this->resolveCourse(
                $course,
                $sourceRow,
                $courses,
                $section,
                $mappings['courses'][$key] ?? null
            );
            $targetCourse = $courseResolution['course'];
            $roomResolution = $this->resolveRoom(
                $sourceRow,
                $rooms,
                $mappings['rooms'][$key] ?? null
            );
            $targetRoom = $roomResolution['room'];
            $row = $this->makeRow(
                $key,
                $sourceRow,
                $targetCourse,
                $section,
                $targetRoom,
                'valid',
                ''
            );
            $row['section_options'] = $sectionResolution['options'];
            $row['course_options'] = $courseResolution['options'];
            $row['room_options'] = [];
            $row['included'] = true;

            if (!$section) {
                $row['status'] = 'needs_review';
                $row['mapping_kind'] = 'sections';
                $row['message'] = $sectionResolution['message'];
            } elseif (!$targetCourse) {
                $row['status'] = 'needs_review';
                $row['mapping_kind'] = 'courses';
                $row['message'] = $courseResolution['message'];
            } elseif (!$this->facultyIsUsable($faculty)) {
                $row['status'] = 'error';
                $row['message'] = 'The archived faculty member is no longer active.';
            } elseif (!$targetRoom && $targetCourse->course_code !== 'REC') {
                $row['status'] = 'needs_review';
                $row['mapping_kind'] = 'rooms';
                $row['message'] = $roomResolution['message'] ?: 'Choose an available room for this schedule.';
                $row['room_options'] = $this->roomOptions($rooms);
            } elseif ($roomResolution['invalid']) {
                $row['status'] = 'needs_review';
                $row['mapping_kind'] = 'rooms';
                $row['message'] = $roomResolution['message'];
                $row['room_options'] = $this->roomOptions($rooms);
            } else {
                $assignments[$key] = [
                    'subj_id' => $targetCourse->course_id,
                    'fac_id' => $sourceRow['faculty_id'],
                    'sec_id' => $section->sec_id,
                    'room_id' => $targetRoom?->room_id,
                    'sem_id' => $destinationSemesterId,
                    'day' => $sourceRow['day'],
                    'shift' => $sourceRow['shift'] ?: $this->derivedShift($sourceRow, $section),
                    'start_time' => substr((string) $sourceRow['start_time'], 0, 5),
                    'end_time' => substr((string) $sourceRow['end_time'], 0, 5),
                ];
            }

            $rows[$key] = $row;
        }

        return [
            'error' => null,
            'rows' => $rows,
            'assignments' => $assignments,
            'source_label' => $this->semesterLabel($source),
            'destination_label' => $this->semesterLabel($destination),
        ];
    }

    private function sourceScheduleRows(
        string $semesterId,
        string $scope,
        string $targetId,
        ?Section $sourceSection
    ): array {
        $schedules = DB::table('schedule as sch')
            ->leftJoin('course as c', 'c.course_id', '=', 'sch.sch_course_id')
            ->leftJoin('section as sec', 'sec.sec_id', '=', 'sch.sch_sec_id')
            ->leftJoin('faculty as fac', 'fac.fac_id', '=', 'sch.sch_fac_id')
            ->leftJoin('room as room', 'room.room_id', '=', 'sch.sch_room_id')
            ->where('sch.sch_sem_id', $semesterId)
            ->where('sch.sch_is_active', true);
        $this->applySourceFilter($schedules, $scope, $targetId, $sourceSection, 'sch.sch_fac_id', 'sch.sch_sec_id');

        $rows = [];
        foreach ($schedules->orderBy('sch.sch_day')->orderBy('sch.sch_start_time')->get([
            'sch.sch_id as record_id',
            'sch.sch_fac_id as faculty_id',
            'sch.sch_course_id as course_id',
            'sch.sch_sec_id as section_id',
            'sch.sch_room_id as room_id',
            'sch.sch_day as day',
            'sch.sch_start_time as start_time',
            'sch.sch_end_time as end_time',
            'sch.sch_shift as shift',
            'c.course_code',
            'c.course_name',
            'fac.fac_first_name',
            'fac.fac_last_name',
            'sec.sec_name as section_name',
            'room.room_name',
        ]) as $row) {
            $rows[] = $this->normalizeSourceRow($row, 'schedule');
        }

        return $rows;
    }

    private function applySourceFilter(
        Builder $query,
        string $scope,
        string $targetId,
        ?Section $sourceSection,
        string $facultyColumn,
        string $sectionColumn
    ): void {
        if ($scope === 'pbt') {
            $query->where($facultyColumn, $targetId);
            return;
        }

        $query->where($sectionColumn, $sourceSection?->sec_id ?? $targetId);
    }

    private function normalizeSourceRow(object $row, string $kind): array
    {
        return [
            'source_key' => $kind . ':' . $row->record_id,
            'faculty_id' => $row->faculty_id,
            'course_id' => $row->course_id,
            'section_id' => $row->section_id,
            'room_id' => $row->room_id,
            'day' => $row->day,
            'start_time' => $row->start_time,
            'end_time' => $row->end_time,
            'shift' => $row->shift,
            'course_code' => $row->course_code,
            'course_name' => $row->course_name,
            'faculty_name' => trim(($row->fac_first_name ?? '') . ' ' . ($row->fac_last_name ?? '')),
            'section_name' => $row->section_name,
            'room_name' => $row->room_name,
        ];
    }

    private function resolveSection(
        ?Section $sourceSection,
        ?Course $sourceCourse,
        Collection $eligibleSections,
        ?string $manualId
    ): array {
        $sourceDepartmentId = $sourceSection?->sec_dept_id ?? $sourceCourse?->course_dept_id;
        $sourceYearLevel = $sourceSection?->sec_year_level ?? $sourceCourse?->course_year_level;
        $candidates = $eligibleSections->filter(function ($section) use ($sourceDepartmentId, $sourceYearLevel) {
            return (!$sourceDepartmentId || (string) $section->sec_dept_id === (string) $sourceDepartmentId)
                && ($sourceYearLevel === null || (string) $section->sec_year_level === (string) $sourceYearLevel);
        })->values();

        if ($manualId) {
            $selected = $candidates->firstWhere('sec_id', $manualId);
            if ($selected) {
                return [
                    'section' => $selected,
                    'options' => $this->sectionOptions($candidates),
                    'message' => null,
                ];
            }
        }

        if ($sourceSection) {
            $exactMatches = $candidates->filter(fn ($section) => $this->sameSectionIdentity($sourceSection, $section));
            if ($exactMatches->count() === 1) {
                return [
                    'section' => $exactMatches->first(),
                    'options' => $this->sectionOptions($exactMatches),
                    'message' => null,
                ];
            }
        }

        if (!$sourceSection && $candidates->count() === 1) {
            return [
                'section' => $candidates->first(),
                'options' => $this->sectionOptions($candidates),
                'message' => null,
            ];
        }

        return [
            'section' => null,
            'options' => $this->sectionOptions($candidates),
            'message' => $candidates->isEmpty()
                ? 'No current section is available for this department and year level.'
                : 'Select the current destination section for this archived assignment.',
        ];
    }

    private function resolveCourse(
        ?Course $sourceCourse,
        array $sourceRow,
        Collection $eligibleCourses,
        ?Section $targetSection,
        ?string $manualId
    ): array {
        $archivedCourse = $sourceCourse
            ? $eligibleCourses->firstWhere('course_id', $sourceCourse->course_id)
            : null;
        if ($archivedCourse) {
            return ['course' => $archivedCourse, 'options' => [], 'message' => null];
        }

        $departmentId = $sourceCourse?->course_dept_id ?? $targetSection?->sec_dept_id;
        $courses = $eligibleCourses->filter(function ($course) use ($departmentId, $targetSection) {
            return (!$departmentId || (string) $course->course_dept_id === (string) $departmentId)
                && $this->courseFitsSection($course, $targetSection);
        })->values();
        $sourceCode = $sourceCourse?->course_code ?: ($sourceRow['course_code'] ?? null);
        $sameCode = $courses->filter(fn ($course) =>
            $sourceCode && strcasecmp((string) $course->course_code, (string) $sourceCode) === 0
        )->values();

        if ($manualId) {
            $selected = $courses->firstWhere('course_id', $manualId);
            if ($selected) {
                return ['course' => $selected, 'options' => $this->courseOptions($sameCode->isNotEmpty() ? $sameCode : $courses), 'message' => null];
            }
        }

        if ($sourceCourse
            && $courses->contains(fn ($course) => (string) $course->course_id === (string) $sourceCourse->course_id)) {
            return ['course' => $sourceCourse, 'options' => $this->courseOptions($sameCode), 'message' => null];
        }
        if ($sameCode->count() === 1) {
            return ['course' => $sameCode->first(), 'options' => $this->courseOptions($sameCode), 'message' => null];
        }

        return [
            'course' => null,
            'options' => $this->courseOptions($sameCode->isNotEmpty() ? $sameCode : $courses),
            'message' => $sameCode->isEmpty()
                ? 'No active course with a matching code is available for this semester. Choose an eligible course.'
                : 'Several active courses match this code. Select the correct current course.',
        ];
    }

    private function resolveRoom(array $sourceRow, Collection $rooms, ?string $manualId): array
    {
        if ($manualId) {
            $room = $rooms->firstWhere('room_id', $manualId);
            return [
                'room' => $room,
                'invalid' => !$room,
                'message' => $room ? null : 'Choose an available room.',
            ];
        }

        $room = $sourceRow['room_id'] ? $rooms->firstWhere('room_id', $sourceRow['room_id']) : null;
        return [
            'room' => $room,
            'invalid' => !empty($sourceRow['room_id']) && !$room,
            'message' => !$room ? 'The archived room is no longer available. Choose an available room.' : null,
        ];
    }

    private function destinationSections(object $destination, string $departmentId, string $collegeId): Collection
    {
        return Section::query()
            ->where(function ($query) use ($destination) {
                $query->where('sec_sem_id', $destination->sem_id)
                    ->orWhere(function ($fallback) use ($destination) {
                        $fallback->whereNull('sec_sem_id')->where('sec_ay_id', $destination->sem_ay_id);
                    });
            })
            ->where('sec_dept_id', $departmentId)
            ->whereHas('program', fn ($query) => $query->where('dept_college_id', $collegeId))
            ->where(function ($query) {
                $query->whereNull('sec_status')->orWhere('sec_status', 'active');
            })
            ->orderBy('sec_name')
            ->get();
    }

    private function sectionCandidates(Collection $sections, Section $source, ?Course $course): Collection
    {
        $departmentId = $source->sec_dept_id ?? $course?->course_dept_id;
        $yearLevel = $source->sec_year_level ?? $course?->course_year_level;
        return $sections->filter(fn ($section) =>
            (!$departmentId || (string) $section->sec_dept_id === (string) $departmentId)
            && ($yearLevel === null || (string) $section->sec_year_level === (string) $yearLevel)
        )->values();
    }

    private function createDestinationSection(Section $source, object $destination): Section
    {
        return Section::create([
            'sec_dept_id' => $source->sec_dept_id,
            'sec_ay_id' => $destination->sem_ay_id,
            'sec_sem_id' => $destination->sem_id,
            'sec_name' => $source->sec_name,
            'sec_year_level' => $source->sec_year_level,
            'sec_is_evening' => $source->sec_is_evening,
            'sec_no_of_student' => 0,
            'sec_max_capacity' => max(1, (int) ($source->sec_max_capacity ?: 40)),
            'sec_status' => 'active',
        ]);
    }

    private function simulate(array &$plan): void
    {
        $connection = DB::connection();
        $transactionLevel = $connection->transactionLevel();
        $connection->beginTransaction();

        try {
            foreach ($plan['assignments'] as $sourceKey => $assignment) {
                $assignment['workload_override'] = true;
                $assignment['workload_override_reason'] = 'Preview only; this transaction is rolled back.';
                $result = $this->scheduler->assign($assignment);
                $row = &$plan['rows'][$sourceKey];

                if (!empty($result['success'])) {
                    $override = $result['workload_override'] ?? null;
                    $row['status'] = $override ? 'workload_override' : 'valid';
                    if ($override) {
                        $row['message'] = sprintf(
                            'Faculty workload will be %s/%sh. A reason is required to continue.',
                            $override['hours_after'],
                            $override['cap_hours']
                        );
                        $row['workload'] = $override;
                    }
                } else {
                    $row['status'] = !empty($result['conflict']) ? 'conflict' : 'error';
                    $row['message'] = $result['message'] ?? 'This schedule could not be validated.';
                    if (!empty($result['conflict']) && str_starts_with((string) $row['message'], 'Room ')) {
                        $row['mapping_kind'] = 'rooms';
                        $row['room_options'] = $this->roomOptions(
                            Room::query()->where('room_is_available', true)->orderBy('room_name')->get()
                        );
                    }
                }
                unset($row);
            }
        } finally {
            $this->rollbackToTransactionLevel($transactionLevel);
        }
    }

    private function facultyIsUsable(?Faculty $faculty): bool
    {
        if (!$faculty) {
            return false;
        }
        return app(UnassignedFacultyService::class)->isPlaceholder($faculty->fac_id)
            || (bool) $faculty->user()->where('usr_is_active', true)->exists();
    }

    private function courseFitsSection(Course $course, ?Section $section): bool
    {
        return !$section
            || $course->course_code === 'REC'
            || !$course->course_year_level
            || (string) $course->course_year_level === (string) $section->sec_year_level;
    }

    private function sameSectionIdentity(Section $source, Section $target): bool
    {
        return (string) $source->sec_dept_id === (string) $target->sec_dept_id
            && mb_strtolower(trim((string) $source->sec_name)) === mb_strtolower(trim((string) $target->sec_name))
            && (string) $source->sec_year_level === (string) $target->sec_year_level;
    }

    private function derivedShift(array $sourceRow, Section $section): string
    {
        return $section->sec_is_evening || substr((string) $sourceRow['start_time'], 0, 5) >= '16:00'
            ? 'evening'
            : 'day';
    }

    private function makeRow(
        string $key,
        array $source,
        ?Course $course,
        ?Section $section,
        ?Room $room,
        string $status,
        string $message
    ): array {
        return [
            'source_key' => $key,
            'course_code' => $course?->course_code ?? $source['course_code'] ?? 'Course unavailable',
            'course_name' => $course?->course_name ?? $source['course_name'] ?? '',
            'faculty_name' => $source['faculty_name'] ?: 'Faculty unavailable',
            'section_name' => $section?->sec_name ?? $source['section_name'] ?? 'Section unavailable',
            'room_name' => $room?->room_name ?? $source['room_name'] ?? 'Room unavailable',
            'day' => $source['day'],
            'start_time' => substr((string) $source['start_time'], 0, 5),
            'end_time' => substr((string) $source['end_time'], 0, 5),
            'status' => $status,
            'message' => $message,
            'section_options' => [],
            'course_options' => [],
            'room_options' => [],
            'mapping_kind' => null,
            'included' => true,
            'user_excludable' => true,
        ];
    }

    private function summary(array $rows): array
    {
        $includedRows = array_filter($rows, fn ($row) => !empty($row['included']));
        $count = fn (string $status) => count(array_filter(
            $includedRows,
            fn ($row) => $row['status'] === $status
        ));

        return [
            'valid' => $count('valid'),
            'conflicts' => $count('conflict'),
            'needs_review' => $count('needs_review'),
            'errors' => $count('error'),
            'workload_overrides' => $count('workload_override'),
            'excluded' => count(array_filter($rows, fn ($row) => $row['status'] === 'excluded')),
            'blocked' => $count('conflict') + $count('needs_review') + $count('error'),
            'selected' => count($includedRows),
            'total' => count($rows),
        ];
    }

    private function previewMessage(array $summary, bool $hasAssignments): string
    {
        if ($summary['blocked'] > 0) {
            return 'Fix the reported conflicts or missing records, close this preview, then select Reuse Schedule again to check the latest data.';
        }
        if (!$hasAssignments) {
            return 'No assignments are ready to reuse.';
        }
        return 'All archived assignments passed checks. Confirm to copy them to the destination semester.';
    }

    private function previewPayload(array $rows, array $summary, bool $canCopy, array $plan): array
    {
        return [
            'rows' => $rows,
            'summary' => $summary,
            'can_copy' => $canCopy,
            'source_label' => $plan['source_label'],
            'destination_label' => $plan['destination_label'],
        ];
    }

    private function errorPreview(string $message, ?array $plan = null): array
    {
        return [
            'success' => false,
            'can_copy' => false,
            'message' => $message,
            'rows' => [],
            'summary' => $this->summary([]),
            'source_label' => $plan['source_label'] ?? '',
            'destination_label' => $plan['destination_label'] ?? '',
        ];
    }

    private function emptyPlan(string $error, ?object $source = null, ?object $destination = null): array
    {
        return [
            'error' => $error,
            'rows' => [],
            'assignments' => [],
            'source_label' => $source ? $this->semesterLabel($source) : '',
            'destination_label' => $destination ? $this->semesterLabel($destination) : '',
        ];
    }

    private function semester(string $semesterId): ?object
    {
        return DB::table('semester as sem')
            ->leftJoin('academic_year as ay', 'ay.ay_id', '=', 'sem.sem_ay_id')
            ->where('sem.sem_id', $semesterId)
            ->first([
                'sem.sem_id',
                'sem.sem_name',
                'sem.sem_ay_id',
                'sem.sem_is_active',
                'sem.sem_is_plotting_enabled',
                'sem.sem_start_date',
                'ay.ay_academic_year',
                'ay.ay_year_label',
                'ay.ay_is_active',
            ]);
    }

    private function semesterIsWritable(string $semesterId): bool
    {
        $semester = $this->semester($semesterId);
        if (!$semester || !(bool) ($semester->ay_is_active ?? false)) {
            return false;
        }

        if ((bool) $semester->sem_is_active) {
            return true;
        }

        $officialSemesterName = DB::table('semester')->where('sem_is_active', true)->value('sem_name');
        return $semester->sem_name === '2nd Semester'
            && $officialSemesterName === '1st Semester'
            && (bool) $semester->sem_is_plotting_enabled;
    }

    private function semesterLabel(object $semester): string
    {
        $year = $semester->ay_academic_year ?: $semester->ay_year_label ?: 'Academic year';
        return trim($year . ', ' . $semester->sem_name);
    }

    private function chairDepartmentId(): ?string
    {
        $id = Dept_Chair::query()->where('dc_usr_id', Auth::id())->value('dc_dept_id');
        return $id ? (string) $id : null;
    }

    private function chairCollegeId(): ?string
    {
        $departmentId = $this->chairDepartmentId();
        $id = $departmentId
            ? DB::table('department')->where('dept_id', $departmentId)->value('dept_college_id')
            : null;
        return $id ? (string) $id : null;
    }

    private function rollbackToTransactionLevel(int $level): void
    {
        $connection = DB::connection();
        while ($connection->transactionLevel() > $level) {
            $connection->rollBack();
        }
    }

    private function sectionOptions(Collection $sections): array
    {
        return $sections->map(fn ($section) => [
            'id' => (string) $section->sec_id,
            'label' => $section->sec_name . ' · Year ' . $section->sec_year_level,
        ])->values()->all();
    }

    private function courseOptions(Collection $courses): array
    {
        return $courses->map(fn ($course) => [
            'id' => (string) $course->course_id,
            'label' => $course->course_code . ' — ' . $course->course_name,
        ])->values()->all();
    }

    private function roomOptions(Collection $rooms): array
    {
        return $rooms->map(fn ($room) => [
            'id' => (string) $room->room_id,
            'label' => $room->room_name . ($room->room_type ? ', ' . $room->room_type : ''),
        ])->values()->all();
    }

    private static function termNumber(string $name): ?int
    {
        $name = mb_strtolower($name);
        if (str_contains($name, '1st') || str_contains($name, 'first')) {
            return 1;
        }
        if (str_contains($name, '2nd') || str_contains($name, 'second')) {
            return 2;
        }
        return null;
    }
}

class ScheduleReuseAbort extends RuntimeException
{
    public function __construct(public array $response)
    {
        parent::__construct((string) ($response['message'] ?? 'Schedule reuse failed.'));
    }
}
