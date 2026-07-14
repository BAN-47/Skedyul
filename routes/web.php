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

Route::get('/dashboard', function () {
    $users = User::orderBy('usr_name')->take(5)->get();

    $roleCounts = [
        'faculty'          => User::where('usr_role', 'faculty')->count(),
        'department_chair' => User::where('usr_role', 'department_chair')->count(),
        'dean'             => User::where('usr_role', 'dean')->count(),
        'system_admin'     => User::where('usr_role', 'system_admin')->count(),
    ];

    $totalUsers = array_sum($roleCounts);

    return view('admin.admin_dashboard', compact('users', 'roleCounts', 'totalUsers'));
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

    Route::get('/rooms', function () {
        return view('admin.rooms');
    })->name('admin.rooms');

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

