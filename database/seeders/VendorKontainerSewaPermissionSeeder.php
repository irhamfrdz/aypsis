<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorKontainerSewaPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Seeder untuk menambahkan permissions Vendor Kontainer Sewa
     *
     * Usage: php artisan db:seed --class=VendorKontainerSewaPermissionSeeder
     */
    public function run(): void
    {
        $now = Carbon::now();

        // Permissions yang akan dibuat untuk Vendor Kontainer Sewa
        $permissions = [
            [
                'name' => 'vendor-kontainer-sewa-view',
                'description' => 'Melihat data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'vendor-kontainer-sewa-create',
                'description' => 'Menambah data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'vendor-kontainer-sewa-edit',
                'description' => 'Mengedit data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'vendor-kontainer-sewa-delete',
                'description' => 'Menghapus data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'vendor-kontainer-sewa-export',
                'description' => 'Export data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'vendor-kontainer-sewa-print',
                'description' => 'Print data vendor kontainer sewa',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        // Insert permissions yang belum ada
        foreach ($permissions as $permission) {
            $exists = DB::table('permissions')
                ->where('name', $permission['name'])
                ->exists();

            if (!$exists) {
                DB::table('permissions')->insert($permission);
                $this->command->info("✅ Permission '{$permission['name']}' berhasil ditambahkan");
            } else {
                // Update deskripsi jika permission sudah ada
                DB::table('permissions')
                    ->where('name', $permission['name'])
                    ->update([
                        'description' => $permission['description'],
                        'updated_at' => $now,
                    ]);
                $this->command->info("ℹ️  Permission '{$permission['name']}' sudah ada - deskripsi di-update");
            }
        }

        // Assign permissions ke user admin (jika ada)
        $this->assignPermissionsToAdmin($permissions);

        $this->command->info("🎉 Vendor Kontainer Sewa permissions seeder completed!");
    }

    /**
     * Assign permissions ke user admin
     */
    private function assignPermissionsToAdmin(array $permissions): void
    {
        // Cari user admin
        $adminUser = DB::table('users')->where('username', 'admin')->first();

        if (!$adminUser) {
            $this->command->warn("⚠️  User admin tidak ditemukan, permissions tidak di-assign");
            return;
        }

        $this->command->info("👤 User admin ditemukan: ID {$adminUser->id}");

        // Get permission IDs
        $permissionNames = array_column($permissions, 'name');
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id', 'name')
            ->toArray();

        if (empty($permissionIds)) {
            $this->command->error("❌ Tidak ada permission yang ditemukan!");
            return;
        }

        // Assign permissions ke admin
        $assignedCount = 0;
        $skippedCount = 0;

        foreach ($permissionIds as $permissionName => $permissionId) {
            $exists = DB::table('user_permissions')
                ->where('user_id', $adminUser->id)
                ->where('permission_id', $permissionId)
                ->exists();

            if (!$exists) {
                DB::table('user_permissions')->insert([
                    'user_id' => $adminUser->id,
                    'permission_id' => $permissionId,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
                $assignedCount++;
            } else {
                $skippedCount++;
            }
        }

        $this->command->info("✨ Permissions assigned ke admin: {$assignedCount} baru, {$skippedCount} sudah ada");

        // Log permissions yang berhasil di-assign
        $adminPermissions = DB::table('user_permissions')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->where('user_permissions.user_id', $adminUser->id)
            ->where('permissions.name', 'LIKE', 'vendor-kontainer-sewa-%')
            ->select('permissions.name', 'permissions.description')
            ->get();

        $this->command->info("📋 Permissions vendor kontainer sewa yang di-assign:");
        foreach ($adminPermissions as $permission) {
            $this->command->line("   - {$permission->name}: {$permission->description}");
        }

        if ($adminPermissions->count() === count($permissions)) {
            $this->command->info("🎯 Semua permissions berhasil di-assign ke admin!");
            $this->command->info("🌐 Admin sekarang dapat mengakses menu Vendor Kontainer Sewa");
        }
    }

    /**
     * Rollback permissions (untuk testing atau rollback)
     */
    public function rollback(): void
    {
        $permissionNames = [
            'vendor-kontainer-sewa-view',
            'vendor-kontainer-sewa-create',
            'vendor-kontainer-sewa-edit',
            'vendor-kontainer-sewa-delete',
            'vendor-kontainer-sewa-export',
            'vendor-kontainer-sewa-print',
        ];

        // Hapus dari user_permissions terlebih dahulu
        $permissionIds = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->pluck('id')
            ->toArray();

        if (!empty($permissionIds)) {
            DB::table('user_permissions')
                ->whereIn('permission_id', $permissionIds)
                ->delete();

            $this->command->info("🗑️  User permissions untuk vendor kontainer sewa berhasil dihapus");
        }

        // Hapus permissions
        $deletedCount = DB::table('permissions')
            ->whereIn('name', $permissionNames)
            ->delete();

        $this->command->info("🗑️  {$deletedCount} permissions vendor kontainer sewa berhasil dihapus");
        $this->command->info("✅ Rollback vendor kontainer sewa permissions completed!");
    }
}
