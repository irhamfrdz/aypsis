<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PerbaikanKontainer;

echo 'Checking latest data...' . PHP_EOL;
$latest = PerbaikanKontainer::orderBy('updated_at', 'desc')->take(5)->get();
foreach ($latest as $item) {
    echo 'ID: ' . $item->id . ', updated_at: ' . $item->updated_at . ', tanggal_cat: ' . ($item->tanggal_cat ?? 'NULL') . ', tanggal_catatan: ' . ($item->tanggal_catatan ?? 'NULL') . PHP_EOL;
}

echo PHP_EOL . 'Total records with tanggal_cat: ' . PerbaikanKontainer::whereNotNull('tanggal_cat')->count() . PHP_EOL;
?>
