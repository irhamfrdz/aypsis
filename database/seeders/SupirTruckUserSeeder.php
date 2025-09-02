<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\Karyawan;

class SupirTruckUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat karyawan supir truck jika belum ada
        $karyawan = Karyawan::firstOrCreate([
            'nama_lengkap' => 'Supir Truck',
            'pekerjaan' => 'Supir Truck',
        ], [
            // Provide minimal required fields to satisfy DB constraints
            'nik' => 'SUPTRUCK001',
            'nama_panggilan' => 'SupirTruck',
            'email' => null,
        ]);

        // Buat user supir truck jika belum ada
        User::firstOrCreate([
            'username' => 'supirtruck'], [
            'name' => 'Supir Truck',
            'password' => Hash::make('password'),
            'karyawan_id' => $karyawan->id,
        ]);
    }
}
