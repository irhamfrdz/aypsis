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
            ['id' => 1, 'nik' => 'ADM001', 'nama_panggilan' => 'Admin', 'nama_lengkap' => 'Administrator Utama', 'email' => 'admin@aypsis.com', 'no_hp' => '(+62) 439 4465 613', 'divisi' => 'IT', 'pekerjaan' => 'Administrator', 'tanggal_masuk' => '2020-08-15', 'plat' => null],
            ['id' => 2, 'nik' => 'STF001', 'nama_panggilan' => 'Staff', 'nama_lengkap' => 'Staff Operasional', 'email' => 'staff@aypsis.com', 'no_hp' => '0582 9516 8021', 'divisi' => 'Operasional', 'pekerjaan' => 'Staff', 'tanggal_masuk' => '2024-08-15', 'plat' => null],
            ['id' => 3, 'nik' => 'SUP001', 'nama_panggilan' => 'Emong', 'nama_lengkap' => 'Joko Mahendra', 'plat' => 'B 4196 AX', 'no_hp' => '0662 1198 6049', 'divisi' => 'Transportasi', 'pekerjaan' => 'Supir Truck', 'tanggal_masuk' => '2023-12-14', 'email' => null],
            ['id' => 4, 'nik' => 'SUP002', 'nama_panggilan' => 'Jono', 'nama_lengkap' => 'Darijan Jaga Utama', 'plat' => 'B 6622 NF', 'no_hp' => '0380 1838 523', 'divisi' => 'Transportasi', 'pekerjaan' => 'Supir Truck', 'tanggal_masuk' => '2016-10-21', 'email' => null],
            ['id' => 5, 'nik' => 'SUP003', 'nama_panggilan' => 'Gandi', 'nama_lengkap' => 'Uda Wahyudin', 'plat' => 'B 5214 TI', 'no_hp' => '0399 1431 2050', 'divisi' => 'Transportasi', 'pekerjaan' => 'Supir Truck', 'tanggal_masuk' => '2018-06-16', 'email' => null],
        ]);
    }
}
