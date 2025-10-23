<?php

// Load Laravel environment
require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Testing NIK API endpoint...\n\n";

    // Simulate the API call that the frontend would make
    $url = 'http://localhost:8000/master/karyawan/get-next-nik';

    // Test the controller method directly
    $controller = new App\Http\Controllers\KaryawanController();
    $response = $controller->getNextNik();

    echo "Controller response:\n";
    echo $response->getContent() . "\n\n";

    // Decode the JSON response
    $data = json_decode($response->getContent(), true);

    if ($data && isset($data['success']) && $data['success']) {
        echo "✅ API working correctly!\n";
        echo "Next NIK: " . $data['nik'] . "\n";
    } else {
        echo "❌ API response error\n";
        var_dump($data);
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
