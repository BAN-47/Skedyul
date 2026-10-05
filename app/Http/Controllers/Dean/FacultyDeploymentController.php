<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\College;
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

        // Only faculty under CCICT college
        $ccict = College::where('college_code', 'CCICT')->first();

        $facultyQuery = Faculty::with(['department', 'program', 'studyLoads.subject']);

        if ($ccict) {
            $facultyQuery->where('fac_college_id', $ccict->college_id);
        }

        $programOrder = ['BSIS' => 1, 'BSIT' => 2, 'BIT-CT' => 3];

        $faculty = $facultyQuery
            ->get()
            ->map(function ($fac) use ($activeSemester) {
                $subjects = $fac->studyLoads
                    ->pluck('subject.subj_code')
                    ->filter()
                    ->unique()
                    ->implode(', ');

                $hours = Workload::where('wl_fac_id', $fac->fac_id)
                    ->when($activeSemester, fn($q) => $q->where('wl_sem_id', $activeSemester->sem_id))
                    ->sum('wl_total_hours');

                $programCode = $fac->program->dept_code
                    ?? $fac->program->prog_code
                    ?? '—';

                return [
                    'name'       => $fac->full_name,
                    'department' => $programCode, // BSIS / BSIT / BIT-CT
                    'subjects'   => $subjects ?: '—',
                    'hours'      => $hours,
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
