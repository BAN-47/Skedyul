<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    // GET /dean/notifications
    public function index()
    {
        $chairs = Dept_Chair::with(['user', 'department'])
            ->get()
            ->sortBy(fn ($chair) => $chair->department?->dept_code ?? '')
            ->values()
            ->map(function ($chair) {
                $name = $chair->user?->usr_name ?: $chair->full_name;
                $first = $chair->dc_first_name ?? '';
                $last  = $chair->dc_last_name ?? '';

                return [
                    'dc_id'     => $chair->dc_id,
                    'dc_usr_id' => $chair->dc_usr_id,
                    'chair_name' => $name ?: 'Unnamed Chair',
                    'initials'   => strtoupper(
                    substr($first, 0, 1) . substr($last, 0, 1)
                    ),
                    'dept_code' => $chair->department?->dept_code ?? 'N/A',
                ];
            });

        return response()->json(['chairs' => $chairs]);
    }

    // POST /dean/notifications/send
    public function send(Request $request)
    {
        $request->validate([
            'chair_ids'   => 'required|array|min:1',
            'chair_ids.*' => 'required|string',
            'title'       => 'required|string|max:200',
            'message'     => 'required|string|max:2000',
            'type'        => 'required|in:info,reminder,urgent,deadline',
        ]);

        $sent = 0;

        foreach ($request->chair_ids as $usrId) {
            $exists = DB::table(DB::raw('"USER"'))
                ->where('usr_id', $usrId)
                ->where('usr_role', 'department_chair')
                ->exists();

            if (!$exists) continue;

            DB::table('notification')->insert([
                'notif_id'         => (string) Str::uuid(),
                'notif_usr_id'     => $usrId,
                'notif_title'      => $request->title,
                'notif_message'    => $request->message,
                'notif_type'       => $request->type,
                'notif_is_read'    => false,
                'notif_created_at' => now(),
                'notif_updated_at' => now(),
            ]);

            $sent++;
        }

        return response()->json([
            'success' => true,
            'sent'    => $sent,
            'message' => "Notification sent to {$sent} chair(s).",
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
        DB::table('notification')
            ->where('notif_id', $id)
            ->where('notif_usr_id', auth()->id())
            ->update([
                'notif_is_read'    => true,
                'notif_updated_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }
}