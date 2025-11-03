<?php

// Load Laravel application
require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

try {
    echo "Adding Pembayaran Surat Jalan permissions...\n";
    
    // Permissions untuk Pembayaran Surat Jalan
    $permissions = [
        [
            'name' => 'pembayaran-surat-jalan-view',
            'description' => 'View pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-create', 
            'description' => 'Create pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-update',
            'description' => 'Update pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-delete',
            'description' => 'Delete pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-approve',
            'description' => 'Approve pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-print',
            'description' => 'Print pembayaran surat jalan'
        ],
        [
            'name' => 'pembayaran-surat-jalan-export',
            'description' => 'Export pembayaran surat jalan'
        ]
    ];

    foreach ($permissions as $permissionData) {
        // Cek apakah permission sudah ada
        $existing = Permission::where('name', $permissionData['name'])->first();

        if (!$existing) {
            $permission = new Permission();
            $permission->name = $permissionData['name'];
            $permission->description = $permissionData['description'];
            $permission->save();
            
            echo "✅ Added permission: {$permissionData['name']}\n";
        } else {
            echo "⚠️  Permission already exists: {$permissionData['name']}\n";
        }
    }

    echo "\n🎉 Pembayaran Surat Jalan permissions setup completed!\n";
    echo "\nAvailable permissions:\n";
    foreach ($permissions as $permission) {
        echo "- {$permission['name']}: {$permission['description']}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}