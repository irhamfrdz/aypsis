<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate middleware check
$user = User::where('username', 'test4')->first();

if (!$user) {
    echo "User not found.\n";
    exit(1);
}

echo "Testing middleware 'can:tagihan-kontainer-view' for user: {$user->username}\n\n";

// Test the can middleware logic
$permission = 'tagihan-kontainer-view';
$canAccess = $user->can($permission);

echo "user->can('$permission'): " . ($canAccess ? '✅ ALLOWED' : '❌ DENIED') . "\n";

if ($canAccess) {
    echo "✅ User should be able to access the route!\n";
} else {
    echo "❌ User will get 403 error.\n";
}

// Test with the old permission name
$oldPermission = 'tagihan-kontainer.view';
$canAccessOld = $user->can($oldPermission);
echo "\nuser->can('$oldPermission'): " . ($canAccessOld ? '✅ ALLOWED' : '❌ DENIED') . "\n";
