<?php

require_once 'vendor/autoload.php';

use Illuminate\Http\Request;
use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Permission yang terkait karyawan di database:\n";
echo "===========================================\n\n";

$perms = Permission::where('name', 'like', '%karyawan%')->get();
foreach($perms as $p) {
    echo $p->name . ' (ID: ' . $p->id . ")\n";
}

echo "\n\nSemua permission di database:\n";
echo "=============================\n\n";

$allPerms = Permission::all();
foreach($allPerms as $p) {
    echo $p->name . ' (ID: ' . $p->id . ")\n";
}
