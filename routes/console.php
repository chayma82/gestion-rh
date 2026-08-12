<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Schedule::command('employes:synchroniser-statuts')
    ->everyMinute();



Schedule::command('contrats:actualiser')->dailyAt('00:05')
    ->withoutOverlapping();
Schedule::command('contrats:expirer')->everyMinute()
    ->withoutOverlapping();

Schedule::command('salaires:generer-mensuel')->everyMinute()
    ->withoutOverlapping();
Schedule::command('factures:maj-statuts')->everyMinute()
    ->withoutOverlapping();
Schedule::command('notifications:verifier-contrats')->everyMinute()
    ->withoutOverlapping();
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
