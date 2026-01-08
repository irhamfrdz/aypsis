<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

try {
    // Define permissions for Pricelist Biaya Dokumen
    $permissions = [
        'master-pricelist-biaya-dokumen-view',
        'master-pricelist-biaya-dokumen-create',
        'master-pricelist-biaya-dokumen-edit',
        'master-pricelist-biaya-dokumen-delete',
    ];

    echo "Starting to add Pricelist Biaya Dokumen permissions...\n\n";

    foreach ($permissions as $permission) {
        // Check if permission already exists
        $existingPermission = Permission::where('name', $permission)->first();
        
        if ($existingPermission) {
            echo "✓ Permission '{$permission}' already exists (ID: {$existingPermission->id})\n";
        } else {
            // Create new permission
            Permission::create(['name' => $permission]);
            echo "✓ Permission '{$permission}' created successfully\n";
        }
    }

    echo "\n✅ All Pricelist Biaya Dokumen permissions have been processed!\n";
    echo "\nNext steps:\n";
    echo "1. Assign these permissions to appropriate roles/users\n";
    echo "2. Use the admin panel to manage user permissions\n\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
