<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$controller = app(App\Http\Controllers\UserController::class);
$matrix = ['master-kelola-bbm' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1],
		   'master-pricelist-uang-jalan-batam' => ['view' => 1, 'create' => 1, 'update' => 1, 'delete' => 1]];
$result = $controller->testConvertMatrixPermissionsToIds($matrix);
print_r($result);

// Test perm names -> matrix
$names = ['master-kelola-bbm-view','master-kelola-bbm-create','master-kelola-bbm-edit','master-kelola-bbm-delete',
		  'master-pricelist-uang-jalan-batam-view','master-pricelist-uang-jalan-batam-create','master-pricelist-uang-jalan-batam-edit','master-pricelist-uang-jalan-batam-delete'];
$mat = $controller->testConvertPermissionsToMatrix($names);
print_r($mat);

echo "\nDone\n";