<?php
// Script untuk menambahkan permissions Operational Management Modules

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    echo "🔧 Adding Operational Management Permissions...\n\n";

    // Define all operational permissions
    $operationalPermissions = [
        // Order Management
        'order-management-view' => 'View Order Management',
        'order-management-create' => 'Create Order Management',
        'order-management-update' => 'Update Order Management',
        'order-management-delete' => 'Delete Order Management',
        'order-management-print' => 'Print Order Management',
        'order-management-export' => 'Export Order Management',

        // Surat Jalan
        'surat-jalan-view' => 'View Surat Jalan',
        'surat-jalan-create' => 'Create Surat Jalan',
        'surat-jalan-update' => 'Update Surat Jalan',
        'surat-jalan-delete' => 'Delete Surat Jalan',
        'surat-jalan-print' => 'Print Surat Jalan',
        'surat-jalan-export' => 'Export Surat Jalan',

        // Tanda Terima
        'tanda-terima-view' => 'View Tanda Terima',
        'tanda-terima-create' => 'Create Tanda Terima',
        'tanda-terima-update' => 'Update Tanda Terima',
        'tanda-terima-delete' => 'Delete Tanda Terima',
        'tanda-terima-print' => 'Print Tanda Terima',
        'tanda-terima-export' => 'Export Tanda Terima',

        // Gate In
        'gate-in-view' => 'View Gate In',
        'gate-in-create' => 'Create Gate In',
        'gate-in-update' => 'Update Gate In',
        'gate-in-delete' => 'Delete Gate In',
        'gate-in-print' => 'Print Gate In',
        'gate-in-export' => 'Export Gate In',

        // Pranota Surat Jalan
        'pranota-surat-jalan-view' => 'View Pranota Surat Jalan',
        'pranota-surat-jalan-create' => 'Create Pranota Surat Jalan',
        'pranota-surat-jalan-update' => 'Update Pranota Surat Jalan',
        'pranota-surat-jalan-delete' => 'Delete Pranota Surat Jalan',
        'pranota-surat-jalan-print' => 'Print Pranota Surat Jalan',
        'pranota-surat-jalan-export' => 'Export Pranota Surat Jalan',

        // Approval Surat Jalan
        'approval-surat-jalan-view' => 'View Approval Surat Jalan',
        'approval-surat-jalan-approve' => 'Approve Surat Jalan',
        'approval-surat-jalan-reject' => 'Reject Surat Jalan',
        'approval-surat-jalan-print' => 'Print Approval Surat Jalan',
        'approval-surat-jalan-export' => 'Export Approval Surat Jalan'
    ];

    foreach ($operationalPermissions as $permissionName => $description) {
        $permission = \App\Models\Permission::firstOrCreate(
            ['name' => $permissionName],
            ['description' => $description]
        );

        if ($permission->wasRecentlyCreated) {
            echo "✅ Created: {$permissionName} - {$description}\n";
        } else {
            echo "ℹ️  Exists: {$permissionName}\n";
        }
    }

    echo "\n✅ All operational management permissions processed successfully!\n\n";

    // Count total operational permissions
    $totalCount = \App\Models\Permission::where('name', 'like', 'order-management%')
        ->orWhere('name', 'like', 'surat-jalan%')
        ->orWhere('name', 'like', 'tanda-terima%')
        ->orWhere('name', 'like', 'gate-in%')
        ->orWhere('name', 'like', 'pranota-surat-jalan%')
        ->orWhere('name', 'like', 'approval-surat-jalan%')
        ->count();

    echo "📊 Total operational permissions in database: {$totalCount}\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}