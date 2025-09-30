<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = User::where('username', 'marlina')->first();

if ($user) {
    echo "User marlina found: ID={$user->id}\n";
    echo "Username: {$user->username}\n";
    echo "Name: " . ($user->name ?? 'N/A') . "\n";
    echo "Active: " . ($user->is_active ?? 'N/A') . "\n";
    echo "Email verified: " . ($user->email_verified_at ? 'YES' : 'NO') . "\n";
    echo "Permissions count: " . $user->permissions()->count() . "\n";

    $perms = $user->permissions()->where('name', 'like', '%tagihan-perbaikan%')->get();
    echo "Tagihan perbaikan permissions: " . $perms->count() . "\n";
    foreach ($perms as $p) {
        echo "- {$p->name} (ID: {$p->id})\n";
    }

    // Test the permission check
    echo "\nPermission check: tagihan-perbaikan-kontainer-view = " . ($user->can('tagihan-perbaikan-kontainer-view') ? 'YES' : 'NO') . "\n";

} else {
    echo "User marlina not found\n";
}
