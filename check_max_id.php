<?php

require_once "vendor/autoload.php";

$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Permission;

$maxId = Permission::max('id');
echo "Max permission ID: {$maxId}\n";
