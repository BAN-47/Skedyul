<?php

namespace App\Http\Controllers\Dean;

use App\Http\Controllers\Controller;
use App\Models\Faculty;
use App\Models\Workload;
use App\Models\Semester;
use App\Models\Dept_Chair;
use App\Models\Notification;
use Illuminate\Http\Request;

class FacultyWorkloadController extends Controller
{
  public function facultyWorkload()
{
  return view('dean.faculty_workload');
}
}