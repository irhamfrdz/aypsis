<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Memeriksa kolom-kolom yang dibutuhkan di tabel tujuans...\n";
echo "=========================================================\n";

$requiredColumns = ['uang_jalan_20', 'uang_jalan_40', 'antar_20', 'antar_40'];

try {
    $columns = DB::select('DESCRIBE tujuans');

    echo "Status kolom yang dibutuhkan:\n";
    foreach ($requiredColumns as $requiredColumn) {
        $found = false;
        foreach ($columns as $column) {
            if ($column->Field === $requiredColumn) {
                $found = true;
                echo "✅ {$requiredColumn} - {$column->Type}";
                if ($column->Default !== null) {
                    echo " (Default: {$column->Default})";
                }
                echo "\n";
                break;
            }
        }
        if (!$found) {
            echo "❌ {$requiredColumn} - TIDAK DITEMUKAN\n";
        }
    }

    echo "\nMemeriksa data sample:\n";
    $sampleData = DB::table('tujuans')->first();

    if ($sampleData) {
        echo "Data pertama:\n";
        foreach ($requiredColumns as $column) {
            $value = $sampleData->$column ?? 'NULL';
            echo "- {$column}: Rp " . number_format($value, 0, ',', '.') . "\n";
        }
    } else {
        echo "Tidak ada data di tabel tujuans\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\nSelesai!\n";
