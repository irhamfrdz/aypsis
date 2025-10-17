<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterTerminal;

class MasterTerminalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terminals = [
            [
                'kode_terminal' => 'TRM001',
                'nama_terminal' => 'Terminal Tanjung Priok',
                'lokasi' => 'Jakarta Utara',
                'status' => 'aktif',
                'keterangan' => 'Terminal utama di Pelabuhan Tanjung Priok'
            ],
            [
                'kode_terminal' => 'TRM002',
                'nama_terminal' => 'Terminal Teluk Lamong',
                'lokasi' => 'Surabaya',
                'status' => 'aktif',
                'keterangan' => 'Terminal modern di Pelabuhan Tanjung Perak'
            ],
            [
                'kode_terminal' => 'TRM003',
                'nama_terminal' => 'Terminal Semarang',
                'lokasi' => 'Semarang',
                'status' => 'aktif',
                'keterangan' => 'Terminal di Pelabuhan Tanjung Emas'
            ],
            [
                'kode_terminal' => 'TRM004',
                'nama_terminal' => 'Terminal Makassar',
                'lokasi' => 'Makassar',
                'status' => 'aktif',
                'keterangan' => 'Terminal di Pelabuhan Soekarno-Hatta'
            ],
            [
                'kode_terminal' => 'TRM005',
                'nama_terminal' => 'Terminal Belawan',
                'lokasi' => 'Medan',
                'status' => 'aktif',
                'keterangan' => 'Terminal di Pelabuhan Belawan'
            ]
        ];

        foreach ($terminals as $terminal) {
            MasterTerminal::create($terminal);
        }
    }
}
