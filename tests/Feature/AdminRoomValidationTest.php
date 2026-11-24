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

class AdminRoomValidationTest extends TestCase
{
    private string $collegeId;

    private string $roomId;

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
        });
        Schema::create('room', function (Blueprint $table) {
            $table->uuid('room_id')->primary();
            $table->string('room_name');
            $table->uuid('room_college_id');
            $table->string('room_type');
            $table->integer('room_capacity');
            $table->boolean('room_is_available');
            $table->string('room_building')->nullable();
            $table->timestamp('room_created_at')->nullable();
            $table->timestamp('room_updated_at')->nullable();
        });

        $this->collegeId = (string) Str::uuid();
        $this->roomId = (string) Str::uuid();
        DB::table('college')->insert(['college_id' => $this->collegeId]);
        DB::table('room')->insert([
            'room_id' => $this->roomId,
            'room_name' => 'Room 303',
            'room_college_id' => $this->collegeId,
            'room_type' => 'Lecture',
            'room_capacity' => 40,
            'room_is_available' => true,
        ]);
    }

    public function test_admin_cannot_create_a_room_with_angle_brackets(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('admin.rooms.store'), $this->roomData([
                'room_name' => 'Room <303>',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_name');
    }

    public function test_admin_cannot_update_a_room_with_angle_brackets(): void
    {
        $this->actingAs($this->admin())
            ->putJson(route('admin.rooms.update', $this->roomId), $this->roomData([
                'room_building' => 'ICT <Building>',
            ]))
            ->assertUnprocessable()
            ->assertJsonValidationErrors('room_building');
    }

    public function test_admin_can_create_a_room_with_spaced_name_type_and_building(): void
    {
        $this->actingAs($this->admin())
            ->postJson(route('admin.rooms.store'), $this->roomData([
                'room_name' => 'Room 304',
                'room_type' => 'AVR / Function Hall',
                'room_building' => 'ICT Building',
            ]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('room', [
            'room_name' => 'Room 304',
            'room_type' => 'AVR / Function Hall',
            'room_building' => 'ICT Building',
        ]);
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

    private function roomData(array $overrides = []): array
    {
        return array_merge([
            'room_name' => 'Room 303',
            'room_college_id' => $this->collegeId,
            'room_type' => 'Lecture',
            'room_capacity' => 40,
            'room_is_available' => true,
            'room_building' => 'ICT Building',
        ], $overrides);
    }
}
