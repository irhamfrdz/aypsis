<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$noVoyage = 'AP06JP26';
$user = \App\Models\User::first(); // Just get the first user

$obController = app(\App\Http\Controllers\ObController::class);

// Get all containers that are sudah_ob for this voyage
$naikKapals = \App\Models\NaikKapal::where('no_voyage', $noVoyage)->where('sudah_ob', true)->get();

$createdCount = 0;
foreach($naikKapals as $nk) {
    // Check if manifest already exists
    $manifestExists = \App\Models\Manifest::where('no_voyage', $noVoyage)
        ->where('nomor_kontainer', $nk->nomor_kontainer)
        ->exists();
        
    if(!$manifestExists) {
        echo "Creating manifest for container: " . $nk->nomor_kontainer . "\n";
        try {
            $obController->createManifestForNaikKapal($nk, $user);
            $createdCount++;
        } catch (\Exception $e) {
            echo "Error for " . $nk->nomor_kontainer . ": " . $e->getMessage() . "\n";
        }
    }
}

echo "\nDone! Created $createdCount missing manifests.\n";
