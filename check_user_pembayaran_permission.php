<?php

/**
 * Script to check if current admin user has pembayaran-pranota-uang-jalan-view permission
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "=== Checking User Pembayaran Pranota Uang Jalan Permissions ===\n\n";

// Get admin user (you can change this to your username)
echo "Enter username to check (or press Enter for 'admin'): ";
$username = trim(fgets(STDIN));
if (empty($username)) {
    $username = 'admin';
}

$user = User::where('username', $username)->first();

if (!$user) {
    echo "✗ User '{$username}' not found!\n";
    exit(1);
}

echo "✓ User found: {$user->username} (ID: {$user->id})\n\n";

// Check specific permissions
$permissionsToCheck = [
    'pembayaran-pranota-uang-jalan-view',
    'pembayaran-pranota-uang-jalan-create',
    'pembayaran-pranota-uang-jalan-edit',
    'pembayaran-pranota-uang-jalan-delete',
    'pembayaran-pranota-uang-jalan-approve',
    'pembayaran-pranota-uang-jalan-print',
    'pembayaran-pranota-uang-jalan-export'
];

echo "=== Permission Status ===\n";
$hasAny = false;
$userPermissions = $user->permissions()->pluck('name')->toArray();

foreach ($permissionsToCheck as $permName) {
    $hasPermission = in_array($permName, $userPermissions);
    
    if ($hasPermission) {
        echo "✓ HAS: {$permName}\n";
        $hasAny = true;
    } else {
        echo "✗ MISSING: {$permName}\n";
    }
}

if (!$hasAny) {
    echo "\n⚠ User '{$username}' doesn't have any pembayaran-pranota-uang-jalan permissions!\n";
    echo "\nTo fix this:\n";
    echo "1. Go to: Master > User > Edit '{$username}'\n";
    echo "2. Find 'Pembayaran Pranota Uang Jalan' section\n";
    echo "3. Check the permissions you want to grant\n";
    echo "4. Click 'Perbarui'\n";
} else {
    echo "\n✓ User has at least one pembayaran-pranota-uang-jalan permission!\n";
    echo "The menu should be visible in the sidebar.\n";
}

echo "\n=== All User Permissions (Total: " . count($userPermissions) . ") ===\n";
foreach ($userPermissions as $perm) {
    if (strpos($perm, 'pembayaran') !== false || strpos($perm, 'uang-jalan') !== false) {
        echo "  - {$perm}\n";
    }
}

echo "\nDone!\n";
