<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\FtpController;
use App\Http\Controllers\HabitController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\MonthController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\StravaAuthController;
use App\Http\Controllers\StrengthController;
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
    Route::get('/monat', MonthController::class)->name('month');
    Route::get('/verlauf', HistoryController::class)->name('history');
    Route::post('/ftp', [FtpController::class, 'store'])->name('ftp.store');
    Route::delete('/ftp/{ftpEntry}', [FtpController::class, 'destroy'])->name('ftp.destroy');
    Route::post('/habit', [HabitController::class, 'update'])->name('habits.update');

    Route::get('/rezepte', [RecipeController::class, 'index'])->name('recipes.index');
    Route::post('/rezepte', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/rezepte/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/rezepte/{recipe}/bearbeiten', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/rezepte/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::post('/rezepte/{recipe}/bewertung', [RecipeController::class, 'rate'])->name('recipes.rate');
    Route::delete('/rezepte/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    Route::get('/kraft', [StrengthController::class, 'index'])->name('strength.index');
    Route::get('/kraft/neu', [StrengthController::class, 'create'])->name('strength.create');
    Route::post('/kraft', [StrengthController::class, 'store'])->name('strength.store');

    Route::get('/kraft/uebungen', [ExerciseController::class, 'index'])->name('exercises.index');
    Route::post('/kraft/uebungen', [ExerciseController::class, 'store'])->name('exercises.store');
    Route::delete('/kraft/uebungen/{exercise}', [ExerciseController::class, 'destroy'])->name('exercises.destroy');
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('/auth/strava', [StravaAuthController::class, 'redirect'])->name('strava.redirect');
    Route::get('/auth/strava/callback', [StravaAuthController::class, 'callback'])->name('strava.callback');

    Route::get('/auth/withings', [WithingsAuthController::class, 'redirect'])->name('withings.redirect');
    Route::get('/auth/withings/callback', [WithingsAuthController::class, 'callback'])->name('withings.callback');
});
