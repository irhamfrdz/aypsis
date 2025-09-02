<?php
require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $rows = \DB::table('master_pricelist_sewa_kontainers')->where('vendor', 'ZONA')->orderBy('id', 'desc')->get();
    $count = \DB::table('master_pricelist_sewa_kontainers')->where('vendor', 'ZONA')->count();

    echo "pricelist_zona_count: $count\n";
    foreach ($rows as $r) {
        echo sprintf("id=%d vendor=%s tarif=%s ukuran=%s harga=%s tanggal_awal=%s tanggal_akhir=%s\n", $r->id, $r->vendor, $r->tarif, $r->ukuran_kontainer, $r->harga, $r->tanggal_harga_awal, $r->tanggal_harga_akhir);
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
