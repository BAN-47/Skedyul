<?php

namespace Tests\Feature;

use App\Models\Faculty;
use App\Services\ScheduleAssignmentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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

    public function test_each_shift_has_its_own_30_hour_default_limit(): void
    {
        $fullTime = new Faculty(['fac_employment_type' => 'full_time']);
        $partTime = new Faculty(['fac_employment_type' => 'part_time']);

        $this->assertSame(30, ScheduleAssignmentService::TOTAL_MAX_HOURS);
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($fullTime, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($fullTime, 'evening'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($partTime, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($partTime, 'evening'));
    }

    public function test_perg_chair_day_workload_limit_is_16_hours(): void
    {
        $faculty = new Faculty([
            'fac_employment_type' => 'full_time',
            'fac_special_position' => 'PERG Chair',
        ]);

        $this->assertSame(16.0, ScheduleAssignmentService::facultyMaxHours($faculty, 'day'));
        $this->assertSame(30.0, ScheduleAssignmentService::facultyMaxHours($faculty, 'evening'));
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

    public function test_schedule_times_can_cross_the_day_evening_grid_boundary(): void
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

    public function test_remaining_evening_capacity_is_not_blocked_by_day_hours(): void
    {
        $facultyId = (string) Str::uuid();
        $semesterId = (string) Str::uuid();
        $this->addLoad($facultyId, $semesterId, 'day', 16);
        $this->addLoad($facultyId, $semesterId, 'evening', 13);

        $scheduler = app(ScheduleAssignmentService::class);
        $eveningAfterAssignment = $scheduler->facultyHoursForShift($facultyId, $semesterId, 'evening') + 9;

        $this->assertSame(16.0, $scheduler->facultyHoursForShift($facultyId, $semesterId, 'day'));
        $this->assertSame(13.0, $scheduler->facultyHoursForShift($facultyId, $semesterId, 'evening'));
        $this->assertSame(22.0, $eveningAfterAssignment);
        $this->assertLessThanOrEqual(30.0, $eveningAfterAssignment);
        $this->assertGreaterThan(30.0, $scheduler->facultyHours($facultyId, $semesterId) + 9);
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
}
