<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$t = App\Models\TandaTerimaLcl::first();
if (!$t) {
    echo "No TandaTerimaLcl records\n";
    exit;
}

echo "Found id: " . $t->id . "\n";
try {
    $rel = $t->tujuanPengiriman;
    echo "Relation loaded: " . ($rel ? ($rel->nama_tujuan ?? 'HAS NO nama_tujuan') : 'NULL') . "\n";
} catch (Throwable $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
