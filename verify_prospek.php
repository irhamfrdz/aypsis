<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get the records that were updated in the last 15 minutes to 'aktif'
$updatedProspeks = \App\Models\Prospek::where('status', 'aktif')
    ->where('updated_at', '>=', now()->subMinutes(15))
    ->get();

$falsePositives = 0;
$truePositives = 0;

foreach ($updatedProspeks as $prospek) {
    // 1. Is it in Manifests?
    $inManifest = \App\Models\Manifest::where('prospek_id', $prospek->id)->exists();
    if (! $inManifest && ! empty($prospek->nomor_kontainer) && ! empty($prospek->no_voyage)) {
        $inManifest = \App\Models\Manifest::where('nomor_kontainer', $prospek->nomor_kontainer)
            ->where('no_voyage', $prospek->no_voyage)
            ->exists();
    }

    // 2. Is it in BLs?
    $inBl = \App\Models\Bl::where('prospek_id', $prospek->id)->exists();
    if (! $inBl && ! empty($prospek->nomor_kontainer) && ! empty($prospek->no_voyage)) {
        $inBl = \App\Models\Bl::where('nomor_kontainer', $prospek->nomor_kontainer)
            ->where('no_voyage', $prospek->no_voyage)
            ->exists();
    }

    // 3. Is it in NaikKapal with sudah_ob = true?
    $inNaikKapal = \App\Models\NaikKapal::where('prospek_id', $prospek->id)
        ->where('sudah_ob', true)
        ->exists();
    if (! $inNaikKapal && ! empty($prospek->nomor_kontainer) && ! empty($prospek->no_voyage)) {
        $inNaikKapal = \App\Models\NaikKapal::where('nomor_kontainer', $prospek->nomor_kontainer)
            ->where('no_voyage', $prospek->no_voyage)
            ->where('sudah_ob', true)
            ->exists();
    }

    if ($inManifest || $inBl || $inNaikKapal) {
        $falsePositives++;
        // Revert it back to sudah_muat
        $prospek->update(['status' => 'sudah_muat']);
    } else {
        $truePositives++;
    }
}

echo 'Total recently updated records checked: '.$updatedProspeks->count()."\n";
echo "False Positives (Actually HAVE Manifest/BL/OB Muat, Reverted back to sudah_muat): $falsePositives\n";
echo "True Positives (Correctly kept as aktif, definitely NOT loaded): $truePositives\n";
