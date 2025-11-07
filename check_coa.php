<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Coa;

echo "=== Cek Akun COA untuk Uang Jalan ===\n";

// Cek akun Biaya Uang Jalan Muat
$biayaUangJalan = Coa::where('nama_akun', 'Biaya Uang Jalan Muat')->first();
if ($biayaUangJalan) {
    echo "✅ FOUND: Biaya Uang Jalan Muat\n";
    echo "   Kode: {$biayaUangJalan->kode_nomor}\n";
    echo "   Saldo: Rp " . number_format($biayaUangJalan->saldo, 0, ',', '.') . "\n";
    echo "   Tipe: {$biayaUangJalan->tipe_akun}\n\n";
} else {
    echo "❌ NOT FOUND: Biaya Uang Jalan Muat\n\n";
}

// Cari semua akun yang mengandung "uang jalan"
echo "=== Semua Akun Uang Jalan ===\n";
$uangJalanAccounts = Coa::where('nama_akun', 'LIKE', '%uang jalan%')
                        ->orWhere('nama_akun', 'LIKE', '%Uang Jalan%')
                        ->get(['nama_akun', 'kode_nomor', 'saldo', 'tipe_akun']);

if ($uangJalanAccounts->count() > 0) {
    foreach ($uangJalanAccounts as $akun) {
        echo "- {$akun->nama_akun}\n";
        echo "  Kode: {$akun->kode_nomor}\n";
        echo "  Saldo: Rp " . number_format($akun->saldo, 0, ',', '.') . "\n";
        echo "  Tipe: {$akun->tipe_akun}\n\n";
    }
} else {
    echo "Tidak ada akun yang mengandung 'uang jalan'\n";
}

// Cek akun bank untuk referensi
echo "=== Akun Bank (untuk referensi) ===\n";
$bankAccounts = Coa::where('tipe_akun', 'LIKE', '%bank%')
                   ->orWhere('nama_akun', 'LIKE', '%bank%')
                   ->orWhere('nama_akun', 'LIKE', '%kas%')
                   ->take(5)
                   ->get(['nama_akun', 'kode_nomor']);

foreach ($bankAccounts as $bank) {
    echo "- {$bank->nama_akun} ({$bank->kode_nomor})\n";
}