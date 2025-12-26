<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$permissions = [
    ['name' => 'master-klasifikasi-biaya-view', 'description' => 'View Master Klasifikasi Biaya'],
    ['name' => 'master-klasifikasi-biaya-create', 'description' => 'Create Master Klasifikasi Biaya'],
    ['name' => 'master-klasifikasi-biaya-update', 'description' => 'Update Master Klasifikasi Biaya'],
    ['name' => 'master-klasifikasi-biaya-delete', 'description' => 'Delete Master Klasifikasi Biaya'],
];

foreach ($permissions as $p) {
    $perm = Permission::firstOrCreate([
        'name' => $p['name']
    ], [
        'description' => $p['description']
    ]);

    echo "Ensured permission: {$perm->name}\n";
}

echo "Done\n";
