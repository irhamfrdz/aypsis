<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING PERMISSION FORMAT ===\n\n";

// Cek format permissions yang sudah ada
$pranotaPermissions = DB::table('permissions')
    ->where('name', 'like', '%pranota-supir%')
    ->get();

echo "Format permissions pranota-supir yang sudah ada:\n";
foreach ($pranotaPermissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n";

$pembayaranPermissions = DB::table('permissions')
    ->where('name', 'like', '%pembayaran-pranota-supir%')
    ->get();

echo "Format permissions pembayaran-pranota-supir yang sudah ada:\n";
foreach ($pembayaranPermissions as $perm) {
    echo "- {$perm->name}\n";
}

echo "\n";

$permohonanPermissions = DB::table('permissions')
    ->where('name', 'like', '%permohonan%')
    ->get();

echo "Format permissions permohonan yang sudah ada:\n";
foreach ($permohonanPermissions as $perm) {
    echo "- {$perm->name}\n";
}

?>
