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

class DeanDepartmentValidationTest extends TestCase
{
    private string $collegeId;

    private string $departmentId;

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
        Schema::create('college', function (Blueprint $table) {
            $table->uuid('college_id')->primary();
            $table->string('college_name');
            $table->string('college_code');
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
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
        });
        Schema::create('faculty', function (Blueprint $table) {
            $table->uuid('fac_id')->primary();
            $table->string('fac_usr_id');
            $table->uuid('fac_college_id');
            $table->uuid('fac_dept_id');
            $table->boolean('fac_is_placeholder')->default(false);
            $table->string('fac_employee_id')->nullable();
        });

        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        $userId = (string) Str::uuid();

        DB::table('college')->insert([
            'college_id' => $this->collegeId,
            'college_name' => 'College One',
            'college_code' => 'CO1',
        ]);
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
        $this->actingAs(User::create([
            'usr_id' => $userId,
            'usr_name' => 'Test Dean',
            'usr_email' => 'dean@example.com',
            'usr_password_hash' => Hash::make('dean-password'),
            'usr_role' => 'dean',
            'usr_is_active' => true,
        ]));
    }

    public function test_dean_cannot_create_or_update_a_department_with_angle_brackets(): void
    {
        $this->from(route('dean.departments'))
            ->post(route('dean.departments.store'), [
                'dept_name' => 'Department <Two>',
                'dept_code' => 'D2',
                '_department_form' => 'add',
            ])
            ->assertSessionHasErrors([
                'dept_name' => 'Angle brackets (< and >) are not allowed.',
            ]);

        $this->from(route('dean.departments'))
            ->put(route('dean.departments.update', $this->departmentId), [
                'dept_name' => 'Department One',
                'dept_code' => 'D<1>',
                '_department_form' => 'edit',
            ])
            ->assertSessionHasErrors([
                'dept_code' => 'Angle brackets (< and >) are not allowed.',
            ]);
    }

    public function test_dean_cannot_create_or_update_faculty_with_angle_brackets(): void
    {
            $this->from(route('dean.departments', ['department_id' => $this->departmentId]))
                ->post(route('dean.departments.faculty.store', $this->departmentId), [
                    '_faculty_form' => 'add',
                    'usr_first_name' => 'Jane<',
                    'usr_last_name' => 'Doe',
                    'usr_email' => 'jane.doe@example.com',
                    'password' => 'Temp!Pass123',
                    'employment_type' => 'full_time',
                    'role_phone_number' => '+639123456789',
                ])
                ->assertSessionHasErrors([
                    'usr_first_name' => 'Angle brackets (< and >) are not allowed.',
                ]);

            $facultyUserId = (string) Str::uuid();
            $facultyId = (string) Str::uuid();
            User::create([
                'usr_id' => $facultyUserId,
                'usr_name' => 'Jane Doe',
                'usr_email' => 'jane.doe@example.com',
                'usr_password_hash' => Hash::make('Temp!Pass123'),
                'usr_role' => 'faculty',
                'usr_is_active' => true,
            ]);
            DB::table('faculty')->insert([
                'fac_id' => $facultyId,
                'fac_usr_id' => $facultyUserId,
                'fac_college_id' => $this->collegeId,
                'fac_dept_id' => $this->departmentId,
                'fac_is_placeholder' => false,
                'fac_employee_id' => null,
            ]);

            $this->from(route('dean.departments', ['department_id' => $this->departmentId]))
                ->put(route('dean.departments.faculty.update', [$this->departmentId, $facultyId]), [
                    '_faculty_form' => 'edit',
                    '_faculty_id' => $facultyId,
                    'usr_first_name' => 'Jane',
                    'usr_last_name' => 'Doe',
                    'usr_email' => 'jane.doe@example.com',
                    'fac_employment_type' => 'full_time',
                    'fac_special_position' => '',
                    'fac_phone_number' => '',
                    'fac_address' => '12 Main <Street>',
                    'dept_id' => $this->departmentId,
                    'usr_is_active' => '1',
                ])
                ->assertSessionHasErrors([
                    'fac_address' => 'Angle brackets (< and >) are not allowed.',
                ]);
    }
}
