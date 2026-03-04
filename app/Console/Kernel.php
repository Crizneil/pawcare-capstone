<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // Register custom commands
    protected $commands = [
        \App\Console\Commands\SendVaccinationReminders::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // Run the vaccination reminder command daily at 8AM
        $schedule->command('vaccinations:reminder')->dailyAt('08:00');
    }

    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
