<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permissions = Permission::where('name', 'like', 'pembayaran-pranota-kontainer%')->get();

echo 'Found permissions:' . PHP_EOL;
foreach ($permissions as $p) {
    echo $p->name . ' (ID: ' . $p->id . ')' . PHP_EOL;
}
echo 'Total: ' . $permissions->count() . PHP_EOL;
