<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Carbon\Carbon;

class RecalculateTagihanDPP extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tagihan:recalculate-dpp
                            {--container= : Specific container number to recalculate}
                            {--periode= : Specific periode to recalculate}
                            {--dry-run : Show what would be changed without actually updating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate DPP for tagihan kontainer sewa based on correct tarif logic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $container = $this->option('container');
        $periode = $this->option('periode');
        $dryRun = $this->option('dry-run');

        $query = DaftarTagihanKontainerSewa::query();

        if ($container) {
            $query->where('nomor_kontainer', $container);
        }

        if ($periode) {
            $query->where('periode', $periode);
        }

        $tagihans = $query->get();

        if ($tagihans->isEmpty()) {
            $this->error('No tagihan found with the specified criteria.');
            return 1;
        }

        $this->info("Found {$tagihans->count()} tagihan to process...");

        $updated = 0;
        $errors = 0;

        foreach ($tagihans as $tagihan) {
            try {
                $oldDpp = (float) $tagihan->dpp;
                $newCalculation = $this->calculateCorrectDPP($tagihan);

                if (abs($newCalculation['dpp'] - $oldDpp) > 0.01) { // Use small threshold for float comparison
                    $this->line("Container: {$tagihan->nomor_kontainer}, Periode: {$tagihan->periode}");
                    $this->line("  Tarif: {$tagihan->tarif}");
                    $this->line("  Old DPP: " . number_format($oldDpp, 2));
                    $this->line("  New DPP: " . number_format($newCalculation['dpp'], 2));
                    $this->line("  New PPN: " . number_format($newCalculation['ppn'], 2));
                    $this->line("  New PPH: " . number_format($newCalculation['pph'], 2));
                    $this->line("  New Total: " . number_format($newCalculation['grand_total'], 2));

                    if (!$dryRun) {
                        $tagihan->update([
                            'dpp' => $newCalculation['dpp'],
                            'ppn' => $newCalculation['ppn'],
                            'pph' => $newCalculation['pph'],
                            'grand_total' => $newCalculation['grand_total'],
                        ]);
                        $this->info("  ✓ Updated");
                    } else {
                        $this->info("  (Dry run - not updated)");
                    }

                    $updated++;
                } else {
                    $this->line("Container: {$tagihan->nomor_kontainer}, Periode: {$tagihan->periode} - No change needed");
                }
            } catch (\Exception $e) {
                $this->error("Error processing {$tagihan->nomor_kontainer} periode {$tagihan->periode}: " . $e->getMessage());
                $errors++;
            }
        }

        $this->info("\nSummary:");
        $this->info("Processed: {$tagihans->count()}");
        $this->info("Updated: {$updated}");
        $this->info("Errors: {$errors}");

        if ($dryRun) {
            $this->warn("This was a dry run. Use without --dry-run to actually update the data.");
        }

        return 0;
    }

    /**
     * Calculate correct DPP based on tarif type
     */
    private function calculateCorrectDPP(DaftarTagihanKontainerSewa $tagihan): array
    {
        $tarifType = $tagihan->tarif;
        $periode = $tagihan->periode;
        $size = $tagihan->size;
        $vendor = $tagihan->vendor;

        // Get master pricelist based on tarif type and effective date
        $tagihanDate = Carbon::parse($tagihan->tanggal_awal);
        $masterPricelist = MasterPricelistSewaKontainer::where('ukuran_kontainer', $size)
            ->where('vendor', $vendor)
            ->where('tarif', $tarifType)
            ->where('tanggal_harga_awal', '<=', $tagihanDate)
            ->orderBy('tanggal_harga_awal', 'desc')
            ->first();

        $dpp = 0;

        if ($masterPricelist) {
            if ($tarifType === 'Harian') {
                // For harian: calculate days and multiply by daily rate
                $jumlahHari = $this->calculateDaysFromTagihan($tagihan);
                $dpp = $masterPricelist->harga * $jumlahHari;
            } else {
                // For bulanan: use monthly rate * periode
                $dpp = $masterPricelist->harga * $periode;
            }
        } else {
            throw new \Exception("Master pricelist not found for {$vendor} {$size}ft {$tarifType}");
        }

        // Calculate PPN (11% of DPP)
        $ppn = $dpp * 0.11;

        // Calculate PPH (2% of DPP)
        $pph = $dpp * 0.02;

        // Grand Total = DPP + PPN - PPH
        $grand_total = $dpp + $ppn - $pph;

        return [
            'dpp' => $dpp,
            'ppn' => $ppn,
            'pph' => $pph,
            'grand_total' => $grand_total,
        ];
    }

    /**
     * Calculate number of days from tagihan data
     */
    private function calculateDaysFromTagihan(DaftarTagihanKontainerSewa $tagihan): int
    {
        // Prioritize tanggal_awal and tanggal_akhir (actual database dates)
        // Field 'masa' is intentionally reduced by 1 day for display purposes
        if ($tagihan->tanggal_awal && $tagihan->tanggal_akhir) {
            try {
                $startDate = Carbon::parse($tagihan->tanggal_awal);
                $endDate = Carbon::parse($tagihan->tanggal_akhir);
                return $startDate->diffInDays($endDate) + 1; // +1 karena termasuk hari pertama
            } catch (\Exception $e) {
                // If parsing fails, fall back to masa calculation
            }
        }

        // Fall back to masa field (but this is less accurate as it's reduced by 1 day)
        if ($tagihan->masa && strpos($tagihan->masa, ' - ') !== false) {
            try {
                $parts = explode(' - ', $tagihan->masa);
                if (count($parts) === 2) {
                    $startDate = Carbon::parse($parts[0]);
                    $endDate = Carbon::parse($parts[1]);
                    return $startDate->diffInDays($endDate) + 1; // +1 karena termasuk hari pertama
                }
            } catch (\Exception $e) {
                // If all else fails, default to periode * 30 (approximate days per period)
                return $tagihan->periode * 30;
            }
        }

        // Last resort: assume 30 days per periode
        return $tagihan->periode * 30;
    }
}
