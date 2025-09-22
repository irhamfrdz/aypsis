<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
if ($user) {
    echo 'User has role admin: ' . ($user->hasRole('admin') ? 'YES' : 'NO') . PHP_EOL;
    echo 'User roles: ';
    foreach ($user->roles as $role) {
        echo $role->name . ' ';
    }
    echo PHP_EOL;
} else {
    echo 'User not found';
}
