<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dean\DeanFacultyWorkloadController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Dean\DeanDashboardController;
use App\Http\Controllers\Dean\ScheduleReportsController;
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

    /*
    |--------------------------------------------------------------------------
    | FACULTY WORKLOAD
    |--------------------------------------------------------------------------
    */
    Route::get('/faculty-workload', [DeanFacultyWorkloadController::class, 'index'])
        ->name('faculty_workload');

    /*
    |--------------------------------------------------------------------------
    | DEPARTMENTS
    |--------------------------------------------------------------------------
    */
    Route::get('/departments', function () {
        return view('dean.departments');
    })->name('departments');

    Route::get('/pending-approvals', function () {
        return view('dean.pending_approvals');
    })->name('pending_approvals');

    /*
    |--------------------------------------------------------------------------
    | SCHEDULE REPORTS
    |--------------------------------------------------------------------------
    */
    Route::get('/reports', [ScheduleReportsController::class, 'index'])->name('schedule_reports');
    Route::get('/reports/department/{deptId}', [ScheduleReportsController::class, 'departmentDetail'])->name('dept.detail');
    Route::get('/reports/conflicts/{sectionId?}', [ScheduleReportsController::class, 'detectConflicts'])->name('conflicts');
    Route::post('/notify', [ScheduleReportsController::class, 'sendNotification'])->name('notify');

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