<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder ini untuk memberikan semua permissions ke user admin
     *
     * @return void
     */
    public function run(): void
    {
        // Cari user admin (ID 1 atau username 'admin')
        $admin = User::where('id', 1)
                    ->orWhere('username', 'admin')
                    ->first();

        if (!$admin) {
            $this->command->warn('⚠️  User admin tidak ditemukan!');
            $this->command->info('💡 Pastikan KaryawanSeeder dan UserSeeder sudah dijalankan terlebih dahulu.');
            return;
        }

        // Ambil semua permission IDs
        $allPermissionIds = Permission::pluck('id')->toArray();

        if (empty($allPermissionIds)) {
            $this->command->warn('⚠️  Tidak ada permissions di database!');
            $this->command->info('💡 Jalankan PermissionSeederComprehensive terlebih dahulu.');
            return;
        }

        // Attach semua permissions ke admin (sync akan replace existing permissions)
        $admin->permissions()->sync($allPermissionIds);

        $this->command->info('✅ User admin (' . $admin->username . ') telah diberi ' . count($allPermissionIds) . ' permissions!');
        $this->command->info('📧 Email: ' . ($admin->karyawan->email ?? 'N/A'));
        $this->command->info('👤 Nama: ' . ($admin->karyawan->nama_lengkap ?? 'N/A'));
    }
}
