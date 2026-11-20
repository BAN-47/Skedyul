<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Departments;
use App\Models\User;
use App\Services\AdminIssueAttachmentStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    // GET /dean/notifications
    public function index()
    {
        $dean = Dean::where('dean_usr_id', auth()->id())->first();
        abort_unless(auth()->user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        // Use the same department-chair relationships used by the Dean's
        // existing faculty deployment page. This keeps the picker scoped to
        // this college and avoids a fragile raw join to the quoted USER table.
        $departmentIds = Departments::where('dept_college_id', $dean->dean_college_id)->pluck('dept_id');
        $chairs = Dept_Chair::with(['program', 'user'])
            ->whereIn('dc_dept_id', $departmentIds)
            ->whereHas('user', fn ($query) => $query
                ->where('usr_role', 'department_chair')
                ->where('usr_is_active', true))
            ->orderBy('dc_last_name')
            ->get()
            ->unique('dc_usr_id')
            ->values()
            ->map(function ($chair) {
                $name = trim(implode(' ', array_filter([
                    $chair->dc_first_name,
                    $chair->dc_middle_name,
                    $chair->dc_last_name,
                    $chair->dc_suffix,
                ])));
                return [
                    'dc_usr_id' => $chair->dc_usr_id,
                    'chair_name' => $name ?: 'Unnamed Chair',
                    'initials'   => strtoupper(substr($chair->dc_first_name ?? '', 0, 1) . substr($chair->dc_last_name ?? '', 0, 1)),
                    'dept_code' => $chair->program?->dept_code ?? 'N/A',
                ];
            });

        return response()->json(['chairs' => $chairs]);
    }

    // POST /dean/notifications/send
    public function send(Request $request)
    {
        $request->validate([
            'chair_ids'   => 'required|array|min:1',
            'chair_ids.*' => 'required|uuid|distinct',
            'title'       => 'required|string|max:200',
            'message'     => 'required|string|max:2000',
            'type'        => 'required|in:info,reminder,urgent,deadline',
        ]);

        $dean = Dean::where('dean_usr_id', auth()->id())->first();
        abort_unless(auth()->user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        // Recheck recipients on submit so a crafted request cannot message
        // chairs outside the dean's college.
        $departmentIds = Departments::where('dept_college_id', $dean->dean_college_id)->pluck('dept_id');
        $chairIds = Dept_Chair::whereIn('dc_dept_id', $departmentIds)
            ->whereIn('dc_usr_id', $request->input('chair_ids'))
            ->whereHas('user', fn ($query) => $query
                ->where('usr_role', 'department_chair')
                ->where('usr_is_active', true))
            ->distinct()
            ->pluck('dc_usr_id');

        if ($chairIds->count() !== count($request->input('chair_ids'))) {
            return response()->json([
                'success' => false,
                'message' => 'One or more selected recipients are no longer department chairs. Refresh the page and try again.',
            ], 422);
        }

        $now = now();
        $rows = $chairIds->map(fn ($usrId) => [
            'notif_id'         => (string) Str::uuid(),
            'notif_usr_id'     => $usrId,
            'notif_title'      => $request->input('title'),
            'notif_message'    => $request->input('message'),
            'notif_type'       => $request->input('type'),
            'notif_is_read'    => false,
            'notif_created_at' => $now,
            'notif_updated_at' => $now,
        ])->all();

        DB::table('notification')->insert($rows);

        return response()->json([
            'success' => true,
            'sent'    => count($rows),
            'message' => 'Notification sent to ' . count($rows) . ' chair(s).',
        ]);
    }

    // POST /dean/notifications/report-issue
    public function reportIssue(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:175',
            'message' => 'required|string|max:1700',
            'type'    => 'required|in:bug,system_error,feature_request,access,other',
            'attachment' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        abort_unless(auth()->user()?->usr_role === 'dean', 403);

        $admins = User::query()
            ->where('usr_role', 'system_admin')
            ->where('usr_is_active', true)
            ->pluck('usr_id');

        if ($admins->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active system administrators can receive this report right now.',
            ], 422);
        }

        $reporter = auth()->user();
        $reporterName = trim(strip_tags((string) ($reporter->usr_name ?: implode(' ', array_filter([
            $reporter->usr_first_name,
            $reporter->usr_middle_name,
            $reporter->usr_last_name,
        ])))));
        $safeTitle = trim(strip_tags($validated['title']));
        $safeMessage = trim(strip_tags($validated['message']));
        $notificationType = in_array($validated['type'], ['bug', 'system_error'], true) ? 'urgent' : 'info';
        $now = now();
        $attachmentStorage = app(AdminIssueAttachmentStorage::class);
        $attachmentFilename = null;
        if ($request->hasFile('attachment')) {
            try {
                $attachmentFilename = $attachmentStorage->store($request->file('attachment'));
            } catch (\Throwable $exception) {
                return response()->json([
                    'success' => false,
                    'message' => 'The screenshot could not be saved. Please try again.',
                ], 422);
            }
        }
        $attachmentLine = $attachmentFilename
            ? "\n\nScreenshot: " . route('admin.notifications.attachment', ['filename' => $attachmentFilename])
            : '';

        $rows = $admins->map(fn ($adminId) => [
            'notif_id'         => (string) Str::uuid(),
            'notif_usr_id'     => $adminId,
            'notif_title'      => '[Dean Issue Report] ' . $safeTitle,
            'notif_message'    => "Reported by {$reporterName} (" . strip_tags((string) $reporter->usr_email) . ")\n\nIssue category: {$validated['type']}\n\n{$safeMessage}{$attachmentLine}",
            'notif_type'       => $notificationType,
            'notif_is_read'    => false,
            'notif_created_at' => $now,
            'notif_updated_at' => $now,
        ])->all();

        try {
            DB::transaction(fn () => DB::table('notification')->insert($rows));
        } catch (\Throwable $exception) {
            $attachmentStorage->delete($attachmentFilename);
            throw $exception;
        }

        return response()->json([
            'success' => true,
            'sent' => count($rows),
            'message' => 'Issue report sent to the system administrators.',
        ]);
    }

    // GET /dean/notifications/unread
    public function unreadCount()
    {
        $count = DB::table('notification')
            ->where('notif_usr_id', auth()->id())
            ->where('notif_is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    // POST /dean/notifications/{id}/read
    public function markRead($id)
    {
        try {
            DB::table('notification')
                ->where('notif_id', $id)
                ->where('notif_usr_id', auth()->id())
                ->update([
                    'notif_is_read'    => true,
                    'notif_updated_at' => now(),
                ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to update the notification because of a database error.',
            ], 500);
        }

        return response()->json(['success' => true]);
    }
}
