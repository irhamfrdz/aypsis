<?php

require_once 'vendor/autoload.php';

// Load Laravel configuration
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    echo "Cleaning up remaining old tujuan-kegiatan-utama permissions...\n";

    // Get all permissions that contain 'tujuan-kegiatan-utama'
    $oldPermissions = DB::table('permissions')
        ->where('name', 'like', '%tujuan-kegiatan-utama%')
        ->get();

    foreach ($oldPermissions as $permission) {
        echo "Processing permission: {$permission->name}\n";

        // Remove from all users
        $deletedUserPermissions = DB::table('user_permissions')
            ->where('permission_id', $permission->id)
            ->delete();

        if ($deletedUserPermissions > 0) {
            echo "  - Removed from {$deletedUserPermissions} user(s)\n";
        }

        // Delete the permission itself
        DB::table('permissions')->where('id', $permission->id)->delete();
        echo "  - Deleted permission from table\n";
    }

    echo "\n✅ All old tujuan-kegiatan-utama permissions cleaned up!\n";

    // Verify cleanup
    $remainingOldPermissions = DB::table('permissions')
        ->where('name', 'like', '%tujuan-kegiatan-utama%')
        ->count();

    if ($remainingOldPermissions == 0) {
        echo "✅ Verification: No old permissions remaining\n";
    } else {
        echo "❌ Warning: {$remainingOldPermissions} old permissions still exist\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}
