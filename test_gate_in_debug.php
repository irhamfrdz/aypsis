<?php

use Illuminate\Foundation\Application;

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Test the method directly
try {
    $controller = new App\Http\Controllers\GateInController();
    $request = new Illuminate\Http\Request();

    $response = $controller->getKontainersSuratJalan($request);

    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
