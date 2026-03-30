<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InvoiceAktivitasLain;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\UangJalan;
use App\Models\UangJalanAdjustment;
use Illuminate\Support\Facades\DB;

class SyncInvoiceAdjustments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:sync-invoice-adjustments {--dry-run : Only show what will be changed without applying}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync UangJalan and SuratJalan tables from existing Invoice Aktivitas Lain adjustments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        $invoices = InvoiceAktivitasLain::where('jenis_aktivitas', 'Pembayaran Adjustment Uang Jalan')
            ->where('jenis_penyesuaian', 'penambahan')
            ->get();

        $this->info("Found {$invoices->count()} adjustment invoices to process.");

        foreach ($invoices as $invoice) {
            $this->warn("Processing Invoice: {$invoice->nomor_invoice} (ID: {$invoice->id})");
            
            $sjId = $invoice->surat_jalan_id;
            if (!$sjId) {
                $this->error("  No Surat Jalan ID found for this invoice. Skipping.");
                continue;
            }

            // Determine source (regular or bongkar)
            $source = $invoice->surat_jalan_source;
            $sj = null;
            
            if ($source === 'bongkar') {
                $sj = SuratJalanBongkaran::find($sjId);
            } elseif ($source === 'regular') {
                $sj = SuratJalan::find($sjId);
            } else {
                // Try both if source is null
                $sj = SuratJalan::find($sjId);
                if ($sj) {
                    $source = 'regular';
                } else {
                    $sj = SuratJalanBongkaran::find($sjId);
                    if ($sj) {
                        $source = 'bongkar';
                    }
                }
            }

            if (!$sj) {
                $this->error("  Associated Surat Jalan (ID: {$sjId}) not found in database. Skipping.");
                continue;
            }

            $this->info("  Found SJ: {$sj->no_surat_jalan} (Source: {$source})");

            $details = json_decode($invoice->tipe_penyesuaian, true);
            if (!is_array($details)) {
                $this->warn("  No tipe_penyesuaian details found. Skipping.");
                continue;
            }

            $totalAdjustment = 0;
            $updates = [
                'mel' => 0,
                'parkir' => 0,
                'pelancar' => 0,
                'kawalan' => 0,
                'krani' => 0,
                'retur galon' => 0,
                'adjustment' => 0
            ];

            foreach ($details as $detail) {
                $tipe = strtolower($detail['tipe'] ?? '');
                $nominal = floatval($detail['nominal'] ?? 0);
                $totalAdjustment += $nominal;

                if (array_key_exists($tipe, $updates)) {
                    $updates[$tipe] += $nominal;
                } else {
                    $updates['adjustment'] += $nominal;
                }
            }

            if ($totalAdjustment <= 0) {
                $this->warn("  Total adjustment is 0 or invalid. Skipping.");
                continue;
            }

            if ($dryRun) {
                $this->info("  [DRY RUN] Would increment SJ uang_jalan ({$sj->uang_jalan}) by {$totalAdjustment}");
                $this->info("  [DRY RUN] Breakdown: " . json_encode($updates));
            } else {
                try {
                    DB::transaction(function () use ($sj, $source, $sjId, $totalAdjustment, $updates, $invoice) {
                        // Update Surat Jalan
                        $sj->increment('uang_jalan', $totalAdjustment);

                        // Find Uang Jalan Record
                        $field = $source === 'bongkar' ? 'surat_jalan_bongkaran_id' : 'surat_jalan_id';
                        $uj = UangJalan::where($field, $sjId)->first();
                        
                        if ($uj) {
                            // Update Uang Jalan breakdown
                            $uj->increment('jumlah_mel', $updates['mel']);
                            $uj->increment('jumlah_parkir', $updates['parkir']);
                            $uj->increment('jumlah_pelancar', $updates['pelancar']);
                            $uj->increment('jumlah_kawalan', $updates['kawalan']);
                            
                            // Map remaining adjustment components to 'jumlah_penyesuaian' (adjustment field in UangJalan)
                            $generalAdjustment = $updates['adjustment'] + $updates['krani'] + $updates['retur galon'];
                            $uj->increment('jumlah_penyesuaian', $generalAdjustment);
                            
                            $uj->increment('jumlah_total', $totalAdjustment);

                            // Create Adjustment Entry in audit table
                            UangJalanAdjustment::create([
                                'uang_jalan_id' => $uj->id,
                                'tanggal_penyesuaian' => $invoice->tanggal_invoice ?? now(),
                                'jenis_penyesuaian' => 'penambahan',
                                'debit_kredit' => 'debit',
                                'jumlah_penyesuaian' => $totalAdjustment,
                                'jumlah_mel' => $updates['mel'],
                                'jumlah_parkir' => $updates['parkir'],
                                'jumlah_pelancar' => $updates['pelancar'],
                                'jumlah_kawalan' => $updates['kawalan'],
                                'alasan_penyesuaian' => "Sync dari Invoice Aktivitas Lain: {$invoice->nomor_invoice}",
                                'memo' => $invoice->deskripsi,
                                'created_by' => $invoice->created_by ?? 1
                            ]);
                        }
                    });
                    $this->info("  Status SUCCESS: Database synchronized.");
                } catch (\Exception $e) {
                    $this->error("  Status FAILED: {$e->getMessage()}");
                }
            }
        }

        $this->info("Sync operation completed.");
    }
}
