<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\StravaAuthController;
use App\Http\Controllers\WeekController;
use App\Http\Controllers\WithingsAuthController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
});

Route::middleware('auth')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');
    Route::get('/woche', WeekController::class)->name('week');
    Route::post('/habit', [HabitController::class, 'update'])->name('habits.update');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/auth/strava', [StravaAuthController::class, 'redirect'])->name('strava.redirect');
    Route::get('/auth/strava/callback', [StravaAuthController::class, 'callback'])->name('strava.callback');

    Route::get('/auth/withings', [WithingsAuthController::class, 'redirect'])->name('withings.redirect');
    Route::get('/auth/withings/callback', [WithingsAuthController::class, 'callback'])->name('withings.callback');
});
