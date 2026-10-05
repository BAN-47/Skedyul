<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit_Log;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activityLogs = Audit_Log::query()
            ->with('user')
            ->orderByDesc('al_created_at')
            ->get();

        $totalActivities = Audit_Log::count();
        $todayActivities = Audit_Log::whereDate('al_created_at', today())->count();
        $loginActivities = Audit_Log::where('al_action', 'Logged in')->count();
        return view('admin.activity_logs', compact('activityLogs', 'totalActivities', 'todayActivities', 'loginActivities'));
    }
}
