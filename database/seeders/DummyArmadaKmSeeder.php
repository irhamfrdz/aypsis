<?php

namespace Database\Seeders;

use App\Models\Mobil;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class DummyArmadaKmSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            throw new RuntimeException('Data dummy Km armada hanya boleh diisi pada environment local.');
        }

        $result = DB::transaction(function () {
            $armadas = Mobil::whereNotNull('imei_gps')->whereRaw("TRIM(imei_gps) <> ''")
                ->whereNull('km')->orderBy('id')->lockForUpdate()->get();

            foreach ($armadas as $index => $armada) {
                $armada->km = 50000 + ($index * 7500);
                $armada->save();
                $armada->logCustomAction('updated', 'Pengisian Km awal DUMMY untuk simulasi Dashboard Armada.', [
                    'km' => $armada->km, 'dummy' => true, 'previous_km' => null,
                ]);
            }

            return [
                'count' => $armadas->count(),
                'min' => $armadas->min('km'),
                'max' => $armadas->max('km'),
            ];
        });

        $this->command?->info('Km awal dummy tersimpan pada '.$result['count'].' armada GPS. Nilai yang sudah terisi tetap dipertahankan.');
        if ($result['count'] > 0) {
            $this->command?->info('Rentang dummy: '.number_format($result['min'], 0, ',', '.').'–'.number_format($result['max'], 0, ',', '.').' km.');
        }
        $remaining = Mobil::whereNotNull('imei_gps')->whereRaw("TRIM(imei_gps) <> ''")->whereNull('km')->count();
        $this->command?->info('Armada GPS dengan Km masih kosong: '.$remaining.'.');
    }
}
