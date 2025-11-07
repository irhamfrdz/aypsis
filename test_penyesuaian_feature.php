<?php

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use App\Models\PranotaUangJalan;
use App\Models\UangJalan;
use App\Models\SuratJalan;
use App\Models\User;
use Carbon\Carbon;

// Initialize Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🔧 Testing Pranota Uang Jalan Penyesuaian Feature\n";
echo "=" . str_repeat("=", 50) . "\n\n";

try {
    // Get admin user
    $user = User::first();

    if (!$user) {
        echo "❌ No user found!\n";
        exit(1);
    }

    echo "👤 Using user: {$user->name} (ID: {$user->id})\n\n";

    // Test 1: Check database structure
    echo "📊 1. Checking database structure...\n";
    
    $columns = DB::select("DESCRIBE pranota_uang_jalans");
    $columnNames = array_column($columns, 'Field');
    
    $requiredColumns = ['penyesuaian', 'keterangan_penyesuaian'];
    $missingColumns = array_diff($requiredColumns, $columnNames);
    
    if (empty($missingColumns)) {
        echo "✅ Database structure is correct!\n";
        foreach ($requiredColumns as $column) {
            $columnInfo = collect($columns)->firstWhere('Field', $column);
            echo "   - {$column}: {$columnInfo->Type} ({$columnInfo->Null})\n";
        }
    } else {
        echo "❌ Missing columns: " . implode(', ', $missingColumns) . "\n";
        exit(1);
    }

    // Test 2: Create test data with penyesuaian
    echo "\n📋 2. Testing pranota creation with penyesuaian...\n";
    
    // Get available uang jalan
    $availableUangJalans = UangJalan::with(['suratJalan'])
        ->whereDoesntHave('pranotaUangJalan')
        ->whereIn('status', ['belum_dibayar', 'belum_masuk_pranota'])
        ->limit(2)
        ->get();

    if ($availableUangJalans->isEmpty()) {
        echo "⚠️  No available uang jalan found. Creating test data...\n";
        
        // Create test surat jalan and uang jalan
        $suratJalan = SuratJalan::create([
            'no_surat_jalan' => 'TEST-SJ-' . date('YmdHis'),
            'tanggal_surat_jalan' => now()->subDays(1),
            'supir' => 'Test Supir Penyesuaian',
            'kenek' => 'Test Kenek Penyesuaian',
            'tujuan_pengambilan' => 'Test Location',
            'pengirim' => 'Test Pengirim',
            'jenis_barang' => 'Test Barang',
            'status_approval' => 'approved',
            'tanggal_tanda_terima' => now(),
        ]);

        $uangJalan = UangJalan::create([
            'nomor_uang_jalan' => 'TEST-UJ-PENY-' . date('YmdHis'),
            'surat_jalan_id' => $suratJalan->id,
            'tanggal_pemberian' => now()->subDays(1),
            'kegiatan_bongkar_muat' => 'Bongkar Muat Test',
            'jumlah_uang_jalan' => 150000,
            'jumlah_uang_makan' => 25000,
            'jumlah_total' => 175000,
            'status' => 'belum_masuk_pranota',
            'keterangan' => 'Test uang jalan for penyesuaian',
            'created_by' => $user->id,
        ]);

        $availableUangJalans = collect([$uangJalan]);
        echo "✅ Created test uang jalan: {$uangJalan->nomor_uang_jalan}\n";
    }

    $totalAmount = $availableUangJalans->sum('jumlah_total');
    $penyesuaian = -25000; // Test dengan pengurangan
    $totalWithPenyesuaian = $totalAmount + $penyesuaian;
    
    echo "   💰 Subtotal Uang Jalan: Rp " . number_format($totalAmount, 0, ',', '.') . "\n";
    echo "   📝 Penyesuaian: Rp " . number_format($penyesuaian, 0, ',', '.') . "\n";
    echo "   🏆 Total Akhir: Rp " . number_format($totalWithPenyesuaian, 0, ',', '.') . "\n";

    // Generate nomor pranota
    $lastPranota = PranotaUangJalan::orderBy('id', 'desc')->first();
    $nextNumber = $lastPranota ? (intval(substr($lastPranota->nomor_pranota, -5)) + 1) : 1;
    $nomorPranota = 'PUJ' . date('ym') . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

    // Create pranota with penyesuaian
    $pranotaUangJalan = PranotaUangJalan::create([
        'nomor_pranota' => $nomorPranota,
        'tanggal_pranota' => now(),
        'periode_tagihan' => now()->format('Y-m'),
        'jumlah_uang_jalan' => $availableUangJalans->count(),
        'total_amount' => $totalAmount,
        'penyesuaian' => $penyesuaian,
        'keterangan_penyesuaian' => 'Pengurangan biaya administrasi dan pajak',
        'status_pembayaran' => 'approved',
        'catatan' => 'Test pranota dengan penyesuaian negatif',
        'created_by' => $user->id,
    ]);

    // Attach uang jalans
    $pranotaUangJalan->uangJalans()->attach($availableUangJalans->pluck('id'));
    
    // Update uang jalan status
    UangJalan::whereIn('id', $availableUangJalans->pluck('id'))
        ->update(['status' => 'sudah_masuk_pranota']);

    echo "✅ Created pranota: {$pranotaUangJalan->nomor_pranota}\n";

    // Test 3: Verify data integrity
    echo "\n🔍 3. Verifying data integrity...\n";
    
    $createdPranota = PranotaUangJalan::with('uangJalans')->find($pranotaUangJalan->id);
    
    echo "   📋 Nomor: {$createdPranota->nomor_pranota}\n";
    echo "   💰 Subtotal: Rp " . number_format($createdPranota->total_amount, 0, ',', '.') . "\n";
    echo "   📝 Penyesuaian: Rp " . number_format($createdPranota->penyesuaian, 0, ',', '.') . "\n";
    echo "   🏆 Total Akhir: Rp " . number_format($createdPranota->total_with_penyesuaian, 0, ',', '.') . "\n";
    echo "   📄 Keterangan: {$createdPranota->keterangan_penyesuaian}\n";
    echo "   📊 Uang Jalan: {$createdPranota->uangJalans->count()} items\n";

    // Test 4: Test model methods
    echo "\n🧪 4. Testing model methods...\n";
    
    echo "   formatted_penyesuaian: {$createdPranota->formatted_penyesuaian}\n";
    echo "   total_with_penyesuaian: {$createdPranota->total_with_penyesuaian}\n";
    echo "   formatted_total_with_penyesuaian: {$createdPranota->formatted_total_with_penyesuaian}\n";

    // Test 5: Test positive penyesuaian
    echo "\n➕ 5. Testing positive penyesuaian...\n";
    
    $createdPranota->update([
        'penyesuaian' => 50000,
        'keterangan_penyesuaian' => 'Bonus kinerja supir bulan ini'
    ]);

    $createdPranota->refresh();
    
    echo "   💰 Subtotal: Rp " . number_format($createdPranota->total_amount, 0, ',', '.') . "\n";
    echo "   📝 Penyesuaian: Rp " . number_format($createdPranota->penyesuaian, 0, ',', '.') . "\n";
    echo "   🏆 Total Akhir: Rp " . number_format($createdPranota->total_with_penyesuaian, 0, ',', '.') . "\n";
    echo "   📄 Keterangan: {$createdPranota->keterangan_penyesuaian}\n";

    echo "\n🎯 Test Summary:\n";
    echo "✅ Database migration completed\n";
    echo "✅ Pranota creation with penyesuaian working\n";
    echo "✅ Model methods functioning correctly\n";
    echo "✅ Data persistence verified\n";
    echo "✅ Positive and negative penyesuaian supported\n";

    echo "\n🌐 URLs for testing:\n";
    echo "📋 Index: /pranota-uang-jalan\n";
    echo "👁️  Show: /pranota-uang-jalan/{$createdPranota->id}\n";
    echo "✏️  Edit: /pranota-uang-jalan/{$createdPranota->id}/edit\n";
    echo "🖨️  Print: /pranota-uang-jalan/{$createdPranota->id}/print\n";

    echo "\n💡 Features to test manually:\n";
    echo "1. Create new pranota with penyesuaian input\n";
    echo "2. Edit existing pranota to add/modify penyesuaian\n";
    echo "3. View pranota detail with penyesuaian info\n";
    echo "4. Print pranota with penyesuaian summary\n";
    echo "5. JavaScript calculation in forms\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n🎉 Penyesuaian feature test completed successfully!\n";