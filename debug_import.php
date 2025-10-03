<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== DEBUG IMPORT PROCESS ===\n\n";

$csvFile = 'C:\\Users\\amanda\\Downloads\\template_import_dpe_auto_group.csv';

if (!file_exists($csvFile)) {
    echo "❌ File tidak ditemukan: {$csvFile}\n";
    exit(1);
}

echo "✅ File ditemukan: {$csvFile}\n";

// Test database connection
try {
    $count = DaftarTagihanKontainerSewa::count();
    echo "✅ Database connection OK, current records: {$count}\n";
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
    exit(1);
}

// Test CSV processing
echo "\n🔍 Processing CSV...\n";

$handle = fopen($csvFile, 'r');
$delimiter = ';';
$rowNumber = 0;
$headers = [];

while (($row = fgetcsv($handle, 1000, $delimiter)) !== false && $rowNumber < 3) {
    $rowNumber++;

    if ($rowNumber === 1) {
        $headers = array_map('trim', $row);
        echo "📋 Headers found: " . implode(', ', $headers) . "\n";
        continue;
    }

    echo "\n📄 Processing row {$rowNumber}:\n";

    // Map data
    $data = [];
    foreach ($headers as $index => $header) {
        $value = isset($row[$index]) ? trim($row[$index]) : '';
        $data[$header] = $value;
        echo "  {$header}: '{$value}'\n";
    }

    // Try to create a record manually
    try {
        // Map to expected format
        $cleaned = [
            'vendor' => $data['vendor'] ?? '',
            'nomor_kontainer' => $data['nomor_kontainer'] ?? '',
            'size' => (int)($data['size'] ?? 0),
            'tanggal_awal' => $data['tanggal_awal'] ?? '',
            'tanggal_akhir' => $data['tanggal_akhir'] ?? '',
            'periode' => (int)($data['periode'] ?? 0),
            'tarif' => 0, // Default untuk test
            'group' => $data['group'] ?? null,
            'status' => $data['status'] ?? 'ongoing',
            'status_pranota' => null,
            'pranota_id' => null,
        ];

        // Calculate periode in days
        if ($cleaned['tanggal_awal'] && $cleaned['tanggal_akhir']) {
            $start = DateTime::createFromFormat('Y-m-d', $cleaned['tanggal_awal']);
            $end = DateTime::createFromFormat('Y-m-d', $cleaned['tanggal_akhir']);

            if ($start && $end) {
                $days = $start->diff($end)->days + 1;
                $cleaned['periode'] = $days;
                $cleaned['masa'] = $days . ' Hari';
            }
        }

        echo "\n  📊 Cleaned data:\n";
        foreach ($cleaned as $key => $value) {
            echo "    {$key}: '{$value}'\n";
        }

        // Check if record exists
        $existing = DaftarTagihanKontainerSewa::where('nomor_kontainer', $cleaned['nomor_kontainer'])
                                              ->where('tanggal_awal', $cleaned['tanggal_awal'])
                                              ->first();

        if ($existing) {
            echo "\n  ⚠️  Record already exists with ID: {$existing->id}\n";
        } else {
            echo "\n  ✅ Record is new, ready for insert\n";

            // Try to insert (remove this if you don't want to actually insert)
            // $record = DaftarTagihanKontainerSewa::create($cleaned);
            // echo "  ✅ Record created with ID: {$record->id}\n";
        }

    } catch (Exception $e) {
        echo "\n  ❌ Error processing row: " . $e->getMessage() . "\n";
        echo "     Stack trace:\n" . $e->getTraceAsString() . "\n";
    }
}

fclose($handle);

echo "\n" . str_repeat("=", 50) . "\n";
echo "🎯 DIAGNOSIS:\n\n";

// Check model fillable fields
echo "📝 Model fillable fields:\n";
$model = new DaftarTagihanKontainerSewa();
$fillable = $model->getFillable();
foreach ($fillable as $field) {
    echo "  - {$field}\n";
}

// Check table structure
echo "\n📊 Database table columns:\n";
$columns = DB::select('DESCRIBE daftar_tagihan_kontainer_sewa');
foreach ($columns as $column) {
    echo "  - {$column->Field} ({$column->Type})" . ($column->Null === 'NO' ? ' NOT NULL' : '') . "\n";
}

echo "\n=== DEBUG COMPLETE ===\n";
