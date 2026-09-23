<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Respect Remotive's "please don't poll more than a few times a day" guidance
// (see .env's REMOTIVE_MAX_REQUESTS_PER_DAY) — every 6 hours is 4x/day.
Schedule::command('jobs:fetch')
    ->everySixHours()
    ->withoutOverlapping()
    ->onOneServer();
