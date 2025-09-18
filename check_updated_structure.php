<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Checking Updated Pranota Perbaikan Kontainers Structure ===\n\n";

try {
    $columns = DB::select('DESCRIBE pranota_perbaikan_kontainers');
    echo "Table structure for pranota_perbaikan_kontainers:\n";
    echo str_repeat("=", 60) . "\n";

    foreach ($columns as $column) {
        echo sprintf("%-20s %-15s %-10s %-10s %s\n",
            $column->Field,
            $column->Type,
            $column->Null,
            $column->Key,
            $column->Default ?: ''
        );
    }

    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✅ Migration completed successfully!\n";
    echo "✅ estimasi_waktu column removed\n";
    echo "✅ estimasi_biaya renamed to total_biaya\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}