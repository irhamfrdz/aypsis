<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'admin')->first();
if ($user) {
    echo "Admin permissions containing kode-nomor:\n";
    foreach ($user->permissions as $perm) {
        if (strpos($perm->name, 'kode-nomor') !== false) {
            echo "- " . $perm->name . "\n";
        }
    }

    echo "\nChecking specific permission:\n";
    echo "Has master-kode-nomor-view: " . ($user->can('master-kode-nomor-view') ? 'YES' : 'NO') . "\n";
    echo "Has master-kode-nomor.view: " . ($user->can('master-kode-nomor.view') ? 'YES' : 'NO') . "\n";
} else {
    echo "Admin user not found!\n";
}
