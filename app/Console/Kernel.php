<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\FetchIdeasFromMailbox;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\RemoveOldNotAttachedMedia::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Каждый день в полночь удалять старые медиа, которые не ассоциированы ни с одной моделью
//        $schedule->command('media:remove-old')->daily();

        $schedule->command('excel:update')->withoutOverlapping()->cron('0 */8 * * *');
//        // Каждые 15 минут загружать список идей из почты (только на тестовом или боевом сайте)
//        $schedule->command(FetchIdeasFromMailbox::class)
//            ->everyFiveMinutes()
//            ->environments(['staging', 'production']);
//
//        // Каждые пять минут проверять новых пользователей в телеге
//        $schedule->command('app:telegram:check')->everyMinute();
//
//        $schedule->command('app:task:check')->everyMinute();
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
