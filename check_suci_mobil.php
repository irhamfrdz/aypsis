<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

$user = User::where('username', 'suci')->first();

if (!$user) {
    echo "User 'suci' tidak ditemukan!\n";
    exit;
}

echo "=== CEK PERMISSION USER SUCI UNTUK MOBIL ===\n\n";
echo "User ID: {$user->id}\n";
echo "Username: {$user->username}\n\n";

// Cek semua permission mobil
$mobilPerms = $user->permissions()->where('name', 'like', '%mobil%')->get();
echo "Permission Mobil yang dimiliki:\n";
if ($mobilPerms->isEmpty()) {
    echo "  TIDAK ADA!\n";
} else {
    foreach ($mobilPerms as $perm) {
        echo "  - {$perm->name}\n";
    }
}

echo "\n";
echo "Cek hasPermissionTo('master-mobil-view'): " . ($user->hasPermissionTo('master-mobil-view') ? 'YES' : 'NO') . "\n";

// Cek permission yang ada di database
echo "\n=== PERMISSION DI DATABASE ===\n";
$dbPerm = \Spatie\Permission\Models\Permission::where('name', 'master-mobil-view')->first();
if ($dbPerm) {
    echo "Permission 'master-mobil-view' ADA di database (ID: {$dbPerm->id})\n";
} else {
    echo "Permission 'master-mobil-view' TIDAK ADA di database!\n";
}
