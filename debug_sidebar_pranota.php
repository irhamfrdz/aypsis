<?php

// Debug sidebar logic for pranota-supir menu
require_once 'vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Auth;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Simulate user test4 login
$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "🧪 Debugging Sidebar Logic for Pranota Supir Menu\n";
echo "=================================================\n\n";

// Simulate authentication
Auth::login($user);
echo "✅ User test4 authenticated\n\n";

// Check admin status
$isAdmin = $user && method_exists($user, 'hasRole') && $user->hasRole('admin');
echo "👤 Is Admin: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n";

// Check specific permissions
$permissions = [
    'pranota-supir.view' => $user->hasPermissionTo('pranota-supir.view'),
    'pranota-supir.create' => $user->hasPermissionTo('pranota-supir.create'),
    'pranota-supir.update' => $user->hasPermissionTo('pranota-supir.update'),
    'pranota-supir.delete' => $user->hasPermissionTo('pranota-supir.delete'),
    'pranota-supir.approve' => $user->hasPermissionTo('pranota-supir.approve'),
    'pranota-supir.print' => $user->hasPermissionTo('pranota-supir.print'),
    'pranota-supir.export' => $user->hasPermissionTo('pranota-supir.export'),
];

echo "\n📋 Detailed permissions:\n";
foreach ($permissions as $perm => $hasPerm) {
    echo "  {$perm}: " . ($hasPerm ? '✅ YES' : '❌ NO') . "\n";
}

// Check if any pranota permission exists (this is what the sidebar logic checks)
$hasAnyPranotaPermission = array_sum($permissions) > 0;
echo "\n🔐 Has any pranota-supir permission: " . ($hasAnyPranotaPermission ? '✅ YES' : '❌ NO') . "\n";

// Test the can() method used in sidebar
try {
    if (method_exists(Auth::user(), 'can')) {
        $canResult = Auth::user()->can('pranota-supir');
        echo "🔐 Auth::user()->can('pranota-supir'): " . ($canResult ? '✅ YES' : '❌ NO') . "\n";
    } else {
        echo "🔐 Method 'can' NOT available on user model\n";
    }
} catch (Exception $e) {
    echo "🔐 Error testing can() method: " . $e->getMessage() . "\n";
}

// Check if menu should be visible
$menuVisible = $isAdmin || $hasAnyPranotaPermission;
echo "\n📊 Menu Visibility Logic:\n";
echo "  \$isAdmin: " . ($isAdmin ? 'true' : 'false') . "\n";
echo "  hasAnyPranotaPermission: " . ($hasAnyPranotaPermission ? 'true' : 'false') . "\n";
echo "  Menu should be visible: " . ($menuVisible ? '✅ YES' : '❌ NO') . "\n";

if (!$menuVisible) {
    echo "\n🚨 ISSUE FOUND: Menu should be visible but logic says NO!\n";
    echo "Possible causes:\n";
    echo "1. Permission not properly saved to database\n";
    echo "2. User session not updated\n";
    echo "3. Cache issues\n";
} else {
    echo "\n✅ Menu should be visible - no issues with logic\n";
    echo "If menu still not visible, try:\n";
    echo "1. Clear browser cache\n";
    echo "2. Log out and log back in\n";
    echo "3. Clear Laravel cache: php artisan cache:clear\n";
}

echo "\nTest completed: " . date('Y-m-d H:i:s') . "\n";
