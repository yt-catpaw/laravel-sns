<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\SummarizeDailyPosts;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

if (app()->environment('production')) {
    Schedule::command(SummarizeDailyPosts::class)
        ->dailyAt('01:00')
        ->onOneServer();
}
