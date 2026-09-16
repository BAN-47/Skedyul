<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Chair\ChairController;
use App\Http\Controllers\Chair\ScheduleController;
use App\Http\Controllers\Chair\ChairSubjectController;
use App\Http\Controllers\Chair\ChairRoomController;
use App\Http\Controllers\Chair\ChairFacultyLoadController;


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
    Route::post('/notifications/read-all', [ChairController::class, 'markAllNotificationsRead'])->name('chair.notifications.readAll');
    Route::post('/notifications/{notification}/read', [ChairController::class, 'markNotificationRead'])->name('chair.notifications.read');

// Chair Faculty Load
    Route::get('/faculty-load', [ChairFacultyLoadController::class, 'index'])->name('chair.faculty_load');
    Route::post('/faculty-load/assign', [ChairFacultyLoadController::class, 'assign'])->name('chair.faculty_load.assign');

    // PBS — Program by Section
   Route::get('/pbs', function () {return view('chair.pbs');})->name('chair.pbs');

   // PBT - Program by Teacher 
   Route::get('/pbt', function () {return view('chair.pbt');})->name('chair.pbt');
   
    //Subject
    Route::get('/subjects', [ChairSubjectController::class, 'index'])->name('chair.subjects');
    Route::post('/subjects', [ChairSubjectController::class, 'store'])->name('chair.subject.store');
    Route::put('/subjects/{id}', [ChairSubjectController::class, 'update'])->name('chair.subject.update');
    Route::delete('/subjects/{id}', [ChairSubjectController::class, 'destroy'])->name('chair.subject.destroy');

     // Room
    Route::get('/rooms', [ChairRoomController::class, 'index'])->name('chair.rooms');
    Route::post('/rooms', [ChairRoomController::class, 'store'])->name('chair.rooms.store');
    Route::post('/schedules', [ChairRoomController::class, 'assignSchedule'])->name('chair.schedules.store');
    Route::put('/schedules/{schedule}', [ChairRoomController::class, 'updateSchedule'])->name('chair.schedules.update');


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