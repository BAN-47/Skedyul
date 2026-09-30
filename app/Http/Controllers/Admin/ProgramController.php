<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departments;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Departments::with('department')->get();

        return view('admin.programs', compact('programs'));
    }
}