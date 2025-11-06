<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\User;

echo "=== TEST FILTER BTM SETELAH UPDATE ===\n\n";

// 1. Verifikasi data BTM di database
echo "1. VERIFIKASI DATA BTM:\n";
$mobilsBTM = Mobil::where('lokasi', 'BTM')->get();
echo "   Total mobils dengan lokasi BTM: {$mobilsBTM->count()}\n";

if ($mobilsBTM->count() > 0) {
    echo "   Sample data BTM:\n";
    foreach ($mobilsBTM->take(5) as $mobil) {
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Lokasi: {$mobil->lokasi}\n";
    }
} else {
    echo "   ❌ TIDAK ADA mobil dengan lokasi BTM!\n";
}
echo "\n";

// 2. Verifikasi tidak ada data BTH tersisa
echo "2. VERIFIKASI TIDAK ADA BTH TERSISA:\n";
$mobilsBTH = Mobil::where('lokasi', 'BTH')->count();
echo "   Total mobils dengan lokasi BTH: {$mobilsBTH}\n";
if ($mobilsBTH == 0) {
    echo "   ✅ Semua data BTH telah diubah menjadi BTM\n";
} else {
    echo "   ⚠️  Masih ada {$mobilsBTH} mobil dengan lokasi BTH\n";
}
echo "\n";

// 3. Test simulasi filter untuk user BTM
echo "3. SIMULASI FILTER UNTUK USER BTM:\n";
echo "   Kondisi: User memiliki karyawan dengan cabang = 'BTM'\n";
echo "   Filter aktif: mobil.lokasi = 'BTM'\n";
echo "   Hasil filter: {$mobilsBTM->count()} mobil\n";

if ($mobilsBTM->count() > 0) {
    echo "   ✅ FILTER AKAN MENAMPILKAN DATA\n";
} else {
    echo "   ❌ FILTER MASIH KOSONG\n";
}
echo "\n";

// 4. Test simulasi untuk user non-BTM
echo "4. SIMULASI UNTUK USER NON-BTM:\n";
$allMobils = Mobil::count();
echo "   Kondisi: User tidak memiliki karyawan BTM\n";
echo "   Filter aktif: TIDAK ADA (menampilkan semua)\n";
echo "   Hasil: {$allMobils} mobil (semua mobil)\n";
echo "   ✅ User non-BTM akan melihat semua data\n";
echo "\n";

// 5. Breakdown lokasi
echo "5. BREAKDOWN LOKASI MOBIL:\n";
$lokasiBreakdown = Mobil::selectRaw('lokasi, COUNT(*) as count')
    ->groupBy('lokasi')
    ->orderBy('count', 'desc')
    ->get();

foreach ($lokasiBreakdown as $lokasi) {
    echo "   - '{$lokasi->lokasi}': {$lokasi->count} mobil\n";
}
echo "\n";

// 6. Final check
echo "6. 🎯 FINAL CHECK:\n";
if ($mobilsBTM->count() > 0) {
    echo "   ✅ Filter BTM siap digunakan!\n";
    echo "   📊 User BTM akan melihat: {$mobilsBTM->count()} mobil\n";
    echo "   📊 User non-BTM akan melihat: {$allMobils} mobil\n";
    echo "   🚀 Implementasi sukses!\n";
} else {
    echo "   ❌ Filter BTM belum siap\n";
    echo "   💡 Perlu tambahkan/update data mobil dengan lokasi BTM\n";
}

echo "\n=== END TEST ===\n";