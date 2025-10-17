<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "╔══════════════════════════════════════════════════════════════════════════════╗\n";
echo "║ TEST CREATE TANDA TERIMA MANUAL                                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════════════════╝\n\n";

// Cek master data yang diperlukan
echo "📋 CEK MASTER DATA:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$masterKapals = DB::table('master_kapals')
    ->where('status', 'aktif')
    ->orderBy('nama_kapal')
    ->get(['id', 'nama_kapal', 'nickname']);

echo "Master Kapal: " . $masterKapals->count() . " kapal aktif\n";
if ($masterKapals->count() > 0) {
    foreach ($masterKapals->take(5) as $kapal) {
        $nickname = $kapal->nickname ? " ({$kapal->nickname})" : '';
        echo "  - {$kapal->nama_kapal}{$nickname}\n";
    }
}

$masterKegiatans = DB::table('master_kegiatans')
    ->where('status', 'aktif')
    ->orderBy('nama_kegiatan')
    ->get(['id', 'kode_kegiatan', 'nama_kegiatan']);

echo "\nMaster Kegiatan: " . $masterKegiatans->count() . " kegiatan aktif\n";
if ($masterKegiatans->count() > 0) {
    foreach ($masterKegiatans->take(5) as $kegiatan) {
        echo "  - {$kegiatan->nama_kegiatan} ({$kegiatan->kode_kegiatan})\n";
    }
}

echo "────────────────────────────────────────────────────────────────────────────────\n\n";

// Simulasi create tanda terima manual
echo "📝 SIMULASI CREATE TANDA TERIMA MANUAL:\n";
echo "────────────────────────────────────────────────────────────────────────────────\n";

$kapalPertama = $masterKapals->first();
$kegiatanPertama = $masterKegiatans->first();

$dataTandaTerima = [
    'surat_jalan_id' => null, // Manual, tidak ada surat jalan
    'no_surat_jalan' => 'SJ-MANUAL-' . date('YmdHis'),
    'tanggal_surat_jalan' => date('Y-m-d'),
    'supir' => 'BUDI SANTOSO',
    'kegiatan' => $kegiatanPertama ? $kegiatanPertama->kode_kegiatan : 'ANTAR ISI',
    'size' => '40',
    'jumlah_kontainer' => 2,
    'no_kontainer' => 'AYPU0099991, AYPU0099992',
    'no_seal' => 'SEAL123456',
    'tujuan_pengiriman' => 'Pelabuhan Tanjung Priok',
    'pengirim' => 'PT. Test Indonesia',
    'estimasi_nama_kapal' => $kapalPertama ? $kapalPertama->nama_kapal : 'Test Ship',
    'tanggal_ambil_kontainer' => date('Y-m-d'),
    'tanggal_terima_pelabuhan' => date('Y-m-d', strtotime('+1 day')),
    'tanggal_garasi' => null,
    'jumlah' => 1000,
    'satuan' => 'Kg',
    'berat_kotor' => 25000.50,
    'dimensi' => '20x10x5 m',
    'catatan' => null,
    'status' => 'draft',
    'created_by' => 1,
    'created_at' => now(),
    'updated_at' => now(),
];

echo "Data yang akan dibuat:\n";
foreach ($dataTandaTerima as $key => $value) {
    if ($value !== null) {
        echo "  - $key: $value\n";
    }
}

echo "\n💾 Menyimpan data...\n";

DB::beginTransaction();
try {
    $id = DB::table('tanda_terimas')->insertGetId($dataTandaTerima);

    DB::commit();

    echo "✅ Tanda Terima berhasil dibuat!\n";
    echo "   ID: $id\n";
    echo "   No. Surat Jalan: {$dataTandaTerima['no_surat_jalan']}\n\n";

    // Verifikasi data
    $tandaTerima = DB::table('tanda_terimas')->where('id', $id)->first();

    echo "📋 VERIFIKASI DATA:\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n";
    echo "ID                      : {$tandaTerima->id}\n";
    echo "Surat Jalan ID          : " . ($tandaTerima->surat_jalan_id ?? 'NULL (Manual)') . "\n";
    echo "No. Surat Jalan         : {$tandaTerima->no_surat_jalan}\n";
    echo "Tanggal                 : {$tandaTerima->tanggal_surat_jalan}\n";
    echo "Supir                   : {$tandaTerima->supir}\n";
    echo "Kegiatan                : {$tandaTerima->kegiatan}\n";
    echo "No. Kontainer           : {$tandaTerima->no_kontainer}\n";
    echo "Jumlah Kontainer        : {$tandaTerima->jumlah_kontainer}\n";
    echo "Estimasi Kapal          : {$tandaTerima->estimasi_nama_kapal}\n";
    echo "Status                  : {$tandaTerima->status}\n";
    echo "Created At              : {$tandaTerima->created_at}\n";
    echo "────────────────────────────────────────────────────────────────────────────────\n\n";

    echo "🎉 TEST BERHASIL!\n";
    echo "Tanda terima manual dapat dibuat tanpa surat_jalan_id.\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
}
