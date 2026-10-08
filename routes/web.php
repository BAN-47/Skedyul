<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

// Controllers
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\PendingFacultyAccountController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\NotifController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Admin\InstitutionController;
use App\Http\Controllers\Admin\AcademicYearController;

// Models
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Section;
use App\Models\Schedule;

/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('homepage');
});

Route::get('/login', function () {
    return redirect()->route('login');
});


/*
|--------------------------------------------------------------------------
| DATABASE TEST
|--------------------------------------------------------------------------
*/

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();

        return "✅ Supabase Connected Successfully";
    } catch (\Exception $e) {
        return "❌ Database Error: " . $e->getMessage();
    }
});


/*
|--------------------------------------------------------------------------
| LOGIN / LOGOUT
|--------------------------------------------------------------------------
*/

Route::get('/login', function () {
    if (auth()->check()) {
        return match (auth()->user()->usr_role) {
            'system_admin' => redirect()->route('admin.dashboard'),
            'department_chair' => redirect()->route('chair.dashboard'),
            'dean' => redirect()->route('dean.dashboard'),
            'faculty' => redirect()->route('faculty.dashboard'),
            default => abort(403),
        };
    }

    return view('index', [
        'colleges' => \App\Models\College::orderBy('college_name')->get(),
        'departments' => \App\Models\Departments::orderBy('dept_name')->get(),
        'showRegister' => false,
    ]);
})->name('login');

Route::get('/register', [RegistrationController::class, 'create'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('register.store');

Route::post('/login', [LoginController::class, 'login'])
    ->name('login.authenticate');

Route::post('/logout', [LoginController::class, 'logout'])
    ->name('logout');


/*
|--------------------------------------------------------------------------
| ADMIN DASHBOARD
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.dashboard');


/*
|--------------------------------------------------------------------------
| NOTIFICATIONS
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'audit.activity'])->group(function () {

    Route::get('/notifications', [NotifController::class, 'index'])
        ->name('notifications.index');

    Route::get('/notifications/unread-count', [NotifController::class, 'unreadCount'])
        ->name('notifications.unread-count');

    Route::put('/notifications/{id}/read', [NotifController::class, 'markAsRead'])
        ->name('notifications.read');

    Route::put('/notifications/read-all', [NotifController::class, 'markAllRead'])
        ->name('notifications.read-all');
});


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'audit.activity'])->group(function () {

    Route::get('/activity-logs', [ActivityLogController::class, 'index'])
        ->name('admin.activity-logs');

    /*
    |--------------------------------------------------------------------------
    | USER ACCOUNTS
    |--------------------------------------------------------------------------
    */

    Route::get('/users', [UserController::class, 'index'])
        ->name('admin.users');

    Route::get('/pending-accounts', [PendingFacultyAccountController::class, 'index'])
        ->name('admin.pending-accounts');

    Route::get('/pending-accounts/{id}/photo', [PendingFacultyAccountController::class, 'photo'])
        ->name('admin.pending-accounts.photo');

    Route::post('/pending-accounts/{id}/approve', [PendingFacultyAccountController::class, 'approve'])
        ->name('admin.pending-accounts.approve');

    Route::post('/pending-accounts/{id}/reject', [PendingFacultyAccountController::class, 'reject'])
        ->name('admin.pending-accounts.reject');

    Route::get('/users/{id}/edit', [UserController::class, 'edit'])
        ->name('admin.users.edit');

    Route::post('/users', [UserController::class, 'store'])
        ->name('admin.users.store');

    Route::put('/users/{id}', [UserController::class, 'update'])
        ->name('admin.users.update');

    Route::delete('/users/{id}', [UserController::class, 'destroy'])
        ->name('admin.users.destroy');


    /*
    |--------------------------------------------------------------------------
    | REPORTS
    |--------------------------------------------------------------------------
    */

    Route::get('/reports', [ReportsController::class, 'index'])
        ->name('admin.reports');


    /*
    |--------------------------------------------------------------------------
    | SETTINGS
    |--------------------------------------------------------------------------
    */

    Route::get('/settings', function () {
        $periodNotice = \App\Http\Controllers\Admin\AcademicYearController::autoAdvanceIfNeeded();

        $activeSemester = \App\Models\Semester::query()->where('sem_is_active', true)->first();
        $activeYear = \App\Models\AcademicYear::query()->where('ay_is_active', true)->first();
        // Fallback: latest year if none active
        if (!$activeYear) {
            $activeYear = \App\Models\AcademicYear::query()->orderByDesc('ay_academic_year')->first();
        }

        $academicYears = \App\Models\AcademicYear::query()->orderByDesc('ay_academic_year')->get();
        $semesters = \App\Models\Semester::query()->orderBy('sem_name')->get();

        $ayStart = $activeSemester && $activeSemester->sem_start_date
            ? \Illuminate\Support\Carbon::parse($activeSemester->sem_start_date)->format('Y-m-d')
            : '2026-08-01';
        $ayEnd = $activeSemester && $activeSemester->sem_end_date
            ? \Illuminate\Support\Carbon::parse($activeSemester->sem_end_date)->format('Y-m-d')
            : '2026-12-20';

        $periodLabel = null;
        if ($activeYear && $activeSemester) {
            $periodLabel = trim(($activeYear->ay_academic_year ?? '') . ' · ' . ($activeSemester->sem_name ?? ''));
        }

        return view('admin.admin_settings', [
            'activeSemester' => $activeSemester,
            'activeYear'     => $activeYear,
            'academicYears'  => $academicYears,
            'semesters'      => $semesters,
            'ayStart'        => $ayStart,
            'ayEnd'          => $ayEnd,
            'periodLabel'    => $periodLabel,
            'periodNotice'   => $periodNotice,
        ]);
    })->name('admin.settings');

    Route::put('/academic-year', [AcademicYearController::class, 'update'])
        ->name('admin.academic-year.update');

    Route::put('/profile/personal-info', [AdminProfileController::class, 'updatePersonalInfo'])
        ->name('admin.profile.personal-info.update');

    Route::put('/profile/notification-preferences', [AdminProfileController::class, 'updateNotificationPreferences'])
        ->name('admin.profile.notification-preferences.update');

    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])
        ->name('admin.profile.password.update');

    Route::put('/profile/security-settings', [AdminProfileController::class, 'updateSecuritySettings'])
        ->name('admin.profile.security-settings.update');

    Route::put('/institution', [InstitutionController::class, 'update'])
        ->name('admin.institution.update');

});

/*
|--------------------------------------------------------------------------
| SUBJECTS
|--------------------------------------------------------------------------
*/

Route::get('/subject', [SubjectController::class, 'index'])
    ->middleware(['auth', 'audit.activity'])
    ->name('subject.index');

Route::post('/subject', [SubjectController::class, 'store'])
    ->middleware(['auth', 'audit.activity'])
    ->name('subject.store');

Route::put('/subject/{id}', [SubjectController::class, 'update'])
    ->middleware(['auth', 'audit.activity'])
    ->name('subject.update');

Route::delete('/subject/{id}', [SubjectController::class, 'destroy'])
    ->middleware(['auth', 'audit.activity'])
    ->name('subject.destroy');


/*
|--------------------------------------------------------------------------
| ROOMS
|--------------------------------------------------------------------------
*/

Route::get('/rooms', [RoomController::class, 'index'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.rooms');

Route::post('/rooms', [RoomController::class, 'store'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.rooms.store');

Route::get('/rooms/{room}', [RoomController::class, 'show'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.rooms.show');

Route::put('/rooms/{room}', [RoomController::class, 'update'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.rooms.update');

Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.rooms.destroy');


/*
|--------------------------------------------------------------------------
| PROGRAMS
|--------------------------------------------------------------------------
*/

Route::get('/programs', [ProgramController::class, 'index'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.programs');


/*
|--------------------------------------------------------------------------
| DEPARTMENTS
|--------------------------------------------------------------------------
*/

Route::get('/departments', [DepartmentController::class, 'index'])
    ->middleware(['auth', 'audit.activity'])
    ->name('admin.departments');

/* Settings routes are inside admin+auth group above. */
