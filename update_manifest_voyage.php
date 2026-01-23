<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\Manifest;

$voyageNumber = 'AP01BJ26';
$newDate = '2026-01-20';

echo "Sedang memperbarui data manifest untuk nomor voyage: $voyageNumber...\n";

$affected = Manifest::where('no_voyage', $voyageNumber)
    ->update(['tanggal_berangkat' => $newDate]);

echo "Berhasil memperbarui $affected baris data.\n";
