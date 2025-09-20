<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding additional Tagihan CAT permissions...\n";

$additionalPermissions = [
    [
        'name' => 'tagihan-cat-print',
        'description' => 'Print Tagihan CAT',
    ],
    [
        'name' => 'tagihan-cat-export',
        'description' => 'Export Tagihan CAT',
    ],
    [
        'name' => 'tagihan-cat-import',
        'description' => 'Import Tagihan CAT',
    ],
];

$added = 0;
$skipped = 0;

foreach ($additionalPermissions as $permission) {
    $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

    if (!$exists) {
        DB::table('permissions')->insert([
            'name' => $permission['name'],
            'description' => $permission['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        echo "✓ Added permission: {$permission['name']}\n";
        $added++;
    } else {
        echo "- Skipped existing permission: {$permission['name']}\n";
        $skipped++;
    }
}

echo "\nSummary:\n";
echo "Added: {$added} permissions\n";
echo "Skipped: {$skipped} permissions\n";
echo "Total: " . ($added + $skipped) . " permissions processed\n";

echo "\nAll Tagihan CAT permissions:\n";
$currentPermissions = DB::table('permissions')
    ->where('name', 'like', 'tagihan-cat%')
    ->get();

foreach ($currentPermissions as $perm) {
    echo "- {$perm->name} (ID: {$perm->id})\n";
}

echo "\nAdditional Tagihan CAT permissions setup completed!\n";
