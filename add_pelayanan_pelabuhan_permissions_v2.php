<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    echo "==============================================\n";
    echo "Adding Master Pelayanan Pelabuhan Permissions\n";
    echo "==============================================\n\n";

    $permissions = [
        [
            'name' => 'master-pelayanan-pelabuhan-view',
            'description' => 'Melihat data master pelayanan pelabuhan'
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-create',
            'description' => 'Membuat data master pelayanan pelabuhan'
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-edit',
            'description' => 'Mengedit data master pelayanan pelabuhan'
        ],
        [
            'name' => 'master-pelayanan-pelabuhan-delete',
            'description' => 'Menghapus data master pelayanan pelabuhan'
        ]
    ];

    $now = now();
    $addedCount = 0;
    $skippedCount = 0;

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $existing = DB::table('permissions')
            ->where('name', $permission['name'])
            ->first();

        if ($existing) {
            echo "⏭️  Skipped (already exists): {$permission['name']} (ID: {$existing->id})\n";
            $skippedCount++;
        } else {
            // Insert permission
            $id = DB::table('permissions')->insertGetId([
                'name' => $permission['name'],
                'description' => $permission['description'],
                'created_at' => $now,
                'updated_at' => $now
            ]);

            echo "✅ Added: {$permission['name']} (ID: {$id})\n";
            $addedCount++;
        }
    }

    DB::commit();

    echo "\n==============================================\n";
    echo "Summary:\n";
    echo "✅ Added: {$addedCount} permissions\n";
    echo "⏭️  Skipped: {$skippedCount} permissions\n";
    echo "==============================================\n\n";

    // Display all pelayanan pelabuhan permissions
    echo "Current Pelayanan Pelabuhan Permissions in Database:\n";
    echo "-----------------------------------------------------\n";
    $allPermissions = DB::table('permissions')
        ->where('name', 'like', '%pelayanan-pelabuhan%')
        ->orderBy('id')
        ->get(['id', 'name', 'description']);

    foreach ($allPermissions as $perm) {
        echo "ID: {$perm->id} | {$perm->name} | {$perm->description}\n";
    }

    echo "\n✅ Script completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Assign these permissions to admin role via UI\n";
    echo "2. Test the permission matrix in user edit page\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
