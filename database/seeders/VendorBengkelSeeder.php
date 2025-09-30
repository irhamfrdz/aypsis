<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VendorBengkelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();
        $adminUserId = DB::table('users')->where('username', 'admin')->value('id') ?? 1;

        $vendors = [
            [
                'nama_bengkel' => 'AYP Cat Service',
                'keterangan' => 'Bengkel cat kontainer terpercaya dengan pengalaman lebih dari 10 tahun',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_bengkel' => 'Zona Container Painting',
                'keterangan' => 'Spesialis pelayanan cat kontainer dengan harga kompetitif',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_bengkel' => 'PT. Container Repair Indonesia',
                'keterangan' => 'Perusahaan reparasi kontainer dengan teknologi modern',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_bengkel' => 'Bengkel Kontainer Maju Jaya',
                'keterangan' => 'Bengkel reparasi kontainer dengan layanan 24 jam',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_bengkel' => 'CV. Container Maintenance Pro',
                'keterangan' => 'Spesialis maintenance dan perbaikan kontainer',
                'created_by' => $adminUserId,
                'updated_by' => $adminUserId,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($vendors as $vendor) {
            DB::table('vendor_bengkel')->updateOrInsert(
                ['nama_bengkel' => $vendor['nama_bengkel']],
                $vendor
            );
        }
    }
}
