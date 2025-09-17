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
            UserSeeder::class, // Pastikan user dibuat sebelum relasi permission
            UserPermissionSeeder::class, // Seeder untuk user permissions
            KontainerSeeder::class,
            TujuanSeeder::class,
            DivisiSeeder::class, // Seeder untuk master divisi
        ]);
    }
}
