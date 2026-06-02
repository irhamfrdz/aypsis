<?php

// Boot Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Manifest;
use App\Models\Pengirim;
use App\Models\TandaTerima;
use App\Models\TandaTerimaLcl;

echo "Mulai sinkronisasi nama pengirim ke nickname1 di database...\n";

// Get all pengirims with a nickname1 set
$pengirims = Pengirim::whereNotNull('nickname1')
    ->where('nickname1', '!=', '')
    ->get();

$totalFcl = 0;
$totalLcl = 0;
$totalManifest = 0;

foreach ($pengirims as $p) {
    $name = $p->nama_pengirim;
    $nick = $p->nickname1;

    echo "Memproses: '{$name}' -> '{$nick}'\n";

    // 1. Update Tanda Terima FCL
    $fclCount = TandaTerima::where('pengirim', $name)->update(['pengirim' => $nick]);
    if ($fclCount > 0) {
        $totalFcl += $fclCount;
        echo "  - Diupdate {$fclCount} baris di tanda_terimas (FCL)\n";
    }

    // 2. Update Tanda Terima LCL
    $lclCount = TandaTerimaLcl::where('nama_pengirim', $name)->update(['nama_pengirim' => $nick]);
    if ($lclCount > 0) {
        $totalLcl += $lclCount;
        echo "  - Diupdate {$lclCount} baris di tanda_terimas_lcl (LCL)\n";
    }

    // 3. Update Manifest
    $manifestCount = Manifest::where('pengirim', $name)->update(['pengirim' => $nick]);
    if ($manifestCount > 0) {
        $totalManifest += $manifestCount;
        echo "  - Diupdate {$manifestCount} baris di manifests\n";
    }
}

echo "\nSelesai!\n";
echo "Total baris diupdate:\n";
echo "- Tanda Terima FCL: {$totalFcl}\n";
echo "- Tanda Terima LCL: {$totalLcl}\n";
echo "- Manifest: {$totalManifest}\n";
