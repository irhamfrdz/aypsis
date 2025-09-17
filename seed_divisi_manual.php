<?php

/**
 * Manual Divisi Seeder Runner
 * Usage: php seed_divisi_manual.php
 */

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Divisi;

echo "==========================================\n";
echo "   Manual Divisi Seeder Runner\n";
echo "==========================================\n";

try {
    // Check if DivisiSeeder class exists
    if (!class_exists('Database\\Seeders\\DivisiSeeder')) {
        throw new Exception('DivisiSeeder class not found. Please ensure the file exists in database/seeders/');
    }

    echo "✅ DivisiSeeder class found\n";
    echo "🔄 Running Divisi Seeder...\n\n";

    // Run the seeder
    $seeder = new Database\Seeders\DivisiSeeder();
    $seeder->run();

    echo "\n==========================================\n";
    echo "   Seeder Execution Summary\n";
    echo "==========================================\n";
    echo "✅ Master Divisi data has been seeded\n\n";

    // Show current count
    $totalDivisis = Divisi::count();
    $activeDivisis = Divisi::where('is_active', true)->count();

    echo "📊 Database Summary:\n";
    echo "- Total Divisis: {$totalDivisis}\n";
    echo "- Active Divisis: {$activeDivisis}\n\n";

    echo "Next steps:\n";
    echo "1. Check the web interface: Master → Divisi\n";
    echo "2. Verify data in database\n\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Please check the error and try again.\n";
    exit(1);
}

echo "==========================================\n";
