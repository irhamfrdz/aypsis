<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

echo 'Permissions with coa:' . PHP_EOL;
$permissions = Permission::where('name', 'like', '%coa%')->get();
foreach($permissions as $perm) {
    echo '- ' . $perm->name . PHP_EOL;
}
?>
