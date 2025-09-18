<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

// Test creating a sample pranota record
try {
    $result = DB::table('pranota_perbaikan_kontainers')->insert([
        'nomor_pranota' => 'TEST001',
        'perbaikan_kontainer_id' => 1,
        'tanggal_pranota' => '2025-09-18',
        'deskripsi_pekerjaan' => 'Test perbaikan',
        'nama_teknisi' => 'Test Vendor',
        'estimasi_biaya' => 100000,
        'estimasi_waktu' => 0, // Changed from null to 0 since column is not nullable
        'catatan' => 'Test catatan',
        'status' => 'pending',
        'created_by' => 1,
        'updated_by' => 1,
        'created_at' => now(),
        'updated_at' => now()
    ]);
    echo 'Test record inserted successfully: ' . ($result ? 'YES' : 'NO') . PHP_EOL;

    // Check if it was inserted
    $count = DB::table('pranota_perbaikan_kontainers')->where('nomor_pranota', 'TEST001')->count();
    echo 'Test records found: ' . $count . PHP_EOL;

    // Clean up
    DB::table('pranota_perbaikan_kontainers')->where('nomor_pranota', 'TEST001')->delete();
    echo 'Test record cleaned up' . PHP_EOL;

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . PHP_EOL;
}
?>