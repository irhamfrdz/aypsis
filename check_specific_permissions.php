<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$p1 = Permission::where('name', 'tagihan-kontainer')->first();
echo 'tagihan-kontainer: ' . ($p1 ? 'ADA (ID: ' . $p1->id . ')' : 'TIDAK ADA') . PHP_EOL;

$p2 = Permission::where('name', 'master-pranota-tagihan-kontainer')->first();
echo 'master-pranota-tagihan-kontainer: ' . ($p2 ? 'ADA (ID: ' . $p2->id . ')' : 'TIDAK ADA') . PHP_EOL;
