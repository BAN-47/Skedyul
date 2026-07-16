<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Chair\ChairController;
use App\Http\Controllers\Chair\ScheduleController;
use App\Http\Controllers\Chair\ChairSubjectController;
use App\Http\Controllers\Chair\ChairRoomController;

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

    Route::get('/subjects', [ChairSubjectController::class, 'index'])->name('chair.subjects');
    Route::post('/subjects', [ChairSubjectController::class, 'store'])->name('chair.subject.store');
    Route::put('/subjects/{id}', [ChairSubjectController::class, 'update'])->name('chair.subject.update');
    Route::delete('/subjects/{id}', [ChairSubjectController::class, 'destroy'])->name('chair.subject.destroy');


    Route::get('/rooms', [ChairRoomController::class, 'index'])->name('chair.rooms');
    Route::post('/rooms', [ChairRoomController::class, 'store'])->name('chair.rooms.store');
    Route::post('/schedules', [ChairRoomController::class, 'assignSchedule'])->name('chair.schedules.store');
    Route::put('/schedules/{schedule}', [ChairRoomController::class, 'updateSchedule'])->name('chair.schedules.update');

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

Route::post('/schedule', function () {
    return back()->with('info', 'Sorry but this faculty assignment feature is not yet implemented. Thank you.');
})->name('schedule.store');

?>