<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Http\Request;
use App\Models\User;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$perm = Permission::where('name', 'tagihan-kontainer.view')->first();
echo 'tagihan-kontainer.view: ' . ($perm ? 'ADA (ID: ' . $perm->id . ')' : 'TIDAK ADA') . PHP_EOL;

$perm2 = Permission::where('name', 'like', 'tagihan-kontainer%')->get();
echo 'Permissions like tagihan-kontainer: ' . $perm2->count() . PHP_EOL;
foreach($perm2 as $p) {
    echo '  - ' . $p->name . ' (ID: ' . $p->id . ')' . PHP_EOL;
}
