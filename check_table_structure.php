<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$tables = [
    'pranota_tagihan_kontainers',
    'pranota_tagihan_kontainer_sewas',
    'pranota_tagihan_cats',
    'pranotas'
];

foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        echo "Table: $table\n";
        try {
            $columns = Schema::getColumnListing($table);
            echo "Columns: " . implode(', ', $columns) . "\n";

            // Check for records
            $count = DB::table($table)->count();
            echo "Records: $count\n";

            if ($count > 0) {
                // Check for PTKS pattern in no_invoice or similar columns
                $invoiceColumns = ['no_invoice', 'nomor_pranota', 'no_pranota'];
                foreach ($invoiceColumns as $col) {
                    if (in_array($col, $columns)) {
                        $ptksCount = DB::table($table)->where($col, 'like', 'PTKS%')->count();
                        if ($ptksCount > 0) {
                            echo "  PTKS records in $col: $ptksCount\n";
                            $latestPtks = DB::table($table)->where($col, 'like', 'PTKS%')->orderBy($col, 'desc')->first();
                            echo "  Latest PTKS: {$latestPtks->$col}\n";
                        }
                    }
                }
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    } else {
        echo "Table $table does not exist\n\n";
    }
}

// Additional check for pembayaran_pranota_kontainer table structure
echo "=== Database Structure Check ===\n\n";
try {
    $columns = DB::select('DESCRIBE pembayaran_pranota_kontainer');
    echo "Columns in pembayaran_pranota_kontainer table:\n";
    foreach ($columns as $column) {
        echo "- {$column->Field} ({$column->Type})\n";
    }
    echo "\n=== Check Complete ===\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
