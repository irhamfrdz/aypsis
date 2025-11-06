<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;

echo "=== SCRIPT MENGUBAH BTH MENJADI BTM ===\n\n";

// 1. Cek data mobil dengan lokasi BTH
echo "1. CEK DATA MOBIL DENGAN LOKASI BTH:\n";
$mobilsBTH = Mobil::where('lokasi', 'BTH')->get();
echo "   Total mobils dengan lokasi BTH: {$mobilsBTH->count()}\n";

if ($mobilsBTH->count() > 0) {
    echo "   Sample data BTH yang akan diubah:\n";
    foreach ($mobilsBTH->take(5) as $mobil) {
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Lokasi: {$mobil->lokasi}\n";
    }
} else {
    echo "   ℹ️  Tidak ada mobil dengan lokasi BTH\n";
}
echo "\n";

// 2. Cek data mobil dengan lokasi BTM (jika sudah ada)
echo "2. CEK DATA MOBIL DENGAN LOKASI BTM (EXISTING):\n";
$mobilsBTM = Mobil::where('lokasi', 'BTM')->get();
echo "   Total mobils dengan lokasi BTM: {$mobilsBTM->count()}\n";

// 3. Lakukan update BTH -> BTM
if ($mobilsBTH->count() > 0) {
    echo "3. 🔄 MENGUBAH LOKASI BTH MENJADI BTM:\n";
    
    try {
        $updatedCount = Mobil::where('lokasi', 'BTH')->update(['lokasi' => 'BTM']);
        echo "   ✅ Berhasil mengubah {$updatedCount} mobil dari lokasi BTH menjadi BTM\n";
        
        // Verifikasi perubahan
        $newMobilsBTM = Mobil::where('lokasi', 'BTM')->get();
        echo "   📊 Total mobils dengan lokasi BTM sekarang: {$newMobilsBTM->count()}\n";
        
        if ($newMobilsBTM->count() > 0) {
            echo "   Sample data BTM setelah update:\n";
            foreach ($newMobilsBTM->take(5) as $mobil) {
                echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Lokasi: {$mobil->lokasi}\n";
            }
        }
        
    } catch (Exception $e) {
        echo "   ❌ Error saat mengubah data: " . $e->getMessage() . "\n";
    }
} else {
    echo "3. ℹ️  Tidak ada data BTH untuk diubah\n";
}

echo "\n";

// 4. Ringkasan akhir
echo "4. 📋 RINGKASAN AKHIR:\n";
$finalBTH = Mobil::where('lokasi', 'BTH')->count();
$finalBTM = Mobil::where('lokasi', 'BTM')->count();

echo "   - Mobils dengan lokasi BTH: {$finalBTH}\n";
echo "   - Mobils dengan lokasi BTM: {$finalBTM}\n";

if ($finalBTH == 0 && $finalBTM > 0) {
    echo "   ✅ UPDATE SELESAI! Semua data BTH telah diubah menjadi BTM\n";
} elseif ($finalBTM > 0) {
    echo "   ✅ Data BTM tersedia untuk filter\n";
} else {
    echo "   ⚠️  Belum ada data dengan lokasi BTM\n";
}

echo "\n=== SELESAI ===\n";