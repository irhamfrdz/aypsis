<?php
/**
 * Seeder untuk tabel vendor_kontainer_sewas
 * Mengisi data awal vendor kontainer sewa dari dump database
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Foundation\Application;
use Illuminate\Contracts\Http\Kernel;
use App\Models\VendorKontainerSewa;

// Bootstrap Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "=== Seeding Vendor Kontainer Sewa ===\n\n";

// Data vendor dari dump database
$vendors = [
    [
        'kode' => 'vendor001',
        'nama_vendor' => 'ZONA',
        'catatan' => null,
        'status' => 'aktif'
    ],
    [
        'kode' => 'vendor002', 
        'nama_vendor' => 'DPE',
        'catatan' => null,
        'status' => 'aktif'
    ],
    // Tambahkan beberapa vendor lain yang umum digunakan
    [
        'kode' => 'vendor003',
        'nama_vendor' => 'AYP',
        'catatan' => 'Vendor utama AYP',
        'status' => 'aktif'
    ],
    [
        'kode' => 'vendor004',
        'nama_vendor' => 'SOC',
        'catatan' => 'Shipper Own Container',
        'status' => 'aktif'
    ]
];

$created = 0;
$skipped = 0;

foreach ($vendors as $vendorData) {
    // Cek apakah vendor sudah ada berdasarkan kode
    $existingVendor = VendorKontainerSewa::where('kode', $vendorData['kode'])->first();
    
    if ($existingVendor) {
        echo "⚠️  Vendor {$vendorData['kode']} ({$vendorData['nama_vendor']}) sudah ada, dilewati\n";
        $skipped++;
    } else {
        try {
            VendorKontainerSewa::create($vendorData);
            echo "✅ Vendor {$vendorData['kode']} ({$vendorData['nama_vendor']}) berhasil dibuat\n";
            $created++;
        } catch (Exception $e) {
            echo "❌ Error creating vendor {$vendorData['kode']}: {$e->getMessage()}\n";
        }
    }
}

echo "\n=== Hasil Seeding ===\n";
echo "Vendor baru dibuat: {$created}\n";
echo "Vendor dilewati: {$skipped}\n";

// Tampilkan semua vendor yang ada
$allVendors = VendorKontainerSewa::orderBy('kode')->get();
echo "\n=== Daftar Vendor Kontainer Sewa ===\n";
foreach ($allVendors as $vendor) {
    $statusIcon = $vendor->status === 'aktif' ? '🟢' : '🔴';
    echo "{$statusIcon} {$vendor->kode} - {$vendor->nama_vendor} ({$vendor->status})\n";
}

echo "\nTotal vendor: {$allVendors->count()}\n";
echo "\n=== Seeding Selesai ===\n";