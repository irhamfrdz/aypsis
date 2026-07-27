<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$biayas = \DB::table('biaya_kapals')->get();
$updated = 0;

foreach ($biayas as $biaya) {
    $doks = \DB::table('biaya_kapal_dokumens')->where('biaya_kapal_id', $biaya->id)->get();
    
    if ($doks->count() <= 1) {
        continue; // If only 1 section, copying the full nominal was actually correct.
    }
    
    $sumNominal = $doks->sum('nominal');
    
    // If sum of sections is much larger than invoice nominal, it's the flawed migration
    if ($sumNominal > $biaya->nominal && $biaya->nominal > 0) {
        
        $totalBls = 0;
        foreach ($doks as $dok) {
            if (empty(trim($dok->nomor_bl))) continue;
            $blCount = count(array_filter(array_map('trim', explode(',', $dok->nomor_bl))));
            $totalBls += $blCount;
        }
        
        if ($totalBls > 0) {
            $nominalPerBl = $biaya->nominal / $totalBls;
            $pphPerBl = $biaya->pph_dokumen / $totalBls;
            $totalPerBl = $biaya->grand_total_dokumen / $totalBls;
            
            foreach ($doks as $dok) {
                $blCount = 0;
                if (!empty(trim($dok->nomor_bl))) {
                    $blCount = count(array_filter(array_map('trim', explode(',', $dok->nomor_bl))));
                }
                
                $newNominal = $blCount * $nominalPerBl;
                $newPph = $blCount * $pphPerBl;
                $newTotal = $blCount * $totalPerBl;
                
                \DB::table('biaya_kapal_dokumens')->where('id', $dok->id)->update([
                    'nominal' => $newNominal,
                    'pph' => $newPph,
                    'total_biaya' => $newTotal
                ]);
            }
            $updated++;
            echo "Fixed nominals for Invoice ID: {$biaya->id} (Total BLs: $totalBls)\n";
        }
    }
}

echo "Total invoices fixed: $updated\n";
