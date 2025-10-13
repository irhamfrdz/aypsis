<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MasterTujuanKirim;

class MasterTujuanKirimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tujuanKirim = [
            [
                'kode' => 'JKT',
                'nama_tujuan' => 'Jakarta',
                'catatan' => 'Ibu kota Indonesia, pusat bisnis dan pemerintahan',
                'status' => 'active'
            ],
            [
                'kode' => 'BDG',
                'nama_tujuan' => 'Bandung',
                'catatan' => 'Kota metropolitan di Jawa Barat, pusat fashion dan kuliner',
                'status' => 'active'
            ],
            [
                'kode' => 'SBY',
                'nama_tujuan' => 'Surabaya',
                'catatan' => 'Kota terbesar kedua di Indonesia, pusat perdagangan di Jawa Timur',
                'status' => 'active'
            ],
            [
                'kode' => 'YOG',
                'nama_tujuan' => 'Yogyakarta',
                'catatan' => 'Kota budaya dan pendidikan, terkenal dengan candi-candi bersejarah',
                'status' => 'active'
            ],
            [
                'kode' => 'MLG',
                'nama_tujuan' => 'Malang',
                'catatan' => 'Kota pendidikan dan wisata di Jawa Timur',
                'status' => 'active'
            ],
            [
                'kode' => 'SMG',
                'nama_tujuan' => 'Semarang',
                'catatan' => 'Ibukota Jawa Tengah, kota pelabuhan strategis',
                'status' => 'active'
            ],
            [
                'kode' => 'MDN',
                'nama_tujuan' => 'Medan',
                'catatan' => 'Ibukota Sumatera Utara, pusat perdagangan di Sumatera',
                'status' => 'active'
            ],
            [
                'kode' => 'MKS',
                'nama_tujuan' => 'Makassar',
                'catatan' => 'Ibukota Sulawesi Selatan, gerbang utama ke Indonesia Timur',
                'status' => 'active'
            ],
            [
                'kode' => 'DPS',
                'nama_tujuan' => 'Denpasar',
                'catatan' => 'Ibukota Bali, pusat pariwisata internasional',
                'status' => 'active'
            ],
            [
                'kode' => 'PDG',
                'nama_tujuan' => 'Padang',
                'catatan' => 'Ibukota Sumatera Barat, terkenal dengan kuliner Padang',
                'status' => 'active'
            ]
        ];

        foreach ($tujuanKirim as $tujuan) {
            MasterTujuanKirim::create($tujuan);
        }
    }
}
