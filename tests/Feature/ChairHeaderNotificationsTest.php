<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ChairHeaderNotificationsTest extends TestCase
{
    public function test_chair_header_view_compiles_notification_endpoints(): void
    {
        $this->view('partials.chair_header', ['title' => 'Settings'])
            ->assertSee('chairHeaderToggleNotifDropdown')
            ->assertSee('notifications\\/unread-count', false);
    }

    public function test_chair_notifications_and_read_actions_are_scoped_to_the_signed_in_user(): void
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
        Schema::create('notification', function (Blueprint $table) {
            $table->uuid('notif_id')->primary();
            $table->string('notif_usr_id');
            $table->string('notif_title', 200);
            $table->text('notif_message');
            $table->string('notif_type', 50)->default('info');
            $table->boolean('notif_is_read')->default(false);
            $table->timestampTz('notif_created_at')->useCurrent();
            $table->timestampTz('notif_updated_at')->nullable();
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

        $firstChairNotifications = collect(range(1, 12))->map(fn (int $number) => Notification::create([
            'notif_usr_id' => $firstChair->usr_id,
            'notif_title' => "Chair notification {$number}",
            'notif_message' => 'Private to first chair',
            'notif_type' => 'info',
            'notif_is_read' => false,
        ]));
        $secondChairNotification = Notification::create([
            'notif_usr_id' => $secondChair->usr_id,
            'notif_title' => 'Private notification',
            'notif_message' => 'Private to second chair',
            'notif_type' => 'info',
            'notif_is_read' => false,
        ]);

        $this->actingAs($firstChair)
            ->getJson(route('chair.notifications.index'))
            ->assertOk()
            ->assertJsonCount(10)
            ->assertJsonMissing(['notif_id' => $secondChairNotification->notif_id]);

        $this->getJson(route('chair.notifications.unread-count'))
            ->assertOk()
            ->assertJson(['count' => 12]);

        $this->postJson(route('chair.notifications.read', [
            'notification' => $secondChairNotification->notif_id,
        ]))->assertForbidden();

        $this->postJson(route('chair.notifications.read', [
            'notification' => $firstChairNotifications->first()->notif_id,
        ]))->assertOk()->assertJson(['success' => true]);

        $this->postJson(route('chair.notifications.read-all'))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('notification', [
            'notif_id' => $secondChairNotification->notif_id,
            'notif_is_read' => false,
        ]);

        $this->actingAs($secondChair)
            ->getJson(route('chair.notifications.unread-count'))
            ->assertOk()
            ->assertJson(['count' => 1]);
    }
}
