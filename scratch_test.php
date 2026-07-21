<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$pranotas = \App\Models\PranotaOb::all();
foreach ($pranotas as $p) {
    $totalBiaya = 0;
    foreach ($p->getEnrichedItems() as $item) {
        $totalBiaya += (float) ($item['biaya'] ?? 0);
    }
    if ($totalBiaya == 3700000 || $totalBiaya == 3740000) {
        echo "Found in PranotaOb: " . $p->no_voyage . " (Total: $totalBiaya)\n";
    }
}
