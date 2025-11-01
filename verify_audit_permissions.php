<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== VERIFIKASI AUDIT LOG PERMISSIONS ===" . PHP_EOL;

// Cek semua permissions yang mengandung kata audit
$allAuditPermissions = App\Models\Permission::where('name', 'LIKE', '%audit%')
    ->orWhere('description', 'LIKE', '%audit%')
    ->get();

echo "Semua audit-related permissions di database:" . PHP_EOL;
foreach ($allAuditPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
}

// Cek admin user permissions
$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    echo PHP_EOL . "Admin user audit permissions:" . PHP_EOL;
    $adminAuditPerms = $admin->permissions()
        ->where(function($query) {
            $query->where('name', 'LIKE', '%audit%')
                  ->orWhere('description', 'LIKE', '%audit%');
        })->get();
    
    foreach ($adminAuditPerms as $perm) {
        echo "  ✓ {$perm->name} (ID: {$perm->id}) - {$perm->description}" . PHP_EOL;
    }
    
    echo PHP_EOL . "Total permissions admin: " . $admin->permissions()->count() . PHP_EOL;
}

// Test konversi ke matrix format
echo PHP_EOL . "=== TEST MATRIX CONVERSION ===" . PHP_EOL;
$auditPermNames = $allAuditPermissions->pluck('name')->toArray();

if (!empty($auditPermNames)) {
    $userController = new App\Http\Controllers\UserController();
    $matrixPermissions = $userController->testConvertPermissionsToMatrix($auditPermNames);
    
    echo "Audit permissions dalam format matrix:" . PHP_EOL;
    print_r($matrixPermissions);
}

echo PHP_EOL . "=== SELESAI ===" . PHP_EOL;