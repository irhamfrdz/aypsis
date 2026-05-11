<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TujuanKegiatanUtama;

$t = TujuanKegiatanUtama::where('ke', 'CIANJUR')->first();
if ($t) {
    echo "Ke: " . $t->ke . "\n";
    echo "Wilayah: " . $t->wilayah . "\n";
    echo "Cabang: " . $t->cabang . "\n";
} else {
    echo "Not found\n";
}
