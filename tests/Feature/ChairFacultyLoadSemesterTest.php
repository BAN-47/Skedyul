<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChairFacultyLoadSemesterTest extends TestCase
{
    private string $chairUserId = 'chair-user';

    private string $departmentId;

    private string $collegeId;

    private string $facultyId;

    private string $firstSemesterId;

    private string $secondSemesterId;

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
        Schema::create('college', function (Blueprint $table) {
            $table->uuid('college_id')->primary();
            $table->string('college_name');
            $table->string('college_code');
        });
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
            $table->uuid('dept_college_id');
            $table->string('dept_name');
            $table->string('dept_code');
        });
        Schema::create('department_chair', function (Blueprint $table) {
            $table->uuid('dc_id')->primary();
            $table->string('dc_usr_id');
            $table->uuid('dc_college_id');
            $table->uuid('dc_dept_id');
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
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->uuid('sec_dept_id');
            $table->string('sec_name');
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->uuid('fac_college_id')->nullable();
            $table->uuid('fac_dept_id')->nullable();
            $table->string('fac_first_name');
            $table->string('fac_last_name');
            $table->string('fac_employment_type')->nullable();
            $table->string('fac_special_position')->nullable();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->uuid('course_college_id');
            $table->uuid('course_dept_id');
            $table->string('course_code');
            $table->string('course_name');
            $table->decimal('course_units')->default(0);
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
            $table->boolean('course_is_active')->default(true);
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_fac_id');
            $table->uuid('sl_course_id');
            $table->string('sl_shift');
            $table->uuid('sl_sec_id');
            $table->uuid('sl_sem_id');
        });

        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        $this->facultyId = (string) Str::uuid();
        $activeYearId = (string) Str::uuid();
        $pastYearId = (string) Str::uuid();
        $this->firstSemesterId = (string) Str::uuid();
        $this->secondSemesterId = (string) Str::uuid();
        $sectionId = (string) Str::uuid();

        DB::table('college')->insert([
            'college_id' => $this->collegeId,
            'college_name' => 'Computing',
            'college_code' => 'CCICT',
        ]);
        DB::table('department')->insert([
            'dept_id' => $this->departmentId,
            'dept_college_id' => $this->collegeId,
            'dept_name' => 'Information Technology',
            'dept_code' => 'BSIT',
        ]);
        DB::table('department_chair')->insert([
            'dc_id' => (string) Str::uuid(),
            'dc_usr_id' => $this->chairUserId,
            'dc_college_id' => $this->collegeId,
            'dc_dept_id' => $this->departmentId,
        ]);
        DB::table('academic_year')->insert([
            ['ay_id' => $activeYearId, 'ay_academic_year' => '2025-2026', 'ay_year_label' => '2025-2026', 'ay_is_active' => true],
            ['ay_id' => $pastYearId, 'ay_academic_year' => '2024-2025', 'ay_year_label' => '2024-2025', 'ay_is_active' => false],
        ]);
        DB::table('semester')->insert([
            [
                'sem_id' => $this->firstSemesterId,
                'sem_ay_id' => $activeYearId,
                'sem_name' => '1st Semester',
                'sem_start_date' => '2025-08-01',
                'sem_is_active' => true,
            ],
            [
                'sem_id' => $this->secondSemesterId,
                'sem_ay_id' => $pastYearId,
                'sem_name' => '2nd Semester',
                'sem_start_date' => '2025-01-01',
                'sem_is_active' => false,
            ],
        ]);
        DB::table('section')->insert([
            'sec_id' => $sectionId,
            'sec_dept_id' => $this->departmentId,
            'sec_name' => 'BSIT-1A',
        ]);
        DB::table('faculty')->insert([
            'fac_id' => $this->facultyId,
            'fac_college_id' => $this->collegeId,
            'fac_dept_id' => $this->departmentId,
            'fac_first_name' => 'Ada',
            'fac_last_name' => 'Lovelace',
            'fac_employment_type' => 'full_time',
        ]);
        $firstCourseId = (string) Str::uuid();
        $secondCourseId = (string) Str::uuid();
        DB::table('course')->insert([
            [
                'course_id' => $firstCourseId,
                'course_college_id' => $this->collegeId,
                'course_dept_id' => $this->departmentId,
                'course_code' => 'CS101',
                'course_name' => 'First Term Course',
                'course_units' => 3,
                'course_lecture_hours' => 3,
                'course_is_active' => true,
            ],
            [
                'course_id' => $secondCourseId,
                'course_college_id' => $this->collegeId,
                'course_dept_id' => $this->departmentId,
                'course_code' => 'CS202',
                'course_name' => 'Second Term Course',
                'course_units' => 4,
                'course_lecture_hours' => 4,
                'course_is_active' => true,
            ],
        ]);
        DB::table('study_load')->insert([
            [
                'sl_id' => (string) Str::uuid(),
                'sl_fac_id' => $this->facultyId,
                'sl_course_id' => $firstCourseId,
                'sl_shift' => 'day',
                'sl_sec_id' => $sectionId,
                'sl_sem_id' => $this->firstSemesterId,
            ],
            [
                'sl_id' => (string) Str::uuid(),
                'sl_fac_id' => $this->facultyId,
                'sl_course_id' => $secondCourseId,
                'sl_shift' => 'evening',
                'sl_sec_id' => $sectionId,
                'sl_sem_id' => $this->secondSemesterId,
            ],
        ]);
    }

    public function test_chair_can_view_faculty_load_for_selected_semester_without_mixing_terms(): void
    {
        $chair = User::create([
            'usr_id' => $this->chairUserId,
            'usr_name' => 'Department Chair',
            'usr_email' => 'chair@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'department_chair',
        ]);

        $this->actingAs($chair)
            ->get(route('chair.faculty_load', ['semester' => $this->secondSemesterId]))
            ->assertOk()
            ->assertSee('2nd Semester')
            ->assertSee('2024-2025')
            ->assertSee('CS202')
            ->assertDontSee('CS101');
    }

    public function test_faculty_load_defaults_to_official_semester(): void
    {
        $chair = User::create([
            'usr_id' => $this->chairUserId,
            'usr_name' => 'Department Chair',
            'usr_email' => 'chair@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'department_chair',
        ]);

        $this->actingAs($chair)
            ->get(route('chair.faculty_load'))
            ->assertOk()
            ->assertSee('CS101')
            ->assertDontSee('CS202');
    }
}
