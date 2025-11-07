<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

// Direct SQL check
$result = DB::select('SELECT id, no_surat_jalan, pranota_surat_jalan_id FROM surat_jalans WHERE pranota_surat_jalan_id = 12');
echo "Direct SQL result: " . count($result) . "\n";
foreach ($result as $row) {
    echo "- ID: " . $row->id . ", No: " . $row->no_surat_jalan . ", Pranota ID: " . $row->pranota_surat_jalan_id . "\n";
}

// Check the specific surat jalan we just updated
$sj44 = DB::select('SELECT id, no_surat_jalan, pranota_surat_jalan_id FROM surat_jalans WHERE id = 44');
echo "\nSurat Jalan ID 44:\n";
foreach ($sj44 as $row) {
    echo "- ID: " . $row->id . ", No: " . $row->no_surat_jalan . ", Pranota ID: " . $row->pranota_surat_jalan_id . "\n";
}