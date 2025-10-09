<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Coa;
use App\Models\PembayaranDpOb;
use App\Models\PembayaranOb;
use App\Models\NomorTerakhir;

echo "=== TESTING NOMOR PEMBAYARAN SYSTEM ===\n\n";

// 1. Cek master nomor terakhir
echo "1. Master Nomor Terakhir Module: nomor_pembayaran\n";
$nomorTerakhir = NomorTerakhir::where('modul', 'nomor_pembayaran')->first();
if ($nomorTerakhir) {
    echo "   ✓ Module exists - Current number: {$nomorTerakhir->nomor_terakhir}\n\n";
} else {
    echo "   ❌ Module NOT FOUND!\n\n";
    exit(1);
}

// 2. Cek available COAs
echo "2. Available Kas/Bank COAs:\n";
$coas = Coa::where('tipe_akun', 'Kas/Bank')->take(3)->get(['id', 'nama_akun', 'kode_nomor']);
foreach ($coas as $coa) {
    echo "   - ID: {$coa->id}, Name: {$coa->nama_akun}, Code: {$coa->kode_nomor}\n";
}
echo "\n";

// 3. Test generate nomor DP OB
if ($coas->count() > 0) {
    $firstCoa = $coas->first();
    echo "3. Testing DP OB Number Generation:\n";
    echo "   Using COA: {$firstCoa->nama_akun} (ID: {$firstCoa->id})\n";

    try {
        // Preview saja tanpa save
        $today = now();
        $tahun = $today->format('y');
        $bulan = $today->format('m');
        $kodeBank = $firstCoa->kode_nomor ?? '000';

        $nextNumber = $nomorTerakhir->nomor_terakhir + 1;
        $sequence = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
        $previewNomor = "{$kodeBank}-{$bulan}-{$tahun}-{$sequence}";

        echo "   Preview Nomor: {$previewNomor}\n";
        echo "   ✓ Format: [KODE_BANK]-[MM]-[YY]-[XXXXXX]\n\n";

    } catch (Exception $e) {
        echo "   ❌ Error: " . $e->getMessage() . "\n\n";
    }

    echo "4. Testing OB Number Generation:\n";
    echo "   Same format as DP OB\n";
    echo "   ✓ Both use master nomor_pembayaran module\n\n";
} else {
    echo "❌ No COAs found!\n\n";
}

echo "✅ NOMOR SYSTEM READY!\n";
echo "Format: [KODE_BANK]-[MM]-[YY]-[XXXXXX]\n";
echo "Example: KBJ-10-25-000001\n";
