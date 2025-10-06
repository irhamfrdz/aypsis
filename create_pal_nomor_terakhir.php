<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

use App\Models\NomorTerakhir;

echo "Checking & Creating pembayaran_aktivitas_lainnya module in nomor_terakhir...\n";
echo "=" . str_repeat("=", 70) . "\n";

try {
    // Check if module exists
    $existing = NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya')->first();

    if ($existing) {
        echo "✓ Module already exists!\n";
        echo "  - Modul: {$existing->modul}\n";
        echo "  - Current Number: {$existing->nomor_terakhir}\n";
        echo "  - Prefix: {$existing->prefix}\n";
    } else {
        echo "❌ Module NOT FOUND. Creating...\n";

        $nomorTerakhir = NomorTerakhir::create([
            'modul' => 'pembayaran_aktivitas_lainnya',
            'nomor_terakhir' => 0,
            'prefix' => 'PAL',
            'keterangan' => 'Nomor pembayaran aktivitas lainnya'
        ]);

        echo "✅ Module created successfully!\n";
        echo "  - Modul: {$nomorTerakhir->modul}\n";
        echo "  - Initial Number: {$nomorTerakhir->nomor_terakhir}\n";
        echo "  - Prefix: {$nomorTerakhir->prefix}\n";
    }

    echo "\n🎯 Now the generate-nomor-preview should work!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
