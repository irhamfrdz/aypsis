<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Route;

echo "Checking route...\n";

$routeExists = Route::has('master.kode-nomor.index');
echo "Route exists: " . ($routeExists ? 'YES' : 'NO') . "\n";

if ($routeExists) {
    try {
        $url = route('master.kode-nomor.index');
        echo "Generated URL: " . $url . "\n";
    } catch (Exception $e) {
        echo "Error generating route: " . $e->getMessage() . "\n";
    }
}
