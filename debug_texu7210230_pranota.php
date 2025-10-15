<?php
require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== DEBUGGING TEXU7210230 PRANOTA ENTRY ISSUE ===\n\n";

try {
    // Check the specific container
    echo "1. Checking TEXU7210230 data:\n";
    $tagihan = DB::table('daftar_tagihan_kontainer_sewa')
        ->where('nomor_kontainer', 'TEXU7210230')
        ->where('periode', '13')
        ->first();
    
    if ($tagihan) {
        echo "   ✓ Container found!\n";
        echo "   - ID: {$tagihan->id}\n";
        echo "   - Container: {$tagihan->nomor_kontainer}\n";
        echo "   - Periode: {$tagihan->periode}\n";
        echo "   - Vendor: {$tagihan->vendor}\n";
        echo "   - Group: " . ($tagihan->group ?? 'NULL') . "\n";
        echo "   - Invoice Vendor: '" . ($tagihan->invoice_vendor ?? 'NULL') . "'\n";
        echo "   - Tanggal Vendor: '" . ($tagihan->tanggal_vendor ?? 'NULL') . "'\n";
        echo "   - Status Pranota: '" . ($tagihan->status_pranota ?? 'NULL') . "'\n";
        echo "   - Pranota ID: " . ($tagihan->pranota_id ?? 'NULL') . "\n";
        
        // Check for empty or whitespace-only values
        echo "\n2. Detailed field validation:\n";
        
        $invoiceVendor = trim($tagihan->invoice_vendor ?? '');
        $tanggalVendor = trim($tagihan->tanggal_vendor ?? '');
        
        echo "   - Invoice Vendor length: " . strlen($invoiceVendor) . "\n";
        echo "   - Invoice Vendor is empty: " . (empty($invoiceVendor) ? 'YES' : 'NO') . "\n";
        echo "   - Invoice Vendor content: '{$invoiceVendor}'\n";
        
        echo "   - Tanggal Vendor length: " . strlen($tanggalVendor) . "\n";
        echo "   - Tanggal Vendor is empty: " . (empty($tanggalVendor) ? 'YES' : 'NO') . "\n";
        echo "   - Tanggal Vendor content: '{$tanggalVendor}'\n";
        
        // Check validation logic
        echo "\n3. Validation results:\n";
        if (empty($invoiceVendor) || $invoiceVendor === '-' || $invoiceVendor === 'NULL') {
            echo "   ❌ Invoice Vendor validation FAILED\n";
        } else {
            echo "   ✓ Invoice Vendor validation PASSED\n";
        }
        
        if (empty($tanggalVendor) || $tanggalVendor === '-' || $tanggalVendor === 'NULL') {
            echo "   ❌ Tanggal Vendor validation FAILED\n";
        } else {
            echo "   ✓ Tanggal Vendor validation PASSED\n";
        }
        
        // Check the raw database values
        echo "\n4. Raw database values (for debugging):\n";
        $rawData = DB::select("SELECT id, nomor_kontainer, periode, invoice_vendor, tanggal_vendor, 
                                     CHAR_LENGTH(invoice_vendor) as invoice_vendor_length,
                                     CHAR_LENGTH(tanggal_vendor) as tanggal_vendor_length,
                                     invoice_vendor IS NULL as invoice_vendor_is_null,
                                     tanggal_vendor IS NULL as tanggal_vendor_is_null
                              FROM daftar_tagihan_kontainer_sewa 
                              WHERE nomor_kontainer = 'TEXU7210230' AND periode = 13");
        
        foreach ($rawData as $row) {
            echo "   - Invoice Vendor: '{$row->invoice_vendor}' (length: {$row->invoice_vendor_length}, null: " . ($row->invoice_vendor_is_null ? 'YES' : 'NO') . ")\n";
            echo "   - Tanggal Vendor: '{$row->tanggal_vendor}' (length: {$row->tanggal_vendor_length}, null: " . ($row->tanggal_vendor_is_null ? 'YES' : 'NO') . ")\n";
        }
        
    } else {
        echo "   ❌ Container not found!\n";
    }
    
    // Check if there are similar containers with valid data
    echo "\n5. Checking other containers with valid vendor data:\n";
    $validContainers = DB::table('daftar_tagihan_kontainer_sewa')
        ->whereNotNull('invoice_vendor')
        ->where('invoice_vendor', '!=', '')
        ->where('invoice_vendor', '!=', '-')
        ->whereNotNull('tanggal_vendor')
        ->where('tanggal_vendor', '!=', '')
        ->where('tanggal_vendor', '!=', '-')
        ->take(3)
        ->get(['nomor_kontainer', 'periode', 'invoice_vendor', 'tanggal_vendor']);
        
    foreach ($validContainers as $container) {
        echo "   - {$container->nomor_kontainer} (P{$container->periode}): '{$container->invoice_vendor}' | '{$container->tanggal_vendor}'\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== DEBUGGING COMPLETED ===\n";