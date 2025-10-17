<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== Test Permission System ===\n";

// Get admin user
$admin = User::where('username', 'admin')->first();

if (!$admin) {
    echo "❌ User admin tidak ditemukan\n";
    exit;
}

echo "Testing permissions untuk user: {$admin->username}\n\n";

// Test permissions
$permissions = [
    'surat-jalan-approval-dashboard',
    'surat-jalan-approval-level-1-view',
    'surat-jalan-approval-level-1-approve',
    'surat-jalan-approval-level-2-view',
    'surat-jalan-approval-level-2-approve'
];

foreach ($permissions as $permission) {
    if (method_exists($admin, 'hasPermissionTo')) {
        $hasPermission = $admin->hasPermissionTo($permission);
        $status = $hasPermission ? '✅' : '❌';
        echo "{$status} {$permission}: " . ($hasPermission ? 'GRANTED' : 'DENIED') . "\n";
    } else {
        echo "⚠️  Method hasPermissionTo tidak ditemukan di model User\n";
        break;
    }
}

echo "\n=== Test Selesai ===\n";
