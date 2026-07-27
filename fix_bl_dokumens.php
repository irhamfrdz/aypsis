<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    $dokumens = DB::table('biaya_kapal_dokumens')->get();
    $updated = 0;
    
    foreach ($dokumens as $dok) {
        if (!empty($dok->nomor_bl)) {
            $blIds = array_map('trim', explode(',', $dok->nomor_bl));
            $newBls = [];
            $changed = false;
            
            foreach ($blIds as $bl) {
                if (is_numeric($bl)) {
                    $blRecord = DB::table('bls')->where('id', $bl)->first();
                    if ($blRecord && !empty($blRecord->nomor_bl)) {
                        $newBls[] = $blRecord->nomor_bl;
                        $changed = true;
                    } else {
                        $newBls[] = $bl;
                    }
                } else {
                    $newBls[] = $bl;
                }
            }
            
            if ($changed) {
                DB::table('biaya_kapal_dokumens')->where('id', $dok->id)->update(['nomor_bl' => implode(', ', $newBls)]);
                $updated++;
            }
        }
    }
    echo "Updated $updated records.\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
