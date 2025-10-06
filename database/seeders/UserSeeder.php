<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        DB::table('users')->truncate();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        User::insert([
            [
                'id' => 1,
                'username' => 'admin',
                'password' => Hash::make('admin123'),
                'karyawan_id' => 1,
                'role' => 'admin',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'username' => 'budi',
                'password' => Hash::make('budi123'),
                'karyawan_id' => 2,
                'role' => 'user',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'username' => 'agus',
                'password' => Hash::make('agus123'),
                'karyawan_id' => 3,
                'role' => 'supir',
                'status' => 'approved',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Assign all permissions to admin user
        $adminUser = User::find(1);
        if ($adminUser) {
            $allPermissions = \App\Models\Permission::pluck('id')->toArray();
            $adminUser->permissions()->sync($allPermissions);
        }

        echo "Users seeded successfully!\n";
        echo "Admin credentials: username=admin, password=admin123\n";
        echo "Budi credentials: username=budi, password=budi123\n";
        echo "Agus credentials: username=agus, password=agus123\n";
    }
}
