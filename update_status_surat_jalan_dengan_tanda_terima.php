<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalan;
use App\Models\TandaTerima;

echo "=================================================\n";
echo "Update Status Surat Jalan dengan Tanda Terima\n";
echo "=================================================\n\n";

try {
    DB::beginTransaction();
    
    // Cari semua tanda terima
    $tandaTerimas = TandaTerima::all();
    
    echo "Ditemukan " . $tandaTerimas->count() . " tanda terima\n\n";
    
    $updated = 0;
    $skipped = 0;
    
    foreach ($tandaTerimas as $tandaTerima) {
        if ($tandaTerima->surat_jalan_id) {
            $suratJalan = SuratJalan::find($tandaTerima->surat_jalan_id);
            
            if ($suratJalan) {
                $oldStatus = $suratJalan->status;
                
                // Update status menjadi sudah_checkpoint jika belum
                if ($suratJalan->status !== 'sudah_checkpoint') {
                    $suratJalan->update(['status' => 'sudah_checkpoint']);
                    $updated++;
                    echo "✓ Updated: Surat Jalan #{$suratJalan->id} - {$suratJalan->no_surat_jalan}\n";
                    echo "  Status: {$oldStatus} → sudah_checkpoint\n\n";
                } else {
                    $skipped++;
                }
            }
        }
    }
    
    DB::commit();
    
    echo "\n=================================================\n";
    echo "SELESAI!\n";
    echo "=================================================\n";
    echo "Total diupdate: {$updated} surat jalan\n";
    echo "Total dilewati (sudah benar): {$skipped} surat jalan\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
