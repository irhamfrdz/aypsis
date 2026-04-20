<?php

use App\Models\PranotaUangRit;
use App\Models\PranotaUangRitSupirDetail;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

/**
 * Script to fix calculation discrepancies in Pranota Uang Rit records
 * specified by their no_pranota.
 */

$no_pranotas = ['PUR-04-26-000017']; // Add more numbers if needed

foreach ($no_pranotas as $no_pranota) {
    $p = PranotaUangRit::where('no_pranota', $no_pranota)->first();

    if ($p) {
        $details = PranotaUangRitSupirDetail::where('no_pranota', $no_pranota)->get();
        
        $total_uang = $details->sum('total_uang_supir');
        $total_hutang = $details->sum('hutang');
        $total_tabungan = $details->sum('tabungan');
        $total_bpjs = $details->sum('bpjs');
        $grand_total = $total_uang - $total_hutang - $total_tabungan - $total_bpjs;
        
        echo "Processing Record: $no_pranota\n";
        echo " - Current in DB: Total Uang: {$p->total_uang}, Tabungan: {$p->total_tabungan}, Grand Total: {$p->grand_total_bersih}\n";
        echo " - Recalculated from Details: Total Uang: $total_uang, Tabungan: $total_tabungan, Grand Total: $grand_total\n";
        
        // Use DB table update to avoid triggering potentially buggy model events during the migration/fix
        DB::table('pranota_uang_rits')->where('no_pranota', $no_pranota)->update([
            'total_uang' => $total_uang,
            'total_rit' => $total_uang,
            'total_hutang' => $total_hutang,
            'total_tabungan' => $total_tabungan,
            'total_bpjs' => $total_bpjs,
            'grand_total_bersih' => $grand_total,
            'updated_at' => now()
        ]);
        
        echo " - Successfully fixed record $no_pranota\n\n";
    } else {
        echo "Record $no_pranota not found\n\n";
    }
}
