<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "=== TEST PROSPEK DATA ===\n";

    // Test query yang sama dengan controller (tanpa surat jalan join)
    $kontainers = DB::table('stock_kontainers as sk')
        ->leftJoin('tanda_terimas as tt', 'sk.tanda_terima_id', '=', 'tt.id')
        ->leftJoin('tanda_terima_tanpa_surat_jalan as tttsj', 'sk.tanda_terima_tanpa_surat_jalan_id', '=', 'tttsj.id')
        ->leftJoin('master_tujuan_kirim as mtk', 'sk.tujuan_id', '=', 'mtk.id')
        ->leftJoin('master_kapals as mk', 'sk.kapal_id', '=', 'mk.id')
        ->select([
            'sk.*',
            'tt.no_surat_jalan as tt_no_surat_jalan',
            'tt.tanggal_surat_jalan as tt_tanggal',
            'tt.no_kontainer as tt_no_kontainer',
            'tttsj.nomor_tanda_terima as nomor_tt_tanpa_sj',
            'tttsj.tanggal_tanda_terima as tanggal_tt_tanpa_sj',
            'tttsj.no_kontainer as tttsj_no_kontainer',
            'mtk.nama_tujuan',
            'mtk.kode as kode_tujuan',
            'mk.nama_kapal',
            DB::raw("CASE
                WHEN tt.id IS NOT NULL THEN 'Tanda Terima'
                WHEN tttsj.id IS NOT NULL THEN 'Tanda Terima Tanpa SJ'
                ELSE 'Belum Siap'
            END as status_siap_muat")
        ])
        ->where(function($q) {
            // Kondisi siap muat: sudah ada tanda terima ATAU tanda terima tanpa surat jalan
            $q->whereNotNull('tt.id')
              ->orWhereNotNull('tttsj.id');
        })
        ->limit(5)
        ->get();

    echo "Total kontainer siap muat: " . $kontainers->count() . "\n\n";

    if ($kontainers->count() > 0) {
        echo "=== SAMPLE DATA ===\n";
        foreach ($kontainers as $kontainer) {
            echo "ID: {$kontainer->id}\n";
            echo "No. Kontainer: {$kontainer->nomor_seri_gabungan}\n";
            echo "Ukuran: {$kontainer->ukuran}\n";
            echo "Status Siap Muat: {$kontainer->status_siap_muat}\n";
            echo "Tujuan: {$kontainer->nama_tujuan}\n";
            echo "No. TT: " . ($kontainer->tt_no_surat_jalan ?: $kontainer->nomor_tt_tanpa_sj ?: '-') . "\n";
            echo "---\n";
        }
    } else {
        echo "Tidak ada kontainer yang siap muat.\n";
        echo "Mari cek kondisi data:\n\n";

        echo "Total stock kontainer: " . DB::table('stock_kontainers')->count() . "\n";
        echo "Stock kontainer dengan tanda_terima_id: " . DB::table('stock_kontainers')->whereNotNull('tanda_terima_id')->count() . "\n";
        echo "Stock kontainer dengan tanda_terima_tanpa_surat_jalan_id: " . DB::table('stock_kontainers')->whereNotNull('tanda_terima_tanpa_surat_jalan_id')->count() . "\n";
        echo "Total tanda terima: " . DB::table('tanda_terimas')->count() . "\n";
        echo "Total tanda terima tanpa SJ: " . DB::table('tanda_terima_tanpa_surat_jalan')->count() . "\n";
    }

    echo "\n=== TEST TUJUAN KIRIM ===\n";
    $tujuanKirims = DB::table('master_tujuan_kirim')->count();
    echo "Total tujuan kirim: {$tujuanKirims}\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
