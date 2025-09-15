<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Debugging Permission: master.karyawan.index\n";
echo "==========================================\n\n";

$permissionName = 'master.karyawan.index';

echo "Permission name: $permissionName\n";
echo "Contains dot: " . (strpos($permissionName, '.') !== false ? 'YES' : 'NO') . "\n";
echo "Contains dash: " . (strpos($permissionName, '-') !== false ? 'YES' : 'NO') . "\n\n";

if (strpos($permissionName, '.') !== false) {
    echo "Processing as DOT notation:\n";
    $parts = explode('.', $permissionName);
    echo "Parts: " . implode(', ', $parts) . "\n";
    echo "Count: " . count($parts) . "\n";
    echo "First part is 'master': " . ($parts[0] === 'master' ? 'YES' : 'NO') . "\n";

    if (count($parts) >= 3 && $parts[0] === 'master') {
        echo "✓ Matches master.karyawan.index pattern\n";
        $module = $parts[0] . '-' . $parts[1];
        $action = $parts[2];
        echo "Module: $module\n";
        echo "Action: $action\n";
    } elseif (count($parts) >= 2) {
        echo "✓ Matches module.action pattern\n";
        $module = $parts[0];
        $action = $parts[1];
        echo "Module: $module\n";
        echo "Action: $action\n";
    }
} elseif (strpos($permissionName, '-') !== false) {
    echo "Processing as DASH notation:\n";
    $parts = explode('-', $permissionName, 2);
    echo "Parts: " . implode(', ', $parts) . "\n";
    echo "Count: " . count($parts) . "\n";
} else {
    echo "Processing as SIMPLE notation:\n";
    echo "Module: $permissionName\n";
}
