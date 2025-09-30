<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = App\Models\User::where('username', 'admin')->first();
$permission = App\Models\Permission::where('name', 'tagihan-cat-view')->first();

if ($user && $permission) {
    $user->permissions()->attach($permission->id);
    echo 'Added tagihan-cat-view permission to admin user' . PHP_EOL;
} else {
    echo 'User or permission not found' . PHP_EOL;
}
