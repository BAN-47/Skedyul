<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\College;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = College::with('programs')->get();

        return view('admin.departments', compact('departments'));
    }
}