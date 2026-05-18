<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Pre-generate upcoming recurring task instances every morning.
// Phase 3 reminders (daily-brief, due-reminders) will register here too.
Schedule::command('tasks:materialize-recurring')
    ->dailyAt('02:00')
    ->withoutOverlapping();
