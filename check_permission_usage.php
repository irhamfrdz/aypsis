<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$permissions = Permission::all()->pluck('name')->toArray();
echo '=== ANALISIS PENGGUNAAN PERMISSION ===' . PHP_EOL;
echo 'Total permissions to check: ' . count($permissions) . PHP_EOL . PHP_EOL;

// Function to search for permission usage in files
function searchPermissionUsage($permission, $searchPaths = ['app', 'routes', 'resources', 'database']) {
    $usageCount = 0;
    $foundIn = [];

    foreach ($searchPaths as $path) {
        if (!is_dir($path)) continue;

        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
        foreach ($iterator as $file) {
            if ($file->isFile() && in_array($file->getExtension(), ['php', 'blade.php'])) {
                $content = file_get_contents($file->getPathname());
                if (strpos($content, $permission) !== false) {
                    $usageCount++;
                    $foundIn[] = str_replace('\\', '/', $file->getPathname());
                }
            }
        }
    }

    return ['count' => $usageCount, 'files' => $foundIn];
}

// Check each permission
$usedPermissions = [];
$unusedPermissions = [];
$suspiciousPermissions = [];

foreach ($permissions as $permission) {
    $usage = searchPermissionUsage($permission);

    if ($usage['count'] > 0) {
        $usedPermissions[$permission] = $usage;
    } else {
        $unusedPermissions[] = $permission;
    }

    // Check for suspicious patterns (duplicates)
    if (preg_match('/^(master|tagihan|pranota|pembayaran|permohonan|user)\./', $permission)) {
        $suspiciousPermissions[] = $permission;
    }
}

echo '=== PERMISSION YANG DIGUNAKAN ===' . PHP_EOL;
echo 'Total used: ' . count($usedPermissions) . PHP_EOL . PHP_EOL;

$sampleUsed = array_slice($usedPermissions, 0, 10, true);
foreach ($sampleUsed as $perm => $usage) {
    echo "✓ $perm (used in {$usage['count']} files)" . PHP_EOL;
    if (count($usage['files']) <= 3) {
        foreach ($usage['files'] as $file) {
            echo "  - $file" . PHP_EOL;
        }
    } else {
        for ($i = 0; $i < 3; $i++) {
            echo "  - {$usage['files'][$i]}" . PHP_EOL;
        }
        echo "  ... and " . (count($usage['files']) - 3) . " more files" . PHP_EOL;
    }
    echo PHP_EOL;
}

echo '=== PERMISSION YANG TIDAK DIGUNAKAN ===' . PHP_EOL;
echo 'Total unused: ' . count($unusedPermissions) . PHP_EOL . PHP_EOL;

if (count($unusedPermissions) <= 20) {
    foreach ($unusedPermissions as $perm) {
        echo "✗ $perm" . PHP_EOL;
    }
} else {
    for ($i = 0; $i < 10; $i++) {
        echo "✗ {$unusedPermissions[$i]}" . PHP_EOL;
    }
    echo "... and " . (count($unusedPermissions) - 10) . " more unused permissions" . PHP_EOL;
}

echo PHP_EOL;
echo '=== PERMISSION YANG MENCURIGAKAN (DUPLIKAT) ===' . PHP_EOL;
echo 'Total suspicious: ' . count($suspiciousPermissions) . PHP_EOL . PHP_EOL;

foreach ($suspiciousPermissions as $perm) {
    echo "⚠ $perm" . PHP_EOL;
}

echo PHP_EOL;
echo '=== RINGKASAN ===' . PHP_EOL;
echo 'Total permissions: ' . count($permissions) . PHP_EOL;
echo 'Used permissions: ' . count($usedPermissions) . PHP_EOL;
echo 'Unused permissions: ' . count($unusedPermissions) . PHP_EOL;
echo 'Suspicious permissions: ' . count($suspiciousPermissions) . PHP_EOL;
echo 'Safe to delete: ' . (count($unusedPermissions) + count($suspiciousPermissions)) . ' permissions' . PHP_EOL;
