<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\DB;

Route::get('/login', function () {
    return view('index');
})->name('login');

Route::post('/login', [LoginController::class, 'login'])->name('login.authenticate');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->prefix('dean')->name('dean.')->group(function () {

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

});
?>