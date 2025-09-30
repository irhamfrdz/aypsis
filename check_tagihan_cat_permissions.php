<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
use App\Models\Permission;

$permissions = Permission::where('name', 'like', '%tagihan-cat%')->get();
echo 'Tagihan CAT permissions in database:' . PHP_EOL;
foreach ($permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}
