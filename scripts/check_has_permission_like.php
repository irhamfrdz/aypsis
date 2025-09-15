<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$u = User::where('username', 'test2')->first();
if(!$u){
    echo "NO_USER\n";
    exit(0);
}

echo "USER={$u->username}, id={$u->id}\n";
echo "hasPermissionLike('tagihan-kontainer-sewa') => ";
echo $u->hasPermissionLike('tagihan-kontainer-sewa') ? 'yes' : 'no';
echo "\n";
