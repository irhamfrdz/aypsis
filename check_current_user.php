<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Auth;

echo "Current user check:\n";

if (Auth::check()) {
    $user = Auth::user();
    echo "User is logged in: " . $user->username . "\n";
    echo "User ID: " . $user->id . "\n";

    echo "\nChecking permissions:\n";
    echo "Can master-kode-nomor-view: " . ($user->can('master-kode-nomor-view') ? 'YES' : 'NO') . "\n";
    echo "Can master-karyawan-view: " . ($user->can('master-karyawan-view') ? 'YES' : 'NO') . "\n";

    echo "\nAll user permissions:\n";
    $permissions = $user->permissions->pluck('name')->toArray();
    foreach ($permissions as $perm) {
        if (strpos($perm, 'kode-nomor') !== false) {
            echo "- " . $perm . "\n";
        }
    }

    echo "\nTotal permissions: " . count($permissions) . "\n";
} else {
    echo "No user is logged in!\n";
}
