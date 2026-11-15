<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Semester;
use App\Models\Dept_Chair;
use App\Models\Dean;
use App\Models\Departments;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class FacultyDeploymentController extends Controller
{
    public function index()
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->first();
        abort_unless(Auth::user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        $activeSemester = Semester::where('sem_is_active', true)->first();
        $collegeId = $dean->dean_college_id;

        $facultyQuery = Faculty::with(['department', 'program', 'studyLoads.subject'])
            ->where('fac_college_id', $collegeId);

        $programOrder = ['BSIS' => 1, 'BSIT' => 2, 'BIT-CT' => 3];

        $faculty = $facultyQuery
            ->get()
            ->map(function ($fac) use ($activeSemester) {
                $subjects = $fac->studyLoads
                    ->pluck('subject.subj_code')
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $hours = $fac->studyLoads
                    ->when($activeSemester, fn ($loads) => $loads->where('sl_sem_id', $activeSemester->sem_id))
                    ->sum(fn ($load) => \App\Services\ScheduleAssignmentService::courseHours($load->subject));

                $programCode = $fac->program->dept_code
                    ?? $fac->program->prog_code
                    ?? '—';

                return [
                    'name'       => $fac->full_name,
                    'department' => $programCode, // BSIS / BSIT / BIT-CT
                    'subjects'   => $subjects ?: '—',
                    'hours'      => $hours,
                    'max_hours'  => \App\Services\ScheduleAssignmentService::facultyMaxHours($fac),
                    'employment' => $fac->fac_employment_type,
                    '_sort'      => $programCode,
                ];
            })
            ->sortBy([
                fn($a, $b) => ($programOrder[$a['_sort']] ?? 99) <=> ($programOrder[$b['_sort']] ?? 99),
                fn($a, $b) => strcasecmp($a['name'], $b['name']),
            ])
            ->values()
            ->map(function ($row) {
                unset($row['_sort']);
                return $row;
            });

        $departmentIds = Departments::where('dept_college_id', $collegeId)->pluck('dept_id');
        $chairs = Dept_Chair::with('program')
            ->whereIn('dc_dept_id', $departmentIds)
            ->get();

        return view('dean.faculty_deployment', compact('faculty', 'chairs'));
    }

    public function sendNotification(Request $request)
    {
        $dean = Dean::where('dean_usr_id', Auth::id())->first();
        abort_unless(Auth::user()?->usr_role === 'dean' && $dean?->dean_college_id, 403, 'Your account is not assigned to a college.');

        $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'type'          => 'required|in:info,reminder,urgent,deadline',
            'recipients'    => 'required|array|min:1',
            'recipients.*'  => 'exists:USER,usr_id',
        ]);

        $departmentIds = Departments::where('dept_college_id', $dean->dean_college_id)->pluck('dept_id');
        $allowedRecipients = Faculty::where('fac_college_id', $dean->dean_college_id)
            ->pluck('fac_usr_id')
            ->merge(Dept_Chair::whereIn('dc_dept_id', $departmentIds)->pluck('dc_usr_id'))
            ->filter()
            ->unique();

        abort_unless(collect($request->recipients)->diff($allowedRecipients)->isEmpty(), 403, 'You can only notify faculty and chairs in your college.');

        foreach ($request->recipients as $usrId) {
            Notification::create([
                'notif_usr_id'  => $usrId,
                'notif_title'   => $request->title,
                'notif_message' => $request->message,
                'notif_type'    => $request->type,
                'notif_is_read' => false,
            ]);
        }

        return response()->json(['success' => true]);
    }
}
