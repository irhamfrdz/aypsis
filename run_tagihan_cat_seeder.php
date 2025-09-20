<?php
/**
 * Script untuk menjalankan Tagihan CAT Permissions Seeder
 *
 * Usage:
 * php run_tagihan_cat_seeder.php
 *
 * Atau dengan opsi:
 * php run_tagihan_cat_seeder.php --force
 * php run_tagihan_cat_seeder.php --verbose
 */

echo "========================================\n";
echo "Tagihan CAT Permissions Seeder Runner\n";
echo "========================================\n";

// Bootstrap Laravel
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Parse command line arguments
$force = in_array('--force', $argv);
$verbose = in_array('--verbose', $argv);

echo "Running Tagihan CAT Permissions Seeder...\n";
echo "Force mode: " . ($force ? 'YES' : 'NO') . "\n";
echo "Verbose mode: " . ($verbose ? 'YES' : 'NO') . "\n\n";

// Run the seeder
try {
    $command = 'db:seed --class=TagihanCatPermissionsSeeder';

    if ($force) {
        $command .= ' --force';
    }

    if ($verbose) {
        $command .= ' --verbose';
    }

    $exitCode = $kernel->call($command);

    if ($exitCode === 0) {
        echo "\n✅ Tagihan CAT Permissions Seeder completed successfully!\n";
    } else {
        echo "\n❌ Tagihan CAT Permissions Seeder failed with exit code: $exitCode\n";
    }

} catch (Exception $e) {
    echo "\n❌ Error running seeder: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n========================================\n";
echo "Process completed.\n";
echo "========================================\n";
