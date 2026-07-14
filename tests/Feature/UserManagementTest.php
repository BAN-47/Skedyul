<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_user_from_the_users_form(): void
    {
        Schema::create('USER', function (Blueprint $table) {
            $table->string('USR_ID')->primary();
            $table->string('USR_NAME');
            $table->string('USR_EMAIL')->unique();
            $table->string('USR_PASSWORD_HASH');
            $table->string('USR_ROLE')->nullable();
            $table->boolean('USR_IS_ACTIVE')->default(true);
            $table->timestamps();
        });

        $response = $this->post(route('admin.users.store'), [
            'usr_name' => 'Juan Dela Cruz',
            'usr_email' => 'juan@example.com',
            'password' => 'secret1234',
            'usr_role' => 'faculty',
        ]);

        $response->assertRedirect(route('admin.users'));
        $this->assertDatabaseHas('USER', [
            'USR_EMAIL' => 'juan@example.com',
            'USR_NAME' => 'Juan Dela Cruz',
        ]);

        $user = User::where('USR_EMAIL', 'juan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('secret1234', $user->USR_PASSWORD_HASH));
    }
}
