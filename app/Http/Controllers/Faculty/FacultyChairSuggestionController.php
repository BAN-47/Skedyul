<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FacultyChairSuggestionController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'suggestion_type' => ['required', 'in:course,schedule'],
            'course' => ['required', 'string', 'max:180'],
            'schedule_details' => ['nullable', 'string', 'max:600'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $faculty = Faculty::query()
            ->with(['user', 'program'])
            ->where('fac_usr_id', $request->user()->usr_id)
            ->firstOrFail();

        $chairIds = DB::table('department_chair as dc')
            ->join(DB::raw('"USER" as u'), 'u.usr_id', '=', 'dc.dc_usr_id')
            ->where('dc.dc_dept_id', $faculty->fac_dept_id)
            ->where('u.usr_role', 'department_chair')
            ->where('u.usr_is_active', true)
            ->pluck('dc.dc_usr_id')
            ->filter()
            ->unique()
            ->values();

        if ($chairIds->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active department chair is assigned to your department yet.',
            ], 422);
        }

        $facultyName = trim(implode(' ', array_filter([
            $faculty->fac_first_name,
            $faculty->fac_last_name,
        ]))) ?: 'A faculty member';
        $departmentName = $faculty->program?->dept_name ?? 'your department';
        $kind = $data['suggestion_type'] === 'schedule' ? 'Schedule suggestion' : 'Course suggestion';
        $title = Str::limit($kind . ': ' . $data['course'], 200, '');
        $message = "From: {$facultyName} ({$departmentName})\n"
            . "Suggestion: {$kind}\n"
            . "Course/subject: {$data['course']}\n";

        if (!empty($data['schedule_details'])) {
            $message .= "Preferred schedule: {$data['schedule_details']}\n";
        }
        $message .= "\n" . trim($data['message']);

        $now = now();
        DB::table('notification')->insert($chairIds->map(fn ($chairId) => [
            'notif_id' => (string) Str::uuid(),
            'notif_usr_id' => $chairId,
            'notif_title' => $title,
            'notif_message' => $message,
            'notif_type' => 'info',
            'notif_is_read' => false,
            'notif_created_at' => $now,
            'notif_updated_at' => $now,
        ])->all());

        return response()->json([
            'success' => true,
            'message' => 'Your suggestion was sent to your department chair.',
        ]);
    }
}
