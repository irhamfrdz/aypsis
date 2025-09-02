<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Karyawan;
use Illuminate\Support\Facades\Hash;

class KaryawanUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data karyawan dan user
        $data = [
            [
                'nama_lengkap' => 'Administrator',
                'pekerjaan' => 'Admin',
                'nik' => 'ADM001',
                'nama_panggilan' => 'Admin',
                'username' => 'admin',
                'name' => 'Administrator',
            ],
            [
                'nama_lengkap' => 'Staff Operasional',
                'pekerjaan' => 'Staff',
                'nik' => 'STF001',
                'nama_panggilan' => 'Staff',
                'username' => 'staff',
                'name' => 'Staff Operasional',
            ],
            [
                'nama_lengkap' => 'Supir Truck',
                'pekerjaan' => 'Supir Truck',
                'nik' => 'SUP001',
                'nama_panggilan' => 'Supir',
                'username' => 'supirtruck',
                'name' => 'Supir Truck',
            ],
        ];

        foreach ($data as $item) {
            $karyawan = Karyawan::updateOrCreate(
                ['nik' => $item['nik']],
                [
                    'nama_lengkap' => $item['nama_lengkap'],
                    'pekerjaan' => $item['pekerjaan'],
                    'nama_panggilan' => $item['nama_panggilan'],
                ]
            );

            User::updateOrCreate(
                ['username' => $item['username']],
                [
                    'name' => $item['name'],
                    'password' => Hash::make('password'),
                    'karyawan_id' => $karyawan->id,
                ]
            );
        }
    }
}
