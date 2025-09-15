<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MASTER DATA PRINT & EXPORT PERMISSIONS CHECK ===\n\n";

try {
    // Menggunakan DB facade untuk query langsung
    $masterModules = [
        'master-karyawan' => 'Data Karyawan',
        'master-user' => 'Data User',
        'master-kontainer' => 'Data Kontainer',
        'master-tujuan' => 'Data Tujuan',
        'master-kegiatan' => 'Data Kegiatan',
        'master-permission' => 'Data Permission',
        'master-mobil' => 'Data Mobil',
        'master-pricelist-sewa-kontainer' => 'Data Pricelist Sewa Kontainer'
    ];

    $actions = ['print', 'export'];

    echo "Checking permissions for each module:\n\n";

    foreach ($masterModules as $module => $moduleName) {
        echo "=== {$moduleName} ({$module}) ===\n";

        foreach ($actions as $action) {
            $permissionName = $module . '.' . $action;
            $permission = DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                echo "✅ FOUND: {$permissionName} (ID: {$permission->id})\n";
            } else {
                echo "❌ NOT FOUND: {$permissionName}\n";
            }
        }
        echo "\n";
    }

    echo "=== SUMMARY ===\n";

    $foundPermissions = [];
    $missingPermissions = [];

    foreach ($masterModules as $module => $moduleName) {
        foreach ($actions as $action) {
            $permissionName = $module . '.' . $action;
            $permission = DB::table('permissions')->where('name', $permissionName)->first();

            if ($permission) {
                $foundPermissions[] = $permissionName;
            } else {
                $missingPermissions[] = $permissionName;
            }
        }
    }

    echo "Found permissions: " . count($foundPermissions) . "\n";
    echo "Missing permissions: " . count($missingPermissions) . "\n";

    if (!empty($foundPermissions)) {
        echo "\n✅ Modules with Print/Export permissions:\n";
        foreach ($foundPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    }

    if (!empty($missingPermissions)) {
        echo "\n❌ Modules missing Print/Export permissions:\n";
        foreach ($missingPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    }

    echo "\n=== RECOMMENDATION ===\n";

    $modulesWithAllPermissions = [];
    $modulesWithSomePermissions = [];
    $modulesWithNoPermissions = [];

    foreach ($masterModules as $module => $moduleName) {
        $hasPrint = DB::table('permissions')->where('name', $module . '.print')->first();
        $hasExport = DB::table('permissions')->where('name', $module . '.export')->first();

        if ($hasPrint && $hasExport) {
            $modulesWithAllPermissions[] = $moduleName;
        } elseif ($hasPrint || $hasExport) {
            $modulesWithSomePermissions[] = $moduleName;
        } else {
            $modulesWithNoPermissions[] = $moduleName;
        }
    }

    if (!empty($modulesWithAllPermissions)) {
        echo "✅ Modules with BOTH Print & Export permissions (keep checkboxes):\n";
        foreach ($modulesWithAllPermissions as $module) {
            echo "  - {$module}\n";
        }
    }

    if (!empty($modulesWithSomePermissions)) {
        echo "\n⚠️ Modules with PARTIAL permissions (check which one to keep):\n";
        foreach ($modulesWithSomePermissions as $module) {
            echo "  - {$module}\n";
        }
    }

    if (!empty($modulesWithNoPermissions)) {
        echo "\n❌ Modules with NO Print/Export permissions (remove checkboxes):\n";
        foreach ($modulesWithNoPermissions as $module) {
            echo "  - {$module}\n";
        }
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
