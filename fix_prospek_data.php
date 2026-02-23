<?php

use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Get all LCL Prospek records
$prospeks = DB::table('prospek')->where('tipe', 'lcl')->get();

$fixCount = 0;
foreach($prospeks as $p) {
    // Find the latest seal number from LCL pivots for this container
    $pivot = DB::table('tanda_terima_lcl_kontainer_pivot')
        ->where('nomor_kontainer', $p->nomor_kontainer)
        ->whereNotNull('nomor_seal')
        ->where('nomor_seal', '!=', '')
        ->orderBy('updated_at', 'desc')
        ->first();
    
    if ($pivot) {
        if ($p->no_seal != $pivot->nomor_seal || !empty($p->tanda_terima_id)) {
            echo "Fixing ID: {$p->id}, Container: {$p->nomor_kontainer}, Old Seal: {$p->no_seal}, New Seal: {$pivot->nomor_seal}\n";
            DB::table('prospek')->where('id', $p->id)->update([
                'no_seal' => $pivot->nomor_seal,
                'tanda_terima_id' => null // Ensure FCL link is removed
            ]);
            $fixCount++;
        }
    } else {
        // Check legacy table
        $legacy = DB::table('tanda_terima_lcl')
            ->where('nomor_kontainer', $p->nomor_kontainer)
            ->whereNotNull('nomor_seal')
            ->where('nomor_seal', '!=', '')
            ->orderBy('updated_at', 'desc')
            ->first();
        
        if ($legacy && ($p->no_seal != $legacy->nomor_seal || !empty($p->tanda_terima_id))) {
            echo "Fixing ID: {$p->id} (Legacy), Container: {$p->nomor_kontainer}, Old Seal: {$p->no_seal}, New Seal: {$legacy->nomor_seal}\n";
            DB::table('prospek')->where('id', $p->id)->update([
                'no_seal' => $legacy->nomor_seal,
                'tanda_terima_id' => null
            ]);
            $fixCount++;
        }
    }
}

echo "Total records fixed: {$fixCount}\n";
