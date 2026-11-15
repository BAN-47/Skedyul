<?php

namespace Tests\Feature;

use App\Http\Middleware\AuditActivityMiddleware;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(AuditActivityMiddleware::class);

        // The production app uses the legacy-cased USER table; these focused
        // tests create that schema directly instead of running unrelated
        // Laravel starter migrations against an empty SQLite database.
        Schema::create('USER', function (Blueprint $table) {
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
    }

    public function test_admin_can_create_a_user_from_the_users_form(): void
    {
        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->newSystemAdminPayload())
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success', 'User created successfully.');

        $user = User::where('usr_email', 'juan@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('Juan Dela Cruz', $user->usr_name);
        $this->assertTrue(Hash::check('secret1234', $user->usr_password_hash));
    }

    public function test_admin_sees_a_clean_error_when_duplicate_user_email_is_submitted(): void
    {
        User::create([
            'usr_id' => 'existing-user',
            'usr_name' => 'Existing User',
            'usr_email' => 'duplicate@example.com',
            'usr_password_hash' => Hash::make('secret1234'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->from(route('admin.users'))
            ->post(route('admin.users.store'), [
                ...$this->newSystemAdminPayload(),
                'usr_email' => 'duplicate@example.com',
            ])
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas(
                'error',
                'This email is already registered. Please use a different email address.'
            );
    }

    public function test_same_display_name_is_allowed_for_different_email_addresses(): void
    {
        User::create([
            'usr_id' => 'existing-user',
            'usr_name' => 'Juan Dela Cruz',
            'usr_email' => 'existing@example.com',
            'usr_password_hash' => Hash::make('secret1234'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);

        $this->actingAs($this->admin())
            ->post(route('admin.users.store'), $this->newSystemAdminPayload())
            ->assertRedirect(route('admin.users'))
            ->assertSessionHas('success', 'User created successfully.');

        $this->assertSame(2, User::where('usr_name', 'Juan Dela Cruz')->count());
    }

    private function admin(): User
    {
        return User::create([
            'usr_id' => 'test-admin',
            'usr_name' => 'Test Admin',
            'usr_email' => 'admin@example.com',
            'usr_password_hash' => Hash::make('admin-password'),
            'usr_role' => 'system_admin',
            'usr_is_active' => true,
        ]);
    }

    private function newSystemAdminPayload(): array
    {
        return [
            'usr_first_name' => 'Juan',
            'usr_last_name' => 'Dela Cruz',
            'usr_email' => 'juan@example.com',
            'password' => 'secret1234',
            'usr_role' => 'system_admin',
        ];
    }
}
