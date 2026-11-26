<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class DeactivateInactiveUsersTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('USER', function (Blueprint $table): void {
            $table->string('usr_id')->primary();
            $table->boolean('usr_is_active');
            $table->timestamp('usr_last_login_at')->nullable();
        });
    }

    public function test_deactivates_only_accounts_inactive_for_at_least_fifteen_days(): void
    {
        $now = now();
        DB::table('USER')->insert([
            [
                'usr_id' => 'inactive-user',
                'usr_is_active' => true,
                'usr_last_login_at' => $now->copy()->subDays(15)->subSecond(),
            ],
            [
                'usr_id' => 'recent-user',
                'usr_is_active' => true,
                'usr_last_login_at' => $now->copy()->subDays(14),
            ],
            [
                'usr_id' => 'already-inactive-user',
                'usr_is_active' => false,
                'usr_last_login_at' => $now->copy()->subDays(30),
            ],
        ]);

        Artisan::call('app:deactivate-inactive-users');

        $this->assertDatabaseHas('USER', ['usr_id' => 'inactive-user', 'usr_is_active' => false]);
        $this->assertDatabaseHas('USER', ['usr_id' => 'recent-user', 'usr_is_active' => true]);
        $this->assertDatabaseHas('USER', ['usr_id' => 'already-inactive-user', 'usr_is_active' => false]);
    }
}
