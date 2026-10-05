<?php

namespace App\Services;

use App\Models\Audit_Log;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuditActivityLogger
{
    public static function record(User $user, string $action, string $target, string $description, ?string $ip = null, ?string $targetId = null): void
    {
        try {
            Audit_Log::create([
                'al_usr_id' => $user->usr_id,
                'al_action' => substr($action, 0, 100),
                'al_target_table' => substr($target ?: 'application', 0, 100),
                'al_target_id' => $targetId,
                'al_description' => $description,
                'al_ip_address' => $ip,
                'al_created_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            // Audit failures should not interrupt a user's normal application action.
            Log::warning('Unable to write audit activity.', ['message' => $exception->getMessage()]);
        }
    }
}
