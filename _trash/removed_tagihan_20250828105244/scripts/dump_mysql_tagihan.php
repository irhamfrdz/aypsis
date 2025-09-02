<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tagihan = \DB::table('tagihan_kontainer_sewa')->select('id','vendor','tarif','tanggal_harga_awal','tanggal_harga_akhir')->orderBy('id','desc')->limit(10)->get();
    $tagihanCount = \DB::table('tagihan_kontainer_sewa')->count();
    $pivotCount = \DB::table('tagihan_kontainer_sewa_kontainers')->count();
    $orphanCount = \DB::table('tagihan_kontainer_sewa_kontainers')
        ->whereNotIn('tagihan_id', function ($q) {
            $q->select('id')->from('tagihan_kontainer_sewa');
        })->count();

    echo "tagihan_count: $tagihanCount\n";
    echo "pivot_count: $pivotCount\n";
    echo "orphan_pivot_count: $orphanCount\n";

    echo "--- latest tagihan (up to 10) ---\n";
    foreach ($tagihan as $t) {
        $tglAwal = $t->tanggal_harga_awal ? (is_string($t->tanggal_harga_awal) ? $t->tanggal_harga_awal : $t->tanggal_harga_awal) : null;
        echo sprintf("id=%d vendor=%s tarif=%s tanggal_awal=%s tanggal_akhir=%s\n", $t->id, $t->vendor, $t->tarif, $tglAwal, $t->tanggal_harga_akhir);
    }

    if ($orphanCount > 0) {
        echo "--- sample orphan pivot rows ---\n";
        $orphans = \DB::table('tagihan_kontainer_sewa_kontainers')
            ->whereNotIn('tagihan_id', function ($q) {
                $q->select('id')->from('tagihan_kontainer_sewa');
            })->limit(50)->get();
        foreach ($orphans as $o) {
            echo sprintf("pivot id=%d tagihan_id=%s kontainer_id=%s\n", $o->id, $o->tagihan_id, $o->kontainer_id);
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
