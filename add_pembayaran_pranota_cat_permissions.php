<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Adding Pembayaran Pranota CAT permissions...\n";

$permissions = [
    [
        'name' => 'pembayaran-pranota-cat.view',
        'description' => 'Melihat daftar pembayaran pranota CAT',
    ],
    [
        'name' => 'pembayaran-pranota-cat.create',
        'description' => 'Membuat pembayaran pranota CAT baru',
    ],
    [
        'name' => 'pembayaran-pranota-cat.update',
        'description' => 'Mengupdate data pembayaran pranota CAT',
    ],
    [
        'name' => 'pembayaran-pranota-cat.delete',
        'description' => 'Menghapus data pembayaran pranota CAT',
    ],
];

$added = 0;
$skipped = 0;

foreach ($permissions as $permission) {
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

echo "\nSummary: $added added, $skipped skipped\n";
echo "Pembayaran Pranota CAT permissions setup completed!\n";
