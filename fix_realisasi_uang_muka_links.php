<?php

/**
 * Script untuk menghubungkan data realisasi uang muka yang sudah ada dengan pembayaran uang muka
 * Menjalankan script ini akan mengupdate pembayaran_uang_muka_id yang kosong
 */

require_once __DIR__ . '/../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../bootstrap/app.php';
$app->make('Illuminate\Contracts\Http\Kernel')->bootstrap();

use App\Models\RealisasiUangMuka;
use App\Models\PembayaranUangMuka;

echo "=== Script Update Pembayaran Uang Muka ID ===" . PHP_EOL;

// Ambil semua realisasi yang belum memiliki pembayaran_uang_muka_id tapi memiliki dp_amount
$realisasiList = RealisasiUangMuka::whereNull('pembayaran_uang_muka_id')
    ->where('dp_amount', '>', 0)
    ->get();

echo "Ditemukan " . $realisasiList->count() . " realisasi yang perlu diupdate." . PHP_EOL . PHP_EOL;

$updatedCount = 0;

foreach ($realisasiList as $realisasi) {
    echo "Processing Realisasi ID: {$realisasi->id} - {$realisasi->nomor_pembayaran}" . PHP_EOL;
    echo "  DP Amount: " . number_format($realisasi->dp_amount, 0, ',', '.') . PHP_EOL;
    echo "  Supir IDs: " . json_encode($realisasi->supir_ids) . PHP_EOL;

    // Cari uang muka yang cocok
    $uangMukaData = null;

    // Strategy 1: Cari berdasarkan amount dan supir_ids yang sama
    if (!empty($realisasi->supir_ids) && is_array($realisasi->supir_ids)) {
        $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
            ->where('jenis_transaksi', 'uang_muka')
            ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
            ->where(function($query) use ($realisasi) {
                foreach ($realisasi->supir_ids as $supirId) {
                    $query->orWhereJsonContains('supir_ids', (int)$supirId);
                }
            })
            ->orderBy('tanggal_pembayaran', 'desc')
            ->first();
    }

    // Strategy 2: Cari berdasarkan amount dan kegiatan yang sama
    if (!$uangMukaData && $realisasi->kegiatan) {
        $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
            ->where('jenis_transaksi', 'uang_muka')
            ->where('kegiatan', $realisasi->kegiatan)
            ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
            ->orderBy('tanggal_pembayaran', 'desc')
            ->first();
    }

    // Strategy 3: Cari berdasarkan amount saja, tanggal terdekat
    if (!$uangMukaData) {
        $uangMukaData = PembayaranUangMuka::where('total_pembayaran', $realisasi->dp_amount)
            ->where('jenis_transaksi', 'uang_muka')
            ->whereIn('status', ['approved', 'pending', 'uang_muka_belum_terpakai', 'uang_muka_terpakai'])
            ->where('tanggal_pembayaran', '<=', $realisasi->tanggal_pembayaran)
            ->orderBy('tanggal_pembayaran', 'desc')
            ->first();
    }

    if ($uangMukaData) {
        // Update realisasi dengan pembayaran_uang_muka_id
        $realisasi->update(['pembayaran_uang_muka_id' => $uangMukaData->id]);
        echo "  ✅ Berhasil dilink dengan Uang Muka: {$uangMukaData->nomor_pembayaran}" . PHP_EOL;
        $updatedCount++;
    } else {
        echo "  ❌ Tidak ditemukan uang muka yang cocok" . PHP_EOL;
    }

    echo PHP_EOL;
}

echo "=== SELESAI ===" . PHP_EOL;
echo "Total realisasi yang berhasil diupdate: {$updatedCount}" . PHP_EOL;
echo "Sekarang coba refresh halaman print untuk melihat hasilnya." . PHP_EOL;
