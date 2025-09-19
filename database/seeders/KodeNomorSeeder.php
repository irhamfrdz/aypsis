<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\KodeNomor;

class KodeNomorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kodeNomors = [
            [
                'kode' => '1001',
                'catatan' => 'Akun untuk pencatatan kas kecil perusahaan'
            ],
            [
                'kode' => '1002',
                'catatan' => 'Rekening bank BCA untuk operasional'
            ],
            [
                'kode' => '2001',
                'catatan' => 'Hutang kepada supplier'
            ],
            [
                'kode' => '3001',
                'catatan' => 'Modal yang disetor pemilik'
            ],
            [
                'kode' => '4001',
                'catatan' => 'Pendapatan dari jasa yang diberikan'
            ],
            [
                'kode' => '5001',
                'catatan' => 'Beban untuk operasional sehari-hari'
            ]
        ];

        foreach ($kodeNomors as $kodeNomor) {
            KodeNomor::create($kodeNomor);
        }
    }
}
