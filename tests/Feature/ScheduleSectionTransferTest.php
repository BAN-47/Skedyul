<?php

namespace Tests\Feature;

use App\Services\ScheduleAssignmentService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ScheduleSectionTransferTest extends TestCase
{
    private string $facultyId;
    private string $semesterId;
    private string $roomId;
    private string $oldSectionId;
    private string $newSectionId;
    private string $courseId;
    private string $loadId;
    private string $scheduleId;

    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('USER', function (Blueprint $table) {
            $table->uuid('usr_id')->primary();
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->uuid('fac_usr_id');
            $table->string('fac_first_name')->nullable();
            $table->string('fac_last_name')->nullable();
            $table->string('fac_employment_type')->nullable();
            $table->string('fac_special_position')->nullable();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('course_code');
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->string('sec_name');
            $table->boolean('sec_is_evening')->default(false);
        });
        Schema::create('room', function (Blueprint $table) {
            $table->uuid('room_id')->primary();
        });
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
            $table->boolean('ay_is_active')->default(false);
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id')->nullable();
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_fac_id');
            $table->uuid('sl_course_id');
            $table->uuid('sl_sec_id')->nullable();
            $table->uuid('sl_sem_id');
            $table->string('sl_shift');
            $table->string('sl_status')->nullable();
            $table->uuid('sl_assigned_by')->nullable();
            $table->timestamp('sl_assigned_at')->nullable();
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_load_id')->nullable();
            $table->uuid('sch_fac_id');
            $table->uuid('sch_course_id');
            $table->uuid('sch_sec_id')->nullable();
            $table->uuid('sch_room_id')->nullable();
            $table->uuid('sch_sem_id');
            $table->string('sch_day');
            $table->string('sch_shift')->nullable();
            $table->string('sch_start_time');
            $table->string('sch_end_time');
            $table->string('sch_status')->nullable();
            $table->boolean('sch_is_active')->default(true);
            $table->uuid('sch_created_by')->nullable();
            $table->timestamp('sch_created_at')->nullable();
            $table->timestamp('sch_updated_at')->nullable();
        });
        Schema::create('workload', function (Blueprint $table) {
            $table->uuid('wl_id')->primary();
            $table->uuid('wl_fac_id');
            $table->uuid('wl_sem_id');
            $table->uuid('wl_ay_id')->nullable();
            $table->string('wl_type')->nullable();
            $table->decimal('wl_total_hours')->default(0);
            $table->timestamp('wl_created_at')->nullable();
            $table->timestamp('wl_updated_at')->nullable();
        });

        $userId = (string) Str::uuid();
        $this->facultyId = (string) Str::uuid();
        $academicYearId = (string) Str::uuid();
        $this->semesterId = (string) Str::uuid();
        $this->roomId = (string) Str::uuid();
        $this->oldSectionId = (string) Str::uuid();
        $this->newSectionId = (string) Str::uuid();
        $this->courseId = (string) Str::uuid();
        $this->loadId = (string) Str::uuid();
        $this->scheduleId = (string) Str::uuid();

        DB::table('USER')->insert(['usr_id' => $userId, 'usr_is_active' => true]);
        DB::table('faculty')->insert([
            'fac_id' => $this->facultyId,
            'fac_usr_id' => $userId,
            'fac_first_name' => 'Emelie',
            'fac_last_name' => 'Villaceran',
            'fac_employment_type' => 'full_time',
        ]);
        DB::table('academic_year')->insert(['ay_id' => $academicYearId]);
        DB::table('semester')->insert([
            'sem_id' => $this->semesterId,
            'sem_ay_id' => $academicYearId,
        ]);
        DB::table('room')->insert(['room_id' => $this->roomId]);
        DB::table('section')->insert([
            ['sec_id' => $this->oldSectionId, 'sec_name' => 'BSIS 1-2B Evening', 'sec_is_evening' => true],
            ['sec_id' => $this->newSectionId, 'sec_name' => 'BSIS 1-2', 'sec_is_evening' => false],
        ]);
        DB::table('course')->insert([
            'course_id' => $this->courseId,
            'course_code' => 'CC 112',
            'course_lecture_hours' => 9,
            'course_lab_hours' => 0,
        ]);
        DB::table('study_load')->insert([
            'sl_id' => $this->loadId,
            'sl_fac_id' => $this->facultyId,
            'sl_course_id' => $this->courseId,
            'sl_sec_id' => $this->oldSectionId,
            'sl_sem_id' => $this->semesterId,
            'sl_shift' => 'evening',
        ]);
        DB::table('schedule')->insert([
            [
                'sch_id' => $this->scheduleId,
                'sch_load_id' => $this->loadId,
                'sch_fac_id' => $this->facultyId,
                'sch_course_id' => $this->courseId,
                'sch_sec_id' => $this->oldSectionId,
                'sch_room_id' => $this->roomId,
                'sch_sem_id' => $this->semesterId,
                'sch_day' => 'Monday',
                'sch_shift' => 'evening',
                'sch_start_time' => '16:00',
                'sch_end_time' => '19:00',
                'sch_is_active' => true,
            ],
            [
                'sch_id' => (string) Str::uuid(),
                'sch_load_id' => $this->loadId,
                'sch_fac_id' => $this->facultyId,
                'sch_course_id' => $this->courseId,
                'sch_sec_id' => $this->oldSectionId,
                'sch_room_id' => $this->roomId,
                'sch_sem_id' => $this->semesterId,
                'sch_day' => 'Wednesday',
                'sch_shift' => 'evening',
                'sch_start_time' => '16:00',
                'sch_end_time' => '19:00',
                'sch_is_active' => true,
            ],
        ]);

        foreach (['EC 1', 'EC 2', 'EC 3'] as $code) {
            $courseId = (string) Str::uuid();
            DB::table('course')->insert([
                'course_id' => $courseId,
                'course_code' => $code,
                'course_lecture_hours' => 9,
                'course_lab_hours' => 0,
            ]);
            DB::table('study_load')->insert([
                'sl_id' => (string) Str::uuid(),
                'sl_fac_id' => $this->facultyId,
                'sl_course_id' => $courseId,
                'sl_sem_id' => $this->semesterId,
                'sl_shift' => 'evening',
            ]);
        }
    }

    public function test_section_edit_changes_only_one_meeting_without_adding_course_hours(): void
    {
        $service = app(ScheduleAssignmentService::class);
        $beforeTotal = $service->facultyHours($this->facultyId, $this->semesterId);
        $otherScheduleId = DB::table('schedule')
            ->where('sch_load_id', $this->loadId)
            ->where('sch_id', '!=', $this->scheduleId)
            ->value('sch_id');

        $result = $service->update($this->scheduleId, [
            'fac_id' => $this->facultyId,
            'subj_id' => $this->courseId,
            'sec_id' => $this->newSectionId,
            'room_id' => $this->roomId,
            'sem_id' => $this->semesterId,
            'day' => 'Saturday',
            'start_time' => '07:00',
            'end_time' => '12:00',
            'shift' => 'evening',
        ]);

        $this->assertTrue($result['success'], $result['message'] ?? 'The section transfer failed.');
        $this->assertSame($this->newSectionId, DB::table('schedule')->where('sch_id', $this->scheduleId)->value('sch_sec_id'));
        $this->assertSame($this->oldSectionId, DB::table('schedule')->where('sch_id', $otherScheduleId)->value('sch_sec_id'));
        $this->assertSame($beforeTotal, $service->facultyHours($this->facultyId, $this->semesterId));
        $this->assertSame(36.0, $service->facultyHoursForShift($this->facultyId, $this->semesterId, 'evening'));
    }

    public function test_conflict_on_another_day_does_not_block_section_edit_of_this_meeting(): void
    {
        DB::table('schedule')
            ->where('sch_id', '!=', $this->scheduleId)
            ->update([
                'sch_day' => 'Thursday',
                'sch_start_time' => '16:30',
                'sch_end_time' => '19:30',
            ]);

        $otherUserId = (string) Str::uuid();
        $otherFacultyId = (string) Str::uuid();
        DB::table('USER')->insert(['usr_id' => $otherUserId, 'usr_is_active' => true]);
        DB::table('faculty')->insert([
            'fac_id' => $otherFacultyId,
            'fac_usr_id' => $otherUserId,
            'fac_first_name' => 'Gerry Ritz',
            'fac_last_name' => 'Densing',
            'fac_employment_type' => 'full_time',
        ]);
        $otherLoadId = (string) Str::uuid();
        DB::table('study_load')->insert([
            'sl_id' => $otherLoadId,
            'sl_fac_id' => $otherFacultyId,
            'sl_course_id' => $this->courseId,
            'sl_sec_id' => $this->newSectionId,
            'sl_sem_id' => $this->semesterId,
            'sl_shift' => 'evening',
        ]);
        DB::table('schedule')->insert([
            'sch_id' => (string) Str::uuid(),
            'sch_load_id' => $otherLoadId,
            'sch_fac_id' => $otherFacultyId,
            'sch_course_id' => $this->courseId,
            'sch_sec_id' => $this->newSectionId,
            'sch_room_id' => $this->roomId,
            'sch_sem_id' => $this->semesterId,
            'sch_day' => 'Thursday',
            'sch_shift' => 'evening',
            'sch_start_time' => '16:30',
            'sch_end_time' => '19:30',
            'sch_is_active' => true,
        ]);

        $result = app(ScheduleAssignmentService::class)->update($this->scheduleId, [
            'fac_id' => $this->facultyId,
            'subj_id' => $this->courseId,
            'sec_id' => $this->newSectionId,
            'room_id' => $this->roomId,
            'sem_id' => $this->semesterId,
            'day' => 'Saturday',
            'start_time' => '07:00',
            'end_time' => '12:00',
            'shift' => 'day',
        ]);

        $this->assertTrue($result['success'], $result['message'] ?? 'The Thursday class should not block a Saturday-only section change.');
        $this->assertSame($this->newSectionId, DB::table('schedule')->where('sch_id', $this->scheduleId)->value('sch_sec_id'));
        $this->assertSame('Thursday', DB::table('schedule')->where('sch_fac_id', $otherFacultyId)->value('sch_day'));
    }

    public function test_changing_faculty_moves_only_the_selected_schedule(): void
    {
        $newUserId = (string) Str::uuid();
        $newFacultyId = (string) Str::uuid();
        DB::table('USER')->insert(['usr_id' => $newUserId, 'usr_is_active' => true]);
        DB::table('faculty')->insert([
            'fac_id' => $newFacultyId,
            'fac_usr_id' => $newUserId,
            'fac_first_name' => 'New',
            'fac_last_name' => 'Teacher',
            'fac_employment_type' => 'full_time',
        ]);

        $otherScheduleId = DB::table('schedule')
            ->where('sch_load_id', $this->loadId)
            ->where('sch_id', '!=', $this->scheduleId)
            ->value('sch_id');

        $result = app(ScheduleAssignmentService::class)->update($this->scheduleId, [
            'fac_id' => $newFacultyId,
            'subj_id' => $this->courseId,
            'sec_id' => $this->oldSectionId,
            'room_id' => $this->roomId,
            'sem_id' => $this->semesterId,
            'day' => 'Monday',
            'start_time' => '16:00',
            'end_time' => '19:00',
            'shift' => 'evening',
        ]);

        $this->assertTrue($result['success'], $result['message'] ?? 'The faculty transfer failed.');
        $this->assertSame(1, DB::table('schedule')->where('sch_fac_id', $newFacultyId)->count());
        $this->assertSame($this->facultyId, DB::table('schedule')->where('sch_id', $otherScheduleId)->value('sch_fac_id'));
        $this->assertSame(1, DB::table('study_load')->where('sl_fac_id', $this->facultyId)->where('sl_course_id', $this->courseId)->count());
        $this->assertSame(9.0, app(ScheduleAssignmentService::class)->facultyHours($newFacultyId, $this->semesterId));
        $this->assertSame(36.0, app(ScheduleAssignmentService::class)->facultyHours($this->facultyId, $this->semesterId));
    }
}
