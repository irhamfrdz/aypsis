<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\BiayaKapal;
use App\Models\BiayaKapalDokumen;

try {
    DB::beginTransaction();
    $count = 0;

    $oldBiayas = BiayaKapal::whereHas('klasifikasiBiaya', function($q) {
        $q->where('nama', 'like', '%Dokumen%');
    })->get();

    foreach($oldBiayas as $biaya) {
        // Check if dokumens already exist
        if ($biaya->dokumens()->count() > 0) {
            continue; // Already migrated or created in new format
        }

        $kapals = is_array($biaya->nama_kapal) ? $biaya->nama_kapal : (json_decode($biaya->nama_kapal, true) ?? [$biaya->nama_kapal]);
        $voyages = is_array($biaya->no_voyage) ? $biaya->no_voyage : (json_decode($biaya->no_voyage, true) ?? [$biaya->no_voyage]);
        $bls = is_array($biaya->no_bl) ? $biaya->no_bl : (json_decode($biaya->no_bl, true) ?? [$biaya->no_bl]);

        // Iterate assuming array size matches if there are multiple. 
        foreach($kapals as $i => $k) {
            $kapal = $k;
            $voyage = $voyages[$i] ?? ($voyages[0] ?? null);
            $bl = $bls[$i] ?? ($bls[0] ?? null);

            if (!$kapal && !$voyage) continue;

            BiayaKapalDokumen::create([
                'biaya_kapal_id' => $biaya->id,
                'kapal' => $kapal,
                'voyage' => $voyage,
                'vendor_id' => $biaya->vendor_id,
                'nomor_bl' => $bl,
                'nominal' => $biaya->nominal ?? 0,
                'pph' => $biaya->pph_dokumen ?? ($biaya->pph ?? 0),
                'total_biaya' => $biaya->grand_total_dokumen ?? ($biaya->total_biaya ?? 0),
            ]);
            $count++;
        }
    }

    DB::commit();
    echo "Successfully migrated $count records.\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
