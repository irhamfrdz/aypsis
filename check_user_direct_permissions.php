<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::find(1); // Assuming admin is user ID 1
if ($user) {
    echo "User found: {$user->email}\n";
    echo "Direct permissions count: " . $user->permissions()->count() . "\n";

    $directPermissions = $user->permissions()->where('name', 'like', 'approval%')->get();
    echo "Direct approval permissions:\n";
    foreach ($directPermissions as $perm) {
        echo "- {$perm->name}\n";
    }

    $hasApprovalDashboard = $user->permissions()->where('name', 'approval-dashboard')->exists();
    echo "Has approval-dashboard directly: " . ($hasApprovalDashboard ? 'YES' : 'NO') . "\n";
    echo "Can access approval-dashboard: " . ($user->can('approval-dashboard') ? 'YES' : 'NO') . "\n";

    // Check all direct permissions
    echo "\nAll direct permissions:\n";
    $allDirectPermissions = $user->permissions()->get();
    foreach ($allDirectPermissions as $perm) {
        echo "- {$perm->name}\n";
    }
} else {
    echo "User not found\n";
}
