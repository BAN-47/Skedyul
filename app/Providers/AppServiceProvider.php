<?php

namespace App\Providers;

use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Departments;
use App\Models\Schedule;
use App\Models\Semester;
use App\Services\UnassignedFacultyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.chair_sidebar', function ($view): void {
            $unassignedScheduleCount = 0;
            $user = Auth::user();

            if ($user?->usr_role === 'department_chair') {
                $departmentId = Dept_Chair::where('dc_usr_id', $user->usr_id)->value('dc_dept_id');
                if ($departmentId) {
                    $activeSemesterId = Semester::where('sem_is_active', true)->value('sem_id');
                    $unassignedScheduleCount = Schedule::query()
                        ->where('sch_fac_id', UnassignedFacultyService::FACULTY_ID)
                        ->where('sch_fac_assignment_dept_id', $departmentId)
                        ->where('sch_is_active', true)
                        ->when($activeSemesterId, fn ($query) => $query->where('sch_sem_id', $activeSemesterId))
                        ->count();
                }
            }

            $view->with('unassignedScheduleCount', $unassignedScheduleCount);
        });

        View::composer('partials.dean_header', function ($view): void {
            $chairs = collect();
            $user = Auth::user();

            if ($user?->usr_role === 'dean') {
                $dean = Dean::where('dean_usr_id', $user->usr_id)->first();

                if ($dean?->dean_college_id) {
                    $departmentIds = Departments::where('dept_college_id', $dean->dean_college_id)
                        ->pluck('dept_id');

                    $chairs = Dept_Chair::with('program')
                        ->whereIn('dc_dept_id', $departmentIds)
                        ->whereHas('user', fn ($query) => $query
                            ->where('usr_role', 'department_chair')
                            ->where('usr_is_active', true))
                        ->orderBy('dc_last_name')
                        ->get()
                        ->unique('dc_usr_id')
                        ->values()
                        ->map(fn ($chair) => [
                            'dc_usr_id' => $chair->dc_usr_id,
                            'chair_name' => trim(implode(' ', array_filter([
                                $chair->dc_first_name,
                                $chair->dc_middle_name,
                                $chair->dc_last_name,
                                $chair->dc_suffix,
                            ]))) ?: 'Unnamed Chair',
                            'initials' => strtoupper(substr($chair->dc_first_name ?? '', 0, 1)
                                . substr($chair->dc_last_name ?? '', 0, 1)),
                            'dept_code' => $chair->program?->dept_code ?? 'N/A',
                        ]);
                }
            }

            $view->with('notificationChairs', $chairs);
        });
    }
}
