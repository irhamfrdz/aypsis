<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Stock Statuses:\n";
$stockStatuses = DB::table('stock_kontainers')->select('status', DB::raw('count(*) as count'))->groupBy('status')->get();
foreach($stockStatuses as $s) {
    echo "- '" . ($s->status ?? 'NULL') . "': " . $s->count . "\n";
}

echo "\nKontainer Statuses:\n";
$kontainerStatuses = DB::table('kontainers')->select('status', DB::raw('count(*) as count'))->groupBy('status')->get();
foreach($kontainerStatuses as $s) {
    echo "- '" . ($s->status ?? 'NULL') . "': " . $s->count . "\n";
}

echo "\nGudangs:\n";
$gudangs = DB::table('gudangs')->get();
foreach($gudangs as $g) {
    echo "- ID: " . $g->id . ", Name: " . $g->nama_gudang . "\n";
}
