<?php
require "vendor/autoload.php";
$app = require "bootstrap/app.php";
$app->make("Illuminate\Contracts\Console\Kernel")->bootstrap();

use Illuminate\Http\Request;

echo "Testing lepasKontainer for pranota 1\n";

// Simulate the request
$request = new Request();
$request->merge([
    'tagihan_ids' => [842, 863] // IDs dari pranota 1
]);

// Get the controller
$controller = new \App\Http\Controllers\PranotaTagihanKontainerSewaController();

// Call the method
try {
    $response = $controller->lepasKontainer($request, 1);
    $data = json_decode($response->getContent(), true);
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . PHP_EOL;
}

// Check result
echo "\nAfter update:\n";
$tagihans = \App\Models\DaftarTagihanKontainerSewa::whereIn('id', [842, 863])->get();
foreach($tagihans as $tagihan) {
    echo "Tagihan {$tagihan->id}: group = '{$tagihan->group}', status_pranota = '{$tagihan->status_pranota}'\n";
}
?>
