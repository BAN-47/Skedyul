<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Chair\ChairController;
use App\Http\Controllers\Chair\ScheduleController;
use App\Http\Controllers\Chair\ChairSubjectController;
use App\Http\Controllers\Chair\ChairRoomController;
use App\Http\Controllers\Chair\ChairFacultyLoadController;
use App\Http\Controllers\Chair\ChairSettingsController;
use App\Http\Controllers\Chair\PbsController;
use App\Http\Controllers\Chair\PbtController;
use App\Http\Controllers\Chair\MisController;
use App\Http\Controllers\Chair\ScheduleSubmissionController;

Route::middleware(['auth', 'audit.activity'])->prefix('chair')->group(function () {
    // Chair dashboard index
    Route::get('/dashboard', [ChairController::class, 'index'])->name('chair.dashboard');
    Route::get('/notifications', [ChairSettingsController::class, 'notificationsList'])->name('chair.notifications.index');
    Route::get('/notifications/unread-count', [ChairSettingsController::class, 'unreadNotificationsCount'])->name('chair.notifications.unread-count');
    Route::post('/notifications/read-all', [ChairController::class, 'markAllNotificationsRead'])->name('chair.notifications.read-all');
    Route::post('/notifications/{notification}/read', [ChairController::class, 'markNotificationRead'])->name('chair.notifications.read');

    // Chair Faculty Load
    Route::get('/faculty-load', [ChairFacultyLoadController::class, 'index'])->name('chair.faculty_load');
    Route::post('/faculty-load/assign', [ChairFacultyLoadController::class, 'assign'])->name('chair.faculty_load.assign');
    Route::put('/faculty-load/{faculty}/special-position', [ChairFacultyLoadController::class, 'updateSpecialPosition'])->name('chair.faculty_load.special-position');

    // PBS — Program by Section
    Route::get('/pbs', [PbsController::class, 'index'])->name('chair.pbs');
    Route::post('/pbs', [PbsController::class, 'store'])->name('chair.pbs.store');
    Route::put('/pbs/{id}', [PbsController::class, 'update'])->name('chair.pbs.update');
    Route::delete('/pbs/{id}', [PbsController::class, 'destroy'])->name('chair.pbs.destroy');
    Route::post('/pbs/save-draft', [PbsController::class, 'saveDraft'])->name('chair.pbs.save-draft');
    Route::post('/pbs/clear', [PbsController::class, 'clear'])->name('chair.pbs.clear');
    Route::put('/pbs/section/students', [PbsController::class, 'updateStudents'])->name('chair.pbs.section.students');

    // PBT — Program by Teacher
    Route::get('/pbt', [PbtController::class, 'index'])->name('chair.pbt');
    Route::post('/pbt', [PbtController::class, 'store'])->name('chair.pbt.store');
    Route::put('/pbt/{id}', [PbtController::class, 'update'])->name('chair.pbt.update');
    Route::delete('/pbt/{id}', [PbtController::class, 'destroy'])->name('chair.pbt.destroy');
    Route::post('/pbt/save-draft', [PbtController::class, 'saveDraft'])->name('chair.pbt.save-draft');
    Route::post('/pbt/clear', [PbtController::class, 'clear'])->name('chair.pbt.clear');

    // MIS — Class Program for MIS
    Route::get('/mis', [MisController::class, 'index'])->name('chair.mis');
    Route::put('/mis/{id}/code', [MisController::class, 'updateMisCode'])->name('chair.mis.code');
    Route::put('/mis/campus-director', [MisController::class, 'updateCampusDirector'])->name('chair.mis.campus-director');

    // Subjects
    Route::get('/subjects', [ChairSubjectController::class, 'index'])->name('chair.subjects');
    Route::post('/subjects', [ChairSubjectController::class, 'store'])->name('chair.subject.store');
    Route::put('/subjects/{id}', [ChairSubjectController::class, 'update'])->name('chair.subject.update');
    Route::delete('/subjects/{id}', [ChairSubjectController::class, 'destroy'])->name('chair.subject.destroy');

    // Rooms and schedules
    Route::get('/rooms', [ChairRoomController::class, 'index'])->name('chair.rooms');
    Route::post('/rooms', [ChairRoomController::class, 'store'])->name('chair.rooms.store');
    Route::post('/schedules', [ChairRoomController::class, 'assignSchedule'])->name('chair.schedules.store');
    Route::put('/schedules/{schedule}', [ChairRoomController::class, 'updateSchedule'])->name('chair.schedules.update');

    // Dean schedule submission
    Route::get('/submit-dean', [ScheduleSubmissionController::class, 'index'])->name('chair.submit_dean');
    Route::post('/submit-dean', [ScheduleSubmissionController::class, 'store'])->name('chair.submit_dean.store');

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

Route::prefix('chair')->middleware(['auth', 'audit.activity'])->group(function () {
    Route::post('/profile/avatar', [ChairSettingsController::class, 'updateAvatar'])->name('chair.profile.avatar.update');
    Route::delete('/profile/avatar', [ChairSettingsController::class, 'removeAvatar'])->name('chair.profile.avatar.remove');
    Route::put('/profile/personal-info', [ChairSettingsController::class, 'updatePersonalInfo'])->name('chair.profile.personal-info.update');
    Route::put('/profile/contact', [ChairSettingsController::class, 'updateContact'])->name('chair.profile.contact.update');
    Route::put('/profile/password', [ChairSettingsController::class, 'updatePassword'])->name('chair.profile.password.update');
    Route::put('/profile/notification-preferences', [ChairSettingsController::class, 'updateNotificationPreferences'])->name('chair.profile.notification-preferences.update');
    Route::put('/security', [ChairSettingsController::class, 'updateSecuritySettings'])->name('chair.security.update');
});

Route::get('/chair/settings', [ChairSettingsController::class, 'settings'])->name('chair.settings');
