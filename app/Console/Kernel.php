<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            $output = [];
            $return_var = 0;
            exec('php ' . base_path('artisan') . ' driver_money:update', $output, $return_var);

            if ($return_var !== 0) {
                Log::error('Command driver_money:update failed: ' . implode("\n", $output));
            } else {
                Log::info('Command driver_money:update succeeded: ' . implode("\n", $output));
            }
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
