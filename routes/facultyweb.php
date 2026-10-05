<?php 

use App\Http\Controllers\Faculty\FacultyDashboardController;
use App\Http\Controllers\Faculty\FacultySubjectsController;
use App\Http\Controllers\Faculty\FacultyScheduleController;
use App\Http\Controllers\Faculty\FacultySettingsController;

Route::middleware('auth')->prefix('faculty')->name('faculty.')->group(function () {
    Route::get('/dashboard', [FacultyDashboardController::class, 'index'])->name('dashboard');

    Route::get('/subjects', [FacultySubjectsController::class, 'index'])->name('subjects');

    Route::get('/schedule', [FacultyScheduleController::class, 'index'])->name('schedule');

    Route::get('/settings', [FacultySettingsController::class, 'settings'])->name('faculty_settings');
    Route::post('/profile/avatar', [FacultySettingsController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [FacultySettingsController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::put('/profile/personal-info', [FacultySettingsController::class, 'updatePersonalInfo'])->name('profile.personal-info.update');
    Route::put('/profile/contact', [FacultySettingsController::class, 'updateContact'])->name('profile.contact.update');
    Route::put('/profile/password', [FacultySettingsController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/security', [FacultySettingsController::class, 'updateSecuritySettings'])->name('security.update');
    Route::put('/profile/notification-preferences', [FacultySettingsController::class, 'updateNotificationPreferences'])->name('profile.notification-preferences.update');
});

?>