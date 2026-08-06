<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('salaires:generer-mensuel')->dailyAt('12:22');
Schedule::command('factures:maj-statuts')->everyMinute();
Schedule::command('notifications:verifier-contrats')
    ->everyMinute();
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
