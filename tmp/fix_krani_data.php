<?php
// To run: php tmp/fix_krani_data.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\InvoiceAktivitasLain;
use App\Models\SuratJalan;
use App\Models\SuratJalanBongkaran;
use App\Models\UangJalan;
use Illuminate\Support\Facades\DB;

echo "Starting Krani Adjustment Fix Logic...\n";

DB::beginTransaction();
try {
    $invoices = InvoiceAktivitasLain::where('jenis_aktivitas', 'Pembayaran Adjustment Uang Jalan')
        ->where('jenis_penyesuaian', 'penambahan')
        ->where('tipe_penyesuaian', 'like', '%krani%')
        ->get();

    echo "Found " . $invoices->count() . " invoices with potentially incorrect Krani adjustments.\n";

    $totalFixed = 0;
    foreach ($invoices as $invoice) {
        if (!$invoice->tipe_penyesuaian || !$invoice->surat_jalan_id) continue;
        
        $tipeDetails = json_decode($invoice->tipe_penyesuaian, true);
        if (!is_array($tipeDetails)) continue;
        
        $kraniTotal = 0;
        foreach ($tipeDetails as $detail) {
            $tipe = strtolower($detail['tipe'] ?? '');
            if ($tipe === 'krani') {
                $nominalStr = isset($detail['nominal']) ? (string)$detail['nominal'] : '0';
                $kraniTotal += (float)str_replace(['.', ','], '', $nominalStr);
            }
        }
        
        if ($kraniTotal <= 0) continue;

        // Check if already fixed
        $keterangan = json_decode($invoice->keterangan, true) ?: [];
        if (isset($keterangan['krani_fixed'])) {
             // echo "Invoice {$invoice->nomor_invoice} already fixed. skipping.\n";
             continue;
        }

        // Find which surat jalan it belongs to
        $sj = SuratJalan::find($invoice->surat_jalan_id);
        $source = 'regular';
        
        if (!$sj) {
            $sj = SuratJalanBongkaran::find($invoice->surat_jalan_id);
            $source = 'bongkar';
        }
        
        if ($sj) {
            echo "[{$invoice->nomor_invoice}] SJ ID: {$sj->id} ({$source}), Krani: " . number_format($kraniTotal, 0, ',', '.') . "\n";
            
            // 1. Revert SuratJalan.uang_jalan
            $sj->decrement('uang_jalan', $kraniTotal);
            
            // 2. Revert UangJalan table
            $ujQuery = UangJalan::query();
            if ($source === 'bongkar') {
                $ujQuery->where('surat_jalan_bongkaran_id', $sj->id);
            } else {
                $ujQuery->where('surat_jalan_id', $sj->id);
            }
            
            // Get the record that was updated. 
            // Since we don't have a direct link from Invoice to UangJalan, 
            // we use the same logic as store(): latest().
            // However, it's possible that the UangJalan entry was created BEFORE the adjustment.
            $uangJalan = $ujQuery->latest()->first();
            
            if ($uangJalan) {
                $uangJalan->decrement('jumlah_penyesuaian', $kraniTotal);
                $uangJalan->decrement('jumlah_total', $kraniTotal);
                echo "  - Deducted from UangJalan ID: {$uangJalan->id}\n";
            } else {
                echo "  - Warning: No UangJalan record found for SJ {$sj->id}\n";
            }
            
            // Mark as fixed in the invoice metadata
            $keterangan['krani_fixed'] = true;
            $keterangan['krani_fixed_at'] = date('Y-m-d H:i:s');
            $keterangan['krani_amount_deducted'] = $kraniTotal;
            $invoice->update(['keterangan' => json_encode($keterangan)]);
            $totalFixed++;
        } else {
            echo "  - Warning: Surat Jalan ID {$invoice->surat_jalan_id} not found in DB.\n";
        }
    }
    
    DB::commit();
    echo "\nFinal Result: Fixed $totalFixed invoices.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Fatal Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
