<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use Illuminate\Support\Facades\DB;

echo "=== DEBUG IMPORT ISSUE ===\n\n";

// Check if we have any data in the table
$count = DaftarTagihanKontainerSewa::count();
echo "Total records in database: {$count}\n\n";

// Check the last 5 records
echo "Last 5 records:\n";
$lastRecords = DaftarTagihanKontainerSewa::orderBy('id', 'desc')->take(5)->get();
foreach ($lastRecords as $record) {
    echo "  ID: {$record->id} | Kontainer: {$record->nomor_kontainer} | Vendor: {$record->vendor} | Created: {$record->created_at}\n";
}

echo "\n";

// Test creating a single record manually
echo "Testing manual creation...\n";
try {
    $testData = [
        'vendor' => 'DPE',
        'nomor_kontainer' => 'TEST123456',
        'size' => '20',
        'tanggal_awal' => '2025-01-01',
        'tanggal_akhir' => '2025-01-31',
        'tarif' => 'Bulanan',
        'periode' => 31,
        'masa' => 'Periode 1',
        'group' => 'TEST',
        'status' => 'ongoing',
        'dpp' => 775000,
        'adjustment' => 0,
        'dpp_nilai_lain' => 0,
        'ppn' => 85250,
        'pph' => 15500,
        'grand_total' => 844750,
    ];

    $created = DaftarTagihanKontainerSewa::create($testData);
    echo "✓ Successfully created test record ID: {$created->id}\n";

    // Delete the test record
    $created->delete();
    echo "✓ Test record deleted\n";

} catch (\Exception $e) {
    echo "✗ Failed to create test record: " . $e->getMessage() . "\n";
}

echo "\n=== END DEBUG ===\n";
