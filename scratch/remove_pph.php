<?php

use App\Models\PranotaInvoiceVendorSupir;
use Illuminate\Support\Facades\DB;

$nos = ['PRANOTA-VS-20260407-0002', 'PRANOTA-VS-20260407-0001'];

try {
    DB::beginTransaction();
    
    foreach ($nos as $no) {
        $pranota = PranotaInvoiceVendorSupir::where('no_pranota', $no)->first();
        if ($pranota) {
            echo "Processing {$no}...\n";
            echo "Current: total_nominal={$pranota->total_nominal}, pph={$pranota->pph}, grand_total={$pranota->grand_total}\n";
            
            $originalNominal = $pranota->total_nominal + $pranota->pph;
            $pranota->pph = 0;
            $pranota->total_nominal = $originalNominal;
            $pranota->grand_total = $originalNominal + $pranota->total_uang_muat;
            $pranota->save();
            
            echo "New: total_nominal={$pranota->total_nominal}, pph={$pranota->pph}, grand_total={$pranota->grand_total}\n\n";
        } else {
            echo "Pranota {$no} not found.\n";
        }
    }
    
    DB::commit();
    echo "Done!\n";
} catch (\Exception $e) {
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
}
