<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Prospek;
use App\Models\TandaTerima;
use App\Models\SuratJalan;
use Illuminate\Support\Facades\DB;

echo "Analyzing and fixing mismatched Prospek records...\n";

$prospeks = Prospek::whereNotNull('tanda_terima_id')->whereNotNull('surat_jalan_id')->get();
$fixedCount = 0;
$mismatchCount = 0;

DB::beginTransaction();

try {
    foreach ($prospeks as $p) {
        if ($p->tandaTerima && $p->tandaTerima->surat_jalan_id !== $p->surat_jalan_id) {
            $mismatchCount++;
            
            // Find correct Tanda Terima
            $correctTT = TandaTerima::where('surat_jalan_id', $p->surat_jalan_id)->first();
            
            if ($correctTT) {
                // Link to correct Tanda Terima
                $p->tanda_terima_id = $correctTT->id;
                
                // Get container & seal from correct Tanda Terima
                $nomorKontainers = array_values(array_filter(array_map('trim', explode(',', $correctTT->no_kontainer))));
                $noSeals = array_values(array_filter(array_map('trim', explode(',', $correctTT->no_seal))));
                
                // Suffix checking for split prospeks (e.g. -1, -2)
                $parsedIndex = null;
                if ($p->no_surat_jalan && preg_match('/-(\d+)$/', $p->no_surat_jalan, $matches)) {
                    $parsedIndex = (int) $matches[1];
                }
                
                if ($parsedIndex !== null) {
                    $arrayIndex = $parsedIndex - 1;
                    $p->nomor_kontainer = $nomorKontainers[$arrayIndex] ?? null;
                    $p->no_seal = $noSeals[$arrayIndex] ?? null;
                } else {
                    if (count($nomorKontainers) > 1) {
                        $p->nomor_kontainer = implode(',', $nomorKontainers);
                        $p->no_seal = implode(',', $noSeals);
                    } else {
                        $p->nomor_kontainer = $nomorKontainers[0] ?? null;
                        $p->no_seal = $noSeals[0] ?? null;
                    }
                }
                
                // Update destination
                $p->tujuan_pengiriman = $correctTT->tujuan_pengiriman;
                
                // Calculate volume, tonase, kuantitas from correct TT's items
                $totalVolume = 0;
                $totalTonase = 0;
                $kuantitas = 0;
                
                if ($correctTT->dimensi_items) {
                    foreach ($correctTT->dimensi_items as $item) {
                        if (isset($item['meter_kubik']) && is_numeric($item['meter_kubik'])) {
                            $totalVolume += round((float) $item['meter_kubik'], 3);
                        }
                        if (isset($item['tonase']) && is_numeric($item['tonase'])) {
                            $totalTonase += round((float) $item['tonase'], 3);
                        }
                    }
                }
                
                // Fallbacks
                if ($totalVolume == 0 && $correctTT->meter_kubik) {
                    $totalVolume = round((float) $correctTT->meter_kubik, 3);
                }
                if ($totalTonase == 0 && $correctTT->tonase) {
                    $totalTonase = round((float) $correctTT->tonase, 3);
                }
                
                if ($correctTT->jumlah) {
                    $jumlahArray = explode(',', $correctTT->jumlah);
                    foreach ($jumlahArray as $j) {
                        if (is_numeric(trim($j))) {
                            $kuantitas += (int) trim($j);
                        }
                    }
                }
                
                if ($totalVolume > 0) $p->total_volume = $totalVolume;
                if ($totalTonase > 0) $p->total_ton = $totalTonase;
                if ($kuantitas > 0) $p->kuantitas = $kuantitas;
                
            } else {
                // No correct Tanda Terima exists, unlink and sync from Surat Jalan
                $p->tanda_terima_id = null;
                
                $sj = SuratJalan::find($p->surat_jalan_id);
                if ($sj) {
                    $p->no_surat_jalan = $sj->no_surat_jalan;
                    $p->nomor_kontainer = $sj->no_kontainer;
                    $p->no_seal = $sj->no_seal;
                    $p->nama_supir = $sj->supir;
                    $p->barang = $sj->jenis_barang;
                    $p->pt_pengirim = $sj->pengirim;
                    $p->tujuan_pengiriman = $sj->tujuan_pengiriman;
                }
            }
            
            $p->save();
            $fixedCount++;
        }
    }
    
    DB::commit();
    echo "Done! Mismatched: {$mismatchCount}, Fixed: {$fixedCount}\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
