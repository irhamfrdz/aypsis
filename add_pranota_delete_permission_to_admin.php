<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Permission;

echo "=== Adding pranota-kontainer-sewa-delete Permission to Admin ===\n\n";

try {
    // Get or create the permission
    $permission = Permission::firstOrCreate(
        ['name' => 'pranota-kontainer-sewa-delete'],
        ['guard_name' => 'web', 'description' => 'Delete Pranota Kontainer Sewa']
    );

    echo "✓ Permission found/created: pranota-kontainer-sewa-delete (ID: {$permission->id})\n\n";

    // Get admin user
    $admin = User::where('username', 'admin')->first();

    if (!$admin) {
        echo "❌ Admin user not found!\n";
        exit(1);
    }

    echo "Admin user found: {$admin->name} (ID: {$admin->id})\n";

    // Check if admin already has the permission
    if ($admin->hasPermissionTo('pranota-kontainer-sewa-delete')) {
        echo "⏭️  Admin already has pranota-kontainer-sewa-delete permission\n";
    } else {
        $admin->givePermissionTo('pranota-kontainer-sewa-delete');
        echo "✅ Successfully granted pranota-kontainer-sewa-delete permission to admin\n";
    }

    // Verify
    $admin->refresh();
    $allPranotaPerms = $admin->permissions()
        ->where('name', 'like', 'pranota-kontainer-sewa-%')
        ->pluck('name');

    echo "\n📋 Admin's pranota-kontainer-sewa permissions:\n";
    foreach ($allPranotaPerms as $perm) {
        echo "   - {$perm}\n";
    }

    echo "\n✅ Done!\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
