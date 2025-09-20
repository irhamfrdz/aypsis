<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Route;

echo "=== TESTING APPROVAL ACCESS FOR ADMIN USER ===\n\n";

$user = User::where('username', 'admin')->first();

if (!$user) {
    echo "❌ Admin user not found!\n";
    exit(1);
}

echo "✅ Admin user found: {$user->username}\n";

// Test permission checks
echo "\n=== PERMISSION CHECKS ===\n";
echo "Can access 'permohonan': " . ($user->can('permohonan') ? 'YES ✅' : 'NO ❌') . "\n";
echo "Can access 'approval.view': " . ($user->can('approval.view') ? 'YES ✅' : 'NO ❌') . "\n";
echo "Can access 'approval.dashboard': " . ($user->can('approval.dashboard') ? 'YES ✅' : 'NO ❌') . "\n";

// Test route access simulation
echo "\n=== ROUTE ACCESS SIMULATION ===\n";

// Test with correct middleware
$canAccessApproval = $user->can('approval-dashboard');
echo "Can access approval routes (middleware: can:approval-dashboard): " . ($canAccessApproval ? 'YES ✅' : 'NO ❌') . "\n";

// Check if approval routes exist
try {
    $approvalDashboardRoute = Route::getRoutes()->getByName('approval.dashboard');
    if ($approvalDashboardRoute) {
        echo "Route 'approval.dashboard' exists: YES ✅\n";
        echo "Route URI: {$approvalDashboardRoute->uri()}\n";
        echo "Route methods: " . implode(', ', $approvalDashboardRoute->methods()) . "\n";
    } else {
        echo "Route 'approval.dashboard' not found: NO ❌\n";
    }
} catch (Exception $e) {
    echo "Error checking route: {$e->getMessage()}\n";
}

echo "\n=== SUMMARY ===\n";
if ($canAccessApproval) {
    echo "🎉 User admin SHOULD be able to access approval pages now!\n";
    echo "Try accessing: /approval or /approval/dashboard\n";
} else {
    echo "❌ User admin still cannot access approval pages.\n";
    echo "The issue might be with route middleware or permission configuration.\n";
}

echo "\nTest completed.\n";
