<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PricelistTarifHistory;
use App\Models\PricelistUangJalanBatam;
use Illuminate\Support\Facades\DB;

$newBaseRates = [
    1 => [
        'tarif_20ft_full' => 84000,
        'tarif_20ft_empty' => 72000,
        'tarif_antarlokasi_20ft' => 42000,
        'tarif_40ft_full' => 100000,
        'tarif_40ft_empty' => 86000,
        'tarif_antarlokasi_40ft' => 50000,
    ],
    2 => [
        'tarif_20ft_full' => 142000,
        'tarif_20ft_empty' => 120000,
        'tarif_antarlokasi_20ft' => 42000,
        'tarif_40ft_full' => 160000,
        'tarif_40ft_empty' => 140000,
        'tarif_antarlokasi_40ft' => 50000,
    ],
    3 => [
        'tarif_20ft_full' => 222000,
        'tarif_20ft_empty' => 187000,
        'tarif_antarlokasi_20ft' => 42000,
        'tarif_40ft_full' => 260000,
        'tarif_40ft_empty' => 224000,
        'tarif_antarlokasi_40ft' => 50000,
    ],
    4 => [
        'tarif_20ft_full' => 257000,
        'tarif_20ft_empty' => 221000,
        'tarif_antarlokasi_20ft' => 42000,
        'tarif_40ft_full' => 298000,
        'tarif_40ft_empty' => 258000,
        'tarif_antarlokasi_40ft' => 50000,
    ],
    5 => [
        'tarif_20ft_full' => 465000,
        'tarif_20ft_empty' => 403000,
        'tarif_antarlokasi_20ft' => 42000,
        'tarif_40ft_full' => 551000,
        'tarif_40ft_empty' => 473000,
        'tarif_antarlokasi_40ft' => 50000,
    ],
];

DB::beginTransaction();

try {
    // 1. Update the base rates (kelola_bbm_id IS NULL) for expedisi = 'AYP'
    foreach ($newBaseRates as $ring => $rates) {
        $baseRecord = PricelistUangJalanBatam::whereNull('kelola_bbm_id')
            ->where('expedisi', 'AYP')
            ->where('ring', $ring)
            ->first();

        if ($baseRecord) {
            $updateData = [];
            foreach ($rates as $field => $val) {
                $updateData[$field] = $val;
                $updateData[$field.'_base'] = $val;
            }
            $baseRecord->update($updateData);
            echo "Updated base tarif AYP Ring {$ring} successfully.\n";
        } else {
            echo "Warning: Base record for AYP Ring {$ring} not found.\n";
        }
    }

    // 2. Delete all existing versioned pricelist records (kelola_bbm_id IS NOT NULL)
    // so we can regenerate them cleanly from the new base records.
    PricelistTarifHistory::query()->delete();
    PricelistUangJalanBatam::whereNotNull('kelola_bbm_id')->delete();
    echo "Cleared old versioned tariffs and history logs.\n";

    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
    echo 'Error updating base tariffs: '.$e->getMessage()."\n";
    exit(1);
}

// 3. Run the backfill logic to regenerate all versioned periods
echo "Regenerating BBM versioned tariffs...\n";
require __DIR__.'/backfill.php';
