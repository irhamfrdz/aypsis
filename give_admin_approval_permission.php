<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::find(1);
$permission = App\Models\Permission::where('name', 'approval-dashboard')->first();

if ($user && $permission) {
    if (!$user->permissions()->where('name', 'approval-dashboard')->exists()) {
        $user->permissions()->attach($permission);
        echo "Permission 'approval-dashboard' berhasil diberikan ke user admin\n";
    } else {
        echo "User admin sudah memiliki permission 'approval-dashboard'\n";
    }

    // Test lagi
    echo "Sekarang user can('approval-dashboard'): " . ($user->can('approval-dashboard') ? 'YES' : 'NO') . "\n";

    // Test semua approval permissions
    $approvalPermissions = ['approval-dashboard', 'approval.view', 'approval.approve'];
    echo "\nTest semua approval permissions:\n";
    foreach ($approvalPermissions as $perm) {
        echo "- can('$perm'): " . ($user->can($perm) ? 'YES' : 'NO') . "\n";
    }
} else {
    echo "User atau permission tidak ditemukan\n";
    if (!$user) echo "- User admin tidak ditemukan\n";
    if (!$permission) echo "- Permission 'approval-dashboard' tidak ditemukan\n";
}
