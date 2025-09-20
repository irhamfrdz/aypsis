<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $this->call([
            KaryawanSeeder::class, // WAJIB dijalankan pertama
            RoleAndPermissionSeeder::class, // Seeder yang sudah dikonsolidasi
            PermissionsFromRoutesSeeder::class,
            AllPermissionsSeeder::class, // Ensures explicit + route-derived permissions exist
            MasterDivisiPermissionSeeder::class, // Permission untuk master divisi
            MasterPekerjaanPermissionSeeder::class, // Permission untuk master pekerjaan
            KodeNomorPermissionSeeder::class, // Permission untuk master kode nomor
            UserSeeder::class, // Pastikan user dibuat sebelum relasi permission
            UserPermissionSeeder::class, // Seeder untuk user permissions
            UserAdminSeeder::class, // Seeder untuk user_admin dengan semua permission
            KontainerSeeder::class,
            TujuanSeeder::class,
            DivisiSeeder::class, // Seeder untuk master divisi
            PekerjaanSeeder::class, // Seeder untuk master pekerjaan
            BankSeeder::class, // Seeder untuk master bank
            TagihanCatPermissionsSeeder::class, // Permission untuk tagihan CAT
        ]);
    }
}
