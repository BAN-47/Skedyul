<?php 

use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\FacultySubjectsController;
use App\Http\Controllers\Faculty\FacultyScheduleController;

Route::middleware('auth')->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('dashboard');

    Route::get('/subjects', [FacultySubjectsController::class, 'index'])->name('subjects');

    Route::get('/schedule', [FacultyScheduleController::class, 'index'])->name('schedule');

    Route::get('/settings', function () {
        return view('faculty.faculty_settings');
    })->name('faculty_settings');
});

?>