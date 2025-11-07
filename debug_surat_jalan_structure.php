<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking surat_jalans table structure:\n";
$columns = Schema::getColumnListing('surat_jalans');
foreach ($columns as $column) {
    echo "- $column\n";
}

echo "\nChecking if pranota_surat_jalan_id field exists:\n";
if (in_array('pranota_surat_jalan_id', $columns)) {
    echo "✓ pranota_surat_jalan_id field exists\n";
    
    // Check if our specific surat jalan has a pranota linked
    echo "\nChecking surat jalan with pranota_surat_jalan_id = 12:\n";
    $suratJalan = DB::table('surat_jalans')->where('pranota_surat_jalan_id', 12)->first();
    if ($suratJalan) {
        echo "✓ Found surat jalan: " . $suratJalan->no_surat_jalan . "\n";
        echo "  - Supir: " . ($suratJalan->supir ?? '-') . "\n";
        echo "  - Tujuan ID: " . ($suratJalan->tujuan_id ?? '-') . "\n";
    } else {
        echo "✗ No surat jalan found with pranota_surat_jalan_id = 12\n";
    }
    
} else {
    echo "✗ pranota_surat_jalan_id field does not exist\n";
    echo "Available fields related to pranota: none\n";
}