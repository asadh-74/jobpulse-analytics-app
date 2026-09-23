<?php

use App\Http\Controllers\Api\StatsController;
use Illuminate\Support\Facades\Route;

Route::get('/stats', [StatsController::class, 'index'])->name('api.stats');
Route::post('/stats/refresh', [StatsController::class, 'refresh'])
    ->middleware('throttle:5,1')
    ->name('api.stats.refresh');
