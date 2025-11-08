<?php
/**
 * Simple insert script untuk vendor kontainer sewa
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Insert Vendor Kontainer Sewa ===\n";

try {
    // Insert data vendor
    $vendors = [
        [
            'kode' => 'vendor001',
            'nama_vendor' => 'ZONA', 
            'catatan' => null,
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode' => 'vendor002',
            'nama_vendor' => 'DPE',
            'catatan' => null, 
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode' => 'vendor003',
            'nama_vendor' => 'AYP',
            'catatan' => 'Vendor utama AYP',
            'status' => 'aktif', 
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'kode' => 'vendor004',
            'nama_vendor' => 'SOC',
            'catatan' => 'Shipper Own Container',
            'status' => 'aktif',
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    foreach ($vendors as $vendor) {
        // Check if exists
        $exists = DB::table('vendor_kontainer_sewas')->where('kode', $vendor['kode'])->exists();
        
        if (!$exists) {
            DB::table('vendor_kontainer_sewas')->insert($vendor);
            echo "✅ Inserted: {$vendor['nama_vendor']}\n";
        } else {
            echo "⚠️  Already exists: {$vendor['nama_vendor']}\n";
        }
    }
    
    // Show all vendors
    $allVendors = DB::table('vendor_kontainer_sewas')->orderBy('kode')->get();
    echo "\n=== All Vendors ===\n";
    foreach ($allVendors as $vendor) {
        $statusIcon = $vendor->status === 'aktif' ? '🟢' : '🔴';
        echo "{$statusIcon} {$vendor->kode} - {$vendor->nama_vendor} ({$vendor->status})\n";
    }
    
    echo "\nTotal: " . $allVendors->count() . " vendors\n";
    echo "✅ Success!\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}