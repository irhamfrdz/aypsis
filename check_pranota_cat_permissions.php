<?php

require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permissions = Permission::where("name", "like", "%pembayaran-pranota-cat%")->get();

echo "Permissions found for pembayaran-pranota-cat:\n";
echo "==========================================\n";

if ($permissions->count() > 0) {
    foreach ($permissions as $permission) {
        echo "- {$permission->name} (ID: {$permission->id})\n";
    }
} else {
    echo "No permissions found for pembayaran-pranota-cat\n";
}
