<?php

namespace App\Services;

use App\Models\Dean;
use App\Models\Departments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DeanApprovalNotifier
{
    public function submitted(string $departmentId, string $submissionType, string $semesterLabel): void
    {
        $department = Departments::find($departmentId);
        if (!$department) {
            return;
        }

        $deanUserIds = Dean::query()
            ->where('dean_college_id', $department->dept_college_id)
            ->whereHas('user', fn ($query) => $query
                ->where('usr_role', 'dean')
                ->where('usr_is_active', true))
            ->pluck('dean_usr_id')
            ->unique()
            ->values();

        if ($deanUserIds->isEmpty()) {
            return;
        }

        $kind = strtoupper($submissionType);
        $now = now();
        $rows = $deanUserIds->map(fn ($userId) => [
            'notif_id' => (string) Str::uuid(),
            'notif_usr_id' => $userId,
            'notif_title' => "New {$kind} schedule awaiting approval",
            'notif_message' => "{$department->dept_code} submitted a {$kind} schedule for {$semesterLabel}.",
            'notif_type' => 'approval',
            'notif_is_read' => false,
            'notif_created_at' => $now,
            'notif_updated_at' => $now,
        ])->all();

        DB::table('notification')->insert($rows);
    }
}
