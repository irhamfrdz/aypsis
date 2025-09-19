<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Spatie\Permission\Models\Permission;

echo "Total permissions: " . Permission::count() . PHP_EOL;
echo "Kode nomor permissions:" . PHP_EOL;

$permissions = Permission::where('name', 'like', '%kode-nomor%')->get();
foreach ($permissions as $permission) {
    echo "- " . $permission->name . PHP_EOL;
}

echo PHP_EOL . "Checking if kode nomor table exists..." . PHP_EOL;

try {
    $count = DB::table('kode_nomor')->count();
    echo "Kode nomor records: " . $count . PHP_EOL;
} catch (Exception $e) {
    echo "Error checking kode_nomor table: " . $e->getMessage() . PHP_EOL;
}
