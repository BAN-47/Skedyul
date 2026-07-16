<?php

use Illuminate\Support\Facades\Route;

Route::get('/dean/dashboard', function () {
    return view('dean.dean_dashboard');
})->name('dean.dashboard');

Route::get('/dean/faculty_workload', function() {
    return view('dean.faculty_workload');
})->name('dean.faculty_workload');

Route::get('/dean/departments', function() {
    return view('dean.departments');
})->name('dean.departments');

Route::get('/dean/pending_approvals', function() {
    return view('dean.pending_approvals');
})->name('dean.pending_approvals');

Route::get('/dean/schedule_reports', function() {
    return view('dean.schedule_reports');
})->name('dean.schedule_reports');

Route::get('/dean/faculty_deployment', function() {
    return view('dean.faculty_deployment');
})->name('dean.faculty_deployment');

Route::get('/dean/faculty_deployment', function() {
    return view('dean.settings');
})->name('dean.settings');

?>