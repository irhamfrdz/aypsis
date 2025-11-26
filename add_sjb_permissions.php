<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check if permissions already exist
    $permissions = [
        [
            'name' => 'surat-jalan-bongkaran-view',
            'description' => 'View surat jalan bongkaran',
        ],
        [
            'name' => 'surat-jalan-bongkaran-create',
            'description' => 'Create surat jalan bongkaran',
        ],
        [
            'name' => 'surat-jalan-bongkaran-update',
            'description' => 'Update surat jalan bongkaran',
        ],
        [
            'name' => 'surat-jalan-bongkaran-delete',
            'description' => 'Delete surat jalan bongkaran',
        ],
        [
            'name' => 'surat-jalan-bongkaran-print',
            'description' => 'Print surat jalan bongkaran',
        ],
        [
            'name' => 'surat-jalan-bongkaran-export',
            'description' => 'Export surat jalan bongkaran',
        ],
    ];

    $insertedCount = 0;

    foreach ($permissions as $permission) {
        // Check if permission exists
        $existingPermission = DB::table('permissions')
            ->where('name', $permission['name'])
            ->first();

        if (!$existingPermission) {
            DB::table('permissions')->insert([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo "✅ Permission '{$permission['name']}' created successfully\n";
            $insertedCount++;
        } else {
            echo "ℹ️  Permission '{$permission['name']}' already exists\n";
        }
    }

    if ($insertedCount > 0) {
        echo "\n🎉 Successfully created {$insertedCount} new permission(s) for Surat Jalan Bongkaran!\n";
        echo "\n📝 You can now assign these permissions to users through the user management interface.\n";
    } else {
        echo "\n✅ All Surat Jalan Bongkaran permissions already exist in the database.\n";
    }

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}