<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class LoginControllerAvailabilityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('USER', function (Blueprint $table) {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_email');
            $table->string('usr_password_hash');
            $table->string('usr_role');
            $table->boolean('usr_is_active')->default(true);
            $table->unsignedInteger('usr_failed_login_attempts')->default(0);
            $table->timestamp('usr_locked_until')->nullable();
        });
    }

    public function test_valid_login_still_redirects_to_the_role_dashboard(): void
    {
        $userId = (string) Str::uuid();
        DB::table('USER')->insert([
            'usr_id' => $userId,
            'usr_name' => 'Admin User',
            'usr_email' => 'admin@example.test',
            'usr_password_hash' => Hash::make('valid-password'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);
        config(['services.recaptcha.secret_key' => 'test-secret']);
        Http::fake([
            'www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true]),
        ]);

        $this->post(route('login.authenticate'), [
            'email' => 'ADMIN@example.test',
            'password' => 'valid-password',
            'g-recaptcha-response' => 'valid-test-token',
        ])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs(User::findOrFail($userId));
    }
}
