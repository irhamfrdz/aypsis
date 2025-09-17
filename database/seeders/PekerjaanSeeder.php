<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pekerjaan;

class PekerjaanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pekerjaans = [
            [
                'nama_pekerjaan' => 'NAHKODA',
                'kode_pekerjaan' => 'ABK001',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MUALIM I',
                'kode_pekerjaan' => 'ABK002',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MUALIM II',
                'kode_pekerjaan' => 'ABK003',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MUALIM III',
                'kode_pekerjaan' => 'ABK004',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'KKM',
                'kode_pekerjaan' => 'ABK005',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'KKM ATT III',
                'kode_pekerjaan' => 'ABK006',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MASINIS I',
                'kode_pekerjaan' => 'ABK007',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MASINIS II',
                'kode_pekerjaan' => 'ABK008',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MASINIS III',
                'kode_pekerjaan' => 'ABK009',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MASINIS IV',
                'kode_pekerjaan' => 'ABK010',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'JURU MUDI',
                'kode_pekerjaan' => 'ABK011',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'JURU MINYAK',
                'kode_pekerjaan' => 'ABK012',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'JURU MASAK',
                'kode_pekerjaan' => 'ABK013',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'BOSUN',
                'kode_pekerjaan' => 'ABK014',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'CADET DECK',
                'kode_pekerjaan' => 'ABK015',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'CADET ENGINEER',
                'kode_pekerjaan' => 'ABK016',
                'divisi' => 'ABK',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'AKUNTING',
                'kode_pekerjaan' => 'ADM001',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'HRD',
                'kode_pekerjaan' => 'ADM002',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'KASIR',
                'kode_pekerjaan' => 'ADM003',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'KEUANGAN',
                'kode_pekerjaan' => 'ADM004',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'KONTAINER',
                'kode_pekerjaan' => 'ADM005',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'MARKETING',
                'kode_pekerjaan' => 'ADM006',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'PAJAK',
                'kode_pekerjaan' => 'ADM007',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'PENAGIHAN',
                'kode_pekerjaan' => 'ADM008',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'IT',
                'kode_pekerjaan' => 'ADM009',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'SEKRETARIS',
                'kode_pekerjaan' => 'ADM010',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'TRUCKING',
                'kode_pekerjaan' => 'ADM011',
                'divisi' => 'ADMINISTRASI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'DIREKSI UTAMA',
                'kode_pekerjaan' => 'DIR001',
                'divisi' => 'DIREKSI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'DIREKTUR',
                'kode_pekerjaan' => 'DIR002',
                'divisi' => 'DIREKSI',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'PEMBELIAN',
                'kode_pekerjaan' => 'DIR003',
                'divisi' => 'DIREKSI',
                'is_active' => true,
            ],
        ];

        foreach ($pekerjaans as $pekerjaan) {
            Pekerjaan::firstOrCreate(
                ['kode_pekerjaan' => $pekerjaan['kode_pekerjaan']],
                $pekerjaan
            );
        }

        $this->command->info('Pekerjaan data seeded successfully');
    }
}
