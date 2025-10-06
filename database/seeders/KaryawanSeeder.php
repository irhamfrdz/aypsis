<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Karyawan;
use Illuminate\Support\Facades\DB;


class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // disable foreign keys during truncate in a driver-aware way
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        DB::table('karyawans')->truncate();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        Karyawan::insert([
            [
                'id' => 1,
                'nik' => 'ADM001',
                'nama_panggilan' => 'Admin',
                'nama_lengkap' => 'Administrator Utama',
                'email' => 'admin@aypsis.com',
                'no_hp' => '081234567890',
                'divisi' => 'IT',
                'pekerjaan' => 'Administrator',
                'tanggal_masuk' => '2020-01-01',
                'plat' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'nik' => 'STF001',
                'nama_panggilan' => 'Budi',
                'nama_lengkap' => 'Budi Santoso',
                'email' => 'budi@aypsis.com',
                'no_hp' => '081234567891',
                'divisi' => 'Operasional',
                'pekerjaan' => 'Staff',
                'tanggal_masuk' => '2021-03-15',
                'plat' => null,
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'nik' => 'SUP001',
                'nama_panggilan' => 'Agus',
                'nama_lengkap' => 'Agus Setiawan',
                'email' => 'agus@aypsis.com',
                'no_hp' => '081234567892',
                'divisi' => 'Transportasi',
                'pekerjaan' => 'Supir',
                'tanggal_masuk' => '2022-06-10',
                'plat' => 'B 1234 ABC',
                'status' => 'aktif',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
