<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = App\Models\User::find(1);

echo "=== Memberikan Permission OB Bongkar ke User Admin ===\n\n";

$obBongkarPermissions = [
    'ob-bongkar-view',
    'ob-bongkar-create',
    'ob-bongkar-edit',
    'ob-bongkar-delete'
];

foreach ($obBongkarPermissions as $permName) {
    $permission = App\Models\Permission::where('name', $permName)->first();
    
    if ($user && $permission) {
        if (!$user->permissions()->where('name', $permName)->exists()) {
            $user->permissions()->attach($permission);
            echo "✓ Permission '{$permName}' berhasil diberikan ke user admin\n";
        } else {
            echo "→ User admin sudah memiliki permission '{$permName}'\n";
        }
    } else {
        if (!$user) echo "✗ User admin tidak ditemukan\n";
        if (!$permission) echo "✗ Permission '{$permName}' tidak ditemukan\n";
    }
}

echo "\n=== Test Permissions ===\n";
if ($user) {
    foreach ($obBongkarPermissions as $perm) {
        echo "- can('{$perm}'): " . ($user->can($perm) ? 'YES ✓' : 'NO ✗') . "\n";
    }
}

echo "\n=== SELESAI ===\n";
echo "Silakan logout dan login kembali untuk melihat menu OB Bongkar.\n";
