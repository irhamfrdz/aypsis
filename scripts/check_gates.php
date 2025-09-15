<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('username', 'test2')->first();
$admin = User::where('username', 'admin')->first();
if(!$u || !$admin){
    echo "MISSING_USERS\n";
    exit(0);
}

$abilities = [
    'master-user',
    'master-pranota-tagihan-kontainer',
    'pembayaran-pranota-tagihan-kontainer.index',
    'tagihan-kontainer-sewa.index',
    'tagihan-kontainer-sewa.create',
];

foreach (['test2'=>$u, 'admin'=>$admin] as $name => $user) {
    echo "User: $name (id={$user->id})\n";
    foreach ($abilities as $a) {
        $can = app(\Illuminate\Contracts\Auth\Access\Gate::class)->forUser($user)->check($a);
        echo str_pad($a, 45) . ' => ' . ($can ? 'YES' : 'NO') . "\n";
    }
    echo "\n";
}

