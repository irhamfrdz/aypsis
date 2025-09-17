<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Divisi;

class DivisiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $divisis = [
            [
                'nama_divisi' => 'IT (Information Technology)',
                'kode_divisi' => 'IT',
                'deskripsi' => 'Divisi Teknologi Informasi yang mengelola sistem IT, pengembangan software, dan infrastruktur teknologi',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Finance',
                'kode_divisi' => 'FIN',
                'deskripsi' => 'Divisi Keuangan yang mengelola keuangan perusahaan, akuntansi, dan pelaporan keuangan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Operations',
                'kode_divisi' => 'OPS',
                'deskripsi' => 'Divisi Operasional yang mengelola operasi harian perusahaan dan koordinasi kegiatan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Human Resources',
                'kode_divisi' => 'HR',
                'deskripsi' => 'Divisi Sumber Daya Manusia yang mengelola rekrutmen, pengembangan karyawan, dan administrasi personalia',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'ABK (Anak Buah Kapal)',
                'kode_divisi' => 'ABK',
                'deskripsi' => 'Divisi Awak Kapal yang mengelola kru kapal dan operasi pelayaran',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Admin',
                'kode_divisi' => 'ADM',
                'deskripsi' => 'Divisi Administrasi yang mengelola administrasi umum dan dokumentasi perusahaan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Marketing',
                'kode_divisi' => 'MKT',
                'deskripsi' => 'Divisi Pemasaran yang mengelola promosi, penjualan, dan hubungan dengan pelanggan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Procurement',
                'kode_divisi' => 'PRC',
                'deskripsi' => 'Divisi Pengadaan yang mengelola pembelian, supplier, dan rantai pasok',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Quality Control',
                'kode_divisi' => 'QC',
                'deskripsi' => 'Divisi Pengendalian Kualitas yang memastikan standar kualitas produk dan layanan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Maintenance',
                'kode_divisi' => 'MNT',
                'deskripsi' => 'Divisi Pemeliharaan yang mengelola perawatan dan perbaikan aset perusahaan',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Legal',
                'kode_divisi' => 'LEG',
                'deskripsi' => 'Divisi Hukum yang mengelola aspek legal perusahaan dan kontrak',
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'Security',
                'kode_divisi' => 'SEC',
                'deskripsi' => 'Divisi Keamanan yang mengelola keamanan perusahaan dan aset',
                'is_active' => true,
            ],
        ];

        $this->command->info('Seeding master divisi data...');

        $created = 0;
        $skipped = 0;

        foreach ($divisis as $divisi) {
            $existing = Divisi::where('kode_divisi', $divisi['kode_divisi'])
                             ->orWhere('nama_divisi', $divisi['nama_divisi'])
                             ->first();

            if ($existing) {
                $this->command->info("Skipped: {$divisi['nama_divisi']} (already exists)");
                $skipped++;
                continue;
            }

            Divisi::create($divisi);
            $this->command->info("Created: {$divisi['nama_divisi']}");
            $created++;
        }

        $this->command->info("Divisi seeding completed!");
        $this->command->info("Created: {$created} divisis");
        $this->command->info("Skipped: {$skipped} divisis (already exist)");

        // Tampilkan summary
        $totalDivisis = Divisi::count();
        $this->command->info("Total divisis in database: {$totalDivisis}");
    }
}
