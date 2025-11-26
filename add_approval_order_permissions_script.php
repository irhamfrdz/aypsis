<?php

/**
 * Script untuk menambahkan Approval Order Permissions
 * Jalankan dengan: php add_approval_order_permissions_script.php
 */

// Load Laravel environment
require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\DB;

try {
    echo "=== APPROVAL ORDER PERMISSIONS SETUP ===\n\n";
    
    // Define all approval-order permissions
    $permissions = [
        [
            'name' => 'approval-order-view',
            'description' => 'Melihat halaman approval order dan daftar order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-create',
            'description' => 'Menambah term pembayaran untuk order baru',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-update',
            'description' => 'Mengedit dan memperbarui term pembayaran order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-delete',
            'description' => 'Menghapus term pembayaran dari order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-approve',
            'description' => 'Menyetujui dan approve order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-reject',
            'description' => 'Menolak dan reject order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-print',
            'description' => 'Mencetak dokumen approval order',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'approval-order-export',
            'description' => 'Export data approval order ke Excel/PDF',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    $addedCount = 0;
    $existingCount = 0;

    foreach ($permissions as $permissionData) {
        // Check if permission already exists
        $exists = DB::table('permissions')
            ->where('name', $permissionData['name'])
            ->exists();
        
        if (!$exists) {
            DB::table('permissions')->insert($permissionData);
            echo "✅ Added: {$permissionData['name']} - {$permissionData['description']}\n";
            $addedCount++;
        } else {
            echo "⚪ Exists: {$permissionData['name']}\n";
            $existingCount++;
        }
    }

    echo "\n=== SUMMARY ===\n";
    echo "✅ New permissions added: {$addedCount}\n";
    echo "⚪ Already existing: {$existingCount}\n";
    echo "📊 Total approval-order permissions: " . count($permissions) . "\n\n";

    if ($addedCount > 0) {
        echo "🎯 SUCCESS: Approval Order permissions have been successfully added!\n";
        echo "\nNext steps:\n";
        echo "1. Go to Master User → Edit User\n";
        echo "2. Expand 'Sistem Persetujuan' section\n";
        echo "3. Configure 'Approval Order' permissions as needed\n";
        echo "4. Save the user permissions\n\n";
    } else {
        echo "ℹ️  All permissions already exist. No action needed.\n\n";
    }

    echo "=== AVAILABLE PERMISSIONS ===\n";
    foreach ($permissions as $perm) {
        echo "🔹 {$perm['name']}\n";
    }

    echo "\n=== SCRIPT COMPLETED ===\n";

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "Please check your database connection and Laravel configuration.\n";
    exit(1);
}