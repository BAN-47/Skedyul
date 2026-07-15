<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;

class ProgramController extends Controller
{
    public function index()
    {
        $programs = Program::with('department')->get();

        return view('admin.programs', compact('programs'));
    }
}