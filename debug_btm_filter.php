<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\User;

echo "=== DEBUG BTM FILTER ISSUE ===\n\n";

// 1. Cek karyawan BTM yang ada
echo "1. KARYAWAN CABANG BTM:\n";
$karyawanBTM = Karyawan::where('cabang', 'BTM')->get();
echo "   Total karyawan BTM: {$karyawanBTM->count()}\n";

if ($karyawanBTM->count() > 0) {
    echo "   Daftar karyawan BTM:\n";
    foreach ($karyawanBTM->take(5) as $karyawan) {
        echo "   - ID: {$karyawan->id}, Nama: {$karyawan->nama_lengkap}, NIK: {$karyawan->nik}\n";
    }
} else {
    echo "   ❌ TIDAK ADA karyawan dengan cabang BTM!\n";
}
echo "\n";

// 2. Cek user yang terkait dengan karyawan BTM
echo "2. USER DENGAN KARYAWAN BTM:\n";
$usersBTM = User::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
})->with('karyawan')->get();

echo "   Total users dengan karyawan BTM: {$usersBTM->count()}\n";
if ($usersBTM->count() > 0) {
    foreach ($usersBTM->take(3) as $user) {
        echo "   - User: {$user->email}, Karyawan: {$user->karyawan->nama_lengkap}\n";
    }
} else {
    echo "   ❌ TIDAK ADA user dengan karyawan BTM!\n";
}
echo "\n";

// 3. Cek mobil yang terkait dengan karyawan BTM
echo "3. MOBIL YANG TERKAIT DENGAN KARYAWAN BTM:\n";
$mobilsBTM = Mobil::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
})->with('karyawan')->get();

echo "   Total mobils dengan karyawan BTM: {$mobilsBTM->count()}\n";
if ($mobilsBTM->count() > 0) {
    foreach ($mobilsBTM->take(5) as $mobil) {
        echo "   - {$mobil->kode_no} | {$mobil->nomor_polisi} | Karyawan: {$mobil->karyawan->nama_lengkap}\n";
    }
} else {
    echo "   ❌ TIDAK ADA mobil dengan karyawan BTM!\n";
    
    // Cek apakah ada karyawan BTM yang terkait dengan mobil
    if ($karyawanBTM->count() > 0) {
        echo "   📋 Meskipun ada karyawan BTM, tidak ada mobil yang terkait dengan mereka\n";
        
        // Cek beberapa mobil dan karyawan_id mereka
        echo "\n   🔍 DETAIL ASOSIASI MOBIL-KARYAWAN:\n";
        $sampleMobils = Mobil::with('karyawan')->take(10)->get();
        foreach ($sampleMobils as $mobil) {
            $karyawanInfo = $mobil->karyawan ? 
                "Karyawan ID: {$mobil->karyawan->id}, Nama: {$mobil->karyawan->nama_lengkap}, Cabang: {$mobil->karyawan->cabang}" : 
                "Tidak ada karyawan (karyawan_id: " . ($mobil->karyawan_id ?? 'NULL') . ")";
            echo "   - Mobil {$mobil->kode_no}: {$karyawanInfo}\n";
        }
    }
}
echo "\n";

// 4. Test query filter BTM step by step
echo "4. TEST QUERY FILTER STEP BY STEP:\n";

// Step 1: Semua mobil
$allMobils = Mobil::count();
echo "   - Total semua mobils: {$allMobils}\n";

// Step 2: Mobil dengan karyawan
$mobilsWithKaryawan = Mobil::whereNotNull('karyawan_id')->count();
echo "   - Mobils dengan karyawan_id: {$mobilsWithKaryawan}\n";

// Step 3: Mobil dengan karyawan yang ada
$mobilsWithValidKaryawan = Mobil::whereHas('karyawan')->count();
echo "   - Mobils dengan karyawan valid: {$mobilsWithValidKaryawan}\n";

// Step 4: Test query BTM
$mobilsBTMQuery = Mobil::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
});
echo "   - Mobils dengan filter BTM: {$mobilsBTMQuery->count()}\n";

// Step 5: Debug SQL query
$sql = $mobilsBTMQuery->toSql();
$bindings = $mobilsBTMQuery->getBindings();
echo "   - SQL Query: {$sql}\n";
echo "   - Bindings: " . json_encode($bindings) . "\n";

echo "\n";

// 5. Solusi berdasarkan temuan
echo "5. 🔧 ANALISIS DAN SOLUSI:\n";

if ($karyawanBTM->count() == 0) {
    echo "   ❌ MASALAH UTAMA: Tidak ada karyawan dengan cabang 'BTM'\n";
    echo "   💡 SOLUSI: Import/update data karyawan dengan cabang BTM\n";
} elseif ($mobilsBTM->count() == 0) {
    echo "   ❌ MASALAH: Ada karyawan BTM tapi tidak ada mobil yang terkait\n";
    echo "   💡 SOLUSI: Assign mobil ke karyawan BTM atau update karyawan_id pada mobil\n";
} else {
    echo "   ✅ Data BTM ada, kemungkinan masalah di implementasi filter\n";
}

echo "\n=== END DEBUG ===\n";