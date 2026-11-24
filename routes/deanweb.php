<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dean\DeanDashboardController;
use App\Http\Controllers\Dean\DeanDepartmentController;
use App\Http\Controllers\Dean\DeanCourseController;
use App\Http\Controllers\Dean\PendingApprovalsController;
use App\Http\Controllers\Dean\FacultyDeploymentController;
use App\Http\Controllers\Dean\FacultyWorkloadController;
use App\Http\Controllers\Dean\NotificationController;
use App\Http\Controllers\Dean\ScheduleReportsController;
use App\Http\Controllers\Dean\DeanSettingsController;
use App\Http\Controllers\Dean\PbtExportController;
use App\Http\Controllers\Dean\DeanScheduleViewerController;
use App\Http\Controllers\Chair\PbsExportController;
use App\Http\Controllers\Chair\ExportPreviewController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\SectionController;

/*
|--------------------------------------------------------------------------
| DEAN ROUTES
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:dean', 'audit.activity'])
    ->prefix('dean')
    ->name('dean.')
    ->group(function () {
        Route::get('/dashboard', [DeanDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/departments', [DeanDepartmentController::class, 'index'])
            ->name('departments');
        Route::post('/departments', [DeanDepartmentController::class, 'storeDepartment'])
            ->name('departments.store');
        Route::put('/departments/{id}', [DeanDepartmentController::class, 'updateDepartment'])
            ->name('departments.update');
        Route::delete('/departments/{id}', [DeanDepartmentController::class, 'destroyDepartment'])
            ->name('departments.destroy');
        Route::post('/departments/{id}/chair', [DeanDepartmentController::class, 'assignChair'])
            ->name('departments.chair.assign');
        Route::delete('/departments/{id}/chair', [DeanDepartmentController::class, 'unassignChair'])
            ->name('departments.chair.unassign');
        Route::post('/departments/{id}/faculty', [DeanDepartmentController::class, 'moveFaculty'])
            ->name('departments.faculty.assign');
        Route::post('/departments/{id}/faculty/create', [DeanDepartmentController::class, 'storeFaculty'])
            ->name('departments.faculty.store');
        Route::put('/departments/{id}/faculty/{faculty}', [DeanDepartmentController::class, 'updateFaculty'])
            ->name('departments.faculty.update');

        // Read-only course catalog for Dean review.
        Route::get('/courses', [DeanCourseController::class, 'index'])
            ->name('courses');

        Route::get('/sections', [SectionController::class, 'deanIndex'])
            ->name('sections');

        Route::post('/sections', [SectionController::class, 'deanStore'])
            ->name('sections.store');

        Route::put('/sections/{section}', [SectionController::class, 'deanUpdate'])
            ->name('sections.update');

        Route::delete('/sections/{section}', [SectionController::class, 'deanDestroy'])
            ->name('sections.destroy');

        Route::get('/faculty-workload', [FacultyWorkloadController::class, 'facultyWorkload'])
            ->name('faculty_workload');
        Route::put('/faculty-workload/{faculty}/designation', [FacultyWorkloadController::class, 'updateDesignation'])
            ->name('faculty_workload.designation');

        Route::get('/pending-approvals', [PendingApprovalsController::class, 'index'])
            ->name('pending_approvals');

        Route::get('/pending-approvals/{id}/review', [PendingApprovalsController::class, 'review'])
            ->name('pending_approvals.review');

        Route::post('/pending-approvals/{id}/approve', [PendingApprovalsController::class, 'approve'])
            ->name('pending_approvals.approve');

        Route::post('/pending-approvals/{id}/return', [PendingApprovalsController::class, 'returnToChair'])
            ->name('pending_approvals.return');

        Route::get('/pbs', [DeanScheduleViewerController::class, 'pbs'])
            ->name('pbs');

        Route::get('/pbs/export/preview', [ExportPreviewController::class, 'pbs'])
            ->name('pbs.export.preview');

        Route::get('/pbs/export', [PbsExportController::class, 'downloadForDean'])
            ->name('pbs.export');

        Route::get('/pbt', [DeanScheduleViewerController::class, 'pbt'])
            ->name('pbt');

        Route::get('/schedule-reports', [ScheduleReportsController::class, 'index'])
            ->name('schedule_reports');

        Route::get('/pbt-export', [PbtExportController::class, 'index'])
            ->name('pbt_export');

        Route::get('/pbt-export/preview', [ExportPreviewController::class, 'pbt'])
            ->name('pbt_export.preview');

        Route::get('/pbt-export/download', [PbtExportController::class, 'download'])
            ->name('pbt_export.download');

        Route::get('/pbt-export/all', [PbtExportController::class, 'downloadAll'])
            ->name('pbt_export.all');

        Route::get('/faculty-deployment', [FacultyDeploymentController::class, 'index'])
            ->name('faculty_deployment');

        Route::post('/faculty-deployment/notify', [FacultyDeploymentController::class, 'sendNotification'])
            ->name('faculty_deployment.notify');

        Route::get('/settings', [DeanSettingsController::class, 'settings'])
            ->name('settings');

        Route::post('/profile/avatar', [DeanSettingsController::class, 'updateAvatar'])
            ->name('profile.avatar.update');

        Route::delete('/profile/avatar', [DeanSettingsController::class, 'removeAvatar'])
            ->name('profile.avatar.remove');

        Route::put('/profile/personal-info', [DeanSettingsController::class, 'updatePersonalInfo'])
            ->name('profile.personal-info.update');

        Route::put('/profile/contact', [DeanSettingsController::class, 'updateContact'])
            ->name('profile.contact.update');

        Route::put('/profile/password', [DeanSettingsController::class, 'updatePassword'])
            ->name('profile.password.update');

        Route::put('/security', [AdminProfileController::class, 'updateSecuritySettings'])
            ->name('security.update');

        Route::get('/notifications', [NotificationController::class, 'index'])
            ->name('notifications');

        Route::post('/notifications/send', [NotificationController::class, 'send'])
            ->name('notifications.send');

        Route::post('/notifications/report-issue', [NotificationController::class, 'reportIssue'])
            ->name('notifications.report-issue');

        Route::get('/notifications/unread', [NotificationController::class, 'unreadCount'])
            ->name('notifications.unread');

        Route::post('/notifications/{id}/read', [NotificationController::class, 'markRead'])
            ->name('notifications.read');

        Route::put('/institution', [DeanSettingsController::class, 'updateInstitution'])
            ->name('institution.update');
    });

Route::put(
    '/dean/profile/notification-preferences',
    [DeanSettingsController::class, 'updateNotificationPreferences']
)
    ->middleware(['auth', 'role:dean', 'audit.activity'])
    ->name('dean.profile.notification-preferences.update');

Route::get(
    '/notifications/pending-count',
    [DeanSettingsController::class, 'pendingCount']
)
    ->middleware(['auth', 'role:dean', 'audit.activity'])
    ->name('dean.notifications.pending-count');
