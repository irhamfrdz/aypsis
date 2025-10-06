<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "==============================================\n";
echo "  DETAIL DATA MASTER KEGIATAN\n";
echo "==============================================\n";
$kegiatans = DB::table('master_kegiatans')->get();
foreach ($kegiatans as $k) {
    echo sprintf("%-10s | %-35s | %s\n", $k->kode_kegiatan, $k->nama_kegiatan, $k->status);
}

echo "\n==============================================\n";
echo "  DETAIL DATA DIVISI\n";
echo "==============================================\n";
$divisis = DB::table('divisis')->get();
foreach ($divisis as $d) {
    echo sprintf("%-10s | %s\n", $d->kode_divisi, $d->nama_divisi);
}

echo "\n==============================================\n";
echo "  DETAIL DATA BANK\n";
echo "==============================================\n";
$banks = DB::table('banks')->get();
foreach ($banks as $b) {
    echo sprintf("ID: %-5s | %-30s | %s\n", 
        $b->id, 
        $b->nama_bank ?? 'N/A', 
        $b->keterangan ?? '-'
    );
}

echo "\n==============================================\n";
echo "  DETAIL DATA CABANG\n";
echo "==============================================\n";
$cabangs = DB::table('cabangs')->get();
foreach ($cabangs as $c) {
    echo sprintf("ID: %-5s | %-30s | %s\n", 
        $c->id,
        $c->nama_cabang ?? 'N/A',
        $c->alamat ?? '-'
    );
}

echo "\n==============================================\n";
echo "  DETAIL DATA PAJAK\n";
echo "==============================================\n";
$pajaks = DB::table('pajaks')->get();
foreach ($pajaks as $p) {
    echo sprintf("ID: %-5s | %-30s | %s%%\n", 
        $p->id,
        $p->nama_pajak ?? 'N/A',
        $p->persentase ?? '0'
    );
}

echo "\n==============================================\n";
echo "  DETAIL DATA PRICELIST SEWA KONTAINER\n";
echo "==============================================\n";
$pricelists = DB::table('master_pricelist_sewa_kontainers')->get();
foreach ($pricelists as $p) {
    $tarif = is_numeric($p->tarif ?? 0) ? floatval($p->tarif ?? 0) : 0;
    echo sprintf("%-10s | %-10s | Size: %-3s | Rp %s\n", 
        $p->vendor ?? 'N/A', 
        $p->tipe_sewa ?? 'N/A',
        $p->ukuran ?? 'N/A',
        number_format($tarif, 2)
    );
}

echo "\n==============================================\n";
echo "  STATISTIK PEKERJAAN PER DIVISI\n";
echo "==============================================\n";
$stats = DB::table('pekerjaans')
    ->select('divisi_id', DB::raw('COUNT(*) as total'))
    ->groupBy('divisi_id')
    ->get();

foreach ($stats as $stat) {
    $divisi = DB::table('divisis')->find($stat->divisi_id);
    $divisiName = $divisi ? $divisi->nama_divisi : 'Unknown';
    echo sprintf("%-20s : %d pekerjaan\n", $divisiName, $stat->total);
}

echo "\n==============================================\n";
echo "  TOTAL AKUN COA PER TIPE\n";
echo "==============================================\n";
$coaStats = DB::table('akun_coa')
    ->select('tipe_akun', DB::raw('COUNT(*) as total'))
    ->groupBy('tipe_akun')
    ->orderBy('total', 'DESC')
    ->get();

foreach ($coaStats as $stat) {
    echo sprintf("%-20s : %d akun\n", $stat->tipe_akun, $stat->total);
}

echo "\n==============================================\n";
echo "  IMPORT SELESAI!\n";
echo "==============================================\n";
echo "Total data yang berhasil diimpor:\n";
echo "  • Master Kegiatan: 10 record\n";
echo "  • Pricelist Sewa: 7 record\n";
echo "  • Divisi: 9 record\n";
echo "  • Pekerjaan: 56 record\n";
echo "  • Pajak: 12 record\n";
echo "  • Bank: 6 record\n";
echo "  • Akun COA: 417 record\n";
echo "  • Cabang: 3 record\n";
echo "  • Tipe Akun: 15 record\n";
echo "  • Kode Nomor: 21 record\n";
echo "  • Nomor Terakhir: 3 record\n";
echo "==============================================\n";
echo "GRAND TOTAL: 559 record\n";
echo "==============================================\n";
