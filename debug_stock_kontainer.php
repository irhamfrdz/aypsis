<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\StockKontainer;

echo "🔍 Debugging Stock Kontainer Data\n";
echo "=================================\n\n";

// Check total count
$count = StockKontainer::count();
echo "📊 Total Records: {$count}\n\n";

if ($count > 0) {
    echo "📋 Sample Data (First 5 records):\n";
    echo "-----------------------------------\n";
    
    $samples = StockKontainer::limit(5)->get();
    
    foreach ($samples as $kontainer) {
        echo "ID: {$kontainer->id}\n";
        echo "Nomor: {$kontainer->nomor_seri_gabungan}\n";
        echo "Ukuran: {$kontainer->ukuran}ft\n";
        echo "Tipe: {$kontainer->tipe_kontainer}\n";
        echo "Status: {$kontainer->status}\n";
        echo "---\n";
    }
    
    echo "\n📈 Status Distribution:\n";
    echo "------------------------\n";
    
    $statusCounts = StockKontainer::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    foreach ($statusCounts as $status) {
        echo "{$status->status}: {$status->count} records\n";
    }
    
    echo "\n📏 Ukuran Distribution:\n";
    echo "------------------------\n";
    
    $ukuranCounts = StockKontainer::selectRaw('ukuran, COUNT(*) as count')
        ->groupBy('ukuran')
        ->get();
    
    foreach ($ukuranCounts as $ukuran) {
        echo "{$ukuran->ukuran}ft: {$ukuran->count} records\n";
    }
    
} else {
    echo "❌ No data found in stock_kontainers table\n";
}

echo "\n✅ Debug selesai!\n";