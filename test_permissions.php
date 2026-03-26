<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$permissions = \App\Models\Permission::where('name', 'like', '%pranota-uang-jalan-batam%')->pluck('name');
foreach ($permissions as $permission) {
    echo $permission . "\n";
}
