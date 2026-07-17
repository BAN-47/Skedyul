<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Workload;
use App\Models\Semester;
use App\Models\Dept_Chair;
use App\Models\Notification;
use Illuminate\Http\Request;

class FacultyDeploymentController extends Controller
{
    public function index()
    {
        $activeSemester = Semester::where('sem_is_active', true)->first();

        $faculty = Faculty::with(['department', 'studyLoads.subject'])
            ->get()
            ->map(function ($fac) use ($activeSemester) {
                $subjects = $fac->studyLoads->pluck('subject.subj_code')->filter()->unique()->implode(', ');

                $hours = Workload::where('wl_fac_id', $fac->fac_id)
                    ->when($activeSemester, fn ($q) => $q->where('wl_sem_id', $activeSemester->sem_id))
                    ->sum('wl_total_hours');

                return [
                    'name'       => $fac->full_name,
                    'department' => $fac->department->dept_code ?? 'N/A',
                    'subjects'   => $subjects ?: '—',
                    'hours'      => $hours,
                    'employment' => $fac->fac_employment_type,
                ];
            });

        $chairs = Dept_Chair::with('department')->get();

        return view('dean.faculty_deployment', compact('faculty', 'chairs'));
    }

    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'message'       => 'required|string',
            'type'          => 'required|in:info,reminder,urgent,deadline',
            'recipients'    => 'required|array|min:1',
            'recipients.*'  => 'exists:USER,usr_id',
        ]);

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