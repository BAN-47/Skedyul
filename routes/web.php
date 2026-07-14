<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\SubjectController;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});
/*
|--------------------------------------------------------------------------
| DATABASE TEST
|--------------------------------------------------------------------------
*/

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Supabase Connected Successfully";
    } catch (\Exception $e) {
        return "❌ Database Error: " . $e->getMessage();
    }

});
/*
|--------------------------------------------------------------------------
| LOGIN PAGE
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {

    return view('index');

})->name('login');


Route::post('/login',[LoginController::class,'login'])->name('login.authenticate');
/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout',
    [LoginController::class,'logout']
)->name('logout');
/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return view('admin.admin_dashboard');
})->name('admin.dashboard');

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/
use App\Models\Chair\AcademicYear;
use App\Models\Chair\Semester;
use App\Models\Admin\Section;
use App\Models\Admin\Program;
use App\Models\Chair\Schedule;
use App\Models\Admin\Subjects;

Route::get('/dashboard', function () {
    $users = User::orderBy('usr_name')->take(5)->get();

    $roleCounts = [
        'faculty'          => User::where('usr_role', 'faculty')->count(),
        'department_chair' => User::where('usr_role', 'department_chair')->count(),
        'dean'             => User::where('usr_role', 'dean')->count(),
        'system_admin'     => User::where('usr_role', 'system_admin')->count(),
    ];

    $totalUsers = array_sum($roleCounts);

    $academicYear = AcademicYear::where('ay_is_active', true)->first();
    $semester     = Semester::where('sem_is_active', true)->first();

    $section = Section::with('program')
        ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
        ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
        ->get();

    $scheduledCount   = $section->where('sec_status', 'Scheduled')->count();
    $inProgressCount  = $section->where('sec_status', 'In Progress')->count();
    $unscheduledCount = $section->where('sec_status', 'Unscheduled')->count();

    $program = $section
        ->groupBy(fn ($s) => $s->program->prog_name ?? 'Unknown')
        ->map(function ($group, $programName) {
            $total = $group->count();
            $scheduled = $group->where('sec_status', 'Scheduled')->count();
            $percent = $total > 0 ? round(($scheduled / $total) * 100) : 0;

            $color = match(true) {
                $percent >= 90 => 'green',
                $percent >= 50 => 'amber',
                default        => 'red',
            };

            return ['name' => $programName, 'percent' => $percent, 'color' => $color];
        })
        ->values();

    // ---------- SUBJECTS ----------
    $subject = Subjects::with(['department', 'program'])->get();
    $totalSubjectsOffered = $subject->count();
    $scheduleConflicts = $subject->where('subj_is_active', false)->count(); // adjust once "conflict" logic exists

    // ---------- ROOMS (placeholder until Room model exists) ----------
    $room = collect();
    $totalRooms     = 0;
    $roomsInUse     = 0;
    $roomsAvailable = 0;

    // ---------- ACTIVITY LOG (placeholder until ActivityLog model exists) ----------
    $audit_log = collect();

    $dbRecordsCount = $totalUsers + $section->count() + $totalSubjectsOffered + $totalRooms;

    return view('admin.admin_dashboard', compact(
        'users', 'roleCounts', 'totalUsers', 'academicYear', 'semester',
        'section', 'program', 'scheduledCount', 'inProgressCount', 'unscheduledCount',
        'subject', 'totalSubjectsOffered', 'scheduleConflicts',
        'room', 'totalRooms', 'roomsInUse', 'roomsAvailable',
        'audit_log', 'dbRecordsCount'
    ));
})->name('admin.dashboard');
/*
|--------------------------------------------------------------------------
| User_accounts
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\UserController;

Route::prefix('admin')->group(function () {

    Route::get('/users', [UserController::class,'index'])->name('admin.users');
    Route::get('/users/{id}/edit', [UserController::class,'edit'])->name('admin.users.edit');
    Route::post('/users', [UserController::class,'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UserController::class,'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class,'destroy'])->name('admin.users.destroy');

    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('admin.reports');

    Route::get('/settings', function () {
        return view('admin.admin_settings');
    })->name('admin.admin_settings');

});
/*
|--------------------------------------------------------------------------
| SUBJECTS_ADMIN
|--------------------------------------------------------------------------
*/
    Route::get('/subject', [SubjectController::class, 'index'])->name('subject.index');
    Route::post('/subject', [SubjectController::class, 'store'])->name('subject.store');
    Route::put('/subject/{id}', [SubjectController::class, 'update'])->name('subject.update');
    Route::delete('/subject/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy');
/*
|--------------------------------------------------------------------------
| Room_ADMIN
|--------------------------------------------------------------------------
*/
    Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms');
    Route::post('/rooms', [RoomController::class, 'store'])->name('admin.rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('admin.rooms.show');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('admin.rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('admin.rooms.destroy');

