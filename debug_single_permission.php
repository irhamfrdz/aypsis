<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissionName = 'permohonan-create';

echo "Testing single permission: $permissionName\n\n";

$userController = new App\Http\Controllers\UserController();
$reflection = new ReflectionClass($userController);
$toMatrix = $reflection->getMethod('convertPermissionsToMatrix');
$toIds = $reflection->getMethod('convertMatrixPermissionsToIds');
$toMatrix->setAccessible(true);
$toIds->setAccessible(true);

$matrix = $toMatrix->invoke($userController, [$permissionName]);
print_r(['matrix' => $matrix]);

$ids = $toIds->invoke($userController, $matrix);
print_r(['ids' => $ids]);

// Also check direct DB lookups for variations
$variations = [
    'permohonan-create',
    'permohonan.create',
    'permohonan',
    'create-permohonan',
    'create.permohonan'
];

foreach ($variations as $v) {
    $p = Permission::where('name', $v)->first();
    echo "Lookup: $v -> ";
    if ($p) echo "FOUND id={$p->id}\n"; else echo "NOT FOUND\n";
}

echo "\nDone\n";
