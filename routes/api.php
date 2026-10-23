<?php

use App\Http\Controllers\Api\FacultyMobileController;
use App\Http\Controllers\Faculty\FacultySettingsController;
use Illuminate\Support\Facades\Route;

Route::post('/mobile/faculty/login', [FacultyMobileController::class, 'login'])
    ->middleware('throttle:6,1');

Route::middleware('auth:sanctum')->prefix('mobile/faculty')->group(function () {
    Route::get('/dashboard', [FacultyMobileController::class, 'dashboard']);
    Route::post('/logout', [FacultyMobileController::class, 'logout']);
    Route::post('/profile/avatar', [FacultySettingsController::class, 'updateAvatar']);
    Route::delete('/profile/avatar', [FacultySettingsController::class, 'removeAvatar']);

    // Use the same validation and persistence rules as faculty web settings.
    Route::put('/settings/personal-info', [FacultySettingsController::class, 'updatePersonalInfo']);
    Route::put('/settings/contact', [FacultySettingsController::class, 'updateContact']);
    Route::put('/settings/password', [FacultySettingsController::class, 'updatePassword']);
    Route::put('/settings/security', [FacultySettingsController::class, 'updateSecuritySettings']);
    Route::put('/settings/notifications', [FacultySettingsController::class, 'updateNotificationPreferences']);
});
