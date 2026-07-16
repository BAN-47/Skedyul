<?php

use Illuminate\Support\Facades\Route;
<<<<<<< HEAD
use App\Http\Controllers\Dean\DeanFacultyWorkloadController;

Route::get('/dean/dashboard', function () {
    return view('dean.dean_dashboard');
})->name('dean.dashboard');
/*
|--------------------------------------------------------------------------
| FACULTY WORKLOAD
|--------------------------------------------------------------------------
*/
Route::get('/dean/faculty-workload', [DeanFacultyWorkloadController::class, 'index'])->name('dean.faculty_workload');
/*
|--------------------------------------------------------------------------
| DEPARTMENTS
|--------------------------------------------------------------------------
*/
Route::get('/dean/departments', function () {
    return view('dean.departments');
})->name('dean.departments');
=======
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Dean\DeanDashboardController;
use Illuminate\Support\Facades\DB;
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

Route::middleware('auth')->prefix('dean')->name('dean.')->group(function () {

    Route::get('/dashboard', [DeanDashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/faculty-workload', function () {
        return view('dean.faculty_workload');
    })->name('faculty_workload');

    Route::get('/departments', function () {
        return view('dean.departments');
    })->name('departments');

    Route::post('/schedule/{id}/approve', [DeanDashboardController::class, 'approve'])
        ->name('schedule.approve');

    Route::post('/schedule/{id}/return', [DeanDashboardController::class, 'returnSubmission'])
        ->name('schedule.return');
});
>>>>>>> b4d1ec204e939abb2c61a9c80b5fb9c1580ee80f
