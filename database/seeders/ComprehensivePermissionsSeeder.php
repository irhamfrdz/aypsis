<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Permission;

class ComprehensivePermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds to create comprehensive permissions.
     */
    public function run(): void
    {
        $this->command->info('🚀 Starting comprehensive permissions seeding...');

        // Get existing permissions to avoid duplicates
        $existingPermissions = Permission::pluck('name')->toArray();
        $newPermissions = [];

        // 1. DASHBOARD PERMISSIONS
        $dashboardPermissions = [
            ['name' => 'dashboard', 'description' => 'Akses Dashboard Utama'],
            ['name' => 'dashboard.view', 'description' => 'Melihat Dashboard'],
            ['name' => 'dashboard.create', 'description' => 'Membuat Dashboard'],
            ['name' => 'dashboard.update', 'description' => 'Mengupdate Dashboard'],
            ['name' => 'dashboard.delete', 'description' => 'Menghapus Dashboard'],
            ['name' => 'dashboard.print', 'description' => 'Print Dashboard'],
            ['name' => 'dashboard.export', 'description' => 'Export Dashboard'],
        ];

        // 2. MASTER DATA PERMISSIONS
        $masterPermissions = [
            // Master Karyawan
            ['name' => 'master-karyawan', 'description' => 'Akses Master Karyawan'],
            ['name' => 'master-karyawan.view', 'description' => 'Melihat Master Karyawan'],
            ['name' => 'master-karyawan.create', 'description' => 'Membuat Master Karyawan'],
            ['name' => 'master-karyawan.update', 'description' => 'Mengupdate Master Karyawan'],
            ['name' => 'master-karyawan.delete', 'description' => 'Menghapus Master Karyawan'],
            ['name' => 'master-karyawan.print', 'description' => 'Print Master Karyawan'],
            ['name' => 'master-karyawan.export', 'description' => 'Export Master Karyawan'],
            ['name' => 'master.karyawan.index', 'description' => 'Index Master Karyawan'],
            ['name' => 'master.karyawan.create', 'description' => 'Create Master Karyawan'],
            ['name' => 'master.karyawan.store', 'description' => 'Store Master Karyawan'],
            ['name' => 'master.karyawan.show', 'description' => 'Show Master Karyawan'],
            ['name' => 'master.karyawan.edit', 'description' => 'Edit Master Karyawan'],
            ['name' => 'master.karyawan.update', 'description' => 'Update Master Karyawan'],
            ['name' => 'master.karyawan.destroy', 'description' => 'Destroy Master Karyawan'],
            ['name' => 'master.karyawan.print', 'description' => 'Print Master Karyawan'],
            ['name' => 'master.karyawan.print.single', 'description' => 'Print Single Master Karyawan'],
            ['name' => 'master.karyawan.import', 'description' => 'Import Master Karyawan'],
            ['name' => 'master.karyawan.import.store', 'description' => 'Store Import Master Karyawan'],
            ['name' => 'master.karyawan.export', 'description' => 'Export Master Karyawan'],

            // Master User
            ['name' => 'master-user', 'description' => 'Akses Master User'],
            ['name' => 'master-user.view', 'description' => 'Melihat Master User'],
            ['name' => 'master-user.create', 'description' => 'Membuat Master User'],
            ['name' => 'master-user.update', 'description' => 'Mengupdate Master User'],
            ['name' => 'master-user.delete', 'description' => 'Menghapus Master User'],
            ['name' => 'master-user.print', 'description' => 'Print Master User'],
            ['name' => 'master-user.export', 'description' => 'Export Master User'],
            ['name' => 'master.user.index', 'description' => 'Index Master User'],
            ['name' => 'master.user.create', 'description' => 'Create Master User'],
            ['name' => 'master.user.store', 'description' => 'Store Master User'],
            ['name' => 'master.user.show', 'description' => 'Show Master User'],
            ['name' => 'master.user.edit', 'description' => 'Edit Master User'],
            ['name' => 'master.user.update', 'description' => 'Update Master User'],
            ['name' => 'master.user.destroy', 'description' => 'Destroy Master User'],

            // Master Kontainer
            ['name' => 'master-kontainer', 'description' => 'Akses Master Kontainer'],
            ['name' => 'master-kontainer.view', 'description' => 'Melihat Master Kontainer'],
            ['name' => 'master-kontainer.create', 'description' => 'Membuat Master Kontainer'],
            ['name' => 'master-kontainer.update', 'description' => 'Mengupdate Master Kontainer'],
            ['name' => 'master-kontainer.delete', 'description' => 'Menghapus Master Kontainer'],
            ['name' => 'master-kontainer.print', 'description' => 'Print Master Kontainer'],
            ['name' => 'master-kontainer.export', 'description' => 'Export Master Kontainer'],
            ['name' => 'master.kontainer.index', 'description' => 'Index Master Kontainer'],
            ['name' => 'master.kontainer.create', 'description' => 'Create Master Kontainer'],
            ['name' => 'master.kontainer.store', 'description' => 'Store Master Kontainer'],
            ['name' => 'master.kontainer.show', 'description' => 'Show Master Kontainer'],
            ['name' => 'master.kontainer.edit', 'description' => 'Edit Master Kontainer'],
            ['name' => 'master.kontainer.update', 'description' => 'Update Master Kontainer'],
            ['name' => 'master.kontainer.destroy', 'description' => 'Destroy Master Kontainer'],

            // Master Tujuan
            ['name' => 'master-tujuan', 'description' => 'Akses Master Tujuan'],
            ['name' => 'master-tujuan.view', 'description' => 'Melihat Master Tujuan'],
            ['name' => 'master-tujuan.create', 'description' => 'Membuat Master Tujuan'],
            ['name' => 'master-tujuan.update', 'description' => 'Mengupdate Master Tujuan'],
            ['name' => 'master-tujuan.delete', 'description' => 'Menghapus Master Tujuan'],
            ['name' => 'master-tujuan.print', 'description' => 'Print Master Tujuan'],
            ['name' => 'master-tujuan.export', 'description' => 'Export Master Tujuan'],
            ['name' => 'master.tujuan.index', 'description' => 'Index Master Tujuan'],
            ['name' => 'master.tujuan.create', 'description' => 'Create Master Tujuan'],
            ['name' => 'master.tujuan.store', 'description' => 'Store Master Tujuan'],
            ['name' => 'master.tujuan.show', 'description' => 'Show Master Tujuan'],
            ['name' => 'master.tujuan.edit', 'description' => 'Edit Master Tujuan'],
            ['name' => 'master.tujuan.update', 'description' => 'Update Master Tujuan'],
            ['name' => 'master.tujuan.destroy', 'description' => 'Destroy Master Tujuan'],

            // Master Kegiatan
            ['name' => 'master-kegiatan', 'description' => 'Akses Master Kegiatan'],
            ['name' => 'master-kegiatan.view', 'description' => 'Melihat Master Kegiatan'],
            ['name' => 'master-kegiatan.create', 'description' => 'Membuat Master Kegiatan'],
            ['name' => 'master-kegiatan.update', 'description' => 'Mengupdate Master Kegiatan'],
            ['name' => 'master-kegiatan.delete', 'description' => 'Menghapus Master Kegiatan'],
            ['name' => 'master-kegiatan.print', 'description' => 'Print Master Kegiatan'],
            ['name' => 'master-kegiatan.export', 'description' => 'Export Master Kegiatan'],
            ['name' => 'master.kegiatan.index', 'description' => 'Index Master Kegiatan'],
            ['name' => 'master.kegiatan.create', 'description' => 'Create Master Kegiatan'],
            ['name' => 'master.kegiatan.store', 'description' => 'Store Master Kegiatan'],
            ['name' => 'master.kegiatan.show', 'description' => 'Show Master Kegiatan'],
            ['name' => 'master.kegiatan.edit', 'description' => 'Edit Master Kegiatan'],
            ['name' => 'master.kegiatan.update', 'description' => 'Update Master Kegiatan'],
            ['name' => 'master.kegiatan.destroy', 'description' => 'Destroy Master Kegiatan'],
            ['name' => 'master.kegiatan.template', 'description' => 'Template Master Kegiatan'],
            ['name' => 'master.kegiatan.import', 'description' => 'Import Master Kegiatan'],

            // Master Permission
            ['name' => 'master-permission', 'description' => 'Akses Master Permission'],
            ['name' => 'master-permission.view', 'description' => 'Melihat Master Permission'],
            ['name' => 'master-permission.create', 'description' => 'Membuat Master Permission'],
            ['name' => 'master-permission.update', 'description' => 'Mengupdate Master Permission'],
            ['name' => 'master-permission.delete', 'description' => 'Menghapus Master Permission'],
            ['name' => 'master-permission.print', 'description' => 'Print Master Permission'],
            ['name' => 'master-permission.export', 'description' => 'Export Master Permission'],
            ['name' => 'master.permission.index', 'description' => 'Index Master Permission'],
            ['name' => 'master.permission.create', 'description' => 'Create Master Permission'],
            ['name' => 'master.permission.store', 'description' => 'Store Master Permission'],
            ['name' => 'master.permission.show', 'description' => 'Show Master Permission'],
            ['name' => 'master.permission.edit', 'description' => 'Edit Master Permission'],
            ['name' => 'master.permission.update', 'description' => 'Update Master Permission'],
            ['name' => 'master.permission.destroy', 'description' => 'Destroy Master Permission'],

            // Master Mobil
            ['name' => 'master-mobil', 'description' => 'Akses Master Mobil'],
            ['name' => 'master-mobil.view', 'description' => 'Melihat Master Mobil'],
            ['name' => 'master-mobil.create', 'description' => 'Membuat Master Mobil'],
            ['name' => 'master-mobil.update', 'description' => 'Mengupdate Master Mobil'],
            ['name' => 'master-mobil.delete', 'description' => 'Menghapus Master Mobil'],
            ['name' => 'master-mobil.print', 'description' => 'Print Master Mobil'],
            ['name' => 'master-mobil.export', 'description' => 'Export Master Mobil'],

            // Master Pricelist Sewa Kontainer
            ['name' => 'master-pricelist-sewa-kontainer', 'description' => 'Akses Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.view', 'description' => 'Melihat Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.create', 'description' => 'Membuat Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.update', 'description' => 'Mengupdate Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.delete', 'description' => 'Menghapus Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.print', 'description' => 'Print Master Pricelist Sewa Kontainer'],
            ['name' => 'master-pricelist-sewa-kontainer.export', 'description' => 'Export Master Pricelist Sewa Kontainer'],

            // Master Cabang
            ['name' => 'master-cabang', 'description' => 'Akses Master Cabang'],
            ['name' => 'master-cabang.view', 'description' => 'Melihat Master Cabang'],
            ['name' => 'master-cabang.create', 'description' => 'Membuat Master Cabang'],
            ['name' => 'master-cabang.update', 'description' => 'Mengupdate Master Cabang'],
            ['name' => 'master-cabang.delete', 'description' => 'Menghapus Master Cabang'],
            ['name' => 'master-cabang.print', 'description' => 'Print Master Cabang'],
            ['name' => 'master-cabang.export', 'description' => 'Export Master Cabang'],
            ['name' => 'master.cabang.index', 'description' => 'Index Master Cabang'],
            ['name' => 'master.cabang.create', 'description' => 'Create Master Cabang'],
            ['name' => 'master.cabang.store', 'description' => 'Store Master Cabang'],
            ['name' => 'master.cabang.show', 'description' => 'Show Master Cabang'],
            ['name' => 'master.cabang.edit', 'description' => 'Edit Master Cabang'],
            ['name' => 'master.cabang.update', 'description' => 'Update Master Cabang'],
            ['name' => 'master.cabang.destroy', 'description' => 'Destroy Master Cabang'],

            // Master Divisi
            ['name' => 'master-divisi', 'description' => 'Akses Master Divisi'],
            ['name' => 'master-divisi.view', 'description' => 'Melihat Master Divisi'],
            ['name' => 'master-divisi.create', 'description' => 'Membuat Master Divisi'],
            ['name' => 'master-divisi.update', 'description' => 'Mengupdate Master Divisi'],
            ['name' => 'master-divisi.delete', 'description' => 'Menghapus Master Divisi'],
            ['name' => 'master-divisi.print', 'description' => 'Print Master Divisi'],
            ['name' => 'master-divisi.export', 'description' => 'Export Master Divisi'],

            // Master Pekerjaan
            ['name' => 'master-pekerjaan', 'description' => 'Akses Master Pekerjaan'],
            ['name' => 'master-pekerjaan.view', 'description' => 'Melihat Master Pekerjaan'],
            ['name' => 'master-pekerjaan.create', 'description' => 'Membuat Master Pekerjaan'],
            ['name' => 'master-pekerjaan.update', 'description' => 'Mengupdate Master Pekerjaan'],
            ['name' => 'master-pekerjaan.delete', 'description' => 'Menghapus Master Pekerjaan'],
            ['name' => 'master-pekerjaan.print', 'description' => 'Print Master Pekerjaan'],
            ['name' => 'master-pekerjaan.export', 'description' => 'Export Master Pekerjaan'],
            ['name' => 'master.pekerjaan.index', 'description' => 'Index Master Pekerjaan'],
            ['name' => 'master.pekerjaan.create', 'description' => 'Create Master Pekerjaan'],
            ['name' => 'master.pekerjaan.store', 'description' => 'Store Master Pekerjaan'],
            ['name' => 'master.pekerjaan.show', 'description' => 'Show Master Pekerjaan'],
            ['name' => 'master.pekerjaan.edit', 'description' => 'Edit Master Pekerjaan'],
            ['name' => 'master.pekerjaan.update', 'description' => 'Update Master Pekerjaan'],
            ['name' => 'master.pekerjaan.destroy', 'description' => 'Destroy Master Pekerjaan'],

            // Master Pajak
            ['name' => 'master-pajak', 'description' => 'Akses Master Pajak'],
            ['name' => 'master-pajak.view', 'description' => 'Melihat Master Pajak'],
            ['name' => 'master-pajak.create', 'description' => 'Membuat Master Pajak'],
            ['name' => 'master-pajak.update', 'description' => 'Mengupdate Master Pajak'],
            ['name' => 'master-pajak.delete', 'description' => 'Menghapus Master Pajak'],
            ['name' => 'master-pajak.print', 'description' => 'Print Master Pajak'],
            ['name' => 'master-pajak.export', 'description' => 'Export Master Pajak'],
            ['name' => 'master.pajak.index', 'description' => 'Index Master Pajak'],
            ['name' => 'master.pajak.create', 'description' => 'Create Master Pajak'],
            ['name' => 'master.pajak.store', 'description' => 'Store Master Pajak'],
            ['name' => 'master.pajak.show', 'description' => 'Show Master Pajak'],
            ['name' => 'master.pajak.edit', 'description' => 'Edit Master Pajak'],
            ['name' => 'master.pajak.update', 'description' => 'Update Master Pajak'],
            ['name' => 'master.pajak.destroy', 'description' => 'Destroy Master Pajak'],

            // Master Bank
            ['name' => 'master-bank', 'description' => 'Akses Master Bank'],
            ['name' => 'master-bank.view', 'description' => 'Melihat Master Bank'],
            ['name' => 'master-bank.create', 'description' => 'Membuat Master Bank'],
            ['name' => 'master-bank.update', 'description' => 'Mengupdate Master Bank'],
            ['name' => 'master-bank.delete', 'description' => 'Menghapus Master Bank'],
            ['name' => 'master-bank.print', 'description' => 'Print Master Bank'],
            ['name' => 'master-bank.export', 'description' => 'Export Master Bank'],
            ['name' => 'master-bank-index', 'description' => 'Index Master Bank'],
            ['name' => 'master-bank-create', 'description' => 'Create Master Bank'],
            ['name' => 'master-bank-store', 'description' => 'Store Master Bank'],
            ['name' => 'master-bank-show', 'description' => 'Show Master Bank'],
            ['name' => 'master-bank-edit', 'description' => 'Edit Master Bank'],
            ['name' => 'master-bank-update', 'description' => 'Update Master Bank'],
            ['name' => 'master-bank-destroy', 'description' => 'Destroy Master Bank'],

            // Master COA
            ['name' => 'master-coa', 'description' => 'Akses Master COA'],
            ['name' => 'master-coa.view', 'description' => 'Melihat Master COA'],
            ['name' => 'master-coa.create', 'description' => 'Membuat Master COA'],
            ['name' => 'master-coa.update', 'description' => 'Mengupdate Master COA'],
            ['name' => 'master-coa.delete', 'description' => 'Menghapus Master COA'],
            ['name' => 'master-coa.print', 'description' => 'Print Master COA'],
            ['name' => 'master-coa.export', 'description' => 'Export Master COA'],
            ['name' => 'master-coa-index', 'description' => 'Index Master COA'],
            ['name' => 'master-coa-create', 'description' => 'Create Master COA'],
            ['name' => 'master-coa-store', 'description' => 'Store Master COA'],
            ['name' => 'master-coa-show', 'description' => 'Show Master COA'],
            ['name' => 'master-coa-edit', 'description' => 'Edit Master COA'],
            ['name' => 'master-coa-update', 'description' => 'Update Master COA'],
            ['name' => 'master-coa-destroy', 'description' => 'Destroy Master COA'],

            // Master Vendor/Bengkel
            ['name' => 'master-vendor-bengkel', 'description' => 'Akses Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.view', 'description' => 'Melihat Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.create', 'description' => 'Membuat Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.update', 'description' => 'Mengupdate Master Vendor/Bengkel'],
            ['name' => 'master-vendor-bengkel.delete', 'description' => 'Menghapus Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.index', 'description' => 'Index Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.create', 'description' => 'Create Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.store', 'description' => 'Store Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.show', 'description' => 'Show Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.edit', 'description' => 'Edit Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.update', 'description' => 'Update Master Vendor/Bengkel'],
            ['name' => 'master.vendor-bengkel.destroy', 'description' => 'Destroy Master Vendor/Bengkel'],
        ];

        // 3. PRANOTA PERMISSIONS
        $pranotaPermissions = [
            ['name' => 'pranota', 'description' => 'Akses Pranota'],
            ['name' => 'pranota.view', 'description' => 'Melihat Pranota'],
            ['name' => 'pranota.create', 'description' => 'Membuat Pranota'],
            ['name' => 'pranota.update', 'description' => 'Mengupdate Pranota'],
            ['name' => 'pranota.delete', 'description' => 'Menghapus Pranota'],
            ['name' => 'pranota.approve', 'description' => 'Approve Pranota'],
            ['name' => 'pranota.print', 'description' => 'Print Pranota'],
            ['name' => 'pranota.export', 'description' => 'Export Pranota'],

            // Pranota Supir
            ['name' => 'master-pranota-supir', 'description' => 'Akses Master Pranota Supir'],
            ['name' => 'pranota-supir.view', 'description' => 'Melihat Pranota Supir'],
            ['name' => 'pranota-supir.create', 'description' => 'Membuat Pranota Supir'],
            ['name' => 'pranota-supir.update', 'description' => 'Mengupdate Pranota Supir'],
            ['name' => 'pranota-supir.delete', 'description' => 'Menghapus Pranota Supir'],
            ['name' => 'pranota-supir.approve', 'description' => 'Approve Pranota Supir'],
            ['name' => 'pranota-supir.print', 'description' => 'Print Pranota Supir'],
            ['name' => 'pranota-supir.export', 'description' => 'Export Pranota Supir'],

            // Pranota Tagihan Kontainer
            ['name' => 'master-pranota-tagihan-kontainer', 'description' => 'Akses Master Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.view', 'description' => 'Melihat Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.create', 'description' => 'Membuat Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.update', 'description' => 'Mengupdate Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.delete', 'description' => 'Menghapus Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.approve', 'description' => 'Approve Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.print', 'description' => 'Print Pranota Tagihan Kontainer'],
            ['name' => 'pranota-tagihan-kontainer.export', 'description' => 'Export Pranota Tagihan Kontainer'],
        ];

        // 4. PEMBAYARAN PERMISSIONS
        $pembayaranPermissions = [
            // Pembayaran Pranota Supir
            ['name' => 'master-pembayaran-pranota-supir', 'description' => 'Akses Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.view', 'description' => 'Melihat Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.create', 'description' => 'Membuat Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.update', 'description' => 'Mengupdate Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.delete', 'description' => 'Menghapus Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.approve', 'description' => 'Approve Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.print', 'description' => 'Print Pembayaran Pranota Supir'],
            ['name' => 'pembayaran-pranota-supir.export', 'description' => 'Export Pembayaran Pranota Supir'],

            // Pembayaran Pranota Kontainer
            ['name' => 'pembayaran-pranota-kontainer.view', 'description' => 'Melihat Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.create', 'description' => 'Membuat Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.update', 'description' => 'Mengupdate Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.delete', 'description' => 'Menghapus Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.approve', 'description' => 'Approve Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.print', 'description' => 'Print Pembayaran Pranota Kontainer'],
            ['name' => 'pembayaran-pranota-kontainer.export', 'description' => 'Export Pembayaran Pranota Kontainer'],

            // Pembayaran Pranota Perbaikan Kontainer
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.view', 'description' => 'Melihat Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.create', 'description' => 'Membuat Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.update', 'description' => 'Mengupdate Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.delete', 'description' => 'Menghapus Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.approve', 'description' => 'Approve Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.print', 'description' => 'Print Pembayaran Pranota Perbaikan Kontainer'],
            ['name' => 'pembayaran-pranota-perbaikan-kontainer.export', 'description' => 'Export Pembayaran Pranota Perbaikan Kontainer'],
        ];

        // 5. TAGIHAN PERMISSIONS
        $tagihanPermissions = [
            ['name' => 'tagihan-kontainer-sewa', 'description' => 'Akses Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.view', 'description' => 'Melihat Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.create', 'description' => 'Membuat Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.update', 'description' => 'Mengupdate Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.delete', 'description' => 'Menghapus Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.approve', 'description' => 'Approve Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.print', 'description' => 'Print Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.export', 'description' => 'Export Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.group.show', 'description' => 'Show Group Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.adjust_price', 'description' => 'Adjust Price Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.search_by_kontainer', 'description' => 'Search by Kontainer Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.group.adjust_price', 'description' => 'Group Adjust Price Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.history', 'description' => 'History Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.rollover', 'description' => 'Rollover Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.template', 'description' => 'Template Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.import', 'description' => 'Import Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.index', 'description' => 'Index Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.create', 'description' => 'Create Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.store', 'description' => 'Store Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.show', 'description' => 'Show Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.edit', 'description' => 'Edit Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.update', 'description' => 'Update Tagihan Kontainer Sewa'],
            ['name' => 'tagihan-kontainer-sewa.destroy', 'description' => 'Destroy Tagihan Kontainer Sewa'],
        ];

        // 6. PERMOHONAN PERMISSIONS
        $permohonanPermissions = [
            ['name' => 'master-permohonan', 'description' => 'Akses Master Permohonan'],
            ['name' => 'permohonan', 'description' => 'Akses Permohonan'],
            ['name' => 'permohonan.view', 'description' => 'Melihat Permohonan'],
            ['name' => 'permohonan.create', 'description' => 'Membuat Permohonan'],
            ['name' => 'permohonan.update', 'description' => 'Mengupdate Permohonan'],
            ['name' => 'permohonan.delete', 'description' => 'Menghapus Permohonan'],
            ['name' => 'permohonan.approve', 'description' => 'Approve Permohonan'],
            ['name' => 'permohonan.print', 'description' => 'Print Permohonan'],
            ['name' => 'permohonan.export', 'description' => 'Export Permohonan'],
            ['name' => 'permohonan-create', 'description' => 'Create Permohonan'],
            ['name' => 'permohonan-view', 'description' => 'View Permohonan'],
            ['name' => 'permohonan-edit', 'description' => 'Edit Permohonan'],
            ['name' => 'permohonan-delete', 'description' => 'Delete Permohonan'],
            ['name' => 'permohonan-update', 'description' => 'Update Permohonan'],
            ['name' => 'permohonan-approve', 'description' => 'Approve Permohonan'],
            ['name' => 'permohonan-print', 'description' => 'Print Permohonan'],
            ['name' => 'permohonan-export', 'description' => 'Export Permohonan'],
        ];

        // 7. PERBAIKAN KONTAINER PERMISSIONS
        $perbaikanPermissions = [
            ['name' => 'perbaikan-kontainer', 'description' => 'Akses Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.view', 'description' => 'Melihat Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.create', 'description' => 'Membuat Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.update', 'description' => 'Mengupdate Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.delete', 'description' => 'Menghapus Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.approve', 'description' => 'Approve Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.print', 'description' => 'Print Perbaikan Kontainer'],
            ['name' => 'perbaikan-kontainer.export', 'description' => 'Export Perbaikan Kontainer'],

            // Pranota Perbaikan Kontainer
            ['name' => 'pranota-perbaikan-kontainer.view', 'description' => 'Melihat Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.create', 'description' => 'Membuat Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.update', 'description' => 'Mengupdate Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.delete', 'description' => 'Menghapus Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.approve', 'description' => 'Approve Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.print', 'description' => 'Print Pranota Perbaikan Kontainer'],
            ['name' => 'pranota-perbaikan-kontainer.export', 'description' => 'Export Pranota Perbaikan Kontainer'],
        ];

        // 8. USER & APPROVAL PERMISSIONS
        $userPermissions = [
            ['name' => 'user-approval', 'description' => 'Akses User Approval'],
            ['name' => 'user-approval.view', 'description' => 'Melihat User Approval'],
            ['name' => 'user-approval.create', 'description' => 'Membuat User Approval'],
            ['name' => 'user-approval.update', 'description' => 'Mengupdate User Approval'],
            ['name' => 'user-approval.delete', 'description' => 'Menghapus User Approval'],
            ['name' => 'user-approval.print', 'description' => 'Print User Approval'],
            ['name' => 'user-approval.export', 'description' => 'Export User Approval'],

            // Approval System
            ['name' => 'approval.view', 'description' => 'Melihat Approval Tasks'],
            ['name' => 'approval.update', 'description' => 'Mengupdate Approval Tasks'],
            ['name' => 'approval.delete', 'description' => 'Menghapus Approval Tasks'],
            ['name' => 'approval.approve', 'description' => 'Approve Tasks'],
            ['name' => 'approval.print', 'description' => 'Print Approval Reports'],
            ['name' => 'approval.export', 'description' => 'Export Approval Data'],
        ];

        // 9. SYSTEM PERMISSIONS
        $systemPermissions = [
            ['name' => 'login', 'description' => 'Login ke Sistem'],
            ['name' => 'logout', 'description' => 'Logout dari Sistem'],
            ['name' => 'register', 'description' => 'Registrasi User Baru'],
            ['name' => 'password.reset', 'description' => 'Reset Password'],
            ['name' => 'password.update', 'description' => 'Update Password'],
        ];

        // Combine all permissions
        $allPermissions = array_merge(
            $dashboardPermissions,
            $masterPermissions,
            $pranotaPermissions,
            $pembayaranPermissions,
            $tagihanPermissions,
            $permohonanPermissions,
            $perbaikanPermissions,
            $userPermissions,
            $systemPermissions
        );

        // Filter out existing permissions and create new ones
        foreach ($allPermissions as $permission) {
            if (!in_array($permission['name'], $existingPermissions)) {
                $newPermissions[] = $permission;
            }
        }

        // Insert new permissions in chunks
        if (!empty($newPermissions)) {
            $chunks = array_chunk($newPermissions, 50);
            foreach ($chunks as $chunk) {
                Permission::insert($chunk);
            }
            $this->command->info("✅ Added " . count($newPermissions) . " new permissions successfully!");
        } else {
            $this->command->info("ℹ️  All permissions already exist. No new permissions added.");
        }

        $this->command->info('🎉 Comprehensive permissions seeding completed!');
        $this->command->info('📊 Total permissions in system: ' . (count($existingPermissions) + count($newPermissions)));
    }
}
