<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Coa;

class CoaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coas = [
            [
                'nomor_akun' => '1001',
                'nama_akun' => 'Kas',
                'tipe_akun' => 'Aset',
                'saldo' => 5000000.00,
            ],
            [
                'nomor_akun' => '1002',
                'nama_akun' => 'Bank BCA',
                'tipe_akun' => 'Aset',
                'saldo' => 15000000.00,
            ],
            [
                'nomor_akun' => '2001',
                'nama_akun' => 'Hutang Usaha',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 2500000.00,
            ],
            [
                'nomor_akun' => '3001',
                'nama_akun' => 'Modal',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 20000000.00,
            ],
            [
                'nomor_akun' => '4001',
                'nama_akun' => 'Pendapatan Sewa Kontainer',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 7500000.00,
            ],
            [
                'nomor_akun' => '5001',
                'nama_akun' => 'Biaya Operasional',
                'tipe_akun' => 'Beban',
                'saldo' => 1200000.00,
            ],
            [
                'nomor_akun' => '1003',
                'nama_akun' => 'Piutang Usaha',
                'tipe_akun' => 'Aset',
                'saldo' => 3500000.00,
            ],
            [
                'nomor_akun' => '2002',
                'nama_akun' => 'Hutang Bank',
                'tipe_akun' => 'Kewajiban',
                'saldo' => 8000000.00,
            ],
        ];

        foreach ($coas as $coa) {
            Coa::firstOrCreate(
                ['nomor_akun' => $coa['nomor_akun']],
                $coa
            );
        }
    }
}
