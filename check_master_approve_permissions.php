<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo 'Checking master module permissions in database:' . PHP_EOL;
$masterModules = ['master-karyawan', 'master-user', 'master-kontainer', 'master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil'];

foreach ($masterModules as $module) {
    echo PHP_EOL . '=== ' . strtoupper($module) . ' ===' . PHP_EOL;

    // Check main permission
    $mainPerm = Permission::where('name', $module)->first();
    if ($mainPerm) {
        echo 'Main: ' . $mainPerm->name . ' (ID: ' . $mainPerm->id . ')' . PHP_EOL;
    } else {
        echo 'Main: NOT FOUND' . PHP_EOL;
    }

    // Check approve permission
    $approvePerm = Permission::where('name', $module . '-approve')->first();
    if ($approvePerm) {
        echo 'Approve: EXISTS - ' . $approvePerm->name . ' (ID: ' . $approvePerm->id . ')' . PHP_EOL;
    } else {
        echo 'Approve: NOT FOUND' . PHP_EOL;
    }
}
?>
