<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;

$perms = Permission::where('name', 'like', '%kode-nomor%')->get();
echo 'Found ' . $perms->count() . ' kode-nomor permissions:' . PHP_EOL;
foreach($perms as $p) {
    echo '- ' . $p->name . PHP_EOL;
}
