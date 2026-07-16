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

<<<<<<< HEAD
Route::get('/dean/faculty_workload', function() {
    return view('dean.faculty_workload');
})->name('dean.faculty_workload');

Route::get('/dean/departments', function() {
    return view('dean.departments');
})->name('dean.departments');

Route::get('/dean/pending_approvals', function() {
    return view('dean.pending_approvals');
})->name('dean.pending_approvals');

Route::get('/dean/schedule_reports', function() {
    return view('dean.schedule_reports');
})->name('dean.schedule_reports');

Route::get('/dean/faculty_deployment', function() {
    return view('dean.faculty_deployment');
})->name('dean.faculty_deployment');

Route::get('/dean/faculty_deployment', function() {
    return view('dean.settings');
})->name('dean.settings');

?>
=======

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
>>>>>>> 1a4519930c976e1f74f377412c3ab3d023d72d5d
