<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('username', 'test4')->first();
if ($user) {
    echo "User test4 permissions:\n";
    foreach ($user->permissions as $perm) {
        echo "- {$perm->name}\n";
    }
    echo "\n";

    // Check specific permissions for user approval menu
    echo "User Approval Menu Permission Check:\n";
    echo "- isAdmin: NO (assuming test4 is not admin)\n";
    echo "- can('master-user'): " . ($user->can('master-user') ? 'YES' : 'NO') . "\n";
    echo "- can('user-approval.view'): " . ($user->can('user-approval.view') ? 'YES' : 'NO') . "\n";
    echo "- can('user-approval'): " . ($user->can('user-approval') ? 'YES' : 'NO') . "\n";
    echo "\n";

    // Current condition check
    $isAdmin = false; // Assuming test4 is not admin
    $currentCondition = $isAdmin || $user->can('master-user');

    echo "Current menu condition result: " . ($currentCondition ? 'VISIBLE' : 'HIDDEN') . "\n";

    if (!$currentCondition) {
        echo "❌ ISSUE: Menu 'Persetujuan User' tidak akan muncul untuk user test4\n";
        echo "   karena kondisi saat ini hanya memeriksa 'master-user' permission\n";
    }

    echo "\n=== RECOMMENDED FIX ===\n";
    echo "Menu 'Persetujuan User' seharusnya muncul jika user memiliki:\n";
    echo "- master-user permission (untuk admin)\n";
    echo "- ATAU user-approval.view permission (untuk view approval)\n";
    echo "- ATAU user-approval permission (general access)\n";

} else {
    echo 'User test4 not found';
}
