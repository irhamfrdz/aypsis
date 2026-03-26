<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$controller = new \App\Http\Controllers\UserController();

$input = [
    'pranota-uang-jalan-batam' => [
        'view' => '1',
        'create' => '1',
        'update' => '1',
        'delete' => '1',
    ]
];

// simulate convertMatrixPermissionsToIds
$reflection = new \ReflectionClass($controller);
$method = $reflection->getMethod('convertMatrixPermissionsToIds');
$method->setAccessible(true);
$ids = $method->invokeArgs($controller, [$input]);
echo "Ids to save for pranota-uang-jalan-batam:\n";
print_r($ids);

// simulate convertPermissionsToMatrix
$method2 = $reflection->getMethod('convertPermissionsToMatrix');
$method2->setAccessible(true);
$permissionNames = ['pranota-uang-jalan-batam-view', 'pranota-uang-jalan-batam-create'];
$matrix = $method2->invokeArgs($controller, [$permissionNames]);
echo "Matrix read from db:\n";
print_r($matrix);
