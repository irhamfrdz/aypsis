<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo "Nomor Terakhir Permissions:\n";

$permissions = Permission::where('name', 'like', 'master-nomor-terakhir%')->get();

if ($permissions->count() > 0) {
    foreach ($permissions as $permission) {
        echo "✅ {$permission->name}: {$permission->description}\n";
    }
    echo "\nTotal: {$permissions->count()} permissions\n";
} else {
    echo "❌ No permissions found\n";
}
