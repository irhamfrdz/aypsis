<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$id = $argv[1] ?? null;
if (!$id) {
    echo "Usage: php scripts/show_tagihan_details.php <id>\n";
    exit(1);
}

$tag = \DB::table('tagihan_kontainer_sewa')->where('id', $id)->first();
if (!$tag) {
    echo "Tagihan id=$id not found\n";
    exit(0);
}

echo "-- tagihan row --\n";
foreach ((array)$tag as $k => $v) {
    echo "$k => $v\n";
}

// Try to find pricelist matches by vendor+ukuran+date
$tanggal = $tag->tanggal_harga_awal ? $tag->tanggal_harga_awal : null;
$ukuran = $tag->ukuran_kontainer ?? null;

echo "\n-- attempting master pricelist matches for vendor={$tag->vendor} ukuran={$ukuran} tanggal={$tanggal} --\n";
$matches = \DB::table('master_pricelist_sewa_kontainers')
    ->where('vendor', $tag->vendor)
    ->where('ukuran_kontainer', $ukuran)
    ->where('tanggal_harga_awal', '<=', $tanggal)
    ->where(function($q) use ($tanggal) {
        $q->whereNull('tanggal_harga_akhir')->orWhere('tanggal_harga_akhir', '>=', $tanggal);
    })
    ->orderBy('tanggal_harga_awal','desc')
    ->get();

echo "matches count: " . count($matches) . "\n";
foreach ($matches as $m) {
    echo sprintf("id=%d vendor=%s tarif=%s ukuran=%s harga=%s tanggal_awal=%s tanggal_akhir=%s\n", $m->id,$m->vendor,$m->tarif,$m->ukuran_kontainer,$m->harga,$m->tanggal_harga_awal,$m->tanggal_harga_akhir);
}

// Also show any pricelist rows for vendor (regardless of ukuran) for context
$rows = \DB::table('master_pricelist_sewa_kontainers')->where('vendor', $tag->vendor)->orderBy('id','desc')->get();
echo "\n-- all pricelists for vendor {$tag->vendor} (summary) --\n";
foreach ($rows as $r) {
    echo sprintf("id=%d tarif=%s ukuran=%s tanggal_awal=%s tanggal_akhir=%s\n", $r->id, $r->tarif, $r->ukuran_kontainer, $r->tanggal_harga_awal, $r->tanggal_harga_akhir);
}
