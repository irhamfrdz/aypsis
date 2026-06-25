<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
if ($app === true) {
    $app = \Illuminate\Support\Facades\App::getInstance();
}
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\KelolaBbm;
use App\Models\PricelistUangJalanBatam;

$fields = [
    'tarif_20ft_full' => 'tarif_20ft_full_base',
    'tarif_20ft_empty' => 'tarif_20ft_empty_base',
    'tarif_40ft_full' => 'tarif_40ft_full_base',
    'tarif_40ft_empty' => 'tarif_40ft_empty_base',
    'tarif_antarlokasi_20ft' => 'tarif_antarlokasi_20ft_base',
    'tarif_antarlokasi_40ft' => 'tarif_antarlokasi_40ft_base',
];

$basePricelists = PricelistUangJalanBatam::whereNull('kelola_bbm_id')->get();
foreach ($basePricelists as $base) {
    $updateData = [];
    foreach ($fields as $tarifField => $baseField) {
        $updateData[$tarifField] = $base->$baseField ?? $base->$tarifField;
    }
    $base->update($updateData);
}
echo "Tarif Dasar (Base) active rates reset to base values.\n";

$bbms = KelolaBbm::all();

foreach ($bbms as $bbm) {
    $exists = PricelistUangJalanBatam::where('kelola_bbm_id', $bbm->id)->exists();
    if (! $exists) {
        $basePricelists = PricelistUangJalanBatam::whereNull('kelola_bbm_id')->get();
        if ($basePricelists->isEmpty()) {
            echo "BBM ID {$bbm->id}: Skip (Base tarif kosong).\n";

            continue;
        }

        foreach ($basePricelists as $base) {
            $data = $base->toArray();
            unset($data['id']);
            $data['kelola_bbm_id'] = $bbm->id;

            if ($bbm->persentase > 5) {
                $perubahanTarif = $bbm->persentase - 5;
                $faktor = 1 + ($perubahanTarif / 100);
                foreach ($fields as $tarifField => $baseField) {
                    $tarifBase = $data[$baseField] ?? $data[$tarifField];
                    $data[$tarifField] = round($tarifBase * $faktor, -3);
                }
            } else {
                foreach ($fields as $tarifField => $baseField) {
                    $data[$tarifField] = $data[$baseField] ?? $data[$tarifField];
                }
            }

            PricelistUangJalanBatam::create($data);
        }
        echo "BBM ID {$bbm->id} ({$bbm->bulan}/{$bbm->tahun}): Berhasil backfill tarif.\n";
    } else {
        echo "BBM ID {$bbm->id} ({$bbm->bulan}/{$bbm->tahun}): Skip (Tarif sudah ada).\n";
    }
}

echo "Proses selesai!\n";
