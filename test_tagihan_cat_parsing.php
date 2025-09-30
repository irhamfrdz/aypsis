<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Http\Controllers\UserController;

$controller = new UserController();

// Test parsing tagihan-cat-view
$permissionNames = ['tagihan-cat-view'];
$result = $controller->testConvertPermissionsToMatrix($permissionNames);

echo "Parsing result for 'tagihan-cat-view':\n";
echo json_encode($result, JSON_PRETTY_PRINT) . "\n";

// Check if tagihan-cat view is set
if (isset($result['tagihan-cat']['view']) && $result['tagihan-cat']['view']) {
    echo "\n✅ tagihan-cat-view is correctly parsed to tagihan-cat[view]\n";
} else {
    echo "\n❌ tagihan-cat-view is NOT parsed correctly\n";
}
