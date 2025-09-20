<?php

/**
 * Script untuk menjalankan TagihanCatPermissionsSeeder
 * Jalankan dengan: php run_tagihan_cat_permissions_seeder.php
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "==========================================\n";
echo "   Menjalankan TagihanCatPermissionsSeeder\n";
echo "==========================================\n";

try {
    // Jalankan seeder
    Artisan::call('db:seed', [
        '--class' => 'TagihanCatPermissionsSeeder'
    ]);

    echo "✅ Seeder berhasil dijalankan!\n";
    echo "\nOutput:\n";
    echo Artisan::output();

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\n==========================================\n";
echo "   Selesai\n";
echo "==========================================\n";
