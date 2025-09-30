<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$user = User::where('username', 'user_admin')->first();

if ($user) {
    echo "User found: {$user->username} (ID: {$user->id})\n";
    echo "Permissions for master-nomor-terakhir:\n";

    $nomorTerakhirPerms = $user->permissions->where('name', 'like', 'master-nomor-terakhir%')->pluck('name')->toArray();

    if (empty($nomorTerakhirPerms)) {
        echo "❌ No master-nomor-terakhir permissions found\n";
    } else {
        foreach ($nomorTerakhirPerms as $perm) {
            echo "✅ {$perm}\n";
        }
    }

    echo "\nTesting can() method:\n";
    $testPerms = ['master-nomor-terakhir-view', 'master-nomor-terakhir-create'];
    foreach ($testPerms as $perm) {
        $can = $user->can($perm);
        echo ($can ? "✅" : "❌") . " can('{$perm}') = " . ($can ? 'true' : 'false') . "\n";
    }

} else {
    echo "❌ User 'user_admin' not found\n";

    // List all users
    echo "\nAvailable users:\n";
    $users = User::select('id', 'username')->get();
    foreach ($users as $u) {
        echo "- {$u->username} (ID: {$u->id})\n";
    }
}
