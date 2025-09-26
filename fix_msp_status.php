<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = \App\Models\Permohonan::where('nomor_memo', 'MSP10925055665')->first();

if($p) {
    $oldStatus = $p->status;
    $p->status = 'Pending';
    $p->save();

    echo 'Status berhasil diubah!' . PHP_EOL;
    echo 'Nomor Memo: ' . $p->nomor_memo . PHP_EOL;
    echo 'Status lama: ' . $oldStatus . PHP_EOL;
    echo 'Status baru: ' . $p->status . PHP_EOL;

    // Verifikasi kriteria setelah perubahan
    echo PHP_EOL . 'Verifikasi kriteria Approval Tugas 2:' . PHP_EOL;
    $criteria1 = !in_array($p->status, ['Selesai', 'Dibatalkan']);
    $criteria2 = $p->approved_by_system_1 == true;
    $criteria3 = $p->approved_by_system_2 == false;

    echo '1. Status bukan Selesai/Dibatalkan: ' . ($criteria1 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . PHP_EOL;
    echo '2. Sudah disetujui System 1: ' . ($criteria2 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . PHP_EOL;
    echo '3. Belum disetujui System 2: ' . ($criteria3 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . PHP_EOL;

    $allCriteriaMet = $criteria1 && $criteria2 && $criteria3;
    echo PHP_EOL . 'Sekarang data ' . ($allCriteriaMet ? 'AKAN MUNCUL' : 'TIDAK AKAN MUNCUL') . ' di Approval Tugas 2' . PHP_EOL;
} else {
    echo 'Data tidak ditemukan' . PHP_EOL;
}
