<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "=== Test Permission Dashboard Surat Jalan ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan\n";
    exit;
}

echo "Testing permission untuk user: {$admin->username}\n\n";

// Test permission dashboard
$permissionName = 'surat-jalan-approval-dashboard';
$hasPermission = $admin->hasPermissionTo($permissionName);
$canAccess = $admin->can($permissionName);

echo "Permission: {$permissionName}\n";
echo "hasPermissionTo(): " . ($hasPermission ? '✅ GRANTED' : '❌ DENIED') . "\n";
echo "can(): " . ($canAccess ? '✅ GRANTED' : '❌ DENIED') . "\n";

// Check if user is approved (middleware requirement)
echo "\nUser approval status:\n";
echo "Status approval: " . ($admin->is_approved ?? 'null') . "\n";
echo "Status: " . ($admin->status ?? 'null') . "\n";
echo "Karyawan ID: " . ($admin->karyawan_id ?? 'null') . "\n";

// Test level permissions
$levelPermissions = [
    'surat-jalan-approval-level-1-view',
    'surat-jalan-approval-level-2-view'
];

echo "\nLevel permissions:\n";
foreach ($levelPermissions as $permission) {
    $hasLevel = $admin->hasPermissionTo($permission);
    echo "- {$permission}: " . ($hasLevel ? '✅' : '❌') . "\n";
}

echo "\n=== Test Selesai ===\n";
