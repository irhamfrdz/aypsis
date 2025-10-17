<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Setup database connection
$capsule = new DB;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'aypsis',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

try {
    echo "=== Menambahkan Permission Khusus Surat Jalan Approval ===\n\n";

    // Daftar permission untuk approval surat jalan
    $permissions = [
        // Level 1 Approval
        [
            'name' => 'surat-jalan-approval-level-1.view',
            'description' => 'View surat jalan yang perlu approval level 1',
            'guard_name' => 'web'
        ],
        [
            'name' => 'surat-jalan-approval-level-1.approve',
            'description' => 'Approve surat jalan level 1',
            'guard_name' => 'web'
        ],

        // Level 2 Approval
        [
            'name' => 'surat-jalan-approval-level-2.view',
            'description' => 'View surat jalan yang perlu approval level 2',
            'guard_name' => 'web'
        ],
        [
            'name' => 'surat-jalan-approval-level-2.approve',
            'description' => 'Approve surat jalan level 2',
            'guard_name' => 'web'
        ],

        // Permission umum untuk dashboard approval
        [
            'name' => 'surat-jalan-approval.dashboard',
            'description' => 'Access to surat jalan approval dashboard',
            'guard_name' => 'web'
        ],
    ];

    foreach ($permissions as $permissionData) {
        $permission = Permission::firstOrCreate(
            ['name' => $permissionData['name'], 'guard_name' => $permissionData['guard_name']],
            $permissionData
        );

        if ($permission->wasRecentlyCreated) {
            echo "✓ Permission dibuat: {$permissionData['name']}\n";
        } else {
            echo "- Permission sudah ada: {$permissionData['name']}\n";
        }
    }

    echo "\n=== Menambahkan Permission ke Role Admin ===\n";

    // Berikan semua permission ke role admin
    $adminRole = Role::where('name', 'admin')->first();
    if ($adminRole) {
        foreach ($permissions as $permissionData) {
            $permission = Permission::where('name', $permissionData['name'])->first();
            if ($permission && !$adminRole->hasPermissionTo($permission)) {
                $adminRole->givePermissionTo($permission);
                echo "✓ Permission {$permissionData['name']} diberikan ke admin\n";
            }
        }
    } else {
        echo "⚠ Role admin tidak ditemukan\n";
    }

    echo "\n=== Membuat Role Khusus untuk Approver ===\n";

    // Buat role untuk approver level 1
    $approverLevel1 = Role::firstOrCreate([
        'name' => 'surat-jalan-approver-level-1',
        'guard_name' => 'web'
    ], [
        'description' => 'Approver untuk surat jalan level 1'
    ]);

    if ($approverLevel1->wasRecentlyCreated) {
        echo "✓ Role dibuat: surat-jalan-approver-level-1\n";
    } else {
        echo "- Role sudah ada: surat-jalan-approver-level-1\n";
    }

    // Berikan permission level 1 ke role approver level 1
    $level1Permissions = [
        'surat-jalan-approval-level-1.view',
        'surat-jalan-approval-level-1.approve',
        'surat-jalan-approval.dashboard'
    ];

    foreach ($level1Permissions as $permName) {
        $permission = Permission::where('name', $permName)->first();
        if ($permission && !$approverLevel1->hasPermissionTo($permission)) {
            $approverLevel1->givePermissionTo($permission);
            echo "✓ Permission {$permName} diberikan ke approver-level-1\n";
        }
    }

    // Buat role untuk approver level 2
    $approverLevel2 = Role::firstOrCreate([
        'name' => 'surat-jalan-approver-level-2',
        'guard_name' => 'web'
    ], [
        'description' => 'Approver untuk surat jalan level 2'
    ]);

    if ($approverLevel2->wasRecentlyCreated) {
        echo "✓ Role dibuat: surat-jalan-approver-level-2\n";
    } else {
        echo "- Role sudah ada: surat-jalan-approver-level-2\n";
    }

    // Berikan permission level 2 ke role approver level 2
    $level2Permissions = [
        'surat-jalan-approval-level-2.view',
        'surat-jalan-approval-level-2.approve',
        'surat-jalan-approval.dashboard'
    ];

    foreach ($level2Permissions as $permName) {
        $permission = Permission::where('name', $permName)->first();
        if ($permission && !$approverLevel2->hasPermissionTo($permission)) {
            $approverLevel2->givePermissionTo($permission);
            echo "✓ Permission {$permName} diberikan ke approver-level-2\n";
        }
    }

    echo "\n=== Summary ===\n";
    echo "✓ Semua permission surat jalan approval berhasil dibuat\n";
    echo "✓ Role approver level 1 dan 2 berhasil dibuat\n";
    echo "✓ Permission sudah diberikan ke role yang sesuai\n";
    echo "\nCatatan: Sekarang Anda bisa assign role 'surat-jalan-approver-level-1' atau 'surat-jalan-approver-level-2' ke user yang akan menjadi approver\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\nDone!\n";
