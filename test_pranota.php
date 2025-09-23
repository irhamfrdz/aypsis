<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\PranotaController;
use Illuminate\Http\Request;

$data = [
    'tagihan_cat_ids' => [14, 15],
    'nomor_pranota' => 'PTC2509000001',
    'tanggal_pranota' => '2025-09-23',
    'supplier' => 'AYP Cat Service',
    'realisasi_biaya_total' => 5000000,
    'keterangan' => 'Test pranota'
];

try {
    $request = new Request($data);
    $controller = new PranotaController();
    $response = $controller->bulkCreateFromTagihanCat($request);
    echo 'Success: ' . $response->getContent();
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    echo 'File: ' . $e->getFile() . ':' . $e->getLine() . "\n";
}
