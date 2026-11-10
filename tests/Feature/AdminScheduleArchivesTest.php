<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminScheduleArchivesTest extends TestCase
{
    private string $currentYearId;

    private string $pastYearId;

    private string $pastSemesterId;

    private string $currentSemesterId;

    private string $facultyId;

    private string $courseId;

    private string $sectionId;

    private string $roomId;

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
            $table->timestamp('ay_created_at')->nullable();
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id');
            $table->string('sem_name');
            $table->date('sem_start_date')->nullable();
            $table->date('sem_end_date')->nullable();
            $table->boolean('sem_is_active')->default(false);
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->string('fac_first_name')->nullable();
            $table->string('fac_last_name')->nullable();
        });
        Schema::create('course', function (Blueprint $table) {
            $table->uuid('course_id')->primary();
            $table->string('course_code')->nullable();
            $table->string('course_name')->nullable();
        });
        Schema::create('section', function (Blueprint $table) {
            $table->uuid('sec_id')->primary();
            $table->string('sec_name')->nullable();
        });
        Schema::create('room', function (Blueprint $table) {
            $table->uuid('room_id')->primary();
            $table->string('room_name')->nullable();
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_sem_id');
            $table->uuid('sch_fac_id')->nullable();
            $table->uuid('sch_course_id')->nullable();
            $table->uuid('sch_sec_id')->nullable();
            $table->uuid('sch_room_id')->nullable();
            $table->string('sch_day')->nullable();
            $table->string('sch_start_time')->nullable();
            $table->string('sch_end_time')->nullable();
            $table->string('sch_shift')->nullable();
            $table->string('sch_status')->nullable();
            $table->boolean('sch_is_active')->default(true);
            $table->timestamp('sch_updated_at')->nullable();
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
            $table->string('sar_status')->nullable();
            $table->boolean('sar_is_active')->default(false);
            $table->timestamp('sar_archived_at')->nullable();
        });

        $this->pastYearId = $this->year('2024-2025', false);
        $this->currentYearId = $this->year('2025-2026', true);
        $this->pastSemesterId = $this->semester($this->pastYearId, '1st Semester');
        $this->currentSemesterId = $this->semester($this->currentYearId, '1st Semester');

        $this->facultyId = (string) Str::uuid();
        $this->courseId = (string) Str::uuid();
        $this->sectionId = (string) Str::uuid();
        $this->roomId = (string) Str::uuid();
        DB::table('faculty')->insert([
            'fac_id' => $this->facultyId,
            'fac_first_name' => 'Ada',
            'fac_last_name' => 'Lovelace',
        ]);
        DB::table('course')->insert([
            'course_id' => $this->courseId,
            'course_code' => 'CS101',
            'course_name' => 'Introduction to Computing',
        ]);
        DB::table('section')->insert(['sec_id' => $this->sectionId, 'sec_name' => 'BSIT-1A']);
        DB::table('room')->insert(['room_id' => $this->roomId, 'room_name' => 'Room 101']);
    }

    public function test_admin_can_view_archived_and_preserved_past_schedules_without_current_schedules(): void
    {
        $snapshotScheduleId = (string) Str::uuid();
        DB::table('schedule_archives')->insert([
            'sar_id' => (string) Str::uuid(),
            'sar_sch_id' => $snapshotScheduleId,
            'sar_sem_id' => $this->pastSemesterId,
            'sar_ay_id' => $this->pastYearId,
            'sar_fac_id' => $this->facultyId,
            'sar_course_id' => $this->courseId,
            'sar_sec_id' => $this->sectionId,
            'sar_room_id' => $this->roomId,
            'sar_day' => 'Monday',
            'sar_start_time' => '09:00',
            'sar_end_time' => '10:00',
            'sar_shift' => 'day',
            'sar_status' => 'active',
            'sar_is_active' => true,
        ]);
        $this->schedule($snapshotScheduleId, $this->pastSemesterId, 'Monday');
        $preservedScheduleId = (string) Str::uuid();
        $this->schedule($preservedScheduleId, $this->pastSemesterId, 'Tuesday');
        $this->schedule((string) Str::uuid(), $this->currentSemesterId, 'Wednesday');

        $response = $this->actingAs($this->admin())
            ->get(route('admin.schedule-archives'));

        $response->assertOk()
            ->assertSee('Schedule Archives')
            ->assertSee('2024-2025')
            ->assertSee('CS101')
            ->assertSee('Ada Lovelace')
            ->assertSee('Archived snapshot')
            ->assertSee('Preserved schedule')
            ->assertSee('of 2 schedules')
            ->assertDontSee('Wednesday');

        $this->assertSame(1, preg_match_all('/class="sidebar-logo"/', $response->getContent()));
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $preservedScheduleId,
            'sch_is_active' => true,
        ]);
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $snapshotScheduleId,
            'sch_is_active' => true,
        ]);
    }

    public function test_admin_can_filter_archives_by_academic_year_and_semester(): void
    {
        $this->schedule((string) Str::uuid(), $this->pastSemesterId, 'Monday');
        $this->schedule((string) Str::uuid(), $this->currentSemesterId, 'Tuesday');

        $this->actingAs($this->admin())
            ->get(route('admin.schedule-archives', [
                'year' => $this->pastYearId,
                'semester' => $this->pastSemesterId,
            ]))
            ->assertOk()
            ->assertSee('Monday')
            ->assertDontSee('Tuesday');
    }

    public function test_non_admin_cannot_view_schedule_archives(): void
    {
        $user = User::create([
            'usr_id' => 'faculty-user',
            'usr_name' => 'Faculty User',
            'usr_email' => 'faculty@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'faculty',
        ]);

        $this->actingAs($user)
            ->get(route('admin.schedule-archives'))
            ->assertForbidden();
    }

    public function test_schedule_without_semester_year_mapping_is_not_guessed_into_an_archive_year(): void
    {
        $unmappedScheduleId = (string) Str::uuid();
        $this->schedule($unmappedScheduleId, (string) Str::uuid(), 'Thursday');

        $this->actingAs($this->admin())
            ->get(route('admin.schedule-archives'))
            ->assertOk()
            ->assertDontSee('Thursday');

        $this->assertDatabaseHas('schedule', [
            'sch_id' => $unmappedScheduleId,
            'sch_is_active' => true,
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'usr_id' => 'admin-user',
            'usr_name' => 'Admin User',
            'usr_email' => 'admin@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'system_admin',
        ]);
    }

    private function year(string $label, bool $active): string
    {
        $id = (string) Str::uuid();
        DB::table('academic_year')->insert([
            'ay_id' => $id,
            'ay_academic_year' => $label,
            'ay_year_label' => $label,
            'ay_is_active' => $active,
        ]);

        return $id;
    }

    private function semester(string $yearId, string $name): string
    {
        $id = (string) Str::uuid();
        DB::table('semester')->insert([
            'sem_id' => $id,
            'sem_ay_id' => $yearId,
            'sem_name' => $name,
            'sem_is_active' => $yearId === $this->currentYearId,
        ]);

        return $id;
    }

    private function schedule(string $id, string $semesterId, string $day): void
    {
        DB::table('schedule')->insert([
            'sch_id' => $id,
            'sch_sem_id' => $semesterId,
            'sch_fac_id' => $this->facultyId,
            'sch_course_id' => $this->courseId,
            'sch_sec_id' => $this->sectionId,
            'sch_room_id' => $this->roomId,
            'sch_day' => $day,
            'sch_start_time' => '09:00',
            'sch_end_time' => '10:00',
            'sch_shift' => 'day',
            'sch_status' => 'active',
            'sch_is_active' => true,
        ]);
    }
}
