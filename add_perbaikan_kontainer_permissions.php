<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Permission;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding Perbaikan Kontainer permissions...\n";

$permissions = [
    [
        'name' => 'master-perbaikan-kontainer.view',
        'description' => 'Melihat daftar perbaikan kontainer',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'master-perbaikan-kontainer.create',
        'description' => 'Membuat perbaikan kontainer baru',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'master-perbaikan-kontainer.update',
        'description' => 'Mengupdate data perbaikan kontainer',
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'master-perbaikan-kontainer.delete',
        'description' => 'Menghapus data perbaikan kontainer',
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

$inserted = 0;
$skipped = 0;

foreach ($permissions as $permission) {
    $exists = DB::table('permissions')->where('name', $permission['name'])->exists();

    if (!$exists) {
        DB::table('permissions')->insert($permission);
        echo "✓ Added permission: {$permission['name']}\n";
        $inserted++;
    } else {
        echo "- Skipped existing permission: {$permission['name']}\n";
        $skipped++;
    }
}

echo "\nSummary:\n";
echo "Inserted: $inserted permissions\n";
echo "Skipped: $skipped permissions\n";
echo "Total processed: " . ($inserted + $skipped) . " permissions\n";

echo "\nPerbaikan Kontainer permissions setup completed!\n";
