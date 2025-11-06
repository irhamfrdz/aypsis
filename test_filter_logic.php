<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\User;

echo "=== TEST FILTER LOGIC ===\n\n";

// 1. Test tanpa filter (seperti user non-BTM)
echo "1. Test tanpa filter (user non-BTM):\n";
$allMobils = Mobil::with('karyawan')->get();
echo "   Total mobils tanpa filter: {$allMobils->count()}\n";
echo "   Sample data:\n";
foreach ($allMobils->take(3) as $mobil) {
    $karyawanInfo = $mobil->karyawan ? 
        "{$mobil->karyawan->nama_lengkap} (Cabang: {$mobil->karyawan->cabang})" : 
        "Tidak ada karyawan";
    echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Karyawan: {$karyawanInfo}\n";
}
echo "\n";

// 2. Test dengan filter BTM (seperti implementasi saat ini)
echo "2. Test dengan filter BTM (implementasi saat ini):\n";
$btmMobils = Mobil::with('karyawan')
    ->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    })->get();
echo "   Total mobils dengan filter BTM: {$btmMobils->count()}\n";
if ($btmMobils->count() == 0) {
    echo "   ❌ KOSONG - Tidak ada mobil dengan karyawan cabang BTM\n";
}
echo "\n";

// 3. Test dengan filter JKT (untuk simulasi)
echo "3. Test dengan filter JKT (untuk simulasi):\n";
$jktMobils = Mobil::with('karyawan')
    ->whereHas('karyawan', function($q) {
        $q->where('cabang', 'JKT');
    })->get();
echo "   Total mobils dengan filter JKT: {$jktMobils->count()}\n";
if ($jktMobils->count() > 0) {
    echo "   Sample data JKT:\n";
    foreach ($jktMobils->take(3) as $mobil) {
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Karyawan: {$mobil->karyawan->nama_lengkap}\n";
    }
}
echo "\n";

// 4. Analisis masalah
echo "4. ANALISIS MASALAH:\n";
$mobilsWithKaryawan = Mobil::whereNotNull('karyawan_id')->count();
$mobilsWithoutKaryawan = Mobil::whereNull('karyawan_id')->count();
$karyawanBTM = Karyawan::where('cabang', 'BTM')->count();
$karyawanJKT = Karyawan::where('cabang', 'JKT')->count();

echo "   - Total mobils dengan karyawan: {$mobilsWithKaryawan}\n";
echo "   - Total mobils tanpa karyawan: {$mobilsWithoutKaryawan}\n";
echo "   - Total karyawan BTM: {$karyawanBTM}\n";
echo "   - Total karyawan JKT: {$karyawanJKT}\n";
echo "\n";

if ($karyawanBTM == 0) {
    echo "   ❌ MASALAH: Tidak ada karyawan dengan cabang 'BTM' di database!\n";
    echo "   📋 SOLUSI:\n";
    echo "   1. Tambahkan karyawan dengan cabang 'BTM'\n";
    echo "   2. Atau ubah cabang existing karyawan menjadi 'BTM'\n";
    echo "   3. Atau ganti filter dari 'BTM' ke cabang yang ada (contoh: 'JKT')\n";
}

// 5. Cek user yang sedang login (jika ada)
echo "\n5. CEK USER LOGIN:\n";
$currentUser = auth()->user();
if ($currentUser && $currentUser->karyawan) {
    echo "   Current user: {$currentUser->name}\n";
    echo "   Karyawan: {$currentUser->karyawan->nama_lengkap}\n";
    echo "   Cabang: '{$currentUser->karyawan->cabang}'\n";
    
    if ($currentUser->karyawan->cabang === 'BTM') {
        echo "   ✅ User ini adalah BTM - filter akan aktif\n";
    } else {
        echo "   ℹ️  User ini bukan BTM - filter tidak aktif\n";
    }
} else {
    echo "   ⚠️  Tidak ada user yang login atau user tidak memiliki karyawan\n";
}

echo "\n=== END TEST ===\n";