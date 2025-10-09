<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    echo "=== CHECKING ADMIN PERMISSIONS ===\n\n";

    // Check admin user permissions
    $adminPermissions = DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', 1)
        ->where('permissions.name', 'like', '%ob%')
        ->select('permissions.name')
        ->get();

    if ($adminPermissions->count() > 0) {
        echo "✅ Admin user has OB permissions:\n";
        foreach ($adminPermissions as $permission) {
            echo "  - {$permission->name}\n";
        }
    } else {
        echo "❌ Admin user does not have any OB permissions!\n";
    }

    echo "\n";

    // Check if routes exist
    echo "=== CHECKING ROUTES ===\n";
    if (Route::has('pembayaran-dp-ob.index')) {
        echo "✅ Route pembayaran-dp-ob.index exists\n";
    } else {
        echo "❌ Route pembayaran-dp-ob.index does not exist\n";
    }

    if (Route::has('pembayaran-ob.index')) {
        echo "✅ Route pembayaran-ob.index exists\n";
    } else {
        echo "❌ Route pembayaran-ob.index does not exist\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
