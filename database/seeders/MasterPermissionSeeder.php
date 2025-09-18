<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class MasterPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Auth permissions
            ['name' => 'login', 'description' => 'Login permission'],
            ['name' => 'logout', 'description' => 'Logout permission'],

            // Dashboard
            ['name' => 'dashboard', 'description' => 'Dashboard access'],

            // Permohonan permissions
            ['name' => 'permohonan.export', 'description' => 'Export permohonan'],
            ['name' => 'permohonan.import', 'description' => 'Import permohonan'],
            ['name' => 'permohonan.index', 'description' => 'View permohonan list'],
            ['name' => 'permohonan.create', 'description' => 'Create permohonan'],
            ['name' => 'permohonan.store', 'description' => 'Store permohonan'],
            ['name' => 'permohonan.show', 'description' => 'View permohonan detail'],
            ['name' => 'permohonan.edit', 'description' => 'Edit permohonan'],
            ['name' => 'permohonan.update', 'description' => 'Update permohonan'],
            ['name' => 'permohonan.destroy', 'description' => 'Delete permohonan'],
            ['name' => 'permohonan', 'description' => 'Basic permohonan access'],
            ['name' => 'permohonan.approve', 'description' => 'Approve permohonan'],
            ['name' => 'permohonan.print', 'description' => 'Print permohonan'],
            ['name' => 'permohonan.view', 'description' => 'View permohonan'],
            ['name' => 'permohonan.delete', 'description' => 'Delete permohonan'],

            // Supir permissions
            ['name' => 'supir.dashboard', 'description' => 'Supir dashboard'],
            ['name' => 'supir.checkpoint.create', 'description' => 'Create supir checkpoint'],
            ['name' => 'supir.checkpoint.store', 'description' => 'Store supir checkpoint'],
            ['name' => 'supir', 'description' => 'Supir access'],

            // Approval permissions
            ['name' => 'approval.dashboard', 'description' => 'Approval dashboard'],
            ['name' => 'approval.mass_process', 'description' => 'Mass process approval'],
            ['name' => 'approval.create', 'description' => 'Create approval'],
            ['name' => 'approval.store', 'description' => 'Store approval'],
            ['name' => 'approval.riwayat', 'description' => 'Approval history'],
            ['name' => 'approval.view', 'description' => 'View approval'],
            ['name' => 'approval.update', 'description' => 'Update approval'],
            ['name' => 'approval.delete', 'description' => 'Delete approval'],
            ['name' => 'approval.approve', 'description' => 'Approve approval'],
            ['name' => 'approval.print', 'description' => 'Print approval'],
            ['name' => 'approval.export', 'description' => 'Export approval'],
            ['name' => 'approval', 'description' => 'Basic approval access'],

            // Admin permissions
            ['name' => 'admin.features', 'description' => 'Admin features'],
            ['name' => 'admin.debug.perms', 'description' => 'Debug permissions'],

            // Storage
            ['name' => 'storage.local', 'description' => 'Local storage access'],

            // Profile permissions
            ['name' => 'profile.show', 'description' => 'Show profile'],
            ['name' => 'profile.edit', 'description' => 'Edit profile'],
            ['name' => 'profile.update.account', 'description' => 'Update account profile'],
            ['name' => 'profile.update.personal', 'description' => 'Update personal profile'],
            ['name' => 'profile.update.avatar', 'description' => 'Update avatar profile'],
            ['name' => 'profile.destroy', 'description' => 'Destroy profile'],

            // Pranota permissions
            ['name' => 'pranota.view', 'description' => 'View pranota'],
            ['name' => 'pranota.create', 'description' => 'Create pranota'],
            ['name' => 'pranota.update', 'description' => 'Update pranota'],
            ['name' => 'pranota.delete', 'description' => 'Delete pranota'],
            ['name' => 'pranota.approve', 'description' => 'Approve pranota'],
            ['name' => 'pranota.print', 'description' => 'Print pranota'],
            ['name' => 'pranota.export', 'description' => 'Export pranota'],
            ['name' => 'pranota.index', 'description' => 'View pranota list'],
            ['name' => 'pranota.show', 'description' => 'Show pranota detail'],
            ['name' => 'pranota.store', 'description' => 'Store pranota'],
            ['name' => 'pranota.bulk.store', 'description' => 'Bulk store pranota'],
            ['name' => 'pranota.update.status', 'description' => 'Update pranota status'],
            ['name' => 'pranota.destroy', 'description' => 'Destroy pranota'],
            ['name' => 'pranota', 'description' => 'Basic pranota access'],

            // Karyawan permissions
            ['name' => 'karyawan.create', 'description' => 'Create karyawan'],
            ['name' => 'karyawan.store', 'description' => 'Store karyawan'],
            ['name' => 'register.karyawan', 'description' => 'Register karyawan'],
            ['name' => 'register.karyawan.store', 'description' => 'Store register karyawan'],
            ['name' => 'master.karyawan.store', 'description' => 'Store master karyawan'],
            ['name' => 'master.karyawan.print', 'description' => 'Print master karyawan'],
            ['name' => 'master.karyawan.print.single', 'description' => 'Print single master karyawan'],
            ['name' => 'master.karyawan.import', 'description' => 'Import master karyawan'],
            ['name' => 'master.karyawan.import.store', 'description' => 'Store import master karyawan'],
            ['name' => 'master.karyawan.export', 'description' => 'Export master karyawan'],
            ['name' => 'master.karyawan.template', 'description' => 'Template master karyawan'],
            ['name' => 'master.karyawan.show', 'description' => 'Show master karyawan'],
            ['name' => 'master.karyawan.update', 'description' => 'Update master karyawan'],
            ['name' => 'master.karyawan.index', 'description' => 'Index master karyawan'],
            ['name' => 'master.karyawan.create', 'description' => 'Create master karyawan'],
            ['name' => 'master.karyawan.edit', 'description' => 'Edit master karyawan'],
            ['name' => 'master.karyawan.destroy', 'description' => 'Destroy master karyawan'],
            ['name' => 'master.karyawan', 'description' => 'Basic master karyawan access'],

            // User permissions
            ['name' => 'register.user', 'description' => 'Register user'],
            ['name' => 'register.user.store', 'description' => 'Store register user'],
            ['name' => 'master.user.create', 'description' => 'Create master user'],
            ['name' => 'master.user.store', 'description' => 'Store master user'],
            ['name' => 'master.user.edit', 'description' => 'Edit master user'],
            ['name' => 'master.user.update', 'description' => 'Update master user'],
            ['name' => 'master.user.destroy', 'description' => 'Destroy master user'],
            ['name' => 'master.user.permissions', 'description' => 'Manage master user permissions'],
            ['name' => 'master.user.index', 'description' => 'Index master user'],
            ['name' => 'master.user.show', 'description' => 'Show master user'],
            ['name' => 'master.user', 'description' => 'Basic master user access'],

            // Kontainer permissions
            ['name' => 'master.kontainer.index', 'description' => 'Index master kontainer'],
            ['name' => 'master.kontainer.create', 'description' => 'Create master kontainer'],
            ['name' => 'master.kontainer.store', 'description' => 'Store master kontainer'],
            ['name' => 'master.kontainer.show', 'description' => 'Show master kontainer'],
            ['name' => 'master.kontainer.edit', 'description' => 'Edit master kontainer'],
            ['name' => 'master.kontainer.update', 'description' => 'Update master kontainer'],
            ['name' => 'master.kontainer.destroy', 'description' => 'Destroy master kontainer'],
            ['name' => 'master.kontainer', 'description' => 'Basic master kontainer access'],

            // Tujuan permissions
            ['name' => 'master.tujuan.index', 'description' => 'Index master tujuan'],
            ['name' => 'master.tujuan.create', 'description' => 'Create master tujuan'],
            ['name' => 'master.tujuan.store', 'description' => 'Store master tujuan'],
            ['name' => 'master.tujuan.show', 'description' => 'Show master tujuan'],
            ['name' => 'master.tujuan.edit', 'description' => 'Edit master tujuan'],
            ['name' => 'master.tujuan.update', 'description' => 'Update master tujuan'],
            ['name' => 'master.tujuan.destroy', 'description' => 'Destroy master tujuan'],
            ['name' => 'master.tujuan', 'description' => 'Basic master tujuan access'],

            // Kegiatan permissions
            ['name' => 'master.kegiatan.index', 'description' => 'Index master kegiatan'],
            ['name' => 'master.kegiatan.create', 'description' => 'Create master kegiatan'],
            ['name' => 'master.kegiatan.store', 'description' => 'Store master kegiatan'],
            ['name' => 'master.kegiatan.show', 'description' => 'Show master kegiatan'],
            ['name' => 'master.kegiatan.edit', 'description' => 'Edit master kegiatan'],
            ['name' => 'master.kegiatan.update', 'description' => 'Update master kegiatan'],
            ['name' => 'master.kegiatan.destroy', 'description' => 'Destroy master kegiatan'],
            ['name' => 'master.kegiatan.template', 'description' => 'Template master kegiatan'],
            ['name' => 'master.kegiatan.import', 'description' => 'Import master kegiatan'],
            ['name' => 'master.kegiatan', 'description' => 'Basic master kegiatan access'],

            // Permission permissions
            ['name' => 'master.permission.index', 'description' => 'Index master permission'],
            ['name' => 'master.permission.create', 'description' => 'Create master permission'],
            ['name' => 'master.permission.store', 'description' => 'Store master permission'],
            ['name' => 'master.permission.show', 'description' => 'Show master permission'],
            ['name' => 'master.permission.edit', 'description' => 'Edit master permission'],
            ['name' => 'master.permission.update', 'description' => 'Update master permission'],
            ['name' => 'master.permission.destroy', 'description' => 'Destroy master permission'],
            ['name' => 'master.permission.sync', 'description' => 'Sync master permission'],
            ['name' => 'master.permission.users', 'description' => 'Users master permission'],
            ['name' => 'master.permission', 'description' => 'Basic master permission access'],

            // Mobil permissions
            ['name' => 'master.mobil.index', 'description' => 'Index master mobil'],
            ['name' => 'master.mobil.create', 'description' => 'Create master mobil'],
            ['name' => 'master.mobil.store', 'description' => 'Store master mobil'],
            ['name' => 'master.mobil.show', 'description' => 'Show master mobil'],
            ['name' => 'master.mobil.edit', 'description' => 'Edit master mobil'],
            ['name' => 'master.mobil.update', 'description' => 'Update master mobil'],
            ['name' => 'master.mobil.destroy', 'description' => 'Destroy master mobil'],
            ['name' => 'master.mobil', 'description' => 'Basic master mobil access'],

            // Master general
            ['name' => 'master', 'description' => 'Basic master access'],

            // Divisi permissions
            ['name' => 'master.divisi.index', 'description' => 'Index master divisi'],
            ['name' => 'master.divisi.create', 'description' => 'Create master divisi'],
            ['name' => 'master.divisi.store', 'description' => 'Store master divisi'],
            ['name' => 'master.divisi.show', 'description' => 'Show master divisi'],
            ['name' => 'master.divisi.edit', 'description' => 'Edit master divisi'],
            ['name' => 'master.divisi.update', 'description' => 'Update master divisi'],
            ['name' => 'master.divisi.destroy', 'description' => 'Destroy master divisi'],
            ['name' => 'master.divisi', 'description' => 'Basic master divisi access'],

            // Cabang permissions
            ['name' => 'master-cabang-view', 'description' => 'Index master cabang'],
            ['name' => 'master-cabang-create', 'description' => 'Create master cabang'],
            ['name' => 'master-cabang-store', 'description' => 'Store master cabang'],
            ['name' => 'master-cabang-show', 'description' => 'Show master cabang'],
            ['name' => 'master-cabang-edit', 'description' => 'Edit master cabang'],
            ['name' => 'master-cabang-update', 'description' => 'Update master cabang'],
            ['name' => 'master-cabang-delete', 'description' => 'Destroy master cabang'],
            ['name' => 'master-cabang', 'description' => 'Basic master cabang access'],

            // Pekerjaan permissions
            ['name' => 'master-pekerjaan-view', 'description' => 'Index master pekerjaan'],
            ['name' => 'master-pekerjaan-create', 'description' => 'Create master pekerjaan'],
            ['name' => 'master-pekerjaan-store', 'description' => 'Store master pekerjaan'],
            ['name' => 'master-pekerjaan-show', 'description' => 'Show master pekerjaan'],
            ['name' => 'master-pekerjaan-edit', 'description' => 'Edit master pekerjaan'],
            ['name' => 'master-pekerjaan-update', 'description' => 'Update master pekerjaan'],
            ['name' => 'master-pekerjaan-destroy', 'description' => 'Destroy master pekerjaan'],
            ['name' => 'master-pekerjaan-print', 'description' => 'Print master pekerjaan'],
            ['name' => 'master-pekerjaan-export', 'description' => 'Export master pekerjaan'],
            ['name' => 'master-pekerjaan', 'description' => 'Basic master pekerjaan access'],

            // Bank permissions
            ['name' => 'master-bank-index', 'description' => 'Index master bank'],
            ['name' => 'master-bank-create', 'description' => 'Create master bank'],
            ['name' => 'master-bank-store', 'description' => 'Store master bank'],
            ['name' => 'master-bank-show', 'description' => 'Show master bank'],
            ['name' => 'master-bank-edit', 'description' => 'Edit master bank'],
            ['name' => 'master-bank-update', 'description' => 'Update master bank'],
            ['name' => 'master-bank-destroy', 'description' => 'Destroy master bank'],
            ['name' => 'master-bank', 'description' => 'Basic master bank access'],

            // Pajak permissions
            ['name' => 'master-pajak-view', 'description' => 'Index master pajak'],
            ['name' => 'master-pajak-create', 'description' => 'Create master pajak'],
            ['name' => 'master-pajak-store', 'description' => 'Store master pajak'],
            ['name' => 'master-pajak-show', 'description' => 'Show master pajak'],
            ['name' => 'master-pajak-edit', 'description' => 'Edit master pajak'],
            ['name' => 'master-pajak-update', 'description' => 'Update master pajak'],
            ['name' => 'master-pajak-destroy', 'description' => 'Destroy master pajak'],
            ['name' => 'master-pajak', 'description' => 'Basic master pajak access'],

            // Auth permissions
            ['name' => 'auth.login', 'description' => 'Auth login'],
            ['name' => 'auth.logout', 'description' => 'Auth logout'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                ['description' => $permission['description']]
            );
        }
    }
}
