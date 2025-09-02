<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
 use App\Console\Commands\InspectPranotaKontainer;
 use App\Console\Commands\CleanTagihanPranota;
 use App\Console\Commands\RestoreTagihanPranota;
use App\Console\Commands\SyncTagihanPeriode;
use App\Console\Commands\BackfillTagihanMasa;
use App\Console\Commands\BackfillTagihanMasaString;
use App\Console\Commands\CreateNextPeriodeTagihan;
use App\Console\Commands\UpdateKontainerPeriods;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
    InspectPranotaKontainer::class,
    CleanTagihanPranota::class,
    RestoreTagihanPranota::class,
    SyncTagihanPeriode::class,
    BackfillTagihanMasa::class,
    BackfillTagihanMasaString::class,
    CreateNextPeriodeTagihan::class,
    UpdateKontainerPeriods::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    // Run periode sync daily at 02:10
    $schedule->command('tagihan:sync-periode')->dailyAt('02:10');
    // Create next periode entries daily at 03:00
    $schedule->command('tagihan:create-next-periode')->dailyAt('03:00');
    // Update ongoing container periods daily at 01:00
    $schedule->command('kontainer:update-periods')->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
