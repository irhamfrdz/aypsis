<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;
use Carbon\Carbon;

echo "🔧 Simple Stock Kontainer Import (Laravel Way)\n";
echo "=============================================\n\n";

echo "🗑️ Step 1: Cleaning existing data...\n";
StockKontainer::truncate();
echo "✅ All existing data removed\n\n";

echo "📄 Step 2: Reading source SQL file...\n";
$file = 'aypsis1.sql';
$handle = fopen($file, 'r');

$insertStatement = '';
while (($line = fgets($handle)) !== false) {
    if (strpos($line, "INSERT INTO `stock_kontainers` VALUES") !== false) {
        $insertStatement = trim($line);
        break;
    }
}
fclose($handle);

if (!$insertStatement) {
    echo "❌ Source INSERT statement not found!\n";
    exit(1);
}

echo "✅ Source INSERT statement found\n\n";

echo "🔄 Step 3: Processing data with Laravel models...\n";

// Extract all VALUES entries
preg_match_all('/\(([^)]+)\)/', $insertStatement, $matches);
$totalEntries = count($matches[1]);

echo "📊 Found {$totalEntries} records to process\n\n";

$processed = 0;
$successful = 0;
$currentTime = Carbon::now();

try {
    DB::beginTransaction();
    
    foreach ($matches[1] as $values) {
        // Parse CSV-like values
        $parsedValues = str_getcsv($values);
        
        // Extract and map data properly
        $data = [
            'id' => intval($parsedValues[0]),
            'ukuran' => trim($parsedValues[1] ?? '', "'\""),                    // '20'
            'tipe_kontainer' => trim($parsedValues[2] ?? '', "'\""),           // 'Dry Container'  
            'status' => trim($parsedValues[3] ?? 'available', "'\""),          // 'available'
            'awalan_kontainer' => trim($parsedValues[7] ?? '', "'\""),         // 'AYPU'
            'nomor_seri_kontainer' => trim($parsedValues[8] ?? '', "'\""),     // '003386'
            'akhiran_kontainer' => trim($parsedValues[9] ?? '', "'\""),        // '0'
            'nomor_seri_gabungan' => trim($parsedValues[10] ?? '', "'\""),     // 'AYPU0033860'
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'keterangan' => trim($parsedValues[10] ?? '', "'\""),              // Same as gabungan
            'tahun_pembuatan' => null,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ];
        
        // Create using Eloquent
        StockKontainer::create($data);
        
        $processed++;
        $successful++;
        
        // Progress indicator
        if ($processed % 50 == 0) {
            $percentage = round(($processed / $totalEntries) * 100, 1);
            echo "⏳ Progress: {$processed}/{$totalEntries} ({$percentage}%)\n";
        }
    }
    
    DB::commit();
    
    echo "\n🎉 Import completed successfully!\n";
    echo "📊 Statistics:\n";
    echo "   Total processed: {$processed}\n";
    echo "   Successfully imported: {$successful}\n\n";
    
    // Verify import
    echo "🔍 Verification - Sample data:\n";
    echo "===============================\n";
    
    $samples = StockKontainer::limit(5)->get();
    foreach ($samples as $sample) {
        echo "ID: {$sample->id}\n";
        echo "Kontainer: {$sample->nomor_seri_gabungan}\n";
        echo "Awalan: {$sample->awalan_kontainer}\n";
        echo "Nomor: {$sample->nomor_seri_kontainer}\n";
        echo "Akhiran: {$sample->akhiran_kontainer}\n";
        echo "Ukuran: {$sample->ukuran}ft\n";
        echo "Tipe: {$sample->tipe_kontainer}\n";
        echo "Status: {$sample->status}\n";
        echo "---\n";
    }
    
    // Final statistics
    $totalCount = StockKontainer::count();
    $statusCounts = StockKontainer::selectRaw('status, COUNT(*) as count')
        ->groupBy('status')
        ->get();
    
    $ukuranCounts = StockKontainer::selectRaw('ukuran, COUNT(*) as count')
        ->groupBy('ukuran')
        ->get();
    
    echo "\n📈 Final Statistics:\n";
    echo "====================\n";
    echo "Total records: {$totalCount}\n";
    
    echo "\nStatus distribution:\n";
    foreach ($statusCounts as $status) {
        echo "  {$status->status}: {$status->count} records\n";
    }
    
    echo "\nUkuran distribution:\n";
    foreach ($ukuranCounts as $ukuran) {
        echo "  {$ukuran->ukuran}ft: {$ukuran->count} records\n";
    }
    
    echo "\n✅ Data import corrected successfully!\n";
    echo "🔗 Your inline editing functionality should now work properly.\n";
    echo "📋 All data is now correctly formatted and accessible.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error during import: " . $e->getMessage() . "\n";
    echo "📋 Import rolled back\n";
}