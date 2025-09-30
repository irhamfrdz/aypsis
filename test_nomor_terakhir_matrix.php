<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Http\Controllers\UserController;

$controller = new UserController();

// Test permission names
$testPermissions = [
    'master-nomor-terakhir-view',
    'master-nomor-terakhir-create',
    'master-nomor-terakhir-update',
    'master-nomor-terakhir-delete'
];

echo "Testing convertPermissionsToMatrix for master-nomor-terakhir:\n";
echo "Input permissions: " . implode(', ', $testPermissions) . "\n\n";

$matrix = $controller->testConvertPermissionsToMatrix($testPermissions);

echo "Output matrix:\n";
print_r($matrix);

echo "\nChecking if master-nomor-terakhir is in matrix:\n";
if (isset($matrix['master-nomor-terakhir'])) {
    echo "✅ master-nomor-terakhir found in matrix\n";
    echo "Actions: " . implode(', ', array_keys($matrix['master-nomor-terakhir'])) . "\n";
} else {
    echo "❌ master-nomor-terakhir NOT found in matrix\n";
}
