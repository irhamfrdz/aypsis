<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Bl;
use App\Models\Prospek;

class FixBlContainerTypeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:bl-types {--dry-run : Only show what would be updated}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perbaiki data tipe kontainer pada tabel bls (hanya yang memiliki prospek_id)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn("Running in DRY RUN mode. No data will be modified.");
        } else {
            $this->info("Starting data fix for BL container types (Strict Mode)...");
            $this->info("Only records with existing prospek_id will be processed.");
        }

        // Only get BLs that have a prospek_id
        $bls = Bl::whereNotNull('prospek_id')->get();
        $total = $bls->count();
        $updated = 0;
        $failedMatch = 0;
        $noChangeNeeded = 0;

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($bls as $bl) {
            $prospek = $bl->prospek;

            if ($prospek && !empty($prospek->tipe)) {
                $targetTipe = $prospek->tipe;
                
                // Normalisasi pengecekan
                if (strtoupper(trim($bl->tipe_kontainer ?? '')) !== strtoupper(trim($targetTipe))) {
                    if (!$dryRun) {
                        $bl->tipe_kontainer = $targetTipe;
                        $bl->save();
                    }
                    $updated++;
                } else {
                    $noChangeNeeded++;
                }
            } else {
                $failedMatch++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $this->table(
            ['Category', 'Count'],
            [
                ['Records with prospek_id', $total],
                ['Updated/To be updated', $updated],
                ['Correct/Already Match', $noChangeNeeded],
                ['No valid type in Prospek', $failedMatch],
            ]
        );
        
        $totalBl = Bl::count();
        $manualBl = $totalBl - $total;
        $this->info("Note: $manualBl records without prospek_id were skipped (Manual Entries).");
        
        if ($dryRun && $updated > 0) {
            $this->info("Run: php artisan fix:bl-types to apply changes.");
        }
    }
}

