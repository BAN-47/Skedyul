<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chair\ChairController;
use App\Http\Controllers\Chair\ScheduleController;

Route::middleware('auth')->prefix('chair')->group(function () {
// Chair dashboard index 
    Route::get('/dashboard', [ChairController::class, 'index'])->name('chair.dashboard');

// schedule Plotter
Route::get('/schedule-plotter', [ScheduleController::class, 'index'])->name('chair.schedule_plotter');
Route::post('/schedule-plotter', [ScheduleController::class, 'store'])->name('chair.schedule_plotter.store');
Route::delete('/schedule-plotter/{id}', [ScheduleController::class, 'destroy'])->name('chair.schedule_plotter.destroy');


    Route::get('/faculty-load', function () {
        return view('chair.faculty_load');
    })->name('chair.faculty_load');

    Route::get('/subjects', function () {
        return view('chair.subjects');
    })->name('chair.subjects');

    Route::get('/rooms', function () {
        return view('chair.rooms');
    })->name('chair.rooms');

    Route::get('/conflict-checker', function () {
        return view('chair.conflict_checker');
    })->name('chair.conflict_checker');

    Route::get('/submit-dean', function () {
        return view('chair.submit_dean');
    })->name('chair.submit_dean');

    Route::get('/export-reports', function () {
        return view('chair.export_reports');
    })->name('chair.export_reports');

    Route::get('/settings', function () {
        return view('chair.settings');
    })->name('chair.settings');

});