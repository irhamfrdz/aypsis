<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SuratJalan;

echo "=== TEST SURAT JALAN APPROVALS RELATIONSHIP ===\n\n";

try {
    // Test relationship
    $suratJalan = SuratJalan::first();
    
    if ($suratJalan) {
        echo "✅ SuratJalan ditemukan: {$suratJalan->no_surat_jalan}\n";
        
        // Test approvals relationship
        $approvals = $suratJalan->approvals;
        echo "✅ Relationship 'approvals' berhasil dipanggil\n";
        echo "📊 Jumlah approval: " . $approvals->count() . "\n";
        
        if ($approvals->count() > 0) {
            foreach ($approvals as $approval) {
                echo "   - Level: {$approval->approval_level}, Status: {$approval->status}\n";
            }
        } else {
            echo "ℹ️  Belum ada data approval untuk surat jalan ini\n";
        }
        
    } else {
        echo "❌ Tidak ada data SuratJalan ditemukan\n";
    }
    
    echo "\n✅ Test berhasil! Error relationship sudah teratasi.\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . "\n";
    echo "📍 Line: " . $e->getLine() . "\n";
}

?>