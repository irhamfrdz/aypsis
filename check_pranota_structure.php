<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle($request = Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== Checking Pranota Structure ===\n\n";

try {
    // Check pranotalist table
    if (Schema::hasTable('pranotalist')) {
        echo "✅ Pranotalist table exists\n";
        $columns = DB::select("DESCRIBE pranotalist");
        echo "Columns:\n";
        foreach($columns as $column) {
            echo "   - {$column->Field}: {$column->Type}\n";
        }

        // Get sample data
        $sample = DB::table('pranotalist')->first();
        if ($sample) {
            echo "\nSample data:\n";
            foreach($sample as $key => $value) {
                echo "   - $key: " . ($value ?? 'NULL') . "\n";
            }
        } else {
            echo "\nNo sample data found\n";
        }
    } else {
        echo "❌ Pranotalist table does not exist\n";
    }

} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

echo "\n=== Selesai ===\n";
