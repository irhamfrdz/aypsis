<?php

/**
 * Script untuk menambahkan permissions Vendor Kontainer Sewa ke database
 * dan assign ke user admin
 *
 * Usage: php setup_vendor_kontainer_sewa_permissions.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function logMessage($message, $type = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    echo "[$timestamp] [$type] $message\n";
}

try {
    logMessage("Memulai setup permissions Vendor Kontainer Sewa...");

    // Permissions yang akan dibuat
    $permissions = [
        'vendor-kontainer-sewa-view' => 'Melihat data vendor kontainer sewa',
        'vendor-kontainer-sewa-create' => 'Menambah data vendor kontainer sewa',
        'vendor-kontainer-sewa-edit' => 'Mengedit data vendor kontainer sewa',
        'vendor-kontainer-sewa-delete' => 'Menghapus data vendor kontainer sewa'
    logMessage("Mengecek dan menambahkan permissions...");

    // Insert permissions
    $newPermissions = 0;
    $existingPermissions = 0;

    foreach ($permissions as $permissionName => $description) {
        $exists = DB::table('permissions')->where('name', $permissionName)->exists();

        if (!$exists) {
            DB::table('permissions')->insert([
                'name' => $permissionName,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            logMessage("Permission '$permissionName' berhasil ditambahkan", 'SUCCESS');
            $newPermissions++;
        } else {
            logMessage("Permission '$permissionName' sudah ada", 'SKIP');
            $existingPermissions++;
        }
    }

    logMessage("Permissions baru: $newPermissions, sudah ada: $existingPermissions");

    // Cari user admin
    logMessage("Mencari user admin...");
    $adminUser = DB::table('users')->where('username', 'admin')->first();

    if (!$adminUser) {
        // Coba cari dengan email admin
        $adminUser = DB::table('users')->where('email', 'admin@admin.com')->first();

        if (!$adminUser) {
            logMessage("User admin tidak ditemukan! Pastikan ada user dengan username 'admin' atau email 'admin@admin.com'", 'ERROR');
            exit(1);
        }
    }

    logMessage("User admin ditemukan: {$adminUser->username} (ID: {$adminUser->id})", 'SUCCESS');

    // Assign permissions ke admin
    logMessage("Assign permissions ke user admin...");
    $newAssignments = 0;
    $existingAssignments = 0;

    foreach (array_keys($permissions) as $permissionName) {
        $permissionRecord = DB::table('permissions')->where('name', $permissionName)->first();

        if ($permissionRecord) {
            $exists = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permissionRecord->id)
                ->exists();

            if (!$exists) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permissionRecord->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                logMessage("Permission '$permissionName' berhasil di-assign ke admin", 'SUCCESS');
                $newAssignments++;
            } else {
                logMessage("Permission '$permissionName' sudah di-assign ke admin", 'SKIP');
                $existingAssignments++;
            }
        } else {
            logMessage("Permission '$permissionName' tidak ditemukan di database", 'WARNING');
        }
    }

    logMessage("Assignments baru: $newAssignments, sudah ada: $existingAssignments");

    // Verifikasi final
    logMessage("Melakukan verifikasi final...");
    $assignedPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->where('permissions.name', 'LIKE', 'vendor-kontainer-sewa-%')
        ->pluck('permissions.name')
        ->toArray();

    logMessage("Permissions vendor kontainer sewa yang berhasil di-assign:");
    foreach ($assignedPermissions as $permission) {
        logMessage("  - $permission", 'SUCCESS');
    }

    if (count($assignedPermissions) === count($permissions)) {
        logMessage("Setup permissions vendor kontainer sewa berhasil lengkap!", 'SUCCESS');
        logMessage("Total permissions: " . count($permissions));
        logMessage("User admin sekarang memiliki akses penuh ke menu Vendor Kontainer Sewa");
    } else {
        logMessage("Setup permissions tidak lengkap. Silakan cek log di atas", 'WARNING');
    }

} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage(), 'ERROR');
    logMessage("Stack trace: " . $e->getTraceAsString(), 'ERROR');
    exit(1);
}

logMessage("Script selesai dijalankan.");

    foreach ($adminPermissions as $permission) {
        echo "   ✓ {$permission->name}: {$permission->description}\n";
    }

    echo "\n🎉 Vendor Kontainer Sewa permissions setup completed successfully!\n";
    echo "🔗 You can now access the menu at: /master/vendor-kontainer-sewa\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
