<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo 'Checking master-karyawan permissions in database:' . PHP_EOL;
$karyawanPerms = Permission::where('name', 'like', '%master-karyawan%')->get();
foreach ($karyawanPerms as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL . 'Checking if master-karyawan-approve exists:' . PHP_EOL;
$approvePerm = Permission::where('name', 'master-karyawan-approve')->first();
if ($approvePerm) {
    echo 'EXISTS: ' . $approvePerm->name . ' (ID: ' . $approvePerm->id . ')' . PHP_EOL;
} else {
    echo 'NOT FOUND: master-karyawan-approve' . PHP_EOL;
}
?>
