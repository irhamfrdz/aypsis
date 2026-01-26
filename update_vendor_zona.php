<?php

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;

// Configuration
$nomorKontainer = 'TCLU5328217';
$targetVendor = 'ZONA';

echo "Starting update for container: $nomorKontainer to vendor: $targetVendor\n";

// 1. Get all records for this container
$items = DaftarTagihanKontainerSewa::where('nomor_kontainer', $nomorKontainer)->get();

if ($items->isEmpty()) {
    echo "No items found for container $nomorKontainer\n";
    exit;
}

echo "Found " . $items->count() . " records.\n";

// 2. Iterate and update
foreach ($items as $item) {
    echo "Processing item ID: {$item->id} (Periode: {$item->periode})...\n";
    
    // Set Vendor
    $item->vendor = $targetVendor;
    
    // Find matching pricelist
    // Logic: Match vendor and size
    $pricelist = MasterPricelistSewaKontainer::where('vendor', $targetVendor)
                    ->where('ukuran_kontainer', $item->size)
                    ->first();
                    
    if ($pricelist) {
        echo "  Found Pricelist: Harga={$pricelist->harga}, Tarif={$pricelist->tarif}\n";
        
        // Update values from Pricelist
        $item->dpp = $pricelist->harga;
        $item->tarif = $pricelist->tarif;
        
        // Recalculate taxes and totals using the Model's logic
        // calculateGrandTotal() calls recalculateTaxes() automatically
        $item->calculateGrandTotal();
        
        // Explicitly recalculate just in case (though calculateGrandTotal does it)
        // $item->recalculateTaxes(); 
        
        $item->save();
        
        echo "  Updated Successfully:\n";
        echo "    - DPP: " . number_format($item->dpp, 2) . "\n";
        echo "    - PPN: " . number_format($item->ppn, 2) . "\n";
        echo "    - PPH: " . number_format($item->pph, 2) . "\n";
        echo "    - Grand Total: " . number_format($item->grand_total, 2) . "\n";
        
    } else {
        echo "  [WARNING] No pricelist found for Vendor '$targetVendor' and Size '{$item->size}'. Skipping price update.\n";
        // We still save the vendor change even if no pricelist found? 
        // Better to probably NOT save if we can't set the correct price, or maybe just set vendor.
        // But user asked to "atur dpp ppn pphnya sesuai dengan table master_pricelist", implying if fails, maybe don't update?
        // Use safe approach: Update vendor anyway, but warn about price.
        $item->save(); 
    }
    echo "---------------------------------------------------\n";
}

echo "All operations completed.\n";
