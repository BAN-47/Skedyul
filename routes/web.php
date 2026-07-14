<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SubjectController;


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
Route::prefix('admin')->group(function () {

    Route::get('/dashboard', function () {
        return view('admin.admin_dashboard');
    })->name('admin.dashboard');


    Route::get('/users', function () {
        return view('admin.user_accounts');
    })->name('admin.users');


    Route::get('/subjects', function () {
        return view('admin.subjects');
    })->name('admin.subjects');


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
Route::resource('subject', SubjectController::class);