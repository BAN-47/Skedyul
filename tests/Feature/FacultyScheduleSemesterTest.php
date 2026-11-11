<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class FacultyScheduleSemesterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->withoutMiddleware(AuditActivityMiddleware::class);

        Schema::create('USER', function (Blueprint $table) {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_email');
            $table->string('usr_password_hash');
            $table->string('usr_role');
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
            $table->string('ay_academic_year');
            $table->string('ay_year_label')->nullable();
            $table->boolean('ay_is_active')->default(false);
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id');
            $table->string('sem_name');
            $table->date('sem_start_date')->nullable();
            $table->boolean('sem_is_active')->default(false);
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->string('fac_usr_id');
            $table->uuid('fac_college_id')->nullable();
            $table->uuid('fac_dept_id')->nullable();
            $table->string('fac_first_name');
            $table->string('fac_last_name');
            $table->string('fac_employment_type')->nullable();
            $table->string('fac_rank')->nullable();
            $table->string('fac_special_position')->nullable();
            $table->string('fac_profile_image')->nullable();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('course_code');
            $table->string('course_name');
            $table->decimal('course_units')->default(0);
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->string('sec_name');
        });
        Schema::create('room', function (Blueprint $table) {
            $table->uuid('room_id')->primary();
            $table->string('room_name');
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
            $table->string('sch_start_time');
            $table->string('sch_end_time');
            $table->string('sch_shift');
            $table->boolean('sch_is_active')->default(true);
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_fac_id');
            $table->uuid('sl_course_id');
            $table->string('sl_shift');
            $table->uuid('sl_sec_id')->nullable();
            $table->uuid('sl_sem_id');
        });
        Schema::create('system_setting', function (Blueprint $table) {
            $table->uuid('sset_id')->primary();
            $table->string('sset_key')->unique();
            $table->text('sset_value')->nullable();
        });
    }

    public function test_faculty_can_view_selected_semester_without_changing_official_term(): void
    {
        $facultyUser = User::create([
            'usr_id' => 'faculty-user',
            'usr_name' => 'Ada Lovelace',
            'usr_email' => 'ada@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'faculty',
        ]);
        $facultyId = (string) Str::uuid();
        DB::table('faculty')->insert([
            'fac_id' => $facultyId,
            'fac_usr_id' => $facultyUser->usr_id,
            'fac_first_name' => 'Ada',
            'fac_last_name' => 'Lovelace',
            'fac_employment_type' => 'full_time',
        ]);
        $yearId = (string) Str::uuid();
        $currentSemesterId = (string) Str::uuid();
        $selectedSemesterId = (string) Str::uuid();
        DB::table('academic_year')->insert([
            'ay_id' => $yearId,
            'ay_academic_year' => '2025-2026',
            'ay_year_label' => '2025-2026',
            'ay_is_active' => true,
        ]);
        DB::table('semester')->insert([
            [
                'sem_id' => $currentSemesterId,
                'sem_ay_id' => $yearId,
                'sem_name' => '1st Semester',
                'sem_start_date' => '2025-08-01',
                'sem_is_active' => true,
            ],
            [
                'sem_id' => $selectedSemesterId,
                'sem_ay_id' => $yearId,
                'sem_name' => '2nd Semester',
                'sem_start_date' => '2026-01-01',
                'sem_is_active' => false,
            ],
        ]);
        $currentCourseId = (string) Str::uuid();
        $selectedCourseId = (string) Str::uuid();
        DB::table('course')->insert([
            [
                'course_id' => $currentCourseId,
                'course_code' => 'CS101',
                'course_name' => 'First Semester Course',
                'course_units' => 3,
                'course_lecture_hours' => 3,
            ],
            [
                'course_id' => $selectedCourseId,
                'course_code' => 'CS202',
                'course_name' => 'Second Semester Course',
                'course_units' => 3,
                'course_lecture_hours' => 3,
            ],
        ]);
        $sectionId = (string) Str::uuid();
        $roomId = (string) Str::uuid();
        DB::table('section')->insert(['sec_id' => $sectionId, 'sec_name' => 'BSIT-1A']);
        DB::table('room')->insert(['room_id' => $roomId, 'room_name' => 'Room 101']);

        foreach ([
            [$currentSemesterId, $currentCourseId],
            [$selectedSemesterId, $selectedCourseId],
        ] as [$semesterId, $courseId]) {
            $loadId = (string) Str::uuid();
            DB::table('study_load')->insert([
                'sl_id' => $loadId,
                'sl_fac_id' => $facultyId,
                'sl_course_id' => $courseId,
                'sl_shift' => 'day',
                'sl_sec_id' => $sectionId,
                'sl_sem_id' => $semesterId,
            ]);
            DB::table('schedule')->insert([
                'sch_id' => (string) Str::uuid(),
                'sch_load_id' => $loadId,
                'sch_fac_id' => $facultyId,
                'sch_course_id' => $courseId,
                'sch_sec_id' => $sectionId,
                'sch_room_id' => $roomId,
                'sch_sem_id' => $semesterId,
                'sch_day' => 'Monday',
                'sch_start_time' => '09:00',
                'sch_end_time' => '10:00',
                'sch_shift' => 'day',
                'sch_is_active' => true,
            ]);
        }

        $this->actingAs($facultyUser)
            ->get(route('faculty.schedule', ['semester' => $selectedSemesterId]))
            ->assertOk()
            ->assertSee('2nd Semester, AY 2025 - 2026')
            ->assertSee('CS202')
            ->assertDontSee('CS101');

        $this->assertDatabaseHas('semester', [
            'sem_id' => $currentSemesterId,
            'sem_is_active' => true,
        ]);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $selectedSemesterId,
            'sem_is_active' => false,
        ]);
    }
}
