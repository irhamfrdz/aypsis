<?php

use App\Models\PranotaInvoiceVendorSupir;

$pranotas = PranotaInvoiceVendorSupir::where('grand_total', 0)->get();
foreach($pranotas as $p) {
    if ($p->total_nominal > 0) {
        $pph = $p->total_nominal * 0.02;
        $grandTotal = $p->total_nominal - $pph;
        $p->update([
            'pph' => $pph,
            'grand_total' => $grandTotal
        ]);
        echo "Updated {$p->no_pranota} | PPh: {$pph} | Grand Total: {$grandTotal}\n";
    }
}
echo "Selesai memperbaiki " . $pranotas->count() . " data Pranota.\n";
