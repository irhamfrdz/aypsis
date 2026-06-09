<?php

namespace App\Console\Commands;

use App\Models\NaikKapal;
use Illuminate\Console\Command;
use Log;

class SyncNaikKapalSeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:naik-kapal-seals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync missing seal numbers from prospeks to naik_kapal table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting synchronization of seal numbers...');
        Log::info('Console Command: Starting sync:naik-kapal-seals');

        $query = NaikKapal::where(function ($q) {
            $q->whereNull('no_seal')->orWhere('no_seal', '');
        })->whereNotNull('prospek_id');

        $total = $query->count();
        $this->info("Found {$total} naik_kapal records with empty no_seal and valid prospek_id.");

        if ($total === 0) {
            $this->info('No records to update.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updatedCount = 0;

        $query->chunk(100, function ($records) use ($bar, &$updatedCount) {
            foreach ($records as $record) {
                if ($record->prospek && ! empty($record->prospek->no_seal)) {
                    $record->update([
                        'no_seal' => $record->prospek->no_seal,
                    ]);
                    $updatedCount++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->info("Successfully updated {$updatedCount} records.");
        Log::info("Console Command: Finished sync:naik-kapal-seals, updated {$updatedCount} records");

        return Command::SUCCESS;
    }
}
