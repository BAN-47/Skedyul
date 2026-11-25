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

class DeanFacultyWorkloadValidationTest extends TestCase
{
    private string $collegeId;

    private string $departmentId;

    private string $facultyId;

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
        Schema::create('dean', function (Blueprint $table) {
            $table->string('dean_id')->primary();
            $table->string('dean_usr_id');
            $table->uuid('dean_college_id');
        });
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
            $table->uuid('dept_college_id');
            $table->string('dept_name');
            $table->string('dept_code');
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->uuid('fac_dept_id');
            $table->boolean('fac_is_placeholder')->default(false);
        });
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
        });

        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        $this->facultyId = (string) Str::uuid();
        $userId = (string) Str::uuid();

        DB::table('dean')->insert([
            'dean_id' => (string) Str::uuid(),
            'dean_usr_id' => $userId,
            'dean_college_id' => $this->collegeId,
        ]);
        DB::table('department')->insert([
            'dept_id' => $this->departmentId,
            'dept_college_id' => $this->collegeId,
            'dept_name' => 'Department One',
            'dept_code' => 'D1',
        ]);
        DB::table('faculty')->insert([
            'fac_id' => $this->facultyId,
            'fac_dept_id' => $this->departmentId,
            'fac_is_placeholder' => false,
        ]);
        DB::table('designations')->insert([
            'id' => (string) Str::uuid(),
            'name' => 'Department Chairperson',
        ]);
        $this->actingAs(User::create([
            'usr_id' => $userId,
            'usr_name' => 'Test Dean',
            'usr_email' => 'dean@example.com',
            'usr_password_hash' => Hash::make('dean-password'),
            'usr_role' => 'dean',
            'usr_is_active' => true,
        ]));
    }

    public function test_workload_search_rejects_angle_brackets_and_shows_the_validation_message(): void
    {
        $this->get(route('dean.faculty_workload', ['search' => 'Faculty<One>']))
            ->assertRedirect(route('dean.faculty_workload'))
            ->assertSessionHasErrors([
                'search' => 'Angle brackets (< and >) are not allowed.',
            ]);
    }

    public function test_workload_designation_update_rejects_angle_brackets(): void
    {
        $this->from(route('dean.faculty_workload'))
            ->put(route('dean.faculty_workload.designation', $this->facultyId), [
                '_workload_form' => 'designation',
                '_workload_faculty_id' => $this->facultyId,
                'special_position' => 'Chair <One>',
            ])
            ->assertSessionHasErrors([
                'special_position' => 'Angle brackets (< and >) are not allowed.',
            ]);
    }
}
