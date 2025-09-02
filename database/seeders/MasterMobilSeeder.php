<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mobil;

class MasterMobilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'aktiva' => 'MOB001',
                'plat' => 'B 1234 ABC',
                'nomor_rangka' => 'RANGKA001',
                'ukuran' => 'Truk Besar',
            ],
            [
                'aktiva' => 'MOB002',
                'plat' => 'B 5678 DEF',
                'nomor_rangka' => 'RANGKA002',
                'ukuran' => 'Truk Sedang',
            ],
            [
                'aktiva' => 'MOB003',
                'plat' => 'B 9012 GHI',
                'nomor_rangka' => 'RANGKA003',
                'ukuran' => 'Truk Kecil',
            ],
        ];

        foreach ($data as $item) {
            Mobil::updateOrCreate(
                ['aktiva' => $item['aktiva']],
                $item
            );
        }
    }
}
