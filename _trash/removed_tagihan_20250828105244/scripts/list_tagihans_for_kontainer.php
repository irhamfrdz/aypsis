<?php
// list tagihan rows for a given kontainer id (or serial) for debugging
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\DB;

$arg = $argv[1] ?? null;
if (!$arg) {
    echo "Usage: php list_tagihans_for_kontainer.php <kontainer_id_or_serial>\n";
    exit(1);
}

// try numeric id first
if (is_numeric($arg)) {
    $kontainerId = (int)$arg;
    $rows = DB::table('tagihan_kontainer_sewa_kontainers as tkk')
        ->join('tagihan_kontainer_sewa as tk', 'tkk.tagihan_id', '=', 'tk.id')
        ->where('tkk.kontainer_id', $kontainerId)
        ->orderBy('tk.id', 'desc')
    ->select('tk.id','tk.vendor','tk.tarif','tk.harga','tk.tanggal_harga_awal','tk.periode','tk.group_code','tk.status_pembayaran','tk.keterangan')
        ->get();
    echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
    exit(0);
}

// otherwise try serial lookup
$serial = trim($arg);
$kont = DB::table('kontainers')
    ->where('nomor_seri_gabungan', $serial)
    ->orWhere(DB::raw("CONCAT(awalan_kontainer, nomor_seri_kontainer, akhiran_kontainer)"), $serial)
    ->first();
if (!$kont) {
    echo "No kontainer found for serial {$serial}\n";
    exit(2);
}
$kontainerId = $kont->id;
$rows = DB::table('tagihan_kontainer_sewa_kontainers as tkk')
    ->join('tagihan_kontainer_sewa as tk', 'tkk.tagihan_id', '=', 'tk.id')
    ->where('tkk.kontainer_id', $kontainerId)
    ->orderBy('tk.id', 'desc')
    ->select('tk.id','tk.vendor','tk.tarif','tk.harga','tk.tanggal_harga_awal','tk.periode','tk.group_code','tk.status_pembayaran','tk.keterangan')
    ->get();
echo json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
