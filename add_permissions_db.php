<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

echo "=== Adding Perbaikan Kontainer Permissions to Admin User ===\n";

$user = User::find(1);
if (!$user) {
    echo "❌ User with ID 1 not found\n";
    exit;
}

echo "User: {$user->username}\n";

// Get permission IDs for perbaikan-kontainer
$permissionIds = DB::table('permissions')
    ->where('name', 'like', 'perbaikan-kontainer%')
    ->pluck('id')
    ->toArray();

echo "Found " . count($permissionIds) . " perbaikan-kontainer permission IDs: " . implode(', ', $permissionIds) . "\n";

// Attach permissions to user
$user->permissions()->attach($permissionIds);

echo "✅ Permissions attached to user\n";

echo "\n=== Verification ===\n";
$userPermissions = $user->permissions()->where('name', 'like', 'perbaikan-kontainer%')->get();
echo "User now has " . $userPermissions->count() . " perbaikan-kontainer permissions:\n";
foreach($userPermissions as $perm) {
    echo "- {$perm->name}\n";
}
