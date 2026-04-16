<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use App\Models\Kontainer;
use App\Models\StockKontainer;
use App\Models\HistoryKontainer;
use Illuminate\Support\Facades\Artisan;

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$mismatches = [];

echo "Checking Kontainers...\n";
Kontainer::whereNotNull('gudangs_id')->each(function($k) use (&$mismatches) {
    $lastHistory = HistoryKontainer::where('nomor_kontainer', $k->nomor_seri_gabungan)->orderBy('id', 'desc')->first();
    if (!$lastHistory || $lastHistory->gudang_id != $k->gudangs_id) {
        $mismatches[] = [
            'type' => 'kontainer',
            'nomor' => $k->nomor_seri_gabungan,
            'current_gudang_id' => $k->gudangs_id,
            'last_history_id' => $lastHistory ? $lastHistory->id : null,
            'last_history_gudang_id' => $lastHistory ? $lastHistory->gudang_id : null
        ];
    }
});

echo "Checking StockKontainers...\n";
StockKontainer::whereNotNull('gudangs_id')->each(function($s) use (&$mismatches) {
    $lastHistory = HistoryKontainer::where('nomor_kontainer', $s->nomor_seri_gabungan)->orderBy('id', 'desc')->first();
    if (!$lastHistory || $lastHistory->gudang_id != $s->gudangs_id) {
        $mismatches[] = [
            'type' => 'stock',
            'nomor' => $s->nomor_seri_gabungan,
            'current_gudang_id' => $s->gudangs_id,
            'last_history_id' => $lastHistory ? $lastHistory->id : null,
            'last_history_gudang_id' => $lastHistory ? $lastHistory->gudang_id : null
        ];
    }
});

file_put_contents('scratch/mismatches.json', json_encode($mismatches, JSON_PRETTY_PRINT));
echo "Found " . count($mismatches) . " mismatches.\n";
