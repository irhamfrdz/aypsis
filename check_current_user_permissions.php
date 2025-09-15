<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get a test user (you can change this to check different users)
$user = User::where('username', 'test4')->first(); // Change this to the user you want to check

if (!$user) {
    echo "User not found. Available users:\n";
    $users = User::select('id', 'username')->get();
    foreach ($users as $u) {
        echo "  - {$u->username} (ID: {$u->id})\n";
    }
    exit(1);
}

echo "Checking user: {$user->username} (ID: {$user->id})\n\n";

// Check specific permissions
$permissionsToCheck = [
    'tagihan-kontainer.view',
    'tagihan-kontainer-view',
    'tagihan-kontainer'
];

foreach ($permissionsToCheck as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if ($perm) {
        $hasPermission = $user->permissions->contains('id', $perm->id);
        echo "Permission '$permName' (ID: {$perm->id}): " . ($hasPermission ? '✅ HAS' : '❌ MISSING') . "\n";
    } else {
        echo "Permission '$permName': ❌ NOT FOUND IN DB\n";
    }
}

echo "\n";

// Test gate
echo "Gate check for 'tagihan-kontainer.view': " . (\Illuminate\Support\Facades\Gate::allows('tagihan-kontainer.view', $user) ? '✅ ALLOWED' : '❌ DENIED') . "\n";
echo "Gate check for 'tagihan-kontainer-view': " . (\Illuminate\Support\Facades\Gate::allows('tagihan-kontainer-view', $user) ? '✅ ALLOWED' : '❌ DENIED') . "\n";
