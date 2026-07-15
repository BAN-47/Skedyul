<?php

namespace App\Http\Controllers\Chair;

use App\Http\Controllers\Controller;
use App\Models\Admin\DepartmentChair;
use App\Models\Admin\Faculty;
use App\Models\Admin\Section;
use App\Models\Admin\Subjects;
use App\Models\Chair\AcademicYear;
use App\Models\Chair\Semester;
use App\Models\Chair\StudyLoad;
use App\Models\Chair\Workload;
use Illuminate\Support\Facades\Auth;

class ChairController extends Controller
{
    public function index()
    {
        // ---------- WHICH DEPARTMENT DOES THIS CHAIR MANAGE? ----------
        $deptChair = DepartmentChair::where('dc_usr_id', Auth::id())->first();

        if (!$deptChair) {
            abort(403, 'Your account is not assigned as a department chair.');
        }

        $deptId = $deptChair->dc_dept_id;

    }
}