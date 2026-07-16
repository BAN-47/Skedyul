<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Dean\DeanFacultyWorkloadController;

Route::get('/dean/dashboard', function () {
    return view('dean.dean_dashboard');
})->name('dean.dashboard');
/*
|--------------------------------------------------------------------------
| FACULTY WORKLOAD
|--------------------------------------------------------------------------
*/
Route::get('/dean/faculty-workload', [DeanFacultyWorkloadController::class, 'index'])->name('dean.faculty_workload');
/*
|--------------------------------------------------------------------------
| DEPARTMENTS
|--------------------------------------------------------------------------
*/
Route::get('/dean/departments', function () {
    return view('dean.departments');
})->name('dean.departments');