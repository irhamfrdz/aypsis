<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Karyawan;

echo "=== STATUS PAJAK DATA ===\n";
$statuses = Karyawan::select('status_pajak')->distinct()->whereNotNull('status_pajak')->get();

foreach($statuses as $status) {
    echo "Status: [" . $status->status_pajak . "]\n";
}

echo "\n=== RECENT DATA ===\n";
$recent = Karyawan::select('nama_lengkap', 'status_pajak')->whereNotNull('status_pajak')->take(10)->get();

foreach($recent as $data) {
    echo $data->nama_lengkap . " => [" . $data->status_pajak . "]\n";
}
