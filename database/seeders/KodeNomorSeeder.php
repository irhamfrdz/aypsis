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
                'nomor_akun' => '1-1001',
                'nama_akun' => 'Kas Kecil',
                'tipe_akun' => 'Aktiva',
                'saldo' => 5000000.00,
                'nama' => 'Kas Kecil',
                'deskripsi' => 'Akun untuk pencatatan kas kecil perusahaan'
            ],
            [
                'kode' => '1002',
                'nomor_akun' => '1-1002',
                'nama_akun' => 'Bank BCA',
                'tipe_akun' => 'Aktiva',
                'saldo' => 25000000.00,
                'nama' => 'Bank BCA',
                'deskripsi' => 'Rekening bank BCA untuk operasional'
            ],
            [
                'kode' => '2001',
                'nomor_akun' => '2-2001',
                'nama_akun' => 'Hutang Usaha',
                'tipe_akun' => 'Pasiva',
                'saldo' => 15000000.00,
                'nama' => 'Hutang Usaha',
                'deskripsi' => 'Hutang kepada supplier'
            ],
            [
                'kode' => '3001',
                'nomor_akun' => '3-3001',
                'nama_akun' => 'Modal Pemilik',
                'tipe_akun' => 'Ekuitas',
                'saldo' => 75000000.00,
                'nama' => 'Modal Pemilik',
                'deskripsi' => 'Modal yang disetor pemilik'
            ],
            [
                'kode' => '4001',
                'nomor_akun' => '4-4001',
                'nama_akun' => 'Pendapatan Jasa',
                'tipe_akun' => 'Pendapatan',
                'saldo' => 0.00,
                'nama' => 'Pendapatan Jasa',
                'deskripsi' => 'Pendapatan dari jasa yang diberikan'
            ],
            [
                'kode' => '5001',
                'nomor_akun' => '5-5001',
                'nama_akun' => 'Beban Operasional',
                'tipe_akun' => 'Beban',
                'saldo' => 0.00,
                'nama' => 'Beban Operasional',
                'deskripsi' => 'Beban untuk operasional sehari-hari'
            ]
        ];

        foreach ($kodeNomors as $kodeNomor) {
            KodeNomor::create($kodeNomor);
        }
    }
}
