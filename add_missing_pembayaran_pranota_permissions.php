<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Menambah Permission yang Hilang ===\n";

$missingPermissions = [
    'pembayaran-pranota-surat-jalan-approve',
    'pembayaran-pranota-surat-jalan-print',
    'pembayaran-pranota-surat-jalan-export'
];

foreach ($missingPermissions as $permName) {
    // Cek apakah permission sudah ada
    $existing = App\Models\Permission::where('name', $permName)->first();
    
    if (!$existing) {
        $permission = App\Models\Permission::create([
            'name' => $permName,
            'guard_name' => 'web'
        ]);
        
        echo "✅ Berhasil menambah permission: {$permName} (ID: {$permission->id})\n";
        
        // Assign ke admin user
        $admin = App\Models\User::where('username', 'admin')->first();
        if ($admin) {
            $admin->permissions()->attach($permission->id);
            echo "   └─ Permission diberikan ke user admin\n";
        }
    } else {
        echo "⚠️  Permission sudah ada: {$permName} (ID: {$existing->id})\n";
    }
}

echo "\n=== Verifikasi Permission Lengkap ===\n";
$allPermissions = App\Models\Permission::where('name', 'like', 'pembayaran-pranota-surat-jalan%')
    ->orderBy('name')
    ->get(['id', 'name']);

echo "Semua permission pembayaran-pranota-surat-jalan:\n";
foreach ($allPermissions as $perm) {
    echo "- ID {$perm->id}: {$perm->name}\n";
}

echo "\n=== Test Permission Admin User ===\n";
$admin = App\Models\User::where('username', 'admin')->first();
if ($admin) {
    $adminPermissions = $admin->permissions()
        ->where('name', 'like', 'pembayaran-pranota-surat-jalan%')
        ->get(['name'])
        ->pluck('name')
        ->toArray();
    
    echo "Permission admin untuk pembayaran-pranota-surat-jalan:\n";
    foreach ($adminPermissions as $perm) {
        echo "- {$perm}\n";
    }
    
    // Test can() method
    echo "\nTest can() method:\n";
    echo "- Can view: " . ($admin->can('pembayaran-pranota-surat-jalan-view') ? 'Yes' : 'No') . "\n";
    echo "- Can create: " . ($admin->can('pembayaran-pranota-surat-jalan-create') ? 'Yes' : 'No') . "\n";
    echo "- Can edit: " . ($admin->can('pembayaran-pranota-surat-jalan-edit') ? 'Yes' : 'No') . "\n";
    echo "- Can delete: " . ($admin->can('pembayaran-pranota-surat-jalan-delete') ? 'Yes' : 'No') . "\n";
    echo "- Can approve: " . ($admin->can('pembayaran-pranota-surat-jalan-approve') ? 'Yes' : 'No') . "\n";
    echo "- Can print: " . ($admin->can('pembayaran-pranota-surat-jalan-print') ? 'Yes' : 'No') . "\n";
    echo "- Can export: " . ($admin->can('pembayaran-pranota-surat-jalan-export') ? 'Yes' : 'No') . "\n";
}