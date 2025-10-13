<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterKegiatan;

class MasterKegiatanUangMukaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kegiatanUangMuka = [
            [
                'kode_kegiatan' => 'UM001',
                'nama_kegiatan' => 'Uang Muka Pengiriman Lokal',
                'type' => 'uang muka',
                'keterangan' => 'Uang muka untuk kegiatan pengiriman dalam kota',
                'status' => 'aktif',
            ],
            [
                'kode_kegiatan' => 'UM002',
                'nama_kegiatan' => 'Uang Muka Pengiriman Antar Kota',
                'type' => 'uang muka',
                'keterangan' => 'Uang muka untuk kegiatan pengiriman antar kota',
                'status' => 'aktif',
            ],
            [
                'kode_kegiatan' => 'UM003',
                'nama_kegiatan' => 'Uang Muka Pengiriman Ekspor',
                'type' => 'uang muka',
                'keterangan' => 'Uang muka untuk kegiatan pengiriman ekspor kontainer',
                'status' => 'aktif',
            ],
            [
                'kode_kegiatan' => 'UM004',
                'nama_kegiatan' => 'Uang Muka Bongkar Muat',
                'type' => 'uang muka',
                'keterangan' => 'Uang muka untuk kegiatan bongkar muat kontainer',
                'status' => 'aktif',
            ],
            [
                'kode_kegiatan' => 'UM005',
                'nama_kegiatan' => 'Uang Muka Operasional Harian',
                'type' => 'uang muka',
                'keterangan' => 'Uang muka untuk operasional harian supir',
                'status' => 'aktif',
            ],
        ];

        foreach ($kegiatanUangMuka as $kegiatan) {
            MasterKegiatan::updateOrCreate([
                'kode_kegiatan' => $kegiatan['kode_kegiatan']
            ], $kegiatan);
        }

        $this->command->info('✅ Berhasil membuat ' . count($kegiatanUangMuka) . ' master kegiatan uang muka!');
    }
}
