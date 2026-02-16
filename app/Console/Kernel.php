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
use App\Console\Commands\CheckTagihanPermissions;
use App\Console\Commands\CheckTagihanPerbaikanPermissions;
use App\Console\Commands\ValidateDuplicateKontainers;

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
        // Pranota OB tools
        \App\Console\Commands\CheckPranotaObItems::class,
        \App\Console\Commands\DumpPranotaOb::class,
        \App\Console\Commands\BackfillPranotaObItems::class,
    CheckTagihanPerbaikanPermissions::class,
    ValidateDuplicateKontainers::class,
    \App\Console\Commands\FixPenerimaTirtaInvestama::class,
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

    // Validate and fix duplicate kontainers daily at 04:00
    $schedule->command('kontainer:validate-duplicates --fix')
             ->dailyAt('04:00')
             ->appendOutputTo(storage_path('logs/duplicate-validation.log'));

    // Recalculate grand_total for all tagihan every hour
    $schedule->command('tagihan:recalculate-grand-total --force')
             ->hourly()
             ->withoutOverlapping()
             ->appendOutputTo(storage_path('logs/grand-total-recalculation.log'));

    // Update manifest and tanda terima penerima every 30 minutes
    $schedule->command('manifest:update-penerima --all')->everyThirtyMinutes()->withoutOverlapping();
    $schedule->command('tanda-terima:update-penerima --all')->everyThirtyMinutes()->withoutOverlapping();
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
