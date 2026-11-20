<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    // GET /dean/notifications
    public function index()
    {
        $dean = auth()->user()?->dean;
        abort_unless(auth()->user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        // Only fetch active chairs in this dean's college. Keeping this scoped
        // avoids loading every college's chairs into the picker.
        $chairs = DB::table('department_chair as dc')
            ->join(DB::raw('"USER" as u'), 'u.usr_id', '=', 'dc.dc_usr_id')
            ->join('department as d', 'd.dept_id', '=', 'dc.dc_dept_id')
            ->where('d.dept_college_id', $dean->dean_college_id)
            ->where('u.usr_role', 'department_chair')
            ->where('u.usr_is_active', true)
            ->select([
                'dc.dc_usr_id',
                'dc.dc_first_name',
                'dc.dc_last_name',
                'u.usr_name',
                'd.dept_code',
            ])
            ->orderBy('d.dept_code')
            ->orderBy('dc.dc_last_name')
            ->get()
            ->unique('dc_usr_id')
            ->values()
            ->map(function ($chair) {
                $name = $chair->usr_name ?: trim(($chair->dc_first_name ?? '') . ' ' . ($chair->dc_last_name ?? ''));
                return [
                    'dc_usr_id' => $chair->dc_usr_id,
                    'chair_name' => $name ?: 'Unnamed Chair',
                    'initials'   => strtoupper(substr($chair->dc_first_name ?? '', 0, 1) . substr($chair->dc_last_name ?? '', 0, 1)),
                    'dept_code' => $chair->dept_code ?? 'N/A',
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

        $dean = auth()->user()?->dean;
        abort_unless(auth()->user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        // Recheck recipients on submit so a crafted request cannot message
        // chairs outside the dean's college.
        $chairIds = DB::table('department_chair as dc')
            ->join(DB::raw('"USER" as u'), 'u.usr_id', '=', 'dc.dc_usr_id')
            ->join('department as d', 'd.dept_id', '=', 'dc.dc_dept_id')
            ->where('d.dept_college_id', $dean->dean_college_id)
            ->whereIn('dc.dc_usr_id', $request->input('chair_ids'))
            ->where('u.usr_role', 'department_chair')
            ->where('u.usr_is_active', true)
            ->distinct()
            ->pluck('dc.dc_usr_id');

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
