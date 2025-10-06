<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\MasterKegiatan;

echo "Data Master Kegiatan:\n";
echo "==========================================\n";

$kegiatans = MasterKegiatan::all();

foreach ($kegiatans as $k) {
    echo sprintf(
        "%s - %s - %s\n",
        $k->kode_kegiatan,
        $k->nama_kegiatan,
        $k->status
    );
}

echo "==========================================\n";
echo "Total: " . $kegiatans->count() . " data\n";
