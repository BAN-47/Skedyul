<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\NotifController;

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
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('index');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD  (single source of truth — no duplicate route)
|--------------------------------------------------------------------------
*/
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Section;
use App\Models\Program;
use App\Models\Schedule;
use App\Models\Subjects;

Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotifController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread-count', [NotifController::class, 'unreadCount'])->name('notifications.unread-count');
    Route::put('/notifications/{id}/read', [NotifController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notifications/read-all', [NotifController::class, 'markAllRead'])->name('notifications.read-all');
});

/*
|--------------------------------------------------------------------------
| USER ACCOUNTS (admin)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('admin.users');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/reports', function () {
        return view('admin.reports');
    })->name('admin.reports');

    Route::get('/settings', function () {
        return view('admin.admin_settings');
    })->name('admin.admin_settings');
});

/*
|--------------------------------------------------------------------------
| SUBJECTS (admin)
|--------------------------------------------------------------------------
*/
Route::get('/subject', [SubjectController::class, 'index'])->name('subject.index');
Route::post('/subject', [SubjectController::class, 'store'])->name('subject.store');
Route::put('/subject/{id}', [SubjectController::class, 'update'])->name('subject.update');
Route::delete('/subject/{id}', [SubjectController::class, 'destroy'])->name('subject.destroy');

/*
|--------------------------------------------------------------------------
| ROOMS (admin)
|--------------------------------------------------------------------------
*/
Route::get('/rooms', [RoomController::class, 'index'])->name('admin.rooms');
Route::post('/rooms', [RoomController::class, 'store'])->name('admin.rooms.store');
Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('admin.rooms.show');
Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('admin.rooms.update');
Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('admin.rooms.destroy');
/*
|--------------------------------------------------------------------------
| Department And Programs 
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\DepartmentController;


Route::get('/programs',
[ProgramController::class,'index'])
->name('admin.programs');


Route::get('/departments',
[DepartmentController::class,'index'])
->name('admin.departments');

