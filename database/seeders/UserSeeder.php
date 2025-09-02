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
            ['id' => 1, 'name' => 'Administrator', 'username' => 'admin', 'password' => Hash::make('password'), 'karyawan_id' => 1],
            ['id' => 2, 'name' => 'Staff Operasional', 'username' => 'staff', 'password' => Hash::make('password'), 'karyawan_id' => 2],
        ]);
    }
}
