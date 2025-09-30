<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

echo "=== CHECK TAGIHAN KONTAINER SEWA PERMISSIONS FOR MARLINA ===\n\n";

// Get user marlina
$user = User::where('username', 'marlina')->first();
if (!$user) {
    echo "User marlina not found!\n";
    exit(1);
}

echo "User: {$user->name} (ID: {$user->id})\n\n";

// Check if user has tagihan-kontainer-sewa-index permission
$hasTagihanPermission = $user->can('tagihan-kontainer-sewa-index');
echo "Has 'tagihan-kontainer-sewa-index' permission: " . ($hasTagihanPermission ? 'YES' : 'NO') . "\n";

// Get all permissions for this user
$userPermissions = $user->getAllPermissions();
echo "\nAll user permissions:\n";
foreach ($userPermissions as $perm) {
    if (strpos($perm->name, 'tagihan') !== false) {
        echo "- {$perm->name} (ID: {$perm->id})\n";
    }
}

// Check database directly
echo "\n=== DATABASE CHECK ===\n";
$tagihanPermission = Permission::where('name', 'tagihan-kontainer-sewa-index')->first();
if ($tagihanPermission) {
    echo "Permission 'tagihan-kontainer-sewa-index' exists in database (ID: {$tagihanPermission->id})\n";

    // Check if user has this permission
    $hasPermissionInDB = DB::table('model_has_permissions')
        ->where('model_type', 'App\Models\User')
        ->where('model_id', $user->id)
        ->where('permission_id', $tagihanPermission->id)
        ->exists();

    echo "User has permission in database: " . ($hasPermissionInDB ? 'YES' : 'NO') . "\n";
} else {
    echo "Permission 'tagihan-kontainer-sewa-index' NOT found in database!\n";
}

// Check form matrix permissions
echo "\n=== FORM MATRIX CHECK ===\n";
$userPermissionNames = $user->getAllPermissions()->pluck('name')->toArray();
$userController = new \App\Http\Controllers\UserController();
$matrixPermissions = $userController->testConvertPermissionsToMatrix($userPermissionNames);

if (isset($matrixPermissions['tagihan-kontainer-sewa'])) {
    echo "Tagihan Kontainer Sewa matrix permissions:\n";
    foreach ($matrixPermissions['tagihan-kontainer-sewa'] as $action => $value) {
        echo "- $action: " . ($value ? 'checked' : 'unchecked') . "\n";
    }
} else {
    echo "No tagihan-kontainer-sewa permissions found in matrix!\n";
}

echo "\n=== CONCLUSION ===\n";
if ($hasTagihanPermission) {
    echo "✓ User should see the menu\n";
} else {
    echo "✗ User will NOT see the menu - permission issue\n";
}
