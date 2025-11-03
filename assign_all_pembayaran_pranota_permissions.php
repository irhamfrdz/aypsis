<?php

require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Assign Semua Permission Pembayaran Pranota Surat Jalan ke Admin ===\n";

$admin = App\Models\User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ User admin tidak ditemukan!\n";
    exit;
}

$allPermissions = App\Models\Permission::where('name', 'like', 'pembayaran-pranota-surat-jalan%')
    ->get(['id', 'name']);

echo "Assign permission ke admin:\n";
foreach ($allPermissions as $perm) {
    // Cek apakah user sudah punya permission ini
    $hasPermission = $admin->permissions()->where('permission_id', $perm->id)->exists();
    
    if (!$hasPermission) {
        $admin->permissions()->attach($perm->id);
        echo "✅ Ditambahkan: {$perm->name}\n";
    } else {
        echo "⚠️  Sudah ada: {$perm->name}\n";
    }
}

echo "\n=== Verifikasi Final ===\n";
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