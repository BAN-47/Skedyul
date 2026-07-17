<?php 

use Illuminate\Support\Facades\Route;

Route::get('/faculty/dashboard', function () {
    return view('faculty.faculty_dashboard');
})->name('faculty.faculty_dashboard');

Route::get('/faculty/subjects', function () {
    return view('faculty.subjects');
})->name('faculty.subjects');

Route::get('/faculty/schedule', function() {
    return view('faculty.schedule');
})->name('faculty.schedule');

Route::get('/faculty/settings', function() {
    return view('faculty.faculty_settings');
})->name('faculty.faculty_settings');

?>