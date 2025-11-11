<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\PranotaTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

class RecalculateTagihanGrandTotal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:recalculate-grand-total {--force : Force recalculation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate grand_total for all existing tagihan kontainer sewa based on DPP, adjustment, PPN, and PPH';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $startTime = now();
        $this->info('🔄 Starting Grand Total Recalculation for Tagihan Kontainer Sewa...');
        $this->info('Started at: ' . $startTime->format('Y-m-d H:i:s'));
        $this->newLine();

        // Get total count
        $totalCount = DaftarTagihanKontainerSewa::count();
        
        if ($totalCount === 0) {
            $this->warn('No tagihan found in database.');
            $this->info('Completed at: ' . now()->format('Y-m-d H:i:s'));
            return 0;
        }

        $this->info("Found {$totalCount} tagihan records.");
        
        // Show confirmation only if not forced
        if (!$this->option('force')) {
            $this->newLine();
            if (!$this->confirm('Do you want to proceed with recalculation?', true)) {
                $this->warn('Operation cancelled.');
                return 1;
            }
        }

        $this->newLine();

        // Create progress bar (skip for non-interactive/scheduled mode)
        $progressBar = null;
        $isForced = $this->option('force');
        
        if (!$isForced) {
            $progressBar = $this->output->createProgressBar($totalCount);
            $progressBar->start();
        }

        $updatedCount = 0;
        $unchangedCount = 0;
        $errorCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            // Process in chunks to avoid memory issues
            DaftarTagihanKontainerSewa::chunk(100, function ($tagihans) use (&$progressBar, &$updatedCount, &$unchangedCount, &$errorCount, &$errors) {
                foreach ($tagihans as $tagihan) {
                    try {
                        // Store old grand total
                        $oldGrandTotal = $tagihan->grand_total;

                        // Recalculate taxes
                        $tagihan->recalculateTaxes();
                        
                        // Calculate new grand total (this will be done automatically in save)
                        $tagihan->calculateGrandTotal();
                        
                        // Check if changed
                        if (abs($oldGrandTotal - $tagihan->grand_total) > 0.01) {
                            // Save without triggering boot again
                            $tagihan->saveQuietly();
                            $updatedCount++;
                        } else {
                            $unchangedCount++;
                        }

                    } catch (\Exception $e) {
                        $errorCount++;
                        $errors[] = [
                            'id' => $tagihan->id,
                            'container' => $tagihan->nomor_kontainer,
                            'error' => $e->getMessage()
                        ];
                    }

                    if ($progressBar) {
                        $progressBar->advance();
                    }
                }
            });

            DB::commit();

            if ($progressBar) {
                $progressBar->finish();
                $this->newLine(2);
            }

            $endTime = now();
            $duration = $startTime->diffInSeconds($endTime);

            // Display results
            $this->info('✅ Recalculation completed!');
            $this->info('Completed at: ' . $endTime->format('Y-m-d H:i:s'));
            $this->info('Duration: ' . $duration . ' seconds');
            $this->newLine();
            
            $this->table(
                ['Status', 'Count'],
                [
                    ['Updated', $updatedCount],
                    ['Unchanged', $unchangedCount],
                    ['Errors', $errorCount],
                    ['Total Processed', $totalCount],
                ]
            );

            if ($errorCount > 0) {
                $this->newLine();
                $this->error("⚠️  {$errorCount} errors occurred during recalculation:");
                $this->table(
                    ['ID', 'Container', 'Error'],
                    array_map(function($error) {
                        return [$error['id'], $error['container'], $error['error']];
                    }, array_slice($errors, 0, 10)) // Show first 10 errors
                );
                
                if (count($errors) > 10) {
                    $this->warn('... and ' . (count($errors) - 10) . ' more errors.');
                }
            }

            // Update pranota total_amount for affected pranota
            if ($updatedCount > 0) {
                $this->newLine();
                $this->info('🔄 Updating pranota total amounts...');
                
                $affectedPranotaIds = DaftarTagihanKontainerSewa::whereNotNull('pranota_id')
                    ->distinct()
                    ->pluck('pranota_id');
                
                $pranotaUpdatedCount = 0;
                
                foreach ($affectedPranotaIds as $pranotaId) {
                    $pranota = PranotaTagihanKontainerSewa::find($pranotaId);
                    
                    if ($pranota) {
                        $oldTotal = $pranota->total_amount;
                        $newTotal = DaftarTagihanKontainerSewa::where('pranota_id', $pranotaId)
                            ->sum('grand_total');
                        
                        if (abs($oldTotal - $newTotal) > 0.01) {
                            $pranota->total_amount = $newTotal;
                            $pranota->jumlah_tagihan = DaftarTagihanKontainerSewa::where('pranota_id', $pranotaId)->count();
                            $pranota->save();
                            $pranotaUpdatedCount++;
                        }
                    }
                }
                
                $this->info("✅ Updated {$pranotaUpdatedCount} pranota records.");
            }

            $this->newLine();
            $this->info('🎉 Done!');

            return 0;

        } catch (\Exception $e) {
            DB::rollback();
            $this->error('❌ Error during recalculation: ' . $e->getMessage());
            $this->info('Completed at: ' . now()->format('Y-m-d H:i:s'));
            return 1;
        }
    }
}
