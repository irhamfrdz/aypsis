<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SupirUserSeeder extends Seeder
{
    public function run()
    {
        User::firstOrCreate([
            'username' => 'supirtruck'
        ], [
            'name' => 'Supir Truck',
            'password' => Hash::make('supir123'),
            'karyawan_id' => null,
        ]);
    }
}
