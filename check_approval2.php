<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$data = \App\Models\Permohonan::where('approved_by_system_1', true)
    ->where('approved_by_system_2', false)
    ->get(['nomor_memo', 'status', 'approved_by_system_1', 'approved_by_system_2']);

echo "Data yang sudah disetujui system 1 tapi belum system 2:\n";
echo "==================================================\n";

foreach($data as $d) {
    echo $d->nomor_memo . ' - Status: ' . $d->status . ' - Sys1: ' . ($d->approved_by_system_1 ? 'true' : 'false') . ' - Sys2: ' . ($d->approved_by_system_2 ? 'true' : 'false') . "\n";
}

echo "\nTotal: " . $data->count() . " data\n";
