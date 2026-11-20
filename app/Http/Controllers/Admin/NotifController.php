<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NotifController extends Controller
{
    // dropdown list (latest, e.g. 10)
    public function index()
    {
        $notifications = Notification::where('notif_usr_id', Auth::id())
            ->orderByDesc('notif_created_at')
            ->take(10)
            ->get();

        return response()->json($notifications);
    }

    // just the badge count, used for polling
    public function unreadCount()
    {
        $count = Notification::where('notif_usr_id', Auth::id())
            ->unread()
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAsRead($id)
    {
        $notif = Notification::where('notif_id', $id)
            ->where('notif_usr_id', Auth::id())
            ->firstOrFail();

        $notif->update(['notif_is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        Notification::where('notif_usr_id', Auth::id())
            ->unread()
            ->update(['notif_is_read' => true]);

        return response()->json(['success' => true]);
    }

    public function sendAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'audience' => 'required|in:all,deans,chairs,faculty',
            'type' => 'required|in:info,urgent,deadline',
            'title' => 'required|string|max:200',
            'message' => 'required|string|max:2000',
        ]);

        $recipients = User::query()
            ->where('usr_is_active', true)
            ->when($validated['audience'] !== 'all', function ($query) use ($validated) {
                $role = match ($validated['audience']) {
                    'deans' => 'dean',
                    'chairs' => 'department_chair',
                    'faculty' => 'faculty',
                };
                $query->where('usr_role', $role);
            })
            ->pluck('usr_id');

        if ($recipients->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'There are no active accounts in that recipient group.',
            ], 422);
        }

        $title = trim(strip_tags($validated['title']));
        $message = trim(strip_tags($validated['message']));
        $now = now();

        DB::transaction(function () use ($recipients, $validated, $title, $message, $now): void {
            foreach ($recipients->chunk(500) as $recipientChunk) {
                $rows = $recipientChunk->map(fn ($userId) => [
                    'notif_id' => (string) Str::uuid(),
                    'notif_usr_id' => $userId,
                    'notif_title' => $title,
                    'notif_message' => $message,
                    'notif_type' => $validated['type'],
                    'notif_is_read' => false,
                    'notif_created_at' => $now,
                    'notif_updated_at' => $now,
                ])->all();

                DB::table('notification')->insert($rows);
            }
        });

        return response()->json([
            'success' => true,
            'sent' => $recipients->count(),
            'message' => 'Announcement sent to ' . $recipients->count() . ' active user(s).',
        ]);
    }
}
