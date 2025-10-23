<?php

use Illuminate\Support\Facades\DB;

echo "Checking pembayaran_pranota_surat_jalan table structure:\n";
echo "=" . str_repeat("=", 60) . "\n\n";

try {
    $columns = DB::select('DESCRIBE pembayaran_pranota_surat_jalan');

    echo sprintf("%-30s | %-20s | %-8s | %-10s\n", 'Field', 'Type', 'Null', 'Default');
    echo str_repeat("-", 75) . "\n";

    foreach($columns as $col) {
        $default = $col->Default ?? 'NULL';
        echo sprintf("%-30s | %-20s | %-8s | %-10s\n",
            $col->Field,
            $col->Type,
            $col->Null,
            $default
        );
    }

    // Check specifically for jumlah_pembayaran field
    echo "\n" . str_repeat("=", 75) . "\n";
    echo "Field Analysis:\n";
    foreach($columns as $col) {
        if ($col->Field === 'jumlah_pembayaran') {
            echo "- jumlah_pembayaran: Found, Type: {$col->Type}, Null: {$col->Null}, Default: " . ($col->Default ?? 'NO DEFAULT') . "\n";
        }
    }

    $requiredFields = ['total_pembayaran', 'jumlah_pembayaran'];
    echo "\nRequired fields check:\n";
    foreach ($requiredFields as $field) {
        $found = false;
        foreach($columns as $col) {
            if ($col->Field === $field) {
                $found = true;
                echo "- {$field}: EXISTS (Type: {$col->Type}, Null: {$col->Null})\n";
                break;
            }
        }
        if (!$found) {
            echo "- {$field}: MISSING!\n";
        }
    }

} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}

echo "\n" . str_repeat("=", 75) . "\n";
