<?php

namespace App\Console;

use App\Console\Commands\lastLogin;
use App\Console\Commands\clientRecommendation;
use App\Console\Commands\beforeGig;
use App\Console\Commands\afterGig;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        lastLogin::class,
        clientRecommendation::class,
        beforeGig::class,
        afterGig::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('user:llogin')->dailyAt('16:00');
        $schedule->command('client:recommendation')->hourly();
        $schedule->command('user:reminder')->hourly();
        $schedule->command('user:review')->dailyAt('16:00');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
