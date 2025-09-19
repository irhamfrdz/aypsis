<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use App\Models\User;

// Simulate the exact sidebar logic
echo "=== SIMULATING SIDEBAR LOGIC ===\n";

// Get user like in sidebar
$user = Auth::user();

if (!$user) {
    echo "❌ No authenticated user found!\n";
    echo "This means Auth::user() returns null in sidebar.\n";
    exit;
}

echo "✅ User found: {$user->username} (ID: {$user->id})\n";

// Check karyawan relationship
$hasKaryawan = $user && $user->karyawan;
$showSidebar = $hasKaryawan;

echo "Has karyawan: " . ($hasKaryawan ? 'YES' : 'NO') . "\n";
echo "Show sidebar: " . ($showSidebar ? 'YES' : 'NO') . "\n\n";

// Test the exact condition from sidebar
echo "=== TESTING KODE NOMOR MENU CONDITION ===\n";
$condition1 = $user && $user->can('master-kode-nomor-view');

echo "\$user && \$user->can('master-kode-nomor-view'): " . ($condition1 ? 'TRUE' : 'FALSE') . "\n";

// Break it down
echo "Breakdown:\n";
echo "- \$user exists: " . ($user ? 'TRUE' : 'FALSE') . "\n";
echo "- \$user->can('master-kode-nomor-view'): " . ($user->can('master-kode-nomor-view') ? 'TRUE' : 'FALSE') . "\n";

if ($condition1) {
    echo "\n✅ KODE NOMOR MENU SHOULD BE VISIBLE\n";
    echo "If it's not showing, there might be:\n";
    echo "1. JavaScript error hiding the menu\n";
    echo "2. CSS issue making it invisible\n";
    echo "3. Blade template caching issue\n";
    echo "4. Browser cache issue\n";
} else {
    echo "\n❌ KODE NOMOR MENU WILL NOT BE VISIBLE\n";
    echo "The condition \$user && \$user->can('master-kode-nomor-view') is false\n";
}

// Test route exists
echo "\n=== CHECKING ROUTE ===\n";
try {
    $routeExists = \Illuminate\Support\Facades\Route::has('master.kode-nomor.index');
    echo "Route 'master.kode-nomor.index' exists: " . ($routeExists ? 'YES' : 'NO') . "\n";
} catch (Exception $e) {
    echo "Error checking route: " . $e->getMessage() . "\n";
}
