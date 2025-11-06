<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\User;

echo "=== TEST FILTER BERDASARKAN LOKASI ===\n\n";

// 1. Cek semua lokasi yang ada di database
echo "1. LOKASI MOBIL YANG ADA:\n";
$lokasiCount = Mobil::selectRaw('lokasi, COUNT(*) as count')
    ->groupBy('lokasi')
    ->orderBy('count', 'desc')
    ->get();

foreach ($lokasiCount as $lokasi) {
    echo "   - '{$lokasi->lokasi}': {$lokasi->count} mobil\n";
}
echo "\n";

// 2. Test filter berdasarkan lokasi BTH (Batam)
echo "2. TEST FILTER LOKASI BTH (BATAM):\n";
$mobilsBTH = Mobil::where('lokasi', 'BTH')->with('karyawan')->get();
echo "   Total mobils dengan lokasi BTH: {$mobilsBTH->count()}\n";

if ($mobilsBTH->count() > 0) {
    echo "   Sample data BTH:\n";
    foreach ($mobilsBTH->take(5) as $mobil) {
        $karyawanInfo = $mobil->karyawan ? 
            "Karyawan: {$mobil->karyawan->nama_lengkap}" : 
            "Tidak ada karyawan";
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | {$karyawanInfo}\n";
    }
} else {
    echo "   ❌ TIDAK ADA mobil dengan lokasi BTH!\n";
}
echo "\n";

// 3. Test filter berdasarkan lokasi JKT (Jakarta) untuk perbandingan
echo "3. TEST FILTER LOKASI JKT (JAKARTA):\n";
$mobilsJKT = Mobil::where('lokasi', 'JKT')->with('karyawan')->get();
echo "   Total mobils dengan lokasi JKT: {$mobilsJKT->count()}\n";

if ($mobilsJKT->count() > 0) {
    echo "   Sample data JKT:\n";
    foreach ($mobilsJKT->take(3) as $mobil) {
        $karyawanInfo = $mobil->karyawan ? 
            "Karyawan: {$mobil->karyawan->nama_lengkap}" : 
            "Tidak ada karyawan";
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | {$karyawanInfo}\n";
    }
}
echo "\n";

// 4. Cek karyawan BTM untuk memastikan ada yang bisa login
echo "4. CEK KARYAWAN CABANG BTM:\n";
$karyawanBTM = Karyawan::where('cabang', 'BTM')->get();
echo "   Total karyawan BTM: {$karyawanBTM->count()}\n";

if ($karyawanBTM->count() > 0) {
    echo "   Sample karyawan BTM:\n";
    foreach ($karyawanBTM->take(3) as $karyawan) {
        echo "   - {$karyawan->nama_lengkap} (NIK: {$karyawan->nik})\n";
    }
} else {
    echo "   ❌ TIDAK ADA karyawan dengan cabang BTM!\n";
}
echo "\n";

// 5. Cek user yang terkait dengan karyawan BTM
echo "5. CEK USER DENGAN KARYAWAN BTM:\n";
$usersBTM = User::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
})->with('karyawan')->get();

echo "   Total users dengan karyawan BTM: {$usersBTM->count()}\n";
if ($usersBTM->count() > 0) {
    foreach ($usersBTM->take(3) as $user) {
        echo "   - Email: {$user->email}, Karyawan: {$user->karyawan->nama_lengkap}\n";
    }
} else {
    echo "   ❌ TIDAK ADA user dengan karyawan BTM!\n";
}
echo "\n";

// 6. Simulasi filter yang baru (berdasarkan lokasi)
echo "6. 🆕 SIMULASI FILTER BARU (BERDASARKAN LOKASI):\n";
echo "   Jika user BTM login, mereka akan melihat:\n";
echo "   - Filter aktif: lokasi = 'BTH'\n";
echo "   - Jumlah mobil yang akan ditampilkan: {$mobilsBTH->count()}\n";

if ($mobilsBTH->count() > 0) {
    echo "   ✅ FILTER AKAN BEKERJA dengan menampilkan mobil lokasi BTH\n";
} else {
    echo "   ❌ FILTER MASIH KOSONG - perlu cek apakah ada mobil dengan lokasi BTH\n";
}

echo "\n";

// 7. Bandingkan dengan filter lama
echo "7. 📊 PERBANDINGAN FILTER:\n";
echo "   Filter LAMA (berdasarkan karyawan cabang BTM): 0 mobil\n";
echo "   Filter BARU (berdasarkan lokasi BTH): {$mobilsBTH->count()} mobil\n";

if ($mobilsBTH->count() > 0) {
    echo "   ✅ Filter baru akan menampilkan data!\n";
} else {
    echo "   ⚠️  Perlu pastikan ada mobil dengan lokasi 'BTH' di database\n";
}

echo "\n=== END TEST ===\n";