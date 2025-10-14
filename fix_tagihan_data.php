<?php

/**
 * Script Perbaikan Data Tagihan Kontainer Sewa
 *
 * Script ini akan:
 * 1. Memperbaiki master pricelist
 * 2. Recalculate semua nilai finansial berdasarkan logic yang benar
 * 3. Memperbaiki inkonsistensi data
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
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

echo "=== SCRIPT PERBAIKAN DATA TAGIHAN KONTAINER SEWA ===\n";
echo "Tanggal: " . date('Y-m-d H:i:s') . "\n\n";

try {
    // Step 1: Perbaiki Master Pricelist
    echo "🔧 STEP 1: PERBAIKI MASTER PRICELIST\n";

    // Data pricelist yang benar berdasarkan analisis
    $correctPricelistData = [
        ['vendor' => 'ZONA', 'size' => '20', 'harga_harian' => 22522, 'harga_bulanan' => 675660],  // 22522 * 30
        ['vendor' => 'ZONA', 'size' => '40', 'harga_harian' => 45044, 'harga_bulanan' => 1351320], // 45044 * 30
        ['vendor' => 'DPE', 'size' => '20', 'harga_harian' => 20000, 'harga_bulanan' => 600000],   // 20000 * 30
        ['vendor' => 'DPE', 'size' => '40', 'harga_harian' => 40000, 'harga_bulanan' => 1200000],  // 40000 * 30
    ];

    echo "📋 Membersihkan master pricelist lama...\n";
    MasterPricelistSewaKontainer::truncate();

    echo "📝 Memasukkan data pricelist yang benar...\n";
    foreach ($correctPricelistData as $data) {
        MasterPricelistSewaKontainer::create($data);
        echo "   ✅ {$data['vendor']} {$data['size']}ft: Harian=" . number_format($data['harga_harian']) . ", Bulanan=" . number_format($data['harga_bulanan']) . "\n";
    }

    echo "\n";

    // Step 2: Analisis data yang perlu diperbaiki
    echo "🔍 STEP 2: ANALISIS DATA YANG PERLU DIPERBAIKI\n";

    $allTagihan = DaftarTagihanKontainerSewa::whereNotNull('nomor_kontainer')
        ->where('nomor_kontainer', 'NOT LIKE', 'GROUP_%')
        ->orderBy('nomor_kontainer')
        ->orderBy('periode')
        ->get();

    echo "📊 Total tagihan yang perlu dianalisis: " . $allTagihan->count() . "\n";

    $needRecalculation = [];
    $totalInconsistent = 0;

    foreach ($allTagihan as $tagihan) {
        // Hitung ulang nilai finansial
        $recalculated = recalculateFinancialData($tagihan);

        // Cek apakah ada perbedaan
        $hasDifference = false;
        $differences = [];

        if (abs($tagihan->dpp - $recalculated['dpp']) > 0.01) {
            $hasDifference = true;
            $differences[] = "DPP: " . number_format($tagihan->dpp, 2) . " → " . number_format($recalculated['dpp'], 2);
        }

        if (abs($tagihan->ppn - $recalculated['ppn']) > 0.01) {
            $hasDifference = true;
            $differences[] = "PPN: " . number_format($tagihan->ppn, 2) . " → " . number_format($recalculated['ppn'], 2);
        }

        if (abs($tagihan->pph - $recalculated['pph']) > 0.01) {
            $hasDifference = true;
            $differences[] = "PPH: " . number_format($tagihan->pph, 2) . " → " . number_format($recalculated['pph'], 2);
        }

        if (abs($tagihan->grand_total - $recalculated['grand_total']) > 0.01) {
            $hasDifference = true;
            $differences[] = "GT: " . number_format($tagihan->grand_total, 2) . " → " . number_format($recalculated['grand_total'], 2);
        }

        if ($hasDifference) {
            $needRecalculation[] = [
                'id' => $tagihan->id,
                'container' => $tagihan->nomor_kontainer,
                'periode' => $tagihan->periode,
                'differences' => $differences,
                'old_data' => [
                    'dpp' => $tagihan->dpp,
                    'ppn' => $tagihan->ppn,
                    'pph' => $tagihan->pph,
                    'grand_total' => $tagihan->grand_total
                ],
                'new_data' => $recalculated
            ];
            $totalInconsistent++;
        }
    }

    echo "❌ Data yang perlu diperbaiki: {$totalInconsistent}\n";
    echo "✅ Data yang sudah benar: " . ($allTagihan->count() - $totalInconsistent) . "\n\n";

    if ($totalInconsistent > 0) {
        // Tampilkan sample perbedaan
        echo "📋 SAMPLE PERBEDAAN (10 pertama):\n";
        foreach (array_slice($needRecalculation, 0, 10) as $item) {
            echo "   {$item['container']} P{$item['periode']}: " . implode(', ', $item['differences']) . "\n";
        }
        if (count($needRecalculation) > 10) {
            echo "   ... dan " . (count($needRecalculation) - 10) . " lainnya\n";
        }
        echo "\n";

        // Step 3: Konfirmasi perbaikan
        echo "⚠️  KONFIRMASI PERBAIKAN\n";
        echo "Script akan memperbaiki {$totalInconsistent} record dengan perhitungan yang benar.\n";
        echo "Apakah Anda yakin ingin melanjutkan? (ketik 'YA' untuk konfirmasi): ";

        $handle = fopen("php://stdin", "r");
        $confirmation = trim(fgets($handle));
        fclose($handle);

        if (strtoupper($confirmation) !== 'YA') {
            echo "❌ Perbaikan dibatalkan oleh user.\n";
            exit(0);
        }

        // Step 4: Proses perbaikan
        echo "\n🔧 STEP 4: PROSES PERBAIKAN DATA\n";

        DB::beginTransaction();

        $fixedCount = 0;
        $errors = [];

        foreach ($needRecalculation as $item) {
            try {
                $tagihan = DaftarTagihanKontainerSewa::find($item['id']);

                if (!$tagihan) {
                    $errors[] = "Tagihan ID {$item['id']} tidak ditemukan";
                    continue;
                }

                // Update dengan nilai yang benar
                $tagihan->update($item['new_data']);

                echo "✅ Fixed: {$item['container']} P{$item['periode']}\n";
                $fixedCount++;

                // Log perubahan
                Log::info("Tagihan finansial diperbaiki", [
                    'tagihan_id' => $item['id'],
                    'container' => $item['container'],
                    'periode' => $item['periode'],
                    'old_data' => $item['old_data'],
                    'new_data' => $item['new_data'],
                    'fixed_by' => 'fix_script',
                    'timestamp' => now()
                ]);

            } catch (\Exception $e) {
                $errors[] = "Error pada {$item['container']} P{$item['periode']}: " . $e->getMessage();
            }
        }

        if (empty($errors)) {
            DB::commit();
            echo "\n✅ Transaction committed successfully.\n";
        } else {
            DB::rollback();
            echo "\n❌ Transaction rolled back due to errors:\n";
            foreach ($errors as $error) {
                echo "   - {$error}\n";
            }
        }

        echo "\n📊 SUMMARY PERBAIKAN:\n";
        echo "   Data diperbaiki: {$fixedCount}\n";
        echo "   Errors: " . count($errors) . "\n";

    } else {
        echo "🎉 Semua data sudah benar, tidak perlu perbaikan!\n";
    }

    // Step 5: Verifikasi hasil
    echo "\n🔍 STEP 5: VERIFIKASI HASIL\n";

    // Re-check beberapa sample
    $sampleContainers = ['AMFU8640522', 'APZU3960241', 'BMOU2383868'];

    foreach ($sampleContainers as $container) {
        echo "📦 {$container}:\n";

        $records = DaftarTagihanKontainerSewa::where('nomor_kontainer', $container)
            ->orderBy('periode')
            ->limit(3)
            ->get();

        foreach ($records as $record) {
            $recalculated = recalculateFinancialData($record);

            $isCorrect = (
                abs($record->dpp - $recalculated['dpp']) <= 0.01 &&
                abs($record->ppn - $recalculated['ppn']) <= 0.01 &&
                abs($record->pph - $recalculated['pph']) <= 0.01 &&
                abs($record->grand_total - $recalculated['grand_total']) <= 0.01
            );

            echo "   P{$record->periode}: " . ($isCorrect ? "✅ Correct" : "❌ Still incorrect") . "\n";
        }
    }

    echo "\n🎉 Perbaikan selesai!\n";

} catch (\Exception $e) {
    if (DB::transactionLevel() > 0) {
        DB::rollback();
    }

    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";

    Log::error("Fix tagihan kontainer gagal", [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);

    exit(1);
}

/**
 * Recalculate financial data berdasarkan logic yang benar
 */
function recalculateFinancialData($tagihan)
{
    // Default rates
    $defaultRates = [
        'ZONA' => ['20' => 22522, '40' => 45044],
        'DPE' => ['20' => 20000, '40' => 40000]
    ];

    $vendor = $tagihan->vendor;
    $size = $tagihan->size;
    $periode = $tagihan->periode;
    $tarif = strtolower($tagihan->tarif ?? 'bulanan');
    $adjustment = floatval($tagihan->adjustment ?? 0);

    // Get daily rate
    $dailyRate = $defaultRates[$vendor][$size] ?? 0;

    // Calculate base DPP
    if ($tarif === 'bulanan' || $tarif === 'monthly') {
        // Monthly rate: daily rate * 30 * periode
        $baseDpp = $dailyRate * 30 * $periode;
    } else {
        // Daily rate: calculate actual days
        $startDate = Carbon::parse($tagihan->tanggal_awal);
        $endDate = Carbon::parse($tagihan->tanggal_akhir);
        $actualDays = $startDate->diffInDays($endDate);
        $baseDpp = $dailyRate * $actualDays;
    }

    // Apply adjustment
    $adjustedDpp = $baseDpp + $adjustment;

    // Calculate taxes based on adjusted DPP
    $ppn = round($adjustedDpp * 0.11, 2);     // 11%
    $pph = round($adjustedDpp * 0.02, 2);     // 2%
    $grandTotal = round($adjustedDpp + $ppn - $pph, 2);

    // Calculate DPP Nilai Lain (11/12 of adjusted DPP)
    $dppNilaiLain = round($adjustedDpp * 11/12, 2);

    return [
        'dpp' => round($baseDpp, 2),
        'adjustment' => $adjustment,
        'dpp_nilai_lain' => $dppNilaiLain,
        'ppn' => $ppn,
        'pph' => $pph,
        'grand_total' => $grandTotal
    ];
}
