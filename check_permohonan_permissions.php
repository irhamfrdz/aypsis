<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo '=== CHECKING PERMOHONAN PERMISSIONS IN DATABASE ===' . PHP_EOL;

$permissions = Permission::where('name', 'LIKE', '%permohonan%')->get();
echo 'Permissions containing "permohonan":' . PHP_EOL;
foreach ($permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}

echo PHP_EOL;
$permohonanSimple = Permission::where('name', 'permohonan')->first();
if ($permohonanSimple) {
    echo '✅ Permission "permohonan" exists with ID: ' . $permohonanSimple->id . PHP_EOL;
} else {
    echo '❌ Permission "permohonan" does NOT exist' . PHP_EOL;
}
