<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');

// Simulate HTTP request
$request = Illuminate\Http\Request::create(
    '/bl/get-voyage-by-kapal?nama_kapal=' . urlencode('KM SUMBER ABADI 178'),
    'GET'
);

try {
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response Body:\n";
    echo $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response);
