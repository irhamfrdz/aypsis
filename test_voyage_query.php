<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Bl;

echo "Test Query Voyage\n";
echo "========================================\n\n";

$namaKapal = "KM SUMBER ABADI 178";
echo "Input nama kapal: {$namaKapal}\n\n";

// Test 1: Query langsung
echo "Test 1: Query langsung dengan nama exact\n";
$test1 = DB::table('bls')
    ->where('nama_kapal', $namaKapal)
    ->whereNotNull('no_voyage')
    ->distinct()
    ->pluck('no_voyage')
    ->toArray();
echo "Hasil: " . json_encode($test1) . "\n";
echo "Jumlah: " . count($test1) . "\n\n";

// Test 2: Query dengan LIKE
echo "Test 2: Query dengan LIKE\n";
$test2 = DB::table('bls')
    ->where('nama_kapal', 'like', '%SUMBER ABADI 178%')
    ->whereNotNull('no_voyage')
    ->distinct()
    ->pluck('no_voyage')
    ->toArray();
echo "Hasil: " . json_encode($test2) . "\n";
echo "Jumlah: " . count($test2) . "\n\n";

// Test 3: Simulasi logic di controller
echo "Test 3: Simulasi logic di controller\n";
$kapalClean = preg_replace('/^KM\.?\s*/i', '', $namaKapal);
echo "Kapal clean: {$kapalClean}\n";

$test3 = Bl::where(function($query) use ($namaKapal, $kapalClean) {
        $query->where('nama_kapal', $namaKapal)
              ->orWhere('nama_kapal', 'like', '%' . $kapalClean . '%');
    })
    ->whereNotNull('no_voyage')
    ->distinct()
    ->orderBy('no_voyage', 'desc')
    ->pluck('no_voyage')
    ->toArray();

echo "Hasil: " . json_encode($test3) . "\n";
echo "Jumlah: " . count($test3) . "\n\n";

// Test 4: Lihat SQL query yang dijalankan
echo "Test 4: SQL Query yang dijalankan\n";
DB::enableQueryLog();

Bl::where(function($query) use ($namaKapal, $kapalClean) {
        $query->where('nama_kapal', $namaKapal)
              ->orWhere('nama_kapal', 'like', '%' . $kapalClean . '%');
    })
    ->whereNotNull('no_voyage')
    ->distinct()
    ->orderBy('no_voyage', 'desc')
    ->pluck('no_voyage')
    ->toArray();

$queries = DB::getQueryLog();
foreach ($queries as $q) {
    echo "Query: {$q['query']}\n";
    echo "Bindings: " . json_encode($q['bindings']) . "\n";
}
