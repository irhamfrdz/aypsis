<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\SuratJalan;

echo "=== DEBUG SURAT JALAN YANG TIDAK MUNCUL ===\n\n";

// Cek semua surat jalan
$allSuratJalan = \App\Models\SuratJalan::with(['order.pengirim'])
    ->orderBy('created_at', 'desc')
    ->limit(20)
    ->get();

echo "Total Surat Jalan (20 terakhir): " . $allSuratJalan->count() . "\n\n";

foreach ($allSuratJalan as $sj) {
    echo "ID: {$sj->id} | No: {$sj->no_surat_jalan}\n";
    echo "  - Order ID: " . ($sj->order_id ?? 'NULL') . "\n";
    echo "  - Status Pembayaran UJ: " . ($sj->status_pembayaran_uang_jalan ?? 'NULL') . "\n";
    echo "  - Is Supir Customer: " . ($sj->is_supir_customer ?? 'NULL') . "\n";
    echo "  - Status: " . ($sj->status ?? 'NULL') . "\n";
    
    // Cek apakah sudah ada uang jalan
    $uangJalan = \App\Models\UangJalan::where('surat_jalan_id', $sj->id)->first();
    echo "  - Sudah ada Uang Jalan: " . ($uangJalan ? 'YA (ID: '.$uangJalan->id.')' : 'TIDAK') . "\n";
    
    // Apakah akan muncul di select?
    $willShow = !$sj->order_id ? 'TIDAK (no order_id)' : 
                ($sj->status_pembayaran_uang_jalan !== 'belum_ada' ? 'TIDAK (status: '.$sj->status_pembayaran_uang_jalan.')' :
                ($sj->is_supir_customer ? 'TIDAK (supir customer)' : 'YA'));
    
    echo "  - AKAN MUNCUL: {$willShow}\n";
    echo "  ---\n";
}

// Cek surat jalan bongkaran
echo "\n\n=== SURAT JALAN BONGKARAN ===\n\n";
if (class_exists('\App\Models\SuratJalanBongkaran')) {
    $allBongkaran = \App\Models\SuratJalanBongkaran::orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    echo "Total Surat Jalan Bongkaran (10 terakhir): " . $allBongkaran->count() . "\n\n";

    foreach ($allBongkaran as $sjb) {
        echo "ID: {$sjb->id} | No: {$sjb->nomor_surat_jalan}\n";
        
        // Cek apakah sudah ada uang jalan bongkaran
        $uangJalanB = \App\Models\UangJalanBongkaran::where('surat_jalan_bongkaran_id', $sjb->id)->first();
        echo "  - Sudah ada Uang Jalan Bongkaran: " . ($uangJalanB ? 'YA (ID: '.$uangJalanB->id.')' : 'TIDAK') . "\n";
        echo "  - AKAN MUNCUL: " . ($uangJalanB ? 'TIDAK (sudah ada UJ)' : 'YA') . "\n";
        echo "  ---\n";
    }
}

echo "\n=== END DEBUG ===\n";