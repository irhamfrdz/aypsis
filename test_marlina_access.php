<?php
// Include composer autoload and bootstrap Laravel  
require_once __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

// Boot the application
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Http\Controllers\OrderController;

echo "=== TESTING ACCESS TO ORDERS CONTROLLER AS MARLINA ===\n\n";

// Find and login as Marlina
$marlina = User::where('username', 'marlina')->first();

if (!$marlina) {
    echo "❌ Marlina user not found!\n";
    exit;
}

// Manually set the authenticated user (simulate login)
Auth::setUser($marlina);

echo "✅ Logged in as: {$marlina->username}\n";
echo "   User ID: {$marlina->id}\n";
echo "   Status: " . ($marlina->status ? 'Active' : 'Inactive') . "\n\n";

// Test permission checks that OrderController would use
echo "Testing permissions that OrderController@index would check:\n";
$canOrderView = $marlina->can('order-view');
echo "   order-view: " . ($canOrderView ? "✅ ALLOWED" : "❌ DENIED") . "\n";

if (!$canOrderView) {
    echo "\n❌ User cannot access orders.index route (order-view required)\n";
} else {
    echo "\n✅ User CAN access orders.index route\n";
}

// Test other order permissions
echo "\nOther order permissions:\n";
foreach (['order-create', 'order-update', 'order-delete', 'order-print', 'order-export'] as $perm) {
    $canDo = $marlina->can($perm);
    echo "   {$perm}: " . ($canDo ? "✅ ALLOWED" : "❌ DENIED") . "\n";
}

// Check middleware that would be applied
echo "\nTesting middleware conditions:\n";
echo "   'can:order-view' middleware: " . ($marlina->can('order-view') ? "✅ PASS" : "❌ FAIL") . "\n";

// Test what the sidebar condition would evaluate to
$hasOrderPermissions = $marlina->can('order-view') || $marlina->can('order-create') || $marlina->can('order-update') || $marlina->can('order-delete');
echo "   Sidebar condition (order part): " . ($hasOrderPermissions ? "✅ SHOW" : "❌ HIDE") . "\n";

$hasSuratJalanPerms = $marlina->can('surat-jalan-view') || $marlina->can('surat-jalan-create') || $marlina->can('surat-jalan-update') || $marlina->can('surat-jalan-delete');
echo "   Sidebar condition (surat-jalan part): " . ($hasSuratJalanPerms ? "✅ SHOW" : "❌ HIDE") . "\n";

$finalSidebar = $hasOrderPermissions || $hasSuratJalanPerms || $marlina->can('approval-order-view');
echo "   Final sidebar visibility: " . ($finalSidebar ? "✅ SHOW" : "❌ HIDE") . "\n";

echo "\n";

// Generate a simple test URL
$ordersUrl = url('/orders');
echo "Direct URL access test:\n";
echo "   Orders URL: {$ordersUrl}\n";
echo "   Can access: " . ($marlina->can('order-view') ? "✅ YES" : "❌ NO") . "\n";

echo "\n=== CONCLUSION ===\n";
if ($canOrderView && $finalSidebar) {
    echo "✅ User Marlina SHOULD be able to see and access Order Management menu\n";
    echo "   → Check browser cache, session, or client-side JavaScript issues\n";
    echo "   → Make sure user is properly logged in on the actual system\n";
    echo "   → Try hard refresh (Ctrl+F5) or clear browser cache\n";
} else {
    echo "❌ User Marlina CANNOT access Order Management due to permission issues\n";
}

echo "\n=== END TEST ===\n";