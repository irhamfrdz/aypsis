<?php

require_once 'vendor/autoload.php';

// Load Laravel environment
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Mobil;
use App\Models\Karyawan;
use App\Models\User;

echo "=== DEBUG MOBILS DATABASE ===\n\n";

// 1. Cek total data mobil
$totalMobils = Mobil::count();
echo "1. Total mobils di database: {$totalMobils}\n\n";

// 2. Cek data mobil dengan relasi karyawan
echo "2. Sample data mobils dengan karyawan:\n";
$mobilsWithKaryawan = Mobil::with('karyawan')->take(5)->get();
foreach ($mobilsWithKaryawan as $mobil) {
    echo "   - Mobil ID: {$mobil->id}, Kode: {$mobil->kode_no}, Nomor Polisi: {$mobil->nomor_polisi}\n";
    if ($mobil->karyawan) {
        echo "     Karyawan: {$mobil->karyawan->nama_lengkap}, Cabang: {$mobil->karyawan->cabang}\n";
    } else {
        echo "     Karyawan: NULL (tidak ada relasi)\n";
    }
    echo "\n";
}

// 3. Cek struktur tabel mobils
echo "3. Struktur kolom tabel mobils:\n";
$mobilsTable = \DB::select("DESCRIBE mobils");
foreach ($mobilsTable as $column) {
    echo "   - {$column->Field} ({$column->Type})\n";
}
echo "\n";

// 4. Cek relasi foreign key ke karyawans
echo "4. Mobils yang memiliki karyawan_id:\n";
$mobilsWithKaryawanId = Mobil::whereNotNull('karyawan_id')->count();
echo "   - Mobils dengan karyawan_id: {$mobilsWithKaryawanId}\n";

$mobilsWithoutKaryawanId = Mobil::whereNull('karyawan_id')->count();
echo "   - Mobils tanpa karyawan_id: {$mobilsWithoutKaryawanId}\n\n";

// 5. Cek data karyawan dengan cabang BTM
echo "5. Data karyawan cabang BTM:\n";
$karyawanBTM = Karyawan::where('cabang', 'BTM')->get();
echo "   - Total karyawan BTM: {$karyawanBTM->count()}\n";
foreach ($karyawanBTM->take(3) as $karyawan) {
    echo "   - {$karyawan->nama_lengkap} (ID: {$karyawan->id})\n";
}
echo "\n";

// 6. Cek mobil yang terkait dengan karyawan BTM
echo "6. Mobils yang terkait dengan karyawan BTM:\n";
$mobilsBTM = Mobil::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
})->get();

echo "   - Total mobils BTM: {$mobilsBTM->count()}\n";
foreach ($mobilsBTM->take(3) as $mobil) {
    echo "   - {$mobil->kode_no} - {$mobil->nomor_polisi} (Karyawan: {$mobil->karyawan->nama_lengkap})\n";
}
echo "\n";

// 7. Cek unique values di kolom cabang
echo "7. Unique values di kolom cabang karyawan:\n";
$uniqueCabang = Karyawan::distinct()->pluck('cabang')->filter();
foreach ($uniqueCabang as $cabang) {
    $count = Karyawan::where('cabang', $cabang)->count();
    echo "   - '{$cabang}': {$count} karyawan\n";
}
echo "\n";

// 8. Cek apakah ada user BTM yang login
echo "8. Sample users dengan karyawan BTM:\n";
$usersBTM = User::whereHas('karyawan', function($q) {
    $q->where('cabang', 'BTM');
})->with('karyawan')->take(3)->get();

foreach ($usersBTM as $user) {
    echo "   - User: {$user->name} (Email: {$user->email})\n";
    echo "     Karyawan: {$user->karyawan->nama_lengkap}, Cabang: {$user->karyawan->cabang}\n\n";
}

// 9. Test query filter BTM seperti di controller
echo "9. Test query filter BTM (seperti di controller):\n";
$testQuery = Mobil::with('karyawan')
    ->whereHas('karyawan', function($q) {
        $q->where('cabang', 'BTM');
    })->get();

echo "   - Result count dengan filter BTM: {$testQuery->count()}\n";
if ($testQuery->count() > 0) {
    echo "   - Sample results:\n";
    foreach ($testQuery->take(3) as $mobil) {
        echo "     * {$mobil->kode_no} - {$mobil->nomor_polisi}\n";
    }
}

echo "\n=== END DEBUG ===\n";