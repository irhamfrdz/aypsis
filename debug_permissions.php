<?php

require_once 'vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Permission;

$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "🔍 Debugging Permission Issue\n";
echo "=============================\n\n";

// Find user test4
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found!\n";
    exit(1);
}

echo "👤 User: {$user->username} (ID: {$user->id})\n\n";

// Check what permissions exist in the database
echo "📋 All Permissions in Database:\n";
$allPermissions = Permission::all();
foreach ($allPermissions as $perm) {
    echo "  - {$perm->name} (ID: {$perm->id})\n";
}

echo "\n";

// Check user's current permissions
echo "👤 User test4 Current Permissions:\n";
$userPermissions = $user->permissions;
if ($userPermissions->isEmpty()) {
    echo "❌ No permissions found for user test4!\n";
} else {
    foreach ($userPermissions as $perm) {
        echo "  - {$perm->name} (ID: {$perm->id})\n";
    }
}

echo "\n";

// Check if the required permissions exist
$requiredPermissions = [
    'master-karyawan.view',
    'master-user.view',
    'master-kontainer.view',
    'master-tujuan.view',
    'master-kegiatan.view',
    'master-permission.view',
    'master-mobil.view'
];

echo "🔍 Checking Required Permissions:\n";
$missingPermissions = [];

foreach ($requiredPermissions as $permName) {
    $permission = Permission::where('name', $permName)->first();
    if ($permission) {
        echo "  ✅ {$permName} exists (ID: {$permission->id})\n";

        // Check if user has this permission
        $hasPermission = $user->permissions->contains('id', $permission->id);
        if ($hasPermission) {
            echo "     👤 User HAS this permission\n";
        } else {
            echo "     👤 User MISSING this permission\n";
            $missingPermissions[] = $permName;
        }
    } else {
        echo "  ❌ {$permName} does NOT exist in database\n";
        $missingPermissions[] = $permName;
    }
}

echo "\n📊 Summary:\n";
echo "  - Total permissions in DB: " . $allPermissions->count() . "\n";
echo "  - User permissions: " . $userPermissions->count() . "\n";
echo "  - Missing permissions: " . count($missingPermissions) . "\n";

if (!empty($missingPermissions)) {
    echo "\n⚠️  Missing Permissions:\n";
    foreach ($missingPermissions as $perm) {
        echo "  - {$perm}\n";
    }
}
