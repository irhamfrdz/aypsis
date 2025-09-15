<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== DASHBOARD PERMISSION CHECK ===\n\n";

try {
    // Menggunakan DB facade untuk query langsung
    $dashboardPermissions = [
        'dashboard.view',
        'dashboard.create',
        'dashboard.update',
        'dashboard.delete',
        'dashboard.approve',
        'dashboard.print',
        'dashboard.export'
    ];

    foreach ($dashboardPermissions as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();

        if ($permission) {
            echo "✅ FOUND: {$permissionName} (ID: {$permission->id})\n";
        } else {
            echo "❌ NOT FOUND: {$permissionName}\n";
        }
    }

    echo "\n=== DASHBOARD PERMISSION SUMMARY ===\n";

    $foundPermissions = [];
    $missingPermissions = [];

    foreach ($dashboardPermissions as $permissionName) {
        $permission = DB::table('permissions')->where('name', $permissionName)->first();
        if ($permission) {
            $foundPermissions[] = $permissionName;
        } else {
            $missingPermissions[] = $permissionName;
        }
    }

    echo "Found permissions: " . count($foundPermissions) . "\n";
    echo "Missing permissions: " . count($missingPermissions) . "\n";

    if (!empty($foundPermissions)) {
        echo "\n✅ Dashboard has these permissions:\n";
        foreach ($foundPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    }

    if (!empty($missingPermissions)) {
        echo "\n❌ Dashboard missing these permissions:\n";
        foreach ($missingPermissions as $perm) {
            echo "  - {$perm}\n";
        }
    }

    echo "\n=== RECOMMENDATION ===\n";
    if (count($foundPermissions) === 0) {
        echo "Dashboard tidak memiliki permission apapun di database.\n";
        echo "Rekomendasi: Hapus semua checkbox dashboard dan ganti dengan '-'.\n";
    } elseif (count($foundPermissions) === 1 && in_array('dashboard.view', $foundPermissions)) {
        echo "Dashboard hanya memiliki view permission.\n";
        echo "Rekomendasi: Pertahankan view checkbox, hapus yang lainnya.\n";
    } else {
        echo "Dashboard memiliki beberapa permission.\n";
        echo "Rekomendasi: Periksa permission mana yang benar-benar diperlukan.\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
