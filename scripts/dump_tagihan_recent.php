<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Recent tagihan_kontainer_sewa rows:\n";
$rows = DB::table('tagihan_kontainer_sewa')->orderBy('id','desc')->limit(10)->get();
if (count($rows) === 0) { echo " - (no rows)\n"; } else {
    foreach ($rows as $r) {
        echo "id={$r->id}, vendor={$r->vendor}, tanggal_harga_awal={$r->tanggal_harga_awal}, nomor_kontainer=" . (isset($r->nomor_kontainer)?$r->nomor_kontainer:'') . ", grand_total={$r->grand_total}\n";
    }
}

echo "\nPivot tagihan_kontainer_sewa_kontainers recent rows:\n";
$pivot = DB::table('tagihan_kontainer_sewa_kontainers')->orderBy('id','desc')->limit(10)->get();
if (count($pivot) === 0) { echo " - (no pivot rows)\n"; } else {
    foreach ($pivot as $p) {
        echo "id={$p->id}, tagihan_id={$p->tagihan_id}, kontainer_id={$p->kontainer_id}\n";
    }
}

// Count total for quick sanity
$cnt = DB::table('tagihan_kontainer_sewa')->count();
echo "\nTotal tagihan_kontainer_sewa: {$cnt}\n";
$cnt2 = DB::table('tagihan_kontainer_sewa_kontainers')->count();
echo "Total pivot rows: {$cnt2}\n";
