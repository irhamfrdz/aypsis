<?php
// Test NIK generation route
require_once 'vendor/autoload.php';

// Import Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate HTTP request to the route
$request = Illuminate\Http\Request::create('/master/karyawan/get-next-nik', 'GET');

try {
    $response = $kernel->handle($request);
    $content = $response->getContent();

    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Content-Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response Content: " . $content . "\n";

    $data = json_decode($content, true);
    if ($data) {
        echo "Parsed JSON:\n";
        print_r($data);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
