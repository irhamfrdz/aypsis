<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$ids = [376, 379];
foreach($ids as $id) {
    $perm = Permission::find($id);
    if($perm) {
        echo 'ID ' . $id . ': ' . $perm->name . PHP_EOL;
    }
}
