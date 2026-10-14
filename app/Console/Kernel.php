<?php

namespace App\Console;

use App\Console\Commands\DeleteManifestByVoyage;
use App\Console\Commands\ValidateDuplicateKontainers;
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
        // Pranota OB tools
        \App\Console\Commands\CheckPranotaObItems::class,
        \App\Console\Commands\DumpPranotaOb::class,
        \App\Console\Commands\BackfillPranotaObItems::class,
        ValidateDuplicateKontainers::class,
        \App\Console\Commands\FixPenerimaTirtaInvestama::class,
        DeleteManifestByVoyage::class,
        \App\Console\Commands\UpdateRodaMobil::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        // Validate and fix duplicate kontainers daily at 04:00
        $schedule->command('kontainer:validate-duplicates --fix')
            ->dailyAt('04:00')
            ->appendOutputTo(storage_path('logs/duplicate-validation.log'));

        // Auto-sync attendance from fingerprint machines every 5 minutes
        $schedule->command('attendance:sync-local')
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/attendance-sync.log'));

        // Update manifest and tanda terima penerima frequency
        $schedule->command('manifest:update-penerima --all')->everyThirtyMinutes()->withoutOverlapping();
        $schedule->command('tanda-terima:update-penerima --all')->everyTenMinutes()->withoutOverlapping();
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
