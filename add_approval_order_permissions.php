<?php

require __DIR__ . '/vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Database\Capsule\Manager as Capsule;

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Bootstrap Laravel
$kernel->bootstrap();

// Define approval-order permissions
$permissions = [
    [
        'name' => 'approval-order-view',
        'description' => 'Melihat halaman approval order'
    ],
    [
        'name' => 'approval-order-create', 
        'description' => 'Menambah term pembayaran order'
    ],
    [
        'name' => 'approval-order-update',
        'description' => 'Mengedit term pembayaran order'
    ],
    [
        'name' => 'approval-order-delete',
        'description' => 'Menghapus term pembayaran order'
    ],
    [
        'name' => 'approval-order-approve',
        'description' => 'Menyetujui approval order'
    ],
    [
        'name' => 'approval-order-reject',
        'description' => 'Menolak approval order'
    ],
    [
        'name' => 'approval-order-print',
        'description' => 'Mencetak dokumen approval order'
    ],
    [
        'name' => 'approval-order-export',
        'description' => 'Export data approval order'
    ]
];

echo "Adding approval-order permissions...\n";

foreach ($permissions as $permissionData) {
    $existing = Permission::where('name', $permissionData['name'])->first();
    
    if (!$existing) {
        Permission::create($permissionData);
        echo "✓ Added permission: {$permissionData['name']}\n";
    } else {
        echo "- Permission already exists: {$permissionData['name']}\n";
    }
}

echo "\nApproval-order permissions setup completed!\n";