<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Permission Pembayaran Pranota Surat Jalan ===\n";

// Test UserController methods
$userController = new App\Http\Controllers\UserController();

// Simulate permission matrix untuk pembayaran-pranota-surat-jalan
$testPermissions = [
    'pembayaran-pranota-surat-jalan' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '0',
        'approve' => '0',
        'print' => '1',
        'export' => '1'
    ]
];

echo "Simulasi permission matrix:\n";
print_r($testPermissions);

// Test convertMatrixPermissionsToIds
try {
    $reflection = new ReflectionClass($userController);
    $method = $reflection->getMethod('convertMatrixPermissionsToIds');
    $method->setAccessible(true);
    
    $permissionIds = $method->invoke($userController, $testPermissions);
    
    echo "\nHasil convertMatrixPermissionsToIds:\n";
    echo "Permission IDs: " . implode(', ', $permissionIds) . "\n";
    
    // Cek permission names berdasarkan IDs
    $permissions = App\Models\Permission::whereIn('id', $permissionIds)->get(['id', 'name']);
    echo "\nPermission names:\n";
    foreach ($permissions as $perm) {
        echo "- ID {$perm->id}: {$perm->name}\n";
    }
    
} catch (Exception $e) {
    echo "Error testing convertMatrixPermissionsToIds: " . $e->getMessage() . "\n";
}

echo "\n=== Test Assign Permission ke User ===\n";

// Test assign permission ke admin user
$user = App\Models\User::find(1);
if ($user) {
    echo "Testing dengan user: {$user->username}\n";
    
    // Cek permission yang sudah ada
    $existingPermissions = $user->permissions()
        ->where('name', 'like', '%pembayaran-pranota-surat-jalan%')
        ->get(['name'])
        ->pluck('name')
        ->toArray();
    
    echo "Permission yang sudah ada:\n";
    foreach ($existingPermissions as $perm) {
        echo "- {$perm}\n";
    }
    
    // Test convertPermissionsToMatrix
    try {
        $reflection = new ReflectionClass($userController);
        $method = $reflection->getMethod('convertPermissionsToMatrix');
        $method->setAccessible(true);
        
        $userPermissions = $user->permissions()->get(['name'])->pluck('name')->toArray();
        $matrix = $method->invoke($userController, $userPermissions);
        
        if (isset($matrix['pembayaran-pranota-surat-jalan'])) {
            echo "\nMatrix untuk pembayaran-pranota-surat-jalan:\n";
            print_r($matrix['pembayaran-pranota-surat-jalan']);
        } else {
            echo "\nTidak ada matrix untuk pembayaran-pranota-surat-jalan\n";
        }
        
    } catch (Exception $e) {
        echo "Error testing convertPermissionsToMatrix: " . $e->getMessage() . "\n";
    }
}

echo "\n=== Verifikasi Permission Database ===\n";

$pembarayanPranotaSuratJalanPermissions = App\Models\Permission::where('name', 'like', 'pembayaran-pranota-surat-jalan%')->get(['id', 'name']);
echo "Permission pembayaran-pranota-surat-jalan yang tersedia:\n";
foreach ($pembarayanPranotaSuratJalanPermissions as $perm) {
    echo "- ID {$perm->id}: {$perm->name}\n";
}