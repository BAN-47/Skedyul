<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}