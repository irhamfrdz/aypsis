<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$dokumens = \DB::table('biaya_kapal_dokumens')->get();
// Use master_kapals instead of kapals
$allKapals = \App\Models\MasterKapal::pluck('nama_kapal')->toArray();

$updated = 0;

$cleanStr = function($str) {
    return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $str));
};

foreach ($dokumens as $dok) {
    if (in_array($dok->kapal, $allKapals)) {
        continue;
    }
    
    $dokClean = $cleanStr($dok->kapal);
    $bestMatch = null;
    
    foreach ($allKapals as $k) {
        $kClean = $cleanStr($k);
        if ($kClean === $dokClean) {
            $bestMatch = $k;
            break;
        }
    }
    
    if (!$bestMatch) {
        // Try fallback to search inside
        foreach ($allKapals as $k) {
            if (strpos($cleanStr($k), $dokClean) !== false || strpos($dokClean, $cleanStr($k)) !== false) {
                $bestMatch = $k;
                break;
            }
        }
    }
    
    if ($bestMatch) {
        \DB::table('biaya_kapal_dokumens')
            ->where('id', $dok->id)
            ->update(['kapal' => $bestMatch]);
        $updated++;
        echo "Updated '{$dok->kapal}' to '{$bestMatch}'\n";
    } else {
        echo "Could not find match for '{$dok->kapal}'\n";
    }
}
echo "Total updated: $updated\n";
