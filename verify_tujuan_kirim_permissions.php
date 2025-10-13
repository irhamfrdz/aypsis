<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;
use App\Models\User;

echo "Permission master-tujuan-kirim yang ditemukan:\n";
$permissions = Permission::where('name', 'like', 'master-tujuan-kirim-%')->get();
foreach($permissions as $perm) {
    echo "- " . $perm->name . "\n";
}

echo "\nUser admin permissions:\n";
$userAdmin = User::where('username', 'user_admin')->first();
if($userAdmin) {
    $tujuanKirimPerms = $userAdmin->permissions()->where('name', 'like', 'master-tujuan-kirim-%')->get();
    echo "User admin memiliki " . $tujuanKirimPerms->count() . " permission master-tujuan-kirim\n";
    foreach($tujuanKirimPerms as $perm) {
        echo "- " . $perm->name . "\n";
    }
} else {
    echo "User admin tidak ditemukan\n";
}