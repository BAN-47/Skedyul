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

class AdminDepartmentCollegeValidationTest extends TestCase
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
        Schema::create('department', function (Blueprint $table) {
            $table->uuid('dept_id')->primary();
            $table->uuid('dept_college_id');
            $table->string('dept_name');
            $table->string('dept_code');
        });

        $this->collegeId = (string) Str::uuid();
        $this->departmentId = (string) Str::uuid();
        DB::table('college')->insert([
            'college_id' => $this->collegeId,
            'college_name' => 'College One',
            'college_code' => 'CO1',
        ]);
        DB::table('department')->insert([
            'dept_id' => $this->departmentId,
            'dept_college_id' => $this->collegeId,
            'dept_name' => 'Program One',
            'dept_code' => 'P1',
        ]);
    }

    public function test_admin_cannot_create_or_update_a_college_with_angle_brackets(): void
    {
        $this->actingAs($this->admin())
            ->from(route('admin.departments', ['modal' => 'add-college']))
            ->post(route('admin.colleges.store'), [
                'college_name' => '<College>',
                'college_code' => 'C1',
            ])
            ->assertSessionHasErrors('college_name');

        $this->actingAs($this->admin())
            ->from(route('admin.departments', ['college_id' => $this->collegeId]))
            ->put(route('admin.colleges.update', $this->collegeId), [
                'college_name' => 'College One',
                'college_code' => 'C<1>',
            ])
            ->assertSessionHasErrors('college_code');
    }

    public function test_admin_cannot_create_or_update_a_department_with_angle_brackets(): void
    {
        $this->actingAs($this->admin())
            ->from(route('admin.departments', [
                'college_id' => $this->collegeId,
                'modal' => 'add-department',
            ]))
            ->post(route('admin.departments.store'), [
                'dept_college_id' => $this->collegeId,
                'dept_name' => 'Program <One>',
                'dept_code' => 'P2',
            ])
            ->assertSessionHasErrors('dept_name');

        $this->actingAs($this->admin())
            ->from(route('admin.departments', ['college_id' => $this->collegeId]))
            ->put(route('admin.departments.update', $this->departmentId), [
                'dept_college_id' => $this->collegeId,
                'dept_name' => 'Program One',
                'dept_code' => 'P<2>',
            ])
            ->assertSessionHasErrors('dept_code');
    }

    private function admin(): User
    {
        return User::create([
            'usr_id' => (string) Str::uuid(),
            'usr_name' => 'Test Admin',
            'usr_email' => 'admin@example.com',
            'usr_password_hash' => Hash::make('admin-password'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);
    }
}
