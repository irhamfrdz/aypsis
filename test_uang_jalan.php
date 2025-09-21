<?php<?php



require_once 'vendor/autoload.php';require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);$app = require_once 'bootstrap/app.php';

$kernel->bootstrap();

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Uang Jalan Calculation:\n\n";

use App\Models\Tujuan;

$tujuans = App\Models\Tujuan::all();

echo "Testing kolom uang_jalan di tabel tujuans...\n";

// Test the available routesecho "==============================================\n";

$validRoutes = $tujuans->where('dari', '!=', '')->where('ke', '!=', '');

echo "Available routes with pricing:\n";// Cek apakah ada data tujuan

foreach ($validRoutes as $route) {$count = Tujuan::count();

    echo "- {$route->dari} -> {$route->ke}: 20ft=Rp." . number_format($route->uang_jalan_20) . ", 40ft=Rp." . number_format($route->uang_jalan_40) . "\n";echo "Jumlah data tujuan: $count\n\n";

}

if ($count > 0) {

echo "\nTesting calculation logic:\n";    // Ambil satu data tujuan untuk test

    $tujuan = Tujuan::first();

// Simulate the JavaScript logic    echo "Data tujuan pertama:\n";

function calculateUangJalan($dari, $ke, $ukuran, $isAntarSewa = false, $tujuansData = []) {    echo "- ID: {$tujuan->id}\n";

    $tujuanData = collect($tujuansData)->first(function($tujuan) use ($dari, $ke) {    echo "- Cabang: {$tujuan->cabang}\n";

        return $tujuan['dari'] === $dari && $tujuan['ke'] === $ke;    echo "- Wilayah: {$tujuan->wilayah}\n";

    });    echo "- Rute: {$tujuan->rute}\n";

    echo "- Uang Jalan: Rp " . number_format($tujuan->uang_jalan ?? 0, 0, ',', '.') . "\n";

    if ($tujuanData) {    echo "- Uang Jalan 20ft: Rp " . number_format($tujuan->uang_jalan_20 ?? 0, 0, ',', '.') . "\n";

        if ($isAntarSewa) {    echo "- Uang Jalan 40ft: Rp " . number_format($tujuan->uang_jalan_40 ?? 0, 0, ',', '.') . "\n\n";

            return $ukuran === '20' || $ukuran === '10' ? $tujuanData['antar_20'] : $tujuanData['antar_40'];

        } else {    // Test update uang_jalan

            return $ukuran === '20' || $ukuran === '10' ? $tujuanData['uang_jalan_20'] : $tujuanData['uang_jalan_40'];    echo "Testing update kolom uang_jalan...\n";

        }    $oldValue = $tujuan->uang_jalan;

    } else {    $newValue = 150000;

        // Fallback pricing

        if ($isAntarSewa) {    $tujuan->uang_jalan = $newValue;

            return $ukuran === '20' || $ukuran === '10' ? 250000 : 350000;    $tujuan->save();

        } else {

            return $ukuran === '20' || $ukuran === '10' ? 200000 : 300000;    // Refresh dari database

        }    $tujuan->refresh();

    }

}    if ($tujuan->uang_jalan == $newValue) {

        echo "✅ Update berhasil! Uang jalan: Rp " . number_format($tujuan->uang_jalan, 0, ',', '.') . "\n";

// Test calculations

$testCases = [        // Kembalikan ke nilai lama

    ['Dermaga', 'Semut', '20', false],        $tujuan->uang_jalan = $oldValue;

    ['Merak', 'Jakarta', '20', false],        $tujuan->save();

    ['Dermaga', 'Semut', '40', false],        echo "✅ Data dikembalikan ke nilai semula.\n";

    ['Merak', 'Jakarta', '40', false],    } else {

    ['NonExistent', 'Nowhere', '20', false], // Should use fallback        echo "❌ Update gagal!\n";

];    }



foreach ($testCases as $test) {} else {

    list($dari, $ke, $ukuran, $isAntar) = $test;    echo "Tidak ada data tujuan. Membuat data test...\n";

    $result = calculateUangJalan($dari, $ke, $ukuran, $isAntar, $tujuans->toArray());

    echo "Route: {$dari} -> {$ke} ({$ukuran}ft, antar=" . ($isAntar ? 'yes' : 'no') . ") = Rp." . number_format($result) . "\n";    // Buat data test

}    $testData = [

        'cabang' => 'JKT',

echo "\n✅ Test completed!\n";        'wilayah' => 'Jakarta',
        'rute' => 'Jakarta - Bandung',
        'uang_jalan' => 100000,
        'uang_jalan_20' => 50000,
        'ongkos_truk_20' => 25000,
        'uang_jalan_40' => 75000,
        'ongkos_truk_40' => 35000,
        'antar_20' => 20000,
        'antar_40' => 30000,
    ];

    $tujuan = Tujuan::create($testData);

    if ($tujuan) {
        echo "✅ Data test berhasil dibuat!\n";
        echo "- ID: {$tujuan->id}\n";
        echo "- Uang Jalan: Rp " . number_format($tujuan->uang_jalan, 0, ',', '.') . "\n";
    } else {
        echo "❌ Gagal membuat data test!\n";
    }
}

echo "\nTest selesai!\n";
