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
            // IT Division
            [
                'nama_pekerjaan' => 'IT Manager',
                'kode_pekerjaan' => 'IT001',
                'divisi' => 'IT',
                'deskripsi' => 'Manajer departemen IT',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Software Developer',
                'kode_pekerjaan' => 'IT002',
                'divisi' => 'IT',
                'deskripsi' => 'Pengembang perangkat lunak',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'System Administrator',
                'kode_pekerjaan' => 'IT003',
                'divisi' => 'IT',
                'deskripsi' => 'Administrator sistem',
                'is_active' => true,
            ],

            // Finance Division
            [
                'nama_pekerjaan' => 'Finance Manager',
                'kode_pekerjaan' => 'FIN001',
                'divisi' => 'FIN',
                'deskripsi' => 'Manajer departemen keuangan',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Accountant',
                'kode_pekerjaan' => 'FIN002',
                'divisi' => 'FIN',
                'deskripsi' => 'Akuntan',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Finance Staff',
                'kode_pekerjaan' => 'FIN003',
                'divisi' => 'FIN',
                'deskripsi' => 'Staf keuangan',
                'is_active' => true,
            ],

            // Operations Division
            [
                'nama_pekerjaan' => 'Operations Manager',
                'kode_pekerjaan' => 'OPS001',
                'divisi' => 'OPS',
                'deskripsi' => 'Manajer departemen operasional',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Operations Supervisor',
                'kode_pekerjaan' => 'OPS002',
                'divisi' => 'OPS',
                'deskripsi' => 'Supervisor operasional',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Operations Staff',
                'kode_pekerjaan' => 'OPS003',
                'divisi' => 'OPS',
                'deskripsi' => 'Staf operasional',
                'is_active' => true,
            ],

            // Human Resources Division
            [
                'nama_pekerjaan' => 'HR Manager',
                'kode_pekerjaan' => 'HR001',
                'divisi' => 'HR',
                'deskripsi' => 'Manajer departemen HR',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'HR Staff',
                'kode_pekerjaan' => 'HR002',
                'divisi' => 'HR',
                'deskripsi' => 'Staf HR',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Recruitment Officer',
                'kode_pekerjaan' => 'HR003',
                'divisi' => 'HR',
                'deskripsi' => 'Petugas rekrutmen',
                'is_active' => true,
            ],

            // ABK (Anak Buah Kapal) Division
            [
                'nama_pekerjaan' => 'Nahkoda',
                'kode_pekerjaan' => 'ABK001',
                'divisi' => 'ABK',
                'deskripsi' => 'Kapten kapal',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Mualim I',
                'kode_pekerjaan' => 'ABK002',
                'divisi' => 'ABK',
                'deskripsi' => 'Mualim tingkat I',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Mualim II',
                'kode_pekerjaan' => 'ABK003',
                'divisi' => 'ABK',
                'deskripsi' => 'Mualim tingkat II',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Masinis I',
                'kode_pekerjaan' => 'ABK004',
                'divisi' => 'ABK',
                'deskripsi' => 'Masinis tingkat I',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Masinis II',
                'kode_pekerjaan' => 'ABK005',
                'divisi' => 'ABK',
                'deskripsi' => 'Masinis tingkat II',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Juru Mudi',
                'kode_pekerjaan' => 'ABK006',
                'divisi' => 'ABK',
                'deskripsi' => 'Juru mudi kapal',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Bosun',
                'kode_pekerjaan' => 'ABK007',
                'divisi' => 'ABK',
                'deskripsi' => 'Bosun kapal',
                'is_active' => true,
            ],

            // Admin Division
            [
                'nama_pekerjaan' => 'Admin Manager',
                'kode_pekerjaan' => 'ADM001',
                'divisi' => 'ADM',
                'deskripsi' => 'Manajer administrasi',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Admin Staff',
                'kode_pekerjaan' => 'ADM002',
                'divisi' => 'ADM',
                'deskripsi' => 'Staf administrasi',
                'is_active' => true,
            ],
            [
                'nama_pekerjaan' => 'Document Officer',
                'kode_pekerjaan' => 'ADM003',
                'divisi' => 'ADM',
                'deskripsi' => 'Petugas dokumentasi',
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
