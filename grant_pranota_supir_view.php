<?php

// Grant pranota-supir.view permission to user test4
require_once 'vendor/autoload.php';

use App\Models\User;
use App\Models\Permission;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "🔧 Granting pranota-supir.view permission to user test4...\n";

// Find or create the permission
$permission = Permission::firstOrCreate(['name' => 'pranota-supir.view']);

// Get current permissions and add the new one
$currentPermissionIds = $user->permissions()->pluck('id')->toArray();
$newPermissionIds = array_merge($currentPermissionIds, [$permission->id]);

// Grant the permission to user test4 using sync
$user->permissions()->sync($newPermissionIds);

echo "✅ SUCCESS: Granted pranota-supir.view permission to user test4\n";

// Verify the permission was granted
$hasPermission = $user->hasPermissionTo('pranota-supir.view');
echo "🔍 Verification: " . ($hasPermission ? "✅ Permission granted successfully" : "❌ Permission grant failed") . "\n";

// Check if menu should now be visible
$hasAnyPranotaPermission = $user->hasPermissionTo('pranota-supir.view') ||
                         $user->hasPermissionTo('pranota-supir.create') ||
                         $user->hasPermissionTo('pranota-supir.update') ||
                         $user->hasPermissionTo('pranota-supir.delete') ||
                         $user->hasPermissionTo('pranota-supir.approve') ||
                         $user->hasPermissionTo('pranota-supir.print') ||
                         $user->hasPermissionTo('pranota-supir.export');

echo "📊 Menu visibility: " . ($hasAnyPranotaPermission ? "✅ Menu should now be visible" : "❌ Menu will remain hidden") . "\n";

echo "\n🎯 RESULT:\n";
echo "User test4 can now access the 'Pranota Supir' menu in the sidebar.\n";
echo "The menu will show both 'Buat Pranota Supir' and 'Daftar Pranota Supir' options.\n";
