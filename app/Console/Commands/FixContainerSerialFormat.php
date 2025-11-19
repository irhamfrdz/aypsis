<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;

class FixContainerSerialFormat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:fix-serial-format {--dry-run : Show what would be changed without actually changing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix container serial number format from AYPU1233211 to AYPU 123321-1';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 Running in DRY RUN mode - no changes will be made');
            $this->info('');
        }

        // Get all stock containers
        $stockKontainers = StockKontainer::whereNotNull('nomor_seri_gabungan')->get();
        
        if ($stockKontainers->isEmpty()) {
            $this->warn('No stock containers found with nomor_seri_gabungan.');
            return 0;
        }

        $this->info("Found {$stockKontainers->count()} stock containers to process.");
        $this->info('');

        $fixed = 0;
        $skipped = 0;
        $errors = 0;

        $this->output->progressStart($stockKontainers->count());

        foreach ($stockKontainers as $stock) {
            try {
                $originalFormat = $stock->nomor_seri_gabungan;
                
                // Check if already in correct format (contains space and dash)
                if (preg_match('/^[A-Z]{4} \d{5,6}-\d$/', $originalFormat)) {
                    $skipped++;
                    $this->output->progressAdvance();
                    continue;
                }

                // Remove any existing spaces and dashes
                $cleanNomor = str_replace([' ', '-'], '', $originalFormat);
                
                $newFormat = null;
                $awalan = null;
                $seri = null;
                $akhiran = null;

                // Parse based on length
                if (strlen($cleanNomor) === 11) {
                    // Standard format: ABCD123456X
                    $awalan = substr($cleanNomor, 0, 4);
                    $seri = substr($cleanNomor, 4, 6);
                    $akhiran = substr($cleanNomor, 10, 1);
                } elseif (strlen($cleanNomor) === 10) {
                    // Alternative format: ABCD12345X
                    $awalan = substr($cleanNomor, 0, 4);
                    $seri = substr($cleanNomor, 4, 5);
                    $akhiran = substr($cleanNomor, 9, 1);
                } else {
                    // Try to use existing components if clean parsing fails
                    if ($stock->awalan_kontainer && $stock->nomor_seri_kontainer && $stock->akhiran_kontainer) {
                        $awalan = $stock->awalan_kontainer;
                        $seri = $stock->nomor_seri_kontainer;
                        $akhiran = $stock->akhiran_kontainer;
                    } else {
                        $this->warn("Skipping ID {$stock->id}: Cannot parse format '{$originalFormat}' (length: " . strlen($cleanNomor) . ")");
                        $errors++;
                        $this->output->progressAdvance();
                        continue;
                    }
                }

                // Create new formatted string
                $newFormat = $awalan . ' ' . $seri . '-' . $akhiran;

                if ($dryRun) {
                    $this->line("ID {$stock->id}: '{$originalFormat}' → '{$newFormat}'");
                } else {
                    // Update the record
                    $stock->update([
                        'awalan_kontainer' => $awalan,
                        'nomor_seri_kontainer' => $seri,
                        'akhiran_kontainer' => $akhiran,
                        'nomor_seri_gabungan' => $newFormat,
                    ]);
                }

                $fixed++;
                
            } catch (\Exception $e) {
                $this->error("Error processing ID {$stock->id}: " . $e->getMessage());
                $errors++;
            }

            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        $this->info('');

        // Summary
        $this->info('📊 Summary:');
        $this->info("✅ Fixed: {$fixed}");
        $this->info("⏭️  Skipped (already correct): {$skipped}");
        $this->info("❌ Errors: {$errors}");
        $this->info("📁 Total processed: {$stockKontainers->count()}");

        if ($dryRun) {
            $this->info('');
            $this->info('To actually apply the changes, run:');
            $this->info('php artisan container:fix-serial-format');
        } else {
            $this->info('');
            $this->info('✨ Format fixing completed!');
        }

        return 0;
    }
}
