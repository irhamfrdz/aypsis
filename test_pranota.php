<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$nullItems = App\Models\PranotaOb::whereNull('items')->orWhere('items', '')->count();
echo 'Null or empty items: ' . $nullItems . "\n";

$all = App\Models\PranotaOb::all();
foreach ($all as $p) {
    $items = $p->items;
    if (!is_array($items) || count($items) == 0) {
        echo 'ID ' . $p->id . ' has empty items' . "\n";
    }
}