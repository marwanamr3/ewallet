<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\ProcessSubscriptions;
use Artisan;
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
       // Command\test::class
       'App\Console\Commands\test',
       'App\Console\Commands\processSubscription',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
 //       Artisan::call('test');
        $schedule->call('ProcessSubscriptions')
        ->cron('* * * * *');


                
        // $ProcessSubscription = new ProcessSubscriptions();

        
        // $schedule->$ProcessSubscription->everyMinute();
    
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
