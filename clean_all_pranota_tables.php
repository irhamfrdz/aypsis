<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;

echo "=== Pembersihan Database Pranota Lengkap ===\n\n";

try {
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    echo "1. Disabling foreign key checks...\n";

    // List of tables to clean
    $tables = [
        'pembayaran_pranota_kontainer_items',
        'pembayaran_pranota_kontainer',
        'pembayaran_pranota_items',
        'pranotalist'
    ];

    echo "2. Cleaning tables:\n";

    foreach ($tables as $table) {
        try {
            $count = DB::table($table)->count();
            if ($count > 0) {
                DB::statement("TRUNCATE TABLE {$table}");
                echo "   ✅ Truncated {$table} ({$count} records)\n";
            } else {
                echo "   ⭕ {$table} already empty\n";
            }
        } catch (Exception $e) {
            echo "   ❌ Error truncating {$table}: " . $e->getMessage() . "\n";
        }
    }

    // Re-enable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    echo "\n3. Re-enabled foreign key checks\n";

    echo "\n4. Verifying cleanup:\n";
    foreach ($tables as $table) {
        $count = DB::table($table)->count();
        echo "   - {$table}: {$count} records\n";
    }

    echo "\n5. Checking auto increment values:\n";
    foreach ($tables as $table) {
        try {
            $result = DB::select("SHOW TABLE STATUS LIKE '{$table}'");
            if (!empty($result) && isset($result[0]->Auto_increment)) {
                echo "   - {$table}: next ID = " . $result[0]->Auto_increment . "\n";
            }
        } catch (Exception $e) {
            echo "   - {$table}: error checking auto increment\n";
        }
    }

    echo "\n✅ Pembersihan database pranota lengkap berhasil!\n";

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Selesai ===\n";
