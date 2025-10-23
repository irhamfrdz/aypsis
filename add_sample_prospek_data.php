<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

// Initialize Laravel application
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "📦 Adding Sample Prospek Data...\n";
echo "===============================\n\n";

try {
    // Sample data for prospek
    $sampleData = [
        [
            'tanggal' => Carbon::now()->subDays(5)->format('Y-m-d'),
            'nama_supir' => 'Budi Santoso',
            'barang' => 'Tekstil',
            'pt_pengirim' => 'PT Sinar Jaya',
            'ukuran' => '20',
            'nomor_kontainer' => 'MSKU7858456',
            'no_seal' => 'SL123456',
            'tujuan_pengiriman' => 'Jakarta',
            'nama_kapal' => 'KM Sinar Indah',
            'keterangan' => 'Kontainer berisi tekstil untuk ekspor',
            'status' => 'aktif',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'tanggal' => Carbon::now()->subDays(3)->format('Y-m-d'),
            'nama_supir' => 'Ahmad Fauzi',
            'barang' => 'Elektronik',
            'pt_pengirim' => 'PT Teknologi Maju',
            'ukuran' => '40',
            'nomor_kontainer' => 'TEMU8745621',
            'no_seal' => 'SL789012',
            'tujuan_pengiriman' => 'Surabaya',
            'nama_kapal' => 'KM Nusantara',
            'keterangan' => 'Produk elektronik untuk distribusi nasional',
            'status' => 'aktif',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'tanggal' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'nama_supir' => 'Slamet Wijaya',
            'barang' => 'Makanan Kemasan',
            'pt_pengirim' => 'PT Food Industries',
            'ukuran' => '20',
            'nomor_kontainer' => 'HJMU4567890',
            'no_seal' => 'SL345678',
            'tujuan_pengiriman' => 'Medan',
            'nama_kapal' => 'KM Bahari',
            'keterangan' => null,
            'status' => 'sudah_muat',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'tanggal' => Carbon::now()->subDay()->format('Y-m-d'),
            'nama_supir' => 'Rizki Pratama',
            'barang' => 'Furniture',
            'pt_pengirim' => 'PT Kayu Lestari',
            'ukuran' => '40',
            'nomor_kontainer' => 'CAIU9876543',
            'no_seal' => 'SL901234',
            'tujuan_pengiriman' => 'Makassar',
            'nama_kapal' => 'KM Pelni Jaya',
            'keterangan' => 'Furniture kayu berkualitas tinggi',
            'status' => 'aktif',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'tanggal' => Carbon::now()->format('Y-m-d'),
            'nama_supir' => 'Doni Setiawan',
            'barang' => 'Spare Parts',
            'pt_pengirim' => 'PT Otomotif Prima',
            'ukuran' => '20',
            'nomor_kontainer' => 'GESU1234567',
            'no_seal' => 'SL567890',
            'tujuan_pengiriman' => 'Balikpapan',
            'nama_kapal' => 'KM Dharma Lautan',
            'keterangan' => 'Suku cadang kendaraan bermotor',
            'status' => 'batal',
            'created_by' => 1,
            'updated_by' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    echo "💾 Inserting sample data...\n\n";

    foreach ($sampleData as $index => $data) {
        // Check if similar data already exists
        $exists = DB::table('prospek')
            ->where('nomor_kontainer', $data['nomor_kontainer'])
            ->exists();

        if (!$exists) {
            DB::table('prospek')->insert($data);
            echo "✅ Added prospek #" . ($index + 1) . ": {$data['nomor_kontainer']} - {$data['nama_supir']}\n";
        } else {
            echo "ℹ️  Prospek #" . ($index + 1) . " already exists: {$data['nomor_kontainer']}\n";
        }
    }

    echo "\n📊 Current Prospek Statistics:\n\n";

    $totalProspek = DB::table('prospek')->count();
    $aktifCount = DB::table('prospek')->where('status', 'aktif')->count();
    $sudahMuatCount = DB::table('prospek')->where('status', 'sudah_muat')->count();
    $batalCount = DB::table('prospek')->where('status', 'batal')->count();

    echo "📦 Total Prospek: {$totalProspek}\n";
    echo "🟢 Status Aktif: {$aktifCount}\n";
    echo "🔵 Status Sudah Muat: {$sudahMuatCount}\n";
    echo "🔴 Status Batal: {$batalCount}\n";

    echo "\n✨ SAMPLE DATA SETUP COMPLETED! ✨\n";
    echo "==================================\n\n";

    echo "🎮 Ready to Test:\n";
    echo "1. Visit /prospek to see the data\n";
    echo "2. Test search and filter functionality\n";
    echo "3. Click on 'eye' icon to view details\n\n";

    echo "📱 Features Available:\n";
    echo "• Search by nama supir, barang, pengirim, kontainer, seal, tujuan, kapal\n";
    echo "• Filter by status (aktif, sudah_muat, batal)\n";
    echo "• Filter by ukuran (20 feet, 40 feet)\n";
    echo "• Filter by tujuan pengiriman\n";
    echo "• Pagination for large datasets\n\n";

    echo "✅ Prospek system is ready to use!\n";

} catch (\Exception $e) {
    echo "❌ Error adding sample data: " . $e->getMessage() . "\n";
    echo "📍 Error occurred at line: " . $e->getLine() . "\n";
    echo "📄 In file: " . $e->getFile() . "\n";
    exit(1);
}
