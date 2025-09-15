<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Adding Perbaikan Kontainer Permissions to ALL Users ===\n";

// Get all users
$users = User::all();
echo "Found {$users->count()} users\n";

// Get permission IDs for perbaikan-kontainer
$permissionIds = DB::table('permissions')
    ->where('name', 'like', 'perbaikan-kontainer%')
    ->pluck('id')
    ->toArray();

echo "Found " . count($permissionIds) . " perbaikan-kontainer permission IDs\n";

foreach($users as $user) {
    echo "\nProcessing user: {$user->username} (ID: {$user->id})\n";

    // Check existing permissions
    $existingCount = $user->permissions()->whereIn('id', $permissionIds)->count();
    echo "Existing perbaikan-kontainer permissions: $existingCount\n";

    if ($existingCount < count($permissionIds)) {
        // Attach missing permissions
        $user->permissions()->syncWithoutDetaching($permissionIds);
        echo "✅ Added missing permissions\n";
    } else {
        echo "ℹ️  User already has all permissions\n";
    }

    // Verify final count
    $finalCount = $user->permissions()->whereIn('id', $permissionIds)->count();
    echo "Final count: $finalCount\n";
}

echo "\n=== Summary ===\n";
echo "✅ All users now have perbaikan-kontainer permissions\n";
echo "✅ Permission matrix should now show 'Perbaikan Kontainer' for all users\n";
