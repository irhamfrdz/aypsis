<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\StockKontainer;
use Carbon\Carbon;

echo "🔧 Smart Stock Kontainer Import (Skip Invalid Data)\n";
echo "==================================================\n\n";

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

echo "🔄 Step 3: Processing data with validation...\n";

// Extract all VALUES entries
preg_match_all('/\(([^)]+)\)/', $insertStatement, $matches);
$totalEntries = count($matches[1]);

echo "📊 Found {$totalEntries} records to process\n\n";

$processed = 0;
$successful = 0;
$skipped = 0;
$currentTime = Carbon::now();

try {
    DB::beginTransaction();
    
    foreach ($matches[1] as $index => $values) {
        // Parse CSV-like values
        $parsedValues = str_getcsv($values);
        
        // Extract and clean data
        $nomorGabungan = trim($parsedValues[10] ?? '', "'\"");
        $awalanKontainer = trim($parsedValues[7] ?? '', "'\"");
        $nomorSeri = trim($parsedValues[8] ?? '', "'\"");
        $akhiran = trim($parsedValues[9] ?? '', "'\"");
        $ukuran = trim($parsedValues[1] ?? '', "'\"");
        $tipeKontainer = trim($parsedValues[2] ?? '', "'\"");
        $status = trim($parsedValues[3] ?? 'available', "'\"");
        
        // Skip if essential data is missing
        if (empty($nomorGabungan) || empty($awalanKontainer) || empty($nomorSeri)) {
            $skipped++;
            $processed++;
            continue;
        }
        
        $data = [
            'id' => intval($parsedValues[0]),
            'ukuran' => $ukuran,
            'tipe_kontainer' => $tipeKontainer,
            'status' => $status,
            'awalan_kontainer' => $awalanKontainer,
            'nomor_seri_kontainer' => $nomorSeri,
            'akhiran_kontainer' => $akhiran,
            'nomor_seri_gabungan' => $nomorGabungan,
            'tanggal_masuk' => null,
            'tanggal_keluar' => null,
            'keterangan' => $nomorGabungan,
            'tahun_pembuatan' => null,
            'created_at' => $currentTime,
            'updated_at' => $currentTime,
        ];
        
        try {
            // Create using Eloquent
            StockKontainer::create($data);
            $successful++;
        } catch (\Exception $e) {
            $skipped++;
            // Skip duplicate or invalid entries
        }
        
        $processed++;
        
        // Progress indicator
        if ($processed % 100 == 0) {
            $percentage = round(($processed / $totalEntries) * 100, 1);
            echo "⏳ Progress: {$processed}/{$totalEntries} ({$percentage}%) - Success: {$successful}, Skipped: {$skipped}\n";
        }
    }
    
    DB::commit();
    
    echo "\n🎉 Import completed!\n";
    echo "📊 Final Statistics:\n";
    echo "   Total processed: {$processed}\n";
    echo "   Successfully imported: {$successful}\n";
    echo "   Skipped (invalid/duplicate): {$skipped}\n\n";
    
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
    
    if ($totalCount > 0) {
        $statusCounts = StockKontainer::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();
        
        $ukuranCounts = StockKontainer::selectRaw('ukuran, COUNT(*) as count')
            ->groupBy('ukuran')
            ->get();
        
        echo "\n📈 Final Database Statistics:\n";
        echo "=============================\n";
        echo "Total records: {$totalCount}\n";
        
        echo "\nStatus distribution:\n";
        foreach ($statusCounts as $status) {
            echo "  {$status->status}: {$status->count} records\n";
        }
        
        echo "\nUkuran distribution:\n";
        foreach ($ukuranCounts as $ukuran) {
            echo "  {$ukuran->ukuran}ft: {$ukuran->count} records\n";
        }
        
        echo "\n✅ Data import completed successfully!\n";
        echo "🔗 Your inline editing functionality should now work properly.\n";
        echo "📋 Stock kontainer data is ready for use in approval system.\n";
    } else {
        echo "\n⚠️ No valid data was imported. Please check the source file.\n";
    }
    
} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Critical error during import: " . $e->getMessage() . "\n";
    echo "📋 Import rolled back\n";
}