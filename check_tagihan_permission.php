<?php

require_once 'vendor/autoload.php';

use App\Models\Permission;
use Illuminate\Foundation\Application;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Checking permission: master-pranota-tagihan-kontainer\n";
echo "==================================================\n";

// Check specific permission
$perm = Permission::where('name', 'master-pranota-tagihan-kontainer')->first();
if ($perm) {
    echo "✓ Permission found: {$perm->name} (ID: {$perm->id})\n";
    echo "  Description: {$perm->description}\n";
} else {
    echo "✗ Permission NOT found: master-pranota-tagihan-kontainer\n";
}

// Check for similar permissions
$similar = Permission::where('name', 'like', '%tagihan%')->get();
echo "\nSimilar permissions:\n";
foreach ($similar as $p) {
    echo "  {$p->name} (ID: {$p->id})\n";
}

// Check for pranota permissions
$pranotaPerms = Permission::where('name', 'like', '%pranota%')->get();
echo "\nPranota permissions:\n";
foreach ($pranotaPerms as $p) {
    echo "  {$p->name} (ID: {$p->id})\n";
}
