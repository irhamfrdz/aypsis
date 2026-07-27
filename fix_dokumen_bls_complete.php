<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$oldBiayas = \DB::table('biaya_kapals')->whereExists(function ($query) {
    $query->select(\DB::raw(1))
          ->from('biaya_kapal_dokumens')
          ->whereRaw('biaya_kapal_dokumens.biaya_kapal_id = biaya_kapals.id');
})->get();

$updatedSections = 0;
$createdSections = 0;

foreach ($oldBiayas as $biaya) {
    if (empty($biaya->no_bl)) continue;
    
    $blIds = json_decode($biaya->no_bl, true);
    if (!is_array($blIds)) {
        $blIds = [$biaya->no_bl];
    }
    
    if (empty($blIds)) continue;
    
    // Fetch all BL records
    $blRecords = \DB::table('bls')->whereIn('id', $blIds)->get();
    
    // Group by voyage and kapal
    $groupedBls = [];
    foreach ($blRecords as $blRec) {
        // Some older data might have variations in spacing, let's normalize slightly but use raw DB value for querying
        $key = trim($blRec->no_voyage);
        if (!isset($groupedBls[$key])) {
            $groupedBls[$key] = [
                'kapal' => $blRec->nama_kapal,
                'voyage' => $blRec->no_voyage,
                'nomor_bls' => []
            ];
        }
        if ($blRec->nomor_bl && !in_array($blRec->nomor_bl, $groupedBls[$key]['nomor_bls'])) {
            $groupedBls[$key]['nomor_bls'][] = $blRec->nomor_bl;
        }
    }
    
    foreach ($groupedBls as $group) {
        // Find existing section by voyage
        $existing = \DB::table('biaya_kapal_dokumens')
            ->where('biaya_kapal_id', $biaya->id)
            ->where('voyage', $group['voyage'])
            ->first();
            
        $blsString = implode(', ', $group['nomor_bls']);
            
        if ($existing) {
            // Check if we need to update
            if ($existing->nomor_bl !== $blsString || $existing->kapal !== $group['kapal']) {
                \DB::table('biaya_kapal_dokumens')
                    ->where('id', $existing->id)
                    ->update([
                        'nomor_bl' => $blsString,
                        'kapal' => $group['kapal']
                    ]);
                $updatedSections++;
            }
        } else {
            \DB::table('biaya_kapal_dokumens')->insert([
                'biaya_kapal_id' => $biaya->id,
                'kapal' => $group['kapal'],
                'voyage' => $group['voyage'],
                'vendor_id' => $biaya->vendor_id,
                'nomor_bl' => $blsString,
                'nominal' => 0, 
                'pph' => 0,
                'total_biaya' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $createdSections++;
        }
    }
}
echo "Updated $updatedSections sections.\n";
echo "Created $createdSections sections.\n";
