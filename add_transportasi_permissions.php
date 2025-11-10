<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as DB;
use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    // Check if permissions already exist
    $existingPermissions = DB::table('permissions')
        ->whereIn('name', [
            'master-transportasi-view',
            'master-transportasi-create', 
            'master-transportasi-update',
            'master-transportasi-delete',
            'master-transportasi-print',
            'master-transportasi-export'
        ])
        ->pluck('name')
        ->toArray();

    $permissionsToAdd = [
        [
            'name' => 'master-transportasi-view',
            'display_name' => 'View Master Transportasi', 
            'description' => 'Permission to view transportation data',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'master-transportasi-create',
            'display_name' => 'Create Master Transportasi',
            'description' => 'Permission to create transportation data', 
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'master-transportasi-update', 
            'display_name' => 'Update Master Transportasi',
            'description' => 'Permission to update transportation data',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'master-transportasi-delete',
            'display_name' => 'Delete Master Transportasi', 
            'description' => 'Permission to delete transportation data',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'master-transportasi-print',
            'display_name' => 'Print Master Transportasi',
            'description' => 'Permission to print transportation data',
            'created_at' => now(), 
            'updated_at' => now()
        ],
        [
            'name' => 'master-transportasi-export',
            'display_name' => 'Export Master Transportasi',
            'description' => 'Permission to export transportation data',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    $addedCount = 0;
    foreach ($permissionsToAdd as $permission) {
        if (!in_array($permission['name'], $existingPermissions)) {
            DB::table('permissions')->insert($permission);
            $addedCount++;
            echo "✅ Added permission: {$permission['name']}\n";
        } else {
            echo "⚠️  Permission already exists: {$permission['name']}\n";
        }
    }

    echo "\n🎉 Successfully processed master transportasi permissions!\n";
    echo "📊 Added: {$addedCount} new permissions\n";
    echo "🔄 Skipped: " . (count($permissionsToAdd) - $addedCount) . " existing permissions\n";

} catch (Exception $e) {
    echo "❌ Error adding transportasi permissions: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}