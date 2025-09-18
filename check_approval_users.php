<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Permission;

$approvalPerms = Permission::where('name', 'like', 'approval-%')->get();
echo 'Approval permissions:' . PHP_EOL;
foreach($approvalPerms as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
    $users = $perm->users;
    if($users->count() > 0) {
        echo '  Assigned to users: ';
        foreach($users as $user) {
            echo $user->name . ' (ID: ' . $user->id . '), ';
        }
        echo PHP_EOL;
    } else {
        echo '  Not assigned to any users' . PHP_EOL;
    }
}
