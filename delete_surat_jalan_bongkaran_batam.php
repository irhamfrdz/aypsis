<?php

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalanBongkaranBatam;
use App\Models\HistoryKontainer;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::beginTransaction();

    // Rentang tanggal dari 11 Juli sampai 24 Juli 2026
    $startDate = '2026-07-11';
    $endDate = '2026-07-24';

    echo "Memulai proses penghapusan data Surat Jalan Bongkaran Batam...\n";

    // 1. Hapus History Pergerakan Kontainer
    // History untuk bongkaran batam menggunakan keterangan yang mengandung "SJ Bongkaran Batam"
    $deletedHistory = HistoryKontainer::where('keterangan', 'like', '%SJ Bongkaran Batam%')
        ->whereBetween('tanggal_kegiatan', [$startDate, $endDate])
        ->delete();

    echo "- Berhasil menghapus {$deletedHistory} data history pergerakan kontainer.\n";

    // 2. Hapus Surat Jalan Bongkaran Batam
    $deletedSuratJalan = SuratJalanBongkaranBatam::whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
        ->delete();

    echo "- Berhasil menghapus {$deletedSuratJalan} data Surat Jalan Bongkaran Batam.\n";

    DB::commit();
    echo "Proses selesai dengan sukses!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Gagal menghapus data: " . $e->getMessage() . "\n";
}
