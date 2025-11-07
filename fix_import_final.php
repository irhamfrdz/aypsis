<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;

echo "🔧 Fixing Stock Kontainer Column Size and Import\n";
echo "===============================================\n\n";

echo "🔧 Step 1: Adjusting column sizes...\n";

try {
    // Expand problematic columns
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN akhiran_kontainer VARCHAR(10)");
    DB::statement("ALTER TABLE stock_kontainers MODIFY COLUMN nomor_seri_kontainer VARCHAR(20)");
    echo "✅ Column sizes adjusted\n\n";
} catch (\Exception $e) {
    echo "⚠️ Column adjustment: " . $e->getMessage() . "\n\n";
}

echo "🗑️ Step 2: Cleaning existing data...\n";
StockKontainer::truncate();
echo "✅ All existing data removed\n\n";

echo "📄 Step 3: Reading source SQL file...\n";
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

echo "🔄 Step 4: Extracting and re-mapping data...\n";

// Extract all VALUES entries
preg_match_all('/\(([^)]+)\)/', $insertStatement, $matches);
$totalEntries = count($matches[1]);

echo "📊 Found {$totalEntries} records to process\n\n";

// Process data in smaller batches
$batchSize = 50;
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
            
            // Clean and prepare values
            $id = intval($parsedValues[0]);
            $ukuran = $parsedValues[1] ?? null;
            $tipeKontainer = $parsedValues[2] ?? null;
            $status = $parsedValues[3] ?? 'available';
            $awalanKontainer = $parsedValues[7] ?? null;
            $nomorSeriKontainer = $parsedValues[8] ?? null;
            $akhiranKontainer = $parsedValues[9] ?? null;
            $nomorSeriGabungan = $parsedValues[10] ?? null;
            $createdAt = $parsedValues[12] ?? now();
            $updatedAt = $parsedValues[13] ?? now();
            
            // Map source data to correct target columns
            $mappedData = [
                'id' => $id,
                'awalan_kontainer' => $awalanKontainer,
                'nomor_seri_kontainer' => $nomorSeriKontainer,
                'akhiran_kontainer' => $akhiranKontainer,
                'nomor_seri_gabungan' => $nomorSeriGabungan,
                'ukuran' => $ukuran,
                'tipe_kontainer' => $tipeKontainer,
                'status' => $status,
                'tanggal_masuk' => null,
                'tanggal_keluar' => null,
                'keterangan' => $nomorSeriGabungan, // Same as gabungan for now
                'tahun_pembuatan' => null,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
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
    echo "📋 Data is now correctly mapped to the right columns.\n";
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error during import: " . $e->getMessage() . "\n";
    echo "📋 Import rolled back\n";
}