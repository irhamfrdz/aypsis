<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TEST CONVERT PERMISSIONS TO MATRIX ===" . PHP_EOL;

// Test dengan permission yang sudah ada
$permissionNames = ['dashboard', 'dashboard-view'];

echo "Input permission names:" . PHP_EOL;
foreach ($permissionNames as $name) {
    echo "  - {$name}" . PHP_EOL;
}

// Test convertPermissionsToMatrix function
$userController = new App\Http\Controllers\UserController();

try {
    $matrixPermissions = $userController->testConvertPermissionsToMatrix($permissionNames);
    
    echo PHP_EOL . "✅ Conversion successful!" . PHP_EOL;
    echo "Matrix permissions generated:" . PHP_EOL;
    print_r($matrixPermissions);
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . PHP_EOL;
}

echo PHP_EOL . "=== TEST SELESAI ===" . PHP_EOL;