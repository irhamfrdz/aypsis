<?php

// Check user test4 permissions for pranota-supir
require_once 'vendor/autoload.php';

use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('username', 'test4')->first();
if (!$user) {
    echo "❌ User test4 not found\n";
    exit(1);
}

echo "🧪 Checking user test4 permissions for pranota-supir\n";
echo "================================================\n\n";

$permissions = [
    'view' => $user->hasPermissionTo('pranota-supir.view'),
    'create' => $user->hasPermissionTo('pranota-supir.create'),
    'update' => $user->hasPermissionTo('pranota-supir.update'),
    'delete' => $user->hasPermissionTo('pranota-supir.delete'),
    'approve' => $user->hasPermissionTo('pranota-supir.approve'),
    'print' => $user->hasPermissionTo('pranota-supir.print'),
    'export' => $user->hasPermissionTo('pranota-supir.export'),
];

echo "📋 Pranota Supir permissions for test4:\n";
foreach ($permissions as $action => $hasPermission) {
    $status = $hasPermission ? '✅ YES' : '❌ NO';
    echo "  pranota-supir.{$action}: {$status}\n";
}

echo "\n🔍 Checking sidebar logic...\n";
$hasPranotaSupirAccess = $permissions['view'] || $permissions['create'] || $permissions['update'] || $permissions['delete'] || $permissions['approve'] || $permissions['print'] || $permissions['export'];
echo "Has any pranota-supir permission: " . ($hasPranotaSupirAccess ? '✅ YES' : '❌ NO') . "\n";

echo "\n📊 SUMMARY:\n";
if ($permissions['view']) {
    echo "✅ User has view permission - should see menu\n";
} else {
    echo "❌ User does NOT have view permission - menu should be hidden\n";
}

if ($hasPranotaSupirAccess) {
    echo "✅ User has at least one pranota-supir permission - menu should be visible\n";
} else {
    echo "❌ User has NO pranota-supir permissions - menu should be hidden\n";
}

echo "\n🔧 RECOMMENDATION:\n";
if (!$hasPranotaSupirAccess) {
    echo "User test4 has no pranota-supir permissions, so menu should be hidden.\n";
    echo "If you want the menu to show, grant at least one pranota-supir permission.\n";
} elseif (!$permissions['view']) {
    echo "User has permissions but no view permission. Menu visibility depends on sidebar logic.\n";
    echo "Check the sidebar code to see if it requires view permission specifically.\n";
} else {
    echo "User has view permission and should see the menu. Check sidebar implementation.\n";
}
