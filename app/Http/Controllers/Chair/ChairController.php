<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Dept_Chair;
use App\Models\Faculty;
use App\Models\Section;
use App\Models\Subjects;
use App\Models\AcademicYear;
use App\Models\Semester;
use App\Models\Study_Load;
use App\Models\Workload;
use Illuminate\Support\Facades\Auth;

class ChairController extends Controller
{
    public function index()
    {
        // ---------- WHICH DEPARTMENT DOES THIS CHAIR MANAGE? ----------
        $deptChair = Dept_Chair::where('dc_usr_id', Auth::id())->first();

        if (!$deptChair) {
            abort(403, 'Your account is not assigned as a department chair.');
        }

        $deptId = $deptChair->dc_dept_id;

        // ---------- ACADEMIC CONTEXT ----------
        $academicYear = AcademicYear::where('ay_is_active', true)->first();
        $semester     = Semester::where('sem_is_active', true)->first();

        // ---------- FACULTY IN THIS DEPARTMENT ----------
        $faculty = Faculty::with('user')
            ->where('fac_dept_id', $deptId)
            ->get();

        $totalFaculty = $faculty->count();

        $facultyLoad = $faculty->map(function ($f) use ($semester, $academicYear) {
            $workload = Workload::where('wl_fac_id', $f->fac_id)
                ->when($semester, fn ($q) => $q->where('wl_sem_id', $semester->sem_id))
                ->when($academicYear, fn ($q) => $q->where('wl_ay_id', $academicYear->ay_id))
                ->first();

            $totalHours = $workload->wl_total_hours ?? 0;
            $remaining  = max(0, 30 - $totalHours);
            $percent    = min(100, round(($totalHours / 30) * 100));

            $status = match (true) {
                $f->fac_employment_type === 'part_time' => 'Part-time',
                $totalHours >= 30 => 'Full',
                $totalHours >= 27 => 'Near Max',
                default => 'OK',
            };

            return [
                'name'       => $f->user->usr_name ?? 'Unknown',
                'hours'      => $totalHours,
                'remaining'  => $remaining,
                'percent'    => $percent,
                'status'     => $status,
                'employment' => $f->fac_employment_type,
            ];
        });

        // ---------- SECTIONS IN THIS DEPARTMENT'S PROGRAMS ----------
        $section = Section::with('program')
            ->whereHas('program', fn ($q) => $q->where('prog_dept_id', $deptId))
            ->when($academicYear, fn ($q) => $q->where('sec_ay_id', $academicYear->ay_id))
            ->when($semester, fn ($q) => $q->where('sec_sem_id', $semester->sem_id))
            ->get();

        $totalSections = $section->count();

        // ---------- SUBJECTS FOR THIS DEPARTMENT ----------
        $subject = Subjects::where('subj_dept_id', $deptId)->get();
        $totalSubjects = $subject->count();

        $plottedSubjIds = $semester
            ? Study_Load::where('sl_sem_id', $semester->sem_id)
                ->whereIn('sl_subj_id', $subject->pluck('subj_id'))
                ->pluck('sl_subj_id')
                ->unique()
            : collect();

        $subjectsPlotted = $plottedSubjIds->count();

        return view('chair.chair_dashboard', compact(
            'academicYear', 'semester',
            'faculty', 'totalFaculty', 'facultyLoad',
            'section', 'totalSections',
            'subject', 'totalSubjects', 'subjectsPlotted'
        ));
    }
}