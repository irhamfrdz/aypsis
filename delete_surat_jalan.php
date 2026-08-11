<?php

use Illuminate\Support\Facades\DB;
use App\Models\SuratJalanTarikKosongBatam;
use App\Models\HistoryKontainer;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    DB::beginTransaction();

    // 1. Ambil nomor surat jalan yang akan dihapus untuk mencari referensinya (opsional)
    // Tapi karena kita tahu history nya berdasarkan tanggal dan jenis kegiatan, 
    // kita bisa hapus berdasarkan rentang tanggal.

    $startDate = '2026-07-11';
    $endDate = '2026-07-24';

    echo "Memulai proses penghapusan data...\n";

    // 2. Hapus History Pergerakan Kontainer
    $deletedHistory = HistoryKontainer::where('jenis_kegiatan', 'Tarik Kosong Batam')
        ->whereBetween('tanggal_kegiatan', [$startDate, $endDate])
        ->delete();

    echo "- Berhasil menghapus {$deletedHistory} data history pergerakan kontainer.\n";

    // 3. Hapus Surat Jalan Tarik Kosong Batam
    $deletedSuratJalan = SuratJalanTarikKosongBatam::whereBetween('tanggal_surat_jalan', [$startDate, $endDate])
        ->delete();

    echo "- Berhasil menghapus {$deletedSuratJalan} data Surat Jalan.\n";

    DB::commit();
    echo "Proses selesai dengan sukses!\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "Gagal menghapus data: " . $e->getMessage() . "\n";
}
