<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Permission;

echo "🔧 Adding Operational Permissions to Admin User...\n\n";

try {
    // Find admin user (assuming the first user or user with username 'admin')
    $adminUser = User::first();
    if (!$adminUser) {
        echo "❌ No users found. Please create a user first.\n";
        exit(1);
    }

    echo "👤 Admin User: {$adminUser->username} (ID: {$adminUser->id})\n\n";

    // Get all operational permissions
    $operationalPermissionNames = [
        'order-management-view',
        'order-management-create',
        'order-management-update',
        'order-management-delete',
        'order-management-print',
        'order-management-export',
        'surat-jalan-view',
        'surat-jalan-create',
        'surat-jalan-update',
        'surat-jalan-delete',
        'surat-jalan-print',
        'surat-jalan-export',
        'tanda-terima-view',
        'tanda-terima-create',
        'tanda-terima-update',
        'tanda-terima-delete',
        'tanda-terima-print',
        'tanda-terima-export',
        'gate-in-view',
        'gate-in-create',
        'gate-in-update',
        'gate-in-delete',
        'gate-in-print',
        'gate-in-export',
        'pranota-surat-jalan-view',
        'pranota-surat-jalan-create',
        'pranota-surat-jalan-update',
        'pranota-surat-jalan-delete',
        'pranota-surat-jalan-print',
        'pranota-surat-jalan-export',
        'approval-surat-jalan-view',
        'approval-surat-jalan-approve',
        'approval-surat-jalan-reject',
        'approval-surat-jalan-print',
        'approval-surat-jalan-export',
    ];

    $operationalPermissions = Permission::whereIn('name', $operationalPermissionNames)->get();
    $operationalPermissionIds = $operationalPermissions->pluck('id')->toArray();

    echo "🔍 Found {$operationalPermissions->count()} operational permissions:\n";
    foreach ($operationalPermissions as $permission) {
        echo "   • {$permission->name}\n";
    }
    echo "\n";

    // Get current user permissions
    $currentPermissionIds = $adminUser->permissions()->pluck('permissions.id')->toArray();
    
    // Merge with operational permissions (without duplicates)
    $allPermissionIds = array_unique(array_merge($currentPermissionIds, $operationalPermissionIds));
    
    echo "📊 Permission Summary:\n";
    echo "   • Current permissions: " . count($currentPermissionIds) . "\n";
    echo "   • Operational permissions: " . count($operationalPermissionIds) . "\n";
    echo "   • Total after merge: " . count($allPermissionIds) . "\n\n";

    // Sync permissions
    $adminUser->permissions()->sync($allPermissionIds);

    echo "✅ Successfully added operational permissions to admin user!\n";
    echo "   • User: {$adminUser->username}\n";
    echo "   • Total permissions: " . count($allPermissionIds) . "\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
