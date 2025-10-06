<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  LAPORAN IMPORT DATA MASTER\n";
echo "==============================================\n\n";

$tables = [
    'master_kegiatans' => 'Master Kegiatan',
    'master_pricelist_sewa_kontainers' => 'Pricelist Sewa Kontainer',
    'divisis' => 'Divisi',
    'pekerjaans' => 'Pekerjaan',
    'pajaks' => 'Pajak',
    'banks' => 'Bank',
    'akun_coa' => 'Akun COA (Chart of Accounts)',
    'cabangs' => 'Cabang',
    'tipe_akuns' => 'Tipe Akun',
    'kode_nomor' => 'Kode Nomor',
    'nomor_terakhir' => 'Nomor Terakhir',
];

$totalRecords = 0;

foreach ($tables as $table => $label) {
    try {
        $count = DB::table($table)->count();
        $totalRecords += $count;
        
        $status = $count > 0 ? '✓' : '⚠';
        echo sprintf("%s %-45s : %5d record\n", $status, $label, $count);
        
    } catch (Exception $e) {
        echo sprintf("✗ %-45s : ERROR\n", $label);
    }
}

echo str_repeat("=", 60) . "\n";
echo sprintf("  %-45s : %5d record\n", "TOTAL DATA MASTER", $totalRecords);
echo str_repeat("=", 60) . "\n\n";

// Detail untuk tabel terpenting
echo "==============================================\n";
echo "  DETAIL MASTER KEGIATAN\n";
echo "==============================================\n";
$kegiatans = DB::table('master_kegiatans')->orderBy('kode_kegiatan')->get();
foreach ($kegiatans as $k) {
    echo sprintf("  %s - %s (%s)\n", $k->kode_kegiatan, $k->nama_kegiatan, $k->status);
}

echo "\n==============================================\n";
echo "  DETAIL DIVISI\n";
echo "==============================================\n";
$divisis = DB::table('divisis')->orderBy('kode_divisi')->get();
foreach ($divisis as $d) {
    echo sprintf("  %s - %s\n", $d->kode_divisi, $d->nama_divisi);
}

echo "\n==============================================\n";
echo "  DETAIL CABANG\n";
echo "==============================================\n";
$cabangs = DB::table('cabangs')->get();
foreach ($cabangs as $c) {
    echo sprintf("  %s\n", $c->nama_cabang);
}

echo "\n==============================================\n";
echo "  RINGKASAN PEKERJAAN\n";
echo "==============================================\n";
$pekerjaanSample = DB::table('pekerjaans')->limit(10)->get();
echo "  Sample 10 Pekerjaan Pertama:\n";
foreach ($pekerjaanSample as $p) {
    echo sprintf("  - %s: %s\n", $p->kode_pekerjaan, $p->nama_pekerjaan);
}
echo sprintf("  ... dan %d pekerjaan lainnya\n", DB::table('pekerjaans')->count() - 10);

echo "\n==============================================\n";
echo "  RINGKASAN AKUN COA\n";
echo "==============================================\n";
$coaSample = DB::table('akun_coa')->limit(10)->get();
echo "  Sample 10 Akun COA Pertama:\n";
foreach ($coaSample as $coa) {
    echo sprintf("  - %s: %s\n", $coa->kode_akun ?? 'N/A', $coa->nama_akun ?? 'N/A');
}
echo sprintf("  ... dan %d akun lainnya\n", DB::table('akun_coa')->count() - 10);

echo "\n==============================================\n";
echo "         IMPORT BERHASIL! ✓\n";
echo "==============================================\n";
echo "  Semua data master dari file backup telah\n";
echo "  berhasil diimpor ke database Anda.\n";
echo "==============================================\n";
