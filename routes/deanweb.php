<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Dean\DeanDashboardController;
use App\Http\Controllers\Dean\DeanFacultyWorkloadController;
use App\Http\Controllers\Dean\DeanDepartmentController;
use App\Http\Controllers\Dean\PendingApprovalsController;

/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('index');
})->name('login');

Route::post('/login',  [LoginController::class, 'login'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| DEAN ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('dean')->name('dean.')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DeanDashboardController::class, 'index'])
        ->name('dashboard');

    // Faculty Workload
    Route::get('/faculty-workload', [DeanFacultyWorkloadController::class, 'index'])
        ->name('faculty_workload');

    // Departments
    Route::get('/departments', [DeanDepartmentController::class, 'index'])
        ->name('departments');

    // ── PENDING APPROVALS ─────────────────────────────────────────────
    // index    → GET  /dean/pending-approvals              → shows table of all submissions
    // review   → GET  /dean/pending-approvals/{id}/review  → AJAX: returns schedule rows partial
    // approve  → POST /dean/pending-approvals/{id}/approve → marks submission approved
    // return   → POST /dean/pending-approvals/{id}/return  → returns with remarks to chair
    Route::get ('/pending-approvals',
                [PendingApprovalsController::class, 'index'])
        ->name('pending_approvals');

    Route::get ('/pending-approvals/{id}/review',
                [PendingApprovalsController::class, 'review'])
        ->name('pending_approvals.review');

    Route::post('/pending-approvals/{id}/approve',
                [PendingApprovalsController::class, 'approve'])
        ->name('pending_approvals.approve');

    Route::post('/pending-approvals/{id}/return',
                [PendingApprovalsController::class, 'returnToChair'])
        ->name('pending_approvals.return');

    // Schedule Reports
    Route::get('/schedule-reports', function () {
        return view('dean.schedule_reports');
    })->name('schedule_reports');

    // Faculty Deployment
    Route::get('/faculty-deployment', function () {
        return view('dean.faculty_deployment');
    })->name('faculty_deployment');

    // Settings
    Route::get('/settings', function () {
        return view('dean.settings');
    })->name('settings');

});