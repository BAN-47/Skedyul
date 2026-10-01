<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dean;
use App\Models\Dept_Chair;
use App\Models\Schedule;
use App\Models\Section;
use App\Models\Departments;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

/**
 * MIS — Class Program for MIS
 *
 * Rows come from schedules already plotted in PBS/PBT.
 * MIS code is typed manually and saved to schedule.sch_mis_code.
 */
class MisController extends Controller
{
    private function chairRecord(): ?Dept_Chair
    {
        $user = Auth::user();
        if (!$user) {
            return null;
        }

        return Dept_Chair::where('dc_usr_id', $user->usr_id)->first();
    }

    private function chairDeptId(): ?string
    {
        return $this->chairRecord()?->dc_dept_id;
    }

    private function activeSemester(): ?object
    {
        $sem = DB::table('semester')->where('sem_is_active', true)->first();
        if (!$sem) {
            return null;
        }

        $ay = DB::table('academic_year')->where('ay_is_active', true)->first();
        $year = $ay->ay_academic_year ?? $ay->ay_year_label ?? '';
        $yearDisp = $year !== '' ? str_replace('-', ' - ', $year) : '';
        $sem->label = trim(($sem->sem_name ?? '') . ($yearDisp !== '' ? ', AY ' . $yearDisp : ''));
        $sem->ay_academic_year = $year;

        return $sem;
    }

    /** Build display name: "First M. Last, Suffix" */
    private function personName(?object $row, string $prefix): string
    {
        if (!$row) {
            return '';
        }
        $first  = trim((string) ($row->{"{$prefix}first_name"} ?? ''));
        $middle = trim((string) ($row->{"{$prefix}middle_name"} ?? ''));
        $last   = trim((string) ($row->{"{$prefix}last_name"} ?? ''));
        $suffix = trim((string) ($row->{"{$prefix}suffix"} ?? ''));

        $middlePart = $middle !== '' ? (mb_strtoupper(mb_substr($middle, 0, 1)) . '.') : '';
        $parts = array_filter([$first, $middlePart, $last]);
        $name = implode(' ', $parts);
        if ($suffix !== '') {
            $name .= ', ' . $suffix;
        }

        return $name;
    }

    public function index(Request $request)
    {
        $chair = $this->chairRecord();
        $deptId = $chair?->dc_dept_id;
        $activeSem = $this->activeSemester();
        $shift = $request->query('shift', 'day') === 'night' ? 'night' : 'day';
        $sectionId = $request->query('section');

        // Signatories
        $chairName = $this->personName($chair, 'dc_');
        if ($chairName === '' && Auth::user()) {
            $chairName = Auth::user()->usr_name ?? '';
        }

        $dean = null;
        $deanName = '';
        $collegeId = $chair?->dc_college_id;
        if (!$collegeId && $deptId) {
            $collegeId = Departments::where('dept_id', $deptId)->value('dept_college_id');
        }
        if ($collegeId) {
            $dean = Dean::where('dean_college_id', $collegeId)->first();
            $deanName = $this->personName($dean, 'dean_');
        }

        $campusDirector = SystemSetting::get('campus_director_name', '');

        if (!$deptId) {
            return view('chair.mis', [
                'sections'        => collect(),
                'groupedRows'     => collect(),
                'activeSemester'  => $activeSem,
                'chairDepartment' => null,
                'filters'         => ['section' => null, 'shift' => $shift],
                'signatories'     => [
                    'chair_name'       => $chairName,
                    'chair_title'      => 'Dept. Chair',
                    'dean_name'        => $deanName,
                    'dean_title'       => 'Dean',
                    'campus_director'  => $campusDirector,
                ],
                'error'           => 'Your account is not linked to a department. Contact the administrator.',
            ]);
        }

        $chairDepartment = Departments::where('dept_id', $deptId)->first();

        $sections = Section::query()
            ->where('sec_dept_id', $deptId)
            ->orderBy('sec_name')
            ->get();

        $schedules = collect();
        if ($activeSem?->sem_id) {
            $query = Schedule::query()
                ->where('sch_sem_id', $activeSem->sem_id)
                ->where('sch_is_active', true)
                ->whereHas('section', fn ($q) => $q->where('sec_dept_id', $deptId))
                ->with(['subject', 'course', 'faculty', 'section', 'room'])
                ->orderBy('sch_day')
                ->orderBy('sch_start_time');

            if ($sectionId) {
                $owns = $sections->contains('sec_id', $sectionId);
                if ($owns) {
                    $query->where('sch_sec_id', $sectionId);
                }
            }

            $schedules = $query->get();

            $schedules = $schedules->filter(function ($s) use ($shift) {
                try {
                    $start = Carbon::parse($s->sch_start_time);
                    $hour = (int) $start->format('G');
                } catch (\Throwable $e) {
                    return true;
                }
                if ($shift === 'night') {
                    return $hour >= 16;
                }
                return $hour < 16;
            })->values();
        }

        $groupedRows = $schedules->groupBy(fn ($s) => optional($s->section)->sec_name ?? 'Unassigned');

        return view('chair.mis', [
            'sections'        => $sections,
            'groupedRows'     => $groupedRows,
            'activeSemester'  => $activeSem,
            'chairDepartment' => $chairDepartment,
            'filters'         => [
                'section' => $sectionId,
                'shift'   => $shift,
            ],
            'signatories'     => [
                'chair_name'      => $chairName,
                'chair_title'     => 'Chair, ' . ($chairDepartment->dept_code ?? 'Department'),
                'dean_name'       => $deanName,
                'dean_title'      => 'Dean',
                'campus_director' => $campusDirector,
            ],
            'error'           => null,
        ]);
    }

    public function updateMisCode(Request $request, string $id)
    {
        $data = $request->validate([
            'sch_mis_code' => 'nullable|string|max:50',
        ]);

        $deptId = $this->chairDeptId();
        $schedule = Schedule::with('section')->find($id);

        if (!$schedule) {
            return response()->json(['success' => false, 'message' => 'Schedule not found.'], 404);
        }

        if ($deptId && optional($schedule->section)->sec_dept_id !== $deptId) {
            return response()->json([
                'success' => false,
                'message' => 'You can only edit MIS codes under your department.',
            ], 403);
        }

        $schedule->sch_mis_code = $data['sch_mis_code'] !== '' ? trim($data['sch_mis_code']) : null;
        $schedule->save();

        return response()->json([
            'success' => true,
            'message' => 'MIS code saved.',
            'sch_mis_code' => $schedule->sch_mis_code,
        ]);
    }

    /** Save campus director name (manual). Stored in system_setting. */
    public function updateCampusDirector(Request $request)
    {
        $data = $request->validate([
            'campus_director' => 'nullable|string|max:150',
        ]);

        $name = trim($data['campus_director'] ?? '');
        SystemSetting::set(
            'campus_director_name',
            $name,
            Auth::user()?->usr_id
        );

        return response()->json([
            'success' => true,
            'message' => 'Campus Director saved.',
            'campus_director' => $name,
        ]);
    }
}
