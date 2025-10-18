<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\DB;

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Adding Operational Management Permissions...\n\n";

try {
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
        'approval-surat-jalan-export' => 'Export Approval Surat Jalan',
    ];

    $addedCount = 0;
    $existingCount = 0;

    foreach ($operationalPermissions as $name => $description) {
        // Check if permission already exists
        $existing = DB::table('permissions')->where('name', $name)->first();
        
        if (!$existing) {
            DB::table('permissions')->insert([
                'name' => $name,
                'description' => $description,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            echo "✅ Added: {$name}\n";
            $addedCount++;
        } else {
            echo "⏭️  Exists: {$name}\n";
            $existingCount++;
        }
    }

    echo "\n📊 Summary:\n";
    echo "   • Added: {$addedCount} permissions\n";
    echo "   • Already existed: {$existingCount} permissions\n";
    echo "   • Total operational permissions: " . count($operationalPermissions) . "\n";

    echo "\n✨ All operational permissions have been processed successfully!\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}
