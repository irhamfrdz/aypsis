<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // 1. Prospek
    $invalidProspeks = \App\Models\Prospek::where('status', 'sudah_muat')
        ->whereDoesntHave('manifests')
        ->whereDoesntHave('bls')
        ->whereDoesntHave('naikKapal', function ($q) {
            $q->where('sudah_ob', true);
        })
        ->get();

    echo "Found " . $invalidProspeks->count() . " Prospek (Jakarta) with 'sudah_muat' but not in Manifest, BL, or OB Muat.\n";
    foreach($invalidProspeks->take(5) as $p) {
        echo " - ID: {$p->id}, SJ: {$p->no_surat_jalan}, Kontainer: {$p->nomor_kontainer}\n";
    }

    $countProspek = \App\Models\Prospek::where('status', 'sudah_muat')
        ->whereDoesntHave('manifests')
        ->whereDoesntHave('bls')
        ->whereDoesntHave('naikKapal', function ($q) {
            $q->where('sudah_ob', true);
        })
        ->update(['status' => 'aktif']);

    echo "✅ Updated $countProspek Prospek (Jakarta) back to 'aktif'.\n\n";

    // 2. ProspekBatam
    if (class_exists(\App\Models\ProspekBatam::class)) {
        $invalidProspekBatam = \App\Models\ProspekBatam::where('status', 'sudah_muat')
            ->whereDoesntHave('bls')
            // Add whereDoesntHave manifests/naikKapal if they apply, but ProspekBatamController only checks BLs. Let's be safe.
            ->get();

        echo "Found " . $invalidProspekBatam->count() . " Prospek (Batam) with 'sudah_muat' but no BL.\n";
        
        $countProspekBatam = \App\Models\ProspekBatam::where('status', 'sudah_muat')
            ->whereDoesntHave('bls')
            ->update(['status' => 'aktif']);
            
        echo "✅ Updated $countProspekBatam Prospek (Batam) back to 'aktif'.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
