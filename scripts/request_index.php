<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a GET request to the index with show_all=1
$request = Illuminate\Http\Request::create('/master/karyawan', 'GET', ['show_all' => '1']);
$response = $kernel->handle($request);

echo "HTTP STATUS: " . $response->getStatusCode() . PHP_EOL;
$content = $response->getContent();

// Check for counts and the 'Filter Aktif' banner for show_all
$hasCounts = (strpos($content, 'Aktif:') !== false && strpos($content, 'Total:') !== false);
$hasShowAllBanner = strpos($content, 'Menampilkan semua karyawan') !== false;
$hasTableRow = strpos($content, '<table') !== false;

echo "Has counts: " . ($hasCounts ? 'yes' : 'no') . PHP_EOL;
echo "Has show_all banner: " . ($hasShowAllBanner ? 'yes' : 'no') . PHP_EOL;
echo "Has table: " . ($hasTableRow ? 'yes' : 'no') . PHP_EOL;

// Optionally print short snippet
$snippet = substr($content, 0, 1000);
echo "--- BEGIN SNIPPET ---\n" . $snippet . "\n--- END SNIPPET ---\n";

$kernel->terminate($request, $response);
