<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\MasterPricelistSewaKontainer;

$rows = MasterPricelistSewaKontainer::where('vendor','ZONA')->where('ukuran_kontainer',40)->orderBy('tanggal_harga_awal','desc')->get();
if ($rows->isEmpty()) { echo "No pricelist rows for vendor=ZONA ukuran=40\n"; exit(0); }
foreach ($rows as $r) {
    printf("id=%d vendor=%s ukuran=%s tarif=%s harga=%s tanggal_awal=%s tanggal_akhir=%s keterangan=%s\n",
        $r->id, $r->vendor, $r->ukuran_kontainer, $r->tarif, $r->harga, $r->tanggal_harga_awal, $r->tanggal_harga_akhir, $r->keterangan);
}
