<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$user = User::find(1);
if (!$user) {
    echo "User not found\n";
    exit;
}

$permissions = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create', 'master-nomor-terakhir-update', 'master-nomor-terakhir-delete'];

foreach($permissions as $permName) {
    $perm = Permission::where('name', $permName)->first();
    if($perm && !$user->hasPermissionTo($permName)) {
        $user->permissions()->attach($perm);
        echo "Added: $permName\n";
    } else {
        echo "Skipped: $permName (already has or not found)\n";
    }
}

echo "Done\n";
