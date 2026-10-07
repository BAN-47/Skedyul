<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class FacultyNotificationPreferencesTest extends TestCase
{
    public function test_faculty_notification_preferences_are_saved_for_the_authenticated_user_only(): void
    {
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

        $faculty = User::create([
            'usr_id' => 'faculty-1',
            'usr_name' => 'Faculty One',
            'usr_email' => 'faculty-one@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'faculty',
            'usr_is_active' => true,
        ]);
        User::create([
            'usr_id' => 'faculty-2',
            'usr_name' => 'Faculty Two',
            'usr_email' => 'faculty-two@example.com',
            'usr_password_hash' => 'password-hash',
            'usr_role' => 'faculty',
            'usr_is_active' => true,
        ]);

        $response = $this->actingAs($faculty)->put(route('faculty.profile.notification-preferences.update'), [
            'faculty_notif_schedule_updates' => false,
            'faculty_notif_new_assignments' => true,
            'faculty_notif_reminders' => false,
            'faculty_notif_system_announcements' => true,
        ]);

        $response->assertOk()->assertJson(['success' => true]);
        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'faculty_notif_user_faculty-1_faculty_notif_schedule_updates',
            'sset_value' => '0',
            'sset_updated_by' => 'faculty-1',
        ]);
        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'faculty_notif_user_faculty-1_faculty_notif_new_assignments',
            'sset_value' => '1',
            'sset_updated_by' => 'faculty-1',
        ]);
        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'faculty_notif_user_faculty-1_faculty_notif_reminders',
            'sset_value' => '0',
            'sset_updated_by' => 'faculty-1',
        ]);
        $this->assertDatabaseHas('system_setting', [
            'sset_key' => 'faculty_notif_user_faculty-1_faculty_notif_system_announcements',
            'sset_value' => '1',
            'sset_updated_by' => 'faculty-1',
        ]);
        $this->assertDatabaseMissing('system_setting', [
            'sset_key' => 'faculty_notif_user_faculty-2_faculty_notif_schedule_updates',
        ]);
    }
}
