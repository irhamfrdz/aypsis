<?php

use Illuminate\Support\Facades\DB;
use App\Models\Manifest;
use App\Models\Perincian;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Memulai sinkronisasi data dari Manifest ke Perincian...\n";

// Count total manifests
$totalManifests = Manifest::count();
echo "Total data Manifest yang akan disalin: $totalManifests\n";

if ($totalManifests === 0) {
    echo "Tidak ada data manifest untuk disalin.\n";
    exit;
}

// Clear existing perincians just in case to avoid duplicates if run multiple times
// WARNING: Only do this if perincians is strictly meant to be a 1:1 mirror right now.
// For safety, let's use insertOrIgnore or check for existence, or just truncate since it's a new feature.
echo "Mengosongkan tabel perincian sebelum sinkronisasi...\n";
DB::table('perincians')->truncate();

$chunkSize = 500;
$processed = 0;

Manifest::orderBy('id')->chunk($chunkSize, function ($manifests) use (&$processed, $totalManifests) {
    $insertData = [];
    $now = now();

    foreach ($manifests as $manifest) {
        $data = $manifest->getAttributes();
        
        $insertData[] = $data;
    }

    // Bulk insert
    DB::table('perincians')->insert($insertData);

    $processed += count($manifests);
    echo "Progress: $processed / $totalManifests disalin.\n";
});

echo "Sinkronisasi selesai! $processed data berhasil dimasukkan ke tabel perincian.\n";
