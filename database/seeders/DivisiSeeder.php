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
                'nama_divisi' => 'ABK',
                'kode_divisi' => 'ABK',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'ADMINISTRASI',
                'kode_divisi' => 'ADM',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'DIREKSI',
                'kode_divisi' => 'DIR',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'KRANI',
                'kode_divisi' => 'KRN',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'LAPANGAN',
                'kode_divisi' => 'LAP',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'PORT',
                'kode_divisi' => 'PRT',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'SUPIR',
                'kode_divisi' => 'SPR',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'NON KARYAWAN',
                'kode_divisi' => 'NKR',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'SATPAM',
                'kode_divisi' => 'STP',
                'deskripsi' => null,
                'is_active' => true,
            ],
            [
                'nama_divisi' => 'SUPIR',
                'kode_divisi' => 'SPR',
                'deskripsi' => null,
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
