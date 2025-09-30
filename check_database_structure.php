<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== CEK STRUKTUR DATABASE ===\n";

// Cek semua tabel yang ada
$tables = DB::select('SHOW TABLES');
echo "Tabel yang ada di database:\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    echo "- $tableName\n";
}

echo "\n=== CARI TABEL PIVOT PERMISSION ===\n";

// Cari tabel yang mungkin berisi relasi permission-user
$possiblePivotTables = [
    'permission_user',
    'user_permissions',
    'user_permission',
    'permissions_users',
    'laravel_permission_user'
];

foreach ($possiblePivotTables as $tableName) {
    if (Schema::hasTable($tableName)) {
        echo "✅ Ditemukan tabel pivot: $tableName\n";

        // Cek struktur tabel
        $columns = Schema::getColumnListing($tableName);
        echo "Kolom: " . implode(', ', $columns) . "\n";

        // Cek jumlah record
        $count = DB::table($tableName)->count();
        echo "Jumlah record: $count\n";

        // Cek beberapa sample data
        if ($count > 0) {
            $samples = DB::table($tableName)->limit(3)->get();
            echo "Sample data:\n";
            foreach ($samples as $sample) {
                echo "  " . json_encode($sample) . "\n";
            }
        }
        break;
    } else {
        echo "❌ Tidak ada tabel: $tableName\n";
    }
}

// Cek apakah ada foreign key constraint yang menunjukkan relasi
echo "\n=== CEK CONSTRAINT DATABASE ===\n";
try {
    $constraints = DB::select("
        SELECT
            TABLE_NAME,
            COLUMN_NAME,
            CONSTRAINT_NAME,
            REFERENCED_TABLE_NAME,
            REFERENCED_COLUMN_NAME
        FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
        WHERE REFERENCED_TABLE_SCHEMA = DATABASE()
        AND (REFERENCED_TABLE_NAME = 'permissions' OR TABLE_NAME = 'permissions')
    ");

    echo "Foreign key constraints terkait permissions:\n";
    foreach ($constraints as $constraint) {
        echo "- {$constraint->TABLE_NAME}.{$constraint->COLUMN_NAME} -> {$constraint->REFERENCED_TABLE_NAME}.{$constraint->REFERENCED_COLUMN_NAME}\n";
    }
} catch (Exception $e) {
    echo "Tidak bisa mengakses INFORMATION_SCHEMA: " . $e->getMessage() . "\n";
}
