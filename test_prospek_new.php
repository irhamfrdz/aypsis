<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== TEST PROSPEK DATA BARU ===\n";

    // Test query tanda terima
    echo "1. Testing Tanda Terima...\n";
    $ttCount = DB::table('tanda_terimas')->count();
    echo "Total tanda terima: {$ttCount}\n";

    if ($ttCount > 0) {
        $ttSample = DB::table('tanda_terimas as tt')
            ->leftJoin('master_tujuan_kirim as mtk', 'tt.tujuan_pengiriman', '=', 'mtk.nama_tujuan')
            ->select([
                'tt.id',
                'tt.no_kontainer',
                'tt.size',
                'tt.jenis_barang',
                'tt.tujuan_pengiriman',
                'tt.pengirim',
                'mtk.nama_tujuan'
            ])
            ->limit(3)
            ->get();

        foreach ($ttSample as $tt) {
            echo "- ID: {$tt->id}, Kontainer: {$tt->no_kontainer}, Tujuan: {$tt->tujuan_pengiriman}\n";
        }
    }

    echo "\n2. Testing Tanda Terima Tanpa SJ...\n";
    $tttsjCount = DB::table('tanda_terima_tanpa_surat_jalan')->count();
    echo "Total tanda terima tanpa SJ: {$tttsjCount}\n";

    if ($tttsjCount > 0) {
        $tttsjSample = DB::table('tanda_terima_tanpa_surat_jalan as tttsj')
            ->leftJoin('master_tujuan_kirim as mtk', 'tttsj.tujuan_pengiriman', '=', 'mtk.nama_tujuan')
            ->select([
                'tttsj.id',
                'tttsj.no_kontainer',
                'tttsj.size_kontainer',
                'tttsj.jenis_barang',
                'tttsj.tujuan_pengiriman',
                'tttsj.pengirim',
                'mtk.nama_tujuan'
            ])
            ->limit(3)
            ->get();

        foreach ($tttsjSample as $tttsj) {
            echo "- ID: {$tttsj->id}, Kontainer: {$tttsj->no_kontainer}, Tujuan: {$tttsj->tujuan_pengiriman}\n";
        }
    }

    echo "\n3. Testing Master Tujuan Kirim...\n";
    $tujuanCount = DB::table('master_tujuan_kirim')->count();
    echo "Total tujuan kirim: {$tujuanCount}\n";

    $totalProspek = $ttCount + $tttsjCount;
    echo "\n=== TOTAL PROSPEK KONTAINER: {$totalProspek} ===\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
