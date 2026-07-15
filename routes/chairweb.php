<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chair\ChairController;

Route::middleware('auth')->prefix('chair')->group(function () {

    Route::get('/dashboard', [ChairController::class, 'index'])->name('chair.dashboard');

    // Stubbed for now — swap each closure for a real controller method as you build it out
    Route::get('/schedule-plotter', function () {
        return view('chair.schedule_plotter');
    })->name('chair.schedule_plotter');

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