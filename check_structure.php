<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== STRUKTUR TABEL tanda_terima_lcl_items ===\n";
$columns = DB::select('DESCRIBE tanda_terima_lcl_items');
foreach($columns as $column) {
    echo "{$column->Field}: {$column->Type}\n";
}
echo "===========================================\n";