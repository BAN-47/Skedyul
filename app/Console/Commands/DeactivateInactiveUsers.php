<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeactivateInactiveUsers extends Command
{
    protected $signature = 'app:deactivate-inactive-users';

    protected $description = 'Deactivate accounts with no login activity for 15 days';

    public function handle(): int
    {
        $deactivated = User::query()
            ->where('usr_is_active', true)
            ->where('usr_last_login_at', '<=', now()->subDays(15))
            ->update(['usr_is_active' => false]);

        $this->info("Deactivated {$deactivated} inactive user account(s).");

        return self::SUCCESS;
    }
}
