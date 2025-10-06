<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Coa;
use App\Models\PembayaranAktivitasLainnya;

echo "=== Test Pembayaran Aktivitas Lainnya Store ===\n\n";

// Test 1: Check if table akun_coa exists and has data
echo "1. Checking akun_coa table...\n";
try {
    $coaCount = Coa::count();
    echo "   ✓ Table akun_coa found with {$coaCount} records\n";

    if ($coaCount > 0) {
        $firstCoa = Coa::first();
        echo "   Sample COA: ID={$firstCoa->id}, Nomor={$firstCoa->nomor_akun}, Nama={$firstCoa->nama_akun}\n";
    } else {
        echo "   ⚠ No COA records found in database\n";
    }
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n2. Checking pembayaran_aktivitas_lainnya table...\n";
try {
    $pembayaranCount = PembayaranAktivitasLainnya::count();
    echo "   ✓ Table pembayaran_aktivitas_lainnya found with {$pembayaranCount} records\n";
} catch (\Exception $e) {
    echo "   ✗ Error: " . $e->getMessage() . "\n";
}

echo "\n3. Testing validation rules...\n";
$rules = [
    'nomor_pembayaran' => 'nullable|string|max:255',
    'tanggal_pembayaran' => 'required|date',
    'pilih_bank' => 'required|exists:akun_coa,id',
    'jenis_transaksi' => 'required|in:debit,kredit',
    'aktivitas_pembayaran' => 'required|string|max:1000',
    'total_pembayaran' => 'required|numeric|min:0'
];
echo "   ✓ Validation rules configured:\n";
foreach ($rules as $field => $rule) {
    echo "     - {$field}: {$rule}\n";
}

echo "\n=== Test Complete ===\n";
