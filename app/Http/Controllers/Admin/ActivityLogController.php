<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit_Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:200'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'role' => ['nullable', 'in:system_admin,department_chair,dean,faculty'],
        ]);
        $driver = DB::connection()->getDriverName();
        $likeOperator = $driver === 'pgsql' ? 'ILIKE' : 'LIKE';
        $targetIdExpression = $driver === 'pgsql'
            ? 'CAST(al_target_id AS TEXT)'
            : 'CAST(al_target_id AS CHAR)';

        $query = Audit_Log::query()
            ->with('user')
            ->when($filters['search'] ?? null, function ($query, string $search) use ($likeOperator, $targetIdExpression): void {
                $like = '%' . addcslashes($search, '%_\\') . '%';
                $query->where(function ($query) use ($like, $likeOperator, $targetIdExpression): void {
                    $query->where('al_action', $likeOperator, $like)
                        ->orWhere('al_description', $likeOperator, $like)
                        ->orWhere('al_target_table', $likeOperator, $like)
                        ->orWhereRaw("{$targetIdExpression} {$likeOperator} ?", [$like])
                        ->orWhere('al_ip_address', $likeOperator, $like)
                        ->orWhereHas('user', function ($userQuery) use ($like, $likeOperator): void {
                            $userQuery->where('usr_name', $likeOperator, $like)
                                ->orWhere('usr_email', $likeOperator, $like);
                        });
                });
            })
            ->when($filters['date'] ?? null, function ($query, string $date): void {
                $start = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'))->startOfDay();
                $query->where('al_created_at', '>=', $start)
                    ->where('al_created_at', '<', $start->copy()->addDay());
            })
            ->when($filters['role'] ?? null, fn ($query, string $role) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('usr_role', $role)))
            ->orderByDesc('al_created_at')
            ->orderByDesc('al_id');

        // Fetch just one page from PostgreSQL; the full audit history stays in the database.
        $activityLogs = $query->paginate(25)->withQueryString();

        $totalActivities = Audit_Log::count();
        $todayActivities = Audit_Log::whereDate('al_created_at', today())->count();
        $loginActivities = Audit_Log::where('al_action', 'Logged in')->count();
        return view('admin.activity_logs', compact('activityLogs', 'totalActivities', 'todayActivities', 'loginActivities'));
    }
}
