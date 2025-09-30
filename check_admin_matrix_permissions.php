<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Http\Controllers\UserController;

$user = User::where('username', 'admin')->first();
if ($user) {
    echo "User admin permissions:\n";
    foreach($user->permissions as $perm) {
        if (strpos($perm->name, 'tagihan-cat') !== false) {
            echo "- {$perm->name}\n";
        }
    }

    echo "\nMatrix permissions for tagihan-cat:\n";
    $controller = new UserController();
    $matrixPermissions = $controller->testConvertPermissionsToMatrix($user->permissions->pluck('name')->toArray());

    if (isset($matrixPermissions['tagihan-cat'])) {
        echo json_encode($matrixPermissions['tagihan-cat'], JSON_PRETTY_PRINT) . "\n";

        if (isset($matrixPermissions['tagihan-cat']['view']) && $matrixPermissions['tagihan-cat']['view']) {
            echo "\n✅ tagihan-cat view permission is set in matrix\n";
        } else {
            echo "\n❌ tagihan-cat view permission is NOT set in matrix\n";
        }
    } else {
        echo "No tagihan-cat permissions in matrix\n";
    }
} else {
    echo 'Admin user not found' . PHP_EOL;
}
