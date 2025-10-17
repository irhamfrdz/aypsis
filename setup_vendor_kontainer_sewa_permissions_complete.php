<?php

/**
 * Script untuk menambahkan permissions Vendor Kontainer Sewa yang lengkap ke database
 * dan assign ke user admin
 *
 * Usage: php setup_vendor_kontainer_sewa_permissions_complete.php
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
    logMessage("Memulai setup permissions Vendor Kontainer Sewa yang lengkap...");

    // Permissions yang akan dibuat (lengkap)
    $permissions = [
        'vendor-kontainer-sewa-view' => 'Melihat data vendor kontainer sewa',
        'vendor-kontainer-sewa-create' => 'Menambah data vendor kontainer sewa',
        'vendor-kontainer-sewa-edit' => 'Mengedit data vendor kontainer sewa',
        'vendor-kontainer-sewa-delete' => 'Menghapus data vendor kontainer sewa',
        'vendor-kontainer-sewa-export' => 'Export data vendor kontainer sewa',
        'vendor-kontainer-sewa-print' => 'Print data vendor kontainer sewa'
    ];

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
            // Update deskripsi jika sudah ada
            DB::table('permissions')
                ->where('name', $permissionName)
                ->update([
                    'description' => $description,
                    'updated_at' => now()
                ]);
            logMessage("Permission '$permissionName' sudah ada - deskripsi di-update", 'INFO');
            $existingPermissions++;
        }
    }

    logMessage("Summary: $newPermissions permissions baru, $existingPermissions permissions sudah ada");

    // Cari user admin
    $adminUser = DB::table('users')->where('username', 'admin')->first();

    if (!$adminUser) {
        logMessage("User admin tidak ditemukan!", 'ERROR');
        exit(1);
    }

    logMessage("User admin ditemukan: ID {$adminUser->id}");

    // Get permission IDs
    $permissionIds = DB::table('permissions')
        ->whereIn('name', array_keys($permissions))
        ->pluck('id')
        ->toArray();

    if (empty($permissionIds)) {
        logMessage("Tidak ada permission yang ditemukan!", 'ERROR');
        exit(1);
    }

    logMessage("Permissions ditemukan: " . count($permissionIds) . " permissions");

    // Assign permissions ke admin
    $assignedPermissions = 0;
    $skippedPermissions = 0;

    foreach ($permissionIds as $permissionId) {
        $exists = DB::table('user_permissions')
            ->where('user_id', $adminUser->id)
            ->where('permission_id', $permissionId)
            ->exists();

        if (!$exists) {
            DB::table('user_permissions')->insert([
                'user_id' => $adminUser->id,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $assignedPermissions++;
        } else {
            $skippedPermissions++;
        }
    }

    logMessage("Permissions assigned ke admin: $assignedPermissions baru, $skippedPermissions sudah ada");

    // Cek hasil akhir
    $adminPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $adminUser->id)
        ->where('permissions.name', 'LIKE', 'vendor-kontainer-sewa-%')
        ->select('permissions.name', 'permissions.description')
        ->get();

    logMessage("Permissions vendor kontainer sewa yang berhasil di-assign:");
    foreach ($adminPermissions as $permission) {
        logMessage("- {$permission->name}: {$permission->description}");
    }

    if ($adminPermissions->count() === count($permissions)) {
        logMessage("Setup permissions vendor kontainer sewa berhasil lengkap!", 'SUCCESS');
        logMessage("User admin sekarang memiliki akses penuh ke menu Vendor Kontainer Sewa");
        logMessage("Total permissions: " . $adminPermissions->count());
        
        // Log informasi tambahan
        logMessage("\n=== INFORMASI AKSES ===");
        logMessage("✅ View: Dapat melihat daftar vendor kontainer sewa");
        logMessage("✅ Create: Dapat menambah vendor kontainer sewa baru");
        logMessage("✅ Edit: Dapat mengedit data vendor kontainer sewa");
        logMessage("✅ Delete: Dapat menghapus vendor kontainer sewa");
        logMessage("✅ Export: Dapat export data vendor kontainer sewa");
        logMessage("✅ Print: Dapat print data vendor kontainer sewa");
        
    } else {
        logMessage("Ada masalah dalam assignment permissions", 'WARNING');
        logMessage("Expected: " . count($permissions) . ", Actual: " . $adminPermissions->count());
    }

} catch (Exception $e) {
    logMessage("Error: " . $e->getMessage(), 'ERROR');
    logMessage("Stack trace: " . $e->getTraceAsString(), 'DEBUG');
    exit(1);
}

if (php_sapi_name() === 'cli') {
    echo "\n" . str_repeat("=", 80) . "\n";
    echo "🎉 Vendor Kontainer Sewa permissions setup completed successfully!\n";
    echo "🔗 You can now access the menu at: /vendor-kontainer-sewa\n";
    echo "👤 Admin user can manage vendor kontainer sewa with full permissions\n";
    echo str_repeat("=", 80) . "\n";
}