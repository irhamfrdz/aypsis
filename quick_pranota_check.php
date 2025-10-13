<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== PRANOTA PERMISSIONS CHECK ===\n\n";

$perms1 = DB::table('permissions')->where('name', 'like', 'pranota-kontainer-sewa%')->pluck('name');
echo "1. Pranota Kontainer Sewa (" . $perms1->count() . " found):\n";
foreach ($perms1 as $p) {
    echo "   ✓ $p\n";
}

$perms2 = DB::table('permissions')->where('name', 'like', 'pranota-tagihan-kontainer%')->pluck('name');
echo "\n2. Pranota Tagihan Kontainer (" . $perms2->count() . " found):\n";
foreach ($perms2 as $p) {
    echo "   ✓ $p\n";
}

echo "\n=== COMPLETED ===\n";
