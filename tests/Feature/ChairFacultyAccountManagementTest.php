<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class ChairFacultyAccountManagementTest extends TestCase
{
    private string $collegeId = 'college-1';

    private string $departmentId = 'department-1';

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutMiddleware(AuditActivityMiddleware::class);

        Schema::create('USER', function (Blueprint $table): void {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_first_name')->nullable();
            $table->string('usr_middle_name')->nullable();
            $table->string('usr_last_name')->nullable();
            $table->string('usr_suffix')->nullable();
            $table->string('usr_email')->unique();
            $table->string('usr_password_hash');
            $table->string('usr_role');
            $table->boolean('usr_is_active')->default(true);
        });

        Schema::create('department_chair', function (Blueprint $table): void {
            $table->string('dc_id')->primary();
            $table->string('dc_usr_id');
            $table->string('dc_college_id');
            $table->string('dc_dept_id');
        });

        Schema::create('faculty', function (Blueprint $table): void {
            $table->uuid('fac_id')->primary();
            $table->string('fac_usr_id')->nullable();
            $table->string('fac_college_id')->nullable();
            $table->string('fac_dept_id')->nullable();
            $table->string('fac_first_name');
            $table->string('fac_middle_name')->nullable();
            $table->string('fac_last_name');
            $table->string('fac_suffix')->nullable();
            $table->string('fac_employee_id')->nullable()->unique();
            $table->string('fac_phone_number')->nullable();
            $table->string('fac_address')->nullable();
            $table->string('fac_rank')->nullable();
            $table->string('fac_employment_type')->nullable();
            $table->string('fac_special_position')->nullable();
            $table->decimal('fac_special_position_max_hours', 5, 2)->nullable();
            $table->boolean('fac_is_placeholder')->default(false);
            $table->string('fac_profile_image')->nullable();
            $table->timestamp('fac_created_at')->nullable();
            $table->timestamp('fac_updated_at')->nullable();
        });
        Schema::create('designations', function (Blueprint $table): void {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->unsignedInteger('day_cap');
            $table->unsignedInteger('evening_cap');
            $table->unsignedInteger('max_faculty_count')->nullable();
        });

        Schema::create('study_load', function (Blueprint $table): void {
            $table->string('sl_id')->primary();
            $table->string('sl_fac_id');
        });

        Schema::create('schedule', function (Blueprint $table): void {
            $table->string('sch_id')->primary();
            $table->string('sch_fac_id');
        });

        $chair = User::create([
            'usr_name' => 'Department Chair',
            'usr_first_name' => 'Department',
            'usr_last_name' => 'Chair',
            'usr_email' => 'chair@example.test',
            'usr_password_hash' => Hash::make('chair-password'),
            'usr_role' => 'department_chair',
            'usr_is_active' => true,
        ]);
        DB::table('department_chair')->insert([
            'dc_id' => (string) Str::uuid(),
            'dc_usr_id' => $chair->usr_id,
            'dc_college_id' => $this->collegeId,
            'dc_dept_id' => $this->departmentId,
        ]);
        DB::table('designations')->insert([
            'id' => (string) Str::uuid(),
            'name' => 'Program Head',
            'day_cap' => 16,
            'evening_cap' => 30,
            'max_faculty_count' => null,
        ]);

        $this->actingAs($chair);
    }

    public function test_chair_can_create_edit_and_delete_faculty_accounts(): void
    {
        $this->post(route('chair.faculty_load.faculty.store'), [
            'first_name' => 'Taylor',
            'middle_name' => 'A',
            'last_name' => 'Faculty',
            'email' => 'taylor@example.test',
            'employee_id' => 'EMP-01',
            'phone_number' => '+639123456789',
            'address' => 'Cebu City',
            'rank' => 'Instructor I',
            'employment_type' => 'full_time',
            'special_position' => 'Program Head',
            'password' => 'temporary-pass',
            'password_confirmation' => 'temporary-pass',
        ])->assertRedirect(route('chair.faculty_load'));

        $user = User::where('usr_email', 'taylor@example.test')->firstOrFail();
        $facultyId = DB::table('faculty')->where('fac_usr_id', $user->usr_id)->value('fac_id');
        $this->assertDatabaseHas('faculty', [
            'fac_id' => $facultyId,
            'fac_dept_id' => $this->departmentId,
            'fac_employment_type' => 'full_time',
            'fac_address' => 'Cebu City',
            'fac_special_position' => 'Program Head',
            'fac_special_position_max_hours' => 16,
        ]);

        $this->put(route('chair.faculty_load.faculty.update', $facultyId), [
            'first_name' => 'Taylor',
            'middle_name' => '',
            'last_name' => 'Updated',
            'suffix' => '',
            'email' => 'taylor@example.test',
            'employee_id' => 'EMP-01',
            'phone_number' => '+639123456789',
            'address' => 'Updated address',
            'rank' => 'Assistant Professor I',
            'employment_type' => 'part_time',
            'special_position' => 'Program Head',
            'usr_is_active' => '0',
            'password' => 'replacement-password',
            'password_confirmation' => 'replacement-password',
        ])->assertRedirect(route('chair.faculty_load'));

        $this->assertTrue(Hash::check('replacement-password', $user->fresh()->usr_password_hash));
        $this->assertDatabaseHas('USER', [
            'usr_id' => $user->usr_id,
            'usr_last_name' => 'Updated',
            'usr_is_active' => false,
        ]);
        $this->assertDatabaseHas('faculty', [
            'fac_id' => $facultyId,
            'fac_employment_type' => 'part_time',
            'fac_rank' => 'Assistant Professor I',
            'fac_address' => 'Updated address',
            'fac_special_position' => 'Program Head',
        ]);

        $this->delete(route('chair.faculty_load.faculty.destroy', $facultyId))
            ->assertRedirect(route('chair.faculty_load'));
        $this->assertDatabaseMissing('faculty', ['fac_id' => $facultyId]);
        $this->assertDatabaseMissing('USER', ['usr_id' => $user->usr_id]);
    }

    public function test_chair_cannot_delete_a_faculty_account_with_existing_loads(): void
    {
        $user = User::create([
            'usr_name' => 'Assigned Faculty',
            'usr_first_name' => 'Assigned',
            'usr_last_name' => 'Faculty',
            'usr_email' => 'assigned@example.test',
            'usr_password_hash' => Hash::make('faculty-password'),
            'usr_role' => 'faculty',
            'usr_is_active' => true,
        ]);
        $facultyId = (string) Str::uuid();
        DB::table('faculty')->insert([
            'fac_id' => $facultyId,
            'fac_usr_id' => $user->usr_id,
            'fac_college_id' => $this->collegeId,
            'fac_dept_id' => $this->departmentId,
            'fac_first_name' => 'Assigned',
            'fac_last_name' => 'Faculty',
            'fac_is_placeholder' => false,
        ]);
        DB::table('study_load')->insert([
            'sl_id' => (string) Str::uuid(),
            'sl_fac_id' => $facultyId,
        ]);

        $this->from(route('chair.faculty_load'))
            ->delete(route('chair.faculty_load.faculty.destroy', $facultyId))
            ->assertRedirect(route('chair.faculty_load'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('faculty', ['fac_id' => $facultyId]);
        $this->assertDatabaseHas('USER', ['usr_id' => $user->usr_id]);
    }
}
