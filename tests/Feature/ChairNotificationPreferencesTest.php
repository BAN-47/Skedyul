<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChairNotificationPreferencesTest extends TestCase
{
    public function test_chair_notification_preference_is_saved_separately_for_each_user(): void
    {
        $this->withoutMiddleware(\App\Http\Middleware\AuditActivityMiddleware::class);

        Schema::create('USER', function (Blueprint $table) {
            $table->string('usr_id')->primary();
            $table->string('usr_name');
            $table->string('usr_email')->unique();
            $table->string('usr_password_hash');
            $table->string('usr_role')->nullable();
            $table->boolean('usr_is_active')->default(true);
        });
        Schema::create('system_setting', function (Blueprint $table) {
            $table->uuid('sset_id')->primary();
            $table->string('sset_key', 100)->unique();
            $table->text('sset_value');
            $table->string('sset_updated_by')->nullable();
            $table->timestampTz('sset_updated_at')->useCurrent();
        });

        $firstChair = User::create([
            'usr_id' => 'chair-1',
            'usr_name' => 'First Chair',
            'usr_email' => 'chair-1@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'department_chair',
        ]);
        $secondChair = User::create([
            'usr_id' => 'chair-2',
            'usr_name' => 'Second Chair',
            'usr_email' => 'chair-2@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'department_chair',
        ]);

        $this->actingAs($firstChair)
            ->putJson(route('chair.profile.notification-preferences.update'), [
                'chair_notif_faculty_overload' => false,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'chair_notif_user_chair-1_chair_notif_faculty_overload',
            'sset_value' => '0',
            'sset_updated_by' => 'chair-1',
        ]);
        $this->assertDatabaseMissing('system_setting', [
            'sset_key' => 'chair_notif_user_chair-2_chair_notif_faculty_overload',
        ]);
        $this->assertDatabaseMissing('system_setting', [
            'sset_key' => 'chair_notif_faculty_overload',
        ]);

        $this->actingAs($secondChair)
            ->putJson(route('chair.profile.notification-preferences.update'), [
                'chair_notif_faculty_overload' => true,
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'chair_notif_user_chair-2_chair_notif_faculty_overload',
            'sset_value' => '1',
            'sset_updated_by' => 'chair-2',
        ]);
        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'chair_notif_user_chair-1_chair_notif_faculty_overload',
            'sset_value' => '0',
            'sset_updated_by' => 'chair-1',
        ]);
    }
}
