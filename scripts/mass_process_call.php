<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;

$r = Request::create('/penyelesaian/massProcess', 'POST', ['permohonan_ids' => [6]]);

try {
    $controller = app(App\Http\Controllers\PenyelesaianController::class);
    $response = $controller->massProcess($r);
    echo "massProcess executed. Response type: " . gettype($response) . "\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

