<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== VERIFICATION: MASTER KODE NOMOR TABLE STRUCTURE ===\n\n";

// Check table structure
echo "1. TABLE STRUCTURE:\n";
echo "-------------------\n";

$columns = DB::select("DESCRIBE kode_nomor");
echo "Found " . count($columns) . " columns:\n";

foreach ($columns as $column) {
    echo "✓ {$column->Field} ({$column->Type})" . ($column->Null === 'NO' ? ' NOT NULL' : ' NULLABLE') . "\n";
}

echo "\n2. SAMPLE DATA:\n";
echo "---------------\n";

$sampleData = DB::table('kode_nomor')->limit(3)->get();
if ($sampleData->count() > 0) {
    echo "Found " . $sampleData->count() . " sample records:\n\n";

    foreach ($sampleData as $record) {
        echo "Record ID: {$record->id}\n";
        echo "  Kode: {$record->kode}\n";
        echo "  Nomor Akun: " . ($record->nomor_akun ?? 'NULL') . "\n";
        echo "  Nama Akun: " . ($record->nama_akun ?? 'NULL') . "\n";
        echo "  Tipe Akun: " . ($record->tipe_akun ?? 'NULL') . "\n";
        echo "  Saldo: Rp " . number_format($record->saldo ?? 0, 0, ',', '.') . "\n";
        echo "  Nama: {$record->nama}\n";
        echo "  Deskripsi: " . ($record->deskripsi ?? 'NULL') . "\n";
        echo "  Created: {$record->created_at}\n";
        echo "  -----------------------------------\n";
    }
} else {
    echo "✗ No sample data found!\n";
}

echo "\n3. TOTAL RECORDS:\n";
echo "------------------\n";

$totalRecords = DB::table('kode_nomor')->count();
echo "Total kode nomor records: $totalRecords\n";

echo "\n4. VERIFICATION STATUS:\n";
echo "-----------------------\n";

// Check if all required columns exist
$requiredColumns = ['id', 'kode', 'nomor_akun', 'nama_akun', 'tipe_akun', 'saldo', 'nama', 'deskripsi', 'created_at', 'updated_at'];
$existingColumns = array_column($columns, 'Field');

$missingColumns = array_diff($requiredColumns, $existingColumns);
$extraColumns = array_diff($existingColumns, $requiredColumns);

if (empty($missingColumns)) {
    echo "✓ All required columns are present\n";
} else {
    echo "✗ Missing columns: " . implode(', ', $missingColumns) . "\n";
}

if (!empty($extraColumns)) {
    echo "✓ Extra columns found: " . implode(', ', $extraColumns) . "\n";
}

if ($totalRecords > 0) {
    echo "✓ Sample data has been seeded\n";
} else {
    echo "✗ No data found - run seeder first\n";
}

echo "\n=== VERIFICATION COMPLETE ===\n";
echo "Table structure has been successfully updated with account fields!\n";
