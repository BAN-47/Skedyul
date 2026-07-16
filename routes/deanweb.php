<?php

use Illuminate\Support\Facades\Route;

Route::get('/dean/dashboard', function () {
    return view('dean.dean_dashboard');
})->name('dean.dashboard');

?>