<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseCleanerSeeder extends Seeder
{
    /**
     * Clean old/unnecessary data from server database.
     */
    public function run(): void
    {
        $this->command->info('🧹 Cleaning old data from server database...');

        // Clean old permissions that are not in laptop database
        $oldPermissions = [
            'master-karyawan', 'master-user', 'master-kontainer', 'master-permohonan',
            'permohonan-create', 'permohonan-view', 'permohonan-edit', 'permohonan-delete',
            'master-tujuan', 'master-kegiatan', 'master-permission', 'master-mobil',
            'master-pricelist-sewa-kontainer', 'master-pranota-supir', 'master-pembayaran-pranota-supir',
            'login', 'logout', 'tagihan-kontainer-sewa.group.show', 'tagihan-kontainer-sewa.adjust_price',
            'tagihan-kontainer-sewa.search_by_kontainer', 'tagihan-kontainer-sewa.group.adjust_price',
            'tagihan-kontainer-sewa.history', 'tagihan-kontainer-sewa.rollover',
            'tagihan-kontainer-sewa.export', 'tagihan-kontainer-sewa.template',
            'tagihan-kontainer-sewa.import', 'tagihan-kontainer-sewa.index',
            'tagihan-kontainer-sewa.create', 'tagihan-kontainer-sewa.store',
            'tagihan-kontainer-sewa.show', 'tagihan-kontainer-sewa.edit',
            'tagihan-kontainer-sewa.update', 'tagihan-kontainer-sewa.destroy',
            'dashboard', 'master.karyawan.print', 'master.karyawan.print.single',
            'master.karyawan.import', 'master.karyawan.import.store', 'master.karyawan.export',
            'master.karyawan.index', 'master.karyawan.create', 'master.karyawan.store',
            'master.karyawan.show', 'master.karyawan.edit', 'master.karyawan.update',
            'master.karyawan.destroy', 'master.user.index', 'master.user.create',
            'master.user.store', 'master.user.show', 'master.user.edit', 'master.user.update',
            'master.user.destroy', 'master.kontainer.index', 'master.kontainer.create',
            'master.kontainer.store', 'master.kontainer.show', 'master.kontainer.edit',
            'master.kontainer.update', 'master.kontainer.destroy', 'master.tujuan.index',
            'master.tujuan.create', 'master.tujuan.store', 'master.tujuan.show',
            'master.tujuan.edit', 'master.tujuan.update', 'master.tujuan.destroy',
            'master.kegiatan.index', 'master.kegiatan.create', 'master.kegiatan.store',
            'master.kegiatan.show', 'master.kegiatan.edit', 'master.kegiatan.update',
            'master.kegiatan.destroy', 'master.kegiatan.template', 'master.kegiatan.import',
            'master.permission.index', 'master.permission.create', 'master.permission.store',
            'master.permission.show', 'master.permission.edit', 'master.permission.update',
            'master.permission.destroy', 'master.mobil.index', 'master.mobil.create',
            'master.mobil.store', 'master.mobil.show', 'master.mobil.edit',
            'master.mobil.update', 'master.mobil.destroy', 'master.pricelist-sewa-kontainer.index',
            'master.pricelist-sewa-kontainer.create', 'master.pricelist-sewa-kontainer.store',
            'master.pricelist-sewa-kontainer.show', 'master.pricelist-sewa-kontainer.edit',
            'master.pricelist-sewa-kontainer.update', 'master.pricelist-sewa-kontainer.destroy',
            'permohonan.export', 'permohonan.import', 'permohonan.index',
            'permohonan.create', 'permohonan.store', 'permohonan.show',
            'permohonan.edit', 'permohonan.update', 'permohonan.destroy',
            'pranota-supir.index', 'pranota-supir.create', 'pranota-supir.print',
            'pranota-supir.show', 'pranota-supir.store', 'pranota-tagihan-kontainer.store',
            'pranota-tagihan-kontainer.destroy', 'pembayaran-pranota-tagihan-kontainer.index',
            'pembayaran-pranota-tagihan-kontainer.export', 'pembayaran-pranota-tagihan-kontainer.import',
            'pembayaran-pranota-tagihan-kontainer.create', 'pembayaran-pranota-tagihan-kontainer.store',
            'pembayaran-pranota-tagihan-kontainer.history', 'pembayaran-pranota-supir.index',
            'pembayaran-pranota-supir.print', 'pembayaran-pranota-supir.create',
            'pembayaran-pranota-supir.store', 'supir.dashboard', 'supir.checkpoint.create',
            'supir.checkpoint.store', 'approval.dashboard', 'approval.mass_process',
            'approval.create', 'approval.store', 'admin.features', 'storage.local',
            'master-pranota-tagihan-kontainer', 'admin.debug.perms', 'master-pranota'
        ];

        $deletedPermissions = DB::table('permissions')
            ->whereIn('name', $oldPermissions)
            ->delete();

        $this->command->info("Deleted {$deletedPermissions} old permissions");

        // Clean user_permissions for deleted permissions
        $deletedUserPermissions = DB::table('user_permissions')
            ->whereNotIn('permission_id', function($query) {
                $query->select('id')->from('permissions');
            })
            ->delete();

        $this->command->info("Deleted {$deletedUserPermissions} orphaned user_permissions");

        // Clean users that don't exist in laptop database
        $usersToKeep = [1, 2, 3, 4, 5, 10, 15]; // Based on laptop database
        $deletedUsers = DB::table('users')
            ->whereNotIn('id', $usersToKeep)
            ->delete();

        $this->command->info("Deleted {$deletedUsers} users not in laptop database");

        $this->command->info('✅ Database cleaning completed!');
    }
}
