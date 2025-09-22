<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$user = \App\Models\User::where('username', 'admin')->first();
$perm = \App\Models\Permission::where('name', 'permohonan')->first();
if ($user && $perm) {
    $user->permissions()->detach($perm->id);
    echo 'Permission permohonan removed from admin';
} else {
    echo 'User or permission not found';
}
