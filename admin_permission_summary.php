<?php

require_once 'vendor/autoload.php';

use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Admin User Permission Summary ===\n\n";

// Get admin user
$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "Admin user: {$user->username} (ID: {$user->id})\n";
echo "Total permissions: {$user->permissions->count()}\n\n";

// Group permissions by category
$permissions = $user->permissions->sortBy('name');
$grouped = [];

foreach ($permissions as $perm) {
    $name = $perm->name;

    // Categorize permissions
    if (strpos($name, 'master-') === 0) {
        $grouped['Master Data'][] = $name;
    } elseif (strpos($name, 'tagihan-kontainer') !== false) {
        $grouped['Tagihan Kontainer'][] = $name;
    } elseif (strpos($name, 'pranota') !== false) {
        $grouped['Pranota'][] = $name;
    } elseif (strpos($name, 'pembayaran') !== false) {
        $grouped['Pembayaran'][] = $name;
    } elseif (strpos($name, 'permohonan') !== false) {
        $grouped['Permohonan'][] = $name;
    } elseif (strpos($name, 'dashboard') !== false) {
        $grouped['Dashboard'][] = $name;
    } elseif (strpos($name, 'user') !== false || strpos($name, 'karyawan') !== false) {
        $grouped['User Management'][] = $name;
    } else {
        $grouped['Other'][] = $name;
    }
}

// Display grouped permissions
foreach ($grouped as $category => $perms) {
    echo "$category (" . count($perms) . "):\n";
    foreach ($perms as $perm) {
        echo "  - $perm\n";
    }
    echo "\n";
}

echo "🎉 Admin user has comprehensive access to all system features!\n";
