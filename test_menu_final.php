<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate authentication
$user = \App\Models\User::find(1); // Assuming user ID 1 is admin
\Auth::login($user);

echo "Testing menu visibility conditions:\n\n";

echo "1. Route exists: pembayaran-pranota-cat.index\n";
$routeExists = \Route::has('pembayaran-pranota-cat.index');
echo "   Result: " . ($routeExists ? '✅ YES' : '❌ NO') . "\n\n";

echo "2. User is admin:\n";
$isAdmin = $user->role === 'admin' || $user->hasRole('admin');
echo "   Result: " . ($isAdmin ? '✅ YES' : '❌ NO') . "\n\n";

echo "3. User can 'pembayaran-pranota-cat-view':\n";
$canView = $user->can('pembayaran-pranota-cat-view');
echo "   Result: " . ($canView ? '✅ YES' : '❌ NO') . "\n\n";

echo "4. Overall condition (Route exists AND (isAdmin OR canView)):\n";
$condition = $routeExists && ($isAdmin || $canView);
echo "   Result: " . ($condition ? '✅ TRUE - Menu should appear' : '❌ FALSE - Menu will not appear') . "\n\n";

if ($condition) {
    echo "🎉 SUCCESS: Menu 'Bayar Pranota CAT Kontainer' should now appear in the sidebar!\n";
} else {
    echo "❌ ISSUE: Menu will not appear. Check the conditions above.\n";
}
