<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChairTermScheduleIsolationTest extends TestCase
{
    private string $facultyId;

    private string $sectionId;

    private string $firstSemesterId;

    private string $secondSemesterId;

    private array $scheduleIds = [];

    private array $loadIds = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(AuditActivityMiddleware::class);

        Schema::create('USER', function (Blueprint $table) {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_email');
            $table->string('usr_password_hash');
            $table->string('usr_role');
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
        });
        Schema::create('department_chair', function (Blueprint $table) {
            $table->uuid('dc_id')->primary();
            $table->string('dc_usr_id');
            $table->uuid('dc_dept_id')->nullable();
            $table->uuid('dc_college_id')->nullable();
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->uuid('sec_dept_id')->nullable();
            $table->string('sec_name')->nullable();
        });
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id')->nullable();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->decimal('course_lecture_hours')->default(0);
            $table->decimal('course_lab_hours')->default(0);
        });
        Schema::create('study_load', function (Blueprint $table) {
            $table->uuid('sl_id')->primary();
            $table->uuid('sl_fac_id');
            $table->uuid('sl_course_id')->nullable();
            $table->uuid('sl_sec_id')->nullable();
            $table->uuid('sl_sem_id');
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_load_id')->nullable();
            $table->uuid('sch_fac_id');
            $table->uuid('sch_sec_id')->nullable();
            $table->uuid('sch_sem_id');
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

        $this->facultyId = (string) Str::uuid();
        $this->sectionId = (string) Str::uuid();
        $this->firstSemesterId = (string) Str::uuid();
        $this->secondSemesterId = (string) Str::uuid();
        $firstLoadId = (string) Str::uuid();
        $secondLoadId = (string) Str::uuid();
        $courseId = (string) Str::uuid();
        $academicYearId = (string) Str::uuid();
        DB::table('faculty')->insert(['fac_id' => $this->facultyId]);
        DB::table('academic_year')->insert(['ay_id' => $academicYearId]);
        DB::table('section')->insert(['sec_id' => $this->sectionId, 'sec_name' => 'BSIT-1A']);
        DB::table('course')->insert([
            'course_id' => $courseId,
            'course_lecture_hours' => 3,
            'course_lab_hours' => 0,
        ]);
        DB::table('semester')->insert([
            ['sem_id' => $this->firstSemesterId, 'sem_ay_id' => $academicYearId],
            ['sem_id' => $this->secondSemesterId, 'sem_ay_id' => $academicYearId],
        ]);
        DB::table('study_load')->insert([
            [
                'sl_id' => $firstLoadId,
                'sl_fac_id' => $this->facultyId,
                'sl_course_id' => $courseId,
                'sl_sec_id' => $this->sectionId,
                'sl_sem_id' => $this->firstSemesterId,
            ],
            [
                'sl_id' => $secondLoadId,
                'sl_fac_id' => $this->facultyId,
                'sl_course_id' => $courseId,
                'sl_sec_id' => $this->sectionId,
                'sl_sem_id' => $this->secondSemesterId,
            ],
        ]);
        $firstScheduleId = (string) Str::uuid();
        $secondScheduleId = (string) Str::uuid();
        DB::table('schedule')->insert([
            [
                'sch_id' => $firstScheduleId,
                'sch_load_id' => $firstLoadId,
                'sch_fac_id' => $this->facultyId,
                'sch_sec_id' => $this->sectionId,
                'sch_sem_id' => $this->firstSemesterId,
            ],
            [
                'sch_id' => $secondScheduleId,
                'sch_load_id' => $secondLoadId,
                'sch_fac_id' => $this->facultyId,
                'sch_sec_id' => $this->sectionId,
                'sch_sem_id' => $this->secondSemesterId,
            ],
        ]);
        $this->scheduleIds = [$firstScheduleId, $secondScheduleId];
        $this->loadIds = [$firstLoadId, $secondLoadId];
    }

    public function test_pbs_clear_only_removes_the_selected_semester(): void
    {
        $chair = $this->chair();

        $this->actingAs($chair)
            ->postJson(route('chair.pbs.clear'), [
                'section' => $this->sectionId,
                'semester' => $this->firstSemesterId,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('schedule', ['sch_id' => $this->scheduleIds[0]]);
        $this->assertDatabaseHas('schedule', ['sch_id' => $this->scheduleIds[1]]);
        $this->assertDatabaseMissing('study_load', ['sl_id' => $this->loadIds[0]]);
        $this->assertDatabaseHas('study_load', ['sl_id' => $this->loadIds[1]]);
    }

    public function test_pbt_clear_only_removes_the_selected_semester(): void
    {
        $chair = $this->chair();

        $this->actingAs($chair)
            ->postJson(route('chair.pbt.clear'), [
                'faculty' => $this->facultyId,
                'semester' => $this->firstSemesterId,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('schedule', ['sch_id' => $this->scheduleIds[0]]);
        $this->assertDatabaseHas('schedule', ['sch_id' => $this->scheduleIds[1]]);
        $this->assertDatabaseMissing('study_load', ['sl_id' => $this->loadIds[0]]);
        $this->assertDatabaseHas('study_load', ['sl_id' => $this->loadIds[1]]);
    }

    private function chair(): User
    {
        return User::create([
            'usr_id' => 'chair-user',
            'usr_name' => 'Chair',
            'usr_email' => 'chair@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'department_chair',
        ]);
    }
}
