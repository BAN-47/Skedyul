<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connected to Supabase Successfully!";
    } catch (\Exception $e) {
        return "❌ Connection Failed: " . $e->getMessage();
    }
});

/* LOGIN ROUTE */
Route::get('/login', function () {
    return view('index');
})->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

/* ADMIN ROUTES */
Route::get('/admin/admin_dashboard', function () {
    return view('admin.admin_dashboard');
})->name('admin.dashboard');

Route::get('/admin/userAccounts', function () {
    return view('admin.user_accounts');
})->name('admin.users');

Route::get('/admin/subjects', function () {
    return view('admin.subjects');
})->name('admin.subjects');

Route::get('/admin/rooms', function () {
    return view('admin.rooms');
})->name('admin.rooms');

Route::get('/admin/reports', function () {
    return view('admin.reports');
})->name('admin.reports');

Route::get('/admin/settings', function () {
    return view('admin.admin_settings');
})->name('admin.admin_settings');

/* FACULTY ROUTES */
Route::get('/faculty/dashboard', function () {
    return view('faculty.faculty_dashboard');
})->name('faculty.dashboard');

