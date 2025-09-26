<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

$pranotaId = 1; // Test dengan pranota 1
$pranota = \App\Models\PranotaTagihanKontainerSewa::find($pranotaId);
if (!$pranota) {
    echo "Pranota not found\n";
    exit;
}

$tagihanIds = $pranota->tagihan_kontainer_sewa_ids ?? [];
echo "Pranota $pranotaId tagihan IDs: " . json_encode($tagihanIds) . PHP_EOL;

if (!empty($tagihanIds)) {
    $tagihans = \App\Models\DaftarTagihanKontainerSewa::whereIn('id', $tagihanIds)->get();
    foreach($tagihans as $tagihan) {
        echo "Tagihan {$tagihan->id}: group = '{$tagihan->group}', status_pranota = '{$tagihan->status_pranota}'\n";
    }
}
?>
