<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Models\Course;
use App\Models\Schedule;
use App\Services\ScheduleAssignmentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScheduleAssignmentWorkloadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_fac_id');
            $table->uuid('sl_course_id');
            $table->string('sl_shift');
            $table->uuid('sl_sem_id');
        });
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->unsignedInteger('day_cap');
            $table->unsignedInteger('evening_cap');
        });
    }

    public function test_faculty_hours_are_separated_by_semester_and_shift(): void
    {
        $facultyId = (string) Str::uuid();
        $firstSemesterId = (string) Str::uuid();
        $secondSemesterId = (string) Str::uuid();

        $this->addLoad($facultyId, $firstSemesterId, 'day', 8);
        $this->addLoad($facultyId, $firstSemesterId, 'evening', 4);
        $this->addLoad($facultyId, $secondSemesterId, 'day', 12);
        $this->addLoad($facultyId, $secondSemesterId, 'evening', 3);

        $scheduler = app(ScheduleAssignmentService::class);

        $this->assertSame(8.0, $scheduler->facultyHoursForShift($facultyId, $firstSemesterId, 'day'));
        $this->assertSame(4.0, $scheduler->facultyHoursForShift($facultyId, $firstSemesterId, 'evening'));
        $this->assertSame(12.0, $scheduler->facultyHours($facultyId, $firstSemesterId));
        $this->assertSame(12.0, $scheduler->facultyHoursForShift($facultyId, $secondSemesterId, 'day'));
        $this->assertSame(3.0, $scheduler->facultyHoursForShift($facultyId, $secondSemesterId, 'evening'));
    }

    public function test_part_time_limit_is_shared_across_day_and_evening(): void
    {
        $fullTime = new Faculty(['fac_employment_type' => 'full_time']);
        $partTime = new Faculty(['fac_employment_type' => 'part_time']);

        $this->assertSame(30, ScheduleAssignmentService::TOTAL_MAX_HOURS);
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($fullTime, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($fullTime, 'evening'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($partTime, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($partTime, 'evening'));

        $facultyId = (string) Str::uuid();
        $semesterId = (string) Str::uuid();
        $this->addLoad($facultyId, $semesterId, 'day', 16);
        $this->addLoad($facultyId, $semesterId, 'evening', 13);

        $scheduler = app(ScheduleAssignmentService::class);
        $partTimeHours = $scheduler->facultyHoursForLimit($partTime, $facultyId, $semesterId, 'evening');
        $this->assertSame(29.0, $partTimeHours);
        $this->assertGreaterThan(
            ScheduleAssignmentService::facultyMaxHours($partTime, 'evening'),
            $partTimeHours + 2
        );

        $fullTimeHours = $scheduler->facultyHoursForLimit($fullTime, $facultyId, $semesterId, 'evening');
        $this->assertSame(13.0, $fullTimeHours);
    }

    public function test_perg_chair_day_workload_limit_is_16_hours(): void
    {
        DB::table('designations')->insert([
            'id' => (string) Str::uuid(),
            'name' => 'PERG Chair',
            'day_cap' => 16,
            'evening_cap' => 30,
        ]);
        $faculty = new Faculty([
            'fac_employment_type' => 'full_time',
            'fac_special_position' => 'PERG Chair',
        ]);

        $this->assertSame(16.0, ScheduleAssignmentService::facultyMaxHours($faculty, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($faculty, 'evening'));
    }

    public function test_over_cap_assignment_requires_a_reason_and_returns_20_of_16_analytics(): void
    {
        $faculty = (new Faculty())->forceFill([
            'fac_first_name' => 'Taylor',
            'fac_last_name' => 'Faculty',
            'fac_special_position' => 'PERG Chair',
        ]);
        $course = (new Course())->forceFill(['course_code' => 'COMP-101']);

        try {
            $this->checkWorkloadLimit([], $faculty, $course, 16, 4, 20, 16, 'day');
            $this->fail('The first over-cap attempt should require an explicit override.');
        } catch (\RuntimeException $exception) {
            $this->assertStringStartsWith('WORKLOAD_OVERRIDE_REQUIRED:', $exception->getMessage());
            $details = json_decode(substr($exception->getMessage(), strlen('WORKLOAD_OVERRIDE_REQUIRED:')), true);
            $this->assertSame(20.0, (float) $details['hours_after']);
            $this->assertSame(16.0, (float) $details['cap_hours']);
            $this->assertSame(4.0, (float) $details['over_by']);
        }

        try {
            $this->checkWorkloadLimit(['workload_override' => true], $faculty, $course, 16, 4, 20, 16, 'day');
            $this->fail('An override without a reason should be rejected.');
        } catch (\RuntimeException $exception) {
            $this->assertSame(
                'LOAD_LIMIT:A reason is required to override the faculty workload cap.',
                $exception->getMessage()
            );
        }

        $override = $this->checkWorkloadLimit([
            'workload_override' => true,
            'workload_override_reason' => 'Department coverage approved.',
        ], $faculty, $course, 16, 4, 20, 16, 'day');

        $this->assertSame(20.0, $override['hours_after']);
        $this->assertSame(16.0, $override['cap_hours']);
        $this->assertSame('Department coverage approved.', $override['reason']);
    }

    public function test_workload_override_is_persisted_once_per_study_load(): void
    {
        Schema::create('workload_overrides', function (Blueprint $table) {
            $table->uuid('wlo_id')->primary();
            $table->uuid('wlo_load_id')->unique();
            $table->uuid('wlo_fac_id');
            $table->uuid('wlo_course_id');
            $table->uuid('wlo_section_id')->nullable();
            $table->uuid('wlo_semester_id');
            $table->uuid('wlo_approved_by');
            $table->string('wlo_shift');
            $table->string('wlo_designation')->nullable();
            $table->decimal('wlo_hours_before', 8, 2);
            $table->decimal('wlo_hours_added', 8, 2);
            $table->decimal('wlo_hours_after', 8, 2);
            $table->decimal('wlo_cap_hours', 8, 2);
            $table->text('wlo_reason');
            $table->timestamps();
        });

        $facultyId = (string) Str::uuid();
        $courseId = (string) Str::uuid();
        $loadId = (string) Str::uuid();
        $sectionId = (string) Str::uuid();
        $semesterId = (string) Str::uuid();
        $approverId = (string) Str::uuid();
        $faculty = (new Faculty())->forceFill(['fac_id' => $facultyId]);
        $course = (new Course())->forceFill(['course_id' => $courseId]);
        $load = (new \App\Models\Study_Load())->forceFill(['sl_id' => $loadId]);
        $data = ['sec_id' => $sectionId, 'sem_id' => $semesterId];
        $details = [
            'shift' => 'day',
            'designation' => 'PERG Chair',
            'hours_before' => 16.0,
            'hours_added' => 4.0,
            'hours_after' => 20.0,
            'cap_hours' => 16.0,
            'reason' => 'Department coverage approved.',
        ];
        Auth::shouldReceive('id')->once()->andReturn($approverId);
        $method = new \ReflectionMethod(ScheduleAssignmentService::class, 'recordWorkloadOverride');
        $method->setAccessible(true);
        $scheduler = app(ScheduleAssignmentService::class);

        $method->invoke($scheduler, $data, $faculty, $course, $load, $details);
        $method->invoke($scheduler, $data, $faculty, $course, $load, $details);

        $this->assertSame(1, DB::table('workload_overrides')->count());
        $this->assertSame(20.0, (float) DB::table('workload_overrides')->value('wlo_hours_after'));
        $this->assertSame(16.0, (float) DB::table('workload_overrides')->value('wlo_cap_hours'));
        $this->assertSame('Department coverage approved.', DB::table('workload_overrides')->value('wlo_reason'));
    }

    public function test_evening_section_meetings_use_evening_load_even_in_day_time_slots(): void
    {
        $this->assertSame(
            'evening',
            ScheduleAssignmentService::workloadShiftForAssignment(true, 'day')
        );
    }

    public function test_existing_evening_course_load_stays_evening_for_day_time_meetings(): void
    {
        $this->assertSame(
            'evening',
            ScheduleAssignmentService::workloadShiftForAssignment(false, 'day', 'evening', true)
        );
    }

    public function test_base_time_validator_accepts_cross_boundary_range_for_shift_specific_validation(): void
    {
        $validator = Validator::make([
            'start_time' => '13:00',
            'end_time' => '18:00',
        ], [
            'start_time' => [
                'required',
                'date_format:H:i',
                'regex:/^\d{2}:[03]0$/',
                'after_or_equal:07:00',
                'before:22:00',
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'regex:/^\d{2}:[03]0$/',
                'after:start_time',
                'before_or_equal:22:00',
            ],
        ]);

        $this->assertTrue($validator->passes(), $validator->errors()->first());

        $validator->setData([
            'start_time' => '06:30',
            'end_time' => '14:00',
        ]);
        $this->assertFalse($validator->passes());

        $validator->setData([
            'start_time' => '21:30',
            'end_time' => '22:00',
        ]);
        $this->assertTrue($validator->passes(), $validator->errors()->first());
    }

    public function test_weekday_day_and_evening_schedules_respect_the_four_pm_boundary(): void
    {
        $this->assertSame(
            'For evening sections, 7:00 AM–4:00 PM schedules are allowed on Saturday or Sunday only. Monday–Friday evening schedules must start at or after 4:00 PM.',
            ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Monday',
            'shift' => 'evening',
            'start_time' => '15:30',
            'end_time' => '18:00',
            ])
        );

        $this->assertNotNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Monday',
            'shift' => 'day',
            'start_time' => '13:00',
            'end_time' => '18:00',
        ]));

        $this->assertNotNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Monday',
            'shift' => 'day',
            'start_time' => '13:00',
            'end_time' => '16:00',
        ], sectionIsEvening: true));

        $this->assertNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Monday',
            'shift' => 'day',
            'start_time' => '13:00',
            'end_time' => '16:00',
        ]));

        $this->assertNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Monday',
            'shift' => 'evening',
            'start_time' => '16:00',
            'end_time' => '18:00',
        ]));

        // Evening sections can still use daytime slots on weekends.
        $this->assertNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Saturday',
            'shift' => 'day',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ], sectionIsEvening: true));

        $this->assertNull(ScheduleAssignmentService::scheduleTimeMessage([
            'day' => 'Sunday',
            'shift' => 'day',
            'start_time' => '07:00',
            'end_time' => '10:00',
        ], sectionIsEvening: true));
    }

    public function test_full_time_evening_capacity_remains_separate_from_day_hours(): void
    {
        $facultyId = (string) Str::uuid();
        $semesterId = (string) Str::uuid();
        $this->addLoad($facultyId, $semesterId, 'day', 16);
        $this->addLoad($facultyId, $semesterId, 'evening', 13);

        $scheduler = app(ScheduleAssignmentService::class);
        $fullTime = new Faculty(['fac_employment_type' => 'full_time']);
        $eveningAfterAssignment = $scheduler->facultyHoursForLimit($fullTime, $facultyId, $semesterId, 'evening') + 9;

        $this->assertSame(16.0, $scheduler->facultyHoursForShift($facultyId, $semesterId, 'day'));
        $this->assertSame(13.0, $scheduler->facultyHoursForShift($facultyId, $semesterId, 'evening'));
        $this->assertSame(22.0, $eveningAfterAssignment);
        $this->assertLessThanOrEqual(30.0, $eveningAfterAssignment);
        $this->assertGreaterThan(30.0, $scheduler->facultyHours($facultyId, $semesterId) + 9);
    }

    public function test_scheduled_hours_sum_active_meeting_durations_not_course_workload_hours(): void
    {
        $loads = collect([
            (object) [
                'schedules' => collect([
                    new Schedule(['sch_is_active' => true, 'sch_start_time' => '07:00', 'sch_end_time' => '12:00']),
                    new Schedule(['sch_is_active' => false, 'sch_start_time' => '13:00', 'sch_end_time' => '15:00']),
                ]),
            ],
        ]);

        $this->assertSame(5.0, ScheduleAssignmentService::scheduledHoursForLoads($loads));
    }

    private function addLoad(string $facultyId, string $semesterId, string $shift, float $hours): void
    {
        $courseId = (string) Str::uuid();
        DB::table('course')->insert([
            'course_id' => $courseId,
            'course_lecture_hours' => $hours,
            'course_lab_hours' => 0,
        ]);
        DB::table('study_load')->insert([
            'sl_id' => (string) Str::uuid(),
            'sl_fac_id' => $facultyId,
            'sl_course_id' => $courseId,
            'sl_shift' => $shift,
            'sl_sem_id' => $semesterId,
        ]);
    }

    private function checkWorkloadLimit(
        array $data,
        Faculty $faculty,
        Course $course,
        float $hoursBefore,
        float $hoursAdded,
        float $hoursAfter,
        float $capHours,
        string $shift
    ): ?array {
        $method = new \ReflectionMethod(ScheduleAssignmentService::class, 'checkWorkloadLimit');
        $method->setAccessible(true);

        return $method->invoke(
            app(ScheduleAssignmentService::class),
            $data,
            $faculty,
            $course,
            $hoursBefore,
            $hoursAdded,
            $hoursAfter,
            $capHours,
            $shift
        );
    }
}
