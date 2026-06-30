<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $sj = \App\Models\SuratJalan::where('nomor', 'JP0013490')
        ->orWhere('nomor_surat_jalan', 'JP0013490')
        ->first();
} catch (\Exception $e) {
    $sj = null;
    echo "Error querying SJ: " . $e->getMessage() . "\n";
}

echo "--- Surat Jalan ---\n";
if ($sj) {
    print_r($sj->toArray());
} else {
    echo "Not found\n";
}

try {
    $kontainer = \App\Models\Kontainer::where('nomor_kontainer', 'AYPU3707235')->first();
} catch (\Exception $e) {
    $kontainer = null;
    echo "Error querying Kontainer: " . $e->getMessage() . "\n";
}

echo "\n--- Kontainer ---\n";
if ($kontainer) {
    print_r($kontainer->toArray());
} else {
    echo "Not found\n";
}

try {
    $voyage = \App\Models\Voyage::where('nomor', 'AS05JP26')->first();
} catch (\Exception $e) {
    $voyage = null;
    echo "Error querying Voyage: " . $e->getMessage() . "\n";
}
echo "\n--- Voyage ---\n";
if ($voyage) {
    print_r($voyage->toArray());
} else {
    echo "Not found\n";
}

try {
    $manifest = \App\Models\Manifest::where('surat_jalan_id', $sj?->id)->get();
    echo "\n--- Manifest by SJ ID ---\n";
    print_r($manifest->toArray());
} catch (\Exception $e) {
    echo "Error querying Manifest: " . $e->getMessage() . "\n";
}

