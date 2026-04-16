<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\TandaTerima;

$tt = TandaTerima::where('no_kontainer', 'AYPU2100277')
    ->where('jumlah', 155)
    ->first();

if ($tt) {
    echo "ID: " . $tt->id . PHP_EOL;
    echo "gambar_checkpoint: " . json_encode($tt->gambar_checkpoint) . PHP_EOL;
    echo "surat_jalan_id: " . $tt->surat_jalan_id . PHP_EOL;
    if ($tt->suratJalan) {
        echo "SJ gambar_checkpoint: " . json_encode($tt->suratJalan->gambar_checkpoint) . PHP_EOL;
    }
} else {
    echo "Not found" . PHP_EOL;
}
