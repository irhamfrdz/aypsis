<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memulai pembersihan data shipper dan consignee untuk voyage JB (tanpa shipper_id)...\n";

$updateData = [
    'pengirim' => null,
    'alamat_pengirim' => null,
    'penerima' => null,
    'alamat_penerima' => null,
    'notify_party' => null,
    'alamat_notify_party' => null
];

// Update Manifests
$manifestsUpdated = DB::table('manifests')
    ->where('no_voyage', 'like', '%JB%')
    ->whereNull('shipper_id')
    ->update($updateData);

echo "Tabel manifests: $manifestsUpdated baris berhasil diupdate menjadi kosong.\n";

// Update Perincians
$perinciansUpdated = DB::table('perincians')
    ->where('no_voyage', 'like', '%JB%')
    ->whereNull('shipper_id')
    ->update($updateData);

echo "Tabel perincians: $perinciansUpdated baris berhasil diupdate menjadi kosong.\n";

echo "Selesai!\n";
