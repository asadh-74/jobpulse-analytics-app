<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\JobBoardController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::get('/jobs', [JobBoardController::class, 'index'])->name('jobs.index');
Route::get('/jobs/{job}', [JobBoardController::class, 'show'])->name('jobs.show');
