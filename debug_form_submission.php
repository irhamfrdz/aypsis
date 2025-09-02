<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;

// Setup database connection
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_DATABASE'],
    'username' => $_ENV['DB_USERNAME'],
    'password' => $_ENV['DB_PASSWORD'],
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "=== Debug Form Submission ===\n\n";

// Test data yang akan dikirimkan form
$testData = [
    'nomor_pembayaran' => 'BTK1250900001',
    'bank' => 'BCA',
    'jenis_transaksi' => 'Debit',
    'tanggal_kas' => '2025-09-02',
    'tanggal_pembayaran' => '2025-09-02',
    'total_pembayaran' => 35449.53,
    'total_tagihan_penyesuaian' => -1000,
    'total_tagihan_setelah_penyesuaian' => 34449.53,
    'alasan_penyesuaian' => 'Test adjustment',
    'keterangan' => 'Test payment',
    'status' => 'pending',
    'dibuat_oleh' => 1
];

echo "Test Data:\n";
foreach ($testData as $key => $value) {
    echo "- $key: $value\n";
}
echo "\n";

try {
    echo "Attempting to insert into pembayaran_pranota_kontainer...\n";

    $result = DB::table('pembayaran_pranota_kontainer')->insert($testData);

    if ($result) {
        echo "✅ Insert successful!\n";

        // Get the last inserted record
        $lastRecord = DB::table('pembayaran_pranota_kontainer')
            ->where('nomor_pembayaran', $testData['nomor_pembayaran'])
            ->first();

        echo "Inserted record ID: " . $lastRecord->id . "\n";

        // Clean up test data
        DB::table('pembayaran_pranota_kontainer')
            ->where('nomor_pembayaran', $testData['nomor_pembayaran'])
            ->delete();
        echo "✅ Test data cleaned up\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";

    // Show detailed error information
    if (method_exists($e, 'getBindings')) {
        echo "Bindings: " . print_r($e->getBindings(), true) . "\n";
    }
}

echo "\n=== Debug Complete ===\n";
