<?php

require_once 'vendor/autoload.php';

use App\Models\User;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = User::whereHas('permissions', function($q) {
    $q->where('name', 'master-pranota-tagihan-kontainer');
})->get();

echo 'Users dengan master-pranota-tagihan-kontainer: ' . $users->count() . PHP_EOL;
foreach($users as $user) {
    echo '- ' . $user->name . ' (' . $user->username . ')' . PHP_EOL;
}

echo PHP_EOL;

$users2 = User::whereHas('permissions', function($q) {
    $q->where('name', 'tagihan-kontainer');
})->get();

echo 'Users dengan tagihan-kontainer: ' . $users2->count() . PHP_EOL;
foreach($users2 as $user) {
    echo '- ' . $user->name . ' (' . $user->username . ')' . PHP_EOL;
}
