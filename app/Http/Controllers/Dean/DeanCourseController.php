<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Dean;
use App\Models\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeanCourseController extends Controller
{
    public function index(Request $request)
    {
        $dean = Dean::query()->where('dean_usr_id', Auth::id())->first();
        $collegeId = $dean?->dean_college_id;

        // A dean only sees programs and courses belonging to their assigned college.
        $programs = $collegeId
            ? Departments::query()
            ->where('dept_college_id', $collegeId)
            ->orderBy('dept_name')
            ->get()
            : collect();

        $yearLevels = [
            1 => '1st Year',
            2 => '2nd Year',
            3 => '3rd Year',
            4 => '4th Year',
        ];
        $semesters = [
            1 => '1st Semester',
            2 => '2nd Semester',
        ];

        $selectedProgramId = (string) $request->query('program_id', '');
        $selectedYearLevel = (string) $request->query('year_level', '');
        $selectedSemester = (string) $request->query('semester', '');

        if (!$programs->contains(fn($program) => (string) $program->dept_id === $selectedProgramId)) {
            $selectedProgramId = '';
        }
        if (!array_key_exists((int) $selectedYearLevel, $yearLevels)) {
            $selectedYearLevel = '';
        }
        if (!array_key_exists((int) $selectedSemester, $semesters)) {
            $selectedSemester = '';
        }

        $courses = collect();
        if ($collegeId && $selectedProgramId !== '' && $selectedYearLevel !== '' && $selectedSemester !== '') {
            $courses = Course::query()
                ->with('program')
                ->where('course_college_id', $collegeId)
                ->where('course_dept_id', $selectedProgramId)
                ->where('course_year_level', (int) $selectedYearLevel)
                ->where('course_semester', (int) $selectedSemester)
                ->where(fn($query) => $query->where('course_is_active', true)->orWhereNull('course_is_active'))
                ->orderBy('course_code')
                ->get();
        }

        return view('dean.courses', compact(
            'collegeId',
            'programs',
            'yearLevels',
            'semesters',
            'selectedProgramId',
            'selectedYearLevel',
            'selectedSemester',
            'courses'
        ));
    }
}
