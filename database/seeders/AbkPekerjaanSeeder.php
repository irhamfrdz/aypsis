<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class AbkPekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pekerjaans = [
            'nahkoda',
            'mualim I',
            'mualim II',
            'mualim III',
            'kkm',
            'kkm att III',
            'masinis I',
            'masinis II',
            'masinis III',
            'masinis IV',
            'juru mudi',
            'juru minyak',
            'juru masak',
            'bosun',
            'cadet deck',
            'cadet engineer'
        ];

        foreach($pekerjaans as $index => $pekerjaan) {
            $nama = strtoupper($pekerjaan);
            $kode = 'ABK' . str_pad($index + 1, 3, '0', STR_PAD_LEFT);

            Pekerjaan::create([
                'nama_pekerjaan' => $nama,
                'kode_pekerjaan' => $kode,
                'divisi' => 'ABK',
                'is_active' => true
            ]);
        }
    }
}
