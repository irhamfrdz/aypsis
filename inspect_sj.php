<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$record = \Illuminate\Support\Facades\DB::table('surat_jalan_bongkaran_batams')
    ->where('nomor_surat_jalan', 'like', '%0060063%')
    ->first();

if (!$record) {
    echo "Record 0060063 not found.\n";
    // Search in regular surat_jalan_bongkarans
    $record2 = \Illuminate\Support\Facades\DB::table('surat_jalan_bongkarans')
        ->where('nomor_surat_jalan', 'like', '%0060063%')
        ->first();
    if ($record2) {
        echo "Found in surat_jalan_bongkarans: ID: {$record2->id}, No: {$record2->nomor_surat_jalan}\n";
    } else {
        echo "Not found in surat_jalan_bongkarans either.\n";
    }
    exit;
}

echo "Found record ID: {$record->id}, No: {$record->nomor_surat_jalan}\n";

$controller = new \App\Http\Controllers\SuratJalanBongkaranBatamController();
$response = $controller->getSuratJalanById($record->id);

echo "Response status: " . $response->status() . "\n";
echo "Response content:\n";
echo json_encode(json_decode($response->content()), JSON_PRETTY_PRINT) . "\n";
