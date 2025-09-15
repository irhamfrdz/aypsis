<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo 'Checking for master-related permissions:' . PHP_EOL;
$masterPerms = Permission::where('name', 'like', '%master%')->get();
foreach ($masterPerms as $perm) {
    echo '- ' . $perm->name . ' (ID: ' . $perm->id . ')' . PHP_EOL;
}

echo PHP_EOL . 'Checking for exact master permission:' . PHP_EOL;
$exactMaster = Permission::where('name', 'master')->first();
if ($exactMaster) {
    echo 'Found: ' . $exactMaster->name . ' (ID: ' . $exactMaster->id . ')' . PHP_EOL;
} else {
    echo 'No exact master permission found' . PHP_EOL;
}
?>
