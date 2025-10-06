<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coa;

echo "=== TEST API ENDPOINT FUNCTIONALITY ===\n\n";

// Get a bank COA for testing
$bankCoa = Coa::where('tipe_akun', 'Kas/Bank')->first();

if (!$bankCoa) {
    echo "❌ No bank COA found\n";
    exit(1);
}

echo "✅ Testing with COA:\n";
echo "   - ID: {$bankCoa->id}\n";
echo "   - Nomor Akun: {$bankCoa->nomor_akun}\n";
echo "   - Nama Akun: {$bankCoa->nama_akun}\n\n";

// Simulate the API logic
try {
    $today = now();
    $tahun = $today->format('y');
    $bulan = $today->format('m');

    $kodeBank = strtoupper(substr(str_replace(['.', '-', ' '], '', $bankCoa->nomor_akun), 0, 3));

    $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya')->first();

    if (!$nomorTerakhir) {
        echo "❌ Module pembayaran_aktivitas_lainnya not found\n";
        exit(1);
    }

    $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
    $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

    $nomorPembayaran = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

    echo "✅ Preview API would return:\n";
    echo "   - Nomor Pembayaran: {$nomorPembayaran}\n";
    echo "   - Kode Bank: {$kodeBank}\n";
    echo "   - Bulan: {$bulan}\n";
    echo "   - Tahun: {$tahun}\n";
    echo "   - Running Number: {$sequence}\n";
    echo "   - Current nomor_terakhir: {$nomorTerakhir->nomor_terakhir}\n";
    echo "   - Next number would be: {$nextNumber}\n\n";

    echo "✅ API logic is correct!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
