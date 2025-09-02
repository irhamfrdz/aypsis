<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KontainerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $rows = [
            [
                'awalan_kontainer' => 'ABCD',
                'nomor_seri_kontainer' => '123456',
                'akhiran_kontainer' => '7',
                'nomor_seri_gabungan' => 'ABCD1234567',
                'ukuran' => '20',
                'tipe_kontainer' => 'DRY',
                'status' => 'Tersedia',
                'pemilik_kontainer' => 'PT. Contoh',
                'tahun_pembuatan' => '2020',
                'kontainer_asal' => 'Jakarta',
                'tanggal_beli' => '2022-01-10',
                'tanggal_jual' => null,
                'kondisi_kontainer' => 'Baik',
                'tanggal_masuk_sewa' => null,
                'tanggal_selesai_sewa' => null,
                'keterangan' => 'Kontainer baru',
                'keterangan1' => 'Tidak ada catatan tambahan',
                'keterangan2' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'awalan_kontainer' => 'EFGH',
                'nomor_seri_kontainer' => '654321',
                'akhiran_kontainer' => '3',
                'nomor_seri_gabungan' => 'EFGH6543213',
                'ukuran' => '40',
                'tipe_kontainer' => 'Reefer',
                'status' => 'Disewa',
                'pemilik_kontainer' => 'PT. Sewa',
                'tahun_pembuatan' => '2018',
                'kontainer_asal' => 'Surabaya',
                'tanggal_beli' => '2019-05-15',
                'tanggal_jual' => null,
                'kondisi_kontainer' => 'Perlu perbaikan',
                'tanggal_masuk_sewa' => '2023-02-01',
                'tanggal_selesai_sewa' => '2023-12-31',
                'keterangan' => 'Disewa oleh PT. Sewa',
                'keterangan1' => null,
                'keterangan2' => 'Catatan teknis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($rows as $row) {
            DB::table('kontainers')->updateOrInsert(
                ['nomor_seri_gabungan' => $row['nomor_seri_gabungan']],
                $row
            );
        }
    }
}
