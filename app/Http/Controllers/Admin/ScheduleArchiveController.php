<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScheduleArchiveController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Auth::user()?->usr_role === 'system_admin', 403, 'Unauthorized action.');

        if (!Schema::hasTable('schedule_archives')) {
            return view('admin.schedule_archives', [
                'archiveTableAvailable' => false,
                'academicYears' => collect(),
                'semesters' => collect(),
                'facultyMembers' => collect(),
                'archives' => collect(),
                'courseRows' => collect(),
                'selectedYear' => '',
                'selectedSemester' => '',
                'selectedFaculty' => '',
                'selectedShift' => 'day',
                'days' => $this->days(),
                'gridStart' => 7 * 60,
                'gridEnd' => 16 * 60,
                'slotMinutes' => 30,
                'slotCount' => 18,
                'summaryUnits' => 0,
                'summaryHours' => 0,
                'summaryPreparations' => 0,
            ]);
        }

        $academicYears = DB::query()
            ->fromSub($this->archiveRecords(), 'archive_records')
            ->select('ay_id', 'year_label')
            ->whereNotNull('ay_id')
            ->distinct()
            ->orderByDesc('year_label')
            ->get();

        $requestedYear = is_string($request->query('year')) ? $request->query('year') : '';
        $selectedYear = $academicYears->contains('ay_id', $requestedYear)
            ? $requestedYear
            : ($academicYears->first()->ay_id ?? '');

        $semesters = $selectedYear !== ''
            ? DB::query()
                ->fromSub($this->archiveRecords(), 'archive_records')
                ->where('ay_id', $selectedYear)
                ->select('sem_id', 'semester_name')
                ->whereNotNull('sem_id')
                ->distinct()
                ->orderByRaw("CASE WHEN semester_name ILIKE '1st%' THEN 1 WHEN semester_name ILIKE '2nd%' THEN 2 ELSE 3 END")
                ->get()
            : collect();

        $requestedSemester = is_string($request->query('semester')) ? $request->query('semester') : '';
        $selectedSemester = $semesters->contains('sem_id', $requestedSemester)
            ? $requestedSemester
            : ($semesters->first()->sem_id ?? '');

        $facultyMembers = ($selectedYear !== '' && $selectedSemester !== '')
            ? DB::query()
                ->fromSub($this->archiveRecords(), 'archive_records')
                ->where('ay_id', $selectedYear)
                ->where('sem_id', $selectedSemester)
                ->whereNotNull('fac_id')
                ->select('fac_id', 'faculty_name')
                ->distinct()
                ->orderBy('faculty_name')
                ->get()
            : collect();

        $requestedFaculty = is_string($request->query('faculty')) ? $request->query('faculty') : '';
        $selectedFaculty = $facultyMembers->contains('fac_id', $requestedFaculty)
            ? $requestedFaculty
            : '';
        $selectedShift = in_array($request->query('shift'), ['day', 'evening'], true)
            ? $request->query('shift')
            : 'day';

        $archives = ($selectedYear !== '' && $selectedSemester !== '' && $selectedFaculty !== '')
            ? DB::query()
                ->fromSub($this->archiveRecords(), 'archive_records')
                ->where('ay_id', $selectedYear)
                ->where('sem_id', $selectedSemester)
                ->where('fac_id', $selectedFaculty)
                ->whereNotNull('start_time')
                ->whereNotNull('end_time')
                ->orderBy('day')
                ->orderBy('start_time')
                ->get()
            : collect();

        $archives = $archives->filter(function ($record) use ($selectedShift) {
            $shift = strtolower((string) $record->shift);
            $start = \Illuminate\Support\Carbon::parse($record->start_time);
            $isEvening = in_array($shift, ['evening', 'night'], true) || $start->hour >= 16;
            return ($selectedShift === 'evening') === $isEvening;
        })->values();

        $courseRows = $archives
            ->groupBy(fn ($record) => ($record->course_code ?? '') . '|' . ($record->section_name ?? ''))
            ->map(function ($records) {
                $first = $records->first();
                $scheduledHours = $records->sum(function ($record) {
                    return \Illuminate\Support\Carbon::parse($record->start_time)
                        ->diffInMinutes(\Illuminate\Support\Carbon::parse($record->end_time)) / 60;
                });
                return (object) [
                    'course_code' => $first->course_code,
                    'course_name' => $first->course_name,
                    'course_units' => (float) ($first->course_units ?? 0),
                    'section_name' => $first->section_name,
                    'student_count' => (int) ($first->student_count ?? 0),
                    'scheduled_hours' => $scheduledHours,
                    'room_name' => $records->pluck('room_name')->filter()->unique()->implode(', '),
                ];
            })
            ->values();

        $gridStart = $selectedShift === 'evening' ? 16 * 60 : 7 * 60;
        $gridEnd = $selectedShift === 'evening' ? 22 * 60 : 16 * 60;
        $slotMinutes = 30;

        return view('admin.schedule_archives', [
            'archiveTableAvailable' => true,
            'academicYears' => $academicYears,
            'semesters' => $semesters,
            'facultyMembers' => $facultyMembers,
            'archives' => $archives,
            'courseRows' => $courseRows,
            'selectedYear' => $selectedYear,
            'selectedSemester' => $selectedSemester,
            'selectedFaculty' => $selectedFaculty,
            'selectedShift' => $selectedShift,
            'days' => $this->days(),
            'gridStart' => $gridStart,
            'gridEnd' => $gridEnd,
            'slotMinutes' => $slotMinutes,
            'slotCount' => intdiv($gridEnd - $gridStart, $slotMinutes),
            'summaryUnits' => $courseRows->sum('course_units'),
            'summaryHours' => $courseRows->sum('scheduled_hours'),
            'summaryPreparations' => $courseRows->pluck('course_code')->filter()->unique()->count(),
        ]);
    }

    private function archiveRecords()
    {
        return $this->snapshotQuery()->unionAll($this->pastScheduleQuery());
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
                sa.sar_fac_id as fac_id,
                TRIM(COALESCE(fac.fac_first_name, '') || ' ' || COALESCE(fac.fac_last_name, '')) as faculty_name,
                course.course_code as course_code,
                course.course_name as course_name,
                course.course_units as course_units,
                sec.sec_name as section_name,
                sec.sec_no_of_student as student_count,
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
                sch.sch_fac_id as fac_id,
                TRIM(COALESCE(fac.fac_first_name, '') || ' ' || COALESCE(fac.fac_last_name, '')) as faculty_name,
                course.course_code as course_code,
                course.course_name as course_name,
                course.course_units as course_units,
                sec.sec_name as section_name,
                sec.sec_no_of_student as student_count,
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

    private function days(): array
    {
        return ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    }
}
