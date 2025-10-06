<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\NomorTerakhir;

echo "=== CREATE PEMBAYARAN AKTIVITAS LAINNYA MODULE ===\n\n";

try {
    $module = NomorTerakhir::create([
        'modul' => 'pembayaran_aktivitas_lainnya',
        'nomor_terakhir' => 0,
        'keterangan' => 'Nomor terakhir untuk pembayaran aktivitas lain-lain'
    ]);

    echo "✅ Module berhasil dibuat:\n";
    echo "   - Modul: {$module->modul}\n";
    echo "   - Nomor terakhir: {$module->nomor_terakhir}\n";
    echo "   - Keterangan: {$module->keterangan}\n";

} catch (Exception $e) {
    echo "❌ Gagal membuat module: " . $e->getMessage() . "\n";
}
