<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Artisan::command('app:check-appointment-date', function () {

    $this->info('Checking appointment dates...');
})->purpose('Check appointment dates and process them.')
    ->everyFiveMinutes();
