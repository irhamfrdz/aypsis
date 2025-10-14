<?php

/**
 * Script Analisis Logic Import Controller
 *
 * Script ini akan menganalisis logic import di DaftarTagihanKontainerSewaController
 * untuk memahami bagaimana data diproses dan dihitung
 */

// Pastikan script dijalankan dari direktori Laravel
if (!file_exists('artisan')) {
    die("Error: Script harus dijalankan dari root direktori Laravel\n");
}

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\DaftarTagihanKontainerSewa;
use App\Models\MasterPricelistSewaKontainer;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

echo "=== ANALISIS LOGIC IMPORT & PERHITUNGAN ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // 1. Cek Master Pricelist
    echo "📋 MASTER PRICELIST:\n";
    $pricelist = MasterPricelistSewaKontainer::all();

    if ($pricelist->isEmpty()) {
        echo "⚠️  Tidak ada data master pricelist\n";
    } else {
        foreach ($pricelist as $price) {
            echo "   Vendor: {$price->vendor}, Size: {$price->size}, ";
            echo "Harian: " . number_format($price->harga_harian ?? 0, 0) . ", ";
            echo "Bulanan: " . number_format($price->harga_bulanan ?? 0, 0) . "\n";
        }
    }
    echo "\n";

    // 2. Simulasi Perhitungan berdasarkan Logic di Controller
    echo "🧮 SIMULASI PERHITUNGAN:\n";

    // Ambil sample data dari CSV untuk simulasi
    $sampleData = [
        [
            'vendor' => 'ZONA',
            'size' => '40',
            'tanggal_awal' => '2024-01-15',
            'tanggal_akhir' => '2024-02-14',
            'periode' => 1,
            'tarif' => 'Bulanan'
        ],
        [
            'vendor' => 'ZONA',
            'size' => '20',
            'tanggal_awal' => '2025-01-21',
            'tanggal_akhir' => '2025-02-20',
            'periode' => 1,
            'tarif' => 'Bulanan'
        ],
        [
            'vendor' => 'ZONA',
            'size' => '20',
            'tanggal_awal' => '2025-04-15',
            'tanggal_akhir' => '2025-04-24',
            'periode' => 4,
            'tarif' => 'Harian'
        ]
    ];

    foreach ($sampleData as $index => $data) {
        echo ($index + 1) . ". Simulasi: {$data['vendor']} {$data['size']}ft {$data['tarif']} P{$data['periode']}\n";

        // Hitung jumlah hari
        $startDate = Carbon::parse($data['tanggal_awal']);
        $endDate = Carbon::parse($data['tanggal_akhir']);
        $jumlahHari = $startDate->diffInDays($endDate);

        echo "   Periode: {$data['tanggal_awal']} s/d {$data['tanggal_akhir']} = {$jumlahHari} hari\n";

        // Simulasi calculateFinancialData dari controller
        $financialData = simulateCalculateFinancialData($data, $jumlahHari);

        echo "   DPP: " . number_format($financialData['dpp'], 2) . "\n";
        echo "   PPN (11%): " . number_format($financialData['ppn'], 2) . "\n";
        echo "   PPH (2%): " . number_format($financialData['pph'], 2) . "\n";
        echo "   Grand Total: " . number_format($financialData['grand_total'], 2) . "\n";
        echo "\n";
    }

    // 3. Analisis data existing untuk pola
    echo "📊 ANALISIS POLA DATA EXISTING:\n";

    // Ambil beberapa sample dari database
    $sampleContainers = ['AMFU8640522', 'APZU3960241', 'BMOU2383868'];

    foreach ($sampleContainers as $container) {
        echo "📦 Container: {$container}\n";

        $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)
            ->orderBy('periode')
            ->get();

        if ($records->isEmpty()) {
            echo "   Tidak ada data\n\n";
            continue;
        }

        foreach ($records as $record) {
            echo "   P{$record->periode}: {$record->tanggal_awal} s/d {$record->tanggal_akhir}\n";
            echo "     Tarif: {$record->tarif}, Status: {$record->status}\n";

            // Hitung jumlah hari actual
            $startDate = Carbon::parse($record->tanggal_awal);
            $endDate = Carbon::parse($record->tanggal_akhir);
            $actualDays = $startDate->diffInDays($endDate);

            echo "     Hari: {$actualDays}, DPP: " . number_format($record->dpp, 2);

            if ($record->adjustment != 0) {
                echo " + Adj: " . number_format($record->adjustment, 2);
            }

            echo "\n";
            echo "     PPN: " . number_format($record->ppn, 2) .
                 ", PPH: " . number_format($record->pph, 2) .
                 ", GT: " . number_format($record->grand_total, 2) . "\n";

            // Verifikasi perhitungan
            $adjustedDpp = $record->dpp + $record->adjustment;
            $expectedPpn = $adjustedDpp * 0.11;
            $expectedPph = $adjustedDpp * 0.02;
            $expectedGrandTotal = $adjustedDpp + $expectedPpn - $expectedPph;

            if (abs($record->ppn - $expectedPpn) > 0.01 ||
                abs($record->pph - $expectedPph) > 0.01 ||
                abs($record->grand_total - $expectedGrandTotal) > 0.01) {
                echo "     ⚠️  Perhitungan tidak sesuai!\n";
                echo "     Expected: PPN=" . number_format($expectedPpn, 2) .
                     ", PPH=" . number_format($expectedPph, 2) .
                     ", GT=" . number_format($expectedGrandTotal, 2) . "\n";
            } else {
                echo "     ✅ Perhitungan sesuai\n";
            }
            echo "\n";
        }
    }

    // 4. Cek logic tarif default dari controller
    echo "💰 LOGIC TARIF DEFAULT:\n";
    echo "   Dari controller: getDefaultDailyRate()\n";

    $defaultRates = [
        'ZONA' => ['20' => 22522, '40' => 45044],
        'DPE' => ['20' => 20000, '40' => 40000]
    ];

    foreach ($defaultRates as $vendor => $sizes) {
        echo "   {$vendor}:\n";
        foreach ($sizes as $size => $rate) {
            echo "     Size {$size}: " . number_format($rate, 0) . " per hari\n";
        }
    }
    echo "\n";

    // 5. Rekomendasi
    echo "=== REKOMENDASI ===\n";
    echo "1. 🔍 Periksa master pricelist - pastikan ada data untuk semua vendor/size\n";
    echo "2. 📊 Verifikasi perhitungan PPN (11%) dan PPH (2%) pada semua data\n";
    echo "3. 📅 Periksa logic perhitungan periode - apakah menggunakan jumlah hari yang benar\n";
    echo "4. 🧮 Cek apakah adjustment sudah diterapkan dengan benar pada perhitungan PPN/PPH\n";
    echo "5. 📝 Review logic import untuk format DPE vs ZONA\n";

    echo "\n✅ Analisis selesai!\n";

} catch (\Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

/**
 * Simulate calculateFinancialData method dari controller
 */
function simulateCalculateFinancialData($data, $jumlahHari)
{
    // Default rates dari controller
    $defaultRates = [
        'ZONA' => ['20' => 22522, '40' => 45044],
        'DPE' => ['20' => 20000, '40' => 40000]
    ];

    $vendor = $data['vendor'];
    $size = $data['size'];
    $tarif = $data['tarif'];
    $periode = $data['periode'];

    // Get tarif nominal
    $tarifNominal = $defaultRates[$vendor][$size] ?? 0;

    // Calculate DPP
    if (strtolower($tarif) === 'bulanan') {
        // Monthly: tarif harian * 30 * periode
        $dpp = $tarifNominal * 30 * $periode;
    } else {
        // Daily: tarif harian * jumlah hari
        $dpp = $tarifNominal * $jumlahHari;
    }

    // Calculate taxes
    $ppn = $dpp * 0.11;  // 11%
    $pph = $dpp * 0.02;  // 2%
    $grandTotal = $dpp + $ppn - $pph;

    return [
        'dpp' => $dpp,
        'ppn' => $ppn,
        'pph' => $pph,
        'grand_total' => $grandTotal,
        'adjustment' => 0
    ];
}
