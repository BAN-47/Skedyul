<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\Designation;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDesignationValidationTest extends TestCase
{
    private string $designationId;

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
        Schema::create('designations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name')->unique();
            $table->integer('day_cap');
            $table->integer('evening_cap');
            $table->integer('max_faculty_count')->nullable();
            $table->timestamps();
        });

        $this->designationId = (string) Str::uuid();
        DB::table('designations')->insert([
            'id' => $this->designationId,
            'name' => 'Research Chair',
            'day_cap' => 30,
            'evening_cap' => 30,
        ]);
    }

    public function test_admin_cannot_create_a_designation_with_angle_brackets(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('admin.designations.store'), $this->designationData([
                'name' => 'Research <Chair>',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_admin_cannot_update_a_designation_with_angle_brackets(): void
    {
        $designation = Designation::findOrFail($this->designationId);

        $this->actingAs($this->admin())
            ->putJson(route('admin.designations.update', $designation), $this->designationData([
                'name' => 'Research <Chair>',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_admin_can_create_a_designation_name_with_spaces(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('admin.designations.store'), $this->designationData([
                'name' => 'College Secretary',
            ]))
            ->assertOk()
            ->assertJsonPath('success', true);
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

    private function designationData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Research Chair',
            'day_cap' => 30,
            'evening_cap' => 30,
            'max_faculty_count' => null,
        ], $overrides);
    }
}
