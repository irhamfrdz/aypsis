<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== RAW DATABASE DATA ===\n";
$items = DB::select('SELECT id, item_number, meter_kubik FROM tanda_terima_lcl_items WHERE item_number >= 901 ORDER BY id DESC LIMIT 3');

foreach($items as $item) {
    echo "ID: {$item->id} | Item: {$item->item_number} | Volume: {$item->meter_kubik}\n";
}
echo "========================\n";