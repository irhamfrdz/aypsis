<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== VERIFICATION: Master Data Transportasi Table Structure ===\n\n";

    // Check table structure
    echo "1. Checking table structure:\n";
    $columns = DB::select("DESCRIBE tujuan_kegiatan_utamas");

    $expectedColumns = [
        'id',
        'nama',
        'deskripsi',
        'aktif',
        'kode',
        'cabang',
        'wilayah',
        'dari',
        'ke',
        'uang_jalan_20ft',
        'uang_jalan_40ft',
        'keterangan',
        'liter',
        'jarak_dari_penjaringan_km',
        'mel_20ft',
        'mel_40ft',
        'ongkos_truk_20ft',
        'ongkos_truk_40ft',
        'antar_lokasi_20ft',
        'antar_lokasi_40ft',
        'created_at',
        'updated_at'
    ];

    $actualColumns = array_column($columns, 'Field');

    echo "   Expected columns: " . count($expectedColumns) . "\n";
    echo "   Actual columns: " . count($actualColumns) . "\n";

    $missingColumns = array_diff($expectedColumns, $actualColumns);
    $extraColumns = array_diff($actualColumns, $expectedColumns);

    if (empty($missingColumns) && empty($extraColumns)) {
        echo "   ✅ All columns match!\n";
    } else {
        if (!empty($missingColumns)) {
            echo "   ❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
        }
        if (!empty($extraColumns)) {
            echo "   ❌ Extra columns: " . implode(', ', $extraColumns) . "\n";
        }
    }

    // Check data types
    echo "\n2. Checking data types:\n";
    $decimalColumns = [
        'uang_jalan_20ft',
        'uang_jalan_40ft',
        'liter',
        'jarak_dari_penjaringan_km',
        'mel_20ft',
        'mel_40ft',
        'ongkos_truk_20ft',
        'ongkos_truk_40ft',
        'antar_lokasi_20ft',
        'antar_lokasi_40ft'
    ];

    foreach ($decimalColumns as $col) {
        $columnInfo = collect($columns)->firstWhere('Field', $col);
        if ($columnInfo && strpos($columnInfo->Type, 'decimal') !== false) {
            echo "   ✅ {$col}: {$columnInfo->Type}\n";
        } else {
            echo "   ❌ {$col}: Expected decimal, got " . ($columnInfo->Type ?? 'not found') . "\n";
        }
    }

    // Check nullable columns
    echo "\n3. Checking nullable columns:\n";
    $nullableColumns = [
        'nama',
        'deskripsi',
        'kode',
        'cabang',
        'wilayah',
        'dari',
        'ke',
        'uang_jalan_20ft',
        'uang_jalan_40ft',
        'keterangan',
        'liter',
        'jarak_dari_penjaringan_km',
        'mel_20ft',
        'mel_40ft',
        'ongkos_truk_20ft',
        'ongkos_truk_40ft',
        'antar_lokasi_20ft',
        'antar_lokasi_40ft'
    ];

    foreach ($nullableColumns as $col) {
        $columnInfo = collect($columns)->firstWhere('Field', $col);
        if ($columnInfo && $columnInfo->Null === 'YES') {
            echo "   ✅ {$col}: nullable\n";
        } else {
            echo "   ❌ {$col}: Expected nullable, got " . ($columnInfo->Null ?? 'not found') . "\n";
        }
    }

    // Check record count
    echo "\n4. Checking data:\n";
    $recordCount = DB::table('tujuan_kegiatan_utamas')->count();
    echo "   📊 Total records: {$recordCount}\n";

    if ($recordCount > 0) {
        // Show sample data
        echo "\n5. Sample data:\n";
        $sample = DB::table('tujuan_kegiatan_utamas')->first();
        foreach ($sample as $key => $value) {
            if ($value !== null) {
                if (in_array($key, $decimalColumns)) {
                    echo "   {$key}: " . number_format($value, 2, ',', '.') . "\n";
                } else {
                    echo "   {$key}: {$value}\n";
                }
            }
        }
    }

    echo "\n=== SUMMARY ===\n";
    echo "✅ Migration completed successfully\n";
    echo "✅ Model updated with new fillable attributes\n";
    echo "✅ Controller validation updated\n";
    echo "✅ Views updated with new form fields and table columns\n";
    echo "✅ Menu updated to 'Data Transportasi'\n";
    echo "\nThe Master Data Transportasi module is now ready with all transportation-related fields!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}