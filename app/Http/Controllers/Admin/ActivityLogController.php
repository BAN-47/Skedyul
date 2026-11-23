<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit_Log;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $databaseActivityAvailable = Schema::hasTable('database_activity_log');
        if ($databaseActivityAvailable) {
            $databaseQuery = DB::table('database_activity_log')
                ->when($filters['search'] ?? null, function ($query, string $search) use ($likeOperator): void {
                    $like = '%' . addcslashes($search, '%_\\') . '%';
                    $query->where(function ($query) use ($like, $likeOperator): void {
                        $query->where('dal_operation', $likeOperator, $like)
                            ->orWhere('dal_table_name', $likeOperator, $like)
                            ->orWhere('dal_changed_columns', $likeOperator, $like)
                            ->orWhere('dal_record_key', $likeOperator, $like)
                            ->orWhere('dal_database_user', $likeOperator, $like);
                    });
                })
                ->when($filters['date'] ?? null, function ($query, string $date): void {
                    $start = Carbon::createFromFormat('Y-m-d', $date, config('app.timezone'))->startOfDay();
                    $query->where('dal_created_at', '>=', $start)
                        ->where('dal_created_at', '<', $start->copy()->addDay());
                })
                ->orderByDesc('dal_created_at')->orderByDesc('dal_id');
            $databaseActivityLogs = $databaseQuery->paginate(25, ['*'], 'database_page')->withQueryString();
            $databaseActivityCount = DB::table('database_activity_log')->count();
            $databaseActivitiesToday = DB::table('database_activity_log')->whereDate('dal_created_at', today())->count();
        } else {
            $databaseActivityLogs = new LengthAwarePaginator([], 0, 25, LengthAwarePaginator::resolveCurrentPage('database_page'), [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'database_page',
                'query' => $request->query(),
            ]);
            $databaseActivityCount = 0;
            $databaseActivitiesToday = 0;
        }

        $totalActivities = Audit_Log::count() + $databaseActivityCount;
        $todayActivities = Audit_Log::whereDate('al_created_at', today())->count() + $databaseActivitiesToday;
        $loginActivities = Audit_Log::where('al_action', 'Logged in')->count();
        return view('admin.activity_logs', compact('activityLogs', 'databaseActivityLogs', 'databaseActivityAvailable', 'totalActivities', 'todayActivities', 'loginActivities'));
    }
}
