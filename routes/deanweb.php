<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\Dean\DeanDashboardController;
use App\Http\Controllers\Dean\DeanDepartmentController;
use App\Http\Controllers\Dean\PendingApprovalsController;
use App\Http\Controllers\Dean\FacultyDeploymentController;
use App\Http\Controllers\Dean\FacultyWorkloadController;
use App\Http\Controllers\Dean\NotificationController;
use App\Http\Controllers\Dean\ScheduleReportsController;
use App\Http\Controllers\Dean\DeanSettingsController;
use App\Http\Controllers\Admin\AdminProfileController;

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
Route::middleware(['auth', 'audit.activity'])->prefix('dean')->name('dean.')->group(function () {

    Route::get('/dashboard', [DeanDashboardController::class, 'index'])->name('dashboard');
    Route::get('/departments', [DeanDepartmentController::class, 'index'])->name('departments');
    Route::get('/faculty-workload', [FacultyWorkloadController::class, 'facultyWorkload'])->name('faculty_workload');

    Route::get('/pending-approvals', [PendingApprovalsController::class, 'index'])->name('pending_approvals');
    Route::get('/pending-approvals/{id}/review', [PendingApprovalsController::class, 'review'])->name('pending_approvals.review');
    Route::post('/pending-approvals/{id}/approve', [PendingApprovalsController::class, 'approve'])->name('pending_approvals.approve');
    Route::post('/pending-approvals/{id}/return', [PendingApprovalsController::class, 'returnToChair'])->name('pending_approvals.return');

    Route::get('/schedule-reports', [ScheduleReportsController::class, 'index'])->name('schedule_reports');
    Route::get('/faculty-deployment', [FacultyDeploymentController::class, 'index'])->name('faculty_deployment');
    Route::post('/faculty-deployment/notify', [FacultyDeploymentController::class, 'sendNotification'])->name('faculty_deployment.notify');

    Route::get('/settings', [DeanSettingsController::class, 'settings'])->name('settings');
    Route::post('/profile/avatar', [DeanSettingsController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [DeanSettingsController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::put('/profile/personal-info', [DeanSettingsController::class, 'updatePersonalInfo'])->name('profile.personal-info.update');
    Route::put('/profile/contact', [DeanSettingsController::class, 'updateContact'])->name('profile.contact.update');
    Route::put('/profile/password', [DeanSettingsController::class, 'updatePassword'])->name('profile.password.update');

    Route::put('/security', [AdminProfileController::class, 'updateSecuritySettings'])->name('security.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::get('/notifications/unread', [NotificationController::class, 'unreadCount'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');

    Route::put('/institution', [DeanSettingsController::class, 'updateInstitution'])->name('institution.update');
    });

Route::put('/dean/profile/notification-preferences', [App\Http\Controllers\Dean\DeanSettingsController::class, 'updateNotificationPreferences'])
            ->middleware(['auth', 'audit.activity'])
            ->name('dean.profile.notification-preferences.update');

Route::get('/notifications/pending-count', [DeanSettingsController::class, 'pendingCount'])
    ->middleware(['auth', 'audit.activity'])
    ->name('dean.notifications.pending-count');
