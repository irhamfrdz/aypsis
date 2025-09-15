<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permissions = Permission::all()->pluck('name')->toArray();
echo 'Total permissions: ' . count($permissions) . PHP_EOL;
echo PHP_EOL;

echo '=== DAFTAR LENGKAP PERMISSION ===' . PHP_EOL;
foreach ($permissions as $index => $permission) {
    echo str_pad($index + 1, 3, ' ', STR_PAD_LEFT) . '. ' . $permission . PHP_EOL;
}

echo PHP_EOL;
echo '=== ANALISIS KATEGORI PERMISSION ===' . PHP_EOL;

// Kategorikan permission berdasarkan pola
$categories = [
    'master' => [],
    'tagihan' => [],
    'pranota' => [],
    'pembayaran' => [],
    'permohonan' => [],
    'user' => [],
    'dashboard' => [],
    'other' => []
];

foreach ($permissions as $permission) {
    if (strpos($permission, 'master-') === 0) {
        $categories['master'][] = $permission;
    } elseif (strpos($permission, 'tagihan-') === 0) {
        $categories['tagihan'][] = $permission;
    } elseif (strpos($permission, 'pranota') !== false) {
        $categories['pranota'][] = $permission;
    } elseif (strpos($permission, 'pembayaran') !== false) {
        $categories['pembayaran'][] = $permission;
    } elseif (strpos($permission, 'permohonan') !== false) {
        $categories['permohonan'][] = $permission;
    } elseif (strpos($permission, 'user') !== false) {
        $categories['user'][] = $permission;
    } elseif (strpos($permission, 'dashboard') !== false) {
        $categories['dashboard'][] = $permission;
    } else {
        $categories['other'][] = $permission;
    }
}

foreach ($categories as $category => $perms) {
    echo strtoupper($category) . ': ' . count($perms) . ' permissions' . PHP_EOL;
    if (count($perms) <= 10) { // Show all if <= 10
        foreach ($perms as $perm) {
            echo '  - ' . $perm . PHP_EOL;
        }
    } else { // Show first 5 and last 5 if > 10
        for ($i = 0; $i < 5; $i++) {
            echo '  - ' . $perms[$i] . PHP_EOL;
        }
        echo '  ... (' . (count($perms) - 10) . ' more permissions) ...' . PHP_EOL;
        for ($i = count($perms) - 5; $i < count($perms); $i++) {
            echo '  - ' . $perms[$i] . PHP_EOL;
        }
    }
    echo PHP_EOL;
}
