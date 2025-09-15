<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo 'Checking all karyawan-related permissions:' . PHP_EOL;
$allKaryawanPerms = Permission::where('name', 'like', '%karyawan%')->get();
foreach ($allKaryawanPerms as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}
?>
