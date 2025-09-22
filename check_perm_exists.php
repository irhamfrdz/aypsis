<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$perm = \App\Models\Permission::where('name', 'permohonan-memo-update')->first();
echo $perm ? 'Permission exists: ' . $perm->name : 'Permission NOT found';