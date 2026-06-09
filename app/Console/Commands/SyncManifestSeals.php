<?php

namespace App\Console\Commands;

use App\Models\Bl;
use App\Models\Manifest;
use App\Models\NaikKapal;
use Illuminate\Console\Command;
use Log;

class SyncManifestSeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:manifest-seals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync missing seal numbers in manifests table from prospeks, naik_kapal, or bls';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting synchronization of seal numbers in manifests table...');
        Log::info('Console Command: Starting sync:manifest-seals');

        $query = Manifest::where(function ($q) {
            $q->whereNull('no_seal')->orWhere('no_seal', '');
        });

        $total = $query->count();
        $this->info("Found {$total} manifest records with empty no_seal.");

        if ($total === 0) {
            $this->info('No records to update.');

            return Command::SUCCESS;
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $updatedCount = 0;

        $query->chunk(100, function ($manifests) use ($bar, &$updatedCount) {
            foreach ($manifests as $manifest) {
                $seal = null;

                // 1. Try from associated Prospek
                if ($manifest->prospek_id && $manifest->prospek) {
                    if (! empty($manifest->prospek->no_seal)) {
                        $seal = $manifest->prospek->no_seal;
                    }
                }

                // 2. Try from NaikKapal by prospek_id
                if (! $seal && $manifest->prospek_id) {
                    $seal = NaikKapal::where('prospek_id', $manifest->prospek_id)
                        ->whereNotNull('no_seal')
                        ->where('no_seal', '!=', '')
                        ->value('no_seal');
                }

                // 3. Try from NaikKapal by nomor_kontainer and no_voyage
                if (! $seal && ! empty($manifest->nomor_kontainer) && ! empty($manifest->no_voyage)) {
                    $seal = NaikKapal::where('nomor_kontainer', $manifest->nomor_kontainer)
                        ->where('no_voyage', $manifest->no_voyage)
                        ->whereNotNull('no_seal')
                        ->where('no_seal', '!=', '')
                        ->value('no_seal');
                }

                // 4. Try from Bl by nomor_kontainer and no_voyage
                if (! $seal && ! empty($manifest->nomor_kontainer) && ! empty($manifest->no_voyage)) {
                    // Check if Bl model exists (it exists in AppServiceProvider observers)
                    if (class_exists(\App\Models\Bl::class)) {
                        $seal = \App\Models\Bl::where('nomor_kontainer', $manifest->nomor_kontainer)
                            ->where('no_voyage', $manifest->no_voyage)
                            ->whereNotNull('no_seal')
                            ->where('no_seal', '!=', '')
                            ->value('no_seal');
                    }
                }

                if ($seal) {
                    $manifest->update(['no_seal' => $seal]);
                    $updatedCount++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        $this->info("Successfully updated {$updatedCount} manifest records.");
        Log::info("Console Command: Finished sync:manifest-seals, updated {$updatedCount} records");

        return Command::SUCCESS;
    }
}
