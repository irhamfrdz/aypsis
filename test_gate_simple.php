<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Gate;

echo "=== TESTING GATE AFTER CACHE CLEAR ===\n";

// Get first user
$user = User::first();
if (!$user) {
    echo "❌ No users found in database\n";
    exit;
}

echo "Testing with user: {$user->name} (ID: {$user->id})\n\n";

// Test simple gate
Gate::define('test-gate', function () {
    return true;
});

$result = Gate::check('test-gate', $user);
echo "Simple gate result: " . ($result ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test dashboard permission
$result2 = Gate::check('dashboard', $user);
echo "Dashboard gate result: " . ($result2 ? '✅ ALLOWED' : '❌ DENIED') . "\n";

// Test user can method
$result3 = $user->can('dashboard');
echo "User can dashboard: " . ($result3 ? '✅ ALLOWED' : '❌ DENIED') . "\n";
