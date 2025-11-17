<?php

/**
 * Script untuk memperbaiki jumlah_per_supir dari PembayaranUangMukaSupirDetail
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranUangMuka;
use App\Models\PembayaranAktivitasLainnya;
use App\Models\PembayaranUangMukaSupirDetail;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;

echo "=== Fix jumlah_per_supir dari PembayaranUangMukaSupirDetail ===\n\n";

try {
    DB::beginTransaction();

    $pembayaranUangMukaList = PembayaranUangMuka::all();
    
    $fixedCount = 0;

    foreach ($pembayaranUangMukaList as $pum) {
        echo "\nProcessing: {$pum->nomor_pembayaran}\n";
        echo "  Current supir_ids: " . json_encode($pum->supir_ids) . "\n";
        echo "  Current jumlah_per_supir: " . json_encode($pum->jumlah_per_supir) . "\n";
        
        // Find corresponding PembayaranAktivitasLainnya
        $pal = PembayaranAktivitasLainnya::where('nomor_pembayaran', $pum->nomor_pembayaran)->first();
        
        if (!$pal) {
            echo "  ⚠️  PembayaranAktivitasLainnya not found\n";
            continue;
        }
        
        echo "  Found PembayaranAktivitasLainnya ID: {$pal->id}\n";
        
        // Get supir details
        $supirDetails = PembayaranUangMukaSupirDetail::where('pembayaran_id', $pal->id)->get();
        
        if ($supirDetails->isEmpty()) {
            echo "  ⚠️  No supir details found\n";
            continue;
        }
        
        echo "  Found {$supirDetails->count()} supir details\n";
        
        $newSupirIds = [];
        $newJumlahPerSupir = [];
        
        foreach ($supirDetails as $detail) {
            echo "    - {$detail->nama_supir}: {$detail->jumlah_uang_muka}\n";
            
            // Find karyawan by nama
            $supir = Karyawan::where('nama_lengkap', $detail->nama_supir)->first();
            
            if ($supir) {
                $newSupirIds[] = $supir->id;
                $newJumlahPerSupir[(string)$supir->id] = (float)$detail->jumlah_uang_muka;
                echo "      -> Supir ID: {$supir->id}\n";
            } else {
                echo "      ❌ Supir not found: {$detail->nama_supir}\n";
            }
        }
        
        echo "  New supir_ids: " . json_encode($newSupirIds) . "\n";
        echo "  New jumlah_per_supir: " . json_encode($newJumlahPerSupir) . "\n";
        
        // Update
        $pum->supir_ids = $newSupirIds;
        $pum->jumlah_per_supir = $newJumlahPerSupir;
        $pum->save();
        
        echo "  ✅ FIXED!\n";
        $fixedCount++;
    }

    DB::commit();
    
    echo "\n=== SUMMARY ===\n";
    echo "Fixed: {$fixedCount}\n";
    echo "\n✅ Done!\n";

} catch (\Exception $e) {
    DB::rollback();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
