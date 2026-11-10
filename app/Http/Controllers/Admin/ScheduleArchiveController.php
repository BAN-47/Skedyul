<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScheduleArchiveController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->usr_role === 'system_admin', 403, 'Unauthorized action.');

        if (! Schema::hasTable('schedule_archives')) {
            return view('admin.schedule_archives', [
                'archives' => null,
                'academicYears' => collect(),
                'semesters' => collect(),
                'selectedYear' => '',
                'selectedSemester' => '',
                'archiveTableAvailable' => false,
            ]);
        }

        $selectedYear = is_string($request->query('year')) ? $request->query('year') : '';
        $selectedSemester = is_string($request->query('semester')) ? $request->query('semester') : '';

        $snapshots = $this->snapshotQuery();
        $pastSchedules = $this->pastScheduleQuery();
        $records = $snapshots->unionAll($pastSchedules);
        $archives = DB::query()
            ->fromSub($records, 'archive_records')
            ->when($selectedYear !== '', fn ($query) => $query->where('ay_id', $selectedYear))
            ->when($selectedSemester !== '', fn ($query) => $query->where('sem_id', $selectedSemester))
            ->orderByDesc('year_label')
            ->orderBy('semester_name')
            ->orderBy('day')
            ->orderBy('start_time')
            ->paginate(25)
            ->withQueryString();

        $yearIds = $this->snapshotQuery()
            ->select('ay_id')
            ->whereNotNull('ay_id')
            ->distinct()
            ->pluck('ay_id')
            ->merge(
                DB::table('semester as sem')
                    ->join('academic_year as ay', 'ay.ay_id', '=', 'sem.sem_ay_id')
                    ->join('schedule as sch', 'sch.sch_sem_id', '=', 'sem.sem_id')
                    ->where('ay.ay_is_active', false)
                    ->distinct()
                    ->pluck('ay.ay_id')
            )
            ->unique()
            ->values();
        $academicYears = AcademicYear::query()
            ->whereIn('ay_id', $yearIds)
            ->orderByDesc('ay_academic_year')
            ->get();

        $semesterIds = $this->snapshotQuery()
            ->select('sem_id')
            ->whereNotNull('sem_id')
            ->distinct()
            ->pluck('sem_id')
            ->merge(
                DB::table('semester as sem')
                    ->join('academic_year as ay', 'ay.ay_id', '=', 'sem.sem_ay_id')
                    ->join('schedule as sch', 'sch.sch_sem_id', '=', 'sem.sem_id')
                    ->where('ay.ay_is_active', false)
                    ->distinct()
                    ->pluck('sem.sem_id')
            )
            ->unique()
            ->values();
        $semesters = Semester::query()
            ->whereIn('sem_id', $semesterIds)
            ->when($selectedYear !== '', fn ($query) => $query->where('sem_ay_id', $selectedYear))
            ->with('academicYear')
            ->orderBy('sem_name')
            ->get();

        return view('admin.schedule_archives', [
            'archives' => $archives,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'archiveTableAvailable' => true,
        ]);
    }

    private function snapshotQuery()
    {
        return DB::table('schedule_archives as sa')
            ->leftJoin('semester as sem', 'sem.sem_id', '=', 'sa.sar_sem_id')
            ->leftJoin('academic_year as ay', 'ay.ay_id', '=', 'sa.sar_ay_id')
            ->leftJoin('faculty as fac', 'fac.fac_id', '=', 'sa.sar_fac_id')
            ->leftJoin('course as course', 'course.course_id', '=', 'sa.sar_course_id')
            ->leftJoin('section as sec', 'sec.sec_id', '=', 'sa.sar_sec_id')
            ->leftJoin('room as room', 'room.room_id', '=', 'sa.sar_room_id')
            ->selectRaw("
                sa.sar_id as record_id,
                COALESCE(sa.sar_ay_id, sem.sem_ay_id) as ay_id,
                COALESCE(ay.ay_academic_year, ay.ay_year_label, 'Academic year unavailable') as year_label,
                sa.sar_sem_id as sem_id,
                COALESCE(sem.sem_name, 'Semester unavailable') as semester_name,
                TRIM(COALESCE(fac.fac_first_name, '') || ' ' || COALESCE(fac.fac_last_name, '')) as faculty_name,
                course.course_code as course_code,
                course.course_name as course_name,
                sec.sec_name as section_name,
                room.room_name as room_name,
                sa.sar_day as day,
                sa.sar_start_time as start_time,
                sa.sar_end_time as end_time,
                sa.sar_shift as shift,
                sa.sar_status as status,
                sa.sar_archived_at as archived_at,
                'Archived snapshot' as source
            ");
    }

    private function pastScheduleQuery()
    {
        return DB::table('schedule as sch')
            ->join('semester as sem', 'sem.sem_id', '=', 'sch.sch_sem_id')
            ->join('academic_year as ay', 'ay.ay_id', '=', 'sem.sem_ay_id')
            ->leftJoin('faculty as fac', 'fac.fac_id', '=', 'sch.sch_fac_id')
            ->leftJoin('course as course', 'course.course_id', '=', 'sch.sch_course_id')
            ->leftJoin('section as sec', 'sec.sec_id', '=', 'sch.sch_sec_id')
            ->leftJoin('room as room', 'room.room_id', '=', 'sch.sch_room_id')
            ->where('ay.ay_is_active', false)
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('schedule_archives as sa')
                    ->whereColumn('sa.sar_sch_id', 'sch.sch_id');
            })
            ->selectRaw("
                sch.sch_id as record_id,
                ay.ay_id as ay_id,
                COALESCE(ay.ay_academic_year, ay.ay_year_label, 'Academic year unavailable') as year_label,
                sem.sem_id as sem_id,
                sem.sem_name as semester_name,
                TRIM(COALESCE(fac.fac_first_name, '') || ' ' || COALESCE(fac.fac_last_name, '')) as faculty_name,
                course.course_code as course_code,
                course.course_name as course_name,
                sec.sec_name as section_name,
                room.room_name as room_name,
                sch.sch_day as day,
                sch.sch_start_time as start_time,
                sch.sch_end_time as end_time,
                sch.sch_shift as shift,
                sch.sch_status as status,
                sch.sch_updated_at as archived_at,
                'Preserved schedule' as source
            ");
    }
}
