<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Permission;

echo "=== TESTING NEW PERMISSION MODULES UI ===\n\n";

// Check if new permission modules exist in database
$newModules = [
    'pembayaran-uang-muka',
    'realisasi-uang-muka',
    'pembayaran-ob'
];

$actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

echo "1. Checking if new permission modules exist in database:\n";
echo str_repeat("-", 60) . "\n";

foreach ($newModules as $module) {
    echo "Module: $module\n";
    foreach ($actions as $action) {
        $permissionName = "$module-$action";
        $permission = Permission::where('name', $permissionName)->first();

        if ($permission) {
            echo "  ✅ $permissionName (ID: {$permission->id})\n";
        } else {
            echo "  ❌ $permissionName (NOT FOUND)\n";
        }
    }
    echo "\n";
}

echo "\n2. Checking template files:\n";
echo str_repeat("-", 60) . "\n";

// Check create.blade.php
$createFile = 'resources/views/master-user/create.blade.php';
if (file_exists($createFile)) {
    $content = file_get_contents($createFile);

    echo "create.blade.php:\n";
    foreach ($newModules as $module) {
        if (strpos($content, "permissions[$module]") !== false) {
            echo "  ✅ $module module found\n";
        } else {
            echo "  ❌ $module module NOT found\n";
        }
    }

    // Check for pembayaran header checkbox
    if (strpos($content, 'pembayaran-header-checkbox') !== false) {
        echo "  ✅ pembayaran header checkbox found\n";
    } else {
        echo "  ❌ pembayaran header checkbox NOT found\n";
    }

    // Check for JavaScript functions
    if (strpos($content, 'initializeCheckAllPembayaran') !== false) {
        echo "  ✅ initializeCheckAllPembayaran function found\n";
    } else {
        echo "  ❌ initializeCheckAllPembayaran function NOT found\n";
    }
} else {
    echo "  ❌ create.blade.php NOT found\n";
}

echo "\n";

// Check edit.blade.php
$editFile = 'resources/views/master-user/edit.blade.php';
if (file_exists($editFile)) {
    $content = file_get_contents($editFile);

    echo "edit.blade.php:\n";
    foreach ($newModules as $module) {
        if (strpos($content, "permissions[$module]") !== false) {
            echo "  ✅ $module module found\n";
        } else {
            echo "  ❌ $module module NOT found\n";
        }
    }

    // Check for pembayaran header checkbox
    if (strpos($content, 'pembayaran-header-checkbox') !== false) {
        echo "  ✅ pembayaran header checkbox found\n";
    } else {
        echo "  ❌ pembayaran header checkbox NOT found\n";
    }

    // Check for JavaScript functions
    if (strpos($content, 'initializeCheckAllPembayaran') !== false) {
        echo "  ✅ initializeCheckAllPembayaran function found\n";
    } else {
        echo "  ❌ initializeCheckAllPembayaran function NOT found\n";
    }
} else {
    echo "  ❌ edit.blade.php NOT found\n";
}

echo "\n3. Summary:\n";
echo str_repeat("-", 60) . "\n";

$totalPermissions = count($newModules) * count($actions);
$existingPermissions = 0;

foreach ($newModules as $module) {
    foreach ($actions as $action) {
        $permissionName = "$module-$action";
        if (Permission::where('name', $permissionName)->exists()) {
            $existingPermissions++;
        }
    }
}

echo "Total expected permissions: $totalPermissions\n";
echo "Existing permissions: $existingPermissions\n";
echo "Missing permissions: " . ($totalPermissions - $existingPermissions) . "\n";

if ($existingPermissions === $totalPermissions) {
    echo "✅ All permission modules are ready in database!\n";
} else {
    echo "❌ Some permissions are missing in database!\n";
}

echo "\n=== TEST COMPLETED ===\n";
