<?php

use Illuminate\Support\Facades\Route;
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

    Route::get('/pending-approvals', function () {
        return view('dean.pending_approvals');
    })->name('pending_approvals');

    Route::get('/schedule-reports', function () {
        return view('dean.schedule_reports');
    })->name('schedule_reports');

    Route::get('/faculty-deployment', function () {
        return view('dean.faculty_deployment');
    })->name('faculty_deployment');

    Route::get('/settings', function () {
        return view('dean.settings');
    })->name('settings');

    Route::post('/schedule/{id}/approve', [DeanDashboardController::class, 'approve'])
        ->name('schedule.approve');

    Route::post('/schedule/{id}/return', [DeanDashboardController::class, 'returnSubmission'])
        ->name('schedule.return');
});
