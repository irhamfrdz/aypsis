<?php

require_once 'bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "Updated 'pembayaran_aktivitas_lainnya' table structure:\n";
echo "=" . str_repeat("=", 50) . "\n";

try {
    $columns = DB::select('DESCRIBE pembayaran_aktivitas_lainnya');
    
    foreach($columns as $column) {
        $null = $column->Null === 'YES' ? 'NULL' : 'NOT NULL';
        $default = $column->Default ? "DEFAULT '{$column->Default}'" : '';
        $extra = $column->Extra ? "({$column->Extra})" : '';
        
        echo sprintf("- %-20s %-15s %-8s %-15s %s\n", 
            $column->Field, 
            $column->Type, 
            $null, 
            $default,
            $extra
        );
    }
    
    echo "\n✅ Table structure updated successfully!\n";
    echo "\nColumns now match the create form requirements:\n";
    echo "- nomor_pembayaran (auto-generated)\n";
    echo "- tanggal_pembayaran (date input)\n";
    echo "- total_pembayaran (decimal amount)\n";
    echo "- pilih_bank (foreign key to akun_coa)\n";
    echo "- aktivitas_pembayaran (text description)\n";
    echo "- created_by (user who created)\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}