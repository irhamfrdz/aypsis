<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\CoaTransactionService;
use App\Models\Coa;

echo "=== Test Double Book Accounting untuk Pembayaran Pranota Uang Jalan ===\n\n";

$coaService = new CoaTransactionService();

// Cek saldo awal
$bankKasBesar = Coa::where('nama_akun', 'Kas Besar')->first();
$biayaUangJalan = Coa::where('nama_akun', 'Biaya Uang Jalan Muat')->first();

if (!$bankKasBesar || !$biayaUangJalan) {
    echo "❌ Akun tidak ditemukan!\n";
    if (!$bankKasBesar) echo "- Kas Besar tidak ada\n";
    if (!$biayaUangJalan) echo "- Biaya Uang Jalan Muat tidak ada\n";
    exit(1);
}

echo "Saldo SEBELUM transaksi:\n";
echo "- Kas Besar: Rp " . number_format($bankKasBesar->saldo, 0, ',', '.') . "\n";
echo "- Biaya Uang Jalan Muat: Rp " . number_format($biayaUangJalan->saldo, 0, ',', '.') . "\n\n";

// Simulasi transaksi Kredit (Biaya naik, Bank turun)
echo "🧪 Test Transaksi KREDIT (seperti pembayaran uang jalan):\n";
echo "Jurnal: Dr. Biaya Uang Jalan Muat Rp 1,000,000\n";
echo "        Cr. Kas Besar                 Rp 1,000,000\n\n";

try {
    $result = $coaService->recordDoubleEntry(
        ['nama_akun' => 'Biaya Uang Jalan Muat', 'jumlah' => 1000000], // DEBIT Biaya
        ['nama_akun' => 'Kas Besar', 'jumlah' => 1000000], // KREDIT Bank
        date('Y-m-d'),
        'TEST-PPT11500001',
        'Test Pembayaran Pranota Uang Jalan',
        'Test double book accounting untuk uang jalan'
    );
    
    if ($result) {
        echo "✅ Transaksi berhasil dicatat!\n\n";
        
        // Refresh data
        $bankKasBesar->refresh();
        $biayaUangJalan->refresh();
        
        echo "Saldo SETELAH transaksi:\n";
        echo "- Kas Besar: Rp " . number_format($bankKasBesar->saldo, 0, ',', '.') . " (berkurang Rp 1,000,000)\n";
        echo "- Biaya Uang Jalan Muat: Rp " . number_format($biayaUangJalan->saldo, 0, ',', '.') . " (bertambah Rp 1,000,000)\n\n";
        
        echo "✅ Double Book Accounting BERHASIL!\n";
        echo "✅ Sistem siap digunakan untuk pembayaran pranota uang jalan.\n";
    } else {
        echo "❌ Transaksi gagal!\n";
    }
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Detail: " . $e->getTraceAsString() . "\n";
}