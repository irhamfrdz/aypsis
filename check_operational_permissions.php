<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== CHECKING PERMISSIONS FOR OPERATIONAL MODULES ===\n\n";

$modules = [
    'pranota-supir',
    'pembayaran-pranota-supir',
    'permohonan'
];

$actions = ['view', 'create', 'update', 'delete', 'approve', 'print', 'export'];

echo "Modules to check:\n";
foreach ($modules as $module) {
    echo "- $module\n";
}
echo "\n";

echo "Actions to check:\n";
foreach ($actions as $action) {
    echo "- $action\n";
}
echo "\n";

$foundPermissions = [];
$missingPermissions = [];

foreach ($modules as $module) {
    echo "=== Checking permissions for: $module ===\n";

    foreach ($actions as $action) {
        $permissionName = $module . '-' . $action;

        try {
            $permission = DB::table('permissions')
                ->where('name', $permissionName)
                ->first();

            if ($permission) {
                echo "✅ FOUND: $permissionName (ID: {$permission->id})\n";
                $foundPermissions[] = $permissionName;
            } else {
                echo "❌ MISSING: $permissionName\n";
                $missingPermissions[] = $permissionName;
            }
        } catch (Exception $e) {
            echo "⚠️  ERROR checking $permissionName: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Found permissions: " . count($foundPermissions) . "\n";
echo "Missing permissions: " . count($missingPermissions) . "\n";
echo "\n";

if (!empty($foundPermissions)) {
    echo "Found permissions:\n";
    foreach ($foundPermissions as $perm) {
        echo "- $perm\n";
    }
    echo "\n";
}

if (!empty($missingPermissions)) {
    echo "Missing permissions:\n";
    foreach ($missingPermissions as $perm) {
        echo "- $perm\n";
    }
    echo "\n";
}

echo "=== RECOMMENDATIONS ===\n";
if (!empty($missingPermissions)) {
    echo "Some permissions are missing from the database. This could explain why certain checkboxes appear disabled or missing.\n";
    echo "You may need to:\n";
    echo "1. Create the missing permissions in the database\n";
    echo "2. Or remove the corresponding checkboxes from the UI if these permissions are not needed\n";
} else {
    echo "All permissions exist in the database. If checkboxes are still missing, the issue might be:\n";
    echo "1. JavaScript errors preventing proper display\n";
    echo "2. CSS issues hiding the checkboxes\n";
    echo "3. Blade template rendering issues\n";
    echo "4. Permission data not being passed correctly to the view\n";
}

echo "\n=== END OF CHECK ===\n";
