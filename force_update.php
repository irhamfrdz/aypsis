<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Check existing surat jalans
echo "All surat jalans:\n";
$all = DB::select('SELECT id, no_surat_jalan, pranota_surat_jalan_id FROM surat_jalans ORDER BY id DESC LIMIT 5');
foreach ($all as $row) {
    echo "- ID: " . $row->id . ", No: " . $row->no_surat_jalan . ", Pranota ID: " . ($row->pranota_surat_jalan_id ?? 'NULL') . "\n";
}

// Try to update directly via DB
echo "\nTrying to update surat jalan ID 44:\n";
$affected = DB::update('UPDATE surat_jalans SET pranota_surat_jalan_id = ? WHERE id = ?', [12, 44]);
echo "Affected rows: " . $affected . "\n";

// Check again
$check = DB::select('SELECT id, no_surat_jalan, pranota_surat_jalan_id FROM surat_jalans WHERE id = 44');
foreach ($check as $row) {
    echo "- After update - ID: " . $row->id . ", No: " . $row->no_surat_jalan . ", Pranota ID: " . ($row->pranota_surat_jalan_id ?? 'NULL') . "\n";
}