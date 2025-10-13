<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 MANUALLY ASSIGNING TUJUAN KIRIM PERMISSIONS TO ADMIN\n";
echo "======================================================\n\n";

// Find admin user
$admin = \App\Models\User::where('username', 'admin')->first();
if (!$admin) {
    echo "❌ Admin user not found!\n";
    exit;
}

echo "👤 Found admin user: {$admin->username} (ID: {$admin->id})\n\n";

// Find tujuan-kirim permissions  
$permissions = \App\Models\Permission::where('name', 'like', '%tujuan-kirim%')->get();
echo "📋 Found " . $permissions->count() . " tujuan-kirim permissions:\n";

foreach($permissions as $permission) {
    echo "   - {$permission->name} (ID: {$permission->id})\n";
}

echo "\n🔗 Assigning permissions to admin...\n";

// Get permission IDs
$permissionIds = $permissions->pluck('id');

// Get existing admin permissions
$existingPermissions = $admin->permissions()->pluck('id');

// Merge new permissions with existing ones
$allPermissions = $existingPermissions->merge($permissionIds)->unique();

// Sync permissions (this will add new ones and keep existing ones)
$admin->permissions()->sync($allPermissions);

echo "   ✅ Synced " . $permissionIds->count() . " tujuan-kirim permissions to admin\n";

echo "\n✅ VERIFICATION:\n";
echo "================\n";

$hasView = $admin->hasPermissionTo('master-tujuan-kirim-view');
echo "Has master-tujuan-kirim-view: " . ($hasView ? "✅ YES" : "❌ NO") . "\n";

$hasCreate = $admin->hasPermissionTo('master-tujuan-kirim-create');  
echo "Has master-tujuan-kirim-create: " . ($hasCreate ? "✅ YES" : "❌ NO") . "\n";

$hasUpdate = $admin->hasPermissionTo('master-tujuan-kirim-update');
echo "Has master-tujuan-kirim-update: " . ($hasUpdate ? "✅ YES" : "❌ NO") . "\n";

$hasDelete = $admin->hasPermissionTo('master-tujuan-kirim-delete');
echo "Has master-tujuan-kirim-delete: " . ($hasDelete ? "✅ YES" : "❌ NO") . "\n";

if ($hasView && $hasCreate && $hasUpdate && $hasDelete) {
    echo "\n🎉 SUCCESS! Admin now has ALL tujuan-kirim permissions!\n";
    echo "📱 The 'Tujuan Kirim' menu should now appear in sidebar.\n\n";
    echo "💡 CLEAR BROWSER CACHE AND TEST:\n";
    echo "1. Clear browser cache completely\n";
    echo "2. Or open in Incognito/Private browsing mode\n";
    echo "3. Go to: http://localhost:8000\n";
    echo "4. Login as admin\n";
    echo "5. Check sidebar: Master Data → Tujuan Kirim\n";
} else {
    echo "\n❌ Some permissions are still missing!\n";
}