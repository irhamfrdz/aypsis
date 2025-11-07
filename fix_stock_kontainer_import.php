<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;

echo "🔧 Correcting Stock Kontainer Data Import\n";
echo "=========================================\n\n";

echo "🗑️ Step 1: Cleaning existing incorrect data...\n";
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

echo "🔄 Step 3: Extracting and re-mapping data...\n";

// Extract all VALUES entries
preg_match_all('/\(([^)]+)\)/', $insertStatement, $matches);
$totalEntries = count($matches[1]);

echo "📊 Found {$totalEntries} records to process\n\n";

// Process data in batches
$batchSize = 100;
$processed = 0;
$successful = 0;

try {
    DB::beginTransaction();
    
    for ($i = 0; $i < $totalEntries; $i += $batchSize) {
        $batch = [];
        
        for ($j = $i; $j < min($i + $batchSize, $totalEntries); $j++) {
            $values = $matches[1][$j];
            
            // Parse CSV-like values with proper quote handling
            $parsedValues = str_getcsv($values);
            
            // Map source data to correct target columns
            // Source: [0]=id, [1]=ukuran, [2]=tipe_kontainer, [3]=status, [7]=awalan, [8]=nomor_seri, [9]=akhiran, [10]=gabungan
            $mappedData = [
                'id' => intval($parsedValues[0]),
                'awalan_kontainer' => $parsedValues[7] ?? null,           // 'AYPU'
                'nomor_seri_kontainer' => $parsedValues[8] ?? null,       // '003386'
                'akhiran_kontainer' => $parsedValues[9] ?? null,          // '0'
                'nomor_seri_gabungan' => $parsedValues[10] ?? null,       // 'AYPU0033860'
                'ukuran' => $parsedValues[1] ?? null,                     // '20'
                'tipe_kontainer' => $parsedValues[2] ?? null,             // 'Dry Container'
                'status' => $parsedValues[3] ?? 'available',              // 'available'
                'tanggal_masuk' => null,
                'tanggal_keluar' => null,
                'keterangan' => $parsedValues[10] ?? null,                // Same as gabungan for now
                'tahun_pembuatan' => null,
                'created_at' => $parsedValues[12] ?? now(),
                'updated_at' => $parsedValues[13] ?? now(),
            ];
            
            $batch[] = $mappedData;
            $processed++;
        }
        
        // Insert batch
        if (!empty($batch)) {
            DB::table('stock_kontainers')->insert($batch);
            $successful += count($batch);
        }
        
        // Progress indicator
        $percentage = round(($processed / $totalEntries) * 100, 1);
        echo "⏳ Progress: {$processed}/{$totalEntries} ({$percentage}%)\r";
    }
    
    DB::commit();
    
    echo "\n\n🎉 Import completed successfully!\n";
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
    
    echo "\n📈 Final Statistics:\n";
    echo "====================\n";
    echo "Total records: {$totalCount}\n";
    echo "Status distribution:\n";
    foreach ($statusCounts as $status) {
        echo "  {$status->status}: {$status->count} records\n";
    }
    
    echo "\n✅ Data import corrected successfully!\n";
    echo "🔗 Your editing functionality should now work properly.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error during import: " . $e->getMessage() . "\n";
    echo "📋 Import rolled back\n";
}