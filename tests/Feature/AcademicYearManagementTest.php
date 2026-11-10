<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AcademicYearManagementTest extends TestCase
{
    private string $activeYearId;

    private string $firstSemesterId;

    private string $secondSemesterId;

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
        Schema::create('academic_year', function (Blueprint $table) {
            $table->uuid('ay_id')->primary();
            $table->string('ay_academic_year', 30);
            $table->string('ay_year_label', 50)->nullable();
            $table->boolean('ay_is_active')->default(false);
            $table->timestamp('ay_created_at')->nullable();
        });
        Schema::create('semester', function (Blueprint $table) {
            $table->uuid('sem_id')->primary();
            $table->uuid('sem_ay_id');
            $table->string('sem_name', 50);
            $table->date('sem_start_date');
            $table->date('sem_end_date');
            $table->boolean('sem_is_active')->default(false);
        });
        Schema::create('schedule', function (Blueprint $table) {
            $table->uuid('sch_id')->primary();
            $table->uuid('sch_sem_id');
            $table->boolean('sch_is_active')->default(true);
        });
        Schema::create('system_setting', function (Blueprint $table) {
            $table->uuid('sset_id')->primary();
            $table->string('sset_key')->unique();
            $table->text('sset_value')->nullable();
            $table->string('sset_updated_by')->nullable();
        });

        $this->activeYearId = $this->insertYear('2025-2026', true);
        $this->firstSemesterId = $this->insertSemester(
            $this->activeYearId,
            '1st Semester',
            '2025-08-01',
            '2025-12-20',
            true
        );
        $this->secondSemesterId = $this->insertSemester(
            $this->activeYearId,
            '2nd Semester',
            '2026-01-10',
            '2026-05-30',
            false
        );
    }

    public function test_new_year_requires_confirmation_and_preserves_previous_schedules_and_semesters(): void
    {
        $admin = $this->admin();
        $scheduleId = (string) Str::uuid();
        DB::table('schedule')->insert([
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->firstSemesterId,
            'sch_is_active' => true,
        ]);
        $payload = $this->periodPayload('2026-2027');

        $this->actingAs($admin)
            ->putJson(route('admin.academic-year.update'), $payload)
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'confirmation_required' => true,
            ]);

        $this->assertDatabaseCount('academic_year', 1);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->firstSemesterId,
            'sem_ay_id' => $this->activeYearId,
            'sem_is_active' => true,
        ]);
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $scheduleId,
            'sch_is_active' => true,
        ]);

        $this->putJson(route('admin.academic-year.update'), [
            ...$payload,
            'confirm_new_academic_year' => true,
        ])->assertOk()->assertJson(['success' => true]);

        $newYear = DB::table('academic_year')->where('ay_academic_year', '2026-2027')->first();
        $newSemester = DB::table('semester')
            ->where('sem_ay_id', $newYear->ay_id)
            ->where('sem_name', '1st Semester')
            ->first();

        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->firstSemesterId,
            'sem_ay_id' => $this->activeYearId,
            'sem_is_active' => false,
        ]);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $newSemester->sem_id,
            'sem_ay_id' => $newYear->ay_id,
            'sem_is_active' => true,
        ]);
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->firstSemesterId,
            'sch_is_active' => true,
        ]);
    }

    public function test_second_semester_is_created_for_its_year_without_reassigning_another_years_record(): void
    {
        $this->actingAs($this->admin())
            ->putJson(route('admin.academic-year.update'), [
                ...$this->periodPayload('2025-2026', '2nd Semester'),
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->firstSemesterId,
            'sem_ay_id' => $this->activeYearId,
            'sem_is_active' => false,
        ]);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->secondSemesterId,
            'sem_ay_id' => $this->activeYearId,
            'sem_is_active' => true,
        ]);
        $this->assertSame(
            2,
            DB::table('semester')->where('sem_ay_id', $this->activeYearId)->count()
        );
    }

    public function test_automatic_semester_advance_uses_second_semester_from_the_same_year(): void
    {
        $otherYearId = $this->insertYear('2026-2027', false);
        $otherYearSemesterId = $this->insertSemester(
            $otherYearId,
            '2nd Semester',
            '2026-01-10',
            '2026-05-30',
            false
        );
        DB::table('semester')->where('sem_id', $this->firstSemesterId)->update([
            'sem_end_date' => now()->subDay()->toDateString(),
        ]);

        $notice = AcademicYearController::autoAdvanceIfNeeded();

        $this->assertStringContainsString('Automatically switched', $notice);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->secondSemesterId,
            'sem_is_active' => true,
        ]);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $otherYearSemesterId,
            'sem_is_active' => false,
        ]);
    }

    public function test_automatic_end_of_second_semester_preserves_schedules(): void
    {
        $scheduleId = (string) Str::uuid();
        DB::table('schedule')->insert([
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->secondSemesterId,
            'sch_is_active' => true,
        ]);
        DB::table('semester')->where('sem_id', $this->secondSemesterId)->update([
            'sem_is_active' => true,
            'sem_end_date' => now()->subDay()->toDateString(),
        ]);
        DB::table('semester')->where('sem_id', $this->firstSemesterId)->update([
            'sem_is_active' => false,
        ]);

        $notice = AcademicYearController::autoAdvanceIfNeeded();

        $this->assertStringContainsString('set the next academic year', $notice);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->secondSemesterId,
            'sem_is_active' => false,
        ]);
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->secondSemesterId,
            'sch_is_active' => true,
        ]);
    }

    public function test_scheduled_end_of_second_semester_preserves_schedules(): void
    {
        $scheduleId = (string) Str::uuid();
        DB::table('schedule')->insert([
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->secondSemesterId,
            'sch_is_active' => true,
        ]);
        DB::table('semester')->where('sem_id', $this->secondSemesterId)->update([
            'sem_is_active' => true,
            'sem_end_date' => now()->subDay()->toDateString(),
        ]);
        DB::table('semester')->where('sem_id', $this->firstSemesterId)->update([
            'sem_is_active' => false,
        ]);

        $notice = app(AcademicYearController::class)->autoSwitchSemester();

        $this->assertStringContainsString('Existing schedules were preserved', $notice);
        $this->assertDatabaseHas('semester', [
            'sem_id' => $this->secondSemesterId,
            'sem_is_active' => false,
        ]);
        $this->assertDatabaseHas('schedule', [
            'sch_id' => $scheduleId,
            'sch_sem_id' => $this->secondSemesterId,
            'sch_is_active' => true,
        ]);
    }

    private function admin(): User
    {
        return User::create([
            'usr_id' => 'admin',
            'usr_name' => 'Admin',
            'usr_email' => 'admin@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'system_admin',
        ]);
    }

    private function periodPayload(string $year, string $semester = '1st Semester'): array
    {
        return [
            'ay_academic_year' => $year,
            'sem_name' => $semester,
            'sem_start_date' => $semester === '1st Semester' ? '2026-08-01' : '2027-01-10',
            'sem_end_date' => $semester === '1st Semester' ? '2026-12-20' : '2027-05-30',
        ];
    }

    private function insertYear(string $year, bool $active): string
    {
        $id = (string) Str::uuid();
        DB::table('academic_year')->insert([
            'ay_id' => $id,
            'ay_academic_year' => $year,
            'ay_year_label' => $year,
            'ay_is_active' => $active,
        ]);

        return $id;
    }

    private function insertSemester(
        string $yearId,
        string $name,
        string $startDate,
        string $endDate,
        bool $active
    ): string {
        $id = (string) Str::uuid();
        DB::table('semester')->insert([
            'sem_id' => $id,
            'sem_ay_id' => $yearId,
            'sem_name' => $name,
            'sem_start_date' => $startDate,
            'sem_end_date' => $endDate,
            'sem_is_active' => $active,
        ]);

        return $id;
    }
}
