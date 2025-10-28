<?php

// Simple debug script to check LCL data
require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== Latest LCL Records ===" . PHP_EOL;

try {
    $items = DB::table('tanda_terima_lcl_items')
        ->orderBy('id', 'desc')
        ->limit(5)
        ->get(['id', 'panjang', 'lebar', 'tinggi', 'meter_kubik', 'created_at']);
    
    foreach ($items as $item) {
        echo sprintf(
            "ID:%d | %s x %s x %s = %s m³ | %s" . PHP_EOL,
            $item->id,
            $item->panjang ?? 'null',
            $item->lebar ?? 'null', 
            $item->tinggi ?? 'null',
            $item->meter_kubik ?? 'null',
            $item->created_at
        );
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

echo "===========================" . PHP_EOL;