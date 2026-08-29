<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\HistoryKontainer;
use App\Models\Gudang;
use Illuminate\Support\Facades\DB;

$fixedCount = 0;
$mismatchCount = 0;

$kontainers = Kontainer::all();
$stockKontainers = StockKontainer::all();

echo "Checking Kontainers...\n";
foreach ($kontainers as $k) {
    $nomor = $k->nomor_seri_gabungan ?: $k->awalan_kontainer . $k->nomor_seri_kontainer;
    
    $latestHistory = HistoryKontainer::where('nomor_kontainer', $nomor)
        ->orderBy('tanggal_kegiatan', 'desc')
        ->orderBy('created_at', 'desc')
        ->first();
        
    if ($latestHistory && $latestHistory->gudang_id != $k->gudangs_id) {
        $mismatchCount++;
        echo "Kontainer $nomor mismatch! Current: {$k->gudangs_id}, Should be: {$latestHistory->gudang_id}\n";
        $k->gudangs_id = $latestHistory->gudang_id;
        $k->save();
        $fixedCount++;
    }
}

echo "\nChecking Stock Kontainers...\n";
foreach ($stockKontainers as $sk) {
    $nomor = $sk->nomor_seri_gabungan ?: $sk->awalan_kontainer . $sk->nomor_seri_kontainer;
    
    $latestHistory = HistoryKontainer::where('nomor_kontainer', $nomor)
        ->orderBy('tanggal_kegiatan', 'desc')
        ->orderBy('created_at', 'desc')
        ->first();
        
    if ($latestHistory && $latestHistory->gudang_id != $sk->gudangs_id) {
        $mismatchCount++;
        echo "Stock Kontainer $nomor mismatch! Current: {$sk->gudangs_id}, Should be: {$latestHistory->gudang_id}\n";
        $sk->gudangs_id = $latestHistory->gudang_id;
        $sk->save();
        $fixedCount++;
    }
}

echo "\nDone. Found $mismatchCount mismatches, Fixed $fixedCount records.\n";
