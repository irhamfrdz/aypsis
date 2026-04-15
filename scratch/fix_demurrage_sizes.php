<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BiayaKapalDemurrage;

echo "Memulai update data size kontainer demurrage...\n";

$demurrages = BiayaKapalDemurrage::all();
$updatedCount = 0;

foreach ($demurrages as $demurrage) {
    $kontainerIds = $demurrage->kontainer_ids;
    if (!is_array($kontainerIds)) continue;

    $changed = false;
    foreach ($kontainerIds as $idx => $k) {
        $oldSize = $k['size'] ?? '';
        
        // Normalize size
        $normSize = '20ft'; // Default
        if (str_contains($oldSize, '40')) {
            $normSize = '40ft';
        } elseif (str_contains($oldSize, '20')) {
            $normSize = '20ft';
        } else {
            // If it's just '-' or empty, try to guess from nomor_kontainer if it starts with standard prefixes
            // but for now, 20ft is the safest guess for Meratus if unknown
            $normSize = '20ft';
        }

        if ($oldSize !== $normSize) {
            $kontainerIds[$idx]['size'] = $normSize;
            $changed = true;
        }
    }

    if ($changed) {
        $demurrage->kontainer_ids = $kontainerIds;
        $demurrage->save();
        $updatedCount++;
        echo "Updated Demurrage ID: {$demurrage->id}\n";
    }
}

echo "Selesai. Total data yang diupdate: {$updatedCount}\n";
