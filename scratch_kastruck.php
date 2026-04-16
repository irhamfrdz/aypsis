<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// simulate what KasTruckController does
$referensiList = ['PAL/2026/04/0033', 'PAL/2026/04/0032'];
$nomorAccurateMap = [];

$tablesWithNomor = [
    'pembayaran_aktivitas_lains',
    'pembayaran_invoice_aktivitas_lain',
];
foreach ($tablesWithNomor as $tbl) {
    try {
        $rows = \DB::table($tbl)
            ->whereIn('nomor', $referensiList)
            ->whereNotNull('nomor_accurate')
            ->where('nomor_accurate', '!=', '')
            ->pluck('nomor_accurate', 'nomor');
        foreach ($rows as $key => $accurate) {
            if (!isset($nomorAccurateMap[$key])) {
                echo "Mapping from $tbl: $key => $accurate\n";
                $nomorAccurateMap[$key] = $accurate;
            }
        }
    } catch (\Exception $e) {
        
    }
}

$tablesWithNomorPembayaran = [
    'pembayaran_pranota_uang_jalans',
    'pembayaran_pranota_uang_jalan_batams',
    'pembayaran_pranota_obs',
    'pembayaran_obs',
    'pembayaran_pranota_kontainer',
    'pembayaran_pranota_vendor_supirs',
    'pembayaran_dp_obs',
    'pembayaran_biaya_kapals',
];
foreach ($tablesWithNomorPembayaran as $tbl) {
    try {
        $rows = \DB::table($tbl)
            ->whereIn('nomor_pembayaran', $referensiList)
            ->whereNotNull('nomor_accurate')
            ->where('nomor_accurate', '!=', '')
            ->pluck('nomor_accurate', 'nomor_pembayaran');
        foreach ($rows as $key => $accurate) {
            if (!isset($nomorAccurateMap[$key])) {
                echo "Mapping from $tbl: $key => $accurate\n";
                $nomorAccurateMap[$key] = $accurate;
            }
        }
    } catch (\Exception $e) {
        
    }
}

echo "FINAL MAP: " . json_encode($nomorAccurateMap) . "\n";
