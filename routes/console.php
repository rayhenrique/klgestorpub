<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// Agendamento automático de backup diário
Schedule::command('backup:database --compress')
    ->daily()
    ->at('02:00')
    ->description('Backup automático diário do banco de dados');

// Limpeza de logs antigos (opcional)
Schedule::command('log:clear')
    ->weekly()
    ->sundays()
    ->at('03:00')
    ->description('Limpeza semanal de logs antigos');
