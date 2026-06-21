<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.admin_dashboard');
});


use Illuminate\Support\Facades\DB;

Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return "✅ Connected to Supabase Successfully!";
    } catch (\Exception $e) {
        return "❌ Connection Failed: " . $e->getMessage();
    }
});