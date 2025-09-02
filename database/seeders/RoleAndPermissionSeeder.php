<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds to create roles, permissions, and default users.
     */
    public function run(): void
    {
        // Truncate permissions table and reset foreign key checks in a driver-aware way
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = OFF;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        DB::table('permissions')->truncate();
        if (DB::getDriverName() === 'sqlite') {
            DB::statement('PRAGMA foreign_keys = ON;');
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }
        // Insert permissions with explicit id
        $permissions = [
            ['id' => 1, 'name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
            ['id' => 2, 'name' => 'master-user', 'description' => 'Akses Master User'],
            ['id' => 3, 'name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
            ['id' => 4, 'name' => 'master-permohonan', 'description' => 'Akses Master Permohonan'],
            ['id' => 5, 'name' => 'permohonan-create', 'description' => 'Membuat Permohonan'],
            ['id' => 6, 'name' => 'permohonan-view', 'description' => 'Melihat Permohonan'],
            ['id' => 7, 'name' => 'permohonan-edit', 'description' => 'Mengedit Permohonan'],
            ['id' => 8, 'name' => 'permohonan-delete', 'description' => 'Menghapus Permohonan'],
            ['id' => 9, 'name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
            ['id' => 10, 'name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
            ['id' => 11, 'name' => 'master-permission', 'description' => 'Akses Master Izin'],
            ['id' => 12, 'name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
            ['id' => 13, 'name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
            ['id' => 14, 'name' => 'master-pranota-supir', 'description' => 'Akses Master Pranota Supir'],
        ];
        foreach ($permissions as $permission) {
            Permission::updateOrInsert(['id' => $permission['id']], $permission);
        }
        // 1. Buat semua permission yang dibutuhkan
        $permissions = [
            ['name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
            ['name' => 'master-user', 'description' => 'Akses Master User'],
            ['name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
            ['name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
            ['name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
            ['name' => 'master-permission', 'description' => 'Akses Master Izin'],
            ['name' => 'master-pranota-supir', 'description' => 'Akses Master Pranota Supir'],
            ['name' => 'master-pembayaran-pranota-supir', 'description' => 'Akses Pembayaran Pranota Supir'],
            ['name' => 'master-permohonan', 'description' => 'Akses Master Permohonan'],
            ['name' => 'permohonan-create', 'description' => 'Membuat Permohonan'],
            ['name' => 'permohonan-view', 'description' => 'Melihat Permohonan'],
            ['name' => 'permohonan-edit', 'description' => 'Mengedit Permohonan'],
            ['name' => 'permohonan-delete', 'description' => 'Menghapus Permohonan'],
            ['name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
            ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
        ];
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }

        // 2. Buat Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'description' => 'Administrator Sistem']);
    $staffRole = Role::firstOrCreate(['name' => 'staff', 'description' => 'Role untuk staff operasional']);
    Role::firstOrCreate(['name' => 'supir', 'description' => 'Role untuk supir']);
    // Pastikan admin role memiliki semua permission
    $adminRole->permissions()->sync(Permission::all()->pluck('id'));

        // 3. Buat User Admin, berikan role, dan semua permission
        $adminUser = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'karyawan_id' => 1, // Mengasumsikan KaryawanSeeder sudah jalan
                'password' => Hash::make('password'), // Ganti dengan password yang aman
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);
        $adminUser->permissions()->sync(Permission::all()->pluck('id'));

        // 4. Buat User Staff, berikan role, dan permission yang relevan
        $staffUser = User::firstOrCreate(
            ['username' => 'staff'],
            [
                'name' => 'Staff Operasional',
                'karyawan_id' => 2, // Mengasumsikan KaryawanSeeder sudah jalan
                'password' => Hash::make('password'),
            ]
        );
        $staffUser->roles()->sync([$staffRole->id]);
        $staffPermissions = Permission::whereIn('name', [
            'permohonan-create',
            'permohonan-view',
            'master-permohonan',
            'master-pranota-supir'
        ])->pluck('id');
        $staffUser->permissions()->sync($staffPermissions);
    }
}
