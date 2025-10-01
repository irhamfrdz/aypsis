<?php

// Script untuk memeriksa permission user admin

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "=== CHECKING USER ADMIN PERMISSIONS ===\n\n";

$admin = User::where('username', 'admin')->with('permissions')->first();

if ($admin) {
    echo "User admin ditemukan (ID: {$admin->id})\n";
    echo "Username: {$admin->username}\n\n";

    echo "Permissions yang dimiliki:\n";
    foreach ($admin->permissions as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\nTotal permissions: " . $admin->permissions->count() . "\n";

    // Check specific permissions untuk cabang dan coa
    $cabangPerms = $admin->permissions->filter(function($perm) {
        return str_contains($perm->name, 'cabang');
    });

    $coaPerms = $admin->permissions->filter(function($perm) {
        return str_contains($perm->name, 'coa');
    });

    echo "\nPermissions untuk CABANG:\n";
    foreach ($cabangPerms as $perm) {
        echo "- {$perm->name}\n";
    }

    echo "\nPermissions untuk COA:\n";
    foreach ($coaPerms as $perm) {
        echo "- {$perm->name}\n";
    }

} else {
    echo "User admin tidak ditemukan!\n";

    // Cari semua user yang ada
    echo "\nUser yang tersedia:\n";
    $users = User::all();
    foreach ($users as $user) {
        echo "- {$user->username} (ID: {$user->id})\n";
    }
}
