<?php

namespace App\Console;

use App\Jobs\CloseUnassignedOrders;
use App\Jobs\SetDriversToUnavailable;
use App\Models\User;
use App\Jobs\RemoveOldOrdersInFirebase;
use Illuminate\Console\Scheduling\Schedule;
use App\Jobs\OpenAndCloseRestaurantAutomtion;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
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

        // we log using system user to log anything in system by his name
        auth()->login(User::findOrFail(1)); // 1 : system user

        $schedule->job(new OpenAndCloseRestaurantAutomtion)->everyMinute();
        $schedule->job(new CloseUnassignedOrders)->everyMinute();
        $schedule->job(new RemoveOldOrdersInFirebase)->everyMinute();
        $schedule->job(new SetDriversToUnavailable)->everyMinute();
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
