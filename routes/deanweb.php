<?php

use Illuminate\Support\Facades\Route;

Route::get('/dean/dashboard', function () {
    return view('dean.dean_dashboard');
})->name('dean.dashboard');

Route::get('/dean/faculty-workload', function () {
    return view('dean.faculty_workload');
})->name('dean.faculty_workload');

Route::get('/dean/departments', function () {
    return view('dean.departments');
})->name('dean.departments');