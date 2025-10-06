<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PembayaranAktivitasLainnya;
use App\Models\Coa;

echo "=== TEST NOMOR PEMBAYARAN GENERATION ===\n\n";

// Get a bank COA for testing
$bankCoa = Coa::where('tipe_akun', 'Kas/Bank')->first();

if (!$bankCoa) {
    echo "❌ No bank COA found\n";
    exit(1);
}

echo "✅ Using COA for testing:\n";
echo "   - ID: {$bankCoa->id}\n";
echo "   - Nomor Akun: {$bankCoa->nomor_akun}\n";
echo "   - Nama Akun: {$bankCoa->nama_akun}\n";
echo "   - Tipe Akun: {$bankCoa->tipe_akun}\n\n";

try {
    $nomorPembayaran = PembayaranAktivitasLainnya::generateNomorPembayaranCoa($bankCoa->id);
    echo "✅ Nomor pembayaran generated successfully!\n";
    echo "   - Nomor Pembayaran: {$nomorPembayaran}\n\n";

    // Breakdown format
    $parts = explode('-', $nomorPembayaran);
    echo "Format breakdown:\n";
    echo "   - Bank Code: {$parts[0]}\n";
    echo "   - Month: {$parts[1]}\n";
    echo "   - Year: {$parts[2]}\n";
    echo "   - Running Number: {$parts[3]}\n\n";

    // Check master nomor terakhir update
    $nomorTerakhir = \App\Models\NomorTerakhir::where('modul', 'pembayaran_aktivitas_lainnya')->first();
    echo "✅ Master nomor terakhir updated:\n";
    echo "   - Nomor terakhir: {$nomorTerakhir->nomor_terakhir}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
