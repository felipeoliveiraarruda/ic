<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// Executa todo domingo à meia-noite (ou use ->monthly() para rodar 1x por mês)
Schedule::command('sync:replicado-names')->weeklyOn(0, '00:00');