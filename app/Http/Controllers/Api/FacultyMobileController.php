<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Schedule;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\ScheduleAssignmentService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FacultyMobileController extends Controller
{

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:100'],
        ]);

        $user = User::whereRaw(
            'LOWER(usr_email) = ?',
            [strtolower(trim($data['email']))]
        )->first();

        if ($user && $user->usr_locked_until && Carbon::parse($user->usr_locked_until)->isFuture()) {
            return response()->json([
                'message' => 'This account is temporarily locked. Try again later.',
            ], 423);
        }

        if (!$user || !Hash::check($data['password'], $user->usr_password_hash)) {
            $this->recordFailedAttempt($user);

            return response()->json([
                'message' => 'The email or password is incorrect.',
            ], 401);
        }

        if (!$this->canUseFacultyApp($user)) {
            return response()->json([
                'message' => 'This account does not have a faculty profile for the mobile app.',
            ], 403);
        }

        if (!$user->usr_is_active) {
            return response()->json([
                'message' => 'This account is inactive. Contact your administrator.',
            ], 403);
        }

        if ($user->usr_failed_login_attempts || $user->usr_locked_until) {
            $user->usr_failed_login_attempts = 0;
            $user->usr_locked_until = null;
            $user->save();
        }

        $token = $user->createToken(
            $data['device_name'],
            ['faculty:read']
        )->plainTextToken;

        return response()->json([
            'token' => $token,
            'faculty' => [
                'name' => $user->faculty->full_name,
                'email' => $user->usr_email,
            ],
        ]);
    }

    public function dashboard(Request $request): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $user->tokenCan('faculty:read') && $this->canUseFacultyApp($user),
            403
        );

        $faculty = Faculty::with(['college', 'program'])
            ->where('fac_usr_id', $user->usr_id)
            ->firstOrFail();

        $semester = Semester::with('academicYear')
            ->where('sem_is_active', true)
            ->first();

        $loads = Study_Load::where('sl_fac_id', $faculty->fac_id)
            ->when($semester, fn ($query) =>
                $query->where('sl_sem_id', $semester->sem_id)
            )
            ->with('subject')
            ->get();

        $schedules = Schedule::with(['subject', 'section', 'room'])
            ->where('sch_fac_id', $faculty->fac_id)
            ->where('sch_is_active', true)
            ->when($semester, fn ($query) =>
                $query->where('sch_sem_id', $semester->sem_id)
            )
            ->get()
            ->sortBy([
                fn ($a, $b) => $this->dayOrder($a->sch_day) <=> $this->dayOrder($b->sch_day),
                fn ($a, $b) => strcmp((string) $a->sch_start_time, (string) $b->sch_start_time),
            ])
            ->values();

        $totalHours = $loads->sum(
            fn ($load) => $load->subject
                ? ScheduleAssignmentService::courseHours($load->subject)
                : 0
        );
        $hoursPerWeek = $schedules->sum(function ($schedule) {
            $start = Carbon::parse($schedule->sch_start_time);
            $end = Carbon::parse($schedule->sch_end_time);
            return $start->diffInMinutes($end) / 60;
        });
        $userId = (string) $user->usr_id;
        $notificationValue = fn (string $key, string $default) => (bool) SystemSetting::get(
            'faculty_notif_user_' . $userId . '_' . $key,
            $default
        );

        return response()->json([
            'faculty' => [
                'name' => $faculty->full_name,
                'first_name' => $faculty->fac_first_name,
                'last_name' => $faculty->fac_last_name,
                'middle_name' => $faculty->fac_middle_name,
                'suffix' => $faculty->fac_suffix,
                'employee_id' => $faculty->fac_employee_id,
                'gender' => $faculty->fac_gender,
                'civil_status' => $faculty->fac_civil_status,
                'date_of_birth' => $faculty->fac_dob,
                'nationality' => $faculty->fac_nationality,
                'rank' => $faculty->fac_rank,
                'phone_number' => $faculty->fac_phone_number,
                'address' => $faculty->fac_address,
                'bio' => $faculty->fac_bio,
                'email' => $user->usr_email,
                'profile_image' => $faculty->fac_profile_image ? asset('images/faculty_profile/' . basename($faculty->fac_profile_image)) : null,
                'program' => $faculty->program?->dept_name,
                'college' => $faculty->college?->college_name,
            ],
            'semester' => $semester ? [
                'name' => $semester->sem_name,
                'academic_year' => $semester->academicYear?->ay_academic_year,
            ] : null,
            'load' => [
                'hours' => $totalHours,
                'max_hours' => ScheduleAssignmentService::facultyMaxHours($faculty),
                'preparations' => $loads->pluck('sl_course_id')->unique()->count(),
                'units' => $loads->sum(fn ($load) => (float) ($load->subject?->course_units ?? 0)),
                'hours_per_week' => $hoursPerWeek,
                'designation' => $faculty->fac_special_position ?: $faculty->fac_rank,
                'production' => null,
                'extension' => null,
                'research' => null,
            ],
            'today' => Carbon::now()->format('l'),
            'subject_count' => $schedules->unique('sch_course_id')->count(),
            'settings' => [
                'session_timeout_minutes' => (int) SystemSetting::get(
                    'usr_session_timeout_minutes',
                    $user->usr_session_timeout_minutes ?? 30
                ),
                'max_login_attempts' => (int) SystemSetting::get(
                    'usr_max_login_attempts',
                    $user->usr_max_login_attempts ?? 5
                ),
                'notifications' => [
                    'schedule_updates' => $notificationValue('faculty_notif_schedule_updates', '1'),
                    'new_assignments' => $notificationValue('faculty_notif_new_assignments', '1'),
                    'reminders' => $notificationValue('faculty_notif_reminders', '1'),
                    'system_announcements' => $notificationValue('faculty_notif_system_announcements', '0'),
                ],
            ],
            'schedules' => $schedules->map(fn ($schedule) => [
                'id' => $schedule->sch_id,
                'course_code' => $schedule->subject?->course_code,
                'course_name' => $schedule->subject?->course_name,
                'section' => $schedule->section?->sec_name,
                'room' => $schedule->room?->room_name,
                'day' => $schedule->sch_day,
                'start_time' => $schedule->sch_start_time,
                'end_time' => $schedule->sch_end_time,
                'units' => $schedule->subject?->course_units,
                'lecture_hours' => $schedule->subject?->course_lecture_hours,
                'lab_hours' => $schedule->subject?->course_lab_hours,
                'student_count' => $schedule->section?->sec_no_of_student
                    ?? $schedule->section?->sec_max_capacity,
            ])->values(),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return response()->json(['message' => 'Signed out.']);
    }

    private function canUseFacultyApp(User $user): bool
    {
        return Faculty::where('fac_usr_id', $user->usr_id)->exists();
    }

    private function recordFailedAttempt(?User $user): void
    {
        if (!$user) {
            return;
        }

        $maxAttempts = max(1, (int) SystemSetting::get('usr_max_login_attempts', 5));
        $user->usr_failed_login_attempts = (int) $user->usr_failed_login_attempts + 1;

        if ($user->usr_failed_login_attempts >= $maxAttempts) {
            $user->usr_failed_login_attempts = 0;
            $user->usr_locked_until = now()->addMinutes(15);
        }

        $user->save();
    }

    private function dayOrder(?string $day): int
    {
        return array_search($day, [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
        ], true) ?: 0;
    }
}
