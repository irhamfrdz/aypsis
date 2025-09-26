<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p = \App\Models\Permohonan::where('nomor_memo', 'MSP10925055665')->first();

if($p) {
    echo 'ID: ' . $p->id . PHP_EOL;
    echo 'Nomor Memo: ' . $p->nomor_memo . PHP_EOL;
    echo 'Status: ' . $p->status . PHP_EOL;
    echo 'Approved System 1: ' . ($p->approved_by_system_1 ? 'YA' : 'TIDAK') . PHP_EOL;
    echo 'Approved System 2: ' . ($p->approved_by_system_2 ? 'YA' : 'TIDAK') . PHP_EOL;

    // Analisis kriteria
    echo PHP_EOL . 'Analisis kemunculan di Approval Tugas 2:' . PHP_EOL;
    echo '=======================================' . PHP_EOL;

    $criteria1 = !in_array($p->status, ['Selesai', 'Dibatalkan']);
    $criteria2 = $p->approved_by_system_1 == true;
    $criteria3 = $p->approved_by_system_2 == false;

    echo '1. Status bukan Selesai/Dibatalkan: ' . ($criteria1 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . ' (Status: ' . $p->status . ')' . PHP_EOL;
    echo '2. Sudah disetujui System 1: ' . ($criteria2 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . PHP_EOL;
    echo '3. Belum disetujui System 2: ' . ($criteria3 ? '✓ MEMENUHI' : '✗ TIDAK MEMENUHI') . PHP_EOL;

    $allCriteriaMet = $criteria1 && $criteria2 && $criteria3;
    echo PHP_EOL . 'Kesimpulan: ' . ($allCriteriaMet ? '✓ DATA HARUS MUNCUL di Approval Tugas 2' : '✗ DATA TIDAK AKAN MUNCUL di Approval Tugas 2') . PHP_EOL;

    if (!$allCriteriaMet) {
        echo PHP_EOL . 'Alasan tidak muncul:' . PHP_EOL;
        if (!$criteria1) echo '- Status sudah \'' . $p->status . '\', seharusnya masih \'Pending\'' . PHP_EOL;
        if (!$criteria2) echo '- Belum disetujui di Approval Tugas 1' . PHP_EOL;
        if (!$criteria3) echo '- Sudah disetujui di Approval Tugas 2' . PHP_EOL;
    }
} else {
    echo 'Data dengan nomor memo MSP10925055665 tidak ditemukan' . PHP_EOL;
}
