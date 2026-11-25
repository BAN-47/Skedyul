<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ScheduleAssignmentService;
use App\Services\ScheduleReuseService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Mockery;
use Tests\TestCase;

class ScheduleReuseServiceTest extends TestCase
{
    private string $chairId;
    private string $collegeId;
    private string $departmentId;
    private string $sourceYearId;
    private string $destinationYearId;
    private string $sourceSemesterId;
    private string $sourceSecondSemesterId;
    private string $destinationSemesterId;
    private string $destinationSecondSemesterId;
    private string $facultyId;
    private string $courseId;
    private string $secondCourseId;
    private string $sourceSectionId;
    private string $destinationSectionId;
    private string $roomId;
    private string $firstArchiveId;
    private string $secondArchiveId;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('USER', function (Blueprint $table) {
            $table->uuid('usr_id')->primary();
            $table->string('usr_name')->nullable();
            $table->string('usr_email')->nullable();
            $table->string('usr_password_hash')->nullable();
            $table->string('usr_role')->nullable();
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
            $table->string('ay_academic_year')->nullable();
            $table->string('ay_year_label')->nullable();
            $table->boolean('ay_is_active')->default(false);
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id')->nullable();
            $table->string('sem_name');
            $table->date('sem_start_date')->nullable();
            $table->boolean('sem_is_active')->default(false);
            $table->boolean('sem_is_plotting_enabled')->default(false);
        });
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
            $table->uuid('dept_college_id');
            $table->string('dept_name')->nullable();
            $table->string('dept_code')->nullable();
        });
        Schema::create('department_chair', function (Blueprint $table) {
            $table->uuid('dc_id')->primary();
            $table->uuid('dc_usr_id');
            $table->uuid('dc_dept_id');
            $table->uuid('dc_college_id')->nullable();
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->uuid('fac_usr_id')->nullable();
            $table->uuid('fac_dept_id')->nullable();
            $table->string('fac_first_name')->nullable();
            $table->string('fac_last_name')->nullable();
            $table->string('fac_employment_type')->nullable();
            $table->string('fac_special_position')->nullable();
            $table->boolean('fac_is_placeholder')->default(false);
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->uuid('course_dept_id')->nullable();
            $table->string('course_code')->nullable();
            $table->string('course_name')->nullable();
            $table->decimal('course_units')->nullable();
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
            $table->integer('course_year_level')->nullable();
            $table->integer('course_semester')->nullable();
            $table->boolean('course_is_active')->default(true);
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->uuid('sec_dept_id')->nullable();
            $table->uuid('sec_ay_id')->nullable();
            $table->uuid('sec_sem_id')->nullable();
            $table->string('sec_name');
            $table->integer('sec_year_level')->nullable();
            $table->boolean('sec_is_evening')->default(false);
            $table->integer('sec_no_of_student')->default(0);
            $table->integer('sec_max_capacity')->default(40);
            $table->string('sec_status')->default('active');
            $table->timestamp('sec_created_at')->nullable();
            $table->timestamp('sec_updated_at')->nullable();
        });
        Schema::create('room', function (Blueprint $table) {
            $table->uuid('room_id')->primary();
            $table->string('room_name');
            $table->string('room_type')->nullable();
            $table->boolean('room_is_available')->default(true);
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_load_id')->nullable();
            $table->uuid('sch_fac_id')->nullable();
            $table->uuid('sch_course_id')->nullable();
            $table->uuid('sch_sec_id')->nullable();
            $table->uuid('sch_room_id')->nullable();
            $table->uuid('sch_sem_id')->nullable();
            $table->string('sch_day')->nullable();
            $table->string('sch_shift')->nullable();
            $table->string('sch_start_time')->nullable();
            $table->string('sch_end_time')->nullable();
            $table->boolean('sch_is_active')->default(true);
            $table->string('sch_status')->nullable();
            $table->uuid('sch_created_by')->nullable();
        });
        Schema::create('schedule_archives', function (Blueprint $table) {
            $table->uuid('sar_id')->primary();
            $table->uuid('sar_sch_id')->nullable();
            $table->uuid('sar_sem_id')->nullable();
            $table->uuid('sar_ay_id')->nullable();
            $table->uuid('sar_fac_id')->nullable();
            $table->uuid('sar_course_id')->nullable();
            $table->uuid('sar_sec_id')->nullable();
            $table->uuid('sar_room_id')->nullable();
            $table->string('sar_day')->nullable();
            $table->string('sar_start_time')->nullable();
            $table->string('sar_end_time')->nullable();
            $table->string('sar_shift')->nullable();
            $table->boolean('sar_is_active')->default(false);
            $table->timestamp('sar_archived_at')->nullable();
        });

        $this->chairId = (string) Str::uuid();
        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        $this->sourceYearId = (string) Str::uuid();
        $this->destinationYearId = (string) Str::uuid();
        $this->sourceSemesterId = (string) Str::uuid();
        $this->sourceSecondSemesterId = (string) Str::uuid();
        $this->destinationSemesterId = (string) Str::uuid();
        $this->destinationSecondSemesterId = (string) Str::uuid();
        $this->facultyId = (string) Str::uuid();
        $this->courseId = (string) Str::uuid();
        $this->secondCourseId = (string) Str::uuid();
        $this->sourceSectionId = (string) Str::uuid();
        $this->destinationSectionId = (string) Str::uuid();
        $this->roomId = (string) Str::uuid();
        $this->firstArchiveId = (string) Str::uuid();
        $this->secondArchiveId = (string) Str::uuid();

        $chair = User::create([
            'usr_id' => $this->chairId,
            'usr_name' => 'Department Chair',
            'usr_email' => 'chair@example.test',
            'usr_password_hash' => 'test',
            'usr_role' => 'department_chair',
            'usr_is_active' => true,
        ]);
        Auth::login($chair);

        DB::table('department')->insert([
            'dept_id' => $this->departmentId,
            'dept_college_id' => $this->collegeId,
            'dept_name' => 'Computer Science',
            'dept_code' => 'CS',
        ]);
        DB::table('department_chair')->insert([
            'dc_id' => (string) Str::uuid(),
            'dc_usr_id' => $this->chairId,
            'dc_dept_id' => $this->departmentId,
        ]);
        DB::table('academic_year')->insert([
            [
                'ay_id' => $this->sourceYearId,
                'ay_academic_year' => '2025-2026',
                'ay_year_label' => '2025-2026',
                'ay_is_active' => false,
            ],
            [
                'ay_id' => $this->destinationYearId,
                'ay_academic_year' => '2026-2027',
                'ay_year_label' => '2026-2027',
                'ay_is_active' => true,
            ],
        ]);
        DB::table('semester')->insert([
            [
                'sem_id' => $this->sourceSemesterId,
                'sem_ay_id' => $this->sourceYearId,
                'sem_name' => '1st Semester',
                'sem_is_active' => false,
                'sem_is_plotting_enabled' => false,
            ],
            [
                'sem_id' => $this->destinationSemesterId,
                'sem_ay_id' => $this->destinationYearId,
                'sem_name' => '1st Semester',
                'sem_is_active' => true,
                'sem_is_plotting_enabled' => false,
            ],
            [
                'sem_id' => $this->sourceSecondSemesterId,
                'sem_ay_id' => $this->sourceYearId,
                'sem_name' => '2nd Semester',
                'sem_is_active' => false,
                'sem_is_plotting_enabled' => false,
            ],
            [
                'sem_id' => $this->destinationSecondSemesterId,
                'sem_ay_id' => $this->destinationYearId,
                'sem_name' => '2nd Semester',
                'sem_is_active' => false,
                'sem_is_plotting_enabled' => true,
            ],
        ]);

        $facultyUserId = (string) Str::uuid();
        DB::table('USER')->insert([
            'usr_id' => $facultyUserId,
            'usr_name' => 'Taylor Faculty',
            'usr_email' => 'faculty@example.test',
            'usr_password_hash' => 'test',
            'usr_role' => 'faculty',
            'usr_is_active' => true,
        ]);
        DB::table('faculty')->insert([
            'fac_id' => $this->facultyId,
            'fac_usr_id' => $facultyUserId,
            'fac_dept_id' => $this->departmentId,
            'fac_first_name' => 'Taylor',
            'fac_last_name' => 'Faculty',
            'fac_employment_type' => 'full_time',
            'fac_is_placeholder' => false,
        ]);
        DB::table('course')->insert([
            [
                'course_id' => $this->courseId,
                'course_dept_id' => $this->departmentId,
                'course_code' => 'CS101',
                'course_name' => 'Intro to Computing',
                'course_units' => 3,
                'course_lecture_hours' => 2,
                'course_year_level' => 1,
                'course_semester' => 1,
                'course_is_active' => true,
            ],
            [
                'course_id' => $this->secondCourseId,
                'course_dept_id' => $this->departmentId,
                'course_code' => 'CS102',
                'course_name' => 'Programming',
                'course_units' => 3,
                'course_lecture_hours' => 2,
                'course_year_level' => 1,
                'course_semester' => 1,
                'course_is_active' => true,
            ],
        ]);
        DB::table('section')->insert([
            [
                'sec_id' => $this->sourceSectionId,
                'sec_dept_id' => $this->departmentId,
                'sec_ay_id' => $this->sourceYearId,
                'sec_sem_id' => $this->sourceSemesterId,
                'sec_name' => 'CS-1A',
                'sec_year_level' => 1,
            ],
            [
                'sec_id' => $this->destinationSectionId,
                'sec_dept_id' => $this->departmentId,
                'sec_ay_id' => $this->destinationYearId,
                'sec_sem_id' => $this->destinationSemesterId,
                'sec_name' => 'CS-1A',
                'sec_year_level' => 1,
            ],
        ]);
        DB::table('room')->insert([
            'room_id' => $this->roomId,
            'room_name' => 'Room 101',
            'room_is_available' => true,
        ]);
        $firstScheduleId = (string) Str::uuid();
        $secondScheduleId = (string) Str::uuid();
        DB::table('schedule_archives')->insert([
            [
                'sar_id' => $this->firstArchiveId,
                'sar_sch_id' => $firstScheduleId,
                'sar_sem_id' => $this->sourceSemesterId,
                'sar_ay_id' => $this->sourceYearId,
                'sar_fac_id' => $this->facultyId,
                'sar_course_id' => $this->courseId,
                'sar_sec_id' => $this->sourceSectionId,
                'sar_room_id' => $this->roomId,
                'sar_day' => 'Monday',
                'sar_start_time' => '08:00',
                'sar_end_time' => '10:00',
                'sar_shift' => 'day',
                'sar_is_active' => true,
                'sar_archived_at' => now(),
            ],
            [
                'sar_id' => $this->secondArchiveId,
                'sar_sch_id' => $secondScheduleId,
                'sar_sem_id' => $this->sourceSemesterId,
                'sar_ay_id' => $this->sourceYearId,
                'sar_fac_id' => $this->facultyId,
                'sar_course_id' => $this->secondCourseId,
                'sar_sec_id' => $this->sourceSectionId,
                'sar_room_id' => $this->roomId,
                'sar_day' => 'Tuesday',
                'sar_start_time' => '08:00',
                'sar_end_time' => '10:00',
                'sar_shift' => 'day',
                'sar_is_active' => true,
                'sar_archived_at' => now(),
            ],
        ]);
        DB::table('schedule')->insert([
            [
                'sch_id' => $firstScheduleId,
                'sch_fac_id' => $this->facultyId,
                'sch_course_id' => $this->courseId,
                'sch_sec_id' => $this->sourceSectionId,
                'sch_room_id' => $this->roomId,
                'sch_sem_id' => $this->sourceSemesterId,
                'sch_day' => 'Monday',
                'sch_shift' => 'day',
                'sch_start_time' => '08:00',
                'sch_end_time' => '10:00',
                'sch_is_active' => true,
                'sch_status' => 'draft',
                'sch_created_by' => $this->chairId,
            ],
            [
                'sch_id' => $secondScheduleId,
                'sch_fac_id' => $this->facultyId,
                'sch_course_id' => $this->secondCourseId,
                'sch_sec_id' => $this->sourceSectionId,
                'sch_room_id' => $this->roomId,
                'sch_sem_id' => $this->sourceSemesterId,
                'sch_day' => 'Tuesday',
                'sch_shift' => 'day',
                'sch_start_time' => '08:00',
                'sch_end_time' => '10:00',
                'sch_is_active' => true,
                'sch_status' => 'draft',
                'sch_created_by' => $this->chairId,
            ],
        ]);
    }

    public function test_destination_semester_matches_the_archived_semester_type(): void
    {
        $reuse = new ScheduleReuseService(Mockery::mock(ScheduleAssignmentService::class));

        $this->assertSame(
            $this->destinationSemesterId,
            $reuse->destinationSemesterForSource($this->sourceSemesterId)?->sem_id
        );
        $this->assertSame(
            $this->destinationSecondSemesterId,
            $reuse->destinationSemesterForSource($this->sourceSecondSemesterId)?->sem_id
        );
    }

    public function test_matching_future_semester_is_reported_when_advance_plotting_is_disabled(): void
    {
        DB::table('semester')
            ->where('sem_id', $this->destinationSecondSemesterId)
            ->update(['sem_is_plotting_enabled' => false]);

        $reuse = new ScheduleReuseService(Mockery::mock(ScheduleAssignmentService::class));

        $this->assertSame(
            $this->destinationSecondSemesterId,
            $reuse->destinationSemesterForSource($this->sourceSecondSemesterId)?->sem_id
        );

        $preview = $reuse->preview(
            'pbt',
            $this->facultyId,
            $this->destinationSecondSemesterId,
            $this->sourceSecondSemesterId
        );

        $this->assertFalse($preview['can_copy']);
        $this->assertSame('2025-2026, 2nd Semester', $preview['source_label']);
        $this->assertSame('2026-2027, 2nd Semester', $preview['destination_label']);
        $this->assertStringContainsString('Advance Plotting', $preview['message']);
    }

    public function test_pbs_preview_uses_archived_section_and_simulates_without_persisting_schedules(): void
    {
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->twice()->andReturnUsing(function (array $assignment) {
            $this->assertSame($this->destinationSectionId, $assignment['sec_id']);
            $this->insertSimulatedSchedule($assignment);
            return ['success' => true, 'workload_override' => null];
        });

        $preview = (new ScheduleReuseService($scheduler))->preview(
            'pbs',
            $this->sourceSectionId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertTrue($preview['can_copy']);
        $this->assertSame(2, $preview['summary']['valid']);
        $this->assertSame(2, $preview['summary']['total']);
        $this->assertSame(0, DB::table('schedule')->where('sch_sem_id', $this->destinationSemesterId)->count());
    }

    public function test_reuse_ignores_archival_snapshots_without_active_schedule_rows(): void
    {
        DB::table('schedule')->where('sch_sem_id', $this->sourceSemesterId)->delete();
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldNotReceive('assign');

        $preview = (new ScheduleReuseService($scheduler))->preview(
            'pbt',
            $this->facultyId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertFalse($preview['can_copy']);
        $this->assertSame(0, $preview['summary']['total']);
        $this->assertStringContainsString('No active schedules', $preview['message']);
    }

    public function test_pbs_preview_creates_the_archived_section_when_the_current_name_differs(): void
    {
        DB::table('section')->where('sec_id', $this->destinationSectionId)->update(['sec_name' => 'CS-1B']);
        $createdSectionIds = [];
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->twice()->andReturnUsing(function (array $assignment) use (&$createdSectionIds) {
            $createdSectionIds[] = $assignment['sec_id'];
            $this->assertSame('CS-1A', DB::table('section')->where('sec_id', $assignment['sec_id'])->value('sec_name'));
            return ['success' => true, 'workload_override' => null];
        });
        $preview = (new ScheduleReuseService($scheduler))->preview(
            'pbs',
            $this->sourceSectionId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertTrue($preview['can_copy']);
        $this->assertSame(2, $preview['summary']['valid']);
        $this->assertSame(1, count(array_unique($createdSectionIds)));
        $this->assertSame(2, DB::table('section')->count());
        $this->assertSame(0, DB::table('schedule')->where('sch_sem_id', $this->destinationSemesterId)->count());
    }

    public function test_pbs_preview_creates_a_temporary_destination_section_but_does_not_save_it(): void
    {
        DB::table('section')->where('sec_id', $this->destinationSectionId)->delete();
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->twice()->andReturnUsing(function (array $assignment) {
            $this->assertSame('CS-1A', DB::table('section')->where('sec_id', $assignment['sec_id'])->value('sec_name'));
            $this->insertSimulatedSchedule($assignment);
            return ['success' => true, 'workload_override' => null];
        });

        $preview = (new ScheduleReuseService($scheduler))->preview(
            'pbs',
            $this->sourceSectionId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertTrue($preview['can_copy']);
        $this->assertSame(2, $preview['summary']['valid']);
        $this->assertSame(0, DB::table('section')->where('sec_sem_id', $this->destinationSemesterId)->count());
        $this->assertSame(0, DB::table('schedule')->where('sch_sem_id', $this->destinationSemesterId)->count());
    }

    public function test_pbs_apply_creates_the_current_section_and_copies_schedules_atomically(): void
    {
        DB::table('section')->where('sec_id', $this->destinationSectionId)->delete();
        $lastSectionId = null;
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->times(6)->andReturnUsing(function (array $assignment) use (&$lastSectionId) {
            $lastSectionId = $assignment['sec_id'];
            $this->insertSimulatedSchedule($assignment);
            return ['success' => true, 'workload_override' => null];
        });

        $result = (new ScheduleReuseService($scheduler))->apply(
            'pbs',
            $this->sourceSectionId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertTrue($result['success']);
        $this->assertSame(2, $result['copied_count']);
        $this->assertDatabaseHas('section', [
            'sec_id' => $lastSectionId,
            'sec_dept_id' => $this->departmentId,
            'sec_ay_id' => $this->destinationYearId,
            'sec_sem_id' => $this->destinationSemesterId,
            'sec_name' => 'CS-1A',
            'sec_year_level' => 1,
            'sec_is_evening' => false,
            'sec_no_of_student' => 0,
        ]);
        $this->assertSame(2, DB::table('schedule')->where('sch_sem_id', $this->destinationSemesterId)->count());
        $this->assertDatabaseHas('schedule', [
            'sch_sem_id' => $this->destinationSemesterId,
            'sch_sec_id' => $lastSectionId,
        ]);
    }

    public function test_pbs_apply_rolls_back_all_schedules_if_one_assignment_fails(): void
    {
        $call = 0;
        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->times(6)->andReturnUsing(function (array $assignment) use (&$call) {
            $call++;
            if ($call === 6) {
                return ['success' => false, 'conflict' => true, 'message' => 'Room 101 is occupied.'];
            }
            $this->insertSimulatedSchedule($assignment);
            return ['success' => true, 'workload_override' => null];
        });

        $result = (new ScheduleReuseService($scheduler))->apply(
            'pbs',
            $this->sourceSectionId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertFalse($result['success']);
        $this->assertSame(0, DB::table('schedule')->where('sch_sem_id', $this->destinationSemesterId)->count());
        $this->assertDatabaseCount('section', 2);
    }

    public function test_pbt_preview_creates_missing_current_sections_and_keeps_archived_course_identity(): void
    {
        $sourceSectionId = (string) Str::uuid();
        $archiveId = (string) Str::uuid();
        $scheduleId = (string) Str::uuid();
        DB::table('course')->where('course_id', $this->courseId)->update([
            'course_semester' => 1,
            'course_year_level' => 2,
        ]);
        DB::table('section')->insert([
            'sec_id' => $sourceSectionId,
            'sec_dept_id' => $this->departmentId,
            'sec_ay_id' => $this->sourceYearId,
            'sec_sem_id' => $this->sourceSecondSemesterId,
            'sec_name' => 'CS-1B',
            'sec_year_level' => 1,
        ]);
        DB::table('schedule_archives')->insert([
            'sar_id' => $archiveId,
            'sar_sch_id' => $scheduleId,
            'sar_sem_id' => $this->sourceSecondSemesterId,
            'sar_ay_id' => $this->sourceYearId,
            'sar_fac_id' => $this->facultyId,
            'sar_course_id' => $this->courseId,
            'sar_sec_id' => $sourceSectionId,
            'sar_room_id' => $this->roomId,
            'sar_day' => 'Wednesday',
            'sar_start_time' => '08:00',
            'sar_end_time' => '10:00',
            'sar_shift' => 'day',
            'sar_is_active' => true,
            'sar_archived_at' => now(),
        ]);
        DB::table('schedule')->insert([
            'sch_id' => $scheduleId,
            'sch_fac_id' => $this->facultyId,
            'sch_course_id' => $this->courseId,
            'sch_sec_id' => $sourceSectionId,
            'sch_room_id' => $this->roomId,
            'sch_sem_id' => $this->sourceSecondSemesterId,
            'sch_day' => 'Wednesday',
            'sch_shift' => 'day',
            'sch_start_time' => '08:00',
            'sch_end_time' => '10:00',
            'sch_is_active' => true,
            'sch_status' => 'draft',
            'sch_created_by' => $this->chairId,
        ]);

        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldReceive('assign')->times(4)->andReturnUsing(function (array $assignment) {
            $this->assertSame($this->courseId, $assignment['subj_id']);
            $this->assertSame($this->destinationSecondSemesterId, $assignment['sem_id']);
            $this->assertSame('CS-1B', DB::table('section')->where('sec_id', $assignment['sec_id'])->value('sec_name'));
            $this->insertSimulatedSchedule($assignment);
            return ['success' => true, 'workload_override' => null];
        });

        $reuse = new ScheduleReuseService($scheduler);
        $preview = $reuse->preview(
            'pbt',
            $this->facultyId,
            $this->destinationSecondSemesterId,
            $this->sourceSecondSemesterId
        );
        $this->assertTrue($preview['can_copy']);
        $this->assertSame(1, $preview['summary']['valid']);
        $this->assertSame(0, DB::table('schedule')->where('sch_sem_id', $this->destinationSecondSemesterId)->count());
        $this->assertSame(0, DB::table('section')->where('sec_sem_id', $this->destinationSecondSemesterId)->count());

        $result = $reuse->apply(
            'pbt',
            $this->facultyId,
            $this->destinationSecondSemesterId,
            $this->sourceSecondSemesterId
        );
        $this->assertTrue($result['success']);
        $this->assertSame(1, $result['copied_count']);
        $this->assertSame(1, DB::table('schedule')->where('sch_sem_id', $this->destinationSecondSemesterId)->count());
        $this->assertSame(1, DB::table('section')->where('sec_sem_id', $this->destinationSecondSemesterId)->count());
    }

    public function test_pbt_reuse_is_limited_to_the_chair_of_the_source_department(): void
    {
        $otherDepartmentId = (string) Str::uuid();
        DB::table('department')->insert([
            'dept_id' => $otherDepartmentId,
            'dept_college_id' => $this->collegeId,
            'dept_name' => 'Information Systems',
            'dept_code' => 'IS',
        ]);
        DB::table('section')->where('sec_id', $this->sourceSectionId)->update([
            'sec_dept_id' => $otherDepartmentId,
        ]);

        $scheduler = Mockery::mock(ScheduleAssignmentService::class);
        $scheduler->shouldNotReceive('assign');
        $preview = (new ScheduleReuseService($scheduler))->preview(
            'pbt',
            $this->facultyId,
            $this->destinationSemesterId,
            $this->sourceSemesterId
        );

        $this->assertFalse($preview['can_copy']);
        $this->assertSame(2, $preview['summary']['excluded']);
        $this->assertFalse($preview['rows'][0]['user_excludable']);
        $this->assertStringContainsString('Only that department’s chair', $preview['rows'][0]['message']);
    }

    private function insertSimulatedSchedule(array $assignment): void
    {
        DB::table('schedule')->insert([
            'sch_id' => (string) Str::uuid(),
            'sch_fac_id' => $assignment['fac_id'],
            'sch_course_id' => $assignment['subj_id'],
            'sch_sec_id' => $assignment['sec_id'],
            'sch_room_id' => $assignment['room_id'],
            'sch_sem_id' => $assignment['sem_id'],
            'sch_day' => $assignment['day'],
            'sch_shift' => $assignment['shift'],
            'sch_start_time' => $assignment['start_time'],
            'sch_end_time' => $assignment['end_time'],
            'sch_is_active' => true,
            'sch_status' => 'draft',
            'sch_created_by' => $this->chairId,
        ]);
    }
}
