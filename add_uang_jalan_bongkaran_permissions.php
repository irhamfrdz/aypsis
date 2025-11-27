<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use Illuminate\Support\Facades\DB;

echo "🔧 Menambahkan Permission Uang Jalan Bongkaran (Jika belum ada)\n";

$permissions = [
    [
        'name' => 'uang-jalan-bongkaran-view',
        'description' => 'Melihat data uang jalan bongkaran'
    ],
    [
        'name' => 'uang-jalan-bongkaran-create',
        'description' => 'Membuat data uang jalan bongkaran baru'
    ],
    [
        'name' => 'uang-jalan-bongkaran-update',
        'description' => 'Mengubah data uang jalan bongkaran'
    ],
    [
        'name' => 'uang-jalan-bongkaran-delete',
        'description' => 'Menghapus data uang jalan bongkaran'
    ],
    // Optional: print/export
    // [ 'name' => 'uang-jalan-bongkaran-print', 'description' => 'Mencetak data uang jalan bongkaran' ],
    // [ 'name' => 'uang-jalan-bongkaran-export', 'description' => 'Mengexport data uang jalan bongkaran' ],
];

$added = 0;
$skipped = 0;

DB::transaction(function () use ($permissions, &$added, &$skipped) {
    foreach ($permissions as $perm) {
        $existing = Permission::where('name', $perm['name'])->first();
        if ($existing) {
            echo "⚠️  {$perm['name']} already exists\n";
            $skipped++;
            continue;
        }

        Permission::create([
            'name' => $perm['name'],
            'description' => $perm['description'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        echo "✅ Added permission: {$perm['name']}\n";
        $added++;
    }
});

echo "\n🎉 Done. Added: {$added}. Skipped: {$skipped}.\n";

echo "\n📋 Action: You can also run the Laravel seeder: php artisan db:seed --class=UangJalanBongkaranPermissionSeeder\n";
