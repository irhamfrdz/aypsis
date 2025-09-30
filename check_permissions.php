<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo "Admin user permissions:\n";
    $permissions = $user->permissions()->pluck('name')->toArray();
    foreach ($permissions as $perm) {
        echo "- $perm\n";
    }

    echo "\nDirect permissions check:\n";
    echo "Has pranota-cat-create: " . ($user->hasPermissionTo('pranota-cat-create') ? 'YES' : 'NO') . "\n";
    echo "Has pranota-cat-view: " . ($user->hasPermissionTo('pranota-cat-view') ? 'YES' : 'NO') . "\n";

    echo "\nVia can() method:\n";
    echo "Can pranota-cat-create: " . ($user->can('pranota-cat-create') ? 'YES' : 'NO') . "\n";
    echo "Can pranota-cat-view: " . ($user->can('pranota-cat-view') ? 'YES' : 'NO') . "\n";

    // Check roles
    echo "\nUser roles:\n";
    $roles = $user->roles()->pluck('name')->toArray();
    foreach ($roles as $role) {
        echo "- $role\n";
    }
} else {
    echo "Admin user not found\n";
}
