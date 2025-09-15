<?php

// Check user test4 permissions in database
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\DB;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Checking User test4 Permissions in Database\n";
echo "===============================================\n\n";

// Get user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "👤 User test4 found (ID: {$user->id})\n\n";

// Check all permissions for user test4
$permissions = DB::table('model_has_permissions')
    ->join('permissions', 'model_has_permissions.permission_id', '=', 'permissions.id')
    ->where('model_has_permissions.model_type', 'App\Models\User')
    ->where('model_has_permissions.model_id', $user->id)
    ->select('permissions.name', 'permissions.id')
    ->get();

echo "📋 Current permissions for user test4:\n";
if ($permissions->count() > 0) {
    foreach ($permissions as $perm) {
        echo "  ✅ {$perm->name} (ID: {$perm->id})\n";
    }
} else {
    echo "  ❌ No permissions found\n";
}

echo "\n🔍 Checking for pranota-related permissions:\n";
$pranotaPermissions = $permissions->filter(function($perm) {
    return strpos($perm->name, 'pranota') !== false;
});

if ($pranotaPermissions->count() > 0) {
    foreach ($pranotaPermissions as $perm) {
        echo "  ✅ {$perm->name}\n";
    }
} else {
    echo "  ❌ No pranota permissions found\n";
}

// Check if pranota-supir permission exists in permissions table
$pranotaSupirPerm = DB::table('permissions')
    ->where('name', 'pranota-supir')
    ->first();

if ($pranotaSupirPerm) {
    echo "\n✅ Permission 'pranota-supir' exists in permissions table (ID: {$pranotaSupirPerm->id})\n";

    // Check if user has this permission
    $hasPermission = DB::table('model_has_permissions')
        ->where('model_type', 'App\Models\User')
        ->where('model_id', $user->id)
        ->where('permission_id', $pranotaSupirPerm->id)
        ->exists();

    echo "👤 User test4 has 'pranota-supir' permission: " . ($hasPermission ? '✅ YES' : '❌ NO') . "\n";
} else {
    echo "\n❌ Permission 'pranota-supir' does NOT exist in permissions table\n";
}

// Check for pranota-supir.view permission
$pranotaSupirViewPerm = DB::table('permissions')
    ->where('name', 'pranota-supir.view')
    ->first();

if ($pranotaSupirViewPerm) {
    echo "\n✅ Permission 'pranota-supir.view' exists in permissions table (ID: {$pranotaSupirViewPerm->id})\n";

    // Check if user has this permission
    $hasViewPermission = DB::table('model_has_permissions')
        ->where('model_type', 'App\Models\User')
        ->where('model_id', $user->id)
        ->where('permission_id', $pranotaSupirViewPerm->id)
        ->exists();

    echo "👤 User test4 has 'pranota-supir.view' permission: " . ($hasViewPermission ? '✅ YES' : '❌ NO') . "\n";
} else {
    echo "\n❌ Permission 'pranota-supir.view' does NOT exist in permissions table\n";
}

echo "\n🔧 RECOMMENDATIONS:\n";
if (!$pranotaSupirViewPerm) {
    echo "1. Create the 'pranota-supir.view' permission: php artisan permission:create-permission pranota-supir.view\n";
    echo "2. Or create the 'pranota-supir' permission: php artisan permission:create-permission pranota-supir\n";
}

if ($pranotaSupirViewPerm && !$hasViewPermission) {
    echo "3. Grant permission to user test4: php artisan permission:grant-user-permission test4 pranota-supir.view\n";
}

echo "4. Clear permission cache: php artisan permission:cache-reset\n";
echo "5. Clear application cache: php artisan cache:clear\n";

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
