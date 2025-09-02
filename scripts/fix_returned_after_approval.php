<?php
require __DIR__ . '/../vendor/autoload.php';

use App\Models\Permohonan;
use Illuminate\Support\Facades\Log;

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = Permohonan::where('status', 'Selesai')
    ->whereIn('vendor_perusahaan', ['ZONA','DPE','SOC'])
    ->with(['kontainers', 'checkpoints'])
    ->get();

$out = [];
foreach ($items as $p) {
    $kegiatanName = \App\Models\MasterKegiatan::where('kode_kegiatan', $p->kegiatan)->value('nama_kegiatan') ?? ($p->kegiatan ?? '');
    $kegiatanLower = strtolower($kegiatanName);
    $isReturnSewa = (stripos($kegiatanLower, 'tarik') !== false && stripos($kegiatanLower, 'sewa') !== false)
        || (stripos($kegiatanLower, 'pengambilan') !== false)
        || ($kegiatanLower === 'pengambilan');

    if (!$isReturnSewa) continue;

    $latestCheckpoint = $p->checkpoints->max('tanggal_checkpoint');
    $doneDate = $latestCheckpoint ?: now()->format('Y-m-d');

    $updated = [];
    foreach ($p->kontainers as $k) {
        $k->tanggal_selesai_sewa = $doneDate;
        $k->status = 'dikembalikan';
        $k->save();
        $updated[] = ['id' => $k->id, 'serial' => $k->nomor_seri_gabungan ?? null];
    }

    if (!empty($updated)) {
        $out[] = ['memo' => $p->nomor_memo, 'updated' => $updated];
    }
}

echo json_encode($out, JSON_PRETTY_PRINT);
