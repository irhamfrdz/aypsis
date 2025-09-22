<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$role = \Spatie\Permission\Models\Role::where('name', 'admin')->first();
echo $role ? 'Role admin exists' : 'Role admin NOT found';