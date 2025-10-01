<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class SystemPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Master modules
            ['name' => 'master-karyawan', 'description' => 'Master Karyawan'],
            ['name' => 'master.karyawan', 'description' => 'Master Karyawan (dot notation)'],
            ['name' => 'master-karyawan-view', 'description' => 'View Master Karyawan'],
            ['name' => 'master-karyawan-create', 'description' => 'Create Master Karyawan'],
            ['name' => 'master-karyawan-update', 'description' => 'Update Master Karyawan'],
            ['name' => 'master-karyawan-delete', 'description' => 'Delete Master Karyawan'],
            ['name' => 'master-karyawan-print', 'description' => 'Print Master Karyawan'],
            ['name' => 'master-karyawan-export', 'description' => 'Export Master Karyawan'],
            ['name' => 'master-karyawan-approve', 'description' => 'Approve Master Karyawan'],

            ['name' => 'master-coa', 'description' => 'Master COA'],
            ['name' => 'master-coa-view', 'description' => 'View Master COA'],
            ['name' => 'master-coa-create', 'description' => 'Create Master COA'],
            ['name' => 'master-coa-update', 'description' => 'Update Master COA'],
            ['name' => 'master-coa-delete', 'description' => 'Delete Master COA'],

            ['name' => 'master-bank', 'description' => 'Master Bank'],
            ['name' => 'master-bank-view', 'description' => 'View Master Bank'],
            ['name' => 'master-bank-create', 'description' => 'Create Master Bank'],
            ['name' => 'master-bank-update', 'description' => 'Update Master Bank'],
            ['name' => 'master-bank-delete', 'description' => 'Delete Master Bank'],

            ['name' => 'master-pajak', 'description' => 'Master Pajak'],
            ['name' => 'master-pajak-view', 'description' => 'View Master Pajak'],
            ['name' => 'master-pajak-create', 'description' => 'Create Master Pajak'],
            ['name' => 'master-pajak-update', 'description' => 'Update Master Pajak'],
            ['name' => 'master-pajak-delete', 'description' => 'Delete Master Pajak'],

            ['name' => 'master-pekerjaan', 'description' => 'Master Pekerjaan'],
            ['name' => 'master-pekerjaan-view', 'description' => 'View Master Pekerjaan'],
            ['name' => 'master-pekerjaan-create', 'description' => 'Create Master Pekerjaan'],
            ['name' => 'master-pekerjaan-update', 'description' => 'Update Master Pekerjaan'],
            ['name' => 'master-pekerjaan-delete', 'description' => 'Delete Master Pekerjaan'],

            ['name' => 'master-cabang', 'description' => 'Master Cabang'],
            ['name' => 'master-cabang-view', 'description' => 'View Master Cabang'],
            ['name' => 'master-cabang-create', 'description' => 'Create Master Cabang'],
            ['name' => 'master-cabang-update', 'description' => 'Update Master Cabang'],
            ['name' => 'master-cabang-delete', 'description' => 'Delete Master Cabang'],

            ['name' => 'master-vendor-bengkel', 'description' => 'Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-view', 'description' => 'View Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-create', 'description' => 'Create Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-update', 'description' => 'Update Master Vendor Bengkel'],
            ['name' => 'master-vendor-bengkel-delete', 'description' => 'Delete Master Vendor Bengkel'],

            ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-view', 'description' => 'View Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-create', 'description' => 'Create Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-update', 'description' => 'Update Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer-delete', 'description' => 'Delete Master Pricelist Sewa Kontainer'],

            ['name' => 'master-pricelist-cat', 'description' => 'Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-view', 'description' => 'View Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-create', 'description' => 'Create Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-update', 'description' => 'Update Master Pricelist CAT'],
            ['name' => 'master-pricelist-cat-delete', 'description' => 'Delete Master Pricelist CAT'],

            ['name' => 'master-tipe-akun', 'description' => 'Master Tipe Akun'],
            ['name' => 'master-tipe-akun-view', 'description' => 'View Master Tipe Akun'],
            ['name' => 'master-tipe-akun-create', 'description' => 'Create Master Tipe Akun'],
            ['name' => 'master-tipe-akun-update', 'description' => 'Update Master Tipe Akun'],
            ['name' => 'master-tipe-akun-delete', 'description' => 'Delete Master Tipe Akun'],

            ['name' => 'master-stock-kontainer', 'description' => 'Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-view', 'description' => 'View Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-create', 'description' => 'Create Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-update', 'description' => 'Update Master Stock Kontainer'],
            ['name' => 'master-stock-kontainer-delete', 'description' => 'Delete Master Stock Kontainer'],

            ['name' => 'master-nomor-terakhir', 'description' => 'Master Nomor Terakhir'],
            ['name' => 'master-nomor-terakhir-view', 'description' => 'View Master Nomor Terakhir'],
            ['name' => 'master-nomor-terakhir-create', 'description' => 'Create Master Nomor Terakhir'],
            ['name' => 'master-nomor-terakhir-update', 'description' => 'Update Master Nomor Terakhir'],
            ['name' => 'master-nomor-terakhir-delete', 'description' => 'Delete Master Nomor Terakhir'],

            ['name' => 'master-kode-nomor', 'description' => 'Master Kode Nomor'],
            ['name' => 'master-kode-nomor-view', 'description' => 'View Master Kode Nomor'],
            ['name' => 'master-kode-nomor-create', 'description' => 'Create Master Kode Nomor'],
            ['name' => 'master-kode-nomor-update', 'description' => 'Update Master Kode Nomor'],
            ['name' => 'master-kode-nomor-delete', 'description' => 'Delete Master Kode Nomor'],

            // Tagihan modules
            ['name' => 'tagihan-kontainer', 'description' => 'Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-view', 'description' => 'View Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-create', 'description' => 'Create Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-update', 'description' => 'Update Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-delete', 'description' => 'Delete Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-print', 'description' => 'Print Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer-export', 'description' => 'Export Tagihan Kontainer'],

            ['name' => 'tagihan-cat', 'description' => 'Tagihan CAT'],
            ['name' => 'tagihan-cat-view', 'description' => 'View Tagihan CAT'],
            ['name' => 'tagihan-cat-create', 'description' => 'Create Tagihan CAT'],
            ['name' => 'tagihan-cat-update', 'description' => 'Update Tagihan CAT'],
            ['name' => 'tagihan-cat-delete', 'description' => 'Delete Tagihan CAT'],
            ['name' => 'tagihan-cat-print', 'description' => 'Print Tagihan CAT'],
            ['name' => 'tagihan-cat-export', 'description' => 'Export Tagihan CAT'],

            ['name' => 'tagihan-kontainer-sewa', 'description' => 'Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-view', 'description' => 'View Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-create', 'description' => 'Create Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-update', 'description' => 'Update Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-delete', 'description' => 'Delete Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa-export', 'description' => 'Export Tagihan Kontainer Sewa'],

            ['name' => 'tagihan-perbaikan-kontainer', 'description' => 'Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-view', 'description' => 'View Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-create', 'description' => 'Create Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-update', 'description' => 'Update Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-delete', 'description' => 'Delete Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-approve', 'description' => 'Approve Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-print', 'description' => 'Print Tagihan Perbaikan Kontainer'],
            ['name' => 'tagihan-perbaikan-kontainer-export', 'description' => 'Export Tagihan Perbaikan Kontainer'],

            // Pranota modules
            ['name' => 'pranota-supir', 'description' => 'Pranota Supir'],
            ['name' => 'pranota-supir-view', 'description' => 'View Pranota Supir'],
            ['name' => 'pranota-supir-create', 'description' => 'Create Pranota Supir'],
            ['name' => 'pranota-supir-update', 'description' => 'Update Pranota Supir'],
            ['name' => 'pranota-supir-delete', 'description' => 'Delete Pranota Supir'],
            ['name' => 'pranota-supir-print', 'description' => 'Print Pranota Supir'],
            ['name' => 'pranota-supir-export', 'description' => 'Export Pranota Supir'],

            ['name' => 'pranota-cat', 'description' => 'Pranota CAT'],
            ['name' => 'pranota-cat-view', 'description' => 'View Pranota CAT'],
            ['name' => 'pranota-cat-create', 'description' => 'Create Pranota CAT'],
            ['name' => 'pranota-cat-update', 'description' => 'Update Pranota CAT'],
            ['name' => 'pranota-cat-delete', 'description' => 'Delete Pranota CAT'],
            ['name' => 'pranota-cat-print', 'description' => 'Print Pranota CAT'],
            ['name' => 'pranota-cat-export', 'description' => 'Export Pranota CAT'],

            ['name' => 'pranota-kontainer-sewa', 'description' => 'Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-view', 'description' => 'View Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-create', 'description' => 'Create Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-update', 'description' => 'Update Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-delete', 'description' => 'Delete Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-print', 'description' => 'Print Pranota Kontainer Sewa'],
            ['name' => 'pranota-kontainer-sewa-export', 'description' => 'Export Pranota Kontainer Sewa'],

            ['name' => 'pranota-perbaikan-kontainer', 'description' => 'Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-view', 'description' => 'View Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-create', 'description' => 'Create Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-update', 'description' => 'Update Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer-delete', 'description' => 'Delete Pranota Perbaikan Kontainer'],

            // Pembayaran modules
            ['name' => 'pembayaran-pranota-supir', 'description' => 'Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-view', 'description' => 'View Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-create', 'description' => 'Create Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-update', 'description' => 'Update Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir-delete', 'description' => 'Delete Pembayaran Pranota Supir'],

            ['name' => 'pembayaran-pranota-kontainer', 'description' => 'Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-view', 'description' => 'View Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-create', 'description' => 'Create Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-update', 'description' => 'Update Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer-delete', 'description' => 'Delete Pembayaran Pranota Kontainer'],

            ['name' => 'pembayaran-pranota-cat', 'description' => 'Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-view', 'description' => 'View Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-create', 'description' => 'Create Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-update', 'description' => 'Update Pembayaran Pranota CAT'],
            ['name' => 'pembayaran-pranota-cat-delete', 'description' => 'Delete Pembayaran Pranota CAT'],

            ['name' => 'pembayaran-pranota-perbaikan-kontainer', 'description' => 'Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-view', 'description' => 'View Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-create', 'description' => 'Create Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-update', 'description' => 'Update Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer-delete', 'description' => 'Delete Pembayaran Pranota Perbaikan Kontainer'],

            ['name' => 'pembayaran-pranota-tagihan-kontainer', 'description' => 'Pembayaran Pranota Tagihan Kontainer'],

            // Perbaikan modules
            ['name' => 'perbaikan-kontainer', 'description' => 'Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-view', 'description' => 'View Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-create', 'description' => 'Create Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-update', 'description' => 'Update Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer-delete', 'description' => 'Delete Perbaikan Kontainer'],

            // Other modules
            ['name' => 'permohonan', 'description' => 'Permohonan'],
            ['name' => 'permohonan-view', 'description' => 'View Permohonan'],
            ['name' => 'permohonan-create', 'description' => 'Create Permohonan'],
            ['name' => 'permohonan-update', 'description' => 'Update Permohonan'],
            ['name' => 'permohonan-delete', 'description' => 'Delete Permohonan'],
            ['name' => 'permohonan.edit', 'description' => 'Edit Permohonan (dot notation)'],
            ['name' => 'permohonan.create', 'description' => 'Create Permohonan (dot notation)'],

            ['name' => 'permohonan-memo', 'description' => 'Permohonan Memo'],
            ['name' => 'permohonan-memo-view', 'description' => 'View Permohonan Memo'],
            ['name' => 'permohonan-memo-create', 'description' => 'Create Permohonan Memo'],
            ['name' => 'permohonan-memo-update', 'description' => 'Update Permohonan Memo'],
            ['name' => 'permohonan-memo-delete', 'description' => 'Delete Permohonan Memo'],

            ['name' => 'profile', 'description' => 'Profile'],
            ['name' => 'profile-show', 'description' => 'Show Profile'],
            ['name' => 'profile-edit', 'description' => 'Edit Profile'],
            ['name' => 'profile-update', 'description' => 'Update Profile'],
            ['name' => 'profile-update-account', 'description' => 'Update Account Profile'],
            ['name' => 'profile-destroy', 'description' => 'Destroy Profile'],

            ['name' => 'supir', 'description' => 'Supir'],
            ['name' => 'supir-dashboard', 'description' => 'Supir Dashboard'],
            ['name' => 'supir-checkpoint', 'description' => 'Supir Checkpoint'],
            ['name' => 'supir-checkpoint-create', 'description' => 'Create Supir Checkpoint'],
            ['name' => 'supir-checkpoint-store', 'description' => 'Store Supir Checkpoint'],

            ['name' => 'approval', 'description' => 'Approval'],
            ['name' => 'approval-view', 'description' => 'View Approval'],
            ['name' => 'approval-dashboard', 'description' => 'Approval Dashboard'],
            ['name' => 'approval-mass-process', 'description' => 'Mass Process Approval'],
            ['name' => 'approval-create', 'description' => 'Create Approval'],
            ['name' => 'approval-store', 'description' => 'Store Approval'],
            ['name' => 'approval-riwayat', 'description' => 'Riwayat Approval'],
            ['name' => 'approval-approve', 'description' => 'Approve Approval'],
            ['name' => 'approval-print', 'description' => 'Print Approval'],

            ['name' => 'admin', 'description' => 'Admin'],
            ['name' => 'admin-debug', 'description' => 'Admin Debug'],
            ['name' => 'admin-features', 'description' => 'Admin Features'],

            ['name' => 'user-approval', 'description' => 'User Approval'],
            ['name' => 'user-approval-view', 'description' => 'View User Approval'],

            ['name' => 'storage-local', 'description' => 'Storage Local'],

            ['name' => 'login', 'description' => 'Login'],
            ['name' => 'logout', 'description' => 'Logout'],

            // Additional permissions from analysis
            ['name' => 'master-user-view', 'description' => 'View Master User'],
            ['name' => 'master-kontainer', 'description' => 'Master Kontainer'],
            ['name' => 'master-tujuan', 'description' => 'Master Tujuan'],
            ['name' => 'master-kegiatan', 'description' => 'Master Kegiatan'],
            ['name' => 'master-permission', 'description' => 'Master Permission'],
            ['name' => 'master-mobil', 'description' => 'Master Mobil'],
            ['name' => 'dashboard', 'description' => 'Dashboard'],
            ['name' => 'master-pranota-tagihan-kontainer', 'description' => 'Master Pranota Tagihan Kontainer'],
            ['name' => 'tagihan-kontainer.view', 'description' => 'Tagihan Kontainer View (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa.index', 'description' => 'Tagihan Kontainer Sewa Index (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa.create', 'description' => 'Tagihan Kontainer Sewa Create (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa.update', 'description' => 'Tagihan Kontainer Sewa Update (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa.destroy', 'description' => 'Tagihan Kontainer Sewa Destroy (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa.export', 'description' => 'Tagihan Kontainer Sewa Export (dot notation)'],
            ['name' => 'tagihan-kontainer-sewa-index', 'description' => 'Tagihan Kontainer Sewa Index (dash notation)'],
            ['name' => 'tagihan-kontainer-sewa-create', 'description' => 'Tagihan Kontainer Sewa Create (dash notation)'],
        ];

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                $permissionData
            );
        }

        $this->command->info('System permissions seeded successfully!');
    }
}
