<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\CloseExpiredProcesses::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Ejecutar al final del día (5 segundos antes de medianoche)
        $schedule->command('processes:close-expired')->dailyAt('23:59:55');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
